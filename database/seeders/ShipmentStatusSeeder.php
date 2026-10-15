<?php

namespace Database\Seeders;

use App\Models\ShipmentStatus;
use Illuminate\Database\Seeder;

class ShipmentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Shipment Created', 'slug' => 'shipment-created', 'sort_order' => 1, 'color' => 'slate'],
            ['name' => 'Order Confirmed', 'slug' => 'order-confirmed', 'sort_order' => 2, 'color' => 'sky'],
            ['name' => 'Shipment Prepared', 'slug' => 'shipment-prepared', 'sort_order' => 3, 'color' => 'indigo'],
            ['name' => 'Dispatched', 'slug' => 'dispatched', 'sort_order' => 4, 'color' => 'blue'],
            ['name' => 'In Transit', 'slug' => 'in-transit', 'sort_order' => 5, 'color' => 'cyan'],
            ['name' => 'Arrived at Sorting Facility', 'slug' => 'arrived-sorting-facility', 'sort_order' => 6, 'color' => 'teal'],
            ['name' => 'Customs Processing', 'slug' => 'customs-processing', 'sort_order' => 7, 'color' => 'amber'],
            ['name' => 'Customs Cleared', 'slug' => 'customs-cleared', 'sort_order' => 8, 'color' => 'lime'],
            ['name' => 'Out for Delivery', 'slug' => 'out-for-delivery', 'sort_order' => 9, 'color' => 'violet'],
            ['name' => 'Delivered', 'slug' => 'delivered', 'sort_order' => 10, 'color' => 'green', 'is_final' => true],
            ['name' => 'Delivery Attempted', 'slug' => 'delivery-attempted', 'sort_order' => 11, 'color' => 'orange'],
            ['name' => 'Delivery Exception', 'slug' => 'delivery-exception', 'sort_order' => 12, 'color' => 'red'],
            ['name' => 'On Hold', 'slug' => 'on-hold', 'sort_order' => 13, 'color' => 'yellow'],
            ['name' => 'Returned to Sender', 'slug' => 'returned-to-sender', 'sort_order' => 14, 'color' => 'rose', 'is_final' => true],
            ['name' => 'Cancelled', 'slug' => 'cancelled', 'sort_order' => 15, 'color' => 'gray', 'is_final' => true],
            ['name' => 'Delivery in Progress', 'slug' => 'delivery-in-progress', 'sort_order' => 16, 'color' => 'violet'],
            ['name' => 'Processing', 'slug' => 'processing', 'sort_order' => 17, 'color' => 'sky'],
            ['name' => 'Order on Hold', 'slug' => 'order-on-hold', 'sort_order' => 18, 'color' => 'yellow'],
        ];

        foreach ($statuses as $status) {
            ShipmentStatus::updateOrCreate(['slug' => $status['slug']], $status);
        }
    }
}
