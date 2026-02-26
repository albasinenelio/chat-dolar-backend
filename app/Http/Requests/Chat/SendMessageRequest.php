<?php

namespace App\Http\Requests\Chat;

use App\Enums\MessageType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // visitante não autenticado pode enviar
    }

    public function rules(): array
    {
        return [
            'conversation_id' => ['required', 'uuid', 'exists:conversations,id'],
            'type'            => ['required', new Enum(MessageType::class)],
            'content'         => ['required', 'string', 'max:5000'],
            'image_url'       => ['nullable', 'url', 'max:500'],
            'caption'         => ['nullable', 'string', 'max:500'],
        ];
    }
}