<?php

namespace App\Actions\Auth;

use App\Mail\OtpMail;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class LoginAction
{
    /**
     * Valida credenciais, gera OTP e envia por email.
     *
     * @throws ValidationException
     */
    public function execute(string $email, string $password): void
    {
        $user = User::where('email', $email)->first();

        // Credenciais inválidas — mensagem genérica (não revela se email existe)
        if (!$user || !Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }

        // Gerar OTP: 6 dígitos, válido por 5 minutos
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->update([
            'otp_code'       => $otp,
            'otp_expires_at' => now()->addMinutes(5),
        ]);

        // Enviar OTP por email via Gmail SMTP
        Mail::to($user->email)->send(new OtpMail($otp, $user->name));
    }
}