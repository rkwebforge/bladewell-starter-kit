<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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

        // Once the app has a database: while you build, an error when code loads relations one row at a time (slow
        // pages), sets a field that isn't fillable (silently dropped), or reads one that wasn't selected. Production
        // stays lenient so it never breaks.
        Model::shouldBeStrict(! $production);

        // No `migrate:fresh`, `db:wipe` or rolling back in production: they delete data.
        DB::prohibitDestructiveCommands($production);
    }
}
