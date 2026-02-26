<?php

namespace App\Jobs;

use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPushNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public array $backoff = [30, 120, 300]; // 30s, 2min, 5min

    public function __construct(
        private readonly string $tenantId,
        private readonly array  $payload,
    ) {
        $this->onQueue('notifications');
    }

    public function handle(PushNotificationService $service): void
    {
        try {
            $sent = $service->notifyAdmins($this->tenantId, $this->payload);

            Log::info($sent
                ? '✅ Push enviado'
                : '⚠️ Sem subscrições activas', [
                    'tenant_id'       => $this->tenantId,
                    'conversation_id' => $this->payload['conversation_id'] ?? null,
                    'attempt'         => $this->attempts(),
                ]);

        } catch (\Exception $e) {
            Log::error('❌ Erro ao enviar push', [
                'tenant_id' => $this->tenantId,
                'error'     => $e->getMessage(),
                'attempt'   => $this->attempts(),
            ]);

            throw $e; // activa retry
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('💥 Push falhou definitivamente', [
            'tenant_id' => $this->tenantId,
            'error'     => $e->getMessage(),
        ]);
    }
}