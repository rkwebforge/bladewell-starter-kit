<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\Rules\Password;
use Tests\TestCase;

final class PasswordRulesTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_passwords_need_twelve_characters(): void
    {
        $this->post('/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'password' => 'elevenchars',
            'password_confirmation' => 'elevenchars',
        ])->assertSessionHasErrors('password');

        $this->assertGuest();
    }

    public function test_the_field_shows_the_same_rules_the_server_checks(): void
    {
        config(['security.passwords.min' => 16]);

        $this->get('/register')->assertSee('16 characters');
    }

    public function test_breached_passwords_are_checked_in_production_only(): void
    {
        $this->assertFalse(Password::defaults()->appliedRules()['uncompromised']);

        $this->app['env'] = 'production';
        $this->assertTrue(Password::defaults()->appliedRules()['uncompromised']);

        config(['security.passwords.check_breached' => false]);
        $this->assertFalse(Password::defaults()->appliedRules()['uncompromised']);
    }
}
