<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class Ad extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'type',
        'image',
        'code',
        'description',
        'url',
        'position',
        'state_id',
        'district_id',
        'tehsil_id',
        'block_id',
        'start_date',
        'end_date',
        'status',
        'is_active',
        'clicks',
        'impressions',
        'priority',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'clicks' => 'integer',
        'impressions' => 'integer',
        'priority' => 'integer',
        'deleted_at' => 'datetime',
    ];

    protected $attributes = [
        'clicks' => 0,
        'impressions' => 0,
        'priority' => 0,
        'status' => 'inactive',
        'is_active' => 1,
    ];

    // ============================================================
    // ✅ RELATIONSHIPS
    // ============================================================

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

    // ============================================================
    // ✅ SCOPES
    // ============================================================

    /**
     * Scope for active ads (status = active, is_active = 1, within date range)
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
            ->where('is_active', 1)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Scope for inactive ads
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Scope for paused ads
     */
    public function scopePaused($query)
    {
        return $query->where('status', 'paused');
    }

    /**
     * Scope by position
     */
    public function scopePosition($query, $position)
    {
        return $query->where('position', $position);
    }

    /**
     * Scope by type
     */
    public function scopeType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * ✅ Scope for location targeting (state, district, tehsil, block)
     */
    public function scopeLocation($query, $stateId = null, $districtId = null, $tehsilId = null, $blockId = null)
    {
        return $query->where(function($q) use ($stateId, $districtId, $tehsilId, $blockId) {
            // Match specific location if provided
            if ($stateId) {
                $q->orWhere('state_id', $stateId);
            }
            if ($districtId) {
                $q->orWhere('district_id', $districtId);
            }
            if ($tehsilId) {
                $q->orWhere('tehsil_id', $tehsilId);
            }
            if ($blockId) {
                $q->orWhere('block_id', $blockId);
            }
            // Also include ads with no location restriction (global ads)
            $q->orWhere(function($sub) {
                $sub->whereNull('state_id')
                    ->whereNull('district_id')
                    ->whereNull('tehsil_id')
                    ->whereNull('block_id');
            });
        });
    }

    /**
     * Scope for specific location only (exact match)
     */
    public function scopeLocationExact($query, $stateId = null, $districtId = null, $tehsilId = null, $blockId = null)
    {
        if ($stateId) {
            $query->where('state_id', $stateId);
        }
        if ($districtId) {
            $query->where('district_id', $districtId);
        }
        if ($tehsilId) {
            $query->where('tehsil_id', $tehsilId);
        }
        if ($blockId) {
            $query->where('block_id', $blockId);
        }
        return $query;
    }

    /**
     * Scope for ads that are currently visible
     */
    public function scopeVisible($query)
    {
        return $query->active();
    }

    /**
     * ✅ Scope for sorting by priority (higher priority first)
     */
    public function scopePriority($query)
    {
        return $query->orderBy('priority', 'desc')->orderBy('id', 'desc');
    }

    // ============================================================
    // ✅ ACCESSORS & MUTATORS
    // ============================================================

    /**
     * Get image URL
     */
    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        return [
            'active' => 'success',
            'inactive' => 'secondary',
            'paused' => 'warning',
            'expired' => 'danger',
        ][$this->status] ?? 'secondary';
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        return [
            'active' => '✅ सक्रिय',
            'inactive' => '⛔ निष्क्रिय',
            'paused' => '⏸️ रोका गया',
            'expired' => '⏳ समाप्त',
        ][$this->status] ?? $this->status;
    }

    /**
     * Get position label
     */
    public function getPositionLabelAttribute()
    {
        return [
            'header' => '🚀 हेडर',
            'sidebar' => '📌 साइडबार',
            'in-article' => '📄 आर्टिकल के बीच',
            'in_content' => '📄 आर्टिकल के बीच',
            'footer' => '🔻 फुटर',
            'mobile' => '📱 मोबाइल',
        ][$this->position] ?? $this->position;
    }

    /**
     * Get type label
     */
    public function getTypeLabelAttribute()
    {
        return [
            'image' => '🖼️ इमेज',
            'code' => '💻 कोड',
            'text' => '📝 टेक्स्ट',
        ][$this->type] ?? $this->type;
    }

    /**
     * Get is_active label
     */
    public function getIsActiveLabelAttribute()
    {
        return $this->is_active ? '✅ हाँ' : '❌ नहीं';
    }

    /**
     * Check if ad is active and visible
     */
    public function getIsVisibleAttribute()
    {
        if ($this->status !== 'active') {
            return false;
        }
        if (!$this->is_active) {
            return false;
        }
        if ($this->start_date && $this->start_date > now()) {
            return false;
        }
        if ($this->end_date && $this->end_date < now()) {
            return false;
        }
        return true;
    }

    // ============================================================
    // ✅ HELPER METHODS
    // ============================================================

    /**
     * ✅ Increment impression count
     */
    public function incrementImpressions()
    {
        $this->increment('impressions');
        return $this;
    }

    /**
     * ✅ Increment click count
     */
    public function incrementClicks()
    {
        $this->increment('clicks');
        return $this;
    }

    /**
     * ✅ Get ads for a specific position with caching
     */
    public static function getForPosition($position, $stateId = null, $districtId = null, $tehsilId = null, $blockId = null)
    {
        $cacheKey = 'ads_' . $position . '_' . ($stateId ?? 'all') . '_' . ($districtId ?? 'all') . '_' . ($tehsilId ?? 'all') . '_' . ($blockId ?? 'all');
        
        return Cache::remember($cacheKey, 300, function() use ($position, $stateId, $districtId, $tehsilId, $blockId) {
            return self::visible()
                ->position($position)
                ->location($stateId, $districtId, $tehsilId, $blockId)
                ->priority()
                ->get();
        });
    }

    /**
     * ✅ Clear cache for this ad's position
     */
    public function clearCache()
    {
        Cache::forget('ads_' . $this->position . '_all_all_all_all');
        Cache::forget('ad_' . $this->position);
        
        // Also clear location-specific caches
        $locations = [
            $this->state_id,
            $this->district_id,
            $this->tehsil_id,
            $this->block_id
        ];
        foreach ($locations as $loc) {
            if ($loc) {
                Cache::forget('ads_' . $this->position . '_' . $loc . '_all_all_all');
                Cache::forget('ads_' . $this->position . '_all_' . $loc . '_all_all');
                Cache::forget('ads_' . $this->position . '_all_all_' . $loc . '_all');
                Cache::forget('ads_' . $this->position . '_all_all_all_' . $loc);
            }
        }
        return $this;
    }

    // ============================================================
    // ✅ BOOT METHOD
    // ============================================================

    protected static function boot()
    {
        parent::boot();

        // ✅ Clear cache when ad is created, updated, or deleted
        static::saved(function ($ad) {
            $ad->clearCache();
        });

        static::deleted(function ($ad) {
            $ad->clearCache();
        });

        static::restored(function ($ad) {
            $ad->clearCache();
        });
    }
}