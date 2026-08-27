<?php

namespace Database\Factories;

use App\Models\AidRequest;
use App\Models\Shipment;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shipment>
 */
class ShipmentFactory extends Factory
{
    protected $model = Shipment::class;

    public function definition(): array
    {
        return [
            'aid_request_id' => AidRequest::factory()->dispatched(),
            'warehouse_id' => Warehouse::factory(),
            'driver_name' => fake()->name(),
            'driver_phone' => fake()->phoneNumber(),
            'status' => 'dispatched',
            'qr_code_token' => 'RF-'.strtoupper(fake()->bothify('########')),
        ];
    }

    public function delivered(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'delivered',
            'delivered_at' => now(),
        ]);
    }
}
