<?php

namespace App\Http\Controllers\Api;

use App\Actions\Push\SubscribeAction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushController extends Controller
{
    /**
     * Admin subscreve para receber notificações VAPID.
     */
    public function subscribe(Request $request, SubscribeAction $action): JsonResponse
    {
        $validated = $request->validate([
            'endpoint'   => ['required', 'string'],
            'public_key' => ['required', 'string'],
            'auth_token' => ['required', 'string'],
            'device_id'  => ['nullable', 'string'],
        ]);

        try {
            $action->execute($request->user(), $validated);

            return response()->json([
                'message' => 'Subscrição registada com sucesso.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao registar subscrição.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Admin remove a subscrição VAPID do dispositivo actual.
     */
    public function unsubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => ['required', 'string'],
        ]);

        $request->user()
            ->pushSubscriptions()
            ->where('endpoint_hash', \App\Models\PushSubscription::hashEndpoint($validated['endpoint']))
            ->delete();

        return response()->json([
            'message' => 'Subscrição removida.',
        ]);
    }
}