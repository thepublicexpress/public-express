<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id',
        'news_id',      // ✅ नया Column – किस खबर से संबंधित है
        'type',         // admin, reporter, subscriber
        'title',
        'body',
        'message',      // ✅ आपकी Table में message भी है
        'data',         // JSON – extra data (news_id, url, etc.)
        'is_read',      // ✅ boolean (0/1)
        'read_at',      // ✅ timestamp
    ];

    protected $casts = [
        'data' => 'array',
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // ===== RELATIONSHIPS =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    // ===== SCOPES =====
    public function scopeUnread($query)
    {
        return $query->where('is_read', 0)->whereNull('read_at');
    }

    // ===== ACCESSORS =====
    public function getIsReadAttribute($value)
    {
        return (bool) $value;
    }

    // ===== METHODS =====
    public function markAsRead()
    {
        $this->update([
            'is_read' => 1,
            'read_at' => now(),
        ]);
    }

    public function isRead()
    {
        return $this->is_read == 1 || !is_null($this->read_at);
    }
}