<?php

declare(strict_types=1);

namespace Tests\Feature;

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

    public function test_it_lists_users_with_the_account_menu(): void
    {
        $user = User::factory()->create(['name' => 'Ana Silva']);
        User::factory()->create(['name' => 'Ben Carter']);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Ben Carter')
            ->assertSee('AS')
            ->assertSee(route('logout'))
            // The nav shows only icons on phones; the names stay for screen readers.
            ->assertSee('<span class="sr-only sm:not-sr-only">Dashboard</span>', false);
    }

    public function test_it_sorts_by_a_column(): void
    {
        $user = User::factory()->create(['name' => 'Zoe Adams']);
        User::factory()->create(['name' => 'Ana Silva']);

        $this->actingAs($user)
            ->get('/dashboard?sort=name&direction=asc')
            ->assertSeeInOrder(['Ana Silva', 'Zoe Adams']);
    }

    public function test_it_ignores_a_sort_that_is_not_a_column(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/dashboard?sort=password')
            ->assertOk();
    }
}
