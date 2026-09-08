<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'fcm_token',
        'browser',
        'platform',
        'user_agent',
        'ip_address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope for active tokens
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}