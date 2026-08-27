<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_see_the_public_landing_page(): void
    {
        $response = $this->get('/');

        $response->assertOk();
    }

    public function test_guests_are_redirected_away_from_the_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect(route('login'));
    }
}
