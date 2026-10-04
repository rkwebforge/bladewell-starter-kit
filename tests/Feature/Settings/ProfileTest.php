<?php

declare(strict_types=1);

namespace Tests\Feature\Settings;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

final class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_settings_page_renders_with_the_current_details(): void
    {
        $user = User::factory()->create();

        $this->confirmedAs($user)
            ->get('/settings/profile')
            ->assertOk()
            ->assertSee($user->name)
            ->assertSee($user->email);
    }

    public function test_the_name_can_be_changed(): void
    {
        $user = User::factory()->create();

        $this->confirmedAs($user)
            ->patch('/settings/profile', ['name' => 'Ana Silva', 'email' => $user->email])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile')
            ->assertSessionHas('success');

        $this->assertSame('Ana Silva', $user->fresh()->name);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function test_a_new_email_has_to_be_verified_again(): void
    {
        $user = User::factory()->create();

        $this->confirmedAs($user)
            ->patch('/settings/profile', ['name' => $user->name, 'email' => 'new@example.com'])
            ->assertRedirect(route('verification.notice', absolute: false));

        $this->assertSame('new@example.com', $user->fresh()->email);
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_the_password_can_be_changed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/settings/profile')
            ->put('/settings/password', [
                'current_password' => 'password',
                'password' => 'a-new-long-password',
                'password_confirmation' => 'a-new-long-password',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect('/settings/profile');

        $this->assertTrue(Hash::check('a-new-long-password', $user->fresh()->password));
    }

    public function test_changing_the_password_needs_the_current_one(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put('/settings/password', [
                'current_password' => 'wrong-password',
                'password' => 'a-new-long-password',
                'password_confirmation' => 'a-new-long-password',
            ])
            ->assertSessionHasErrorsIn('updatePassword', 'current_password');
    }

    public function test_the_account_can_be_deleted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->delete('/settings/profile', ['password' => 'password'])
            ->assertRedirect(route('home', absolute: false));

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_deleting_needs_the_password_and_reopens_the_dialog(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->from('/settings/profile')
            ->delete('/settings/profile', ['password' => 'wrong-password'])
            ->assertSessionHasErrorsIn('userDeletion', 'password')
            ->assertRedirect('/settings/profile');

        $this->assertNotNull($user->fresh());

        $this->confirmedAs($user)
            ->from('/settings/profile')
            ->followingRedirects()
            ->delete('/settings/profile', ['password' => 'wrong-password'])
            ->assertSee('data-open-on-load', false);
    }

    public function test_the_profile_asks_for_the_password_first(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/settings/profile')->assertRedirect(route('password.confirm', absolute: false));
        $this->actingAs($user)
            ->patch('/settings/profile', ['name' => $user->name, 'email' => 'attacker@example.com'])
            ->assertRedirect(route('password.confirm', absolute: false));

        $this->assertSame($user->email, $user->fresh()->email);
    }

    // Signed in, with the password confirmed a moment ago, as the profile requires.
    private function confirmedAs(User $user): self
    {
        return $this->actingAs($user)->withSession(['auth.password_confirmed_at' => time()]);
    }
}
