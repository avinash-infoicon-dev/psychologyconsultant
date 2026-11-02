<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Admin\DashboardController;
use App\Http\Controllers\API\Admin\UserController;
use App\Http\Controllers\API\Admin\PlanController;
use App\Http\Controllers\API\Admin\PaymentController;
use App\Http\Controllers\API\Admin\SettingsController;
use App\Http\Controllers\API\Admin\LogsController;
use App\Http\Controllers\API\Admin\ReportsController;
use App\Http\Controllers\API\Admin\PsychologistController;

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth.jwt', 'role:admin'])->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index']);

    Route::apiResource('users', UserController::class);
    Route::apiResource('plans', PlanController::class);

    Route::get('psychologists', [PsychologistController::class, 'index']);
    Route::patch('psychologists/{psychologist}/start-review', [PsychologistController::class, 'startReview']);
    Route::patch('psychologists/{psychologist}/verify', [PsychologistController::class, 'verify']);
    Route::patch('psychologists/{psychologist}/reject', [PsychologistController::class, 'reject']);
    Route::patch('psychologists/{psychologist}/suspend', [PsychologistController::class, 'suspend']);
    Route::patch('psychologists/{psychologist}/restore', [PsychologistController::class, 'restore']);

    Route::get('payments', [PaymentController::class, 'index']);
    Route::get('payments/{payment}', [PaymentController::class, 'show']);
    Route::post('payments/{payment}/refund', [PaymentController::class, 'refund']);

    Route::get('settings', [SettingsController::class, 'index']);
    Route::put('settings', [SettingsController::class, 'update']);

    Route::get('logs/activity', [LogsController::class, 'activity']);
    Route::get('logs/audit', [LogsController::class, 'audit']);

    Route::get('reports/summary', [ReportsController::class, 'summary']);
    Route::get('reports/export', [ReportsController::class, 'export']);
});
