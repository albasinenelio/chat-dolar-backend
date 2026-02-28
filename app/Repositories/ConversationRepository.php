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
        $count     = Conversation::where('tenant_id', $dto->tenantId)->count();
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
        $query = Conversation::with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->whereNull('archived_at')
            ->latest('last_message_at');

        if (!$user->isSuperAdmin()) {
            $query->where('tenant_id', $user->tenant_id);
        }

        return $query->get();
    }

    public function listArchivedForUser(User $user): Collection
    {
        $query = Conversation::with(['messages' => fn($q) => $q->latest()->limit(1)])
            ->whereNotNull('archived_at')
            ->latest('archived_at');

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

    public function archive(string $id, User $user): bool
    {
        $conversation = $this->findForUser($id, $user);

        if (!$conversation || $conversation->archived_at !== null) {
            return false;
        }

        return (bool) $conversation->update(['archived_at' => now()]);
    }

    public function unarchive(string $id, User $user): bool
    {
        $query = Conversation::where('id', $id)->whereNotNull('archived_at');

        if (!$user->isSuperAdmin()) {
            $query->where('tenant_id', $user->tenant_id);
        }

        $conversation = $query->first();

        if (!$conversation) {
            return false;
        }

        return (bool) $conversation->update(['archived_at' => null]);
    }

    public function deleteArchived(string $id, User $user): bool
    {
        $query = Conversation::where('id', $id)->whereNotNull('archived_at');

        if (!$user->isSuperAdmin()) {
            $query->where('tenant_id', $user->tenant_id);
        }

        $conversation = $query->first();

        if (!$conversation) {
            return false;
        }

        return (bool) $conversation->delete();
    }
}