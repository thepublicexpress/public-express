@extends('layouts.app')

@section('title', $block->display_name . ' - द पब्लिक एक्सप्रेस')

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-black text-slate-950">📍 {{ $block->display_name }} की ताजा खबरें</h1>
        <p class="text-sm text-slate-500 mt-1">ब्लॉक की सभी महत्वपूर्ण खबरें यहाँ पढ़ें</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($news as $item)
            <div class="bg-white rounded-lg shadow-md overflow-hidden border border-slate-200 hover:shadow-lg transition">
                <a href="{{ route('news.show', $item->slug) }}">
                    @if($item->featured_image)
                        <img src="{{ asset($item->featured_image) }}" 
                             alt="{{ $item->alt_text ?? $item->title }}" 
                             class="w-full h-48 object-cover"
                             loading="lazy"
                             onerror="this.src='https://placehold.co/600x400/c62828/white?text=News'">
                    @else
                        <div class="w-full h-48 bg-slate-200 flex items-center justify-center text-slate-500 text-sm">📸</div>
                    @endif
                </a>
                <div class="p-4">
                    <h2 class="font-bold text-base text-slate-900 line-clamp-2">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-brand transition">{{ $item->title }}</a>
                    </h2>
                    <p class="text-xs text-slate-500 mt-1">{{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}</p>
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-10 text-slate-500">कोई खबर नहीं मिली।</div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $news->links() }}
    </div>
@endsection