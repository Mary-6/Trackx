<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\ShipmentStatus;
use App\Models\TrackingEvent;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrackingEventFactory extends Factory
{
    protected $model = TrackingEvent::class;

    public function definition(): array
    {
        return [
            'shipment_id' => Shipment::factory(),
            'shipment_status_id' => ShipmentStatus::where('slug', 'in-transit')->value('id')
                ?? ShipmentStatus::create(['name' => 'In Transit', 'slug' => 'in-transit'])->id,
            'location_name' => fake()->city(),
            'occurred_at' => now(),
            'public_message' => 'Update recorded.',
        ];
    }
}
