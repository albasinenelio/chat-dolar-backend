<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSessionActivity
{
    private const TIMEOUT = 600; // 10 minutos em segundos

    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() !== null) {
            $lastActivity = session('last_activity_at');

            if ($lastActivity && (time() - $lastActivity) > self::TIMEOUT) {
                // Auth::logout() não existe no guard sanctum (RequestGuard).
                // Invalidar a sessão directamente é suficiente para terminar a autenticação SPA.
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->json([
                    'message' => 'Sessão expirada por inatividade.',
                    'code'    => 'SESSION_EXPIRED',
                ], 401);
            }

            session(['last_activity_at' => time()]);
        }

        return $next($request);
    }
}