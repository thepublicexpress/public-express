<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if(isset($state))
            {{ $state->display_name ?? $state->name }} की खबरें - द पब्लिक एक्सप्रेस
        @elseif(isset($district))
            {{ $district->display_name ?? $district->name }} की खबरें - द पब्लिक एक्सप्रेस
        @elseif(isset($tehsil))
            {{ $tehsil->display_name ?? $tehsil->name }} की खबरें - द पब्लिक एक्सप्रेस
        @elseif(isset($category))
            {{ $category->display_name ?? $category->name }} - द पब्लिक एक्सप्रेस
        @else
            खबरें - द पब्लिक एक्सप्रेस
        @endif
    </title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f1f5f9; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .border-brand { border-color: #c62828; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* मोबाइल स्क्रॉलिंग फ़िक्स */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body>

    <!-- ==================== HOME PAGE EXACT MENU ==================== -->
    <div class="bg-white w-full border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 max-w-7xl flex justify-between items-center">
            <a href="/" class="flex items-center">
                <img src="{{ asset('images/logo.png') }}" alt="द पब्लिक एक्सप्रेस" class="h-14 md:h-16 w-auto object-contain" onerror="this.onerror=null; this.src='https://placehold.co/200x60/c62828/white?text=The+Public'">
            </a>
            <div class="flex items-center space-x-4">
                <div class="relative group">
                    <button class="flex items-center text-gray-700 hover:text-brand font-medium text-sm">
                        <i class="fas fa-bars mr-1"></i> श्रेणियाँ
                        <i class="fas fa-chevron-down ml-1 text-xs"></i>
                    </button>
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-100 py-2 hidden group-hover:block z-50">
                        <a href="/category/politics" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">राजनीति</a>
                        <a href="/category/crime" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">अपराध</a>
                        <a href="/category/development" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">विकास</a>
                        <a href="/category/education" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">शिक्षा</a>
                        <a href="/category/health" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">स्वास्थ्य</a>
                        <a href="/category/agriculture" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">कृषि</a>
                        <a href="/category/sports" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">खेल</a>
                        <a href="/category/entertainment" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-brand">मनोरंजन</a>
                    </div>
                </div>
                <a href="/leaderboard" class="text-gray-700 hover:text-brand font-medium text-sm flex items-center">
                    <i class="fas fa-trophy text-yellow-500 mr-1"></i> लीडरबोर्ड
                </a>
                <button onclick="toggleSearch()" class="text-gray-700 hover:text-brand">
                    <i class="fas fa-search"></i>
                </button>
                <span class="hidden lg:inline text-xs text-gray-500 border-l border-gray-300 pl-4">
                    हर कस्बे, गाँव और सिटी की खबरें
                </span>
            </div>
        </div>
    </div>

    <!-- ==================== MOBILE SEARCH ==================== -->
    <div id="mobileSearch" class="hidden bg-white border-b border-gray-200 px-4 py-3">
        <form action="/news/search" method="GET" class="flex gap-2">
            <input type="text" name="q" placeholder="खोजें..." class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-brand text-sm">
            <button type="submit" class="bg-brand text-white px-4 py-2 rounded-lg text-sm hover:bg-red-700 transition">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- ==================== NAVIGATION BAR ==================== -->
    <nav class="bg-nav text-white sticky top-0 z-50 shadow-md border-b-2 border-brand" style="background-color: #0f172a;">
        <div class="container mx-auto max-w-7xl flex items-center justify-between">
            <div class="flex items-center overflow-x-auto no-scrollbar w-full font-bold text-sm tracking-wide">
                <a href="/" class="bg-brand text-white px-5 py-3.5 flex items-center gap-1.5 whitespace-nowrap shrink-0 hover:bg-red-700 transition-colors">
                    <i class="fas fa-home"></i> होम
                </a>
                <a href="/category/politics" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">राजनीति</a>
                <a href="/category/crime" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">अपराध</a>
                <a href="/category/development" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">विकास</a>
                <a href="/category/education" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">शिक्षा</a>
                <a href="/category/health" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">स्वास्थ्य</a>
                <a href="/category/agriculture" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">कृषि</a>
                <a href="/category/sports" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">खेल</a>
                <a href="/category/entertainment" class="px-4 py-3.5 hover:bg-brand/20 transition-colors whitespace-nowrap shrink-0">मनोरंजन</a>
            </div>
            <div class="hidden lg:flex items-center gap-4 text-xs font-bold pr-4 whitespace-nowrap">
                <a href="/leaderboard" class="text-yellow-400 hover:text-yellow-300 flex items-center gap-1">
                    <i class="fas fa-trophy"></i> लीडरबोर्ड
                </a>
            </div>
        </div>
    </nav>

    <!-- ==================== MAIN CONTENT ==================== -->
    <main class="container mx-auto px-4 py-6 max-w-7xl">

        <!-- BREADCRUMB -->
        <nav class="text-xs md:text-sm text-gray-500 mb-6 flex items-center gap-2 flex-wrap bg-white p-3 rounded-xl shadow-sm border border-gray-200/60">
            <a href="/" class="hover:text-brand font-bold transition flex items-center gap-1">
                <i class="fas fa-home text-xs"></i> होम
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-700 font-medium">
                @if(isset($state))
                    {{ $state->display_name ?? $state->name }}
                @elseif(isset($district))
                    {{ $district->display_name ?? $district->name }}
                @elseif(isset($tehsil))
                    {{ $tehsil->display_name ?? $tehsil->name }}
                @elseif(isset($category))
                    {{ $category->display_name ?? $category->name }}
                @else
                    सभी खबरें
                @endif
            </span>
        </nav>

        <!-- Title -->
        <div class="border-l-4 border-brand pl-4 mb-6">
            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-800">
                @if(isset($state))
                    {{ $state->display_name ?? $state->name }} की ताज़ा खबरें
                @elseif(isset($district))
                    {{ $district->display_name ?? $district->name }} की ताज़ा खबरें
                @elseif(isset($tehsil))
                    {{ $tehsil->display_name ?? $tehsil->name }} की ताज़ा खबरें
                @elseif(isset($category))
                    {{ $category->display_name ?? $category->name }} की ताज़ा खबरें
                @else
                    सभी खबरें
                @endif
            </h1>
            <p class="text-sm text-gray-500 mt-1">{{ $news->total() }} खबरें मिलीं</p>
        </div>

        @if($news->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($news as $item)
            <article class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-200 hover:shadow-lg transition flex flex-col">
                <!-- ✅ IMAGE FIXED -->
                @if($item->featured_image)
                    <img src="{{ asset($item->featured_image) }}" 
                         alt="{{ $item->alt_text ?? $item->title }}" 
                         class="w-full h-48 object-cover bg-gray-100"
                         onerror="this.onerror=null; this.src='https://placehold.co/600x400/c62828/white?text=News'">
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <span class="text-gray-400 text-sm">📸 कोई फोटो नहीं</span>
                    </div>
                @endif
                <div class="p-4 flex flex-col flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        @if($item->category)
                            <span class="bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wide">
                                {{ $item->category->display_name ?? $item->category->name }}
                            </span>
                        @endif
                        <span class="text-xs text-gray-400">{{ $item->published_at?->diffForHumans() ?? 'अभी' }}</span>
                    </div>
                    <h2 class="text-base font-bold text-gray-800 line-clamp-2 mb-2 flex-1">
                        <a href="{{ route('news.show', $item->slug) }}" class="hover:text-brand transition">
                            {{ $item->title }}
                        </a>
                    </h2>
                    <p class="text-sm text-gray-500 line-clamp-2 mb-3">{{ Str::limit($item->summary ?? strip_tags($item->body), 100) }}</p>
                    <div class="flex items-center justify-between mt-auto pt-3 border-t border-gray-100">
                        <span class="text-xs text-gray-400">
                            <i class="far fa-eye"></i> {{ number_format($item->views ?? 0) }}
                        </span>
                        <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-xs font-bold hover:underline flex items-center gap-1">
                            पूरी खबर <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </article>
            @endforeach
        </div>

        <div class="mt-8 flex justify-center">
            {{ $news->links() }}
        </div>

        @else
        <div class="bg-white rounded-xl p-12 text-center shadow-sm border border-gray-200">
            <p class="text-gray-400 text-lg">इस लोकेशन में अभी कोई खबर उपलब्ध नहीं है।</p>
            <a href="/" class="inline-block mt-4 text-brand font-bold hover:underline">🏠 होम पेज पर जाएं</a>
        </div>
        @endif
    </main>

    <!-- ==================== FOOTER ==================== -->
    <footer class="bg-gray-900 text-white py-8 mt-12 border-t-4 border-brand">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm font-medium">© {{ date('Y') }} द पब्लिक एक्सप्रेस - हाइपरलोकल न्यूज़ प्लेटफॉर्म</p>
        </div>
    </footer>

    <script>
        function toggleSearch() {
            var search = document.getElementById('mobileSearch');
            search.classList.toggle('hidden');
        }
    </script>
</body>
</html>