<?php

namespace App\Contracts;

use App\DTOs\StartConversationDTO;
use App\Models\Conversation;
use Illuminate\Support\Collection;

interface ConversationRepositoryInterface
{
    /**
     * Cria uma nova conversa para o tenant + visitante.
     */
    public function create(StartConversationDTO $dto): Conversation;

    /**
     * Lista conversas — todas (super_admin) ou só do tenant (admin).
     */
    public function listForUser(\App\Models\User $user): Collection;

    /**
     * Busca conversa pelo ID garantindo acesso do utilizador.
     */
    public function findForUser(string $id, \App\Models\User $user): ?Conversation;
}