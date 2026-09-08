<?php

namespace App\Http\Controllers;

use App\Models\PushSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PushSubscriptionController extends Controller
{
    /**
     * Save or update FCM token for the current user/guest
     */
    public function saveToken(Request $request)
    {
        try {
            $request->validate(['fcm_token' => 'required|string']);

            $userId = Auth::id();

            // Find existing subscription for this token
            $subscription = PushSubscription::where('fcm_token', $request->fcm_token)->first();

            if ($subscription) {
                // Update existing
                $subscription->update([
                    'user_id' => $userId,
                    'browser' => $this->getBrowser($request->userAgent()),
                    'platform' => $this->getPlatform($request->userAgent()),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'is_active' => true,
                    'updated_at' => now(),
                ]);
            } else {
                // Create new
                $subscription = PushSubscription::create([
                    'user_id' => $userId,
                    'fcm_token' => $request->fcm_token,
                    'browser' => $this->getBrowser($request->userAgent()),
                    'platform' => $this->getPlatform($request->userAgent()),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'is_active' => true,
                ]);
            }

            Log::info('✅ FCM token saved', [
                'user_id' => $userId ?? 'guest',
                'token' => substr($request->fcm_token, 0, 20) . '...',
                'browser' => $subscription->browser,
                'platform' => $subscription->platform,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Token saved successfully',
                'subscription' => $subscription,
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Token save error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove/Deactivate a token
     */
    public function removeToken(Request $request)
    {
        try {
            $request->validate(['fcm_token' => 'required|string']);

            $subscription = PushSubscription::where('fcm_token', $request->fcm_token)->first();
            if ($subscription) {
                $subscription->update(['is_active' => false]);
                Log::info('✅ FCM token deactivated', [
                    'user_id' => $subscription->user_id ?? 'guest',
                    'token' => substr($request->fcm_token, 0, 20) . '...'
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Token deactivated successfully',
            ]);

        } catch (\Exception $e) {
            Log::error('❌ Token removal error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function getBrowser($userAgent)
    {
        if (strpos($userAgent, 'Chrome') !== false) return 'Chrome';
        if (strpos($userAgent, 'Firefox') !== false) return 'Firefox';
        if (strpos($userAgent, 'Safari') !== false) return 'Safari';
        if (strpos($userAgent, 'Edge') !== false) return 'Edge';
        return 'Other';
    }

    private function getPlatform($userAgent)
    {
        if (strpos($userAgent, 'Windows') !== false) return 'Windows';
        if (strpos($userAgent, 'Mac') !== false) return 'Mac';
        if (strpos($userAgent, 'Linux') !== false) return 'Linux';
        if (strpos($userAgent, 'Android') !== false) return 'Android';
        if (strpos($userAgent, 'iPhone') !== false) return 'iOS';
        return 'Other';
    }
}