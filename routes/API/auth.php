<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\Auth\TwoFactorController;
use App\Http\Controllers\API\Auth\PasswordResetController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
| Controllers are stubbed in Step 3 (Auth & Authorization). Route shape
| is defined now so the module boundary is locked in early.
*/

Route::prefix('auth')->group(function () {
    //Route::post('register', [AuthController::class, 'register'])->middleware('geoblock');
    Route::post('register', [AuthController::class, 'register']);
    Route::post('email/verify', [AuthController::class, 'verifyEmail']);
    Route::post('email/resend', [AuthController::class, 'resendEmailVerification']);

    Route::post('login', [AuthController::class, 'login'])->middleware('geoblock');
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth.jwt');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth.jwt');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth.jwt');

    Route::post('2fa/challenge', [TwoFactorController::class, 'challenge']);
    Route::post('2fa/verify', [TwoFactorController::class, 'verify']);

    Route::post('password/forgot', [PasswordResetController::class, 'sendResetLink']);
    Route::post('password/reset', [PasswordResetController::class, 'reset']);
});
