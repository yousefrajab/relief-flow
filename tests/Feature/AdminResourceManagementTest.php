<?php

namespace Tests\Feature;

use App\Models\Inventory;
use App\Models\Item;
use App\Models\Shipment;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminResourceManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_a_warehouse(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/warehouses', [
            'name' => 'North Depot',
            'location' => 'Gaza City',
            'capacity' => 5000,
        ]);

        $response->assertRedirect(route('warehouses.show', Warehouse::where('name', 'North Depot')->firstOrFail()));
        $this->assertDatabaseHas('warehouses', ['name' => 'North Depot']);
    }

    public function test_non_admin_cannot_create_a_warehouse(): void
    {
        $manager = User::factory()->depotManager()->create();

        $response = $this->actingAs($manager)->post('/warehouses', [
            'name' => 'North Depot',
            'location' => 'Gaza City',
            'capacity' => 5000,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('warehouses', ['name' => 'North Depot']);
    }

    public function test_warehouse_with_shipment_history_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $warehouse = Warehouse::factory()->create();
        Shipment::factory()->for($warehouse)->create();

        $response = $this->actingAs($admin)->delete("/warehouses/{$warehouse->id}");

        $response->assertRedirect(route('warehouses.show', $warehouse));
        $response->assertSessionHas('error');
        $this->assertModelExists($warehouse);
    }

    public function test_warehouse_without_history_can_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $warehouse = Warehouse::factory()->create();

        $response = $this->actingAs($admin)->delete("/warehouses/{$warehouse->id}");

        $response->assertRedirect(route('warehouses.index'));
        $this->assertModelMissing($warehouse);
    }

    public function test_admin_can_create_an_item(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/items', [
            'name' => 'Blanket Pack',
            'category' => 'Shelter',
            'unit' => 'pack',
        ]);

        $response->assertRedirect(route('items.index'));
        $this->assertDatabaseHas('items', ['name' => 'Blanket Pack']);
    }

    public function test_item_with_stock_cannot_be_deleted(): void
    {
        $admin = User::factory()->admin()->create();
        $item = Item::factory()->create();
        Inventory::factory()->create(['item_id' => $item->id, 'quantity' => 50]);

        $response = $this->actingAs($admin)->delete("/items/{$item->id}");

        $response->assertSessionHas('error');
        $this->assertModelExists($item);
    }

    public function test_depot_manager_can_add_stock(): void
    {
        $manager = User::factory()->depotManager()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($manager)->post('/inventory', [
            'warehouse_id' => $warehouse->id,
            'item_id' => $item->id,
            'quantity' => 200,
        ]);

        $response->assertRedirect(route('inventory.index'));
        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $warehouse->id,
            'item_id' => $item->id,
            'quantity' => 200,
        ]);
    }

    public function test_adding_stock_twice_accumulates_quantity(): void
    {
        $manager = User::factory()->depotManager()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();

        $this->actingAs($manager)->post('/inventory', [
            'warehouse_id' => $warehouse->id,
            'item_id' => $item->id,
            'quantity' => 200,
        ]);
        $this->actingAs($manager)->post('/inventory', [
            'warehouse_id' => $warehouse->id,
            'item_id' => $item->id,
            'quantity' => 150,
        ]);

        $this->assertDatabaseHas('inventories', [
            'warehouse_id' => $warehouse->id,
            'item_id' => $item->id,
            'quantity' => 350,
        ]);
    }

    public function test_coordinator_cannot_add_stock(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $warehouse = Warehouse::factory()->create();
        $item = Item::factory()->create();

        $response = $this->actingAs($coordinator)->post('/inventory', [
            'warehouse_id' => $warehouse->id,
            'item_id' => $item->id,
            'quantity' => 200,
        ]);

        $response->assertForbidden();
    }
}
