<?php

namespace App\DTOs;

use App\Enums\MessageType;
use App\Enums\SenderType;

readonly class SendMessageDTO
{
    public function __construct(
        public string      $conversationId,
        public SenderType  $senderType,
        public MessageType $type,
        public string      $content,
        public ?string     $imageUrl  = null,
        public ?string     $caption   = null,
    ) {}

    public static function fromArray(array $data, SenderType $sender): self
    {
        return new self(
            conversationId: $data['conversation_id'],
            senderType:     $sender,
            type:           MessageType::from($data['type'] ?? 'text'),
            content:        $data['content'],
            imageUrl:       $data['image_url'] ?? null,
            caption:        $data['caption'] ?? null,
        );
    }
}