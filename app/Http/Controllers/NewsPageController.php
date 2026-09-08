<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\Category;
use App\Models\District;
use App\Models\State;
use App\Models\Tehsil;
use App\Models\Block;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsPageController extends Controller
{
    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $news = News::where('category_id', $category->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        return view('news.category', compact('news', 'category'));
    }

    public function district($slug)
    {
        $district = District::where('slug', $slug)->firstOrFail();
        $news = News::where('district_id', $district->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        return view('news.district', compact('news', 'district'));
    }

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

    public function tehsil($slug)
    {
        $tehsil = Tehsil::where('slug', $slug)->firstOrFail();
        $news = News::where('tehsil_id', $tehsil->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        return view('news.tehsil', compact('news', 'tehsil'));
    }

    public function block($slug)
    {
        $block = Block::where('slug', $slug)->firstOrFail();
        $news = News::where('block_id', $block->id)
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        return view('news.block', compact('news', 'block'));
    }

    public function localSearch(Request $request)
    {
        $query = News::where('status', 'published');
        $searchTitle = "लोकल खबरें";

        if ($request->filled('block')) {
            $block = Block::find($request->block);
            if ($block) {
                $query->where('block_id', $block->id);
                $searchTitle = "ब्लॉक: " . $block->name;
            }
        } elseif ($request->filled('tehsil')) {
            $tehsil = Tehsil::find($request->tehsil);
            if ($tehsil) {
                $query->where('tehsil_id', $tehsil->id);
                $searchTitle = "तहसील: " . $tehsil->name;
            }
        } elseif ($request->filled('district')) {
            $district = District::find($request->district);
            if ($district) {
                $query->where('district_id', $district->id);
                $searchTitle = "जिला: " . $district->name;
            }
        } elseif ($request->filled('state')) {
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

    // ============================================================
    // ✅ SINGLE NEWS – SIMPLE AND WORKING
    // ============================================================
    public function show($slug)
    {
        try {
            $news = News::with([
                'user', 'category', 'state', 'district', 'tehsil', 'block', 'approver'
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

            $news->increment('views');

            $relatedNews = News::with(['category', 'user'])
                ->where('category_id', $news->category_id)
                ->where('id', '!=', $news->id)
                ->where('status', 'published')
                ->latest('published_at')
                ->take(2)
                ->get();

            $latestNews = News::where('status', 'published')
                ->where('id', '!=', $news->id)
                ->orderBy('created_at', 'desc')
                ->limit(6)
                ->get();

            $moreFromCategory = News::where('category_id', $news->category_id)
                ->where('id', '!=', $news->id)
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get();

            $moreFromLocation = News::where('status', 'published')
                ->where('id', '!=', $news->id)
                ->where(function ($query) use ($news) {
                    if ($news->block_id) {
                        $query->orWhere('block_id', $news->block_id);
                    }
                    if ($news->tehsil_id) {
                        $query->orWhere('tehsil_id', $news->tehsil_id);
                    }
                    if ($news->district_id) {
                        $query->orWhere('district_id', $news->district_id);
                    }
                    if ($news->state_id) {
                        $query->orWhere('state_id', $news->state_id);
                    }
                })
                ->orderBy('published_at', 'desc')
                ->limit(4)
                ->get();

            $trendingNews = News::where('status', 'published')
                ->where('id', '!=', $news->id)
                ->orderBy('views', 'desc')
                ->limit(6)
                ->get();

            $seoMeta = [
                'description' => $news->summary ?? strip_tags(substr($news->body ?? '', 0, 160)),
                'og_title' => $news->title,
                'og_description' => $news->summary ?? strip_tags(substr($news->body ?? '', 0, 160)),
            ];

            return view('news.show', compact(
                'news',
                'relatedNews',
                'latestNews',
                'trendingNews',
                'moreFromCategory',
                'moreFromLocation',
                'seoMeta'
            ));

        } catch (\Exception $e) {
            Log::error('News show error: ' . $e->getMessage());
            abort(404, 'News not found');
        }
    }

    public function index()
    {
        $news = News::with(['user', 'category'])
                    ->where('status', 'published')
                    ->latest('published_at')
                    ->paginate(12);
        return view('news.index', compact('news'));
    }

    public function latest()
    {
        $news = News::with(['user', 'category'])
                    ->where('status', 'published')
                    ->orderBy('published_at', 'desc')
                    ->paginate(20);
        return view('news.latest', compact('news'));
    }

    public function trending()
    {
        $news = News::with(['user', 'category'])
                    ->where('status', 'published')
                    ->orderBy('views', 'desc')
                    ->orderBy('published_at', 'desc')
                    ->paginate(20);
        return view('news.trending', compact('news'));
    }

    public function sitemap()
    {
        $news = News::where('status', 'published')->latest()->get();
        $categories = Category::where('is_active', true)->get();
        $districts = District::where('is_active', true)->get();
        $states = State::where('is_active', true)->get();
        return response()->view('sitemap', compact('news', 'categories', 'districts', 'states'))
                         ->header('Content-Type', 'text/xml');
    }
}