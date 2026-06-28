<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\State;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show($slug)
    {
        // Get category
        $category = NewsCategory::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        // Get news for this category
        $news = News::where('category_id', $category->id)
            ->where('status', 'published')
            ->latest('published_at')
            ->paginate(12);

        // ===== GET ALL CATEGORIES FOR MENU =====
        $categories = NewsCategory::where('is_active', true)
                                  ->whereIn('slug', ['national', 'politics', 'education', 'sports', 'entertainment'])
                                  ->orderBy('sort_order')
                                  ->get();

        // ===== GET STATES FOR MENU =====
        $states = State::where('is_active', true)
                       ->with(['districts' => function($query) {
                           $query->where('is_active', true);
                       }])
                       ->orderBy('name')
                       ->get();

        return view('category.show', compact('category', 'news', 'categories', 'states'));
    }
}