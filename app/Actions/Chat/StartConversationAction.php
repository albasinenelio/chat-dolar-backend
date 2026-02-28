<?php

namespace App\Actions\Chat;

use App\Contracts\ConversationRepositoryInterface;
use App\DTOs\StartConversationDTO;
use App\Events\ConversationCreated;
use App\Events\MessageSent;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\Product;
use App\Models\Tenant;
use Illuminate\Validation\ValidationException;

class StartConversationAction
{
    public function __construct(
        private readonly ConversationRepositoryInterface $repository,
    ) {}

    /**
     * Inicia uma nova conversa para o visitante.
     * Após criar, injeta:
     *   1. Evento ConversationCreated — admins do tenant actualizam a lista
     *   2. Mensagem automática do visitante ("Hi, I want to buy...")
     *   3. Auto-reply bot com instruções de pagamento
     *
     * @throws ValidationException
     */
    public function execute(StartConversationDTO $dto): Conversation
    {
        $tenant = Tenant::find($dto->tenantId);

        if (!$tenant) {
            throw ValidationException::withMessages([
                'tenant_id' => ['Invalid destination.'],
            ]);
        }

        $conversation = $this->repository->create($dto);

        // ── 1. Notifica admins do tenant — nova conversa na lista ─────────────
        broadcast(new ConversationCreated($conversation));

        // ── 2. Lookup do produto por public_id ────────────────────────────────
        $product = $dto->productId
            ? Product::where('public_id', $dto->productId)
                     ->where('tenant_id', $dto->tenantId)
                     ->first()
            : null;

        // ── 3. Mensagem automática do visitante ───────────────────────────────
        $visitorMessage = $product
            ? 'Hi! I want to buy: ' . $product->visual_name . ' ($' . $product->price . ')'
            : 'Hi! I would like more information.';

        $this->createAndBroadcast($conversation, 'visitor', $visitorMessage);

        // ── 4. Auto-reply bot com instruções de pagamento ─────────────────────
        $this->createAndBroadcast($conversation, 'admin', $this->buildPaymentMessage($product));

        return $conversation;
    }

    private function createAndBroadcast(Conversation $conversation, string $senderType, string $content): void
    {
        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => $senderType,
            'type'            => 'text',
            'content'         => $content,
            'read'            => false,
        ]);

        $conversation->update([
            'last_message'    => $content,
            'last_message_at' => now(),
        ]);

        broadcast(new MessageSent($message));
    }

    private function buildPaymentMessage(?Product $product): string
    {
        $price     = $product ? (float) $product->price : null;
        $priceText = $price ? '$' . $price : 'the agreed amount';

        $enebaLink = $price
            ? 'https://www.eneba.com/store/gift-cards?q=' . $price . '+usd'
            : 'https://www.eneba.com';

        $g2aLink = $price
            ? 'https://www.g2a.com/search?query=' . $price . '+usd+gift+card'
            : 'https://www.g2a.com';

        return
            "Hello! To complete your purchase, please choose one of the payment options below:\n" .
            "\n" .
            "Option 1 - Crypto (Binance)\n" .
            "Send " . $priceText . " to the following BTC address:\n" .
            "1MPa1fSFYYWADLC7xvZpXh5VM1mDp2mjGD\n" .
            "\n" .
            "Option 2 - Gift Card via Eneba\n" .
            "Purchase a " . $priceText . " gift card here:\n" .
            $enebaLink . "\n" .
            "\n" .
            "Option 3 - Gift Card via G2A\n" .
            "Purchase a " . $priceText . " gift card here:\n" .
            $g2aLink . "\n" .
            "\n" .
            "Once payment is done, please send us the receipt or proof of payment in this chat and we will process your order right away.";
    }
}