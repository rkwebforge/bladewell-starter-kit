<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Schedule;

// Deletes sign-in history older than config('security.sign_in_history_days'). Needs the scheduler running in
// production: one cron entry, `* * * * * php artisan schedule:run` (see SECURITY.md).
Schedule::command('model:prune')->daily();
