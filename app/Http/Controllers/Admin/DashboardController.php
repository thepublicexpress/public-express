<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\News;
use App\Models\Withdrawal;
use App\Models\ReporterPoint;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_reporters' => User::whereIn('role', ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'])->count(),
            'state_reporters' => User::where('role', 'state_reporter')->count(),
            'district_reporters' => User::where('role', 'district_reporter')->count(),
            'tehsil_reporters' => User::where('role', 'tehsil_reporter')->count(),
            'block_reporters' => User::where('role', 'block_reporter')->count(),
            'total_news' => News::count(),
            'published_news' => News::where('status', 'published')->count(),
            'pending_news' => News::where('status', 'pending')->count(),
            'rejected_news' => News::where('status', 'rejected')->count(),
            'total_views' => News::sum('views'),
            'pending_withdrawals' => Withdrawal::where('status', 'pending')->count(),
            'completed_withdrawals' => Withdrawal::where('status', 'completed')->count(),
            'total_withdrawals' => Withdrawal::count(),
            'total_points_awarded' => ReporterPoint::sum('points'),
        ];
        
        $recent_news = News::with(['user', 'category'])
                           ->latest()
                           ->take(10)
                           ->get();
        
        $recent_users = User::latest()->take(5)->get();
        
        return view('admin.dashboard', compact('stats', 'recent_news', 'recent_users'));
    }
}