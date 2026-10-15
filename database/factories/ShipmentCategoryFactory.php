<?php

namespace Database\Factories;

use App\Models\ShipmentCategory;
use Illuminate\Database\Eloquent\Factories\Factory;

class ShipmentCategoryFactory extends Factory
{
    protected $model = ShipmentCategory::class;

    public function definition(): array
    {
        return ['name' => fake()->unique()->words(2, true)];
    }
}
