<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\Inventory;
use App\Models\Item;
use App\Models\User;
use App\Models\Warehouse;
use App\Services\AIService;
use App\Services\LogisticsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmartFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_priority_classification_flags_urgent_keywords_in_simulation_mode(): void
    {
        $aiService = app(AIService::class);

        $this->assertSame('critical', $aiService->classifyPriority('Some location', 'Urgent, children need medical attention'));
        $this->assertSame('normal', $aiService->classifyPriority('Some location', 'Routine monthly restock'));
    }

    public function test_submitting_a_request_with_urgent_notes_is_flagged_critical(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $item = Item::factory()->create();

        $this->actingAs($coordinator)->post('/aid-requests', [
            'location' => 'Somewhere far',
            'notes' => 'Emergency, injured people need supplies now',
            'items' => [['item_id' => $item->id, 'quantity' => 10]],
        ]);

        $this->assertDatabaseHas('aid_requests', [
            'location' => 'Somewhere far',
            'priority' => 'critical',
        ]);
    }

    public function test_logistics_service_ranks_closer_fulfillable_warehouse_first(): void
    {
        $item = Item::factory()->create();

        $near = Warehouse::factory()->create(['latitude' => 31.40, 'longitude' => 34.35, 'status' => 'active']);
        $far = Warehouse::factory()->create(['latitude' => 31.60, 'longitude' => 34.70, 'status' => 'active']);

        Inventory::create(['warehouse_id' => $near->id, 'item_id' => $item->id, 'quantity' => 500]);
        Inventory::create(['warehouse_id' => $far->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create(['latitude' => 31.41, 'longitude' => 34.36]);
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 50]);

        $ranked = app(LogisticsService::class)->rankWarehousesFor($aidRequest);

        $this->assertSame($near->id, $ranked->first()['warehouse']->id);
        $this->assertTrue($ranked->first()['can_fulfill']);
    }

    public function test_logistics_service_ranks_unfulfillable_warehouse_last(): void
    {
        $item = Item::factory()->create();

        $lowStock = Warehouse::factory()->create(['latitude' => 31.40, 'longitude' => 34.35, 'status' => 'active']);
        $wellStocked = Warehouse::factory()->create(['latitude' => 31.60, 'longitude' => 34.70, 'status' => 'active']);

        Inventory::create(['warehouse_id' => $lowStock->id, 'item_id' => $item->id, 'quantity' => 5]);
        Inventory::create(['warehouse_id' => $wellStocked->id, 'item_id' => $item->id, 'quantity' => 500]);

        $aidRequest = AidRequest::factory()->create(['latitude' => 31.41, 'longitude' => 34.36]);
        $aidRequest->requestItems()->create(['item_id' => $item->id, 'quantity' => 50]);

        $ranked = app(LogisticsService::class)->rankWarehousesFor($aidRequest);

        $this->assertSame($wellStocked->id, $ranked->first()['warehouse']->id);
        $this->assertFalse($ranked->last()['can_fulfill']);
    }

    public function test_delivery_photo_verification_defaults_to_needs_review_in_simulation_mode(): void
    {
        $aiService = app(AIService::class);
        $file = \Illuminate\Http\UploadedFile::fake()->image('proof.jpg');

        $result = $aiService->verifyDeliveryPhoto($file, ['Family Food Parcel']);

        $this->assertSame('needs_review', $result['status']);
    }
}
