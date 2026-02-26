<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class VerifyOtpAction
{
    /**
     * Valida o OTP e autentica o utilizador via Sanctum SPA (cookie httpOnly).
     *
     * @return User
     * @throws ValidationException
     */
    public function execute(string $email, string $code): User
    {
        $user = User::where('email', $email)->first();

        // Utilizador não encontrado ou OTP inválido/expirado
        if (!$user || !$user->hasValidOtp($code)) {
            throw ValidationException::withMessages([
                'code' => ['Código inválido ou expirado.'],
            ]);
        }

        // Limpar OTP após uso (one-time)
        $user->clearOtp();

        // Autenticar via Sanctum SPA — cria sessão com cookie httpOnly
        Auth::guard('web')->login($user);

        // Regenerar sessão CSRF para evitar session fixation
        request()->session()->regenerate();

        return $user;
    }
}