<?php

declare(strict_types=1);

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_registration_page_renders(): void
    {
        $this->get('/register')->assertOk()->assertSee('name="password_confirmation"', false);
    }

    public function test_people_can_register_and_are_asked_to_verify_their_email(): void
    {
        Notification::fake();

        $this->post('/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $user = User::where('email', 'ana@example.com')->sole();
        $this->assertAuthenticatedAs($user);
        Notification::assertSentTo($user, VerifyEmail::class);

        $this->get('/dashboard')->assertRedirect(route('verification.notice', absolute: false));
    }

    public function test_an_email_that_is_taken_is_rejected(): void
    {
        User::factory()->create(['email' => 'ana@example.com']);

        $this->post('/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'a-long-password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_passwords_that_do_not_match_are_rejected(): void
    {
        $this->post('/register', [
            'name' => 'Ana Silva',
            'email' => 'ana@example.com',
            'password' => 'a-long-password',
            'password_confirmation' => 'something-else',
        ])->assertSessionHasErrors('password');
    }
}
