<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OtpVerification extends Model
{
    protected $table = 'otp_verifications';

    protected $fillable = [
        'mobile',
        'email',
        'otp',
        'expires_at',
        'is_used',
        'attempts',
        'resend_count',
        'last_resend_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_used' => 'boolean',
    ];

    public function scopeValid($query)
    {
        return $query->where('is_used', false)
            ->where('expires_at', '>', now());
    }

    public function isExpired()
    {
        return $this->expires_at <= now();
    }

    public function markAsUsed()
    {
        $this->is_used = true;
        $this->save();
        return $this;
    }
}