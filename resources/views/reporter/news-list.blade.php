<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>मेरी खबरें - द पब्लिक एक्सप्रेस</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Noto Sans Devanagari', sans-serif; background-color: #f3f4f6; }
        .bg-brand { background-color: #c62828; }
        .text-brand { color: #c62828; }
        .border-brand { border-color: #c62828; }
        .status-badge { padding: 3px 10px; border-radius: 9999px; font-size: 11px; font-weight: 600; }
        .status-published { background: #dcfce7; color: #166534; }
        .status-pending { background: #fef3c7; color: #92400e; }
        .status-rejected { background: #fee2e2; color: #991b1b; }
        .status-draft { background: #e5e7eb; color: #374151; }
        .card-hover { transition: all 0.2s; }
        .card-hover:hover { transform: translateY(-2px); box-shadow: 0 8px 30px rgba(0,0,0,0.08); }
        .image-placeholder { background: #f3f4f6; display: flex; align-items: center; justify-content: center; color: #9ca3af; font-size: 12px; }
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="bg-brand text-white p-4 sticky top-0 z-50 flex items-center gap-3 shadow-md">
        <a href="{{ route('reporter.dashboard') }}" class="hover:text-gray-200 transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <h1 class="text-lg font-bold">📰 मेरी खबरें</h1>
        <a href="{{ route('reporter.news.create') }}" class="ml-auto bg-white/20 hover:bg-white/30 transition px-4 py-1.5 rounded-full text-sm font-bold">
            + नई खबर
        </a>
    </header>

    <main class="p-4 max-w-2xl mx-auto">

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 text-green-700 p-4 rounded-lg mb-4 text-sm">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg mb-4 text-sm">
                ❌ {{ session('error') }}
            </div>
        @endif

        <!-- Stats -->
        <div class="grid grid-cols-4 gap-2 mb-4">
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-xl font-bold text-brand">{{ $news->total() }}</p>
                <p class="text-xs text-gray-500">कुल</p>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-xl font-bold text-green-600">{{ $news->where('status', 'published')->count() }}</p>
                <p class="text-xs text-gray-500">लाइव</p>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-xl font-bold text-yellow-600">{{ $news->where('status', 'pending')->count() }}</p>
                <p class="text-xs text-gray-500">पेंडिंग</p>
            </div>
            <div class="bg-white rounded-xl p-3 text-center shadow-sm">
                <p class="text-xl font-bold text-red-600">{{ $news->where('status', 'rejected')->count() }}</p>
                <p class="text-xs text-gray-500">रिजेक्ट</p>
            </div>
        </div>

        @if($news->count() > 0)
        <div class="space-y-3">
            @foreach($news as $item) {{-- ✅ Loop ke andar $item use karein --}}
            <div class="bg-white rounded-xl shadow-sm overflow-hidden card-hover border border-gray-100">
                <div class="flex gap-3 p-3">
                    <!-- Image -->
                    <div class="flex-shrink-0">
                        @if($item->featured_image)
                            <img src="{{ url('/serve-image/' . urlencode($item->featured_image)) }}"
                                 class="w-20 h-20 rounded-lg object-cover"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex'">
                            <div class="w-20 h-20 rounded-lg image-placeholder" style="display:none;">
                                <span>📷</span>
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-lg image-placeholder flex items-center justify-center">
                                <span class="text-2xl">📰</span>
                            </div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="flex-1 min-w-0">
                        <h3 class="text-sm font-bold line-clamp-2">
                            <a href="{{ route('reporter.news.show', $item->id) }}" class="hover:text-brand transition">
                                {{ $item->title }}
                            </a>
                        </h3>

                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="status-badge
                                {{ $item->status == 'published' ? 'status-published' :
                                   ($item->status == 'pending' ? 'status-pending' :
                                   ($item->status == 'rejected' ? 'status-rejected' : 'status-draft')) }}">
                                @if($item->status == 'published')
                                    ✅ लाइव
                                @elseif($item->status == 'pending')
                                    ⏳ पेंडिंग
                                @elseif($item->status == 'rejected')
                                    ❌ रिजेक्ट
                                @else
                                    📝 ड्राफ्ट
                                @endif
                            </span>
                            <span class="text-xs text-gray-400">📅 {{ $item->created_at->format('d/m/Y h:i A') }}</span>
                        </div>

                        @if($item->status == 'rejected' && $item->rejection_reason)
                            <div class="bg-red-50 border-l-4 border-red-500 p-2 mt-1 rounded text-xs">
                                <p class="text-red-600 font-medium">❌ Rejection Reason:</p>
                                <p class="text-red-600">{{ $item->rejection_reason }}</p>
                                <p class="text-red-500 mt-1">💡 Edit and resubmit for approval.</p>
                            </div>
                        @endif

                        <div class="flex flex-wrap items-center gap-3 mt-2 text-xs text-gray-500">
                            <span>👁️ {{ number_format($item->views ?? 0) }}</span>
                            <span>📂 {{ $item->category->name ?? 'अन्य' }}</span>
                            @if($item->district)
                                <span>📍 {{ $item->district->name ?? '' }}</span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-wrap gap-2 mt-2 pt-2 border-t border-gray-100">
                            <a href="{{ route('reporter.news.show', $item->id) }}"
                               class="text-gray-600 text-xs font-medium hover:text-brand transition px-2 py-1 bg-gray-50 rounded-lg">
                                👁️ View
                            </a>

                            <a href="{{ route('reporter.news.edit', $item->id) }}"
                               class="text-blue-600 text-xs font-medium hover:text-blue-800 transition px-2 py-1 bg-blue-50 rounded-lg">
                                ✏️ Edit & Resubmit
                            </a>

                            @if($item->status != 'published')
                                <form method="POST" action="{{ route('reporter.news.destroy', $item->id) }}"
                                      style="display:inline"
                                      onsubmit="return confirm('क्या आप यह खबर डिलीट करना चाहते हैं? यह कार्रवाई वापस नहीं ली जा सकती।')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-red-600 text-xs font-medium hover:text-red-800 transition px-2 py-1 bg-red-50 rounded-lg">
                                        🗑️ Delete
                                    </button>
                                </form>
                            @endif

                            @if($item->status == 'rejected' && $item->rejection_reason)
                                <span class="text-xs text-red-500 bg-red-50 px-2 py-1 rounded-lg">
                                    Reason: {{ Str::limit($item->rejection_reason, 50) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6">
            {{ $news->links() }}
        </div>

        @else
        <div class="bg-white rounded-xl p-10 text-center shadow-sm border border-gray-100">
            <div class="text-6xl mb-4">📝</div>
            <h3 class="text-lg font-bold text-gray-700">अभी कोई खबर नहीं</h3>
            <p class="text-gray-400 text-sm mt-1">आपने अभी तक कोई खबर सबमिट नहीं की है।</p>
            <a href="{{ route('reporter.news.create') }}" class="inline-block mt-4 bg-brand text-white px-6 py-2.5 rounded-full font-bold text-sm hover:bg-red-700 transition">
                + पहली खबर भेजें
            </a>
        </div>
        @endif
    </main>

    <footer class="text-center py-4 text-xs text-gray-400">
        द पब्लिक एक्सप्रेस - रिपोर्टर पैनल
    </footer>

</body>
</html>