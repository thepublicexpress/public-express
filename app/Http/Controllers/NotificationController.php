<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\PushSubscription;
use App\Helpers\NotificationHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Display a listing of notifications.
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
                                    ->latest()
                                    ->paginate(20);
        
        // Mark all as read when viewing
        Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
        
        return view('notifications.index', compact('notifications'));
    }

    /**
     * Get unread count for badge.
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
        return response()->json(['count' => $count]);
    }

    /**
     * Mark a single notification as read.
     */
    public function markRead($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                                        ->where('id', $id)
                                        ->first();
            if ($notification) {
                $notification->markAsRead();
                return response()->json(['success' => true, 'message' => 'Notification marked as read']);
            }
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllRead()
    {
        try {
            Notification::where('user_id', Auth::id())
                        ->where('is_read', false)
                        ->update(['is_read' => true, 'read_at' => now()]);
            
            return redirect()->back()->with('success', '✅ सभी notifications पढ़ लिए गए');
        } catch (\Exception $e) {
            Log::error('Error marking all notifications as read: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Error marking notifications as read');
        }
    }

    /**
     * Delete a notification.
     */
    public function destroy($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                                        ->where('id', $id)
                                        ->first();
            if ($notification) {
                $notification->delete();
                return response()->json(['success' => true, 'message' => 'Notification deleted']);
            }
            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error deleting notification: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead()
    {
        try {
            Notification::where('user_id', Auth::id())
                        ->where('is_read', true)
                        ->delete();
            
            return redirect()->back()->with('success', '✅ सभी पढ़ी गई notifications हटा दी गईं');
        } catch (\Exception $e) {
            Log::error('Error deleting read notifications: ' . $e->getMessage());
            return redirect()->back()->with('error', '❌ Error deleting notifications');
        }
    }

    /**
     * Send test notification via push.
     */
    public function sendTestPush(Request $request)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not logged in'], 401);
            }

            $title = $request->input('title', '🔔 Test Push Notification');
            $body = $request->input('body', 'This is a test notification from the server!');
            
            // Send to current user
            $sent = NotificationHelper::sendToUser(
                $user,
                $title,
                $body,
                ['type' => 'test', 'sound' => 'default'],
                '/'
            );

            if ($sent && $sent > 0) {
                // Also save in database
                Notification::create([
                    'user_id' => $user->id,
                    'title' => $title,
                    'body' => $body,
                    'type' => 'test',
                    'data' => ['type' => 'test'],
                    'is_read' => false,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => "✅ Test notification sent to {$sent} devices",
                    'sent' => $sent
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No active push subscriptions found for this user'
            ], 404);

        } catch (\Exception $e) {
            Log::error('Error sending test push: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user's push subscription status.
     */
    public function pushStatus()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'User not logged in'], 401);
            }

            $subscriptions = PushSubscription::where('user_id', $user->id)
                                            ->where('is_active', true)
                                            ->get();

            return response()->json([
                'success' => true,
                'has_push' => $subscriptions->count() > 0,
                'devices' => $subscriptions->count(),
                'browsers' => $subscriptions->pluck('browser')->unique()->values(),
                'platforms' => $subscriptions->pluck('platform')->unique()->values(),
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting push status: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Send notification to all subscribers (Admin only).
     */
    public function sendBroadcast(Request $request)
    {
        try {
            // Check if user is admin
            if (!in_array(Auth::user()->role, ['admin', 'super_admin'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Only admins can send broadcast notifications'
                ], 403);
            }

            $request->validate([
                'title' => 'required|string|max:255',
                'body' => 'required|string|max:500',
                'click_action' => 'nullable|string',
            ]);

            $title = $request->input('title');
            $body = $request->input('body');
            $clickAction = $request->input('click_action', '/');
            
            $data = [
                'type' => 'broadcast',
                'sent_by' => Auth::user()->name,
                'timestamp' => now()->toISOString(),
                'sound' => 'default',
            ];

            // Send to ALL subscribers (guests + users)
            $sent = NotificationHelper::sendToAll($title, $body, $data, $clickAction);

            return response()->json([
                'success' => true,
                'message' => "✅ Broadcast notification sent to {$sent} subscribers",
                'sent' => $sent
            ]);

        } catch (\Exception $e) {
            Log::error('Error sending broadcast: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}