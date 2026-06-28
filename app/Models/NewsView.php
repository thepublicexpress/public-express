<?php
// app/Models/NewsView.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsView extends Model
{
    protected $table = 'news_views';

    protected $fillable = [
        'news_id',
        'user_id',
        'ip_address',
        'user_agent',
        'session_id',
        'referer',
        'is_unique',
        'is_fake',
        'fake_reason',
        'viewed_at'
    ];

    protected $casts = [
        'is_unique' => 'boolean',
        'is_fake' => 'boolean',
        'viewed_at' => 'datetime'
    ];

    public $timestamps = false;

    // ===== RELATIONSHIPS =====
    public function news()
    {
        return $this->belongsTo(News::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== SCOPES =====
    public function scopeUnique($query)
    {
        return $query->where('is_unique', true);
    }

    public function scopeFake($query)
    {
        return $query->where('is_fake', true);
    }

    public function scopeReal($query)
    {
        return $query->where('is_fake', false);
    }

    // ===== HELPERS =====
    public static function trackView($newsId, $request)
    {
        $ip = $request->ip();
        $sessionId = $request->session()->getId();
        
        // Check if already viewed in last 24 hours
        $existingView = self::where('news_id', $newsId)
            ->where(function($q) use ($ip, $sessionId) {
                $q->where('ip_address', $ip)
                  ->orWhere('session_id', $sessionId);
            })
            ->where('viewed_at', '>', now()->subHours(24))
            ->first();

        $isUnique = !$existingView;
        $isFake = false;
        $fakeReason = null;

        // Check for fake views
        $settings = \App\Models\RevenueSetting::getSettings();
        if ($settings) {
            // Check daily views per IP
            $dailyViews = self::where('ip_address', $ip)
                ->where('viewed_at', '>', now()->startOfDay())
                ->count();

            if ($dailyViews >= $settings->max_views_per_ip_per_day) {
                $isFake = true;
                $fakeReason = 'Exceeded max views per IP per day: ' . $dailyViews;
            }
        }

        return self::create([
            'news_id' => $newsId,
            'user_id' => auth()->id(),
            'ip_address' => $ip,
            'user_agent' => $request->userAgent(),
            'session_id' => $sessionId,
            'is_unique' => $isUnique,
            'is_fake' => $isFake,
            'fake_reason' => $fakeReason,
            'viewed_at' => now(),
        ]);
    }
}