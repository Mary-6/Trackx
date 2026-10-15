<?php

namespace Database\Seeders;

use App\Models\ShipmentCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ShipmentCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Valuables & Fragile Goods', 'description' => 'High-value and delicate goods with enhanced handling.'],
            ['name' => 'Electronics', 'description' => 'Phones, laptops, gadgets and consumer electronics.'],
            ['name' => 'Fashion & Accessories', 'description' => 'Clothing, footwear, jewellery and accessories.'],
            ['name' => 'Documents', 'description' => 'Contracts, certificates and other paperwork.'],
            ['name' => 'Personal Parcels', 'description' => 'Personal packages and gifts.'],
            ['name' => 'Commercial Packages', 'description' => 'B2B parcels and trade goods.'],
            ['name' => 'Business Cargo', 'description' => 'Palletised and bulk business freight.'],
            ['name' => 'Other Goods', 'description' => 'Anything not covered by another category.'],
        ];

        foreach ($categories as $category) {
            ShipmentCategory::updateOrCreate(
                ['name' => $category['name']],
                ['description' => $category['description'], 'slug' => Str::slug($category['name'])]
            );
        }
    }
}
