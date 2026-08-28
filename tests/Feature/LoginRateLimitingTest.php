<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginRateLimitingTest extends TestCase
{
    use RefreshDatabase;

    public function test_repeated_failed_logins_are_rate_limited(): void
    {
        $user = User::factory()->coordinator()->create(['password' => Hash::make('password')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_rate_limit_message_differs_from_normal_credential_failure(): void
    {
        $user = User::factory()->coordinator()->create(['password' => Hash::make('password')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);

        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertNotSame(__('These credentials do not match our records.'), $errors[0]);
    }

    public function test_successful_login_clears_the_rate_limiter(): void
    {
        $user = User::factory()->coordinator()->create(['password' => Hash::make('password')]);

        for ($i = 0; $i < 4; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        }

        $response = $this->post('/login', ['email' => $user->email, 'password' => 'password']);
        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);

        $this->post('/logout');

        // A single fresh failed attempt should not be blocked — the counter was reset on success.
        $response = $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        $response->assertSessionHasErrors('email');
        $errors = session('errors')->get('email');
        $this->assertSame(__('These credentials do not match our records.'), $errors[0]);
    }

    public function test_rate_limit_is_scoped_per_email(): void
    {
        $lockedOutUser = User::factory()->coordinator()->create(['password' => Hash::make('password')]);
        $otherUser = User::factory()->coordinator()->create(['password' => Hash::make('password')]);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $lockedOutUser->email, 'password' => 'wrong-password']);
        }

        $response = $this->post('/login', ['email' => $otherUser->email, 'password' => 'password']);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($otherUser);
    }
}
