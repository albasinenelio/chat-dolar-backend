<?php

use App\Http\Controllers\Api\PushController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Push Routes — apenas admins autenticados
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/push/subscribe',   [PushController::class, 'subscribe']);
    Route::post('/push/unsubscribe', [PushController::class, 'unsubscribe']);
});