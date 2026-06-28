<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(isset($state))
            {{ $state->name }} की खबरें - द पब्लिक एक्सप्रेस
        @elseif(isset($district))
            {{ $district->name }} की खबरें - द पब्लिक एक्सप्रेस
        @elseif(isset($tehsil))
            {{ $tehsil->name }} की खबरें - द पब्लिक एक्सप्रेस
        @elseif(isset($category))
            {{ $category->name }} - द पब्लिक एक्सप्रेस
        @else
            खबरें - द पब्लिक एक्सप्रेस
        @endif
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body>

    <header class="bg-brand text-white p-4 sticky top-0 z-50 shadow-md">
        <div class="container mx-auto flex items-center gap-4">
            <a href="/" class="text-xl font-bold">द पब्लिक एक्सप्रेस</a>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 max-w-4xl">
        <!-- Title -->
        <div class="border-l-4 border-brand pl-3 mb-6">
            <h1 class="text-2xl font-bold text-gray-800">
                @if(isset($state))
                    {{ $state->name }} की ताज़ा खबरें
                @elseif(isset($district))
                    {{ $district->name }} की ताज़ा खबरें
                @elseif(isset($tehsil))
                    {{ $tehsil->name }} की ताज़ा खबरें
                @elseif(isset($category))
                    {{ $category->name }} की ताज़ा खबरें
                @else
                    सभी खबरें
                @endif
            </h1>
        </div>

        @if($news->count() > 0)
        <div class="space-y-4">
            @foreach($news as $item)
            <article class="bg-white rounded-xl shadow-sm overflow-hidden flex gap-3 p-3 hover:shadow-md transition">
                <img src="{{ $item->featured_image ? asset('storage/'.$item->featured_image) : 'https://placehold.co/100x100?text=News' }}" 
                     class="w-28 h-28 rounded-lg object-cover bg-gray-100">
                <div class="flex-1">
                    <h2 class="text-md font-bold text-gray-800 line-clamp-2">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-brand transition">
                            {{ $item->title }}
                        </a>
                    </h2>
                    <p class="text-xs text-gray-400 mt-1">{{ $item->published_at?->diffForHumans() ?? 'अभी' }}</p>
                    <p class="text-sm text-gray-500 line-clamp-2 mt-1">{{ $item->summary }}</p>
                    <div class="mt-2 flex items-center gap-3">
                        <span class="text-xs text-gray-400">{{ $item->views ?? 0 }} व्यूज</span>
                        <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-xs font-semibold">पूरी खबर पढ़ें →</a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $news->links() }}
        </div>
        @else
        <div class="bg-white rounded-xl p-12 text-center">
            <p class="text-gray-400">इस लोकेशन में अभी कोई खबर उपलब्ध नहीं है।</p>
            <a href="/" class="inline-block mt-4 text-brand font-bold underline">होम पेज पर जाएं</a>
        </div>
        @endif
    </main>

    <footer class="bg-gray-900 text-white py-6 mt-8">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">© {{ date('Y') }} द पब्लिक एक्सप्रेस - हाइपरलोकल न्यूज़ प्लेटफॉर्म</p>
        </div>
    </footer>

</body>
</html>