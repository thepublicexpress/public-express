<?php

namespace App\Listeners;

use App\Events\NewsRejected;
use App\Models\Notification;

class SendNewsRejectedNotificationToReporter
{
    public function handle(NewsRejected $event)
    {
        $news = $event->news;
        $reason = $event->reason ?? 'कोई कारण नहीं दिया गया।';

        Notification::create([
            'user_id' => $news->user_id,
            'news_id' => $news->id,
            'type' => 'reporter',
            'title' => '❌ खबर अस्वीकृत कर दी गई',
            'body' => "आपकी खबर \"{$news->title}\" अस्वीकृत कर दी गई है।\nकारण: {$reason}",
            'message' => "आपकी खबर \"{$news->title}\" अस्वीकृत कर दी गई है।\nकारण: {$reason}",
            'data' => json_encode(['news_id' => $news->id, 'url' => route('reporter.news.show', $news->id)]),
            'is_read' => 0,
        ]);
    }
}