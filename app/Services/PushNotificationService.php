<?php

namespace App\Services;

use App\Models\PushSubscription;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $this->webPush = new WebPush([
            'VAPID' => [
                'subject'    => config('services.vapid.subject'),
                'publicKey'  => config('services.vapid.public_key'),
                'privateKey' => config('services.vapid.private_key'),
            ],
        ]);
    }

    /**
     * Notifica admins do tenant sobre nova mensagem do visitante.
     * super_admin recebe sempre; admin só do seu tenant.
     */
    public function notifyAdmins(string $tenantId, array $payload): bool
    {
        $subscriptions = PushSubscription::whereHas('user', fn($q) =>
            $q->where('role', 'super_admin')
              ->orWhere(fn($q2) =>
                  $q2->where('role', 'admin')->where('tenant_id', $tenantId)
              )
        )->get();

        if ($subscriptions->isEmpty()) {
            return false;
        }

        foreach ($subscriptions as $sub) {
            $this->webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $sub->endpoint,
                    'keys'     => [
                        'p256dh' => $sub->public_key,
                        'auth'   => $sub->auth_token,
                    ],
                ]),
                json_encode($payload)
            );
        }

        foreach ($this->webPush->flush() as $report) {
            if (!$report->isSuccess()) {
                Log::warning('Push VAPID falhou', [
                    'reason' => $report->getReason(),
                ]);

                if ($report->isSubscriptionExpired()) {
                    PushSubscription::where('endpoint_hash',
                        PushSubscription::hashEndpoint((string) $report->getRequest()->getUri())
                    )->delete();
                }
            }
        }

        return true;
    }
}