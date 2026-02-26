<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Canal público — qualquer cliente (incluindo visitante não autenticado)
| pode subscrever 'conversation.{id}' para receber mensagens em tempo real.
|
*/

Broadcast::channel('conversation.{conversationId}', function () {
    // Canal público: retorna true para permitir qualquer subscritor
    return true;
});