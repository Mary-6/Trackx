<?php

namespace Database\Factories;

use App\Models\Shipment;
use App\Models\ShipmentCategory;
use App\Models\ShipmentStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'tracking_number' => strtoupper(Str::random(10)).(string) fake()->unique()->numberBetween(10, 99),
            'shipment_category_id' => ShipmentCategory::factory(),
            'shipment_status_id' => ShipmentStatus::where('slug', 'shipment-created')->value('id')
                ?? ShipmentStatus::create(['name' => 'Shipment Created', 'slug' => 'shipment-created'])->id,
            'sender_name' => fake()->name(),
            'sender_email' => fake()->safeEmail(),
            'sender_phone' => fake()->phoneNumber(),
            'sender_address' => fake()->streetAddress(),
            'recipient_name' => fake()->name(),
            'recipient_email' => fake()->safeEmail(),
            'recipient_phone' => fake()->phoneNumber(),
            'recipient_address' => fake()->streetAddress(),
            'origin_country' => 'Switzerland',
            'origin_city' => 'Geneva',
            'origin_latitude' => 46.2044,
            'origin_longitude' => 6.1432,
            'destination_country' => 'United Kingdom',
            'destination_city' => 'London',
            'destination_latitude' => 51.5074,
            'destination_longitude' => -0.1278,
            'private_notes' => 'INTERNAL_NOTE_SECRET',
            'public_notes' => 'Handle with care.',
        ];
    }
}
