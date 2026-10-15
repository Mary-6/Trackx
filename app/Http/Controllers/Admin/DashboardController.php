<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\TrackingEvent;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(): View
    {
        $statusId = fn (string $slug) => ShipmentStatus::where('slug', $slug)->value('id');

        $stats = [
            'total' => Shipment::where('is_archived', false)->count(),
            'created_today' => Shipment::whereDate('created_at', today())->count(),
            'awaiting_dispatch' => Shipment::whereIn('shipment_status_id', [
                $statusId('shipment-created'), $statusId('order-confirmed'), $statusId('shipment-prepared'),
            ])->where('is_archived', false)->count(),
            'in_transit' => Shipment::whereIn('shipment_status_id', [
                $statusId('dispatched'), $statusId('in-transit'), $statusId('arrived-sorting-facility'),
                $statusId('customs-processing'), $statusId('customs-cleared'),
            ])->where('is_archived', false)->count(),
            'out_for_delivery' => Shipment::where('shipment_status_id', $statusId('out-for-delivery'))->count(),
            'delivered' => Shipment::where('shipment_status_id', $statusId('delivered'))->count(),
            'delayed' => Shipment::where('is_archived', false)
                ->whereNull('delivered_at')
                ->whereDate('estimated_delivery_date', '<', today())
                ->whereNotIn('shipment_status_id', [
                    $statusId('delivered'), $statusId('returned-to-sender'), $statusId('cancelled'),
                ])->count(),
            'exceptions' => Shipment::whereIn('shipment_status_id', [
                $statusId('delivery-exception'), $statusId('delivery-attempted'), $statusId('on-hold'),
            ])->where('is_archived', false)->count(),
            'customers' => Customer::count(),
            'unresolved_enquiries' => ContactMessage::where('is_resolved', false)->count(),
        ];

        $distribution = ShipmentStatus::query()
            ->leftJoin('shipments', function ($join) {
                $join->on('shipments.shipment_status_id', '=', 'shipment_statuses.id')
                    ->where('shipments.is_archived', false);
            })
            ->select('shipment_statuses.name', 'shipment_statuses.color', DB::raw('COUNT(shipments.id) as total'))
            ->groupBy('shipment_statuses.id', 'shipment_statuses.name', 'shipment_statuses.color', 'shipment_statuses.sort_order')
            ->orderBy('shipment_statuses.sort_order')
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'distribution' => $distribution,
            'recentShipments' => Shipment::with(['status', 'category'])->latest()->take(8)->get(),
            'recentEvents' => TrackingEvent::with(['shipment', 'status'])->latest('occurred_at')->take(8)->get(),
            'recentActivity' => AuditLog::with('user')->latest()->take(8)->get(),
        ]);
    }
}
