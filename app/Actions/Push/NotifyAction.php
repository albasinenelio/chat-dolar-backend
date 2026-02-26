<?php

namespace App\Actions\Push;

use App\Jobs\SendPushNotificationJob;
use App\Models\Conversation;

class NotifyAction
{
    /**
     * Despacha o Job de push VAPID ao(s) admin(s) do tenant.
     * Chamado após visitante enviar mensagem.
     */
    public function execute(Conversation $conversation, string $visitorName): void
    {
        SendPushNotificationJob::dispatch(
            $conversation->tenant_id,
            [
                'conversation_id' => $conversation->id,
                'visitor_name'    => $visitorName,
                'product_id'      => $conversation->product_id,
                'title'           => '💬 Nova mensagem',
                'body'            => "{$visitorName} enviou uma mensagem.",
                'url'             => '/admin/conversations/' . $conversation->id,
            ]
        );
    }
}