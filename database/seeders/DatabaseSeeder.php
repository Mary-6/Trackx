<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Core seeders run in every environment. Demo shipments and
     * customers are seeded ONLY outside production.
     */
    public function run(): void
    {
        $this->call([
            ShipmentStatusSeeder::class,
            ShipmentCategorySeeder::class,
            SiteSettingSeeder::class,
        ]);

        if (! app()->environment('production')) {
            $this->call(DevSampleSeeder::class);
        }
    }
}
