<?php

use App\Http\Controllers\API\Messaging\ConversationController;
use App\Http\Controllers\API\Messaging\MessageController;
use App\Http\Controllers\API\Notification\DeviceController;
use App\Http\Controllers\API\Notification\NotificationController;
use App\Http\Controllers\API\Patient\ProfileController;
use App\Http\Controllers\API\Patient\PsychologistDirectoryController;
use App\Http\Controllers\API\Speech\SpeechController;
use App\Http\Controllers\API\Subscription\PlanController;
use App\Http\Controllers\API\Subscription\SubscriptionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Patient Routes
|--------------------------------------------------------------------------
*/

Route::prefix('patient')->middleware(['auth.jwt', 'role:patient', 'geoblock'])->group(function () {

    Route::get('profile', [ProfileController::class, 'show']);
    Route::put('profile', [ProfileController::class, 'update']);

    Route::get('psychologists', [PsychologistDirectoryController::class, 'index']);
    // Legacy binding used {psychologist:uuid}; endpoint remains unchanged.
    Route::get('psychologists/{professional:uuid}', [PsychologistDirectoryController::class, 'show']);

    Route::apiResource('conversations', ConversationController::class)->only(['index', 'store', 'show']);
    Route::get('conversations/{conversation:uuid}/messages', [MessageController::class, 'index']);
    Route::post('conversations/{conversation:uuid}/messages', [MessageController::class, 'store']);
    Route::post('conversations/{conversation:uuid}/messages/audio', [MessageController::class, 'storeAudio']);
    Route::patch('messages/{message}/read', [MessageController::class, 'markRead']);

    Route::post('speech/transcribe', [SpeechController::class, 'transcribe']);

    Route::get('plans', [PlanController::class, 'index']);

    Route::get('subscription', [SubscriptionController::class, 'current']);
    Route::post('subscription/subscribe', [SubscriptionController::class, 'subscribe']);
    Route::post('subscription/cancel', [SubscriptionController::class, 'cancel']);

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
