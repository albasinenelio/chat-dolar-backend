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

    public function execute(StartConversationDTO $dto): Conversation
    {
        $tenant = Tenant::find($dto->tenantId);

        if (!$tenant) {
            throw ValidationException::withMessages([
                'tenant_id' => ['Invalid destination.'],
            ]);
        }

        $conversation = $this->repository->create($dto);

        broadcast(new ConversationCreated($conversation));

        $product = $dto->productId
            ? Product::where('public_id', $dto->productId)
                     ->where('tenant_id', $dto->tenantId)
                     ->first()
            : null;

        $visitorMessage = $product
            ? 'Hi! I want to buy: ' . $product->visual_name . ' ($' . $product->price . ')'
            : 'Hi! I want to finalize the purchase.';

        $this->createAndBroadcast($conversation, 'visitor', $visitorMessage);
        $this->createAndBroadcast($conversation, 'admin', $this->buildPaymentMessage($product, $tenant));
        $this->createAndBroadcast($conversation, 'admin', "Once payment is done, please send us the receipt or proof of payment.");

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

        if ($senderType === 'visitor') {
            $conversation->incrementUnread();
        }

        broadcast(new MessageSent($message));
    }

    private function buildPaymentMessage(?Product $product, Tenant $tenant): string
    {
        $price     = $product ? (float) $product->price : null;
        $priceText = $price ? '$' . $price : 'the agreed amount';

        $enebaLink = $price
            ? 'https://www.eneba.com/store/gift-cards?q=' . $price . '+usd'
            : 'https://www.eneba.com';

        $g2aLink = $price
            ? 'https://www.g2a.com/search?query=' . $price . '+usd+gift+card'
            : 'https://www.g2a.com';

        $lines   = [];
        $lines[] = "The safest and most private payments are via Crypto or Gift Cards.";
        $lines[] = "";
        $lines[] = "To complete your purchase of {$priceText}, choose one of the options below:";
        $lines[] = "";

        $optionNum = 1;

        $lines[] = "Option {$optionNum} – Gift Card via Eneba";
        $lines[] = "Simple, easy and safe — accepted methods include:";
        $lines[] = "Card ✅  PayPal ✅  Apple Pay ✅  Paysafe Card ✅";
        $lines[] = "NETELLER ✅  Google Pay ✅  Skrill ✅  and more…";
        $lines[] = "Purchase here: {$enebaLink}";
        $lines[] = "";
        $optionNum++;

        $lines[] = "Option {$optionNum} – Gift Card via G2A";
        $lines[] = "Same methods accepted.";
        $lines[] = "Purchase here: {$g2aLink}";
        $lines[] = "";
        $optionNum++;

        if (!empty($tenant->paypal_address)) {
            $lines[] = "Option {$optionNum} – PayPal";
            $lines[] = "Send {$priceText} to: {$tenant->paypal_address}";
            $lines[] = "";
            $optionNum++;
        }

        if (!empty($tenant->btc_address)) {
            $lines[] = "Option {$optionNum} – Crypto (BTC)";
            $lines[] = "Send {$priceText} to the following address ⤵️";
            $lines[] = $tenant->btc_address;
        }

        return implode("\n", $lines);
    }
}