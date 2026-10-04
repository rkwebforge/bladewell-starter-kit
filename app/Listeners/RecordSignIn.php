<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\SignIn;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Str;

/**
 * Keeps each sign-in, and each wrong password for an existing account, in that account's history. Attempts on
 * emails with no account aren't kept: they belong to nobody, and storing them would only collect strangers' data.
 */
final class RecordSignIn
{
    public function handleLogin(Login $event): void
    {
        $this->record($event->user, succeeded: true);
    }

    public function handleFailed(Failed $event): void
    {
        $this->record($event->user, succeeded: false);
    }

    private function record(mixed $user, bool $succeeded): void
    {
        if (! $user instanceof User) {
            return;
        }

        SignIn::create([
            'user_id' => $user->id,
            'succeeded' => $succeeded,
            'ip_address' => request()->ip(),
            'user_agent' => Str::limit((string) request()->userAgent(), 509),
        ]);
    }
}
