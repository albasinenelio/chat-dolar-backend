<?php

namespace App\Http\Controllers\Api;

use App\Events\UserTyping;
use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TypingController extends Controller
{
    /**
     * Recebe sinal de digitação e faz broadcast no canal da conversa.
     * Rota pública — visitante não autenticado pode chamar.
     */
    public function notify(Request $request): JsonResponse
    {
        $request->validate([
            'conversation_id' => ['required', 'uuid'],
        ]);

        $conversationId = $request->input('conversation_id');

        // Garante que a conversa existe
        if (!Conversation::where('id', $conversationId)->exists()) {
            return response()->json(['message' => 'Conversa não encontrada.'], 404);
        }

        // Determina quem está a digitar
        $senderType = $request->user() ? 'admin' : 'visitor';

        broadcast(new UserTyping($conversationId, $senderType));

        return response()->json(['ok' => true]);
    }
}