<?php
use App\Http\Controllers\Auth\RegistrationFlowController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\TwoFactorLoginController;

// POST stays the same
Route::post('/register', [RegistrationFlowController::class, 'store'])->name('register.store');

// Move all GET/POST steps off the /register/* prefix:
Route::get('/auth/verify-otp', [RegistrationFlowController::class, 'showVerifyForm'])->name('register.verify.form');
Route::post('/auth/verify-otp', [RegistrationFlowController::class, 'verifyOtp'])->name('register.verify');
Route::post('/auth/resend-otp', [RegistrationFlowController::class, 'resendOtp'])->name('register.otp.resend');


Route::get('/welcome-2fa', [RegistrationFlowController::class, 'twoFactorOffer'])->name('register.2fa.offer');

Route::middleware('auth')->group(function () {
    Route::get('/2fa/setup', [TwoFactorController::class, 'showSetup'])->name('2fa.setup');
    Route::post('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');
    Route::post('/2fa/recovery-codes', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('2fa.recovery');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/2fa/confirm', [TwoFactorLoginController::class, 'show'])
        ->name('2fa.challenge.show');
    Route::post('/2fa/confirm', [TwoFactorLoginController::class, 'confirm'])
        ->name('2fa.challenge.confirm');
});
