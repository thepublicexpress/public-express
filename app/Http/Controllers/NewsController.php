<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\State;
use App\Models\District;
use App\Models\Tehsil;
use App\Models\Block;
use App\Services\SEOService;
use App\Services\AdService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class NewsPageController extends Controller
{
    protected $seoService;
    protected $adService;

    public function __construct(SEOService $seoService, AdService $adService)
    {
        $this->seoService = $seoService;
        $this->adService = $adService;
    }

    /**
     * ✅ Show Single News Post with SEO and Ads
     */
    public function show($slug)
    {
        try {
            // Get news with relations
            $news = News::with(['user', 'category', 'state', 'district', 'tehsil', 'block'])
                ->where('slug', $slug)
                ->where('status', 'published')
                ->firstOrFail();

            // Increment views
            $news->increment('views');

            // Get related news
            $relatedNews = News::where('category_id', $news->category_id)
                ->where('id', '!=', $news->id)
                ->where('status', 'published')
                ->orderBy('published_at', 'desc')
                ->limit(5)
                ->get();

            // ✅ Get trending news for sidebar
            $trendingNews = News::where('status', 'published')
                ->orderBy('views', 'desc')
                ->orderBy('published_at', 'desc')
                ->limit(10)
                ->get();

            // ============================================================
            // ✅ SEO META - ALWAYS DEFINED WITH FALLBACK
            // ============================================================
            try {
                $seoMeta = $this->seoService->generateSEOMeta($news);
            } catch (\Exception $e) {
                Log::error('SEO Meta generation failed: ' . $e->getMessage());
                $seoMeta = $this->getDefaultSEOMeta($news);
            }

            // ============================================================
            // ✅ NEWS SCHEMA - WITH FALLBACK
            // ============================================================
            try {
                $newsSchema = $this->seoService->generateNewsSchema($news);
            } catch (\Exception $e) {
                Log::error('News Schema generation failed: ' . $e->getMessage());
                $newsSchema = $this->getDefaultNewsSchema($news);
            }

            // ============================================================
            // ✅ BREADCRUMB SCHEMA - WITH FALLBACK
            // ============================================================
            try {
                $breadcrumbSchema = $this->seoService->generateBreadcrumbSchema($news);
            } catch (\Exception $e) {
                Log::error('Breadcrumb Schema generation failed: ' . $e->getMessage());
                $breadcrumbSchema = $this->getDefaultBreadcrumbSchema($news);
            }

            // ============================================================
            // ✅ ADS - WITH FALLBACK
            // ============================================================
            $sidebarAds = collect([]);
            $inArticleAds = collect([]);

            try {
                $location = $this->adService->getUserLocation();
                $sidebarAds = $this->adService->getAds('sidebar', $location);
                $inArticleAds = $this->adService->getAds('in-article', $location);
            } catch (\Exception $e) {
                Log::error('Ad Service failed: ' . $e->getMessage());
            }

            return view('news.show', compact(
                'news',
                'relatedNews',
                'trendingNews',
                'seoMeta',
                'newsSchema',
                'breadcrumbSchema',
                'sidebarAds',
                'inArticleAds'
            ));

        } catch (\Exception $e) {
            Log::error('News show error: ' . $e->getMessage());
            abort(404, 'News not found');
        }
    }

    /**
     * ✅ Search News
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        $category = $request->get('category');
        $location = $request->get('location');

        $news = News::where('status', 'published');

        if ($query) {
            $news->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('summary', 'LIKE', "%{$query}%")
                  ->orWhere('body', 'LIKE', "%{$query}%");
            });
        }

        if ($category) {
            $news->where('category_id', $category);
        }

        if ($location) {
            $news->where(function($q) use ($location) {
                $q->where('state_id', $location)
                  ->orWhere('district_id', $location)
                  ->orWhere('tehsil_id', $location)
                  ->orWhere('block_id', $location);
            });
        }

        $news = $news->orderBy('published_at', 'desc')->paginate(20);

        return view('news.search', compact('news', 'query'));
    }

    /**
     * ✅ Category News
     */
    public function category($slug)
    {
        $category = NewsCategory::where('slug', $slug)->firstOrFail();

        $news = News::where('category_id', $category->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        // ✅ Trending news for sidebar
        $trendingNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return view('news.category', compact('category', 'news', 'trendingNews'));
    }

    /**
     * ✅ State News
     */
    public function state($slug)
    {
        $state = State::where('slug', $slug)->firstOrFail();

        $news = News::where('state_id', $state->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $trendingNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return view('news.state', compact('state', 'news', 'trendingNews'));
    }

    /**
     * ✅ District News
     */
    public function district($slug)
    {
        $district = District::where('slug', $slug)->firstOrFail();

        $news = News::where('district_id', $district->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $trendingNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return view('news.district', compact('district', 'news', 'trendingNews'));
    }

    /**
     * ✅ Tehsil News
     */
    public function tehsil($slug)
    {
        $tehsil = Tehsil::where('slug', $slug)->firstOrFail();

        $news = News::where('tehsil_id', $tehsil->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $trendingNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return view('news.tehsil', compact('tehsil', 'news', 'trendingNews'));
    }

    /**
     * ✅ Block News
     */
    public function block($slug)
    {
        $block = Block::where('slug', $slug)->firstOrFail();

        $news = News::where('block_id', $block->id)
            ->where('status', 'published')
            ->orderBy('published_at', 'desc')
            ->paginate(20);

        $trendingNews = News::where('status', 'published')
            ->orderBy('views', 'desc')
            ->limit(10)
            ->get();

        return view('news.block', compact('block', 'news', 'trendingNews'));
    }

    // ============================================================
    // ✅ DEFAULT FALLBACK METHODS
    // ============================================================

    /**
     * ✅ Default SEO Meta (Fallback)
     */
    protected function getDefaultSEOMeta($news)
    {
        return [
            'title' => $news->title ?? 'द पब्लिक एक्सप्रेस',
            'description' => strip_tags($news->summary ?? $news->body ?? 'हर कस्बे गाँव और सिटी की खबरें'),
            'og_title' => $news->title ?? 'द पब्लिक एक्सप्रेस',
            'og_description' => strip_tags($news->summary ?? 'हर कस्बे गाँव और सिटी की खबरें'),
            'og_image' => $news->featured_image ?? asset('images/logo.png'),
            'og_url' => route('news.show', $news->slug ?? ''),
            'og_type' => 'article',
            'twitter_card' => 'summary_large_image',
            'twitter_title' => $news->title ?? 'द पब्लिक एक्सप्रेस',
            'twitter_description' => strip_tags($news->summary ?? 'हर कस्बे गाँव और सिटी की खबरें'),
            'twitter_image' => $news->featured_image ?? asset('images/logo.png'),
        ];
    }

    /**
     * ✅ Default News Schema (Fallback)
     */
    protected function getDefaultNewsSchema($news)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news->title ?? '',
            'description' => $news->summary ?? '',
            'image' => $news->featured_image ?? '',
            'datePublished' => $news->published_at ?? $news->created_at ?? date('Y-m-d H:i:s'),
            'dateModified' => $news->updated_at ?? date('Y-m-d H:i:s'),
            'author' => [
                '@type' => 'Person',
                'name' => $news->user->name ?? 'द पब्लिक एक्सप्रेस'
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'द पब्लिक एक्सप्रेस',
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('images/logo.png')
                ]
            ],
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => route('news.show', $news->slug ?? '')
            ]
        ];
    }

    /**
     * ✅ Default Breadcrumb Schema (Fallback)
     */
    protected function getDefaultBreadcrumbSchema($news)
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'होम',
                    'item' => url('/')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $news->category->name ?? 'खबरें',
                    'item' => route('category.show', $news->category->slug ?? '')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $news->title ?? 'खबर',
                    'item' => route('news.show', $news->slug ?? '')
                ]
            ]
        ];
    }
}