<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
                                    ->latest()
                                    ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
                            ->where('is_read', false)
                            ->count();
        return response()->json(['count' => $count]);
    }

    public function markRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
                                    ->where('id', $id)
                                    ->first();
        if ($notification) {
            $notification->markAsRead();
        }
        return back();
    }

    public function markAllRead()
    {
        Notification::where('user_id', Auth::id())
                    ->where('is_read', false)
                    ->update(['is_read' => true, 'read_at' => now()]);
        return back()->with('success', 'सभी notifications पढ़ लिए गए');
    }
}