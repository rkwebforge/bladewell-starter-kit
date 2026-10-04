<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_links_to_every_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('See the dashboard')
            ->assertSee('href="'.route('dashboard').'"', false)
            ->assertSee('href="'.route('profile.edit').'"', false)
            ->assertSee('href="'.route('login').'"', false)
            ->assertSee('href="'.route('register').'"', false)
            ->assertSee('Needs sign in');
    }

    public function test_guests_following_the_table_link_sign_in_and_land_on_it(): void
    {
        $user = User::factory()->create();

        $this->get('/dashboard')->assertRedirect(route('login', absolute: false));

        $this->post('/login', ['email' => $user->email, 'password' => 'password'])
            ->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_the_create_account_links_go_when_registration_is_off(): void
    {
        config(['security.registration' => false]);

        $this->get('/')->assertOk()->assertDontSee('href="'.route('register').'"', false);
        $this->get('/login')->assertOk()->assertDontSee('No account yet?');
    }

    public function test_signed_in_people_can_reach_their_profile_and_sign_out_from_home(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Ana Silva']))
            ->get('/')
            ->assertSee('AS')
            ->assertSee('href="'.route('profile.edit').'"', false)
            ->assertSee('action="'.route('logout').'"', false);

        $this->post('/logout');
        $this->get('/')->assertDontSee('action="'.route('logout').'"', false);
    }

    public function test_signed_in_people_are_not_told_to_sign_in(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertDontSee('Needs sign in')
            ->assertDontSee("You'll be asked to sign in first.");
    }
}
