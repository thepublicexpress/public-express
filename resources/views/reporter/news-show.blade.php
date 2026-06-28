<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $news->title }} - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .border-brand { border-color: #c62828; }
        .status-badge { padding: 4px 14px; border-radius: 9999px; font-size: 12px; font-weight: 600; }
        .status-published { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-draft { background: #e5e7eb; color: #374151; }
        .content-body p { margin-bottom: 12px; line-height: 1.8; }
        .content-body img { max-width: 100%; border-radius: 8px; margin: 16px 0; }
        .content-body h2, .content-body h3 { font-weight: 700; margin: 16px 0 8px; }
        .content-body ul, .content-body ol { padding-left: 24px; margin-bottom: 12px; }
        .card-shadow { box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="bg-brand text-white p-4 sticky top-0 z-50 flex items-center gap-3 shadow-md">
        <a href="{{ route('reporter.news.index') }}" class="hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-lg font-bold truncate">📰 खबर देखें</h1>
        <span class="ml-auto text-xs bg-white/20 px-3 py-1 rounded-full">
            #{{ $news->id }}
        </span>
    </header>

    <main class="p-4 max-w-3xl mx-auto">

        <a href="{{ route('reporter.news.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-brand transition text-sm mb-4">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            मेरी खबरें
        </a>

        @if($news->status == 'rejected' && $news->rejection_reason)
            <div class="bg-red-50 border-2 border-red-500 p-4 rounded-xl mb-4">
                <h5 class="text-red-700 font-bold">❌ This news was rejected!</h5>
                <p class="text-red-600 mt-1"><strong>Reason:</strong> {{ $news->rejection_reason }}</p>
                <p class="text-red-500 text-sm mt-2">💡 Edit and resubmit for approval.</p>
                <a href="{{ route('reporter.news.edit', $news->id) }}" 
                   class="inline-block mt-2 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">
                    ✏️ Edit & Resubmit
                </a>
            </div>
        @endif

        <div class="bg-white rounded-2xl overflow-hidden card-shadow border border-gray-100">
            
            <div class="px-6 py-3 border-b border-gray-100 flex flex-wrap items-center justify-between gap-2 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <span class="status-badge 
                        {{ $news->status == 'published' ? 'status-published' : 
                           ($news->status == 'pending' ? 'status-pending' : 
                           ($news->status == 'rejected' ? 'status-rejected' : 'status-draft')) }}">
                        @if($news->status == 'published') ✅ लाइव
                        @elseif($news->status == 'pending') ⏳ पेंडिंग
                        @elseif($news->status == 'rejected') ❌ रिजेक्ट
                        @else 📝 ड्राफ्ट
                        @endif
                    </span>
                </div>
                <span class="text-xs text-gray-400">
                    📅 {{ $news->created_at->format('d M Y, h:i A') }}
                </span>
            </div>

            @if($news->featured_image)
                <div class="w-full bg-gray-100">
                    <img src="{{ url('/serve-image/' . urlencode($news->featured_image)) }}" 
                         alt="{{ $news->title }}" 
                         class="w-full max-h-[400px] object-cover"
                         onerror="this.src='{{ asset('images/default-news.jpg') }}'">
                </div>
            @endif

            <div class="p-6">
                <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mb-3">
                    @if($news->category)
                        <span class="bg-gray-100 px-3 py-1 rounded-full">📂 {{ $news->category->name }}</span>
                    @endif
                    @if($news->state)
                        <span>📍 {{ $news->state->name }}</span>
                    @endif
                    @if($news->district)
                        <span>→ {{ $news->district->name }}</span>
                    @endif
                    @if($news->tehsil)
                        <span>→ {{ $news->tehsil->name }}</span>
                    @endif
                </div>

                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight mb-3">
                    {{ $news->title }}
                </h1>

                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 border-b border-gray-100 pb-4 mb-4">
                    <span>👁️ {{ number_format($news->views ?? 0) }} व्यूज</span>
                    <span>❤️ {{ number_format($news->likes ?? 0) }} लाइक्स</span>
                    <span>📤 {{ number_format($news->shares ?? 0) }} शेयर</span>
                </div>

                @if($news->summary)
                    <div class="bg-gray-50 rounded-xl p-4 mb-4 border-l-4 border-brand">
                        <p class="text-gray-700 text-sm italic">📌 {{ $news->summary }}</p>
                    </div>
                @endif

                <div class="content-body text-gray-800 text-base leading-relaxed">
                    {!! nl2br(e($news->body)) !!}
                </div>

                @if($news->status == 'rejected' && $news->rejection_reason)
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg mt-4">
                        <p class="text-sm font-bold text-red-700">❌ Rejection Reason:</p>
                        <p class="text-sm text-red-600">{{ $news->rejection_reason }}</p>
                    </div>
                @endif

            </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 mt-4">
            <!-- ✅ Edit Button for ALL status -->
            <a href="{{ route('reporter.news.edit', $news->id) }}" 
               class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-bold px-6 py-3 rounded-xl transition">
                ✏️ Edit & Resubmit
            </a>
            
            @if($news->status != 'published')
                <form method="POST" action="{{ route('reporter.news.destroy', $news->id) }}" 
                      style="display:inline" 
                      onsubmit="return confirm('क्या आप यह खबर डिलीट करना चाहते हैं? यह कार्रवाई वापस नहीं ली जा सकती।')">
                    @csrf @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white font-bold px-6 py-3 rounded-xl transition">
                        🗑️ डिलीट करें
                    </button>
                </form>
            @endif

            @if($news->status == 'published')
                <a href="{{ route('news.show', $news->slug) }}" target="_blank" 
                   class="inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white font-bold px-6 py-3 rounded-xl transition">
                    🌐 लाइव देखें
                </a>
            @endif
        </div>

        <div class="bg-white rounded-xl p-4 mt-4 border border-gray-100 card-shadow">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-brand/10 flex items-center justify-center text-brand font-bold text-lg">
                    {{ substr($news->user->name ?? 'R', 0, 1) }}
                </div>
                <div>
                    <p class="text-sm font-bold">{{ $news->user->name ?? 'Unknown' }}</p>
                    <p class="text-xs text-gray-400">रिपोर्टर</p>
                </div>
                <div class="ml-auto text-right">
                    <p class="text-xs text-gray-400">सबमिट किया</p>
                    <p class="text-xs text-gray-500">{{ $news->created_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>

    </main>

    <footer class="text-center py-4 text-xs text-gray-400 border-t border-gray-200 mt-6">
        द पब्लिक एक्सप्रेस - रिपोर्टर पैनल
    </footer>

</body>
</html>