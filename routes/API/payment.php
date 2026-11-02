<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Payment\PaymentController;
use App\Http\Controllers\API\Payment\InvoiceController;

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
| General payment endpoints shared across roles (patients paying,
| retrieving their own invoices, etc). Admin-only payment management
| lives in admin.php; gateway webhooks live in webhook.php.
*/

Route::prefix('payments')->middleware(['auth.jwt'])->group(function () {
    Route::post('intent', [PaymentController::class, 'createIntent']);
    Route::post('confirm', [PaymentController::class, 'confirm']);

    Route::get('invoices', [InvoiceController::class, 'index']);
    Route::get('invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::get('invoices/{invoice}/download', [InvoiceController::class, 'download']);
});
