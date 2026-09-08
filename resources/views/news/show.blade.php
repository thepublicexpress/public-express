@extends('layouts.app')

@section('title', $news->title . ' - द पब्लिक एक्सप्रेस')

@section('meta_tags')
    <meta name="description" content="{{ $seoMeta['description'] ?? '' }}">
    <meta property="og:title" content="{{ $seoMeta['og_title'] ?? $news->title }}">
    <meta property="og:description" content="{{ $seoMeta['og_description'] ?? '' }}">
    @if($news->featured_image)
        <meta property="og:image" content="{{ asset($news->featured_image) }}">
    @else
        <meta property="og:image" content="https://placehold.co/600x400/c62828/white?text=News">
    @endif
    <link rel="canonical" href="{{ url()->current() }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $news->published_at ?? $news->created_at }}">
    @php
        $seoKeywords = array_filter([
            $news->category->display_name ?? $news->category->name ?? null,
            $news->state->display_name ?? $news->state->name ?? null,
            $news->district->display_name ?? $news->district->name ?? null,
            $news->tehsil->display_name ?? $news->tehsil->name ?? null,
            $news->block->display_name ?? $news->block->name ?? null,
            'हिंदी खबरें',
            'ताज़ा खबरें',
            $news->title,
        ]);
    @endphp
    <meta name="keywords" content="{{ implode(', ', $seoKeywords) }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $news->title }}">
    <meta name="twitter:description" content="{{ $seoMeta['description'] ?? '' }}">
    @if(isset($newsSchema) && is_array($newsSchema))
        <script type="application/ld+json">{!! json_encode($newsSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
    @endif
    @if(isset($breadcrumbSchema) && is_array($breadcrumbSchema))
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
    @endif
    @php
        $locationName = '';
        if ($news->block) {
            $locationName = $news->block->display_name ?? $news->block->name;
        } elseif ($news->tehsil) {
            $locationName = $news->tehsil->display_name ?? $news->tehsil->name;
        } elseif ($news->district) {
            $locationName = $news->district->display_name ?? $news->district->name;
        } elseif ($news->state) {
            $locationName = $news->state->display_name ?? $news->state->name;
        }
    @endphp
@endsection

{{-- 🔥 Override Layout Container – Force Full Width --}}
@push('styles')
<style>
    /* Mobile पर Layout के container को तोड़ो */
    @media (max-width: 640px) {
        .container {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }
        .px-4, .px-6, .px-3, .px-2 {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        .mx-auto {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        .p-4, .p-6, .p-3 {
            padding: 8px !important;
        }
        .md\:p-8 {
            padding: 8px !important;
        }
        .rounded-lg {
            border-radius: 0 !important;
        }
        .shadow-lg, .shadow-md {
            box-shadow: none !important;
        }
        .border {
            border-left: none !important;
            border-right: none !important;
        }
        .bg-white {
            border-radius: 0 !important;
        }
        .space-y-6 > * {
            border-radius: 0 !important;
        }
        #main-content {
            padding-left: 0 !important;
            padding-right: 0 !important;
        }
        main.container {
            padding-left: 0 !important;
            padding-right: 0 !important;
            max-width: 100% !important;
        }
        footer .container {
            padding-left: 8px !important;
            padding-right: 8px !important;
        }
    }
    
    /* ✅ In-Content Image Styles */
    .in-content-image {
        margin: 1.5rem 0;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .in-content-image img {
        width: 100%;
        height: auto;
        display: block;
    }
    .in-content-image .image-caption {
        padding: 10px 15px;
        background: #f8fafc;
        color: #475569;
        font-size: 0.9rem;
        text-align: center;
        border-top: 1px solid #e2e8f0;
    }
    
    /* ✅ In-Content Heading Styles */
    .in-content-heading {
        margin: 1.8rem 0 1rem 0;
        padding-left: 15px;
        border-left: 4px solid #c62828;
    }
    .in-content-heading h2 {
        font-size: 1.6rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .in-content-heading h3 {
        font-size: 1.3rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    .in-content-heading h4 {
        font-size: 1.1rem;
        font-weight: 700;
        color: #0f172a;
        margin: 0;
    }
    
    /* ✅ Ad Container */
    .ad-container {
        margin: 1.5rem 0;
        text-align: center;
        background: #f8fafc;
        padding: 20px 10px;
        border-radius: 8px;
        border: 1px dashed #cbd5e1;
    }
    .ad-container .ad-label {
        font-size: 0.7rem;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 8px;
        display: block;
    }
    
    /* ✅ Pull Quote */
    .pull-quote {
        margin: 1.5rem 0;
        padding: 20px 25px;
        background: #f1f5f9;
        border-left: 4px solid #c62828;
        border-radius: 0 8px 8px 0;
        font-style: italic;
        font-size: 1.1rem;
        color: #1e293b;
    }
    .pull-quote .quote-author {
        display: block;
        margin-top: 8px;
        font-style: normal;
        font-weight: 600;
        color: #64748b;
        font-size: 0.9rem;
    }
    
    /* ✅ List Styles */
    .content-list {
        margin: 1rem 0;
        padding-left: 1.5rem;
    }
    .content-list li {
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }
    .content-list li::marker {
        color: #c62828;
    }
    
    /* ✅ Image ALT Text Styling */
    .prose img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 1.5rem 0;
    }
    .prose figure {
        margin: 1.5rem 0;
    }
    .prose figure figcaption {
        font-size: 0.85rem;
        color: #64748b;
        text-align: center;
        margin-top: 4px;
    }
</style>
@endpush

@section('content')
{{-- Main container – full width on mobile --}}
<div class="container mx-auto px-0 sm:px-4 md:px-6 py-6">

    <!-- Breadcrumb -->
    <nav class="text-sm text-slate-500 mb-4 font-semibold px-3 sm:px-0" aria-label="Breadcrumb">
        <a href="/" class="hover:text-red-600">🏠 Home</a>
        <span class="mx-2">/</span>
        <a href="{{ route('category.show', $news->category->slug ?? '') }}" class="hover:text-red-600">
            {{ $news->category->display_name ?? 'Uncategorized' }}
        </a>
        <span class="mx-2">/</span>
        <span class="text-slate-800">{{ Str::limit($news->title, 50) }}</span>
    </nav>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-3">
            <article class="bg-white rounded-lg shadow-lg overflow-hidden border border-slate-200" itemscope itemtype="https://schema.org/NewsArticle">
                
                <!-- ✅ Featured Image with Proper ALT -->
                @if($news->featured_image)
                    <div class="relative w-full aspect-video bg-slate-900">
                        <img src="{{ asset($news->featured_image) }}" 
                             alt="{{ $news->alt_text ?? \App\Helpers\ImageHelper::generateFeaturedAltText($news) }}" 
                             class="w-full h-full object-cover"
                             loading="lazy"
                             itemprop="image"
                             onerror="this.src='https://placehold.co/600x400/c62828/white?text=News'">
                        @if($news->is_breaking)
                            <span class="absolute top-4 left-4 bg-red-600 text-white text-xs font-black px-3 py-1 animate-pulse">🔴 BREAKING</span>
                        @endif
                    </div>
                @else
                    <div class="w-full aspect-video bg-slate-200 flex items-center justify-center">
                        <span class="text-slate-500 font-bold">📸 कोई मुख्य फोटो नहीं</span>
                    </div>
                @endif

                <div class="p-4 md:p-8">
                    @if($news->category)
                        <span class="inline-block bg-red-600 text-white text-[10px] font-black px-3 py-1 mb-4">{{ $news->category->display_name }}</span>
                    @endif

                    <!-- ✅ Single H1 for SEO -->
                    <h1 class="text-2xl md:text-3xl lg:text-4xl font-black text-slate-950 leading-tight mb-4" itemprop="headline">{{ $news->title }}</h1>

                    <!-- Share Buttons -->
                    <div class="flex flex-wrap items-center gap-2 mb-4">
                        <span class="text-sm font-bold text-slate-600 mr-1">Share:</span>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" target="_blank" class="w-10 h-10 rounded-full bg-black text-white flex items-center justify-center hover:bg-slate-800 transition" aria-label="Share on Twitter"><i class="fab fa-x-twitter"></i></a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center hover:bg-blue-700 transition" aria-label="Share on Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://wa.me/?text={{ urlencode($news->title . ' - ' . url()->current()) }}" target="_blank" class="w-10 h-10 rounded-full bg-green-500 text-white flex items-center justify-center hover:bg-green-600 transition" aria-label="Share on WhatsApp"><i class="fab fa-whatsapp"></i></a>
                        <a href="https://t.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" target="_blank" class="w-10 h-10 rounded-full bg-blue-400 text-white flex items-center justify-center hover:bg-blue-500 transition" aria-label="Share on Telegram"><i class="fab fa-telegram-plane"></i></a>
                        <button onclick="copyLink()" class="w-10 h-10 rounded-full bg-slate-600 text-white flex items-center justify-center hover:bg-slate-700 transition" aria-label="Copy link"><i class="fas fa-link"></i></button>
                    </div>

                    <!-- Author Info -->
                    <div class="flex flex-wrap items-center gap-3 text-sm text-slate-500 border-b border-slate-200 pb-4 mb-4">
                        <button type="button" onclick="openAuthorModal()" class="flex items-center gap-2 hover:opacity-80 transition cursor-pointer group" aria-label="View author details">
                            @if($news->user && $news->user->photo && file_exists(public_path($news->user->photo)))
                                <img src="{{ asset($news->user->photo) }}" 
                                     alt="{{ $news->user->name }}" 
                                     class="w-10 h-10 rounded-full object-cover border-2 border-red-600 group-hover:border-slate-400 transition"
                                     loading="lazy">
                            @else
                                <div class="w-10 h-10 rounded-full bg-red-600 text-white flex items-center justify-center text-sm font-bold group-hover:bg-slate-600 transition">
                                    {{ substr($news->user->name ?? 'A', 0, 1) }}
                                </div>
                            @endif
                            <div class="flex flex-col items-start">
                                <span class="font-semibold text-slate-800 group-hover:text-red-600 transition" itemprop="author">{{ $news->user->name ?? 'Unknown Reporter' }}</span>
                                @if($news->user && $news->user->bio)
                                    <span class="text-xs text-slate-400">{{ Str::limit($news->user->bio, 50) }}</span>
                                @endif
                            </div>
                        </button>
                        <span class="text-slate-300">|</span>
                        <span itemprop="datePublished" content="{{ ($news->published_at ?? $news->created_at)->toIso8601String() }}">📅 {{ \Carbon\Carbon::parse($news->created_at)->format('d M Y, h:i A') }}</span>
                        <span class="text-slate-300">|</span>
                        <span>👁️ {{ number_format($news->views ?? 0) }}</span>
                        @if($locationName)
                            <span class="text-slate-300">|</span>
                            <span class="text-xs">📍 {{ $locationName }}</span>
                        @endif
                    </div>

                    <!-- ✅ SUMMARY -->
                    @if($news->summary)
                        <div class="bg-slate-50 border-l-4 border-red-600 p-4 mb-6" itemprop="description">
                            <p class="text-slate-700 font-semibold italic">{{ $news->summary }}</p>
                        </div>
                    @endif

                    <!-- ============================================================ -->
                    <!-- ✅ RENDER NEWS CONTENT WITH IN-CONTENT BLOCKS                -->
                    <!-- ============================================================ -->
                    <div class="prose prose-slate max-w-none" itemprop="articleBody">
                        {!! $news->body !!}
                    </div>

                    <!-- ============================================================ -->
                    <!-- ✅ RELATED NEWS                                              -->
                    <!-- ============================================================ -->
                    @if($relatedNews->count() > 0)
                        <div class="mt-8 pt-6 border-t-2 border-slate-200">
                            <h3 class="text-lg font-black text-slate-950 mb-4 flex items-center gap-2">
                                <span class="text-2xl">📚</span> यह भी पढ़ें
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($relatedNews as $related)
                                    <a href="{{ route('news.show', $related->slug) }}" class="bg-slate-50 rounded-lg overflow-hidden hover:shadow-lg border border-slate-200 hover:border-red-600">
                                        <div class="aspect-video bg-slate-100">
                                            @if($related->featured_image)
                                                <img src="{{ asset($related->featured_image) }}" 
                                                     alt="{{ $related->alt_text ?? \App\Helpers\ImageHelper::generateFeaturedAltText($related) }}" 
                                                     class="w-full h-full object-cover"
                                                     loading="lazy">
                                            @else
                                                <div class="w-full h-full bg-slate-200 flex items-center justify-center text-2xl">📸</div>
                                            @endif
                                        </div>
                                        <div class="p-3">
                                            <h4 class="font-bold text-sm text-slate-900">{{ $related->title }}</h4>
                                            <p class="text-xs text-slate-500">
                                                {{ \Carbon\Carbon::parse($related->created_at)->format('d M Y') }}
                                                @if($related->category)
                                                    <span class="text-slate-300">•</span> {{ $related->category->display_name }}
                                                @endif
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- ============================================================ -->
                    <!-- ✅ MORE FROM CATEGORY & LOCATION                             -->
                    <!-- ============================================================ -->
                    @if($moreFromCategory->count() > 0 || $moreFromLocation->count() > 0)
                        <div class="mt-8 pt-6 border-t-2 border-slate-200">
                            <h3 class="text-lg font-black text-slate-950 mb-4 flex items-center gap-2">
                                <span class="text-2xl">🔗</span> और लिंक देखें
                            </h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @if($moreFromCategory->count() > 0)
                                    <div class="bg-slate-50 rounded-lg border border-slate-200 p-4">
                                        <h4 class="font-semibold text-slate-900 mb-3">इसी श्रेणी से</h4>
                                        <ul class="space-y-3 text-sm">
                                            @foreach($moreFromCategory as $item)
                                                <li>
                                                    <a href="{{ route('news.show', $item->slug) }}" class="text-slate-800 hover:text-red-600">{{ Str::limit($item->title, 70) }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                @if($moreFromLocation->count() > 0)
                                    <div class="bg-slate-50 rounded-lg border border-slate-200 p-4">
                                        <h4 class="font-semibold text-slate-900 mb-3">इसी स्थान से</h4>
                                        <ul class="space-y-3 text-sm">
                                            @foreach($moreFromLocation as $item)
                                                <li>
                                                    <a href="{{ route('news.show', $item->slug) }}" class="text-slate-800 hover:text-red-600">{{ Str::limit($item->title, 70) }}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="mt-8 text-center">
                        <a href="/" class="inline-block bg-slate-900 text-white px-6 py-3 rounded-lg font-bold hover:bg-red-600">← मुख्य पृष्ठ पर वापस जाएं</a>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Latest News -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 p-4">
                <h3 class="text-base font-black text-slate-950 border-b border-slate-200 pb-2 mb-3 flex items-center gap-2">
                    <span class="text-red-600">🔥</span> ताज़ा खबरें
                </h3>
                <ul class="space-y-3">
                    @forelse($latestNews as $item)
                        <li>
                            <a href="{{ route('news.show', $item->slug) }}" class="block">
                                <span class="text-sm font-semibold text-slate-800 hover:text-red-600">{{ $item->title }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y, h:i A') }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">कोई खबर नहीं</li>
                    @endforelse
                </ul>
            </div>

            <!-- Trending News -->
            <div class="bg-white rounded-lg shadow-md border border-slate-200 p-4">
                <h3 class="text-base font-black text-slate-950 border-b border-slate-200 pb-2 mb-3 flex items-center gap-2">
                    <span class="text-yellow-500">📈</span> ट्रेंडिंग खबरें
                </h3>
                <ul class="space-y-3">
                    @forelse($trendingNews as $item)
                        <li>
                            <a href="{{ route('news.show', $item->slug) }}" class="block">
                                <span class="text-sm font-semibold text-slate-800 hover:text-red-600">{{ $item->title }}</span>
                                <span class="text-[10px] text-slate-400 block">👁️ {{ number_format($item->views ?? 0) }} views</span>
                            </a>
                        </li>
                    @empty
                        <li class="text-sm text-slate-500">कोई ट्रेंडिंग खबर नहीं</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ✅ AUTHOR MODAL                                               -->
<!-- ============================================================ -->
<div id="authorModal" style="display: none; position: fixed; inset: 0; z-index: 9999; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); justify-content: center; align-items: center;">
    <div style="background: white; max-width: 420px; width: 90%; margin: 20px; border-radius: 20px; padding: 30px 25px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); max-height: 90vh; overflow-y: auto; position: relative;">
        <button onclick="closeAuthorModal()" style="position: absolute; top: 12px; right: 18px; font-size: 28px; color: #94a3b8; background: none; border: none; cursor: pointer; transition: 0.2s;" aria-label="Close modal">&times;</button>
        <div style="text-align: center;">
            @if($news->user && $news->user->photo && file_exists(public_path($news->user->photo)))
                <img src="{{ asset($news->user->photo) }}" 
                     alt="{{ $news->user->name }}" 
                     style="width: 96px; height: 96px; border-radius: 50%; object-fit: cover; border: 4px solid #c62828; margin: 0 auto 12px; display: block;"
                     loading="lazy">
            @else
                <div style="width: 96px; height: 96px; border-radius: 50%; background: #c62828; color: white; display: flex; align-items: center; justify-content: center; font-size: 36px; font-weight: bold; margin: 0 auto 12px;">
                    {{ substr($news->user->name ?? 'A', 0, 1) }}
                </div>
            @endif
            <h3 style="font-size: 1.5rem; font-weight: 700; color: #0f172a; margin-bottom: 2px;">{{ $news->user->name ?? 'Unknown Reporter' }}</h3>
            @if($news->user && $news->user->role)
                <p style="color: #c62828; font-size: 0.9rem; font-weight: 600; margin-bottom: 6px;">{{ ucfirst(str_replace('_', ' ', $news->user->role)) }}</p>
            @endif
            @if($news->user && $news->user->getLocationString())
                <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 8px;">📍 {{ $news->user->getLocationString() }}</p>
            @endif
            @if($news->user && $news->user->bio)
                <div style="background: #f1f5f9; padding: 12px 16px; border-radius: 12px; margin: 10px 0; text-align: left;">
                    <p style="color: #334155; font-size: 0.95rem; margin: 0;">{{ $news->user->bio }}</p>
                </div>
            @else
                <p style="color: #94a3b8; font-size: 0.9rem; margin: 8px 0;">इस लेखक ने अभी तक अपना परिचय नहीं दिया है।</p>
            @endif
            @if($news->user && $news->user->email)
                <p style="color: #475569; font-size: 0.9rem; margin: 6px 0;">
                    <i class="fas fa-envelope" style="color: #94a3b8;"></i> {{ $news->user->email }}
                </p>
            @endif
            @php
                $totalNews = $news->user ? $news->user->news()->count() : 0;
                $joinDate = $news->user ? \Carbon\Carbon::parse($news->user->created_at)->format('d M Y') : 'N/A';
            @endphp
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin: 14px 0; padding-top: 14px; border-top: 1px solid #e2e8f0;">
                <div>
                    <p style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0;">{{ $totalNews }}</p>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0;">कुल खबरें</p>
                </div>
                <div>
                    <p style="font-size: 1.2rem; font-weight: 700; color: #0f172a; margin: 0;">{{ $joinDate }}</p>
                    <p style="font-size: 0.75rem; color: #64748b; margin: 0;">ज्वाइन दिनांक</p>
                </div>
            </div>
            <div style="display: flex; justify-content: center; gap: 12px; flex-wrap: wrap; margin-top: 12px;">
                @if($news->user && $news->user->facebook)
                    <a href="{{ $news->user->facebook }}" target="_blank" style="color: #1877f2; font-size: 1.3rem;" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                @endif
                @if($news->user && $news->user->x)
                    <a href="{{ $news->user->x }}" target="_blank" style="color: #0f172a; font-size: 1.3rem;" aria-label="Twitter"><i class="fab fa-x-twitter"></i></a>
                @endif
                @if($news->user && $news->user->instagram)
                    <a href="{{ $news->user->instagram }}" target="_blank" style="color: #e1306c; font-size: 1.3rem;" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                @endif
                @if($news->user && $news->user->youtube)
                    <a href="{{ $news->user->youtube }}" target="_blank" style="color: #ff0000; font-size: 1.3rem;" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                @endif
                @if($news->user && $news->user->linkedin)
                    <a href="{{ $news->user->linkedin }}" target="_blank" style="color: #0a66c2; font-size: 1.3rem;" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                @endif
                @if($news->user && $news->user->website)
                    <a href="{{ $news->user->website }}" target="_blank" style="color: #64748b; font-size: 1.3rem;" aria-label="Website"><i class="fas fa-globe"></i></a>
                @endif
            </div>
            <button onclick="closeAuthorModal()" style="width: 100%; margin-top: 16px; padding: 10px; background: #f1f5f9; border: none; border-radius: 12px; font-weight: 600; color: #334155; cursor: pointer;">बंद करें</button>
        </div>
    </div>
</div>

<script>
    function openAuthorModal() {
        var modal = document.getElementById('authorModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }
    }
    function closeAuthorModal() {
        var modal = document.getElementById('authorModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    document.addEventListener('DOMContentLoaded', function() {
        var modal = document.getElementById('authorModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeAuthorModal();
            });
        }
    });
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeAuthorModal();
    });

    function copyLink() {
        const url = window.location.href;
        navigator.clipboard.writeText(url).then(function() {
            const btn = document.querySelector('.share-copy');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.background = '#28a745';
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.style.background = '#6c757d';
            }, 2000);
        }).catch(function() {
            const input = document.createElement('input');
            input.value = url;
            document.body.appendChild(input);
            input.select();
            document.execCommand('copy');
            document.body.removeChild(input);
            const btn = document.querySelector('.share-copy');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-check"></i>';
            btn.style.background = '#28a745';
            setTimeout(function() {
                btn.innerHTML = originalText;
                btn.style.background = '#6c757d';
            }, 2000);
        });
    }
</script>
@endsection