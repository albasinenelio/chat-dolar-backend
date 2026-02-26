<?php

use App\Http\Controllers\Api\ConversationController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\TypingController;
use Illuminate\Support\Facades\Route;

Route::post('/conversations', [ConversationController::class, 'store']);
Route::post('/messages', [MessageController::class, 'store']);

// Visitante consulta as suas mensagens (sem auth)
Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'visitorMessages']);

// Sinal de digitação — público (visitante não tem auth)
Route::post('/typing', [TypingController::class, 'notify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/admin/conversations', [ConversationController::class, 'index']);
    Route::get('/admin/conversations/{id}', [ConversationController::class, 'show']);
    Route::get('/admin/conversations/{conversationId}/messages', [MessageController::class, 'index']);
});