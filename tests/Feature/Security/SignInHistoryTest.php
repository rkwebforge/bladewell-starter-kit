<?php

declare(strict_types=1);

namespace Tests\Feature\Security;

use App\Models\SignIn;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class SignInHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_ins_and_wrong_passwords_are_recorded(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['email' => $user->email, 'password' => 'wrong-password']);
        $this->withHeader('User-Agent', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 14_0) AppleWebKit/605.1.15 Chrome/129.0 Safari/605.1.15')
            ->post('/login', ['email' => $user->email, 'password' => 'password']);

        $this->assertSame([false, true], $user->signIns()->orderBy('id')->pluck('succeeded')->all());
        $this->assertSame('Chrome on macOS', $user->signIns()->latest('id')->firstOrFail()->device());
    }

    public function test_attempts_on_unknown_emails_are_not_kept(): void
    {
        $this->post('/login', ['email' => 'nobody@example.com', 'password' => 'password']);

        $this->assertDatabaseCount('sign_ins', 0);
    }

    public function test_old_history_is_pruned(): void
    {
        $user = User::factory()->create();
        $old = SignIn::factory()->for($user)->create(['created_at' => now()->subDays(91)]);
        $recent = SignIn::factory()->for($user)->create(['created_at' => now()->subDays(89)]);

        $this->artisan('model:prune', ['--model' => SignIn::class])->assertSuccessful();

        $this->assertModelMissing($old);
        $this->assertModelExists($recent);
    }

    public function test_history_goes_with_a_deleted_account(): void
    {
        $user = User::factory()->create();
        SignIn::factory()->for($user)->create();

        $this->actingAs($user)->delete('/settings/profile', ['password' => 'password']);

        $this->assertDatabaseCount('sign_ins', 0);
    }
}
