<?php
// app/Models/Notification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'user_id',
        'news_id',
        'type',
        'title',
        'message',
        'is_read'
    ];

    protected $casts = [
        'is_read' => 'boolean'
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
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    // ===== HELPERS =====
    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
        return $this;
    }

    public function markAsUnread()
    {
        $this->is_read = false;
        $this->save();
        return $this;
    }

    // ===== ACCESSORS =====
    public function getTimeAgoAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : 'Just now';
    }
}