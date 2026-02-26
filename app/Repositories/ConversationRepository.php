<?php

namespace App\Repositories;

use App\Contracts\ConversationRepositoryInterface;
use App\DTOs\StartConversationDTO;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ConversationRepository implements ConversationRepositoryInterface
{
    public function create(StartConversationDTO $dto): Conversation
    {
        // Gerar visitor_id sequencial por tenant (user-0001, user-0002, ...)
        $count = Conversation::where('tenant_id', $dto->tenantId)->count();
        $visitorId = 'user-' . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

        return Conversation::create([
            'id'           => Str::uuid(),
            'tenant_id'    => $dto->tenantId,
            'visitor_id'   => $visitorId,
            'visitor_name' => $dto->visitorName,
            'product_id'   => $dto->productId,
        ]);
    }

    public function listForUser(User $user): Collection
    {
        $query = Conversation::with(['messages' => function ($q) {
            $q->latest()->limit(1);
        }])->latest('last_message_at');

        // super_admin vê tudo; admin vê só o seu tenant
        if (!$user->isSuperAdmin()) {
            $query->where('tenant_id', $user->tenant_id);
        }

        return $query->get();
    }

    public function findForUser(string $id, User $user): ?Conversation
    {
        $query = Conversation::with('messages')->where('id', $id);

        if (!$user->isSuperAdmin()) {
            $query->where('tenant_id', $user->tenant_id);
        }

        return $query->first();
    }
}