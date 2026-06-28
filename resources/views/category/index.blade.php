@extends('layouts.app')

@section('title', 'सभी कैटेगरी - द पब्लिक एक्सप्रेस')

@section('content')
<div class="container my-2">
    <nav aria-label="breadcrumb" class="mb-4 d-none d-md-block">
        <ol class="breadcrumb bg-white p-3 rounded shadow-sm" style="font-weight: 700; font-size: 14px; border-left: 4px solid #b91c1c;">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-dark"><i class="fas fa-home me-1"></i> होम</a></li>
            <li class="breadcrumb-item active text-danger" aria-current="page">सभी कैटेगरी</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center mb-4 pb-2 border-bottom border-2 border-danger">
        <h3 class="text-dark m-0" style="font-family: 'Noto Sans Devanagari', sans-serif; font-weight: 800;">
            <span class="bg-brand text-white px-3 py-1 rounded me-2" style="background-color: #b91c1c;"><i class="fas fa-folder"></i></span>
            समाचार कैटेगरी
        </h3>
    </div>

    @php
        $finalNews = isset($newsList) && $newsList->count() > 0 ? $newsList : (isset($posts) && $posts->count() > 0 ? $posts : null);
    @endphp

    <div class="row g-4">
        @if($finalNews && $finalNews->count() > 0)
            @foreach($finalNews as $item)
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden bg-white hover-shadow transition-all">
                    <div style="height: 220px; overflow: hidden; background-color: #f8fafc;">
                        @if(isset($item->featured_image))
                            <img src="{{ asset('storage/' . $item->featured_image) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $item->title }}">
                        @elseif(isset($item->image))
                            <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top h-100 w-100 object-fit-cover" alt="{{ $item->title }}">
                        @else
                            <img src="{{ asset('images/logo.png') }}" class="card-img-top p-4 h-100 w-100 object-fit-contain" alt="द पब्लिक एक्सप्रेस">
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column justify-content-between p-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-2" style="font-size: 12px;">
                                <span class="badge bg-danger" style="background-color: #b91c1c;">{{ $category->name ?? 'ताज़ा ख़बर' }}</span>
                                <span class="text-muted">•</span>
                                <span class="text-muted"><i class="far fa-clock"></i> {{ $item->created_at ? $item->created_at->diffForHumans() : '' }}</span>
                            </div>
                            
                            <h5 class="card-title text-dark fw-bold" style="font-family: 'Noto Sans Devanagari', sans-serif; font-size: 1.05rem; line-height: 1.5; font-weight: 700;">
                                <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="text-decoration-none text-dark hover-text-danger">
                                    {{ \Illuminate\Support\Str::limit($item->title, 70) }}
                                </a>
                            </h5>
                            
                            <p class="card-text text-muted small mt-2">
                                {{ \Illuminate\Support\Str::limit(strip_tags($item->summary ?? $item->body ?? $item->content), 110) }}
                            </p>
                        </div>

                        <div class="mt-3 pt-2 border-top border-light d-flex justify-content-between align-items-center text-muted" style="font-size: 12px;">
                            <span><i class="far fa-user"></i> {{ $item->user->name ?? 'रिपोर्टर' }}</span>
                            <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="btn btn-sm btn-danger px-3 fw-bold shadow-sm" style="background-color: #b91c1c; border: none; font-size: 12px;">
                                पूरी ख़बर <i class="fas fa-arrow-right ms-1" style="font-size: 10px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            <div class="col-12 d-flex justify-content-center mt-5">
                {{ $finalNews->links() }}
            </div>
        @else
            <div class="col-md-12 text-center py-5">
                <div class="bg-white p-5 rounded-3 shadow-sm border border-light">
                    <h4 class="text-secondary fw-bold">कोई ख़बर उपलब्ध नहीं है।</h4>
                    <a href="{{ url('/') }}" class="btn btn-danger px-4 py-2 mt-2 fw-bold" style="background-color: #b91c1c; border: none;">होम पेज पर जाएं</a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important; }
    .hover-text-danger:hover { color: #b91c1c !important; }
</style>
@endsection