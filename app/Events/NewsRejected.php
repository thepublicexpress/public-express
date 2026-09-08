<?php

namespace App\Events;

use App\Models\News;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsRejected
{
    use Dispatchable, SerializesModels;

    public $news;
    public $reason;

    public function __construct(News $news, $reason = null)
    {
        $this->news = $news;
        $this->reason = $reason;
    }
}