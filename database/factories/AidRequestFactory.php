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
            'notes' => fake()->sentence(),
            'status' => 'pending',
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
