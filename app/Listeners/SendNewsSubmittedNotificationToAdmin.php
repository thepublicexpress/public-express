<?php

namespace App\Listeners;

use App\Events\NewsSubmitted;
use App\Models\Notification;
use App\Models\User;

class SendNewsSubmittedNotificationToAdmin
{
    public function handle(NewsSubmitted $event)
    {
        $news = $event->news;

        // ✅ सभी Admin Users को Notification भेजें
        $admins = User::whereIn('role', ['admin', 'super_admin', 'state_admin', 'district_admin', 'tehsil_admin'])->get();

        foreach ($admins as $admin) {
            Notification::create([
                'user_id' => $admin->id,
                'news_id' => $news->id,
                'type' => 'admin',
                'title' => '📰 नई खबर सबमिट हुई',
                'body' => "रिपोर्टर {$news->user->name} ने एक नई खबर सबमिट की है: " . \Str::limit($news->title, 60),
                'message' => "रिपोर्टर {$news->user->name} ने एक नई खबर सबमिट की है: " . \Str::limit($news->title, 60),
                'data' => json_encode(['news_id' => $news->id, 'url' => route('admin.news.show', $news->id)]),
                'is_read' => 0,
            ]);
        }
    }
}