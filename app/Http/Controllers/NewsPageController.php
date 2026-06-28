<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\District;
use App\Models\State;
use App\Models\Tehsil;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NewsPageController extends Controller
{
    // Category page
    public function category($slug)
    {
        $category = NewsCategory::where('slug', $slug)->firstOrFail();
        
        $news = News::where('category_id', $category->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        
        return view('news.category', compact('news', 'category'));
    }

    // District page
    public function district($slug)
    {
        $district = District::where('slug', $slug)->firstOrFail();
        
        $news = News::where('district_id', $district->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        
        return view('news.district', compact('news', 'district'));
    }

    // State page
    public function state($slug)
    {
        $state = State::where('slug', $slug)->firstOrFail();
        
        $districtIds = District::where('state_id', $state->id)->pluck('id');
        
        $news = News::whereIn('district_id', $districtIds)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        
        return view('news.state', compact('news', 'state'));
    }

    // Tehsil page
    public function tehsil($slug)
    {
        $tehsil = Tehsil::where('slug', $slug)->firstOrFail();
        
        $news = News::where('tehsil_id', $tehsil->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        
        return view('news.tehsil', compact('news', 'tehsil'));
    }

    // Block page
    public function block($slug)
    {
        $block = Block::where('slug', $slug)->firstOrFail();
        
        $news = News::where('block_id', $block->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        
        return view('news.block', compact('news', 'block'));
    }

    // Local Search
    public function localSearch(Request $request)
    {
        $query = News::where('status', 'published');
        $searchTitle = "लोकल खबरें";

        // 1. If block selected
        if ($request->filled('block')) {
            $block = Block::find($request->block);
            if ($block) {
                $query->where('block_id', $block->id);
                $searchTitle = "ब्लॉक: " . $block->name;
            }
        } 
        // 2. If tehsil selected
        elseif ($request->filled('tehsil')) {
            $tehsil = Tehsil::find($request->tehsil);
            if ($tehsil) {
                $query->where('tehsil_id', $tehsil->id);
                $searchTitle = "तहसील: " . $tehsil->name;
            }
        } 
        // 3. If district selected
        elseif ($request->filled('district')) {
            $district = District::find($request->district);
            if ($district) {
                $query->where('district_id', $district->id);
                $searchTitle = "जिला: " . $district->name;
            }
        } 
        // 4. If state selected
        elseif ($request->filled('state')) {
            $state = State::find($request->state);
            if ($state) {
                $districtIds = District::where('state_id', $state->id)->pluck('id');
                $query->whereIn('district_id', $districtIds);
                $searchTitle = "राज्य: " . $state->name;
            }
        }

        $news = $query->latest('published_at')->paginate(12);

        return view('news.search', compact('news', 'searchTitle'));
    }

    // Search (Normal Text Search)
    public function search(Request $request)
    {
        $query = $request->input('q');
        $searchTitle = "खोज परिणाम: " . $query;
        
        $news = News::where('status', 'published')
                    ->where(function($q) use ($query) {
                        $q->where('title', 'LIKE', "%{$query}%")
                          ->orWhere('summary', 'LIKE', "%{$query}%")
                          ->orWhere('body', 'LIKE', "%{$query}%");
                    })
                    ->latest('published_at')
                    ->paginate(15);
        
        return view('news.search', compact('news', 'query', 'searchTitle'));
    }

    // Single News
    public function show($slug)
    {
        $news = News::where('slug', $slug)
                    ->where('status', 'published')
                    ->firstOrFail();
        
        $news->increment('views');
        
        $relatedNews = News::where('category_id', $news->category_id)
                           ->where('id', '!=', $news->id)
                           ->where('status', 'published')
                           ->latest('published_at')
                           ->take(5)
                           ->get();
        
        return view('news.show', compact('news', 'relatedNews'));
    }

    // Index / All News
    public function index()
    {
        $news = News::where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        
        return view('news.index', compact('news'));
    }

    // Sitemap
    public function sitemap()
    {
        $news = News::where('status', 'published')->latest()->get();
        $categories = NewsCategory::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $states = State::where('is_active', true)->get();
        
        return response()->view('sitemap', compact('news', 'categories', 'districts', 'states'))
                         ->header('Content-Type', 'text/xml');
    }
}