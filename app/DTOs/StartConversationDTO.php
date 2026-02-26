<?php

namespace App\DTOs;

readonly class StartConversationDTO
{
    public function __construct(
        public string  $tenantId,
        public string  $visitorName,
        public ?string $productId = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            tenantId:    $data['tenant_id'],
            visitorName: $data['visitor_name'],
            productId:   $data['product_id'] ?? null,
        );
    }
}