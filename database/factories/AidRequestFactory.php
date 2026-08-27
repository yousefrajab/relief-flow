<?php

namespace Database\Factories;

use App\Models\AidRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AidRequest>
 */
class AidRequestFactory extends Factory
{
    protected $model = AidRequest::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->coordinator(),
            'location' => fake()->address(),
            'latitude' => fake()->latitude(31, 31.6),
            'longitude' => fake()->longitude(34.2, 34.5),
            'notes' => fake()->sentence(),
            'status' => 'pending',
            'priority' => 'normal',
        ];
    }

    public function rejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'rejected',
            'rejection_reason' => fake()->sentence(),
        ]);
    }

    public function dispatched(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'dispatched']);
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'delivered']);
    }
}
