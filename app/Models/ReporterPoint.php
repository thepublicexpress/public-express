<?php
// app/Models/ReporterPoint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporterPoint extends Model
{
    protected $fillable = [
        'user_id',
        'news_id',
        'points',
        'reason',
        'action'
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
    public function scopeEarned($query)
    {
        return $query->where('points', '>', 0);
    }

    public function scopeDeducted($query)
    {
        return $query->where('points', '<', 0);
    }

    // ===== ACCESSORS =====
    public function getPointsDisplayAttribute()
    {
        if ($this->points > 0) {
            return '+' . $this->points;
        }
        return (string) $this->points;
    }

    public function getColorAttribute()
    {
        if ($this->points > 0) return 'green';
        if ($this->points < 0) return 'red';
        return 'gray';
    }
}