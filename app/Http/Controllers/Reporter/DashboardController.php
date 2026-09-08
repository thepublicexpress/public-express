<?php

namespace App\Http\Controllers\Reporter;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\RevenueSetting;
use App\Models\ReporterMonetisation;
use App\Models\NewsView;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // ============================================================
        // ✅ NEWS STATS – View के अनुसार Variable Names
        // ============================================================
        $totalNews = News::where('user_id', $user->id)
                         ->whereNull('deleted_at')
                         ->count();
        
        $publishedNews = News::where('user_id', $user->id)
                             ->where('status', 'published')
                             ->whereNull('deleted_at')
                             ->count();
        
        $pendingNews = News::where('user_id', $user->id)
                           ->where('status', 'pending')
                           ->whereNull('deleted_at')
                           ->count();
        
        $rejectedNews = News::where('user_id', $user->id)
                            ->where('status', 'rejected')
                            ->whereNull('deleted_at')
                            ->count();
        
        $draftNews = News::where('user_id', $user->id)
                         ->where('status', 'draft')
                         ->whereNull('deleted_at')
                         ->count();
        
        $totalViews = News::where('user_id', $user->id)
                          ->whereNull('deleted_at')
                          ->sum('views');
        
        // ============================================================
        // ✅ WALLET & POINTS – User Table से Direct
        // ============================================================
        $walletBalance = $user->wallet_balance ?? 0;
        $points = $user->points ?? 0;
        
        // ============================================================
        // ✅ RECENT NEWS (Latest 10)
        // ============================================================
        $recentNews = News::where('user_id', $user->id)
                          ->whereNull('deleted_at')
                          ->orderBy('created_at', 'desc')
                          ->limit(10)
                          ->get();
        
        // ============================================================
        // ✅ MONETISATION CHECK (पहले जैसा ही रखा)
        // ============================================================
        $settings = RevenueSetting::getSettings();
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        
        $canAccessWallet = false;
        $walletVisible = false;
        $monetisationMessage = null;
        $monetisationRequirements = [];
        $allRequirementsMet = false;
        
        if ($monetisation) {
            $followers = NewsView::whereIn('news_id', function($query) use ($user) {
                $query->select('id')->from('news')->where('user_id', $user->id);
            })->where('is_unique', true)->distinct('user_id')->count('user_id');
            
            $monetisation->total_followers = $followers;
            
            if ($settings) {
                $monetisation->required_followers_met = $followers >= $settings->min_followers_for_monetisation;
                $monetisation->required_views_met = $monetisation->unique_views >= $settings->min_views_for_monetisation;
                $monetisation->all_criteria_met = $monetisation->required_followers_met && $monetisation->required_views_met;
            }
            
            $monetisation->save();
        }
        
        $eligibility = RevenueSetting::canReporterMonetise($user);
        
        if ($settings && $settings->is_wallet_visible && $monetisation && $monetisation->is_monetisation_active) {
            if ($eligibility['can_monetise']) {
                $canAccessWallet = true;
                $walletVisible = true;
                $allRequirementsMet = true;
                $monetisationMessage = '✅ You are eligible for monetisation!';
            } else {
                $monetisationMessage = $eligibility['reason'];
                $monetisationRequirements = $eligibility['requirements'] ?? [];
                $walletVisible = false;
                $canAccessWallet = false;
            }
        } else {
            $walletVisible = false;
            $canAccessWallet = false;
            
            if (!$settings || !$settings->is_wallet_visible) {
                $monetisationMessage = '🔒 Monetisation is currently inactive. Please contact admin.';
            } elseif (!$monetisation || !$monetisation->is_monetisation_active) {
                $monetisationMessage = '⏳ Your monetisation request is pending admin approval.';
            } else {
                $monetisationMessage = '🔒 Monetisation is not available at this time.';
            }
        }

        // ============================================================
        // ✅ VIEW के लिए सभी Variables Pass करें
        // ============================================================
        return view('reporter.dashboard', compact(
            'totalNews',
            'publishedNews',
            'pendingNews',
            'rejectedNews',
            'draftNews',
            'totalViews',
            'walletBalance',
            'points',
            'recentNews',
            'user',
            'walletVisible',
            'canAccessWallet',
            'monetisationMessage',
            'monetisationRequirements',
            'allRequirementsMet',
            'monetisation'
        ));
    }
}