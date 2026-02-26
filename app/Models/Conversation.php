<?php

namespace App\Models;

use App\Events\MessagesRead;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversation extends Model
{
    use HasUuids;

    protected $fillable = [
        'tenant_id',
        'visitor_id',
        'visitor_name',
        'product_id',
        'last_message_at',
        'last_message',
        'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_count'    => 'integer',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function incrementUnread(): void
    {
        $this->increment('unread_count');
    }

    /**
     * Marca mensagens como lidas e notifica o outro lado via WebSocket.
     *
     * @param string $readBy 'admin' | 'visitor'
     */
    public function markAsRead(string $readBy = 'admin'): void
    {
        $this->update(['unread_count' => 0]);
        $this->messages()->where('read', false)->update(['read' => true]);

        // Notifica o canal em tempo real — o outro lado actualiza os ticks
        broadcast(new MessagesRead($this->id, $readBy));
    }
}