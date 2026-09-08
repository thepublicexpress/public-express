<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use App\Models\User;
use App\Models\Category; // ✅ NewsCategory → Category
use App\Models\Ad;
use App\Models\Withdrawal;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    /**
     * ✅ Admin Dashboard - Complete with all data including State & District
     */
    public function index()
    {
        try {
            // ============================================================
            // 1. NEWS STATS
            // ============================================================
            $totalNews = News::count();
            $pendingNews = News::where('status', 'pending')->count();
            $publishedNews = News::where('status', 'published')->count();
            $rejectedNews = News::where('status', 'rejected')->count();
            $draftNews = News::where('status', 'draft')->count();
            $totalViews = News::sum('views');

            // ============================================================
            // 2. USER STATS
            // ============================================================
            $totalUsers = User::count();
            $totalReporters = User::where('role', 'reporter')->count();
            $totalAdmins = User::where('role', 'admin')->orWhere('role', 'super_admin')->count();
            $totalSubscribers = User::where('role', 'subscriber')->count();
            $pendingReporters = User::where('role', 'reporter')->where('is_verified', 0)->count();

            // ============================================================
            // 3. TODAY'S STATS
            // ============================================================
            $todayNews = News::whereDate('created_at', today())->count();
            $todayPublished = News::whereDate('created_at', today())->where('status', 'published')->count();
            $todayUsers = User::whereDate('created_at', today())->count();

            // ============================================================
            // 4. AD STATS
            // ============================================================
            $totalAds = Ad::count();
            $activeAds = Ad::where('status', 'active')->count();
            $totalAdClicks = Ad::sum('clicks');
            $totalAdImpressions = Ad::sum('impressions');

            // ============================================================
            // 5. WITHDRAWAL STATS
            // ============================================================
            $pendingWithdrawals = Withdrawal::where('status', 'pending')->count();

            // ============================================================
            // 6. LOCATION STATS ✅ NEW
            // ============================================================
            $totalStates = State::count();
            $totalDistricts = District::count();
            $totalTehsils = Tehsil::count();
            $totalBlocks = Block::count();

            // ============================================================
            // 7. STATE WISE NEWS COUNT ✅ NEW
            // ============================================================
            $stateWiseNews = State::select('id', 'name', 'slug')
                ->withCount(['news' => function($query) {
                    $query->where('status', 'published');
                }])
                ->orderBy('news_count', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 8. DISTRICT WISE NEWS COUNT ✅ NEW
            // ============================================================
            $districtWiseNews = District::select('id', 'name', 'slug', 'state_id')
                ->with(['state' => function($query) {
                    $query->select('id', 'name');
                }])
                ->withCount(['news' => function($query) {
                    $query->where('status', 'published');
                }])
                ->orderBy('news_count', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 9. STATE WISE REPORTERS ✅ NEW
            // ============================================================
            $stateWiseReporters = State::select('id', 'name')
                ->withCount(['users' => function($query) {
                    $query->where('role', 'reporter');
                }])
                ->orderBy('users_count', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 10. CATEGORY WISE NEWS (using Category model)
            // ============================================================
            $categoryStats = Category::where('is_active', 1)
                ->withCount(['news' => function($query) {
                    $query->where('status', 'published');
                }])
                ->orderBy('news_count', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 11. TOP REPORTERS
            // ============================================================
            $topReporters = User::where('role', 'reporter')
                ->withCount(['news' => function($query) {
                    $query->where('status', 'published');
                }])
                ->orderBy('news_count', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 12. RECENT NEWS
            // ============================================================
            $recentNews = News::with(['user', 'category', 'state', 'district'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 13. RECENT USERS
            // ============================================================
            $recentUsers = User::orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 14. PENDING APPROVALS
            // ============================================================
            $pendingApprovals = News::with(['user', 'category'])
                ->where('status', 'pending')
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // 15. BREAKING NEWS
            // ============================================================
            $breakingNews = News::where('is_breaking', true)
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            // ============================================================
            // 16. CHART DATA (Last 7 Days)
            // ============================================================
            $chartLabels = [];
            $chartData = [];

            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $chartLabels[] = $date->format('d M');
                $chartData[] = News::whereDate('created_at', $date->toDateString())
                    ->where('status', 'published')
                    ->count();
            }

            // ============================================================
            // 17. MONTHLY STATS
            // ============================================================
            $monthlyStats = [];
            for ($i = 11; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $monthlyStats[] = [
                    'month' => $month->format('M Y'),
                    'news' => News::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->count(),
                    'published' => News::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->where('status', 'published')
                        ->count(),
                ];
            }

            return view('admin.dashboard', compact(
                'totalNews',
                'pendingNews',
                'publishedNews',
                'rejectedNews',
                'draftNews',
                'totalViews',
                'totalUsers',
                'totalReporters',
                'totalAdmins',
                'totalSubscribers',
                'pendingReporters',
                'todayNews',
                'todayPublished',
                'todayUsers',
                'totalAds',
                'activeAds',
                'totalAdClicks',
                'totalAdImpressions',
                'pendingWithdrawals',
                'totalStates',
                'totalDistricts',
                'totalTehsils',
                'totalBlocks',
                'stateWiseNews',
                'districtWiseNews',
                'stateWiseReporters',
                'categoryStats',
                'topReporters',
                'recentNews',
                'recentUsers',
                'pendingApprovals',
                'breakingNews',
                'chartLabels',
                'chartData',
                'monthlyStats'
            ));

        } catch (\Exception $e) {
            Log::error('Dashboard Error: ' . $e->getMessage());
            Log::error('Dashboard Trace: ' . $e->getTraceAsString());

            // Return with empty data
            return view('admin.dashboard', [
                'error' => 'Dashboard लोड करने में समस्या आ रही है: ' . $e->getMessage(),
                'totalNews' => 0,
                'pendingNews' => 0,
                'publishedNews' => 0,
                'rejectedNews' => 0,
                'draftNews' => 0,
                'totalViews' => 0,
                'totalUsers' => 0,
                'totalReporters' => 0,
                'totalAdmins' => 0,
                'totalSubscribers' => 0,
                'pendingReporters' => 0,
                'todayNews' => 0,
                'todayPublished' => 0,
                'todayUsers' => 0,
                'totalAds' => 0,
                'activeAds' => 0,
                'totalAdClicks' => 0,
                'totalAdImpressions' => 0,
                'pendingWithdrawals' => 0,
                'totalStates' => 0,
                'totalDistricts' => 0,
                'totalTehsils' => 0,
                'totalBlocks' => 0,
                'stateWiseNews' => collect([]),
                'districtWiseNews' => collect([]),
                'stateWiseReporters' => collect([]),
                'categoryStats' => collect([]),
                'topReporters' => collect([]),
                'recentNews' => collect([]),
                'recentUsers' => collect([]),
                'pendingApprovals' => collect([]),
                'breakingNews' => collect([]),
                'chartLabels' => [],
                'chartData' => [],
                'monthlyStats' => []
            ]);
        }
    }

    /**
     * ✅ Get quick stats for AJAX
     */
    public function quickStats()
    {
        try {
            $stats = [
                'total_news' => News::count(),
                'pending_news' => News::where('status', 'pending')->count(),
                'published_news' => News::where('status', 'published')->count(),
                'total_users' => User::count(),
                'reporters' => User::where('role', 'reporter')->count(),
                'total_views' => News::sum('views'),
                'today_news' => News::whereDate('created_at', today())->count(),
                'today_users' => User::whereDate('created_at', today())->count(),
                'pending_reporters' => User::where('role', 'reporter')->where('is_verified', 0)->count(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'updated_at' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('Quick Stats Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * ✅ Get Location Stats for AJAX
     */
    public function locationStats()
    {
        try {
            $stats = [
                'states' => State::count(),
                'districts' => District::count(),
                'tehsils' => Tehsil::count(),
                'blocks' => Block::count(),
                'state_wise_news' => State::withCount(['news' => function($query) {
                    $query->where('status', 'published');
                }])->orderBy('news_count', 'desc')->limit(10)->get(),
                'district_wise_news' => District::withCount(['news' => function($query) {
                    $query->where('status', 'published');
                }])->orderBy('news_count', 'desc')->limit(10)->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $stats,
                'updated_at' => now()->toDateTimeString()
            ]);

        } catch (\Exception $e) {
            Log::error('Location Stats Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}