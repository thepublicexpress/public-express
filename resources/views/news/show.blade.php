<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $news->title }} - द पब्लिक एक्सप्रेस</title>
    
    <!-- ==================== 📰 ADVANCED SEO META TAGS ==================== -->
    <meta name="description" content="{{ $news->summary ?? strip_tags(substr($news->body, 0, 160)) }}">
    <meta property="og:title" content="{{ $news->title }}">
    <meta property="og:description" content="{{ $news->summary ?? strip_tags(substr($news->body, 0, 160)) }}">
    @if($news->featured_image && file_exists(public_path('storage/' . $news->featured_image)))
        <meta property="og:image" content="{{ asset('storage/' . $news->featured_image) }}">
    @else
        <meta property="og:image" content="https://placehold.co/600x400/c62828/white?text=News">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="article">
    <meta property="article:published_time" content="{{ $news->published_at ?? $news->created_at }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $news->title }}">
    <meta name="twitter:description" content="{{ $news->summary ?? strip_tags(substr($news->body, 0, 160)) }}">
    
    <!-- Fonts & FontAwesome 6 Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background: #f8f9fa; }
        .bg-brand { background-color: #c62828; }
        .bg-nav { background-color: #0f172a; } 
        .text-brand { color: #c62828; }
        .border-brand { border-color: #c62828; }
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        /* 📝 मुख्य खबर के अक्षरों को बड़ा और दोनों तरफ से बराबर (Justify) करने की स्टाइल */
        .prose p, .news-body-content p, .news-body-content { 
            text-align: justify !important; 
            text-justify: inter-word !important; 
            margin-bottom: 1.5rem; 
            line-height: 1.9; 
            font-size: 1.25rem !important; 
            color: #111827; 
        }
        .prose img, .news-body-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.5rem 0; }
        
        /* मोबाइल स्क्रॉलिंग फ़िक्स */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body>

    <!-- ==================== 🗺️ WHITE LOGO AREA (HOME PAGE EXACT STYLE) ==================== -->
    <div class="bg-white w-full border-b border-gray-200">
        <div class="container mx-auto px-4 py-3 max-w-7xl flex justify-between items-center">
            
            <!-- होम पेज जैसा ही गोल इमेज लोगो -->
            <a href="/" class="flex items-center">
                <img src="https://thepublicexpress.com/images/logo.png" alt="द पब्लिक एक्सप्रेस" class="h-14 md:h-16 w-auto object-contain" onerror="this.onerror=null; this.src='{{ asset('images/logo.png') }}';">
            </a>

            <!-- रिपोर्टर लॉगिन बटन -->
            <div class="flex items-center">
                <a href="/login" class="bg-brand text-white text-xs font-bold px-4 py-2.5 rounded uppercase tracking-wider shadow-sm hover:bg-red-700 transition">
                    <i class="fas fa-user-tie mr-1"></i> REPORTER LOGIN
                </a>
            </div>
        </div>
    </div>

    <!-- ==================== 🛠️ HINDI NAVIGATION BAR ==================== -->
    <nav class="bg-nav text-white sticky top-0 z-50 shadow-md border-b-2 border-brand">
        <div class="container mx-auto max-w-7xl flex items-center justify-between">
            
            <div class="flex items-center overflow-x-auto no-scrollbar w-full font-bold text-sm tracking-wide">
                
                <a href="/" class="bg-brand text-white px-5 py-3.5 flex items-center gap-1.5 whitespace-nowrap shrink-0 hover:bg-red-700 transition-colors">
                    <i class="fas fa-home"></i> होम
                </a>
                
                <!-- कैटेगरीज शुद्ध हिंदी में -->
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

    <!-- Main Content Container -->
    <main class="container mx-auto px-4 py-6 max-w-7xl">
        
        <!-- 🗺️ BREADCRUMB -->
        <nav class="text-xs md:text-sm text-gray-500 mb-6 flex items-center gap-2 flex-wrap bg-white p-3 rounded-xl shadow-sm border border-gray-200/60">
            <a href="/" class="hover:text-brand font-bold transition flex items-center gap-1">
                <i class="fas fa-home text-xs"></i> होम
            </a>
            <span class="text-gray-300">/</span>
            <span class="text-gray-700 font-medium">
                {{ $news->category->name ?? 'अन्य खबर' }}
            </span>
            <span class="text-gray-300">/</span>
            <span class="text-gray-400 truncate max-w-[180px] md:max-w-none">{{ Str::limit($news->title, 40) }}</span>
        </nav>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

            <!-- MAIN NEWS ARTICLE -->
            <div class="lg:col-span-8 space-y-6">
                <article class="bg-white rounded-xl shadow-sm overflow-hidden border border-gray-100">
                    
                    @if($news->featured_image && file_exists(public_path('storage/' . $news->featured_image)))
                        <img src="{{ asset('storage/' . $news->featured_image) }}" alt="{{ $news->title }}" class="w-full h-auto max-h-[450px] object-cover" onerror="this.src='https://placehold.co/600x400/c62828/white?text=News'">
                    @else
                        <div class="w-full h-72 bg-gray-200 flex items-center justify-center">
                            <span class="text-gray-400 text-lg">📸 कोई मुख्य फोटो उपलब्ध नहीं है</span>
                        </div>
                    @endif
                    
                    <div class="p-6 md:p-8">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="bg-red-50 text-red-600 border border-red-100 px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wide">
                                {{ $news->category->name ?? 'अन्य' }}
                            </span>
                        </div>
                        
                        <h1 class="text-2xl md:text-3xl lg:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                            {{ $news->title }}
                        </h1>
                        
                        <div class="flex flex-wrap items-center justify-between gap-4 text-sm text-gray-500 mb-6 pb-4 border-b border-gray-100">
                            <div class="flex items-center gap-2">
                                <i class="far fa-user text-brand font-bold"></i>
                                <span class="font-bold text-gray-800">
                                    @if(isset($news->user) && is_object($news->user))
                                        {{ $news->user->name }}
                                    @else
                                        द Public Express रिपोर्टर
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center gap-4">
                                <span><i class="far fa-calendar-alt text-brand"></i> {{ $news->published_at ? $news->published_at->format('d M Y, h:i A') : 'अभी' }}</span>
                                <span><i class="far fa-eye text-brand"></i> {{ number_format($news->views ?? 0) }} व्यूज</span>
                            </div>
                        </div>
                        
                        @if($news->summary)
                            <div class="bg-gray-50 p-4 rounded-xl border-l-4 border-brand mb-6 text-gray-700 italic text-base">
                                📌 {{ $news->summary }}
                            </div>
                        @endif

                        <!-- 📝 मुख्य खबर का भाग (बड़ा फॉन्ट और दोनों तरफ से जस्टिफाई) -->
                        <div class="prose max-w-none news-body-content">
                            {!! $news->body !!}
                        </div>

                        <!-- ==================== ✉️ NEWSLETTER BOX ==================== -->
                        <div class="mt-8 bg-gray-50 p-6 rounded-xl border border-gray-200 shadow-sm text-center">
                            <h4 class="font-black text-gray-800 text-lg mb-1 flex items-center justify-center gap-2">
                                <i class="far fa-envelope-open text-brand"></i> द पब्लिक एक्सप्रेस न्यूज़लेटर
                            </h4>
                            <p class="text-xs text-gray-500 mb-4">ताजा खबरों और बड़ी ख़बरों के बुलेटिन सीधे अपने ईमेल पर पाने के लिए सब्सक्राइब करें।</p>
                            <form action="#" method="POST" class="flex flex-col sm:flex-row gap-2 max-w-md mx-auto">
                                @csrf
                                <input type="email" name="email" placeholder="अपना ईमेल पता दर्ज करें" required class="flex-1 px-4 py-2 text-sm rounded-lg border border-gray-300 focus:outline-none focus:border-brand text-gray-800 bg-white">
                                <button type="submit" class="bg-brand text-white font-bold text-xs px-5 py-2.5 rounded-lg shadow hover:bg-red-700 transition whitespace-nowrap">
                                    सब्सक्राइब करें
                                </button>
                            </form>
                        </div>

                        <!-- SHARE BUTTONS (WhatsApp, Facebook, X, Telegram और Copy Link) -->
                        <div class="mt-8 pt-6 border-t border-gray-100">
                            <p class="text-sm font-bold text-gray-700 mb-3 flex items-center gap-1.5">
                                <i class="fas fa-share-alt text-brand"></i> इस खबर को शेयर करें:
                            </p>
                            <div class="flex flex-wrap gap-2.5">
                                <!-- WhatsApp -->
                                <a href="https://wa.me/?text={{ urlencode($news->title . ' - ' . url()->current()) }}" target="_blank" class="bg-[#25D366] hover:bg-[#20ba5a] text-white px-4 py-2 rounded-lg text-xs md:text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                                    <i class="fab fa-whatsapp text-base"></i> WhatsApp
                                </a>
                                <!-- Facebook -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="bg-[#1877F2] hover:bg-[#166fe5] text-white px-4 py-2 rounded-lg text-xs md:text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                                    <i class="fab fa-facebook-f text-sm"></i> Facebook
                                </a>
                                <!-- X (Twitter) -->
                                <a href="https://twitter.com/intent/tweet?text={{ urlencode($news->title) }}&url={{ urlencode(url()->current()) }}" target="_blank" class="bg-[#000000] hover:bg-[#222222] text-white px-4 py-2 rounded-lg text-xs md:text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                                    <i class="fab fa-x-twitter text-sm"></i> X (Twitter)
                                </a>
                                <!-- Telegram -->
                                <a href="https://telegram.me/share/url?url={{ urlencode(url()->current()) }}&text={{ urlencode($news->title) }}" target="_blank" class="bg-[#0088cc] hover:bg-[#0077b3] text-white px-4 py-2 rounded-lg text-xs md:text-sm font-bold flex items-center gap-2 transition-all shadow-sm">
                                    <i class="fab fa-telegram-plane text-base"></i> Telegram
                                </a>
                                <!-- Copy Link -->
                                <button onclick="copyLink()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg text-xs md:text-sm font-bold flex items-center gap-2 border border-gray-200 transition-all">
                                    <i class="fas fa-link text-xs"></i> लिंक कॉपी करें
                                </button>
                            </div>
                        </div>

                    </div>
                </article>
            </div>

            <!-- SIDEBAR -->
            <div class="lg:col-span-4 space-y-6">
                <!-- 🔥 TRENDING NEWS SECTION -->
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <h4 class="font-bold text-gray-900 text-base mb-4 pb-1.5 border-b-2 border-brand inline-block">
                        🔥 ट्रेंडिंग खबरें
                    </h4>
                    <div class="divide-y divide-gray-100">
                        @forelse($trendingNews ?? [] as $item)
                            <div class="py-3 first:pt-0 last:pb-0 flex gap-3 group">
                                <span class="text-base font-black text-red-600/30 group-hover:text-red-600 transition-colors w-5">
                                    {{ $loop->iteration }}
                                </span>
                                <a href="{{ route('news.show', $item->slug) }}" class="text-sm font-bold text-gray-800 hover:text-brand transition-colors line-clamp-2 flex-1 leading-snug">
                                    {{ $item->title }}
                                </a>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-2">कोई खबर उपलब्ध नहीं है।</p>
                        @endforelse
                    </div>
                </div>

                <!-- ==================== 🔗 SAMBANDHIT KHABRE (वापस जोड़ा गया) ==================== -->
                <div class="bg-white rounded-xl shadow-sm p-5 border border-gray-100">
                    <h4 class="font-bold text-gray-900 text-base mb-4 pb-1.5 border-b-2 border-brand inline-block">
                        🔗 संबंधित खबरें
                    </h4>
                    <div class="space-y-4">
                        @forelse($relatedNews ?? [] as $item)
                            <div class="flex gap-3 group items-start">
                                @if($item->featured_image && file_exists(public_path('storage/' . $item->featured_image)))
                                    <img src="{{ asset('storage/' . $item->featured_image) }}" alt="{{ $item->title }}" class="w-20 h-14 object-cover rounded-lg shrink-0 shadow-sm">
                                @else
                                    <img src="https://placehold.co/150x100/c62828/white?text=News" class="w-20 h-14 object-cover rounded-lg shrink-0 shadow-sm">
                                @endif
                                <div class="flex-1 min-w-0">
                                    <a href="{{ route('news.show', $item->slug) }}" class="text-xs md:text-sm font-bold text-gray-800 hover:text-brand transition-colors line-clamp-2 leading-snug">
                                        {{ $item->title }}
                                    </a>
                                    <span class="text-[10px] text-gray-400 block mt-1">
                                        <i class="far fa-calendar-alt"></i> {{ $item->published_at ? $item->published_at->format('d M Y') : $item->created_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-2">कोई संबंधित खबर नहीं मिली।</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </div>
    </main>

    <footer class="bg-gray-900 text-white py-6 mt-12 text-center text-sm border-t-4 border-brand">
        <div class="container mx-auto px-4">
            <p class="text-gray-400 tracking-wide font-medium">2026 The Public Express All rights Reserved</p>
        </div>
    </footer>

    <script>
        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('✅ लिंक कॉपी हो गया!');
            });
        }
    </script>
</body>
</html>