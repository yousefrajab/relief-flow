<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_guests_are_eventually_redirected_to_login(): void
    {
        $response = $this->get('/');
        $response->assertRedirect(route('dashboard'));

        $response = $this->get('/dashboard');
        $response->assertRedirect(route('login'));
    }
}
