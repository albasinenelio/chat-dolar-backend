<?php

namespace App\Contracts;

use App\DTOs\StartConversationDTO;
use App\Models\Conversation;
use App\Models\User;
use Illuminate\Support\Collection;

interface ConversationRepositoryInterface
{
    public function create(StartConversationDTO $dto): Conversation;

    public function listForUser(User $user): Collection;

    public function listArchivedForUser(User $user): Collection;

    public function findForUser(string $id, User $user): ?Conversation;

    public function archive(string $id, User $user): bool;

    public function unarchive(string $id, User $user): bool;

    public function deleteArchived(string $id, User $user): bool;
}