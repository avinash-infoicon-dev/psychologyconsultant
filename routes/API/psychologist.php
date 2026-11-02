<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Psychologist\ProfileController;
use App\Http\Controllers\API\Psychologist\PatientListController;
use App\Http\Controllers\API\Psychologist\ClinicalNoteController;
use App\Http\Controllers\API\Messaging\ConversationController;
use App\Http\Controllers\API\Messaging\MessageController;
use App\Http\Controllers\API\Notification\NotificationController;
use App\Http\Controllers\API\Notification\DeviceController;

/*
|--------------------------------------------------------------------------
| Psychologist Routes
|--------------------------------------------------------------------------
*/

Route::prefix('psychologist')->middleware(['auth.jwt', 'role:psychologist', 'geoblock'])->group(function () {

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);
    Route::post('profile/submit-verification', [ProfileController::class, 'submitVerification']);

    Route::get('patients', [PatientListController::class, 'index']);
    Route::get('patients/{patient}', [PatientListController::class, 'show']);

    Route::apiResource('conversations', ConversationController::class)->only(['index', 'show']);
    Route::get('conversations/{conversation:uuid}/messages', [MessageController::class, 'index']);
    Route::post('conversations/{conversation:uuid}/messages', [MessageController::class, 'store']);
    Route::post('conversations/{conversation:uuid}/messages/audio', [MessageController::class, 'storeAudio']);
    Route::patch('messages/{message}/read', [MessageController::class, 'markRead']);

    Route::apiResource('patients.clinical-notes', ClinicalNoteController::class)
        ->shallow()
        ->parameters(['clinical-notes' => 'clinicalNote'])
        ->except(['destroy']);

    Route::get('notifications', [NotificationController::class, 'index']);
    Route::get('notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('notifications/read-all', [NotificationController::class, 'markAllRead']);
    Route::patch('notifications/{notification}/read', [NotificationController::class, 'markRead']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
    Route::get('notification-preferences', [NotificationController::class, 'preferences']);
    Route::put('notification-preferences', [NotificationController::class, 'updatePreferences']);

    Route::post('devices', [DeviceController::class, 'store']);
    Route::delete('devices/{fcmToken}', [DeviceController::class, 'destroy'])->where('fcmToken', '.*');
});
