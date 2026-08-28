<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_the_impact_report_as_csv(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/reports/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('Deliveries completed', $response->streamedContent());
    }

    public function test_coordinator_cannot_export_the_impact_report(): void
    {
        $coordinator = User::factory()->coordinator()->create();

        $response = $this->actingAs($coordinator)->get('/reports/export');

        $response->assertForbidden();
    }

    public function test_admin_can_export_the_impact_report_as_pdf(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/reports/export-pdf');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringStartsWith('%PDF', $response->getContent());
    }

    public function test_coordinator_cannot_export_the_impact_report_as_pdf(): void
    {
        $coordinator = User::factory()->coordinator()->create();

        $response = $this->actingAs($coordinator)->get('/reports/export-pdf');

        $response->assertForbidden();
    }
}
