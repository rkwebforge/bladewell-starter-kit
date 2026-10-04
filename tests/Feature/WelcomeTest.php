<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class WelcomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_home_page_links_to_the_users_table_and_every_page(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('See the users table')
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

    public function test_signed_in_people_are_not_told_to_sign_in(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/')
            ->assertOk()
            ->assertDontSee('Needs sign in')
            ->assertDontSee("You'll be asked to sign in first.");
    }
}
