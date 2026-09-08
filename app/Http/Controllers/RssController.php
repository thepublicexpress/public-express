<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class RssController extends Controller
{
    public function index()
    {
        // ✅ सबसे ताज़ा 20 प्रकाशित खबरें
        $news = News::where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->limit(20)
                    ->get();

        // ✅ RSS XML Headers
        return response()
            ->view('rss', compact('news'))
            ->header('Content-Type', 'application/xml');
    }
}