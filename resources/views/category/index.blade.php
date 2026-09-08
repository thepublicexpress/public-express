@extends('layouts.app')

@section('title', 'सभी कैटेगरी - द पब्लिक एक्सप्रेस')

@section('meta_tags')
    <meta name="description" content="द पब्लिक एक्सप्रेस की सभी कैटेगरी - राजनीति, अपराध, शिक्षा, खेल, मनोरंजन">
    <meta property="og:title" content="सभी कैटेगरी - द पब्लिक एक्सप्रेस">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
@endsection

@section('content')

    <!-- ============================================================ -->
    <!--  CLEAN HEADER (NO BREADCRUMB)                                -->
    <!-- ============================================================ -->
    <div class="relative mb-8 rounded-2xl overflow-hidden bg-gradient-to-r from-slate-900 via-slate-800 to-slate-900 shadow-xl border-b-4 border-brand">
        <div class="absolute inset-0 opacity-5">
            <div class="absolute top-0 right-0 w-64 h-64 bg-brand rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-48 h-48 bg-blue-500 rounded-full blur-3xl"></div>
        </div>
        <div class="relative z-10 px-6 md:px-8 py-6 md:py-8">
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-5xl font-black text-white tracking-tight flex items-center gap-3">
                        <span class="text-4xl md:text-6xl">📂</span>
                        <span class="text-brand-light drop-shadow-lg">सभी कैटेगरी</span>
                    </h1>
                    <p class="text-slate-400 text-sm font-semibold mt-1 flex items-center gap-2">
                        <i class="fas fa-folder-open text-brand"></i>
                        हर कस्बे, गाँव और सिटी की खबरें
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="bg-brand/20 backdrop-blur-sm text-brand-light px-4 py-2 rounded-lg font-bold border border-brand/30 text-sm">
                        <i class="fas fa-file-alt mr-2"></i>{{ $categories->count() ?? 0 }} कैटेगरी
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!--  CATEGORIES GRID                                             -->
    <!-- ============================================================ -->
    @if(isset($categories) && $categories->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($categories as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" 
                   class="group bg-white rounded-xl shadow-md overflow-hidden border border-slate-200 hover:shadow-2xl transition-all duration-300 hover:-translate-y-1 block">
                    <div class="p-6 flex items-center gap-4">
                        <span class="text-4xl">{{ $cat->icon ?? '📰' }}</span>
                        <div>
                            <h3 class="font-extrabold text-lg text-slate-900 group-hover:text-brand transition-colors">
                                {{ $cat->display_name ?? $cat->name }}
                            </h3>
                            <p class="text-sm text-slate-500 line-clamp-1">
                                {{ $cat->description ?? $cat->display_name . ' की ताजा खबरें' }}
                            </p>
                            <span class="text-xs text-brand font-black mt-1 inline-block">
                                <i class="fas fa-arrow-right"></i> खबरें देखें
                            </span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if(isset($categories) && method_exists($categories, 'links'))
            <div class="mt-10 flex justify-center">
                {{ $categories->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-16 bg-white rounded-xl shadow-sm border border-slate-200">
            <span class="text-6xl block mb-4">📭</span>
            <h3 class="text-xl font-black text-slate-600">कोई कैटेगरी नहीं मिली</h3>
            <p class="text-sm text-slate-400 mt-1">अभी कोई कैटेगरी उपलब्ध नहीं है।</p>
            <a href="/" class="inline-block mt-4 bg-brand text-white px-6 py-2 rounded-lg font-bold hover:bg-red-700 transition">← होम पेज पर जाएं</a>
        </div>
    @endif

@endsection