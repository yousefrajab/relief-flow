<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'role' => 'coordinator',
            'status' => 'active',
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'admin', 'status' => 'active']);
    }

    public function depotManager(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'depot_manager', 'status' => 'active']);
    }

    public function coordinator(): static
    {
        return $this->state(fn (array $attributes) => ['role' => 'coordinator', 'status' => 'active']);
    }

    public function pendingVerification(): static
    {
        return $this->state(fn (array $attributes) => ['status' => 'pending_verification']);
    }
}
