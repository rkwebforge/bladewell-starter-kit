<?php

declare(strict_types=1);

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Settings\OtherDevicesController;
use App\Http\Controllers\Settings\PasswordController;
use App\Http\Controllers\Settings\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

// auth.session signs a person out everywhere else once their password changes, so a stolen session stops working.
Route::middleware(['auth', 'auth.session', 'verified'])->group(function (): void {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    // Changing the email is how an account gets taken over (change it, then reset the password), so the profile
    // asks for the password first, and again after config('auth.password_timeout') seconds (3 hours).
    Route::middleware('password.confirm')->group(function (): void {
        Route::get('settings/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('settings/profile', [ProfileController::class, 'update'])->name('profile.update');
    });

    // These ask for the password in their own forms.
    Route::delete('settings/profile', [ProfileController::class, 'destroy'])->middleware('throttle:6,1')->name('profile.destroy');
    Route::put('settings/password', PasswordController::class)->middleware('throttle:6,1')->name('password.update');
    Route::delete('settings/other-devices', OtherDevicesController::class)->middleware('throttle:6,1')->name('other-devices.destroy');
});

require __DIR__.'/auth.php';
