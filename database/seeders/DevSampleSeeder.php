<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Shipment;
use App\Models\ShipmentCategory;
use App\Models\ShipmentStatus;
use App\Services\ShipmentService;
use Illuminate\Database\Seeder;

/**
 * Development / demo data only. Never run in production —
 * DatabaseSeeder guards on the environment.
 */
class DevSampleSeeder extends Seeder
{
    public function run(): void
    {
        $service = app(ShipmentService::class);
        $categories = ShipmentCategory::all()->keyBy('slug');
        $statuses = ShipmentStatus::all()->keyBy('slug');

        $customers = collect([
            ['name' => 'Amelia Hart', 'email' => 'amelia.hart@example.com', 'phone' => '+44 7700 900123', 'city' => 'London', 'country' => 'United Kingdom'],
            ['name' => 'Marco Ruiz', 'email' => 'marco.ruiz@example.com', 'phone' => '+34 600 112 233', 'city' => 'Madrid', 'country' => 'Spain'],
            ['name' => 'Chen Wei', 'email' => 'chen.wei@example.com', 'phone' => '+86 138 0000 1122', 'city' => 'Shenzhen', 'country' => 'China'],
        ])->map(fn ($c) => Customer::create($c));

        $samples = [
            [
                'category' => 'valuables-fragile-goods',
                'status' => 'in-transit',
                'description' => 'Insured collector item — tamper-evident packaging.',
                'sender_name' => 'Meridian Goods Ltd',
                'recipient_name' => 'Amelia Hart',
                'recipient_email' => 'amelia.hart@example.com',
                'origin_city' => 'Geneva', 'origin_country' => 'Switzerland',
                'origin_latitude' => 46.2044, 'origin_longitude' => 6.1432,
                'destination_city' => 'London', 'destination_country' => 'United Kingdom',
                'destination_latitude' => 51.5074, 'destination_longitude' => -0.1278,
                'shipping_method' => 'Air Express',
                'dispatch_date' => now()->subDays(3)->toDateString(),
                'estimated_delivery_date' => now()->addDays(2)->toDateString(),
                'weight_kg' => 0.85,
                'customer' => 0,
            ],
            [
                'category' => 'electronics',
                'status' => 'customs-processing',
                'description' => 'Laptop computer, retail box, lithium battery — declared for customs.',
                'sender_name' => 'TechSource GmbH',
                'recipient_name' => 'Marco Ruiz',
                'recipient_email' => 'marco.ruiz@example.com',
                'origin_city' => 'Berlin', 'origin_country' => 'Germany',
                'origin_latitude' => 52.52, 'origin_longitude' => 13.405,
                'destination_city' => 'Madrid', 'destination_country' => 'Spain',
                'destination_latitude' => 40.4168, 'destination_longitude' => -3.7038,
                'shipping_method' => 'Road Freight',
                'dispatch_date' => now()->subDays(5)->toDateString(),
                'estimated_delivery_date' => now()->addDays(4)->toDateString(),
                'weight_kg' => 2.4,
                'customs_reference' => 'ES-IMP-2026-4412',
                'customer' => 1,
            ],
            [
                'category' => 'documents',
                'status' => 'delivered',
                'description' => 'Sealed document envelope — contracts.',
                'sender_name' => 'Harlow & Finch Legal',
                'recipient_name' => 'Chen Wei',
                'origin_city' => 'New York', 'origin_country' => 'United States',
                'origin_latitude' => 40.7128, 'origin_longitude' => -74.006,
                'destination_city' => 'Shenzhen', 'destination_country' => 'China',
                'destination_latitude' => 22.5431, 'destination_longitude' => 114.0579,
                'shipping_method' => 'Air Express',
                'dispatch_date' => now()->subDays(9)->toDateString(),
                'estimated_delivery_date' => now()->subDays(1)->toDateString(),
                'weight_kg' => 0.2,
                'customer' => 2,
            ],
        ];

        foreach ($samples as $sample) {
            $statusId = $statuses[$sample['status']]->id;
            $categorySlug = $sample['category'];
            $statusSlug = $sample['status'];
            $customer = $customers[$sample['customer']];
            unset($sample['customer'], $sample['category'], $sample['status']);

            $shipment = $service->create([
                ...$sample,
                'shipment_category_id' => $categories[$categorySlug]->id,
                'shipment_status_id' => $statuses['shipment-created']->id,
                'customer_id' => $customer->id,
            ]);

            // Walk the shipment through its event history.
            if ($statusSlug !== 'shipment-created') {
                $service->addEvent($shipment, $statuses['dispatched']->id, [
                    'location_name' => $sample['origin_city'].', '.$sample['origin_country'],
                    'latitude' => $sample['origin_latitude'],
                    'longitude' => $sample['origin_longitude'],
                    'occurred_at' => now()->subDays(3),
                    'public_message' => 'Shipment dispatched from origin facility.',
                    'source' => 'system',
                ]);
            }

            if (in_array($statusSlug, ['in-transit', 'customs-processing', 'delivered'], true)) {
                $service->addEvent($shipment, $statuses['in-transit']->id, [
                    'location_name' => 'Regional hub',
                    'occurred_at' => now()->subDays(2),
                    'public_message' => 'Shipment in transit to destination region.',
                    'source' => 'system',
                ]);
            }

            if ($statusSlug === 'customs-processing') {
                $service->addEvent($shipment, $statusId, [
                    'location_name' => 'Madrid customs facility',
                    'latitude' => 40.4168, 'longitude' => -3.7038,
                    'occurred_at' => now()->subDay(),
                    'public_message' => 'Shipment is undergoing customs processing.',
                    'source' => 'system',
                ]);
            }

            if ($statusSlug === 'delivered') {
                $service->addEvent($shipment, $statuses['out-for-delivery']->id, [
                    'location_name' => $sample['destination_city'],
                    'occurred_at' => now()->subDays(2),
                    'public_message' => 'Out for delivery.',
                    'source' => 'system',
                ]);
                $service->addEvent($shipment, $statusId, [
                    'location_name' => $sample['destination_city'].', '.$sample['destination_country'],
                    'latitude' => $sample['destination_latitude'],
                    'longitude' => $sample['destination_longitude'],
                    'occurred_at' => now()->subDays(1),
                    'public_message' => 'Shipment delivered.',
                    'source' => 'system',
                ]);
            }
        }
    }
}
