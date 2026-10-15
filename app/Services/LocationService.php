<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentLocation;
use Carbon\CarbonImmutable;

class LocationService
{
    public function __construct(protected ShipmentService $shipments) {}

    /**
     * Ingest a location update from a trusted GPS device / courier
     * integration. Coordinates and timestamps are validated upstream by
     * the Form Request; here we additionally reject future-dated
     * timestamps beyond a small clock-skew tolerance.
     */
    public function ingest(
        Shipment $shipment,
        float $latitude,
        float $longitude,
        ?string $locationName,
        ?CarbonImmutable $recordedAt,
    ): ShipmentLocation {
        $recordedAt = $recordedAt ?? CarbonImmutable::now();

        if ($recordedAt->isAfter(CarbonImmutable::now()->addMinutes(10))) {
            throw new \InvalidArgumentException('Location timestamp cannot be in the future.');
        }

        return $this->shipments->recordLocation(
            shipment: $shipment,
            latitude: $latitude,
            longitude: $longitude,
            locationName: $locationName,
            source: ShipmentLocation::SOURCE_INTEGRATION,
            recordedAt: $recordedAt,
        );
    }
}
