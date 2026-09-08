<?php

namespace App\Events;

use App\Models\News;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewsSubmitted
{
    use Dispatchable, SerializesModels;

    public $news;

    public function __construct(News $news)
    {
        $this->news = $news;
    }
}