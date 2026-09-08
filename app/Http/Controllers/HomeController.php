<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    public function index()
    {
        try {
            // ============================================================
            // ✅ BREAKING NEWS
            // ============================================================
            $breakingNews = News::where('is_breaking', true)
                                ->where('status', 'published')
                                ->latest('published_at')
                                ->take(5)
                                ->get();

            // ============================================================
            // ✅ LATEST NEWS
            // ============================================================
            $latestNews = News::where('status', 'published')
                              ->with(['user', 'category'])
                              ->latest('published_at')
                              ->paginate(12);

            // ============================================================
            // ✅ CATEGORIES – Only 5 Main Categories
            // ============================================================
            $categorySlugs = ['national', 'politics', 'education', 'sports', 'entertainment'];
            $categories = Category::where('is_active', true)
                                  ->whereIn('slug', $categorySlugs)
                                  ->orderBy('order')
                                  ->get();

            // ============================================================
            // ✅ STATES with DISTRICTS and TEHSILS (Active Only)
            // ============================================================
            $states = State::where('is_active', true)
                           ->with(['districts' => function($query) {
                               $query->where('is_active', true)
                                     ->with(['tehsils' => function($q) {
                                         $q->where('is_active', true);
                                     }]);
                           }])
                           ->orderBy('name')
                           ->get();

            // ============================================================
            // ✅ DISTRICTS & TEHSILS (for filter dropdowns)
            // ============================================================
            $districts = District::where('is_active', true)->orderBy('name')->get();
            $tehsils = Tehsil::where('is_active', true)->orderBy('name')->get();

            // ============================================================
            // ✅ TOP REPORTERS – Performance Based Ranking (FIXED)
            // ============================================================
            $topReporters = User::where('role', 'like', '%reporter%')
                                ->where('is_active', true)
                                ->where('is_verified', true)
                                ->withCount(['news' => function($query) {
                                    $query->where('status', 'published');
                                }])
                                ->withSum(['news' => function($query) {
                                    $query->where('status', 'published');
                                }], 'views')
                                ->orderBy('points', 'desc')
                                ->orderBy('news_count', 'desc')
                                ->orderBy('news_sum_views', 'desc')
                                ->limit(5)
                                ->get();

            // ============================================================
            // ✅ STATS – Total & Today's News
            // ============================================================
            $totalNews = News::where('status', 'published')->count();
            $todayNews = News::where('status', 'published')
                             ->whereDate('published_at', today())
                             ->count();

            // ============================================================
            // ✅ HYPERLOCAL NEWS (Nearby)
            // ============================================================
            $user = Auth::user();
            $nearbyNews = collect();
            $radius = 5;

            if ($user) {
                $location = $user->getLocation();
                if (is_array($location) && isset($location['latitude'], $location['longitude'])) {
                    $latitude = $location['latitude'];
                    $longitude = $location['longitude'];

                    $nearbyNews = News::where('status', 'published')
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->selectRaw(
                            "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                            [$latitude, $longitude, $latitude]
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
                'districts',
                'tehsils',
                'topReporters',
                'totalNews',
                'todayNews',
                'nearbyNews',
                'radius'
            ));

        } catch (\Exception $e) {
            Log::error('HomeController Error: ' . $e->getMessage());

            return view('home', [
                'breakingNews' => collect([]),
                'latestNews' => collect([]),
                'categories' => collect([]),
                'states' => collect([]),
                'districts' => collect([]),
                'tehsils' => collect([]),
                'topReporters' => collect([]),
                'totalNews' => 0,
                'todayNews' => 0,
                'nearbyNews' => collect([]),
                'radius' => 5,
            ])->with('error', 'कुछ गड़बड़ हो गई। कृपया बाद में पुनः प्रयास करें।');
        }
    }
}