@extends('layouts.app')

@section('content')
<!-- Hyperlocal Search -->
<div class="bg-gradient-to-br from-slate-950 to-slate-900 rounded-none border-t-4 border-brand p-5 md:p-6 mb-8 text-white shadow-xl relative overflow-hidden hyperlocal-container">
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5 relative z-10">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="w-2.5 h-2.5 rounded-full bg-brand animate-ping" aria-hidden="true"></span>
                <p class="text-brand text-xs font-black tracking-widest">📍 लाइव लोकल सर्च</p>
            </div>
            <h2 class="text-xl md:text-2xl font-black tracking-tight">हर कस्बे गाँव और सिटी की खबरें</h2>
        </div>
        <div class="flex gap-2.5 flex-wrap w-full lg:w-auto">
            <label for="stateSelect" class="sr-only">राज्य चुनें</label>
            <select id="stateSelect" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-slate-900 text-xs md:text-sm font-bold bg-white border-2 border-slate-200 focus:outline-none focus:border-brand shadow-sm" aria-label="राज्य चुनें">
                <option value="">राज्य चुनें</option>
                @foreach($states ?? [] as $state)
                    <option value="{{ $state->id }}">{{ $state->display_name ?? $state->name }}</option>
                @endforeach
            </select>
            <label for="districtSelect" class="sr-only">जिला चुनें</label>
            <select id="districtSelect" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-slate-900 text-xs md:text-sm font-bold bg-white border-2 border-slate-200 focus:outline-none focus:border-brand shadow-sm" aria-label="जिला चुनें">
                <option value="">जिला चुनें</option>
            </select>
            <label for="tehsilSelect" class="sr-only">तहसील चुनें</label>
            <select id="tehsilSelect" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-slate-900 text-xs md:text-sm font-bold bg-white border-2 border-slate-200 focus:outline-none focus:border-brand shadow-sm" aria-label="तहसील चुनें">
                <option value="">तहसील चुनें</option>
            </select>
            <button onclick="goToLocation()" class="w-full lg:w-auto px-6 py-3 bg-brand text-white font-black text-sm hover:bg-white hover:text-slate-950 transition-all transform active:scale-95 shadow-md tracking-wider" aria-label="खबरें देखें">
                <span>खबरें देखें</span> ➔
            </button>
        </div>
    </div>
</div>

<!-- Big Feature -->
<div class="grid grid-cols-1 gap-6 mb-12">
    <div class="lg:col-span-1">
        @if($latestNews->first())
        <div class="relative rounded-none overflow-hidden shadow-2xl group bg-slate-900 h-[360px] md:h-[460px] border-b-4 border-brand featured-image-container">
            <img src="{{ $latestNews->first()->featured_image ? asset($latestNews->first()->featured_image) : asset('images/default-news.jpg') }}"
                 alt="{{ $latestNews->first()->title }}"
                 class="w-full h-full object-cover opacity-90 group-hover:opacity-75 group-hover:scale-105 transition duration-700"
                 loading="eager"
                 fetchpriority="high"
                 width="800" height="460"
                 decoding="async"
                 onerror="this.src='{{ asset('images/default-news.jpg') }}'">
            <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent p-5 md:p-8 flex flex-col justify-end">
                <div class="flex gap-2 mb-3">
                    <span class="bg-brand text-white text-[10px] font-black tracking-widest px-3 py-1">🔥 मुख्य समाचार</span>
                    <span class="bg-white text-slate-950 text-[10px] font-black px-3 py-1">{{ $latestNews->first()->category->display_name ?? $latestNews->first()->category->name ?? 'ताजा खबर' }}</span>
                </div>
                <h2 class="text-xl md:text-3xl font-black text-white mb-3 leading-tight tracking-tight">
                    <a href="{{ route('news.show', $latestNews->first()->slug) }}" class="hover:text-brand transition" aria-label="{{ $latestNews->first()->title }}">{{ $latestNews->first()->title }}</a>
                </h2>
                <p class="text-slate-300 text-xs md:text-sm line-clamp-2 font-semibold">{{ Str::limit($latestNews->first()->summary ?? $latestNews->first()->body, 120) }}</p>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Latest News Grid -->
<div class="mb-12">
    <div class="flex justify-between items-center mb-6 border-b-2 border-slate-950 pb-2">
        <h2 class="text-base md:text-lg font-black text-slate-950 tracking-wide">📰 ताज़ा मुख्य बुलेटिन समाचार</h2>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($latestNews as $item)
        <div class="bg-white rounded-none shadow-md border border-slate-200 overflow-hidden transform hover:-translate-y-1 hover:shadow-lg transition flex flex-col justify-between">
            <div>
                <div class="relative overflow-hidden aspect-video bg-slate-100 border-b-2 border-slate-100 news-card-image">
                    <img src="{{ $item->featured_image ? asset($item->featured_image) : asset('images/default-news.jpg') }}"
                         alt="{{ $item->alt_text ?? $item->title }}"
                         class="w-full h-full object-cover"
                         loading="lazy"
                         width="400" height="225"
                         decoding="async"
                         onerror="this.src='{{ asset('images/default-news.jpg') }}'">
                    <span class="absolute top-3 left-3 bg-brand text-white text-[9px] font-black tracking-wider px-2 py-0.5 shadow-md">{{ $item->category->display_name ?? $item->category->name ?? 'ताजा समाचार' }}</span>
                    @if($item->is_breaking)
                        <span class="absolute top-3 right-3 bg-red-600 text-white text-[9px] font-black tracking-wider px-2 py-0.5 shadow-md" style="animation:none;">🔴 BREAKING</span>
                    @endif
                </div>
                <div class="p-4">
                    <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-black tracking-wider mb-2">
                        <span>⏱️ {{ $item->created_at->diffForHumans() }}</span>
                    </div>
                    <h3 class="font-black text-base text-slate-950 line-clamp-2 mb-2 leading-tight hover:text-brand transition">
                        <a href="{{ route('news.show', $item->slug) }}" aria-label="{{ $item->title }}">{{ $item->title }}</a>
                    </h3>
                    <p class="text-xs text-slate-500 line-clamp-2 font-semibold">{{ Str::limit($item->summary ?? $item->body, 100) }}</p>
                </div>
            </div>
            <div class="px-4 pb-4 pt-3 flex justify-between items-center border-t border-slate-100">
                <span class="text-[10px] font-black text-slate-500">👁️ {{ number_format($item->views ?? 0) }} पाठक</span>
                <a href="{{ route('news.show', $item->slug) }}" class="text-brand text-xs font-black hover:underline flex items-center gap-0.5 tracking-wider" aria-label="{{ $item->title }} पूरी खबर पढ़ें">पूरी खबर →</a>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-8 flex justify-center">
        @if (method_exists($latestNews, 'links'))
            {{ $latestNews->links() }}
        @endif
    </div>
</div>

<!-- Stats & Reporters -->
<div class="mt-16 pt-8 border-t-2 border-slate-200">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-brand">{{ number_format($totalNews ?? 0) }}</div>
            <div class="text-sm text-slate-500">कुल खबरें</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-green-600">{{ number_format($todayNews ?? 0) }}</div>
            <div class="text-sm text-slate-500">आज की खबरें</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-blue-600">{{ number_format($topReporters->count() ?? 0) }}</div>
            <div class="text-sm text-slate-500">सक्रिय रिपोर्टर</div>
        </div>
        <div class="bg-white rounded-xl shadow-md p-6 text-center">
            <div class="text-3xl font-bold text-purple-600">{{ number_format($categories->count() ?? 0) }}</div>
            <div class="text-sm text-slate-500">श्रेणियाँ</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-md p-6">
        <h3 class="text-xl font-bold text-slate-800 mb-4 flex items-center gap-2">
            <i class="fas fa-trophy text-yellow-500"></i> हमारे सर्वश्रेष्ठ रिपोर्टर
            <span class="text-sm font-normal text-slate-400 ml-2">(Performance आधारित)</span>
        </h3>
        @if($topReporters && $topReporters->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($topReporters as $index => $reporter)
                    @php
                        $newsCount = $reporter->news_count ?? 0;
                        $totalViews = $reporter->total_views ?? 0;
                    @endphp
                    <div class="flex items-center p-3 bg-slate-50 rounded-lg hover:shadow-md transition group">
                        <div class="flex-shrink-0 w-10 h-10 bg-brand text-white rounded-full flex items-center justify-center font-bold text-sm mr-3">
                            #{{ $index + 1 }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                @if($reporter->photo && file_exists(public_path($reporter->photo)))
                                    <img src="{{ asset($reporter->photo) }}" alt="{{ $reporter->name }}" class="w-8 h-8 rounded-full object-cover">
                                @endif
                                <span class="font-semibold text-slate-800">{{ $reporter->name }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs text-slate-500 mt-1">
                                <span><i class="fas fa-star text-yellow-500"></i> {{ number_format($reporter->points ?? 0) }} पॉइंट्स</span>
                                <span><i class="fas fa-newspaper"></i> {{ number_format($newsCount) }} खबरें</span>
                                <span><i class="fas fa-eye"></i> {{ number_format($totalViews) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-slate-400">अभी कोई रिपोर्टर नहीं</p>
        @endif
    </div>
</div>

<!-- ===== MOBILE BOTTOM NAV (only visible on mobile) ===== -->
<nav class="md:hidden fixed bottom-4 left-4 right-4 h-16 glass-nav shadow-2xl border-2 border-slate-950/10 z-[9999] flex justify-around items-center px-2 rounded-xl" aria-label="मोबाइल नेविगेशन">
    <a href="/" class="flex flex-col items-center justify-center w-12 h-12 text-brand rounded-xl" aria-label="होम"><i class="fas fa-home text-xl" aria-hidden="true"></i><span class="text-[9px] font-black mt-0.5">होम</span></a>
    <a href="{{ route('news.latest') }}" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl" aria-label="ताजा खबरें"><i class="fas fa-clock text-xl" aria-hidden="true"></i><span class="text-[9px] font-black mt-0.5">ताजा</span></a>
    <a href="{{ route('news.trending') }}" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl" aria-label="ट्रेंडिंग खबरें"><i class="fas fa-fire text-xl" aria-hidden="true"></i><span class="text-[9px] font-black mt-0.5">ट्रेंड</span></a>
    @auth
        @php $profileUrl = (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin') ? route('admin.dashboard') : (Auth::user()->role === 'reporter' ? route('reporter.dashboard') : route('home')); @endphp
        <a href="{{ $profileUrl }}" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl" aria-label="प्रोफाइल"><i class="fas fa-user text-xl" aria-hidden="true"></i><span class="text-[9px] font-black mt-0.5">प्रोफाइल</span></a>
    @else
        <a href="{{ route('login') }}" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl" aria-label="लॉगिन"><i class="fas fa-sign-in-alt text-xl" aria-hidden="true"></i><span class="text-[9px] font-black mt-0.5">लॉगिन</span></a>
    @endauth
</nav>

<script>
    // Location search
    document.getElementById('stateSelect').addEventListener('change', function() {
        const stateId = this.value;
        const districtSelect = document.getElementById('districtSelect');
        const tehsilSelect = document.getElementById('tehsilSelect');
        districtSelect.innerHTML = '<option value="">जिला चुनें</option>';
        tehsilSelect.innerHTML = '<option value="">तहसील चुनें</option>';
        if (!stateId) return;
        fetch('/api/get-districts/' + stateId)
            .then(res => res.json())
            .then(data => {
                data.forEach(d => {
                    const displayName = d.display_name || d.name;
                    districtSelect.innerHTML += `<option value="${d.id}">${displayName}</option>`;
                });
            });
    });
    document.getElementById('districtSelect').addEventListener('change', function() {
        const districtId = this.value;
        const tehsilSelect = document.getElementById('tehsilSelect');
        tehsilSelect.innerHTML = '<option value="">तहसील चुनें</option>';
        if (!districtId) return;
        fetch('/api/get-tehsils/' + districtId)
            .then(res => res.json())
            .then(data => {
                data.forEach(t => {
                    const displayName = t.display_name || t.name;
                    tehsilSelect.innerHTML += `<option value="${t.id}">${displayName}</option>`;
                });
            });
    });
    function goToLocation() {
        const stateSelect = document.getElementById('stateSelect');
        const districtSelect = document.getElementById('districtSelect');
        const tehsilSelect = document.getElementById('tehsilSelect');
        const stateId = stateSelect.value;
        const districtId = districtSelect.value;
        const tehsilId = tehsilSelect.value;
        if (tehsilId) {
            fetch('/api/get-tehsils/' + districtId)
                .then(res => res.json())
                .then(data => {
                    const tehsil = data.find(t => t.id == tehsilId);
                    if (tehsil && tehsil.slug) window.location.href = '/news/tehsil/' + tehsil.slug;
                    else alert('तहसील का लिंक नहीं मिला।');
                });
        } else if (districtId) {
            fetch('/api/get-districts/' + stateId)
                .then(res => res.json())
                .then(data => {
                    const district = data.find(d => d.id == districtId);
                    if (district && district.slug) window.location.href = '/news/district/' + district.slug;
                    else alert('जिले का लिंक नहीं मिला।');
                });
        } else if (stateId) {
            fetch('/api/get-states')
                .then(res => res.json())
                .then(data => {
                    const state = data.find(s => s.id == stateId);
                    if (state && state.slug) window.location.href = '/news/state/' + state.slug;
                    else alert('राज्य का लिंक नहीं मिला।');
                });
        } else {
            alert('कृपया राज्य, जिला या तहसील चुनें।');
        }
    }
</script>
@endsection