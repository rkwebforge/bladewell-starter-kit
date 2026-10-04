<?php

declare(strict_types=1);

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    // Answers 404 while config('security.registration') is off.
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store'])->middleware('throttle:register');

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    // LoginRequest also locks an email + IP after 5 wrong passwords; this caps one IP trying many emails.
    Route::post('login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:30,1');

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->middleware('throttle:6,1')->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->middleware('throttle:6,1')->name('password.store');
});

Route::middleware('auth')->group(function (): void {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('email/verification-notification', EmailVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'create'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store'])->middleware('throttle:6,1');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
