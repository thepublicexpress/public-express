<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsPageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\LocationAPIController;
use App\Http\Controllers\Reporter\NewsController as ReporterNewsController;
use App\Http\Controllers\Reporter\AuthController as ReporterAuthController;
use App\Http\Controllers\Reporter\DashboardController as ReporterDashboardController;
use App\Http\Controllers\Reporter\WalletController as ReporterWalletController;
use App\Http\Controllers\Reporter\NotificationController as ReporterNotificationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\LocationController as AdminLocationController;
use App\Http\Controllers\Admin\ReporterAssignmentController;
use App\Http\Controllers\Admin\SettingsController as AdminSettingsController;
use App\Http\Controllers\Admin\WithdrawalController as AdminWithdrawalController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\ReporterMonetisationController;
use App\Http\Controllers\Admin\FakeViewsController;
use App\Http\Controllers\Admin\AdController as AdminAdController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\NotificationController as AdminNotificationController;
use App\Http\Controllers\Admin\PollController as AdminPollController; // ✅ Admin Poll Controller
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\HyperlocalNewsController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\NewsletterController;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RssController;
use App\Http\Controllers\AdvertiseController;

// ✅ FCM Push Notification System
use App\Http\Controllers\PushSubscriptionController;
use App\Http\Controllers\NotificationController;
use App\Helpers\NotificationHelper;

// ✅ Opinion Poll System
use App\Http\Controllers\PollController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===================== EMERGENCY & CACHE ROUTES =====================

Route::get('/clear-all-cache', function() {
    try {
        \Illuminate\Support\Facades\Cache::flush();
        \Illuminate\Support\Facades\Artisan::call('config:clear');
        \Illuminate\Support\Facades\Artisan::call('route:clear');
        \Illuminate\Support\Facades\Artisan::call('view:clear');
        \Illuminate\Support\Facades\Artisan::call('optimize:clear');
        
        $viewPath = storage_path('framework/views');
        if (is_dir($viewPath)) {
            $files = glob($viewPath . '/*.php');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        
        $sessionPath = storage_path('framework/sessions');
        if (is_dir($sessionPath)) {
            $files = glob($sessionPath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        
        $cachePath = storage_path('framework/cache');
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '/*');
            foreach ($files as $file) {
                if (is_file($file)) unlink($file);
            }
        }
        
        return '<h2>✅ All Cache Cleared Successfully!</h2>
                <ul>
                    <li>✅ Application Cache</li>
                    <li>✅ Config Cache</li>
                    <li>✅ Route Cache</li>
                    <li>✅ View Cache</li>
                    <li>✅ Session Files</li>
                    <li>✅ Cache Files</li>
                </ul>
                <p><a href="/">🏠 Go Home</a></p>';
                
    } catch (\Exception $e) {
        return '<h2>❌ Error Clearing Cache</h2>
                <p>Error: ' . $e->getMessage() . '</p>
                <p><a href="/">Go Home</a></p>';
    }
});

// ===================== SEO & META ROUTES =====================

Route::get('/robots.txt', function() {
    $content = "User-agent: *\n";
    $content .= "Allow: /\n";
    $content .= "Disallow: /admin/\n";
    $content .= "Disallow: /reporter/\n";
    $content .= "Disallow: /otp/\n";
    $content .= "Disallow: /api/\n";
    $content .= "Sitemap: " . url('/sitemap.xml') . "\n";
    $content .= "Sitemap: " . url('/news-sitemap.xml') . "\n";
    
    return response($content, 200)->header('Content-Type', 'text/plain');
});

// ============================================================
// ✅ FIXED SITEMAP - Only 200 OK URLs
// ============================================================

Route::get('/sitemap.xml', function() {
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
    $sitemap .= '<url><loc>' . url('/') . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>1.0</priority></url>';
    
    $categories = App\Models\Category::where('is_active', 1)->where('slug', '!=', '')->get();
    foreach ($categories as $category) {
        if (route('category.show', $category->slug, false)) {
            $sitemap .= '<url><loc>' . route('category.show', $category->slug) . '</loc><lastmod>' . date('Y-m-d') . '</lastmod><changefreq>daily</changefreq><priority>0.8</priority></url>';
        }
    }
    
    $news = App\Models\News::where('status', 'published')
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->orderBy('published_at', 'desc')
        ->limit(500)
        ->get();
    
    foreach ($news as $item) {
        if (route('news.show', $item->slug, false)) {
            $sitemap .= '<url><loc>' . route('news.show', $item->slug) . '</loc><lastmod>' . ($item->updated_at ? $item->updated_at->format('Y-m-d') : date('Y-m-d')) . '</lastmod><changefreq>weekly</changefreq><priority>0.6</priority></url>';
        }
    }
    
    $sitemap .= '</urlset>';
    return response($sitemap, 200)->header('Content-Type', 'application/xml')->header('Cache-Control', 'public, max-age=3600');
});

Route::get('/news-sitemap.xml', function() {
    $news = App\Models\News::where('status', 'published')
        ->whereNotNull('slug')
        ->where('slug', '!=', '')
        ->orderBy('published_at', 'desc')
        ->limit(1000)
        ->get();
    
    $sitemap = '<?xml version="1.0" encoding="UTF-8"?>';
    $sitemap .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">';
    
    foreach ($news as $item) {
        if (route('news.show', $item->slug, false)) {
            $sitemap .= '<url>';
            $sitemap .= '<loc>' . route('news.show', $item->slug) . '</loc>';
            $sitemap .= '<lastmod>' . $item->updated_at->format('Y-m-d') . '</lastmod>';
            $sitemap .= '<changefreq>daily</changefreq>';
            $sitemap .= '<priority>0.6</priority>';
            $sitemap .= '<news:news>';
            $sitemap .= '<news:publication>';
            $sitemap .= '<news:name>द पब्लिक एक्सप्रेस</news:name>';
            $sitemap .= '<news:language>hi</news:language>';
            $sitemap .= '</news:publication>';
            $sitemap .= '<news:publication_date>' . $item->published_at->format('Y-m-d') . '</news:publication_date>';
            $sitemap .= '<news:title>' . htmlspecialchars($item->title, ENT_XML1, 'UTF-8') . '</news:title>';
            $sitemap .= '</news:news>';
            $sitemap .= '</url>';
        }
    }
    
    $sitemap .= '</urlset>';
    return response($sitemap, 200)->header('Content-Type', 'application/xml')->header('Cache-Control', 'public, max-age=3600');
})->name('news.sitemap');

// ads.txt – dynamic redirect
Route::get('/ads.txt', function() {
    return redirect()->away('https://srv.adstxtmanager.com/19390/thepublicexpress.com', 301);
});

// ============================================================
// ✅ FIXED IMAGE ROUTES - Direct Path, No Redirects
// ============================================================

Route::get('/serve-image/{path}', function($path) {
    $path = urldecode($path);
    $path = str_replace(['..', '\\', '//'], '', $path);
    $path = ltrim($path, '/');
    
    $fullPath = public_path($path);
    $possiblePaths = [
        public_path('uploads/news/' . basename($path)),
        public_path('storage/' . $path),
        storage_path('app/public/' . $path),
        public_path('images/' . basename($path)),
    ];
    array_unshift($possiblePaths, $fullPath);
    
    foreach (array_unique($possiblePaths) as $fullPath) {
        $fullPath = str_replace(['//', '\\\\'], '/', $fullPath);
        if (file_exists($fullPath) && is_readable($fullPath) && !is_dir($fullPath)) {
            try {
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                $etag = md5_file($fullPath);
                $lastModified = filemtime($fullPath);
                $headers = [
                    'Content-Type' => $mime,
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                    'ETag' => $etag,
                    'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                    'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
                ];
                if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] == $etag) {
                    return response('', 304)->withHeaders($headers);
                }
                $imageData = file_get_contents($fullPath);
                if ($imageData === false) {
                    continue;
                }
                return response($imageData, 200)->withHeaders($headers);
            } catch (\Exception $e) {
                \Log::error('Image serve error: ' . $e->getMessage());
            }
        }
    }

    $placeholder = "data:image/svg+xml;charset=UTF-8,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22400%22%20height%3D%22225%22%20viewBox%3D%220%200%20400%20225%22%3E%3Crect%20fill%3D%22%23e30613%22%20width%3D%22400%22%20height%3D%22225%22%2F%3E%3Ctext%20fill%3D%22white%22%20font-family%3D%22sans-serif%22%20font-size%3D%2220%22%20x%3D%2250%25%22%20y%3D%2250%25%22%20text-anchor%3D%22middle%22%20dominant-baseline%3D%22middle%22%3ENews%3C%2Ftext%3E%3C%2Fsvg%3E";
    return response($placeholder, 200)->header('Content-Type', 'image/svg+xml')->header('Cache-Control', 'public, max-age=86400');
})->where('path', '.*');

// ===================== PUBLIC ROUTES =====================
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('news')->name('news.')->group(function () {
    Route::get('/search', [NewsPageController::class, 'search'])->name('search');
    Route::get('/category/{slug}', [NewsPageController::class, 'category'])->name('category');
    Route::get('/state/{slug}', [NewsPageController::class, 'state'])->name('state');
    Route::get('/district/{slug}', [NewsPageController::class, 'district'])->name('district');
    Route::get('/tehsil/{slug}', [NewsPageController::class, 'tehsil'])->name('tehsil');
    Route::get('/block/{slug}', [NewsPageController::class, 'block'])->name('block');
    Route::get('/latest', [NewsPageController::class, 'latest'])->name('latest');
    Route::get('/trending', [NewsPageController::class, 'trending'])->name('trending');
    Route::get('/{slug}', [NewsPageController::class, 'show'])->name('show');
});

Route::prefix('category')->name('category.')->group(function () {
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
});

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');
Route::get('/rss', [RssController::class, 'index'])->name('rss');

Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

Route::get('/advertise', [AdvertiseController::class, 'index'])->name('advertise');

// ===================== OTP ROUTES =====================
Route::prefix('otp')->name('otp.')->group(function () {
    Route::get('/register', [OTPController::class, 'showRegisterForm'])->name('register');
    Route::get('/login', [OTPController::class, 'showLoginForm'])->name('login');
    Route::post('/generate', [OTPController::class, 'generateOtp'])->name('generate');
    Route::post('/verify', [OTPController::class, 'verifyOtp'])->name('verify');
    Route::post('/resend', [OTPController::class, 'resendOtp'])->name('resend');
    Route::post('/send', [OTPController::class, 'generateOtp'])->name('send');
    Route::post('/register', [OTPController::class, 'verifyOtp'])->name('register.post');
    Route::get('/check-status', [OTPController::class, 'checkStatus'])->name('check-status');
});

// ===================== GENERAL AUTH ROUTES =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ============================================================
// REPORTER ROUTES
// ============================================================
Route::prefix('reporter')->name('reporter.')->group(function () {
    Route::get('/login', [ReporterAuthController::class, 'showLogin'])->name('login');
    Route::post('/otp/generate', [ReporterAuthController::class, 'generateOtp'])->name('otp.generate');
    Route::post('/otp/verify', [ReporterAuthController::class, 'verifyOtp'])->name('otp.verify');
    Route::post('/otp/resend', [ReporterAuthController::class, 'resendOtp'])->name('otp.resend');
    Route::get('/register', [ReporterAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ReporterAuthController::class, 'register'])->name('register.post');
    Route::post('/logout', [ReporterAuthController::class, 'logout'])->name('logout');
});

Route::middleware(['auth', 'role:reporter'])->prefix('reporter')->name('reporter.')->group(function () {
    Route::get('/dashboard', [ReporterDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ReporterAuthController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [ReporterAuthController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('/news', [ReporterNewsController::class, 'index'])->name('news.index');
    Route::get('/news/create', [ReporterNewsController::class, 'create'])->name('news.create');
    Route::post('/news/store', [ReporterNewsController::class, 'store'])->name('news.store');
    Route::get('/news/{id}/edit', [ReporterNewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{id}', [ReporterNewsController::class, 'update'])->name('news.update');
    Route::get('/news/{id}', [ReporterNewsController::class, 'show'])->name('news.show');
    Route::delete('/news/{id}', [ReporterNewsController::class, 'destroy'])->name('news.destroy');
    Route::post('/news/{id}/resubmit', [ReporterNewsController::class, 'resubmit'])->name('news.resubmit');
    
    Route::get('/wallet', [ReporterWalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/withdraw', [ReporterWalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');
    
    Route::get('/notifications', [ReporterNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-read/{id}', [ReporterNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [ReporterNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    
    Route::post('/google/post', [ReporterNewsController::class, 'postToGoogle'])->name('reporter.google.post');
    Route::post('/setup/save', [ReporterAuthController::class, 'saveSetup'])->name('reporter.setup.save');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/dashboard/quick-stats', [AdminDashboardController::class, 'quickStats'])->name('dashboard.quick-stats');
        Route::get('/dashboard/location-stats', [AdminDashboardController::class, 'locationStats'])->name('dashboard.location-stats');
        
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminUserController::class, 'index'])->name('index');
            Route::get('/create', [AdminUserController::class, 'create'])->name('create');
            Route::post('/', [AdminUserController::class, 'store'])->name('store');
            Route::get('/{user}', [AdminUserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [AdminUserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [AdminUserController::class, 'update'])->name('update');
            Route::delete('/{user}', [AdminUserController::class, 'destroy'])->name('destroy');
            Route::post('/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('toggle-active');
            Route::post('/{user}/approve', [AdminUserController::class, 'approve'])->name('approve');
            Route::post('/{user}/reject', [AdminUserController::class, 'reject'])->name('reject');
            Route::post('/{user}/verify-reporter', [AdminUserController::class, 'verifyReporter'])->name('verify-reporter');
            Route::post('/{user}/toggle', [AdminUserController::class, 'toggle'])->name('toggle');
            Route::post('/{user}/verify', [AdminUserController::class, 'verify'])->name('verify');
            Route::post('/{user}/add-points', [AdminUserController::class, 'addPoints'])->name('add-points');
            Route::post('/{user}/deduct-points', [AdminUserController::class, 'deductPoints'])->name('deduct-points');
        });

        Route::prefix('news')->name('news.')->group(function () {
            Route::get('/', [AdminNewsController::class, 'index'])->name('index');
            Route::get('/create', [AdminNewsController::class, 'create'])->name('create');
            Route::post('/store', [AdminNewsController::class, 'store'])->name('store');
            Route::get('/{news}', [AdminNewsController::class, 'show'])->name('show');
            Route::get('/{news}/edit', [AdminNewsController::class, 'edit'])->name('edit');
            Route::put('/{news}', [AdminNewsController::class, 'update'])->name('update');
            Route::delete('/{news}', [AdminNewsController::class, 'destroy'])->name('destroy');
            Route::post('/{news}/approve', [AdminNewsController::class, 'approve'])->name('approve');
            Route::post('/{news}/reject', [AdminNewsController::class, 'reject'])->name('reject');
            Route::post('/{news}/breaking', [AdminNewsController::class, 'breaking'])->name('breaking');
            Route::post('/{news}/featured', [AdminNewsController::class, 'featured'])->name('featured');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [AdminCategoryController::class, 'index'])->name('index');
            Route::get('/create', [AdminCategoryController::class, 'create'])->name('create');
            Route::post('/', [AdminCategoryController::class, 'store'])->name('store');
            Route::get('/{category}/edit', [AdminCategoryController::class, 'edit'])->name('edit');
            Route::put('/{category}', [AdminCategoryController::class, 'update'])->name('update');
            Route::delete('/{category}', [AdminCategoryController::class, 'destroy'])->name('destroy');
            Route::post('/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('toggle');
        });

        Route::prefix('locations')->name('locations.')->group(function () {
            Route::get('/states', [AdminLocationController::class, 'states'])->name('states');
            Route::post('/states', [AdminLocationController::class, 'storeState'])->name('states.store');
            Route::put('/states/{state}', [AdminLocationController::class, 'updateState'])->name('states.update');
            Route::post('/states/{state}/toggle', [AdminLocationController::class, 'toggleState'])->name('states.toggle');
            Route::delete('/states/{state}', [AdminLocationController::class, 'destroyState'])->name('states.destroy');

            Route::get('/districts', [AdminLocationController::class, 'districts'])->name('districts');
            Route::post('/districts', [AdminLocationController::class, 'storeDistrict'])->name('districts.store');
            Route::put('/districts/{district}', [AdminLocationController::class, 'updateDistrict'])->name('districts.update');
            Route::post('/districts/{district}/toggle', [AdminLocationController::class, 'toggleDistrict'])->name('districts.toggle');
            Route::delete('/districts/{district}', [AdminLocationController::class, 'destroyDistrict'])->name('districts.destroy');

            Route::get('/tehsils', [AdminLocationController::class, 'tehsils'])->name('tehsils');
            Route::post('/tehsils', [AdminLocationController::class, 'storeTehsil'])->name('tehsils.store');
            Route::put('/tehsils/{tehsil}', [AdminLocationController::class, 'updateTehsil'])->name('tehsils.update');
            Route::post('/tehsils/{tehsil}/toggle', [AdminLocationController::class, 'toggleTehsil'])->name('tehsils.toggle');
            Route::delete('/tehsils/{tehsil}', [AdminLocationController::class, 'destroyTehsil'])->name('tehsils.destroy');

            Route::get('/blocks', [AdminLocationController::class, 'blocks'])->name('blocks');
            Route::post('/blocks', [AdminLocationController::class, 'storeBlock'])->name('blocks.store');
            Route::put('/blocks/{block}', [AdminLocationController::class, 'updateBlock'])->name('blocks.update');
            Route::post('/blocks/{block}/toggle', [AdminLocationController::class, 'toggleBlock'])->name('blocks.toggle');
            Route::delete('/blocks/{block}', [AdminLocationController::class, 'destroyBlock'])->name('blocks.destroy');
        });

        // ============================================================
        // ✅ ADMIN ADS ROUTES (FULL CRUD)
        // ============================================================
        Route::prefix('ads')->name('ads.')->group(function () {
            Route::get('/', [AdminAdController::class, 'index'])->name('index');
            Route::get('/create', [AdminAdController::class, 'create'])->name('create');
            Route::post('/', [AdminAdController::class, 'store'])->name('store');
            Route::get('/{ad}/edit', [AdminAdController::class, 'edit'])->name('edit');
            Route::put('/{ad}', [AdminAdController::class, 'update'])->name('update');
            Route::delete('/{ad}', [AdminAdController::class, 'destroy'])->name('destroy');
            Route::get('/{ad}/toggle', [AdminAdController::class, 'toggle'])->name('toggle');
            Route::get('/{ad}/toggle-status', [AdminAdController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/{ad}/toggle-status', [AdminAdController::class, 'toggleStatus']);
        });

        // ============================================================
        // ✅ ADMIN AJAX ROUTES FOR ADS LOCATION
        // ============================================================
        Route::prefix('api')->name('api.')->group(function () {
            Route::get('/districts/{stateId}', [AdminAdController::class, 'getDistricts'])->name('districts');
            Route::get('/tehsils/{districtId}', [AdminAdController::class, 'getTehsils'])->name('tehsils');
            Route::get('/blocks/{tehsilId}', [AdminAdController::class, 'getBlocks'])->name('blocks');
        });

        Route::prefix('reporter-assignments')->name('reporter-assignments.')->group(function () {
            Route::get('/', [ReporterAssignmentController::class, 'index'])->name('index');
            Route::get('/create', [ReporterAssignmentController::class, 'create'])->name('create');
            Route::post('/', [ReporterAssignmentController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ReporterAssignmentController::class, 'edit'])->name('edit');
            Route::put('/{id}', [ReporterAssignmentController::class, 'update'])->name('update');
            Route::post('/{id}/toggle', [ReporterAssignmentController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}', [ReporterAssignmentController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('fake-views')->name('fake-views.')->group(function () {
            Route::get('/', [FakeViewsController::class, 'index'])->name('index');
            Route::get('/report', [FakeViewsController::class, 'report'])->name('report');
            Route::post('/{id}/mark-real', [FakeViewsController::class, 'markReal'])->name('mark-real');
            Route::post('/{id}/mark-fake', [FakeViewsController::class, 'markFake'])->name('mark-fake');
        });

        Route::prefix('reporter-views')->name('reporter-views.')->group(function () {
            Route::get('/', [FakeViewsController::class, 'reporterAnalytics'])->name('index');
            Route::get('/{user}', [FakeViewsController::class, 'reporterDetail'])->name('detail');
        });

        Route::prefix('reporter-monetisation')->name('reporter-monetisation.')->group(function () {
            Route::get('/', [ReporterMonetisationController::class, 'index'])->name('index');
            Route::get('/update-all-stats', [ReporterMonetisationController::class, 'updateAllStats'])->name('update-all-stats');
            Route::get('/{user}', [ReporterMonetisationController::class, 'show'])->name('show');
            Route::post('/{user}/toggle', [ReporterMonetisationController::class, 'toggle'])->name('toggle');
            Route::post('/{user}/add-points', [ReporterMonetisationController::class, 'addPoints'])->name('add-points');
            Route::post('/{user}/deduct-points', [ReporterMonetisationController::class, 'deductPoints'])->name('deduct-points');
            Route::post('/{id}/update-stats', [ReporterMonetisationController::class, 'updateStats'])->name('update-stats');
        });

        Route::prefix('pages')->name('pages.')->group(function () {
            Route::get('/', [AdminPageController::class, 'index'])->name('index');
            Route::get('/{slug}/edit', [AdminPageController::class, 'edit'])->name('edit');
            Route::put('/{slug}', [AdminPageController::class, 'update'])->name('update');
        });

        Route::prefix('withdrawals')->name('withdrawals.')->group(function () {
            Route::get('/', [AdminWithdrawalController::class, 'index'])->name('index');
            Route::put('/{withdrawal}/process', [AdminWithdrawalController::class, 'process'])->name('process');
        });

        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/revenue', [AdminSettingsController::class, 'revenueSettings'])->name('revenue');
            Route::post('/revenue', [AdminSettingsController::class, 'updateRevenueSettings'])->name('revenue.update');
            Route::get('/site', [AdminSettingsController::class, 'siteSettings'])->name('site');
            Route::post('/site', [AdminSettingsController::class, 'updateSiteSettings'])->name('site.update');
            Route::get('/navigation', [AdminSettingsController::class, 'navigationMenu'])->name('navigation');
            Route::post('/navigation', [AdminSettingsController::class, 'storeNavigationMenu'])->name('navigation.store');
            Route::put('/navigation/{menu}', [AdminSettingsController::class, 'updateNavigationMenu'])->name('navigation.update');
            Route::delete('/navigation/{menu}', [AdminSettingsController::class, 'deleteNavigationMenu'])->name('navigation.delete');
        });

        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::get('/', [NewsletterController::class, 'index'])->name('index');
            Route::get('/toggle/{id}', [NewsletterController::class, 'toggle'])->name('toggle');
            Route::delete('/{id}', [NewsletterController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [AdminNotificationController::class, 'index'])->name('index');
            Route::post('/mark-read/{id}', [AdminNotificationController::class, 'markRead'])->name('mark-read');
            Route::post('/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('mark-all-read');
        });

        // ✅ Admin Broadcast Push Notification
        Route::post('/notifications/broadcast', [App\Http\Controllers\NotificationController::class, 'sendBroadcast'])
            ->name('notifications.broadcast');

        // ✅ Opinion Poll Management and Reports
        Route::get('/poll-results', [AdminPollController::class, 'index'])->name('poll.results');
        Route::get('/poll-results/export', [AdminPollController::class, 'export'])->name('poll.export');
        Route::post('/polls', [AdminPollController::class, 'store'])->name('poll.store');
        Route::put('/polls/{poll}', [AdminPollController::class, 'update'])->name('poll.update');
        Route::post('/polls/{poll}/toggle', [AdminPollController::class, 'toggle'])->name('poll.toggle');
        Route::delete('/polls/{poll}', [AdminPollController::class, 'destroy'])->name('poll.destroy');

        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');
    }); // <-- This closes the admin middleware group
}); // <-- This closes the entire admin prefix group

// ============================================================
// ✅ USER PROFILE EDIT ROUTES
// ============================================================
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/{id}/edit', [UserController::class, 'edit'])->name('edit');
    Route::put('/{id}', [UserController::class, 'update'])->name('update');
});

// ============================================================
// HYPERLOCAL ROUTES
// ============================================================
Route::middleware(['auth'])->prefix('hyperlocal')->name('hyperlocal.')->group(function () {
    Route::get('/', [HyperlocalNewsController::class, 'index'])->name('index');
    Route::get('/notifications', [HyperlocalNewsController::class, 'notifications'])->name('notifications');
    Route::get('/unread-count', [HyperlocalNewsController::class, 'getUnreadCount'])->name('unread-count');
    Route::post('/mark-read', [HyperlocalNewsController::class, 'markRead'])->name('mark-read');
});

// ============================================================
// AJAX API ROUTES (Location)
// ============================================================
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/get-districts/{state_id}', [LocationAPIController::class, 'getDistricts']);
    Route::get('/get-tehsils/{district_id}', [LocationAPIController::class, 'getTehsils']);
    Route::get('/get-blocks/{tehsil_id}', [LocationAPIController::class, 'getBlocks']);
    Route::get('/get-states', [LocationAPIController::class, 'getStates']);
});

// ============================================================
// ✅ FIREBASE PUSH NOTIFICATIONS (FCM) - GUEST + LOGGED-IN USERS
// ============================================================
Route::prefix('api/fcm')->group(function () {
    Route::post('/token', [PushSubscriptionController::class, 'saveToken'])->name('fcm.save-token');
    Route::delete('/token', [PushSubscriptionController::class, 'removeToken'])->name('fcm.remove-token');
});

// ============================================================
// ✅ USER NOTIFICATION ROUTES (Logged-in Users)
// ============================================================
Route::middleware(['auth'])->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::get('/unread-count', [NotificationController::class, 'unreadCount'])->name('unread-count');
    Route::post('/mark-read/{id}', [NotificationController::class, 'markRead'])->name('mark-read');
    Route::post('/mark-all-read', [NotificationController::class, 'markAllRead'])->name('mark-all-read');
    Route::delete('/{id}', [NotificationController::class, 'destroy'])->name('destroy');
    Route::delete('/delete-all-read', [NotificationController::class, 'deleteAllRead'])->name('delete-all-read');
    Route::get('/push-status', [NotificationController::class, 'pushStatus'])->name('push-status');
    Route::post('/test-push', [NotificationController::class, 'sendTestPush'])->name('test-push');
});

// ============================================================
// ✅ TEST PUSH NOTIFICATION ROUTE (for debugging)
// ============================================================
Route::get('/test-push', function () {
    $count = NotificationHelper::sendToAll(
        '🔔 Test Push Notification',
        'This is a test message from the server!',
        ['type' => 'test', 'sound' => 'default'],
        '/'
    );
    return "✅ Test notification sent to {$count} subscribers.";
});

// ============================================================
// ✅ AD CLICK ROUTE (Public)
// ============================================================
Route::get('/ads/click/{id}', function ($id) {
    try {
        $ad = \App\Models\Ad::findOrFail($id);
        $ad->incrementClicks();
        \Illuminate\Support\Facades\Cache::forget('ads_' . $ad->position . '_global');
        if ($ad->url) {
            return redirect()->away($ad->url, 301);
        }
        return redirect()->back();
    } catch (\Exception $e) {
        \Log::error('Ad click error: ' . $e->getMessage());
        return redirect()->back();
    }
})->name('ads.click');



// ============================================================
// ✅ DEBUG ADS ROUTE (for testing - Remove in production)
// ============================================================
Route::get('/debug-ads', function () {
    $ads = \App\Models\Ad::all();
    $result = [
        'total' => $ads->count(),
        'active' => \App\Models\Ad::active()->count(),
        'ads' => $ads->map(function($ad) {
            return [
                'id' => $ad->id,
                'title' => $ad->title,
                'type' => $ad->type,
                'position' => $ad->position,
                'status' => $ad->status,
                'image' => $ad->image,
                'image_url' => $ad->image ? asset('storage/' . $ad->image) : null,
                'image_exists' => $ad->image && file_exists(storage_path('app/public/' . $ad->image)),
                'is_visible' => $ad->is_visible,
                'url' => $ad->url,
            ];
        })
    ];
    return response()->json($result);
});

// ============================================================
// ✅ OPINION POLL ROUTES (Public)
// ============================================================
Route::prefix('poll')->name('poll.')->group(function () {
    // Existing simple poll (keep if used)
    Route::post('/vote', [PollController::class, 'vote'])->name('vote');

    // ✅ UP Election 2027 Opinion Poll Endpoints
    Route::get('/seats', [PollController::class, 'getSeats'])->name('seats');
    Route::get('/districts', [PollController::class, 'getDistricts'])->name('districts');
    Route::get('/seats/{district}', [PollController::class, 'getSeatsByDistrict'])->name('seats.by-district');
    Route::get('/questions/{seatId}', [PollController::class, 'getQuestions'])->name('questions');
    Route::post('/submit', [PollController::class, 'submitPoll'])->name('submit');
    Route::get('/results/{pollId}', [PollController::class, 'getResults'])->name('results');
});

// ============================================================
// PUBLIC ROUTES
// ============================================================
Route::prefix('problem')->name('problem.')->group(function () {
    Route::post('/store', [App\Http\Controllers\ProblemController::class, 'store'])->name('store');
});

Route::get('/news/local-search', [NewsPageController::class, 'localSearch'])->name('news.local-search');

// ============================================================
// HANDLE POST REQUESTS TO NEWS URLs (Prevent 405)
// ============================================================
Route::post('/news/{slug}', function ($slug) {
    return redirect()->route('news.show', $slug);
})->where('slug', '.*');

// ============================================================
// STORAGE LINK
// ============================================================
Route::get('/fix-storage-link', function () {
    if (file_exists(public_path('storage'))) {
        if (is_link(public_path('storage'))) {
            unlink(public_path('storage'));
        } else {
            rename(public_path('storage'), public_path('storage_old_' . time()));
        }
    }
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return "✅ Storage link fixed!";
});

// ============================================================
// FALLBACK ROUTE
// ============================================================
Route::fallback(function () {
    return redirect()->route('home');
});

// ============================================================
// NEWSLETTER PUBLIC ROUTE
// ============================================================
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// ============================================================
// DUMP AUTOLOAD (Temporary)
// ============================================================
Route::get('/dump-autoload', function() {
    exec('composer dump-autoload');
    return 'Autoload refreshed!';
});