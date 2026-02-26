<?php

namespace App\Actions\Push;

use App\Models\PushSubscription;
use App\Models\User;

class SubscribeAction
{
    /**
     * Salva ou actualiza a subscrição VAPID do admin.
     * Usa endpoint_hash para evitar duplicados.
     */
    public function execute(User $user, array $data): PushSubscription
    {
        $hash = PushSubscription::hashEndpoint($data['endpoint']);

        return PushSubscription::updateOrCreate(
            ['endpoint_hash' => $hash],
            [
                'user_id'    => $user->id,
                'endpoint'   => $data['endpoint'],
                'public_key' => $data['public_key'],
                'auth_token' => $data['auth_token'],
                'device_id'  => $data['device_id'] ?? null,
            ]
        );
    }
}