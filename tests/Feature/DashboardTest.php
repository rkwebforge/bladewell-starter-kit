<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\SignIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_sent_to_sign_in(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login', absolute: false));
    }

    public function test_unverified_people_are_sent_to_verify(): void
    {
        $this->actingAs(User::factory()->unverified()->create())
            ->get('/dashboard')
            ->assertRedirect(route('verification.notice', absolute: false));
    }

    public function test_it_shows_the_account_menu_with_named_nav_links(): void
    {
        $this->actingAs(User::factory()->create(['name' => 'Ana Silva']))
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('AS')
            ->assertSee(route('logout'))
            // The app name leads back to the home page.
            ->assertSee('<a href="'.route('home').'" class="font-semibold">', false)
            // The nav shows only icons on phones; the names stay for screen readers.
            ->assertSee('<span class="sr-only sm:not-sr-only">Dashboard</span>', false);
    }

    public function test_it_lists_only_your_own_sign_ins(): void
    {
        $user = User::factory()->create();
        SignIn::factory()->for($user)->create(['ip_address' => '203.0.113.7']);
        SignIn::factory()->for(User::factory())->create(['ip_address' => '198.51.100.99']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertSee('203.0.113.7')
            ->assertDontSee('198.51.100.99');
    }

    public function test_it_sorts_by_a_column(): void
    {
        $user = User::factory()->create();
        SignIn::factory()->for($user)->create(['ip_address' => '203.0.113.9']);
        SignIn::factory()->for($user)->create(['ip_address' => '203.0.113.1']);

        $this->actingAs($user)
            ->get('/dashboard?sort=ip_address&direction=asc')
            ->assertSeeInOrder(['203.0.113.1', '203.0.113.9']);
    }

    public function test_it_ignores_a_sort_that_is_not_a_column(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard?sort=user_id')
            ->assertOk();
    }

    public function test_it_warns_about_recent_wrong_passwords(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/dashboard')->assertDontSee('wrong password in the last 30 days');

        SignIn::factory()->for($user)->create(['succeeded' => false, 'created_at' => now()->subDay()]);

        $this->actingAs($user)->get('/dashboard')->assertSee('1 wrong password in the last 30 days');
    }
}
