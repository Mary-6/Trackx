<?php

namespace App\Http\Controllers;

use App\Http\Requests\TrackingLookupRequest;
use App\Models\Shipment;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Picqer\Barcode\BarcodeGeneratorSVG;

class TrackingController extends Controller
{
    public function index(): View
    {
        return view('public.track');
    }

    public function lookup(TrackingLookupRequest $request): RedirectResponse
    {
        $number = $request->trackingNumber();

        return redirect()->route('track.result', ['trackingNumber' => $number]);
    }

    public function result(string $trackingNumber): View
    {
        $number = strtoupper(preg_replace('/[\s\-_]/', '', (string) $trackingNumber));

        $shipment = Shipment::with(['category', 'status', 'trackingEvents.status', 'locations'])
            ->where('tracking_number', $number)
            ->where('is_archived', false)
            ->first();

        if (! $shipment) {
            return view('public.tracking-error', [
                'message' => 'No shipment was found for that tracking number. Please check the number and try again.',
            ])->with('trackingNumber', $number);
        }

        $barcode = preg_replace(
            '/<\?xml.*?\?>\s*|<!DOCTYPE[^>]*>\s*/i',
            '',
            (new BarcodeGeneratorSVG)->getBarcode(
                $shipment->tracking_number,
                BarcodeGeneratorSVG::TYPE_CODE_128,
                2,
                60
            )
        );

        return view('public.tracking-result', [
            'shipment' => $shipment,
            'payload' => $shipment->publicPayload(),
            'barcode' => $barcode,
            'historyOrder' => setting('tracking_history_order', 'desc'),
        ]);
    }

    /**
     * Public, privacy-safe JSON feed used by the tracking page to refresh
     * the map. Returns only approved fields.
     */
    public function locationJson(string $trackingNumber): JsonResponse
    {
        $shipment = Shipment::with('locations')
            ->where('tracking_number', strtoupper(trim($trackingNumber)))
            ->where('is_archived', false)
            ->firstOrFail();

        return response()->json([
            'status' => $shipment->status?->name,
            'map' => $shipment->mapPayload(),
        ]);
    }
}
