<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        // Breaking News
        $breakingNews = News::where('is_breaking', true)
                            ->where('status', 'published')
                            ->latest('published_at')
                            ->take(5)
                            ->get();

        // Latest News
        $latestNews = News::where('status', 'published')
                          ->latest('published_at')
                          ->paginate(12);

        // Categories - ONLY 5 MAIN CATEGORIES
        $categorySlugs = ['national', 'politics', 'education', 'sports', 'entertainment'];
        $categories = NewsCategory::where('is_active', true)
                                  ->whereIn('slug', $categorySlugs)
                                  ->orderBy('sort_order')
                                  ->get();

        // States with districts and tehsils - ONLY ACTIVE LOCATIONS
        $states = State::where('is_active', true)
                       ->with(['districts' => function($query) {
                           $query->where('is_active', true)
                                 ->with(['tehsils' => function($q) {
                                     $q->where('is_active', true);
                                 }]);
                       }])
                       ->orderBy('name')
                       ->get();

        // Top Reporters
        $topReporters = User::whereIn('role', ['reporter', 'state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter'])
                            ->where('is_active', true)
                            ->where('is_verified', true)
                            ->orderBy('points', 'desc')
                            ->take(5)
                            ->get();

        // Additional stats
        $totalNews = News::where('status', 'published')->count();
        $todayNews = News::where('status', 'published')
                         ->whereDate('created_at', today())
                         ->count();

        // ===== HYPERLOCAL NEWS =====
        $user = Auth::user();
        $nearbyNews = collect();
        $radius = 5;
        
        if ($user) {
            $location = $user->getLocation();
            if ($location) {
                $nearbyNews = News::where('status', 'published')
                    ->whereNotNull('latitude')
                    ->whereNotNull('longitude')
                    ->selectRaw(
                        "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                        [$location['latitude'], $location['longitude'], $location['latitude']]
                    )
                    ->having('distance', '<=', $radius)
                    ->orderBy('distance')
                    ->limit(6)
                    ->get();
            }
        }

        return view('home', compact(
            'breakingNews',
            'latestNews',
            'categories',
            'states',
            'topReporters',
            'totalNews',
            'todayNews',
            'nearbyNews',
            'radius'
        ));
    }
}