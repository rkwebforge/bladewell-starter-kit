<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class RegistrationLimitsTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_can_be_turned_off(): void
    {
        config(['security.registration' => false]);

        $this->get('/register')->assertNotFound();
        $this->post('/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
        ])->assertNotFound();

        $this->assertDatabaseCount('users', 0);
    }

    public function test_one_address_can_only_try_ten_sign_ups_an_hour(): void
    {
        foreach (range(1, 10) as $attempt) {
            $this->post('/register', [])->assertSessionHasErrors();
        }

        $this->post('/register', [])->assertTooManyRequests();
    }
}
