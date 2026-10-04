<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\SignIn;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SignIn>
 */
final class SignInFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'succeeded' => fake()->boolean(85),
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
            'created_at' => fake()->dateTimeBetween('-60 days'),
        ];
    }
}
