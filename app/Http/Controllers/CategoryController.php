<?php

namespace App\Http\Controllers;

use App\Models\Category; // ✅ NewsCategory → Category
use App\Models\News;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        $category = Category::where('slug', $slug)->where('is_active', 1)->firstOrFail();

        $news = News::where('category_id', $category->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $trendingNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        $categories = Category::where('is_active', 1)->orderBy('order')->get();

        return view('category.show', compact('category', 'news', 'trendingNews', 'categories'));
    }
}