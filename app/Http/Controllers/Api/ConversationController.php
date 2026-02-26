<?php

namespace App\Http\Controllers\Api;

use App\Actions\Chat\StartConversationAction;
use App\Contracts\ConversationRepositoryInterface;
use App\DTOs\StartConversationDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Chat\StartConversationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ConversationController extends Controller
{
    public function __construct(
        private readonly ConversationRepositoryInterface $repository,
    ) {}

    /**
     * Visitante inicia conversa (rota pública).
     */
    public function store(StartConversationRequest $request, StartConversationAction $action): JsonResponse
    {
        try {
            $conversation = $action->execute(
                StartConversationDTO::fromArray($request->validated())
            );

            return response()->json([
                'conversation_id' => $conversation->id,
                'visitor_id'      => $conversation->visitor_id,
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Destino inválido.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao iniciar conversa.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Admin lista conversas (filtrado por role).
     */
    public function index(Request $request): JsonResponse
    {
        $conversations = $this->repository->listForUser($request->user());

        return response()->json([
            'data' => $conversations->map(fn($c) => [
                'id'              => $c->id,
                'tenant_id'       => $c->tenant_id,
                'visitor_id'      => $c->visitor_id,
                'visitor_name'    => $c->visitor_name,
                'product_id'      => $c->product_id,
                'unread_count'    => $c->unread_count,
                'last_message_at' => $c->last_message_at?->toISOString(),
                'created_at'      => $c->created_at->toISOString(),
            ]),
        ]);
    }

    /**
     * Admin abre uma conversa específica.
     */
    public function show(Request $request, string $id): JsonResponse
    {
        $conversation = $this->repository->findForUser($id, $request->user());

        if (!$conversation) {
            return response()->json(['message' => 'Conversa não encontrada.'], 404);
        }

        // Marcar mensagens como lidas
        $conversation->markAsRead();

        return response()->json([
            'data' => [
                'id'           => $conversation->id,
                'tenant_id'    => $conversation->tenant_id,
                'visitor_id'   => $conversation->visitor_id,
                'visitor_name' => $conversation->visitor_name,
                'product_id'   => $conversation->product_id,
                'created_at'   => $conversation->created_at->toISOString(),
            ],
        ]);
    }
}