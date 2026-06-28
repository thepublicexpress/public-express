<?php
// app/Models/News.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class News extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'news';

    protected $fillable = [
        'user_id',
        'category_id',
        'state_id',
        'district_id',
        'tehsil_id',
        'block_id',
        'title',
        'slug',
        'summary',
        'body',
        'featured_image',
        'type',
        'video_url',
        'status',
        'rejection_reason',
        'views',
        'likes',
        'shares',
        'is_breaking',
        'is_featured',
        'is_national',
        'is_state',
        'location_required',
        'published_at',
        'approved_by',
        'approved_by_level',
        // ===== HYPERLOCAL FIELDS =====
        'latitude',
        'longitude',
        'location_radius',
        'is_hyperlocal',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'is_breaking' => 'boolean',
        'is_featured' => 'boolean',
        'is_national' => 'boolean',
        'is_state' => 'boolean',
        'location_required' => 'boolean',
        'is_hyperlocal' => 'boolean',
        'views' => 'integer',
        'likes' => 'integer',
        'shares' => 'integer',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'location_radius' => 'integer',
    ];

    // ===== RELATIONSHIPS =====
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function tehsil()
    {
        return $this->belongsTo(Tehsil::class);
    }

    public function block()
    {
        return $this->belongsTo(Block::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ===== HYPERLOCAL RELATIONSHIPS =====
    public function hyperlocalNotifications()
    {
        return $this->hasMany(HyperlocalNotification::class);
    }

    // ===== SCOPES =====
    
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeBreaking($query)
    {
        return $query->where('is_breaking', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeNational($query)
    {
        return $query->where('is_national', true);
    }

    public function scopeState($query)
    {
        return $query->where('is_state', true);
    }

    // ===== HYPERLOCAL SCOPES =====
    public function scopeHyperlocal($query)
    {
        return $query->where('is_hyperlocal', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    }

    public function scopeNearby($query, $lat, $lng, $radius = 5)
    {
        return $query->where('status', 'published')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw(
                "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $radius);
    }

    // ===== ACCESSORS =====
    
    public function getFormattedPublishedAtAttribute()
    {
        return $this->published_at ? $this->published_at->diffForHumans() : 'Just now';
    }

    public function getPublishedAtHumanAttribute()
    {
        return $this->published_at ? $this->published_at->format('M d, Y') : 'N/A';
    }

    public function getStatusLabelAttribute()
    {
        $labels = [
            'draft' => '📝 ड्राफ्ट',
            'pending' => '⏳ लंबित',
            'published' => '✅ प्रकाशित',
            'rejected' => '❌ अस्वीकृत'
        ];
        return $labels[$this->status] ?? $this->status;
    }

    public function getStatusBadgeColorAttribute()
    {
        $colors = [
            'published' => 'success',
            'pending' => 'warning',
            'rejected' => 'danger',
            'draft' => 'secondary',
        ];
        return $colors[$this->status] ?? 'secondary';
    }

    public function getLocationLevelAttribute()
    {
        if ($this->block_id) return 'block';
        if ($this->tehsil_id) return 'tehsil';
        if ($this->district_id) return 'district';
        if ($this->state_id) return 'state';
        if ($this->is_national) return 'national';
        return 'none';
    }

    public function getImageUrlAttribute()
    {
        if (empty($this->featured_image)) {
            return asset('images/default-news.jpg');
        }

        if (filter_var($this->featured_image, FILTER_VALIDATE_URL)) {
            return $this->featured_image;
        }

        if (strpos($this->featured_image, 'storage/') === 0) {
            return asset($this->featured_image);
        }

        if (strpos($this->featured_image, 'news/2026/') !== false) {
            return asset('storage/' . $this->featured_image);
        }

        if (strpos($this->featured_image, 'news/') === 0) {
            return asset('storage/' . $this->featured_image);
        }

        return asset('storage/news/' . $this->featured_image);
    }

    public function getExcerptAttribute()
    {
        if ($this->summary) {
            return $this->summary;
        }
        return $this->body ? strip_tags(substr($this->body, 0, 150)) . '...' : '';
    }

    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->body ?? ''));
        $minutes = ceil($words / 200);
        return $minutes . ' min read';
    }

    // ===== HYPERLOCAL ACCESSORS =====
    public function getDistanceFrom($lat, $lng)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }
        return $this->calculateDistance($lat, $lng, $this->latitude, $this->longitude);
    }

    public function getDistanceText($lat, $lng)
    {
        $distance = $this->getDistanceFrom($lat, $lng);
        if ($distance === null) return 'Unknown';
        if ($distance < 1) {
            return round($distance * 1000) . ' मीटर';
        }
        return number_format($distance, 1) . ' किमी';
    }

    // ===== MUTATORS =====
    
    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $value)));
    }

    // ===== HYPERLOCAL METHODS =====
    
    /**
     * Calculate distance between two coordinates (in kilometers)
     */
    public function calculateDistance($lat1, $lng1, $lat2, $lng2)
    {
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles * 1.609344; // Kilometers
    }

    /**
     * Check if news is within radius of a location
     */
    public function isNearby($lat, $lng, $radius = 5)
    {
        if (!$this->latitude || !$this->longitude) {
            return false;
        }
        
        $distance = $this->calculateDistance($lat, $lng, $this->latitude, $this->longitude);
        return $distance <= $radius;
    }

    /**
     * Get nearby users for this news
     */
    public function getNearbyUsers($radius = 5)
    {
        if (!$this->latitude || !$this->longitude) {
            return collect();
        }
        
        return User::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw(
                "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                [$this->latitude, $this->longitude, $this->latitude]
            )
            ->having('distance', '<=', $radius)
            ->get();
    }

    /**
     * Send hyperlocal notification to nearby users
     */
    public function sendHyperlocalNotifications($radius = 5)
    {
        if (!$this->latitude || !$this->longitude) {
            return 0;
        }

        $nearbyUsers = $this->getNearbyUsers($radius);
        $count = 0;

        foreach ($nearbyUsers as $user) {
            // Check if already notified
            $exists = HyperlocalNotification::where('news_id', $this->id)
                ->where('user_id', $user->id)
                ->exists();

            if (!$exists) {
                HyperlocalNotification::create([
                    'news_id' => $this->id,
                    'user_id' => $user->id,
                    'distance' => $user->distance ?? null,
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
                $count++;
            }
        }

        // Mark news as hyperlocal
        $this->is_hyperlocal = true;
        $this->save();

        return $count;
    }

    // ===== APPROVAL METHODS =====
    
    public function canBeApprovedBy(User $user)
    {
        if ($user->isSuperAdmin() || $user->role === 'admin') {
            return true;
        }

        if (!$user->can_approve) {
            return false;
        }

        // Block level
        if ($user->approval_level === 'block' && $this->block_id == $user->assigned_block_id) {
            return true;
        }

        // Tehsil level
        if ($user->approval_level === 'tehsil' && $this->tehsil_id == $user->assigned_tehsil_id) {
            return true;
        }

        // District level
        if ($user->approval_level === 'district' && $this->district_id == $user->assigned_district_id) {
            return true;
        }

        // State level
        if ($user->approval_level === 'state' && $this->state_id == $user->assigned_state_id) {
            return true;
        }

        return false;
    }

    public function approve($user)
    {
        $this->status = 'published';
        $this->published_at = now();
        $this->approved_by = $user->id;
        $this->approved_by_level = $user->approval_level;
        $this->save();

        // Add points
        $this->user->increment('points', 10);
        
        ReporterPoint::create([
            'user_id' => $this->user_id,
            'news_id' => $this->id,
            'points' => 10,
            'reason' => 'News published: ' . $this->title,
            'action' => 'news_published'
        ]);

        // Notification
        Notification::create([
            'user_id' => $this->user_id,
            'news_id' => $this->id,
            'type' => 'news_approved',
            'title' => '✅ खबर स्वीकृत!',
            'message' => 'आपकी खबर "' . $this->title . '" स्वीकृत कर ली गई है। आपको 10 पॉइंट्स मिले हैं!',
            'is_read' => false,
        ]);

        // Send hyperlocal notifications if location exists
        if ($this->latitude && $this->longitude) {
            $this->sendHyperlocalNotifications($this->location_radius ?? 5);
        }

        return $this;
    }

    public function reject($user, $reason)
    {
        $this->status = 'rejected';
        $this->rejection_reason = $reason;
        $this->approved_by = $user->id;
        $this->save();

        Notification::create([
            'user_id' => $this->user_id,
            'news_id' => $this->id,
            'type' => 'news_rejected',
            'title' => '❌ खबर अस्वीकृत!',
            'message' => 'आपकी खबर "' . $this->title . '" अस्वीकृत कर दी गई है।' . "\n\nकारण: " . $reason,
            'is_read' => false,
        ]);

        return $this;
    }
}