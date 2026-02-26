<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StartConversationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // rota pública
    }

    public function rules(): array
    {
        return [
            'tenant_id'    => ['required', 'uuid', 'exists:tenants,id'],
            'visitor_name' => ['required', 'string', 'min:2', 'max:100'],
            'product_id'   => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'tenant_id.required' => 'Destino inválido.',
            'tenant_id.uuid'     => 'Destino inválido.',
            'tenant_id.exists'   => 'Destino inválido.',
            'visitor_name.required' => 'O nome é obrigatório.',
            'visitor_name.min'      => 'O nome deve ter pelo menos 2 caracteres.',
        ];
    }
}