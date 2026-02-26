<?php

namespace App\Actions\Chat;

use App\DTOs\SendMessageDTO;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Validation\ValidationException;

class SendMessageAction
{
    /**
     * Persiste a mensagem, actualiza metadados da conversa e dispara broadcast.
     *
     * @throws ValidationException
     */
    public function execute(SendMessageDTO $dto): Message
    {
        $conversation = Conversation::find($dto->conversationId);

        if (!$conversation) {
            throw ValidationException::withMessages([
                'conversation_id' => ['Conversa não encontrada.'],
            ]);
        }

        // Persistir mensagem
        $message = Message::create([
            'conversation_id' => $dto->conversationId,
            'sender_type'     => $dto->senderType->value,
            'type'            => $dto->type->value,
            'content'         => $dto->content,
            'image_url'       => $dto->imageUrl,
            'caption'         => $dto->caption,
            'read'            => false,
        ]);

        // Actualizar metadados da conversa
        $lastMessage = $dto->type->value === 'image'
            ? ($dto->caption ?? '📷 Imagem')
            : $dto->content;

        $conversation->update([
            'last_message_at' => now(),
            'last_message'    => $lastMessage,   // ← novo
        ]);

        // Incrementar unread apenas para mensagens do visitante
        if ($dto->senderType->value === 'visitor') {
            $conversation->incrementUnread();
        }

        broadcast(new MessageSent($message));

        return $message;
    }
}