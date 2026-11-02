<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
| Kept deliberately thin. Each module owns its own route file so the
| team can work on Messaging, Payments, Admin, etc. without touching a
| single monolithic routes file. Controllers referenced here are added
| in later build steps (Step 3+); this file wires up the structure now.
*/

Route::prefix('v1')->group(function () {
    require __DIR__.'/auth.php';
    require __DIR__.'/patient.php';
    require __DIR__.'/psychologist.php';
    require __DIR__.'/admin.php';
    require __DIR__.'/payment.php';
    require __DIR__.'/webhook.php';
});
