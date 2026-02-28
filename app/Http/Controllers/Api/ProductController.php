<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Retorna preço e visual_name de um produto pelo public_id.
     * Rota pública — visitante consulta antes de iniciar conversa.
     */
    public function show(Request $request, string $publicId): JsonResponse
    {
        $tenantId = $request->query('tenant_id');

        if (!$tenantId) {
            return response()->json(['message' => 'tenant_id is required.'], 422);
        }

        $product = Product::where('public_id', $publicId)
                          ->where('tenant_id', $tenantId)
                          ->first();

        if (!$product) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        return response()->json([
            'data' => [
                'public_id'   => $product->public_id,
                'visual_name' => $product->visual_name,
                'price'       => (float) $product->price,
            ],
        ]);
    }
}