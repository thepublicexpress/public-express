<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\HyperlocalNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HyperlocalNewsController extends Controller
{
    /**
     * Display hyperlocal news based on user location
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $radius = $request->radius ?? 5;
        
        // Get location from request or user
        $lat = $request->latitude;
        $lng = $request->longitude;
        
        if (!$lat || !$lng) {
            $location = $user ? $user->getLocation() : null;
            if ($location) {
                $lat = $location['latitude'];
                $lng = $location['longitude'];
            }
        }
        
        if ($lat && $lng && $user) {
            // Update user location
            $user->updateLocation($lat, $lng);
        }
        
        // Get nearby news (within radius)
        $nearbyNews = $this->getNearbyNews($lat, $lng, $radius);
        
        // Get other news (beyond radius)
        $otherNews = $this->getOtherNews($lat, $lng, $radius);
        
        // Send notifications for new nearby news
        if ($user && $nearbyNews->count() > 0) {
            $this->sendNotifications($user, $nearbyNews);
        }
        
        // Get unread count
        $unreadCount = $user ? $user->getUnreadHyperlocalCount() : 0;
        
        return view('hyperlocal.index', compact('nearbyNews', 'otherNews', 'radius', 'lat', 'lng', 'unreadCount'));
    }

    /**
     * Get news within radius
     */
    private function getNearbyNews($lat, $lng, $radius = 5)
    {
        if (!$lat || !$lng) {
            return News::where('status', 'published')
                ->latest('published_at')
                ->limit(10)
                ->get();
        }
        
        return News::where('status', 'published')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->selectRaw(
                "*, ( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) AS distance",
                [$lat, $lng, $lat]
            )
            ->having('distance', '<=', $radius)
            ->orderBy('distance')
            ->limit(20)
            ->get();
    }

    /**
     * Get news beyond radius
     */
    private function getOtherNews($lat, $lng, $radius = 5)
    {
        if (!$lat || !$lng) {
            return News::where('status', 'published')
                ->latest('published_at')
                ->paginate(20);
        }
        
        return News::where('status', 'published')
            ->where(function($q) use ($lat, $lng, $radius) {
                $q->whereNull('latitude')
                  ->orWhereNull('longitude')
                  ->orWhereRaw(
                      "( 6371 * acos( cos( radians(?) ) * cos( radians( latitude ) ) * cos( radians( longitude ) - radians(?) ) + sin( radians(?) ) * sin( radians( latitude ) ) ) ) > ?",
                      [$lat, $lng, $lat, $radius]
                  );
            })
            ->latest('published_at')
            ->paginate(20);
    }

    /**
     * Send notifications to nearby users
     */
    private function sendNotifications($user, $news)
    {
        $location = $user->getLocation();
        if (!$location) return;
        
        $lat = $location['latitude'];
        $lng = $location['longitude'];
        
        foreach ($news as $item) {
            $distance = $item->getDistanceFrom($lat, $lng);
            
            // Check if notification already sent
            $exists = HyperlocalNotification::where('news_id', $item->id)
                ->where('user_id', $user->id)
                ->exists();
            
            if (!$exists) {
                HyperlocalNotification::create([
                    'news_id' => $item->id,
                    'user_id' => $user->id,
                    'distance' => $distance,
                    'is_read' => false,
                    'sent_at' => now(),
                ]);
            }
        }
    }

    /**
     * Get unread notifications count (AJAX)
     */
    public function getUnreadCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $count = HyperlocalNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * Mark notifications as read (AJAX)
     */
    public function markRead(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized']);
        }
        
        $ids = $request->ids ?? [];
        
        if (empty($ids)) {
            // Mark all as read
            HyperlocalNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } else {
            // Mark specific notifications as read
            HyperlocalNotification::where('user_id', $user->id)
                ->whereIn('id', $ids)
                ->update(['is_read' => true]);
        }
        
        $remaining = HyperlocalNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
        
        return response()->json([
            'success' => true,
            'remaining' => $remaining,
            'message' => 'Notifications marked as read'
        ]);
    }

    /**
     * Get all notifications for user
     */
    public function notifications()
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to view notifications.');
        }
        
        $notifications = HyperlocalNotification::where('user_id', $user->id)
            ->with('news')
            ->orderBy('sent_at', 'desc')
            ->paginate(20);
        
        // Mark all as read when viewing
        if ($request->mark_read ?? true) {
            HyperlocalNotification::where('user_id', $user->id)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }
            
        return view('hyperlocal.notifications', compact('notifications'));
    }

    /**
     * Get notification count for navbar (AJAX)
     */
    public function getNotificationCount()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['count' => 0]);
        }
        
        $count = HyperlocalNotification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();
            
        return response()->json(['count' => $count]);
    }

    /**
     * Get nearby users for a specific news
     */
    public function getNearbyUsers($newsId)
    {
        $news = News::findOrFail($newsId);
        $users = $news->getNearbyUsers(5);
        
        return response()->json([
            'news' => $news->title,
            'nearby_users_count' => $users->count(),
            'users' => $users->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'distance' => number_format($user->distance ?? 0, 2) . ' km'
                ];
            })
        ]);
    }
}