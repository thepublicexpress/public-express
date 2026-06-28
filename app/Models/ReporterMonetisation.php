<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporterMonetisation extends Model
{
    protected $table = 'reporter_monetisation';

    protected $fillable = [
        'user_id',
        'is_monetisation_active',
        'total_points',
        'total_followers',
        'total_views',
        'unique_views',
        'fake_views_detected',
        'total_earnings',
        'available_balance',
        'required_followers_met',
        'required_views_met',
        'all_criteria_met',
        'monetisation_activated_at'
    ];

    protected $casts = [
        'is_monetisation_active' => 'boolean',
        'total_points' => 'integer',
        'total_followers' => 'integer',
        'total_views' => 'integer',
        'unique_views' => 'integer',
        'fake_views_detected' => 'integer',
        'total_earnings' => 'decimal:2',
        'available_balance' => 'decimal:2',
        'required_followers_met' => 'boolean',
        'required_views_met' => 'boolean',
        'all_criteria_met' => 'boolean',
        'monetisation_activated_at' => 'datetime'
    ];

    // ===== RELATIONSHIPS =====
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ===== HELPERS =====
    public function calculateEarnings()
    {
        $settings = RevenueSetting::getSettings();
        if (!$settings) return 0;

        $eligiblePoints = max(0, $this->total_points - $settings->min_points_for_monetisation);
        return $eligiblePoints * $settings->point_to_rupee_rate;
    }

    public function updateStats()
    {
        // Update total views from news_views
        $this->total_views = NewsView::whereIn('news_id', function($q) {
            $q->select('id')->from('news')->where('user_id', $this->user_id);
        })->count();

        $this->unique_views = NewsView::whereIn('news_id', function($q) {
            $q->select('id')->from('news')->where('user_id', $this->user_id);
        })->where('is_unique', true)->count();

        $this->fake_views_detected = NewsView::whereIn('news_id', function($q) {
            $q->select('id')->from('news')->where('user_id', $this->user_id);
        })->where('is_fake', true)->count();

        // Calculate followers (unique users who viewed their news)
        $this->total_followers = NewsView::whereIn('news_id', function($q) {
            $q->select('id')->from('news')->where('user_id', $this->user_id);
        })->where('is_unique', true)->distinct('user_id')->count('user_id');

        $this->total_points = ReporterPoint::where('user_id', $this->user_id)->sum('points');
        $this->total_earnings = $this->calculateEarnings();
        $this->available_balance = max(0, $this->total_earnings - $this->user->withdrawals()->where('status', 'completed')->sum('amount'));

        // Check requirements
        $settings = RevenueSetting::getSettings();
        if ($settings) {
            $this->required_followers_met = $this->total_followers >= $settings->min_followers_for_monetisation;
            $this->required_views_met = $this->unique_views >= $settings->min_views_for_monetisation;
            $this->all_criteria_met = $this->required_followers_met && $this->required_views_met;
        }

        $this->save();
        return $this;
    }

    public function canAccessWallet()
    {
        $settings = RevenueSetting::getSettings();
        if (!$settings || !$settings->is_wallet_visible) {
            return false;
        }

        if (!$this->is_monetisation_active) {
            return false;
        }

        if (!$this->all_criteria_met) {
            return false;
        }

        $settings = RevenueSetting::getSettings();
        if ($this->total_points < $settings->min_points_for_monetisation) {
            return false;
        }

        return true;
    }
}