<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class DemoAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeding_makes_the_demo_account_on_your_own_machine(): void
    {
        $this->artisan('db:seed')->assertSuccessful();

        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_seeding_a_live_server_never_makes_the_demo_account(): void
    {
        foreach (['production', 'staging'] as $environment) {
            $this->app['env'] = $environment;

            $this->artisan('db:seed', ['--force' => true])
                ->expectsOutputToContain('Skipped the demo account')
                ->assertSuccessful();
        }

        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('sign_ins', 0);
    }
}
