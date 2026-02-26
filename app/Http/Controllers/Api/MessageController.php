<?php

namespace App\Http\Controllers\Api;

use App\Actions\Chat\SendMessageAction;
use App\Contracts\ConversationRepositoryInterface;
use App\DTOs\SendMessageDTO;
use App\Enums\SenderType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\SendMessageRequest;
use App\Models\Conversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class MessageController extends Controller
{
    public function __construct(
        private readonly ConversationRepositoryInterface $repository,
    ) {}

    /**
     * Visitante ou admin envia mensagem.
     */
    public function store(SendMessageRequest $request, SendMessageAction $action): JsonResponse
    {
        try {
            $sender = $request->user() ? SenderType::Admin : SenderType::Visitor;

            $message = $action->execute(
                SendMessageDTO::fromArray($request->validated(), $sender)
            );

            return response()->json([
                'data' => $this->formatMessage($message),
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Dados inválidos.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao enviar mensagem.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Admin busca histórico de mensagens de uma conversa.
     */
    public function index(Request $request, string $conversationId): JsonResponse
    {
        $conversation = $this->repository->findForUser($conversationId, $request->user());

        if (!$conversation) {
            return response()->json(['message' => 'Conversa não encontrada.'], 404);
        }

        $messages = $conversation->messages()->oldest()->get();

        return response()->json([
            'data' => $messages->map(fn($m) => $this->formatMessage($m)),
        ]);
    }

    /**
     * Visitante consulta as suas próprias mensagens (sem autenticação).
     * Qualquer pessoa com o UUID da conversa pode ver as mensagens.
     */
    public function visitorMessages(Request $request, string $conversationId): JsonResponse
    {
        $conversation = Conversation::find($conversationId);

        if (!$conversation) {
            return response()->json(['message' => 'Conversa não encontrada.'], 404);
        }

        $messages = $conversation->messages()->oldest()->get();

        return response()->json([
            'data' => $messages->map(fn($m) => $this->formatMessage($m)),
        ]);
    }

    private function formatMessage($m): array
    {
        return [
            'id'              => $m->id,
            'conversation_id' => $m->conversation_id,
            'sender_type'     => $m->sender_type,
            'sender'          => $m->sender_type, // alias para compatibilidade frontend
            'type'            => $m->type,
            'content'         => $m->content,
            'image_url'       => $m->image_url,
            'caption'         => $m->caption,
            'read'            => $m->read,
            'created_at'      => $m->created_at->toISOString(),
        ];
    }
}