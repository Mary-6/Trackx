<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateLocationRequest;
use App\Models\Shipment;
use App\Services\LocationService;
use Carbon\CarbonImmutable;
use Illuminate\Http\JsonResponse;

class LocationUpdateController extends Controller
{
    public function __construct(protected LocationService $locations) {}

    /**
     * POST /api/v1/shipments/{tracking_number}/location
     * Authenticated via Sanctum bearer token (ability: update-location).
     * Rate limited at the route level.
     */
    public function store(UpdateLocationRequest $request, string $trackingNumber): JsonResponse
    {
        abort_unless($request->user()->tokenCan('update-location'), 403);

        $shipment = Shipment::where('tracking_number', strtoupper(trim($trackingNumber)))
            ->where('is_archived', false)
            ->firstOrFail();

        $data = $request->validated();

        try {
            $location = $this->locations->ingest(
                shipment: $shipment,
                latitude: (float) $data['latitude'],
                longitude: (float) $data['longitude'],
                locationName: $data['location_name'] ?? null,
                recordedAt: isset($data['recorded_at']) ? CarbonImmutable::parse($data['recorded_at']) : null,
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'message' => 'Location recorded.',
            'location' => [
                'latitude' => (float) $location->latitude,
                'longitude' => (float) $location->longitude,
                'recorded_at' => $location->recorded_at->toIso8601String(),
            ],
        ], 202);
    }
}
