<?php

namespace App\Helpers;

use App\Models\PushSubscription;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Log;

class NotificationHelper
{
    /**
     * ✅ Clean UTF-8 string helper
     */
    private static function cleanUtf8String($text)
    {
        if (empty($text)) {
            return '';
        }
        
        // Force UTF-8 encoding and remove invalid characters
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        $text = preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $text);
        $text = preg_replace('/[^\x{0009}\x{000A}\x{000D}\x{0020}-\x{D7FF}\x{E000}-\x{FFFD}]+/u', '', $text);
        $text = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $text);
        
        return trim($text);
    }

    /**
     * Send notification to ALL active subscribers (Guests + Users)
     */
    public static function sendToAll($title, $body, $data = [], $clickAction = null)
    {
        try {
            // ✅ Clean title and body
            $cleanTitle = self::cleanUtf8String($title);
            $cleanBody = self::cleanUtf8String($body);
            
            // ✅ Fallback if empty
            if (empty($cleanTitle)) {
                $cleanTitle = 'द पब्लिक एक्सप्रेस';
            }
            if (empty($cleanBody)) {
                $cleanBody = 'ताजा खबर!';
            }

            // Get ALL active subscriptions
            $subscriptions = PushSubscription::where('is_active', true)->get();
            
            Log::info('📊 Subscriber check', [
                'total' => $subscriptions->count(),
                'query' => 'PushSubscription::where(is_active, true)->get()'
            ]);
            
            if ($subscriptions->isEmpty()) {
                Log::info('ℹ️ No active push subscriptions found');
                return 0;
            }

            // Extract tokens
            $tokens = $subscriptions->pluck('fcm_token')->filter()->toArray();
            
            Log::info('📊 Tokens extracted', [
                'count' => count($tokens),
                'sample' => !empty($tokens) ? substr($tokens[0], 0, 20) . '...' : 'none'
            ]);
            
            if (empty($tokens)) {
                Log::info('ℹ️ No valid tokens found');
                return 0;
            }

            Log::info("📢 Sending push to " . count($tokens) . " subscribers");

            // ✅ Convert all data values to strings and clean them
            $stringData = [];
            foreach ($data as $key => $value) {
                $cleanValue = self::cleanUtf8String((string) $value);
                $stringData[$key] = $cleanValue;
            }

            // Clean click action
            $cleanClickAction = self::cleanUtf8String($clickAction);

            // Send notification
            $firebase = new FirebaseService();
            $results = $firebase->sendToMultiple($tokens, $cleanTitle, $cleanBody, $stringData, $cleanClickAction);
            
            // Count successful sends
            $sent = 0;
            foreach ($results as $result) {
                if ($result !== null) {
                    $sent++;
                }
            }
            
            Log::info("✅ Push notification sent to {$sent} subscribers");
            return $sent;

        } catch (\Exception $e) {
            Log::error('❌ Bulk notification error: ' . $e->getMessage());
            Log::error('❌ Stack trace: ' . $e->getTraceAsString());
            return 0;
        }
    }

    /**
     * Send to specific user
     */
    public static function sendToUser($user, $title, $body, $data = [], $clickAction = null)
    {
        if (!$user) {
            Log::warning('⚠️ sendToUser: User is null');
            return null;
        }

        // ✅ Clean title and body
        $cleanTitle = self::cleanUtf8String($title);
        $cleanBody = self::cleanUtf8String($body);
        
        if (empty($cleanTitle)) {
            $cleanTitle = 'द पब्लिक एक्सप्रेस';
        }
        if (empty($cleanBody)) {
            $cleanBody = 'ताजा खबर!';
        }

        $subscriptions = PushSubscription::where('user_id', $user->id)
            ->where('is_active', true)
            ->get();

        Log::info("📊 User {$user->id} subscriptions", [
            'count' => $subscriptions->count()
        ]);

        if ($subscriptions->isEmpty()) {
            Log::info("ℹ️ No active subscriptions for user {$user->id}");
            return null;
        }

        $tokens = $subscriptions->pluck('fcm_token')->filter()->toArray();

        try {
            $stringData = [];
            foreach ($data as $key => $value) {
                $stringData[$key] = self::cleanUtf8String((string) $value);
            }

            $firebase = new FirebaseService();
            $results = $firebase->sendToMultiple($tokens, $cleanTitle, $cleanBody, $stringData, $clickAction);
            $sent = count(array_filter($results));
            Log::info("✅ Sent to {$sent} devices for user {$user->id}");
            return $sent;
        } catch (\Exception $e) {
            Log::error("❌ Notification error for user {$user->id}: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Send to users by role
     */
    public static function sendToRole($role, $title, $body, $data = [], $clickAction = null)
    {
        $cleanTitle = self::cleanUtf8String($title);
        $cleanBody = self::cleanUtf8String($body);
        
        if (empty($cleanTitle)) {
            $cleanTitle = 'द पब्लिक एक्सप्रेस';
        }
        if (empty($cleanBody)) {
            $cleanBody = 'ताजा खबर!';
        }

        $subscriptions = PushSubscription::whereHas('user', function ($q) use ($role) {
            $q->where('role', $role);
        })->where('is_active', true)->get();

        $tokens = $subscriptions->pluck('fcm_token')->filter()->toArray();

        if (empty($tokens)) return 0;

        try {
            $stringData = [];
            foreach ($data as $key => $value) {
                $stringData[$key] = self::cleanUtf8String((string) $value);
            }

            $firebase = new FirebaseService();
            $results = $firebase->sendToMultiple($tokens, $cleanTitle, $cleanBody, $stringData, $clickAction);
            return count(array_filter($results));
        } catch (\Exception $e) {
            Log::error("❌ Role notification error for {$role}: " . $e->getMessage());
            return 0;
        }
    }
}