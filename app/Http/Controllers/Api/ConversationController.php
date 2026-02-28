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
     * Visitante inicia conversa (pública).
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
            return response()->json(['message' => 'Destino inválido.', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao iniciar conversa.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Admin lista conversas activas.
     */
    public function index(Request $request): JsonResponse
    {
        $conversations = $this->repository->listForUser($request->user());

        return response()->json([
            'data' => $conversations->map(fn($c) => $this->formatConversation($c)),
        ]);
    }

    /**
     * Admin lista conversas arquivadas.
     */
    public function indexArchived(Request $request): JsonResponse
    {
        $conversations = $this->repository->listArchivedForUser($request->user());

        return response()->json([
            'data' => $conversations->map(fn($c) => $this->formatConversation($c)),
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

        $conversation->markAsRead();

        return response()->json(['data' => $this->formatConversation($conversation)]);
    }

    /**
     * Admin arquiva uma conversa activa.
     */
    public function archive(Request $request, string $id): JsonResponse
    {
        $success = $this->repository->archive($id, $request->user());

        if (!$success) {
            return response()->json(['message' => 'Conversa não encontrada ou já arquivada.'], 404);
        }

        return response()->json(['message' => 'Conversa arquivada com sucesso.']);
    }

    /**
     * Admin desarquiva uma conversa.
     */
    public function unarchive(Request $request, string $id): JsonResponse
    {
        $success = $this->repository->unarchive($id, $request->user());

        if (!$success) {
            return response()->json(['message' => 'Conversa não encontrada ou não está arquivada.'], 404);
        }

        return response()->json(['message' => 'Conversa desarquivada com sucesso.']);
    }

    /**
     * Admin elimina permanentemente uma conversa arquivada.
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        $success = $this->repository->deleteArchived($id, $request->user());

        if (!$success) {
            return response()->json(['message' => 'Conversa não encontrada ou não está arquivada.'], 404);
        }

        return response()->json(['message' => 'Conversa eliminada com sucesso.']);
    }

    private function formatConversation($c): array
    {
        return [
            'id'              => $c->id,
            'tenant_id'       => $c->tenant_id,
            'visitor_id'      => $c->visitor_id,
            'visitor_name'    => $c->visitor_name,
            'product_id'      => $c->product_id,
            'unread_count'    => $c->unread_count,
            'last_message'    => $c->last_message,
            'last_message_at' => $c->last_message_at?->toISOString(),
            'archived_at'     => $c->archived_at?->toISOString(),
            'created_at'      => $c->created_at->toISOString(),
        ];
    }
}