<?php

namespace Database\Factories;

use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Warehouse>
 */
class WarehouseFactory extends Factory
{
    protected $model = Warehouse::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Depot',
            'location' => fake()->address(),
            'latitude' => fake()->latitude(31, 31.6),
            'longitude' => fake()->longitude(34.2, 34.5),
            'capacity' => fake()->numberBetween(1000, 20000),
            'status' => 'active',
        ];
    }
}
