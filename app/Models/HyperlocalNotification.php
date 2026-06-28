<?php
// app/Models/HyperlocalNotification.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HyperlocalNotification extends Model
{
    protected $table = 'hyperlocal_notifications';

    protected $fillable = [
        'news_id',
        'user_id',
        'distance',
        'is_read',
        'sent_at'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'sent_at' => 'datetime',
        'distance' => 'decimal:2',
    ];

    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    public function markAsRead()
    {
        $this->is_read = true;
        $this->save();
        return $this;
    }

    public function getTimeAgoAttribute()
    {
        return $this->sent_at ? $this->sent_at->diffForHumans() : 'Just now';
    }

    public function getDistanceTextAttribute()
    {
        if (!$this->distance) {
            return 'Nearby';
        }
        if ($this->distance < 1) {
            return round($this->distance * 1000) . ' m';
        }
        return number_format($this->distance, 1) . ' km';
    }
}