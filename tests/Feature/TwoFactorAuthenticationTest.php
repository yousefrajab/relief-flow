<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\TwoFactorCodeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    private function captureCode(User $user): string
    {
        return Notification::sent($user, TwoFactorCodeNotification::class)->last()->code;
    }

    public function test_admin_login_requires_two_factor_code_and_does_not_authenticate_yet(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $admin->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('two-factor.show'));
        $this->assertGuest();
        Notification::assertSentTo($admin, TwoFactorCodeNotification::class);
    }

    public function test_non_admin_login_bypasses_two_factor(): void
    {
        Notification::fake();

        $coordinator = User::factory()->coordinator()->create(['password' => Hash::make('password')]);

        $response = $this->post('/login', [
            'email' => $coordinator->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($coordinator);
        Notification::assertNotSentTo($coordinator, TwoFactorCodeNotification::class);
    }

    public function test_correct_code_completes_admin_login(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $code = $this->captureCode($admin);

        $response = $this->post('/two-factor', ['code' => $code]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
        $this->assertNull($admin->fresh()->two_factor_code);
    }

    public function test_incorrect_code_does_not_authenticate(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);

        $response = $this->post('/two-factor', ['code' => '000000']);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_expired_code_does_not_authenticate(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $code = $this->captureCode($admin);

        $admin->forceFill(['two_factor_expires_at' => now()->subMinute()])->save();

        $response = $this->post('/two-factor', ['code' => $code]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_code_cannot_be_reused_after_a_successful_verification(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $code = $this->captureCode($admin);

        $this->post('/two-factor', ['code' => $code]);
        $this->post('/logout');

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $response = $this->post('/two-factor', ['code' => $code]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_repeated_wrong_attempts_are_rate_limited(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $code = $this->captureCode($admin);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/two-factor', ['code' => '000000']);
        }

        $response = $this->post('/two-factor', ['code' => $code]);

        $response->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_resend_issues_a_new_code_that_invalidates_the_old_one(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create(['password' => Hash::make('password')]);

        $this->post('/login', ['email' => $admin->email, 'password' => 'password']);
        $oldCode = $this->captureCode($admin);

        $this->post('/two-factor/resend');

        Notification::assertSentTimes(TwoFactorCodeNotification::class, 2);
        $newCode = $this->captureCode($admin);
        $this->assertNotSame($oldCode, $newCode);

        $oldAttempt = $this->post('/two-factor', ['code' => $oldCode]);
        $oldAttempt->assertSessionHasErrors('code');
        $this->assertGuest();

        $newAttempt = $this->post('/two-factor', ['code' => $newCode]);
        $newAttempt->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_cannot_access_two_factor_page_without_a_pending_login(): void
    {
        $response = $this->get('/two-factor');

        $response->assertRedirect(route('login'));
    }
}
