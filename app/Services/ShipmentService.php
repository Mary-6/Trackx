<?php

namespace App\Services;

use App\Models\Shipment;
use App\Models\ShipmentLocation;
use App\Models\ShipmentStatus;
use App\Models\TrackingEvent;
use Illuminate\Support\Facades\DB;

class ShipmentService
{
    public function __construct(
        protected TrackingNumberGenerator $trackingNumbers,
    ) {}

    /**
     * Create a shipment with a unique tracking number and its initial
     * "Shipment Created" tracking event inside a transaction.
     */
    public function create(array $data, ?int $creatorId = null): Shipment
    {
        return DB::transaction(function () use ($data, $creatorId) {
            $status = $this->resolveStatus($data['shipment_status_id'] ?? null);

            $shipment = Shipment::create([
                ...$data,
                'tracking_number' => $data['tracking_number'] ?? $this->trackingNumbers->generate(),
                'shipment_status_id' => $status->id,
                'created_by' => $creatorId,
            ]);

            $this->addEvent($shipment, $status->id, [
                'location_name' => collect([$shipment->origin_city, $shipment->origin_country])->filter()->implode(', ') ?: null,
                'latitude' => $shipment->origin_latitude,
                'longitude' => $shipment->origin_longitude,
                'public_message' => 'Shipment record created.',
                'source' => TrackingEvent::SOURCE_SYSTEM,
                'user_id' => $creatorId,
            ]);

            AuditLogger::log('shipment.created', $shipment, null, $shipment->only([
                'tracking_number', 'shipment_category_id', 'recipient_name',
                'origin_city', 'origin_country', 'destination_city', 'destination_country',
            ]));

            return $shipment;
        });
    }

    public function update(Shipment $shipment, array $data): Shipment
    {
        $old = $shipment->only(array_keys($data));
        $shipment->update($data);

        AuditLogger::log('shipment.updated', $shipment, $old, $shipment->only(array_keys($data)));

        return $shipment;
    }

    /**
     * Append a tracking event. History is never overwritten; when a status
     * is supplied the shipment's current status pointer is advanced too.
     */
    public function addEvent(Shipment $shipment, int $statusId, array $attributes = []): TrackingEvent
    {
        return DB::transaction(function () use ($shipment, $statusId, $attributes) {
            $event = $shipment->trackingEvents()->create([
                'shipment_status_id' => $statusId,
                'location_name' => $attributes['location_name'] ?? null,
                'latitude' => $attributes['latitude'] ?? null,
                'longitude' => $attributes['longitude'] ?? null,
                'occurred_at' => $attributes['occurred_at'] ?? now(),
                'public_message' => $attributes['public_message'] ?? null,
                'private_note' => $attributes['private_note'] ?? null,
                'source' => $attributes['source'] ?? TrackingEvent::SOURCE_ADMIN,
                'user_id' => $attributes['user_id'] ?? null,
            ]);

            $oldStatus = $shipment->shipment_status_id;
            $updates = ['shipment_status_id' => $statusId];

            $status = ShipmentStatus::find($statusId);
            if ($status?->slug === 'delivered' && ! $shipment->delivered_at) {
                $updates['delivered_at'] = $event->occurred_at;
            }

            if (($attributes['latitude'] ?? null) !== null && ($attributes['longitude'] ?? null) !== null) {
                $updates['current_latitude'] = $attributes['latitude'];
                $updates['current_longitude'] = $attributes['longitude'];
                $updates['current_location_updated_at'] = $event->occurred_at;
            }

            if (($attributes['location_name'] ?? null) !== null) {
                $updates['current_location_name'] = $attributes['location_name'];
                $updates['current_location_updated_at'] = $event->occurred_at;
            }

            $shipment->update($updates);

            AuditLogger::log('shipment.event_added', $shipment,
                ['shipment_status_id' => $oldStatus],
                ['shipment_status_id' => $statusId, 'event_id' => $event->id],
            );

            return $event;
        });
    }

    /**
     * Record a confirmed location update (manual by an admin, or via the
     * authenticated integrations API). Appends to location history and
     * updates the shipment's current-location pointer.
     */
    public function recordLocation(
        Shipment $shipment,
        float $latitude,
        float $longitude,
        ?string $locationName = null,
        string $source = 'manual',
        ?\DateTimeInterface $recordedAt = null,
        ?int $userId = null,
    ): ShipmentLocation {
        return DB::transaction(function () use ($shipment, $latitude, $longitude, $locationName, $source, $recordedAt, $userId) {
            $recordedAt = $recordedAt ?? now();

            $location = $shipment->locations()->create([
                'latitude' => $latitude,
                'longitude' => $longitude,
                'location_name' => $locationName,
                'source' => $source,
                'recorded_at' => $recordedAt,
                'user_id' => $userId,
            ]);

            $shipment->update([
                'current_latitude' => $latitude,
                'current_longitude' => $longitude,
                'current_location_name' => $locationName,
                'current_location_updated_at' => $recordedAt,
            ]);

            return $location;
        });
    }

    public function archive(Shipment $shipment, string $reason): Shipment
    {
        $old = ['is_archived' => $shipment->is_archived, 'archive_reason' => $shipment->archive_reason];
        $shipment->update(['is_archived' => true, 'archive_reason' => $reason]);

        AuditLogger::log('shipment.archived', $shipment, $old, ['archive_reason' => $reason]);

        return $shipment;
    }

    public function unarchive(Shipment $shipment): Shipment
    {
        $shipment->update(['is_archived' => false, 'archive_reason' => null]);

        AuditLogger::log('shipment.unarchived', $shipment);

        return $shipment;
    }

    protected function resolveStatus(?int $statusId): ShipmentStatus
    {
        if ($statusId && $status = ShipmentStatus::find($statusId)) {
            return $status;
        }

        return ShipmentStatus::where('slug', 'shipment-created')->firstOrFail();
    }
}
