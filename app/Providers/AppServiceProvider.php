<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $production = $this->app->isProduction();

        // Every link and redirect the app makes uses https:// in production, even behind a proxy that talks plain HTTP.
        URL::forceHttps($production);

        // While you build: an error when code loads relations one row at a time (slow pages), sets a field that isn't
        // fillable (silently dropped), or reads one that wasn't selected. Production stays lenient so it never breaks.
        Model::shouldBeStrict(! $production);

        // No `migrate:fresh`, `db:wipe` or rolling back in production: they delete data.
        DB::prohibitDestructiveCommands($production);

        // The rules every new password follows, set in config/security.php. Password::defaults() reads them, and
        // the password fields on the register and settings pages show the same rules.
        Password::defaults(function (): Password {
            $rules = config('security.passwords');
            $password = Password::min($rules['min']);

            if ($rules['mixed_case']) {
                $password->mixedCase();
            }
            if ($rules['numbers']) {
                $password->numbers();
            }
            if ($rules['symbols']) {
                $password->symbols();
            }

            return $this->app->isProduction() && $rules['check_breached'] ? $password->uncompromised() : $password;
        });

        // Account sign-ups per IP address, so a script can't create accounts in bulk.
        RateLimiter::for('register', fn (Request $request): Limit => Limit::perHour(10)->by($request->ip()));
    }
}
