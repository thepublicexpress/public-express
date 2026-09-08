<?php

namespace App\Listeners;

use App\Events\NewsApproved;
use App\Models\Notification;

class SendNewsApprovedNotificationToReporter
{
    public function handle(NewsApproved $event)
    {
        $news = $event->news;

        Notification::create([
            'user_id' => $news->user_id,
            'news_id' => $news->id,
            'type' => 'reporter',
            'title' => '✅ खबर स्वीकृत हो गई',
            'body' => "आपकी खबर \"{$news->title}\" स्वीकृत कर प्रकाशित कर दी गई है।",
            'message' => "आपकी खबर \"{$news->title}\" स्वीकृत कर प्रकाशित कर दी गई है।",
            'data' => json_encode(['news_id' => $news->id, 'url' => route('reporter.news.show', $news->id)]),
            'is_read' => 0,
        ]);
    }
}