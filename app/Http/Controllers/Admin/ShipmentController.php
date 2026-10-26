<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreShipmentRequest;
use App\Http\Requests\StoreTrackingEventRequest;
use App\Http\Requests\UpdateLocationRequest;
use App\Http\Requests\UpdateShipmentRequest;
use App\Models\Shipment;
use App\Models\ShipmentCategory;
use App\Models\ShipmentLocation;
use App\Models\ShipmentStatus;
use App\Models\TrackingEvent;
use App\Services\AuditLogger;
use App\Services\NotificationDispatcher;
use App\Services\ShipmentService;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ShipmentController extends Controller
{
    public function __construct(
        protected ShipmentService $shipments,
        protected NotificationDispatcher $notifications,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', Shipment::class);

        $filters = $request->only(['q', 'status', 'category', 'destination', 'from', 'to', 'archived']);
        $allowedSorts = ['created_at', 'tracking_number', 'recipient_name', 'estimated_delivery_date'];
        $sort = in_array($request->get('sort'), $allowedSorts, true) ? $request->get('sort') : 'created_at';
        $direction = $request->get('direction') === 'asc' ? 'asc' : 'desc';

        $shipments = Shipment::with(['status', 'category'])
            ->filter($filters)
            ->orderBy($sort, $direction)
            ->paginate(15)
            ->withQueryString();

        return view('admin.shipments.index', [
            'shipments' => $shipments,
            'statuses' => ShipmentStatus::active()->get(),
            'categories' => ShipmentCategory::active()->get(),
            'destinations' => Shipment::select('destination_country')->distinct()->orderBy('destination_country')->pluck('destination_country'),
            'filters' => $filters,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', Shipment::class);

        return view('admin.shipments.create', [
            'statuses' => ShipmentStatus::active()->get(),
        ]);
    }

    public function store(StoreShipmentRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $this->resolveCategory($data);
        unset($data['notify_recipient']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('shipments', 'public');
        }

        $shipment = $this->shipments->create($data, $request->user()->id);

        if ($request->boolean('notify_recipient')) {
            $this->notifications->notifyRecipient($shipment, 'created');
        }

        return redirect()
            ->route('admin.shipments.show', $shipment)
            ->with('success', "Shipment {$shipment->tracking_number} created.");
    }

    public function show(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        return view('admin.shipments.show', [
            'shipment' => $shipment->load([
                'category', 'status', 'customer', 'creator',
                'trackingEvents.status', 'trackingEvents.user', 'locations', 'notificationRecords',
            ]),
            'statuses' => ShipmentStatus::active()->get(),
        ]);
    }

    public function edit(Shipment $shipment): View
    {
        $this->authorize('update', $shipment);

        return view('admin.shipments.edit', [
            'shipment' => $shipment->load('category'),
            'statuses' => ShipmentStatus::active()->get(),
        ]);
    }

    public function update(UpdateShipmentRequest $request, Shipment $shipment): RedirectResponse
    {
        $data = $request->validated();
        $this->resolveCategory($data);

        $newStatusId = isset($data['shipment_status_id']) ? (int) $data['shipment_status_id'] : null;
        unset($data['notify_recipient'], $data['shipment_status_id']);

        if ($request->hasFile('image')) {
            if ($shipment->image) {
                Storage::disk('public')->delete($shipment->image);
            }
            $data['image'] = $request->file('image')->store('shipments', 'public');
        }

        $statusChanged = $newStatusId !== null && $newStatusId !== $shipment->shipment_status_id;
        $locationChanged = array_key_exists('current_location_name', $data)
            && ($data['current_location_name'] ?? '') !== ($shipment->current_location_name ?? '');

        $this->shipments->update($shipment, $data);

        // Append a tracking milestone only when something meaningful changed —
        // never on a no-op save, never duplicating an existing entry.
        if ($statusChanged || $locationChanged) {
            $this->shipments->addEvent($shipment, $newStatusId ?? $shipment->shipment_status_id, [
                'location_name' => $data['current_location_name'] ?? null,
                'source' => TrackingEvent::SOURCE_ADMIN,
                'user_id' => $request->user()->id,
            ]);
        }

        return redirect()
            ->route('admin.shipments.show', $shipment)
            ->with('success', 'Shipment updated.');
    }

    /**
     * Resolve the free-text category name into a category id
     * (find-or-create, case-insensitive) for store and update.
     */
    private function resolveCategory(array &$data): void
    {
        if (! empty($data['category_name'])) {
            $name = trim((string) $data['category_name']);
            $category = ShipmentCategory::whereRaw('LOWER(name) = ?', [mb_strtolower($name)])->first()
                ?? ShipmentCategory::create(['name' => $name]);
            $data['shipment_category_id'] = $category->id;
        } elseif (! empty($data['new_category'])) {
            $category = ShipmentCategory::create(['name' => $data['new_category']]);
            $data['shipment_category_id'] = $category->id;
        }
        unset($data['category_name'], $data['new_category']);
    }

    public function addEvent(StoreTrackingEventRequest $request, Shipment $shipment): RedirectResponse
    {
        $this->authorize('addEvent', $shipment);
        $data = $request->validated();

        $this->shipments->addEvent($shipment, (int) $data['shipment_status_id'], [
            ...$data,
            'source' => TrackingEvent::SOURCE_ADMIN,
            'user_id' => $request->user()->id,
        ]);

        if ($request->boolean('notify_recipient')) {
            $statusSlug = ShipmentStatus::find($data['shipment_status_id'])?->slug;
            $type = match ($statusSlug) {
                'dispatched' => 'dispatched',
                'delivered' => 'delivered',
                'delivery-exception', 'delivery-attempted', 'on-hold' => 'exception',
                default => 'status',
            };
            $this->notifications->notifyRecipient($shipment->fresh(['status']), $type);
        }

        return back()->with('success', 'Tracking event added.');
    }

    public function updateLocation(UpdateLocationRequest $request, Shipment $shipment): RedirectResponse
    {
        $this->authorize('updateLocation', $shipment);
        $data = $request->validated();

        $this->shipments->recordLocation(
            shipment: $shipment,
            latitude: (float) $data['latitude'],
            longitude: (float) $data['longitude'],
            locationName: $data['location_name'] ?? null,
            source: ShipmentLocation::SOURCE_MANUAL,
            recordedAt: isset($data['recorded_at']) ? CarbonImmutable::parse($data['recorded_at']) : null,
            userId: $request->user()->id,
        );

        AuditLogger::log('shipment.location_updated', $shipment, null, $data);

        return back()->with('success', 'Confirmed location updated.');
    }

    public function archive(Request $request, Shipment $shipment): RedirectResponse
    {
        $this->authorize('archive', $shipment);
        $request->validate(['archive_reason' => ['required', 'string', 'max:255']]);

        $this->shipments->archive($shipment, $request->input('archive_reason'));

        return redirect()->route('admin.shipments.index')->with('success', 'Shipment archived.');
    }

    public function unarchive(Shipment $shipment): RedirectResponse
    {
        $this->authorize('archive', $shipment);
        $this->shipments->unarchive($shipment);

        return back()->with('success', 'Shipment restored.');
    }

    public function printView(Shipment $shipment): View
    {
        $this->authorize('view', $shipment);

        return view('admin.shipments.print', [
            'shipment' => $shipment->load(['category', 'status', 'trackingEvents.status']),
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        $this->authorize('export', Shipment::class);

        $filters = $request->only(['q', 'status', 'category', 'destination', 'from', 'to', 'archived']);

        AuditLogger::log('shipment.exported', null, null, $filters);

        return response()->streamDownload(function () use ($filters) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'Tracking Number', 'Category', 'Status', 'Recipient', 'Destination',
                'Dispatch Date', 'Est. Delivery', 'Delivered At', 'Created At',
            ]);

            Shipment::with(['status', 'category'])
                ->filter($filters)
                ->orderBy('created_at')
                ->chunk(200, function ($rows) use ($out) {
                    foreach ($rows as $s) {
                        fputcsv($out, [
                            $s->tracking_number,
                            $s->category?->name,
                            $s->status?->name,
                            $s->recipient_name,
                            $s->destination_city.', '.$s->destination_country,
                            $s->dispatch_date?->toDateString(),
                            $s->estimated_delivery_date?->toDateString(),
                            $s->delivered_at?->toDateTimeString(),
                            $s->created_at->toDateTimeString(),
                        ]);
                    }
                });

            fclose($out);
        }, 'shipments-'.now()->format('Ymd-His').'.csv', ['Content-Type' => 'text/csv']);
    }
}
