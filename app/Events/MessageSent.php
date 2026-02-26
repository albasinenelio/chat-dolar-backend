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
     * Canal público — visitante não autenticado pode subscrever.
     */
    public function broadcastOn(): Channel
    {
        return new Channel('conversation.' . $this->message->conversation_id);
    }

    /**
     * Nome do evento que o frontend vai ouvir.
     */
    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /**
     * Payload enviado ao frontend.
     */
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