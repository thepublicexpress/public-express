@extends('layouts.app')

@section('title', 'ट्रेंडिंग खबरें - द पब्लिक एक्सप्रेस')

@section('content')
    <div class="relative mb-8 rounded-2xl overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 shadow-xl border-b-4 border-brand">
        <div class="absolute inset-0 opacity-5"><div class="absolute top-0 right-0 w-64 h-64 bg-amber-500 rounded-full blur-3xl"></div></div>
        <div class="relative z-10 px-6 md:px-8 py-6 md:py-8">
            <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight flex items-center gap-3">
                <span class="text-4xl md:text-6xl">🔥</span>
                <span class="text-amber-400 drop-shadow-lg">ट्रेंडिंग खबरें</span>
            </h1>
            <p class="text-slate-400 text-sm font-semibold mt-1">सबसे ज्यादा पढ़ी जाने वाली खबरें</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($news as $item)
            <div class="bg-white rounded-xl shadow-md overflow-hidden border border-slate-200 hover:shadow-2xl transition hover:-translate-y-1">
                <a href="{{ route('news.show', $item->slug) }}" class="block overflow-hidden relative aspect-video">
                    @if($item->featured_image)
                        <img src="{{ asset($item->featured_image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover" loading="lazy" onerror="this.src='https://placehold.co/600x400/c62828/white?text=News'">
                    @else
                        <div class="w-full h-full bg-slate-200 flex items-center justify-center"><span class="text-4xl opacity-30">📰</span></div>
                    @endif
                    @if($item->is_breaking)
                        <span class="absolute top-3 left-3 bg-red-600 text-white text-[10px] font-black px-3 py-1 animate-pulse shadow-lg">🔴 BREAKING</span>
                    @endif
                    <span class="absolute bottom-3 left-3 bg-black/70 text-white text-[10px] font-black px-3 py-1 rounded">🔥 {{ number_format($item->views ?? 0) }} व्यूज</span>
                </a>
                <div class="p-5">
                    <div class="flex items-center gap-2 text-[10px] text-slate-400 font-bold mb-2">
                        <span><i class="far fa-calendar-alt mr-1"></i> {{ $item->created_at->format('d M Y') }}</span>
                    </div>
                    <h2 class="font-extrabold text-base text-slate-900 line-clamp-2 leading-snug">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-brand transition">{{ $item->title }}</a>
                    </h2>
                    <p class="text-sm text-slate-500 line-clamp-2 mt-2">{{ Str::limit(strip_tags($item->summary ?? $item->body ?? ''), 100) }}</p>
                    <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-[10px] font-bold text-slate-400"><i class="far fa-user mr-1"></i> {{ $item->user->name ?? 'Reporter' }}</span>
                        <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-xs font-black hover:underline">पढ़ें →</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-16"><span class="text-6xl block mb-4">📭</span><h3 class="text-xl font-black text-slate-600">कोई ट्रेंडिंग खबर नहीं</h3></div>
        @endforelse
    </div>
    <div class="mt-10 flex justify-center">{{ $news->links() }}</div>
@endsection