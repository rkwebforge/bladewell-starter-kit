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
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // A few pages of sign-in history for the dashboard table, a couple of wrong passwords among them.
        SignIn::factory(28)->for($user)->create();
    }
}
