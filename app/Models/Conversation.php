<?php

namespace App\Models;

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
        'unread_count',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
        'unread_count'    => 'integer',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function incrementUnread(): void
    {
        $this->increment('unread_count');
    }

    public function markAsRead(): void
    {
        $this->update(['unread_count' => 0]);
        $this->messages()->where('read', false)->update(['read' => true]);
    }
}