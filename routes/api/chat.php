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

Route::get('/conversations/{conversationId}/messages', [MessageController::class, 'visitorMessages']);
Route::get('/products/{productId}', [ProductController::class, 'show']);

// ── Admin — protegidas ────────────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // ⚠️ /archived ANTES de /{id} para evitar conflito de parâmetro
    Route::get('/admin/conversations/archived',               [ConversationController::class, 'indexArchived']);

    Route::get('/admin/conversations',                        [ConversationController::class, 'index']);
    Route::get('/admin/conversations/{id}',                   [ConversationController::class, 'show']);
    Route::get('/admin/conversations/{conversationId}/messages', [MessageController::class, 'index']);

    Route::patch('/admin/conversations/{id}/archive',         [ConversationController::class, 'archive']);
    Route::patch('/admin/conversations/{id}/unarchive',       [ConversationController::class, 'unarchive']);
    Route::delete('/admin/conversations/{id}',                [ConversationController::class, 'destroy']);
});