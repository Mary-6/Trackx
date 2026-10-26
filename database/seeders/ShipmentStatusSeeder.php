<?php

namespace Database\Seeders;

use App\Models\ShipmentStatus;
use Illuminate\Database\Seeder;

class ShipmentStatusSeeder extends Seeder
{
    public function run(): void
    {
        $statuses = [
            ['name' => 'Pending', 'slug' => 'pending', 'sort_order' => 1, 'color' => 'slate'],
            ['name' => 'Processing', 'slug' => 'processing', 'sort_order' => 2, 'color' => 'sky'],
            ['name' => 'Shipment Created', 'slug' => 'shipment-created', 'sort_order' => 3, 'color' => 'slate'],
            ['name' => 'Order Confirmed', 'slug' => 'order-confirmed', 'sort_order' => 4, 'color' => 'sky'],
            ['name' => 'Shipment Prepared', 'slug' => 'shipment-prepared', 'sort_order' => 5, 'color' => 'indigo'],
            ['name' => 'Picked Up', 'slug' => 'picked-up', 'sort_order' => 6, 'color' => 'blue'],
            ['name' => 'Dispatched', 'slug' => 'dispatched', 'sort_order' => 7, 'color' => 'blue'],
            ['name' => 'In Transit', 'slug' => 'in-transit', 'sort_order' => 8, 'color' => 'cyan'],
            ['name' => 'Enroute', 'slug' => 'enroute', 'sort_order' => 9, 'color' => 'cyan'],
            ['name' => 'Delivery in Progress', 'slug' => 'delivery-in-progress', 'sort_order' => 10, 'color' => 'violet'],
            ['name' => 'Arrived at Sorting Facility', 'slug' => 'arrived-sorting-facility', 'sort_order' => 11, 'color' => 'teal'],
            ['name' => 'Customs Processing', 'slug' => 'customs-processing', 'sort_order' => 12, 'color' => 'amber'],
            ['name' => 'Customs Cleared', 'slug' => 'customs-cleared', 'sort_order' => 13, 'color' => 'lime'],
            ['name' => 'Out for Delivery', 'slug' => 'out-for-delivery', 'sort_order' => 14, 'color' => 'violet'],
            ['name' => 'Delivered', 'slug' => 'delivered', 'sort_order' => 15, 'color' => 'green', 'is_final' => true],
            ['name' => 'Delivery Attempted', 'slug' => 'delivery-attempted', 'sort_order' => 16, 'color' => 'orange'],
            ['name' => 'Delivery Exception', 'slug' => 'delivery-exception', 'sort_order' => 17, 'color' => 'red'],
            ['name' => 'On Hold', 'slug' => 'on-hold', 'sort_order' => 18, 'color' => 'yellow'],
            ['name' => 'Order on Hold', 'slug' => 'order-on-hold', 'sort_order' => 19, 'color' => 'yellow'],
            ['name' => 'Returned', 'slug' => 'returned', 'sort_order' => 20, 'color' => 'rose', 'is_final' => true],
            ['name' => 'Returned to Sender', 'slug' => 'returned-to-sender', 'sort_order' => 21, 'color' => 'rose', 'is_final' => true],
            ['name' => 'Cancelled', 'slug' => 'cancelled', 'sort_order' => 22, 'color' => 'gray', 'is_final' => true],
        ];

        foreach ($statuses as $status) {
            ShipmentStatus::updateOrCreate(['slug' => $status['slug']], $status);
        }
    }
}
