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
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\OTPController;
use App\Http\Controllers\HyperlocalNewsController;
use App\Http\Controllers\LeaderboardController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// ===================== EMERGENCY ROUTES =====================
Route::get('/clear-all-cache', function() {
    \Illuminate\Support\Facades\Cache::flush();
    \Illuminate\Support\Facades\View::clearResolved();
    \Illuminate\Support\Facades\Config::clear();

    $sessionPath = storage_path('framework/sessions');
    if (is_dir($sessionPath)) {
        $files = glob($sessionPath . '/*');
        foreach ($files as $file) {
            if (is_file($file)) unlink($file);
        }
    }

    return '✅ All cache cleared! <a href="/">Go Home</a>';
});

// ===================== IMAGE ROUTES =====================
Route::get('/serve-image/{path}', function($path) {
    $path  = urldecode($path);
    $paths = [
        storage_path('app/public/' . $path),
        storage_path('app/public/news/' . $path),
        storage_path('app/public/news/2026/03/' . basename($path)),
        public_path('storage/' . $path),
        public_path('storage/news/' . $path),
    ];

    foreach (array_unique($paths) as $fullPath) {
        $fullPath = str_replace(['//', '\\\\'], '/', $fullPath);
        if (file_exists($fullPath) && is_readable($fullPath)) {
            try {
                $mime = mime_content_type($fullPath) ?: 'image/jpeg';
                return response(file_get_contents($fullPath))
                    ->header('Content-Type', $mime)
                    ->header('Cache-Control', 'public, max-age=86400');
            } catch (\Exception $e) {}
        }
    }

    $defaultPath = public_path('images/default-news.jpg');
    if (file_exists($defaultPath)) {
        return response(file_get_contents($defaultPath))->header('Content-Type', 'image/jpeg');
    }

    abort(404, 'Image not found');
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
    Route::get('/{slug}', [NewsPageController::class, 'show'])->name('show');
});

Route::prefix('category')->name('category.')->group(function () {
    Route::get('/{slug}', [CategoryController::class, 'show'])->name('show');
});

Route::get('/leaderboard', [LeaderboardController::class, 'index'])->name('leaderboard');

Route::view('/about', 'pages.about')->name('about');
Route::view('/contact', 'pages.contact')->name('contact');
Route::view('/privacy', 'pages.privacy')->name('privacy');
Route::view('/terms', 'pages.terms')->name('terms');

// ===================== OTP ROUTES (100% FREE) =====================
Route::prefix('otp')->name('otp.')->group(function () {
    Route::get('/login', [OTPController::class, 'showOtpLogin'])->name('login');
    Route::post('/send', [OTPController::class, 'sendOTP'])->name('send');
    Route::post('/verify', [OTPController::class, 'verifyOTP'])->name('verify');
    Route::post('/resend', [OTPController::class, 'resendOTP'])->name('resend');
    Route::post('/register', [OTPController::class, 'register'])->name('register');
    Route::get('/check-status', [OTPController::class, 'checkStatus'])->name('check-status');
});

// ===================== GENERAL AUTH ROUTES =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.post');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.post');
    
    // ===== FORGET PASSWORD ROUTES =====
    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ===================== REPORTER ROUTES (Guest) =====================
Route::prefix('reporter')->name('reporter.')->group(function () {
    Route::get('/login', [ReporterAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [ReporterAuthController::class, 'login'])->name('login.post');
    Route::get('/register', [ReporterAuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [ReporterAuthController::class, 'register'])->name('register.post');
});

// ===================== REPORTER ROUTES (Protected) =====================
Route::middleware(['auth', 'role:reporter'])->prefix('reporter')->name('reporter.')->group(function () {
    Route::get('/dashboard', [ReporterDashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ReporterAuthController::class, 'showProfile'])->name('profile');
    Route::post('/profile', [ReporterAuthController::class, 'updateProfile'])->name('profile.update');
    
    Route::get('/news/create', [ReporterNewsController::class, 'create'])->name('news.create');
    Route::post('/news/store', [ReporterNewsController::class, 'store'])->name('news.store');
    Route::get('/news', [ReporterNewsController::class, 'index'])->name('news.index');
    Route::get('/news/{id}/edit', [ReporterNewsController::class, 'edit'])->name('news.edit');
    Route::put('/news/{id}', [ReporterNewsController::class, 'update'])->name('news.update');
    Route::get('/news/{id}', [ReporterNewsController::class, 'show'])->name('news.show');
    Route::delete('/news/{id}', [ReporterNewsController::class, 'destroy'])->name('news.destroy');
    
    Route::get('/wallet', [ReporterWalletController::class, 'index'])->name('wallet');
    Route::post('/wallet/withdraw', [ReporterWalletController::class, 'requestWithdrawal'])->name('wallet.withdraw');
    
    Route::get('/notifications', [ReporterNotificationController::class, 'index'])->name('notifications');
    Route::post('/notifications/mark-read/{id}', [ReporterNotificationController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [ReporterNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    
    Route::post('/logout', [ReporterAuthController::class, 'logout'])->name('logout');
});

// ===================== ADMIN ROUTES =====================
Route::prefix('admin')->name('admin.')->group(function () {

    // Guest routes - Admin Login
    Route::middleware('guest')->group(function () {
        Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    // Protected Admin Routes
    Route::middleware(['auth', 'role:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // ===== USERS MANAGEMENT =====
        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
        Route::get('/users/{user}', [AdminUserController::class, 'show'])->name('users.show');
        Route::get('/users/{user}/edit', [AdminUserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
        Route::post('/users/{user}/toggle-active', [AdminUserController::class, 'toggleActive'])->name('users.toggle-active');
        Route::post('/users/{user}/approve', [AdminUserController::class, 'approve'])->name('users.approve');
        Route::post('/users/{user}/reject', [AdminUserController::class, 'reject'])->name('users.reject');
        Route::post('/users/{user}/verify-reporter', [AdminUserController::class, 'verifyReporter'])->name('users.verify-reporter');
        Route::post('/users/{user}/add-points', [AdminUserController::class, 'addPoints'])->name('users.add-points');
        Route::post('/users/{user}/deduct-points', [AdminUserController::class, 'deductPoints'])->name('users.deduct-points');
        Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');

        // ===== NEWS MANAGEMENT =====
        Route::get('/news', [AdminNewsController::class, 'index'])->name('news.index');
        Route::get('/news/{news}', [AdminNewsController::class, 'show'])->name('news.show');
        Route::get('/news/{news}/edit', [AdminNewsController::class, 'edit'])->name('news.edit');
        Route::put('/news/{news}', [AdminNewsController::class, 'update'])->name('news.update');
        Route::post('/news/{news}/approve', [AdminNewsController::class, 'approve'])->name('news.approve');
        Route::post('/news/{news}/reject', [AdminNewsController::class, 'reject'])->name('news.reject');
        Route::post('/news/{news}/breaking', [AdminNewsController::class, 'breaking'])->name('news.breaking');
        Route::post('/news/{news}/featured', [AdminNewsController::class, 'featured'])->name('news.featured');
        Route::delete('/news/{news}', [AdminNewsController::class, 'destroy'])->name('news.destroy');

        // ===== CATEGORIES MANAGEMENT =====
        Route::get('/categories', [AdminCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [AdminCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [AdminCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [AdminCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [AdminCategoryController::class, 'update'])->name('categories.update');
        Route::post('/categories/{category}/toggle', [AdminCategoryController::class, 'toggle'])->name('categories.toggle');
        Route::delete('/categories/{category}', [AdminCategoryController::class, 'destroy'])->name('categories.destroy');

        // ===== LOCATIONS MANAGEMENT =====
        Route::prefix('locations')->name('locations.')->group(function () {
            // States
            Route::get('/states', [AdminLocationController::class, 'states'])->name('states');
            Route::post('/states', [AdminLocationController::class, 'storeState'])->name('states.store');
            Route::put('/states/{state}', [AdminLocationController::class, 'updateState'])->name('states.update');
            Route::post('/states/{state}/toggle', [AdminLocationController::class, 'toggleState'])->name('states.toggle');
            Route::delete('/states/{state}', [AdminLocationController::class, 'destroyState'])->name('states.destroy');

            // Districts
            Route::get('/districts', [AdminLocationController::class, 'districts'])->name('districts');
            Route::post('/districts', [AdminLocationController::class, 'storeDistrict'])->name('districts.store');
            Route::put('/districts/{district}', [AdminLocationController::class, 'updateDistrict'])->name('districts.update');
            Route::post('/districts/{district}/toggle', [AdminLocationController::class, 'toggleDistrict'])->name('districts.toggle');
            Route::delete('/districts/{district}', [AdminLocationController::class, 'destroyDistrict'])->name('districts.destroy');

            // Tehsils
            Route::get('/tehsils', [AdminLocationController::class, 'tehsils'])->name('tehsils');
            Route::post('/tehsils', [AdminLocationController::class, 'storeTehsil'])->name('tehsils.store');
            Route::put('/tehsils/{tehsil}', [AdminLocationController::class, 'updateTehsil'])->name('tehsils.update');
            Route::post('/tehsils/{tehsil}/toggle', [AdminLocationController::class, 'toggleTehsil'])->name('tehsils.toggle');
            Route::delete('/tehsils/{tehsil}', [AdminLocationController::class, 'destroyTehsil'])->name('tehsils.destroy');

            // Blocks
            Route::get('/blocks', [AdminLocationController::class, 'blocks'])->name('blocks');
            Route::post('/blocks', [AdminLocationController::class, 'storeBlock'])->name('blocks.store');
            Route::put('/blocks/{block}', [AdminLocationController::class, 'updateBlock'])->name('blocks.update');
            Route::post('/blocks/{block}/toggle', [AdminLocationController::class, 'toggleBlock'])->name('blocks.toggle');
            Route::delete('/blocks/{block}', [AdminLocationController::class, 'destroyBlock'])->name('blocks.destroy');
        });

        // ===== REPORTER ASSIGNMENTS =====
        Route::prefix('reporter-assignments')->name('reporter-assignments.')->group(function () {
            Route::get('/', [ReporterAssignmentController::class, 'index'])->name('index');
            Route::get('/create', [ReporterAssignmentController::class, 'create'])->name('create');
            Route::post('/', [ReporterAssignmentController::class, 'store'])->name('store');
            Route::get('/{assignment}/edit', [ReporterAssignmentController::class, 'edit'])->name('edit');
            Route::put('/{assignment}', [ReporterAssignmentController::class, 'update'])->name('update');
            Route::post('/{assignment}/toggle', [ReporterAssignmentController::class, 'toggle'])->name('toggle');
            Route::delete('/{assignment}', [ReporterAssignmentController::class, 'destroy'])->name('destroy');
        });

        // ===== WITHDRAWALS =====
        Route::get('/withdrawals', [AdminWithdrawalController::class, 'index'])->name('withdrawals.index');
        Route::post('/withdrawals/{withdrawal}/process', [AdminWithdrawalController::class, 'process'])->name('withdrawals.process');

        // ===== REPORTER MONETISATION =====
        Route::prefix('reporter-monetisation')->name('reporter-monetisation.')->group(function () {
            Route::get('/', [ReporterMonetisationController::class, 'index'])->name('index');
            Route::get('/{user}', [ReporterMonetisationController::class, 'show'])->name('show');
            Route::post('/{user}/toggle', [ReporterMonetisationController::class, 'toggle'])->name('toggle');
            Route::post('/{user}/add-points', [ReporterMonetisationController::class, 'addPoints'])->name('add-points');
            Route::post('/{user}/deduct-points', [ReporterMonetisationController::class, 'deductPoints'])->name('deduct-points');
            Route::post('/update-all-stats', [ReporterMonetisationController::class, 'updateAllStats'])->name('update-all-stats');
            Route::post('/{id}/update-stats', [ReporterMonetisationController::class, 'updateStats'])->name('update-stats');
        });

        // ===== FAKE VIEWS TRACKING =====
        Route::prefix('fake-views')->name('fake-views.')->group(function () {
            Route::get('/', [FakeViewsController::class, 'index'])->name('index');
            Route::get('/report', [FakeViewsController::class, 'report'])->name('report');
            Route::post('/{id}/mark-real', [FakeViewsController::class, 'markReal'])->name('mark-real');
            Route::post('/{id}/mark-fake', [FakeViewsController::class, 'markFake'])->name('mark-fake');
        });

        // ===== REPORTER VIEWS ANALYTICS =====
        Route::prefix('reporter-views')->name('reporter-views.')->group(function () {
            Route::get('/', [FakeViewsController::class, 'reporterAnalytics'])->name('index');
            Route::get('/{user}', [FakeViewsController::class, 'reporterDetail'])->name('detail');
        });

        // ===== SETTINGS =====
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

        // Admin Logout
        Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');
    });
});

// ===================== HYPERLOCAL ROUTES =====================
Route::middleware(['auth'])->prefix('hyperlocal')->name('hyperlocal.')->group(function () {
    Route::get('/', [HyperlocalNewsController::class, 'index'])->name('index');
    Route::get('/notifications', [HyperlocalNewsController::class, 'notifications'])->name('notifications');
    Route::get('/unread-count', [HyperlocalNewsController::class, 'getUnreadCount'])->name('unread-count');
    Route::post('/mark-read', [HyperlocalNewsController::class, 'markRead'])->name('mark-read');
});

// ===================== AJAX API ROUTES =====================
Route::prefix('api')->name('api.')->group(function () {
    // Location APIs
    Route::get('/get-districts/{state_id}', [LocationAPIController::class, 'getDistricts']);
    Route::get('/get-tehsils/{district_id}', [LocationAPIController::class, 'getTehsils']);
    Route::get('/get-blocks/{tehsil_id}', [LocationAPIController::class, 'getBlocks']);
    Route::get('/get-states', [LocationAPIController::class, 'getStates']);
    Route::get('/get-reporters-by-location', [LocationAPIController::class, 'getReportersByLocation']);
});

// ===================== ADMIN AJAX API ROUTES =====================
Route::prefix('admin/api')->name('admin.api.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/districts/{state_id}', [ReporterAssignmentController::class, 'getDistricts']);
    Route::get('/tehsils/{district_id}', [ReporterAssignmentController::class, 'getTehsils']);
    Route::get('/blocks/{tehsil_id}', [ReporterAssignmentController::class, 'getBlocks']);
});

// ===================== STORAGE LINK FIX ROUTES =====================
Route::get('/fix-storage-link', function () {
    if (file_exists(public_path('storage'))) {
        if (is_link(public_path('storage'))) {
            unlink(public_path('storage'));
        } else {
            rename(public_path('storage'), public_path('storage_old_' . time()));
        }
    }
    \Illuminate\Support\Facades\Artisan::call('storage:link');
    return "✅ स्टोरेज लिंक सफलतापूर्वक सही हो गया है!";
});

// ===================== FALLBACK ROUTE =====================
Route::fallback(function () {
    return redirect()->route('home');
});