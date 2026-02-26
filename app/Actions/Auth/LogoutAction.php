<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutAction
{
    /**
     * Termina a sessão do utilizador e regenera o token CSRF.
     */
    public function execute(Request $request): void
    {
        Auth::guard('web')->logout();

        // Invalidar sessão atual
        $request->session()->invalidate();

        // Regenerar token CSRF
        $request->session()->regenerateToken();
    }
}