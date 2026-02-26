<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\VerifyOtpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Etapa 1 do login: valida credenciais e envia OTP por email.
     */
    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        try {
            $action->execute($request->email, $request->password);

            return response()->json([
                'message'      => 'Código enviado para o seu email.',
                'requires_otp' => true,
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Credenciais inválidas.',
                'errors'  => $e->errors(),
            ], 401);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao processar login.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Etapa 2 do login: valida OTP e inicia sessão via cookie httpOnly.
     */
    public function verifyOtp(VerifyOtpRequest $request, VerifyOtpAction $action): JsonResponse
    {
        try {
            $user = $action->execute($request->email, $request->code);

            return response()->json([
                'message' => 'Autenticado com sucesso.',
                'user'    => [
                    'id'        => $user->id,
                    'name'      => $user->name,
                    'email'     => $user->email,
                    'role'      => $user->role,
                    'tenant_id' => $user->tenant_id,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Código inválido ou expirado.',
                'errors'  => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao verificar código.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Termina a sessão do utilizador autenticado.
     */
    public function logout(Request $request, LogoutAction $action): JsonResponse
    {
        try {
            $action->execute($request);

            return response()->json([
                'message' => 'Sessão terminada com sucesso.',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao terminar sessão.',
                'error'   => config('app.debug') ? $e->getMessage() : 'Erro interno.',
            ], 500);
        }
    }

    /**
     * Retorna os dados do utilizador autenticado.
     */
    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Não autenticado.'], 401);
        }

        return response()->json([
            'user' => [
                'id'        => $user->id,
                'name'      => $user->name,
                'email'     => $user->email,
                'role'      => $user->role,
                'tenant_id' => $user->tenant_id,
            ],
        ]);
    }
}