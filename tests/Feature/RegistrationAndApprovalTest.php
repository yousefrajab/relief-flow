<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationAndApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_coordinator_can_register_and_starts_pending(): void
    {
        $response = $this->post('/register', [
            'name' => 'New Coordinator',
            'email' => 'coordinator@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'coordinator',
            'phone' => '0599000000',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('account.pending'));
        $this->assertDatabaseHas('users', [
            'email' => 'coordinator@test.com',
            'role' => 'coordinator',
            'status' => 'pending_verification',
        ]);
    }

    public function test_cannot_register_as_admin(): void
    {
        $response = $this->post('/register', [
            'name' => 'Sneaky Admin',
            'email' => 'sneaky@test.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertDatabaseMissing('users', ['email' => 'sneaky@test.com']);
    }

    public function test_pending_user_is_redirected_away_from_dashboard(): void
    {
        $user = User::factory()->coordinator()->pendingVerification()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect(route('account.pending'));
    }

    public function test_admin_can_approve_pending_user(): void
    {
        $admin = User::factory()->admin()->create();
        $pending = User::factory()->coordinator()->pendingVerification()->create();

        $response = $this->actingAs($admin)->post("/users/{$pending->id}/approve");

        $response->assertRedirect(route('admin.users'));
        $this->assertSame('active', $pending->fresh()->status);
    }

    public function test_admin_can_suspend_active_user(): void
    {
        $admin = User::factory()->admin()->create();
        $active = User::factory()->coordinator()->create();

        $this->actingAs($admin)->post("/users/{$active->id}/reject");

        $this->assertSame('suspended', $active->fresh()->status);
    }

    public function test_non_admin_cannot_approve_users(): void
    {
        $manager = User::factory()->depotManager()->create();
        $pending = User::factory()->coordinator()->pendingVerification()->create();

        $response = $this->actingAs($manager)->post("/users/{$pending->id}/approve");

        $response->assertForbidden();
        $this->assertSame('pending_verification', $pending->fresh()->status);
    }

    public function test_suspended_user_cannot_access_dashboard(): void
    {
        $suspended = User::factory()->coordinator()->create(['status' => 'suspended']);

        $response = $this->actingAs($suspended)->get('/dashboard');

        $response->assertRedirect(route('account.pending'));
    }
}
