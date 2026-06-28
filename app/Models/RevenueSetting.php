<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevenueSetting extends Model
{
    use HasFactory;

    protected $table = 'revenue_settings';

    protected $fillable = [
        'views_per_point',
        'point_value',
        'min_withdrawal',
        'max_withdrawal',
        'points_per_news',
        'min_points_for_monetisation',
        'min_views_for_monetisation',
        'min_followers_for_monetisation',
        'max_views_per_ip_per_day',
        'point_to_rupee_rate',
        'is_monetisation_active',
        'is_wallet_visible',
        'monetisation_terms',
    ];

    protected $casts = [
        'is_monetisation_active' => 'boolean',
        'is_wallet_visible' => 'boolean',
        'point_to_rupee_rate' => 'decimal:2',
        'min_points_for_monetisation' => 'integer',
        'min_views_for_monetisation' => 'integer',
        'min_followers_for_monetisation' => 'integer',
        'max_views_per_ip_per_day' => 'integer',
    ];

    public static function getSettings()
    {
        return self::first();
    }

    public static function isMonetisationActive()
    {
        $settings = self::getSettings();
        return $settings && $settings->is_monetisation_active;
    }

    public static function isWalletVisible()
    {
        $settings = self::getSettings();
        return $settings && $settings->is_wallet_visible;
    }

    // Check if reporter meets all criteria
    public static function canReporterMonetise($user)
    {
        $settings = self::getSettings();
        
        // 1. Check if global monetisation is active
        if (!$settings || !$settings->is_monetisation_active) {
            return [
                'can_monetise' => false,
                'reason' => 'Monetisation is currently inactive. Please contact admin.',
                'requirements' => []
            ];
        }

        // 2. Check if reporter has monetisation record
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        if (!$monetisation || !$monetisation->is_monetisation_active) {
            return [
                'can_monetise' => false,
                'reason' => 'Your monetisation request is pending admin approval.',
                'requirements' => []
            ];
        }

        // 3. Check all criteria
        $requirements = [
            'followers' => [
                'current' => $monetisation->total_followers ?? 0,
                'required' => $settings->min_followers_for_monetisation,
                'met' => ($monetisation->total_followers ?? 0) >= $settings->min_followers_for_monetisation,
                'label' => 'Total Followers'
            ],
            'views' => [
                'current' => $monetisation->unique_views ?? 0,
                'required' => $settings->min_views_for_monetisation,
                'met' => ($monetisation->unique_views ?? 0) >= $settings->min_views_for_monetisation,
                'label' => 'Unique Views'
            ],
            'points' => [
                'current' => $monetisation->total_points ?? 0,
                'required' => $settings->min_points_for_monetisation,
                'met' => ($monetisation->total_points ?? 0) >= $settings->min_points_for_monetisation,
                'label' => 'Total Points'
            ]
        ];

        // Check if all criteria met
        $allMet = true;
        foreach ($requirements as $key => $req) {
            if (!$req['met']) {
                $allMet = false;
                break;
            }
        }

        if (!$allMet) {
            return [
                'can_monetise' => false,
                'reason' => 'You haven\'t met all monetisation requirements yet.',
                'requirements' => $requirements
            ];
        }

        return [
            'can_monetise' => true,
            'reason' => 'You are eligible for monetisation!',
            'requirements' => $requirements
        ];
    }
}