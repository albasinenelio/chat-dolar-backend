<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Recebe imagem, guarda no disk public e devolve URL acessível.
     * Rota pública — visitante não autenticado pode fazer upload.
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'image' => ['required', 'file', 'image', 'max:5120'], // máx 5MB
        ]);

        $path = $request->file('image')->store('chat-images', 'public');

        // asset() gera URL absoluta a partir do path público
        $url = rtrim(config('app.url'), '/') . '/storage/' . $path;

        return response()->json(['url' => $url], 201);
    }
}