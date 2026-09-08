<?php

namespace App\Services;

use App\Models\Ad;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AdService
{
    protected $cacheDuration = 300; // 5 minutes

    /**
     * ✅ Get header ads (multiple)
     */
    public function getHeaderAds($location = null, $limit = 5)
    {
        return $this->getAdsByPosition('header', $location, $limit);
    }

    /**
     * ✅ Get header ad (single - for backward compatibility)
     */
    public function getHeaderAd($location = null)
    {
        $ads = $this->getHeaderAds($location, 1);
        return $ads->first();
    }

    /**
     * ✅ Get sidebar ads
     */
    public function getSidebarAds($location = null, $limit = 3)
    {
        return $this->getAdsByPosition('sidebar', $location, $limit);
    }

    /**
     * ✅ Get sidebar ad (single)
     */
    public function getSidebarAd($location = null)
    {
        $ads = $this->getSidebarAds($location, 1);
        return $ads->first();
    }

    /**
     * ✅ Get in-content ads
     */
    public function getInContentAds($location = null, $limit = 2)
    {
        return $this->getAdsByPosition('in_content', $location, $limit);
    }

    /**
     * ✅ Get in-content ad (single)
     */
    public function getInContentAd($location = null)
    {
        $ads = $this->getInContentAds($location, 1);
        return $ads->first();
    }

    /**
     * ✅ Get footer ads
     */
    public function getFooterAds($location = null, $limit = 3)
    {
        return $this->getAdsByPosition('footer', $location, $limit);
    }

    /**
     * ✅ Get footer ad (single)
     */
    public function getFooterAd($location = null)
    {
        $ads = $this->getFooterAds($location, 1);
        return $ads->first();
    }

    /**
     * ✅ Get mobile ads
     */
    public function getMobileAds($location = null, $limit = 2)
    {
        return $this->getAdsByPosition('mobile', $location, $limit);
    }

    /**
     * ✅ Get ads by position (multiple)
     */
    public function getAdsByPosition($position, $location = null, $limit = 5)
    {
        try {
            $cacheKey = 'ads_' . $position . '_' . ($location ? md5(json_encode($location)) : 'global');
            
            return Cache::remember($cacheKey, $this->cacheDuration, function() use ($position, $location, $limit) {
                $query = Ad::where('position', $position)
                    ->where('status', 'active')
                    ->where('is_active', 1)
                    ->where(function($q) {
                        $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                    });

                // Apply location targeting if provided
                if ($location && is_array($location)) {
                    $query->where(function($q) use ($location) {
                        if (isset($location['state_id']) && $location['state_id']) {
                            $q->orWhere('state_id', $location['state_id']);
                        }
                        if (isset($location['district_id']) && $location['district_id']) {
                            $q->orWhere('district_id', $location['district_id']);
                        }
                        if (isset($location['tehsil_id']) && $location['tehsil_id']) {
                            $q->orWhere('tehsil_id', $location['tehsil_id']);
                        }
                        if (isset($location['block_id']) && $location['block_id']) {
                            $q->orWhere('block_id', $location['block_id']);
                        }
                        // Also include global ads (no location restriction)
                        $q->orWhereNull('state_id')
                          ->orWhereNull('district_id')
                          ->orWhereNull('tehsil_id')
                          ->orWhereNull('block_id');
                    });
                }

                $ads = $query->orderBy('priority', 'desc')
                    ->orderBy('id', 'desc')
                    ->limit($limit)
                    ->get();

                // ✅ Log impressions for all ads
                foreach ($ads as $ad) {
                    $ad->incrementImpressions();
                }

                if ($ads->isNotEmpty()) {
                    Log::info('✅ Ads served', [
                        'position' => $position,
                        'count' => $ads->count(),
                        'ids' => $ads->pluck('id')->toArray()
                    ]);
                } else {
                    Log::warning('⚠️ No ads found for position: ' . $position);
                }

                return $ads;
            });
            
        } catch (\Exception $e) {
            Log::error('❌ AdService error for position ' . $position . ': ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * ✅ Clear all cache
     */
    public function clearCache()
    {
        try {
            Cache::flush();
            Log::info('🗑️ All ad cache cleared');
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Cache clear error: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ✅ Clear specific position cache
     */
    public function clearPositionCache($position)
    {
        try {
            Cache::forget('ads_' . $position . '_global');
            Log::info('🗑️ Ad cache cleared for position: ' . $position);
            return true;
        } catch (\Exception $e) {
            Log::error('❌ Cache clear error for position ' . $position . ': ' . $e->getMessage());
            return false;
        }
    }
}