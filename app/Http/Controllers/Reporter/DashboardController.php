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
        
        $stats = [
            'total_news' => News::where('user_id', $user->id)->count(),
            'published_news' => News::where('user_id', $user->id)->where('status', 'published')->count(),
            'pending_news' => News::where('user_id', $user->id)->where('status', 'pending')->count(),
            'total_views' => News::where('user_id', $user->id)->sum('views'),
        ];
        
        $recent_news = News::where('user_id', $user->id)->latest()->take(5)->get();
        
        // ===== MONETISATION CHECK =====
        $settings = RevenueSetting::getSettings();
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        
        // Default values
        $canAccessWallet = false;
        $walletVisible = false;
        $monetisationMessage = null;
        $monetisationRequirements = [];
        $allRequirementsMet = false;
        
        // Update reporter stats
        if ($monetisation) {
            // Calculate followers (using unique users who viewed their news)
            $followers = NewsView::whereIn('news_id', function($query) use ($user) {
                $query->select('id')->from('news')->where('user_id', $user->id);
            })->where('is_unique', true)->distinct('user_id')->count('user_id');
            
            // Update followers count
            $monetisation->total_followers = $followers;
            
            // Check requirements
            if ($settings) {
                $monetisation->required_followers_met = $followers >= $settings->min_followers_for_monetisation;
                $monetisation->required_views_met = $monetisation->unique_views >= $settings->min_views_for_monetisation;
                $monetisation->all_criteria_met = $monetisation->required_followers_met && $monetisation->required_views_met;
            }
            
            $monetisation->save();
        }
        
        // Check monetisation eligibility
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

        return view('reporter.dashboard', compact(
            'stats', 
            'recent_news', 
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