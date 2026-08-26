<?php

namespace Database\Factories;

use App\Models\AidRequest;
use App\Models\AidRequestItem;
use App\Models\Item;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AidRequestItem>
 */
class AidRequestItemFactory extends Factory
{
    protected $model = AidRequestItem::class;

    public function definition(): array
    {
        return [
            'aid_request_id' => AidRequest::factory(),
            'item_id' => Item::factory(),
            'quantity' => fake()->numberBetween(1, 100),
        ];
    }
}
