<?php

namespace Tests\Feature;

use App\Models\AidRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AidRequestFiltersAndExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_can_be_filtered_by_status(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = AidRequest::factory()->create(['location' => 'Pending Spot']);
        $rejected = AidRequest::factory()->rejected()->create(['location' => 'Rejected Spot']);

        $response = $this->actingAs($admin)->get('/aid-requests?status=pending');

        $response->assertOk();
        $response->assertSee('Pending Spot');
        $response->assertDontSee('Rejected Spot');
    }

    public function test_index_can_be_filtered_by_priority(): void
    {
        $admin = User::factory()->admin()->create();
        $critical = AidRequest::factory()->create(['location' => 'Critical Spot', 'priority' => 'critical']);
        $normal = AidRequest::factory()->create(['location' => 'Normal Spot', 'priority' => 'normal']);

        $response = $this->actingAs($admin)->get('/aid-requests?priority=critical');

        $response->assertOk();
        $response->assertSee('Critical Spot');
        $response->assertDontSee('Normal Spot');
    }

    public function test_index_can_be_searched_by_location(): void
    {
        $admin = User::factory()->admin()->create();
        AidRequest::factory()->create(['location' => 'Khan Younis Distribution Point']);
        AidRequest::factory()->create(['location' => 'Rafah Shelter']);

        $response = $this->actingAs($admin)->get('/aid-requests?search=Khan+Younis');

        $response->assertOk();
        $response->assertSee('Khan Younis Distribution Point');
        $response->assertDontSee('Rafah Shelter');
    }

    public function test_coordinator_filters_only_apply_within_their_own_requests(): void
    {
        $coordinator = User::factory()->coordinator()->create();
        $mine = AidRequest::factory()->for($coordinator)->create(['location' => 'My Request']);
        $theirs = AidRequest::factory()->create(['location' => 'Someone Elses Request']);

        $response = $this->actingAs($coordinator)->get('/aid-requests');

        $response->assertOk();
        $response->assertSee('My Request');
        $response->assertDontSee('Someone Elses Request');
    }

    public function test_export_returns_a_csv_with_matching_rows(): void
    {
        $admin = User::factory()->admin()->create();
        AidRequest::factory()->create(['location' => 'Export Me']);

        $response = $this->actingAs($admin)->get('/aid-requests/export?search=Export+Me');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Export Me', $response->streamedContent());
    }
}
