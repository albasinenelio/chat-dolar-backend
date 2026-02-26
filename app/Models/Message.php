<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasUuids;

    protected $fillable = [
        'conversation_id',
        'sender_type',
        'type',
        'content',
        'image_url',
        'caption',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isFromVisitor(): bool
    {
        return $this->sender_type === 'visitor';
    }

    public function isFromAdmin(): bool
    {
        return $this->sender_type === 'admin';
    }
}