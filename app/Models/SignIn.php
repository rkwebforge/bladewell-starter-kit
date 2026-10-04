<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\SignInFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * One attempt to sign in to an account, kept so its owner can spot sign-ins that weren't them.
 */
#[Fillable(['user_id', 'succeeded', 'ip_address', 'user_agent'])]
final class SignIn extends Model
{
    /** @use HasFactory<SignInFactory> */
    use HasFactory, Prunable;

    public const UPDATED_AT = null;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'succeeded' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Deleted by `php artisan model:prune`, which routes/console.php runs daily.
    public function prunable(): Builder
    {
        return self::query()->where('created_at', '<', now()->subDays((int) config('security.sign_in_history_days')));
    }

    // "Chrome on macOS", read from the user agent. A rough guide for people, not something to make decisions on.
    public function device(): string
    {
        $agent = (string) $this->user_agent;

        $browser = match (true) {
            str_contains($agent, 'Edg/') => 'Edge',
            str_contains($agent, 'Firefox/') => 'Firefox',
            str_contains($agent, 'Chrome/') => 'Chrome',
            str_contains($agent, 'Safari/') => 'Safari',
            default => 'Unknown browser',
        };

        $system = match (true) {
            str_contains($agent, 'iPhone'), str_contains($agent, 'iPad') => 'iOS',
            str_contains($agent, 'Android') => 'Android',
            str_contains($agent, 'Windows') => 'Windows',
            str_contains($agent, 'Mac OS X') => 'macOS',
            str_contains($agent, 'Linux') => 'Linux',
            default => null,
        };

        return $system === null ? $browser : "{$browser} on {$system}";
    }
}
