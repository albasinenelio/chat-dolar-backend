<?php

namespace App\Actions\Chat;

use App\Contracts\ConversationRepositoryInterface;
use App\DTOs\StartConversationDTO;
use App\Models\Conversation;
use App\Models\Tenant;
use Illuminate\Validation\ValidationException;

class StartConversationAction
{
    public function __construct(
        private readonly ConversationRepositoryInterface $repository,
    ) {}

    /**
     * Inicia uma nova conversa para o visitante.
     * Valida que o tenant_id (UUID) existe antes de criar.
     *
     * @throws ValidationException
     */
    public function execute(StartConversationDTO $dto): Conversation
    {
        // Validar que o tenant existe — UUID inválido retorna 422 genérico
        $tenant = Tenant::find($dto->tenantId);

        if (!$tenant) {
            throw ValidationException::withMessages([
                'tenant_id' => ['Destino inválido.'],
            ]);
        }

        return $this->repository->create($dto);
    }
}