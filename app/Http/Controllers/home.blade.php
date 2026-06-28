<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>द पब्लिक एक्सप्रेस - पब्लिक की आवाज़</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background: #f8f9fa; }
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
        <div class="container mx-auto flex items-center justify-between">
            <a href="/" class="text-xl font-bold">द पब्लिक एक्सप्रेस</a>
            <div class="flex gap-3">
                <a href="/reporter/login" class="bg-white text-brand px-3 py-1 rounded-lg text-sm font-bold">रिपोर्टर लॉगिन</a>
                <a href="/admin/login" class="border border-white px-3 py-1 rounded-lg text-sm font-bold hover:bg-white hover:text-brand transition">एडमिन</a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 py-6 max-w-6xl">
        <h1 class="text-2xl font-bold mb-6 border-l-4 border-brand pl-3">ताज़ा खबरें</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($latestNews as $item)
            <div class="bg-white rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                @if($item->featured_image)
                    <img src="{{ asset('storage/' . $item->featured_image) }}" alt="{{ $item->title }}" class="w-full h-48 object-cover">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400">📰</span>
                    </div>
                @endif
                <div class="p-4">
                    <span class="text-xs text-gray-400">{{ $item->category->name ?? 'खबर' }}</span>
                    <h2 class="font-bold text-lg mt-1 line-clamp-2">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-brand">{{ $item->title }}</a>
                    </h2>
                    <p class="text-sm text-gray-500 mt-2 line-clamp-2">{{ $item->summary }}</p>
                    <div class="flex justify-between items-center mt-3 pt-3 border-t">
                        <span class="text-xs text-gray-400">{{ $item->published_at->diffForHumans() }}</span>
                        <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-sm font-bold">पढ़ें →</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-6">
            {{ $latestNews->links() }}
        </div>
    </main>

    <footer class="bg-gray-900 text-white text-center py-6 mt-8 text-sm">
        © {{ date('Y') }} द पब्लिक एक्सप्रेस - पब्लिक की आवाज़
    </footer>

</body>
</html>