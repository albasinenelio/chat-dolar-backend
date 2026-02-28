<?php

use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TypingController;
use App\Http\Controllers\Api\UploadController;
use Illuminate\Support\Facades\Route;

// ── Públicas — visitante ──────────────────────────────────────────────────────
Route::post('/conversations', [ConversationController::class, 'store']);
Route::post('/messages', [MessageController::class, 'store']);
Route::post('/typing', [TypingController::class, 'notify']);
Route::post('/upload', [UploadController::class, 'store']);

// Visitante consulta as suas mensagens (sem auth)
Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'visitorMessages']);

// Visitante consulta preço do produto pelo ID externo + tenant_id
Route::get('/products/{productId}', [ProductController::class, 'show']);

// ── Admin — protegidas ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/conversations', [ConversationController::class, 'index']);
    Route::get('/admin/conversations/{id}', [ConversationController::class, 'show']);
    Route::get('/admin/conversations/{conversationId}/messages', [MessageController::class, 'index']);
});