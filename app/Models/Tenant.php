<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tenant extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'slug',
        'btc_address',
        'paypal_address',
    ];

    public function owner(): HasOne
    {
        return $this->hasOne(User::class)->where('role', 'super_admin')
                    ->orWhere('role', 'admin')
                    ->orderBy('id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function conversations(): HasMany
    {
        return $this->hasMany(Conversation::class);
    }
}