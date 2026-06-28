<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ReporterMonetisation;
use App\Models\RevenueSetting;
use App\Models\ReporterPoint;
use Illuminate\Http\Request;

class ReporterMonetisationController extends Controller
{
    /**
     * Display a listing of reporter monetisation status
     */
    public function index(Request $request)
    {
        $query = User::whereIn('role', [
            'reporter', 
            'state_reporter', 
            'district_reporter', 
            'tehsil_reporter', 
            'block_reporter', 
            'national_reporter'
        ])
        ->with('monetisation');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('monetisation_status')) {
            $query->whereHas('monetisation', function($q) use ($request) {
                $q->where('is_monetisation_active', $request->monetisation_status == 'active');
            });
        }

        $reporters = $query->paginate(20);
        $settings = RevenueSetting::getSettings();

        return view('admin.reporter-monetisation.index', compact('reporters', 'settings'));
    }

    /**
     * Show specific reporter monetisation details
     */
    public function show(User $user)
    {
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        
        if (!$monetisation) {
            $monetisation = ReporterMonetisation::create([
                'user_id' => $user->id,
                'is_monetisation_active' => false,
                'total_points' => $user->points ?? 0,
                'total_followers' => 0,
                'total_views' => 0,
                'unique_views' => 0,
                'fake_views_detected' => 0,
                'total_earnings' => 0,
                'available_balance' => 0,
                'required_followers_met' => false,
                'required_views_met' => false,
                'all_criteria_met' => false,
            ]);
        }

        $monetisation->updateStats();
        $settings = RevenueSetting::getSettings();

        return view('admin.reporter-monetisation.show', compact('user', 'monetisation', 'settings'));
    }

    /**
     * Toggle monetisation for a reporter
     */
    public function toggle(User $user)
    {
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        
        if (!$monetisation) {
            $monetisation = ReporterMonetisation::create([
                'user_id' => $user->id,
                'is_monetisation_active' => true,
                'total_points' => $user->points ?? 0,
                'total_followers' => 0,
                'total_views' => 0,
                'unique_views' => 0,
                'fake_views_detected' => 0,
                'total_earnings' => 0,
                'available_balance' => 0,
                'required_followers_met' => false,
                'required_views_met' => false,
                'all_criteria_met' => false,
                'monetisation_activated_at' => now(),
            ]);
            
            return back()->with('success', 'Monetisation activated for ' . $user->name);
        }

        $monetisation->is_monetisation_active = !$monetisation->is_monetisation_active;
        $monetisation->monetisation_activated_at = $monetisation->is_monetisation_active ? now() : null;
        $monetisation->save();

        $status = $monetisation->is_monetisation_active ? 'activated' : 'deactivated';
        return back()->with('success', "Monetisation {$status} for " . $user->name);
    }

    /**
     * Add points to reporter
     */
    public function addPoints(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer|min:1|max:1000',
            'reason' => 'required|string|max:255',
        ]);

        $user->increment('points', $request->points);

        ReporterPoint::create([
            'user_id' => $user->id,
            'points' => $request->points,
            'reason' => $request->reason,
            'action' => 'admin_added',
        ]);

        // Update monetisation stats
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        if ($monetisation) {
            $monetisation->updateStats();
        }

        return back()->with('success', "{$request->points} points added to " . $user->name);
    }

    /**
     * Deduct points from reporter
     */
    public function deductPoints(Request $request, User $user)
    {
        $request->validate([
            'points' => 'required|integer|min:1|max:' . ($user->points ?? 0),
            'reason' => 'required|string|max:255',
        ]);

        $user->decrement('points', $request->points);

        ReporterPoint::create([
            'user_id' => $user->id,
            'points' => -$request->points,
            'reason' => $request->reason,
            'action' => 'admin_deducted',
        ]);

        // Update monetisation stats
        $monetisation = ReporterMonetisation::where('user_id', $user->id)->first();
        if ($monetisation) {
            $monetisation->updateStats();
        }

        return back()->with('success', "{$request->points} points deducted from " . $user->name);
    }

    /**
     * Update monetisation stats for all reporters
     */
    public function updateAllStats()
    {
        $reporters = User::whereIn('role', [
            'reporter', 
            'state_reporter', 
            'district_reporter', 
            'tehsil_reporter', 
            'block_reporter', 
            'national_reporter'
        ])->get();

        $count = 0;
        foreach ($reporters as $reporter) {
            $monetisation = ReporterMonetisation::where('user_id', $reporter->id)->first();
            if ($monetisation) {
                $monetisation->updateStats();
                $count++;
            }
        }

        return back()->with('success', "Stats updated for {$count} reporters.");
    }

    /**
     * Update stats for specific reporter
     */
    public function updateStats($id)
    {
        $monetisation = ReporterMonetisation::findOrFail($id);
        $monetisation->updateStats();
        
        return back()->with('success', 'Stats updated successfully!');
    }
}