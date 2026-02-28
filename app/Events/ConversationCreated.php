<?php

namespace App\Events;

use App\Models\Conversation;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ConversationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Conversation $conversation,
    ) {}

    /**
     * Canal do tenant — todos os admins deste tenant recebem.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('tenant.' . $this->conversation->tenant_id);
    }

    public function broadcastAs(): string
    {
        return 'conversation.created';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->conversation->id,
            'tenant_id'       => $this->conversation->tenant_id,
            'visitor_id'      => $this->conversation->visitor_id,
            'visitor_name'    => $this->conversation->visitor_name,
            'product_id'      => $this->conversation->product_id,
            'unread_count'    => $this->conversation->unread_count,
            'last_message'    => $this->conversation->last_message,
            'last_message_at' => $this->conversation->last_message_at?->toISOString(),
            'created_at'      => $this->conversation->created_at->toISOString(),
        ];
    }
}