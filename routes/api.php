<?php

use App\Http\Controllers\Api\LocationUpdateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — trusted integrations only.
|
| Location updates require a Sanctum bearer token with the
| `update-location` ability, issued from Admin → API Tokens.
| Always call over HTTPS in production.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth:sanctum', 'throttle:60,1'])
    ->prefix('v1')
    ->group(function () {
        Route::post('shipments/{trackingNumber}/location', [LocationUpdateController::class, 'store'])
            ->name('api.shipments.location');
    });
