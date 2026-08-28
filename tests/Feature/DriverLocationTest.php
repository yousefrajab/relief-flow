<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverLocationTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_update_their_location(): void
    {
        $driver = User::factory()->driver()->create();

        $response = $this->actingAs($driver)->postJson('/driver/location', [
            'latitude' => 31.5017,
            'longitude' => 34.4668,
        ]);

        $response->assertOk();
        $driver->refresh();
        $this->assertEquals(31.5017, (float) $driver->last_latitude);
        $this->assertEquals(34.4668, (float) $driver->last_longitude);
        $this->assertNotNull($driver->last_location_at);
    }

    public function test_location_update_validates_coordinate_ranges(): void
    {
        $driver = User::factory()->driver()->create();

        $response = $this->actingAs($driver)->postJson('/driver/location', [
            'latitude' => 200,
            'longitude' => 34.4668,
        ]);

        $response->assertStatus(422);
    }

    public function test_non_driver_cannot_update_a_location(): void
    {
        $coordinator = User::factory()->coordinator()->create();

        $response = $this->actingAs($coordinator)->postJson('/driver/location', [
            'latitude' => 31.5,
            'longitude' => 34.4,
        ]);

        $response->assertStatus(403);
    }

    public function test_driver_can_clear_their_location(): void
    {
        $driver = User::factory()->driver()->create([
            'last_latitude' => 31.5,
            'last_longitude' => 34.4,
            'last_location_at' => now(),
        ]);

        $response = $this->actingAs($driver)->deleteJson('/driver/location');

        $response->assertOk();
        $driver->refresh();
        $this->assertNull($driver->last_latitude);
        $this->assertNull($driver->last_longitude);
        $this->assertNull($driver->last_location_at);
    }

    public function test_admin_sees_only_fresh_driver_locations(): void
    {
        $admin = User::factory()->admin()->create();
        User::factory()->driver()->create([
            'name' => 'Fresh Driver',
            'last_latitude' => 31.5,
            'last_longitude' => 34.4,
            'last_location_at' => now()->subMinutes(2),
        ]);
        User::factory()->driver()->create([
            'name' => 'Stale Driver',
            'last_latitude' => 31.6,
            'last_longitude' => 34.5,
            'last_location_at' => now()->subMinutes(20),
        ]);
        User::factory()->driver()->create(['name' => 'No Location Driver']);

        $response = $this->actingAs($admin)->getJson('/map/drivers');

        $response->assertOk();
        $response->assertJsonCount(1, 'drivers');
        $response->assertJsonPath('drivers.0.name', 'Fresh Driver');
    }

    public function test_coordinator_cannot_see_driver_locations(): void
    {
        $coordinator = User::factory()->coordinator()->create();

        $response = $this->actingAs($coordinator)->getJson('/map/drivers');

        $response->assertStatus(403);
    }

    public function test_depot_manager_can_see_driver_locations(): void
    {
        $manager = User::factory()->depotManager()->create();
        User::factory()->driver()->create([
            'last_latitude' => 31.5,
            'last_longitude' => 34.4,
            'last_location_at' => now(),
        ]);

        $response = $this->actingAs($manager)->getJson('/map/drivers');

        $response->assertOk();
        $response->assertJsonCount(1, 'drivers');
    }
}
