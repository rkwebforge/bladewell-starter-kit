<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\SignIn;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // The demo account's password is "password", printed in the README for anyone to read. On a live server it
        // would be a door left open, so it's only ever made on your own machine.
        if (! app()->environment(['local', 'testing'])) {
            $this->command?->warn('Skipped the demo account: it is only created when APP_ENV is local.');

            return;
        }

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // A few pages of sign-in history for the dashboard table, a couple of wrong passwords among them.
        SignIn::factory(28)->for($user)->create();
    }
}
