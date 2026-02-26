<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
| Prefixo /api aplicado no AppServiceProvider.
| Sanctum SPA: frontend deve chamar GET /sanctum/csrf-cookie antes do login.
*/

// ── Rotas públicas ────────────────────────────────────────────────────────────
Route::post('/auth/login',      [AuthController::class, 'login']);
Route::post('/auth/otp/verify', [AuthController::class, 'verifyOtp']);

// ── Rotas protegidas ──────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me',      [AuthController::class, 'me']);
});