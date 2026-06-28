<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\OtpAuthController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\UserController;

// Auth
Route::post('/auth/send-otp',   [OtpAuthController::class, 'sendOtp']);
Route::post('/auth/verify-otp', [OtpAuthController::class, 'verifyOtp']);

// Public
Route::get('/news',        [NewsController::class, 'feed']);
Route::get('/news/{news}', [NewsController::class, 'show']);
Route::post('/news/{news}/like', [NewsController::class, 'like']);
Route::get('/leaderboard', [UserController::class, 'leaderboard']);

// Authenticated
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [OtpAuthController::class, 'logout']);
    Route::get('/profile',    [UserController::class, 'profile']);
    Route::post('/profile',   [UserController::class, 'updateProfile']);
    Route::get('/wallet',     [UserController::class, 'wallet']);
    Route::post('/withdraw',  [UserController::class, 'requestWithdrawal']);
    Route::post('/news',      [NewsController::class, 'store']);
    Route::get('/my-news',    [NewsController::class, 'myNews']);
});
