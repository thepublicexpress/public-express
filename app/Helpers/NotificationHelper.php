<?php

namespace App\Helpers;

use App\Models\Notification;
use App\Models\User;

class NotificationHelper
{
    public static function send($type, $title, $message, $link = null)
    {
        $roleMap = ['admin' => 1, 'editor' => 2, 'reporter' => 4];
        $roleId = $roleMap[$type] ?? null;
        if (!$roleId) return;
        $users = User::where('role_id', $roleId)->get();

        foreach ($users as $user) {
            Notification::create([
                'user_id' => $user->id,
                'type' => $type,
                'title' => $title,
                'message' => $message,
                'link' => $link,
                'is_read' => false,
            ]);
        }
    }

    public static function sendToUser($userId, $title, $message, $link = null)
    {
        Notification::create([
            'user_id' => $userId,
            'type' => 'user',
            'title' => $title,
            'message' => $message,
            'link' => $link,
            'is_read' => false,
        ]);
    }

    public static function unreadCount($userId)
    {
        return Notification::where('user_id', $userId)
                          ->where('is_read', false)
                          ->count();
    }
}