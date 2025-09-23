<?php
use App\Http\Controllers\Auth\RegistrationFlowController;
use App\Http\Controllers\Auth\TwoFactorController;
use App\Http\Controllers\Auth\TwoFactorLoginController;

Route::post('/register', [RegistrationFlowController::class, 'store'])->name('register.store');

