@extends('layouts.app')

@section('title', ($category->display_name ?? $category->name) . ' की ताजा खबरें - द पब्लिक एक्सप्रेस')

@section('meta_tags')
    <meta name="description" content="{{ $category->display_name ?? $category->name }} की ताजा खबरें - द पब्लिक एक्सप्रेस">
    <meta property="og:title" content="{{ $category->display_name ?? $category->name }} की ताजा खबरें - द पब्लिक एक्सप्रेस">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endsection

@section('content')

    <!-- ============================================================ -->
    <!--  CLEAN & PROFESSIONAL CATEGORY HEADER (NO BREADCRUMB)        -->
    <!-- ============================================================ -->
    <div class="relative mb-8 rounded-2xl overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 shadow-xl border-b-4 border-brand">
        
        <!-- subtle background glow -->
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 px-6 md:px-8 py-6 md:py-8">

            <!-- ===== MAIN TITLE (Bold, Clean, No Breadcrumb) ===== -->
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight flex items-center gap-3">
                        <span class="text-4xl md:text-6xl">{{ $category->icon ?? '📰' }}</span>
                        <span class="text-brand-light drop-shadow-lg">{{ $category->display_name ?? $category->name }}</span>
                    </h1>
                    <p class="text-slate-400 text-sm font-semibold mt-1 flex items-center gap-2">
                        <i class="fas fa-newspaper text-brand"></i>
                        {{ $category->display_name ?? $category->name }} की ताजा और महत्वपूर्ण खबरें
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-brand/20 backdrop-blur-sm text-brand-light px-4 py-2 rounded-lg font-bold border border-brand/30 text-sm">
                        <i class="fas fa-file-alt mr-2"></i>{{ $news->total() }} खबरें
                    </span>
                </div>
            </div>

        </div>
    </div>

    <!-- ============================================================ -->
    <!--  NEWS CARDS GRID (Titles Bold)                               -->
    <!-- ============================================================ -->
    @if($news->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $item)
                <div class="group bg-white rounded-xl shadow-md overflow-hidden border border-slate-200 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                    
                    <!-- Image -->
                    <a href="{{ route('news.show', $item->slug) }}" class="block overflow-hidden relative aspect-video bg-slate-100">
                        @if($item->featured_image)
                            <img src="{{ asset($item->featured_image) }}" 
                                 alt="{{ $item->alt_text ?? $item->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                 loading="lazy"
                                 onerror="this.src='https://placehold.co/600x400/c62828/white?text=News'">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-slate-200 to-slate-300 flex items-center justify-center">
                                <span class="text-4xl opacity-30">{{ $category->icon ?? '📰' }}</span>
                            </div>
                        @endif
                        
                        @if($item->is_breaking)
                            <span class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black tracking-widest px-3 py-1 animate-pulse shadow-lg">🔴 BREAKING</span>
                        @endif
                        
                        <span class="absolute bottom-3 left-3 bg-black/70 backdrop-blur-sm text-white text-[10px] font-black px-3 py-1 rounded">
                            {{ $item->category->display_name ?? $item->category->name ?? 'ताजा' }}
                        </span>
                    </a>

                    <!-- Content -->
                    <div class="p-5">
                        <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold mb-2">
                            <span><i class="far fa-calendar-alt mr-1"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</span>
                            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                            <span><i class="far fa-eye mr-1"></i> {{ number_format($item->views ?? 0) }}</span>
                        </div>

                        <!-- ✅ Title - BOLD & CLEAN -->
                        <h2 class="font-extrabold text-base md:text-lg text-slate-900 line-clamp-2 leading-snug mb-2 group-hover:text-brand transition-colors">
                            <a href="{{ route('news.show', $item->slug) }}">{{ $item->title }}</a>
                        </h2>

                        <p class="text-sm text-slate-500 line-clamp-2 leading-relaxed">
                            {{ Str::limit(strip_tags($item->summary ?? $item->body ?? ''), 120) }}
                        </p>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] font-bold text-slate-400">
                                <i class="far fa-user mr-1"></i> {{ $item->user->name ?? 'Reporter' }}
                            </span>
                            <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-xs font-black hover:underline flex items-center gap-1">
                                पढ़ें <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-10 flex justify-center">
            {{ $news->links() }}
        </div>
    @else
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-slate-200">
            <span class="text-6xl block mb-4">📭</span>
            <h3 class="text-xl font-black text-slate-600">कोई खबर नहीं मिली</h3>
            <p class="text-sm text-slate-400 mt-1">इस श्रेणी में अभी कोई खबर नहीं है।</p>
            <a href="/" class="inline-block mt-4 bg-brand text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition">← होम पेज पर जाएं</a>
        </div>
    @endif

@endsection