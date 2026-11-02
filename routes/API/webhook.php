<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Payment\WebhookController;

/*
|--------------------------------------------------------------------------
| Webhook Routes
|--------------------------------------------------------------------------
| These are unauthenticated (gateways can't send a Sanctum token) but
| MUST verify a signature inside the controller/middleware. Excluded
| from CSRF protection via bootstrap/app.php `withMiddleware()`.
*/

Route::prefix('webhooks')->middleware(['verify.webhook.signature'])->group(function () {
    Route::post('stripe', [WebhookController::class, 'stripe'])->name('webhooks.stripe');
    Route::post('twint', [WebhookController::class, 'twint'])->name('webhooks.twint');
});
