<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly Message $message,
    ) {}

    /**
     * Broadcast em dois canais:
     *  - conversation.{id}  → visitante + admin dentro do chat
     *  - tenant.{id}        → LeadsPage do admin (badge instantâneo)
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('conversation.' . $this->message->conversation_id),
            new Channel('tenant.' . $this->message->conversation->tenant_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'id'              => $this->message->id,
            'conversation_id' => $this->message->conversation_id,
            'sender_type'     => $this->message->sender_type,
            'type'            => $this->message->type,
            'content'         => $this->message->content,
            'image_url'       => $this->message->image_url,
            'caption'         => $this->message->caption,
            'read'            => $this->message->read,
            'created_at'      => $this->message->created_at->toISOString(),
        ];
    }
}