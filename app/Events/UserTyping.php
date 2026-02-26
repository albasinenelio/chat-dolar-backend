<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTyping implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $conversationId,
        public readonly string $senderType, // 'admin' | 'visitor'
    ) {}

    /**
     * Mesmo canal público das mensagens.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('conversation.' . $this->conversationId);
    }

    /**
     * Nome do evento que o frontend vai ouvir.
     */
    public function broadcastAs(): string
    {
        return 'typing';
    }

    /**
     * Payload mínimo — não persiste na BD.
     */
    public function broadcastWith(): array
    {
        return [
            'conversation_id' => $this->conversationId,
            'sender_type'     => $this->senderType,
        ];
    }
}