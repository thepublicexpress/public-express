@extends('layouts.app')

@section('title', ($category->name ?? 'कैटेगरी') . ' - द पब्लिक एक्सप्रेस')

@section('content')
<div class="container my-2">
    <nav aria-label="breadcrumb" class="mb-4 d-none d-md-block">
        <ol class="breadcrumb bg-white p-3 rounded shadow-sm" style="font-weight: 700; font-size: 14px; border-left: 4px solid #b91c1c;">
            <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none text-dark"><i class="fas fa-home me-1"></i> होम</a></li>
            <li class="breadcrumb-item active text-danger" aria-current="page">{{ $category->name ?? 'ताज़ा ख़बरें' }}</li>
        </ol>
    </nav>

    <h3 class="border-start border-danger border-4 ps-3 mb-4 text-dark" style="font-family: 'Noto Sans Devanagari', sans-serif; font-weight: 800;">
        कैटेगरी: {{ $category->name ?? 'ताज़ा ख़बरें' }}
    </h3>

    <div class="row">
        @if(isset($news) && $news->count() > 0)
            @foreach($news as $item)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm border-0 rounded-3 overflow-hidden bg-white">
                    @if($item->image)
                        <img src="{{ asset('storage/' . $item->image) }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="{{ asset('images/logo.png') }}" class="card-img-top p-4 bg-light" alt="द पब्लिक एक्सप्रेस" style="height: 200px; object-fit: contain;">
                    @endif
                    <div class="card-body d-flex flex-column justify-content-between">
                        <div>
                            <h5 class="card-title text-dark" style="font-family: 'Noto Sans Devanagari', sans-serif; font-weight: 700; font-size: 1.05rem; line-height: 1.5;">
                                <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="text-decoration-none text-dark hover-danger">
                                    {{ \Illuminate\Support\Str::limit($item->title, 65) }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small mt-2">
                                {{ \Illuminate\Support\Str::limit(strip_tags($item->content), 100) }}
                            </p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('news.show', $item->slug ?? $item->id) }}" class="btn btn-sm btn-danger px-3 fw-bold shadow-sm" style="background: #b91c1c; border: none; border-radius: 4px;">
                                पूरी ख़बर पढ़ें <i class="fas fa-arrow-right ms-1" style="font-size: 11px;"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        @else
            <div class="col-md-12 text-center py-5">
                <div class="bg-white p-5 rounded-3 shadow-sm border border-light">
                    <div class="text-muted mb-3" style="font-size: 60px; opacity: 0.3;">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <h4 class="text-secondary fw-bold" style="font-family: 'Noto Sans Devanagari', sans-serif;">इस कैटेगरी में अभी कोई ख़बर उपलब्ध नहीं है।</h4>
                    <p class="text-muted small">जल्द ही इस सेक्शन में नई खबरें जोड़ी जाएंगी।</p>
                    <a href="{{ url('/') }}" class="btn btn-sm btn-dark px-4 py-2 mt-2 fw-bold">
                        <i class="fas fa-home me-1"></i> होम पेज पर वापस जाएं
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    .hover-danger:hover { color: #b91c1c !important; transition: 0.2s; }
</style>
@endsection