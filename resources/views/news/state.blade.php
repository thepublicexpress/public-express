@extends('layouts.app')
@section('title', $state->name . ' - द पब्लिक एक्सप्रेस')
@section('content')
<div class="container mx-auto px-4 py-6 max-w-6xl">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold border-l-4 border-brand pl-3">🗺️ {{ $state->name }} की खबरें</h1>
        <a href="/" class="text-brand text-sm font-bold hover:underline">← होम</a>
    </div>

    @if($news->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($news as $item)
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden card-hover">
            @php
                $imagePath = $item->featured_image;
                if($imagePath && !Str::startsWith($imagePath, 'http')) {
                    $imageUrl = asset('storage/' . $imagePath);
                } elseif($imagePath && Str::startsWith($imagePath, 'http')) {
                    $imageUrl = $imagePath;
                } else {
                    $imageUrl = 'https://placehold.co/600x350/c62828/white?text=' . urlencode($state->name);
                }
            @endphp
            <img src="{{ $imageUrl }}" 
                 alt="{{ $item->title }}" 
                 class="w-full h-48 object-cover"
                 loading="lazy"
                 onerror="this.src='https://placehold.co/600x350/c62828/white?text={{ urlencode($state->name) }}'">
            <div class="p-4">
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-2">
                    <span class="bg-gray-100 px-2 py-0.5 rounded">{{ $item->category->name ?? 'अन्य' }}</span>
                    <span>{{ $item->published_at->diffForHumans() }}</span>
                </div>
                <h2 class="font-bold text-lg line-clamp-2 mb-2">
                    <a href="{{ route('news.show', $item->slug) }}" class="hover:text-brand">{{ $item->title }}</a>
                </h2>
                <p class="text-sm text-gray-500 line-clamp-2">{{ $item->summary }}</p>
                <div class="flex justify-between items-center mt-3 pt-3 border-t">
                    <span class="text-xs text-gray-400">👁️ {{ number_format($item->views ?? 0) }}</span>
                    <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-sm font-bold hover:underline">पढ़ें →</a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-6">
        {{ $news->links() }}
    </div>
    @else
    <div class="bg-white rounded-2xl p-12 text-center">
        <p class="text-gray-400 text-lg">इस राज्य में अभी कोई खबर नहीं है।</p>
    </div>
    @endif
</div>
@endsection