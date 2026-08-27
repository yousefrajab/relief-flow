<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_their_profile(): void
    {
        $user = User::factory()->coordinator()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '0599111222',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertSame('Updated Name', $user->fresh()->name);
        $this->assertSame('updated@example.com', $user->fresh()->email);
    }

    public function test_user_can_upload_a_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->coordinator()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

        $response->assertRedirect(route('profile.edit'));
        $fresh = $user->fresh();
        $this->assertNotNull($fresh->avatar_path);
        Storage::disk('public')->assertExists($fresh->avatar_path);
    }

    public function test_uploading_a_new_photo_deletes_the_old_one(): void
    {
        Storage::fake('public');
        $user = User::factory()->coordinator()->create();

        $this->actingAs($user)->put('/profile', [
            'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('first.jpg'),
        ]);
        $firstPath = $user->fresh()->avatar_path;

        $this->actingAs($user)->put('/profile', [
            'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('second.jpg'),
        ]);

        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($user->fresh()->avatar_path);
    }

    public function test_non_image_avatar_upload_is_rejected(): void
    {
        Storage::fake('public');
        $user = User::factory()->coordinator()->create();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->create('document.pdf', 100),
        ]);

        $response->assertSessionHasErrors('avatar');
        $this->assertNull($user->fresh()->avatar_path);
    }

    public function test_user_can_remove_their_profile_photo(): void
    {
        Storage::fake('public');
        $user = User::factory()->coordinator()->create();
        $this->actingAs($user)->put('/profile', [
            'name' => $user->name, 'email' => $user->email, 'phone' => $user->phone,
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);
        $path = $user->fresh()->avatar_path;

        $response = $this->actingAs($user)->delete('/profile/avatar');

        $response->assertRedirect(route('profile.edit'));
        $this->assertNull($user->fresh()->avatar_path);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_user_can_update_their_password_with_correct_current_password(): void
    {
        $user = User::factory()->coordinator()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'old-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect(route('profile.edit'));
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_password_update_requires_correct_current_password(): void
    {
        $user = User::factory()->coordinator()->create(['password' => Hash::make('old-password')]);

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'wrong-password',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
