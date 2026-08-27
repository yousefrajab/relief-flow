<?php

namespace Database\Factories;

use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'category' => fake()->randomElement(['Food', 'Medical', 'Hygiene', 'Shelter']),
            'unit' => fake()->randomElement(['box', 'kit', 'bag', 'liter']),
            'description' => fake()->sentence(),
        ];
    }
}
