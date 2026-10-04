<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Auth\Events\OtherDeviceLogout;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class SessionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_session_ends_once_the_password_changes_elsewhere(): void
    {
        $user = User::factory()->create();

        // This session now holds the password hash it was signed in with.
        $this->actingAs($user)->get('/dashboard')->assertOk();

        // The password changes in another session (a reset, or the person on another device).
        $user->forceFill(['password' => 'a-new-long-password'])->save();

        $this->get('/dashboard')->assertRedirect(route('login', absolute: false));
        $this->assertGuest();
    }

    public function test_changing_your_password_keeps_you_signed_in_here(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertOk();
        $this->from('/dashboard')->put('/settings/password', [
            'current_password' => 'password',
            'password' => 'a-new-long-password',
            'password_confirmation' => 'a-new-long-password',
        ])->assertSessionHasNoErrors();

        $this->get('/dashboard')->assertOk();
    }

    public function test_other_devices_can_be_signed_out_with_the_password(): void
    {
        Event::fake([OtherDeviceLogout::class]);
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/dashboard')
            ->delete('/settings/other-devices', ['password' => 'password'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('success');

        Event::assertDispatched(OtherDeviceLogout::class);
        $this->get('/dashboard')->assertOk();
    }

    public function test_signing_out_other_devices_needs_the_right_password(): void
    {
        Event::fake([OtherDeviceLogout::class]);

        $this->actingAs(User::factory()->create())
            ->delete('/settings/other-devices', ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('otherDevices', 'password');

        Event::assertNotDispatched(OtherDeviceLogout::class);
    }

    public function test_the_password_can_be_confirmed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/confirm-password')->assertOk();

        $this->actingAs($user)
            ->post('/confirm-password', ['password' => 'wrong-password'])
            ->assertSessionHasErrors('password');

        $this->actingAs($user)
            ->post('/confirm-password', ['password' => 'password'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('auth.password_confirmed_at');
    }

    public function test_the_session_cookie_is_encrypted_http_only_and_secure_in_production(): void
    {
        $this->assertTrue(config('session.encrypt'));
        $this->assertTrue(config('session.http_only'));
        $this->assertSame('lax', config('session.same_site'));

        $config = require config_path('session.php');
        $this->assertFalse($config['secure']);

        [$server, $env] = [$_SERVER['APP_ENV'] ?? null, $_ENV['APP_ENV'] ?? null];
        $_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = 'production';

        try {
            $config = require config_path('session.php');
        } finally {
            [$_SERVER['APP_ENV'], $_ENV['APP_ENV']] = [$server, $env];
        }

        $this->assertTrue($config['secure']);
    }
}
