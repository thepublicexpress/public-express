@extends('layouts.app')

@section('title', $category->display_name . ' - द पब्लिक एक्सप्रेस')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb bg-white p-3 rounded shadow-sm">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}" class="text-decoration-none">होम</a></li>
                    <li class="breadcrumb-item active">{{ $category->display_name }}</li>
                </ol>
            </nav>

            <!-- Category Header -->
            <div class="bg-white p-4 rounded shadow-sm mb-4 border-left-4" style="border-left: 4px solid {{ $category->color ?? '#c62828' }};">
                <h1 class="h2 fw-bold mb-0">
                    {{ $category->icon ?? '📰' }} {{ $category->display_name }}
                </h1>
                <p class="text-muted small mt-1">{{ $category->description ?? $category->display_name . ' की ताज़ा खबरें' }}</p>
            </div>

            <!-- News Grid -->
            @if($news->count() > 0)
                <div class="row g-4">
                    @foreach($news as $item)
                        <div class="col-md-6 col-lg-4">
                            <div class="card h-100 shadow-sm hover-shadow transition">
                                @if($item->featured_image)
                                    <img src="{{ url('/serve-image/' . urlencode($item->featured_image)) }}" 
                                         class="card-img-top" 
                                         style="height:200px; object-fit:cover;"
                                         alt="{{ $item->title }}"
                                         onerror="this.src='{{ asset('images/default-news.jpg') }}'">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                                        <span class="display-1 text-muted">📰</span>
                                    </div>
                                @endif
                                <div class="card-body">
                                    <h5 class="card-title fw-bold">
                                        <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark hover-text-brand">
                                            {{ Str::limit($item->title, 70) }}
                                        </a>
                                    </h5>
                                    <p class="card-text text-muted small">{{ Str::limit($item->summary ?? $item->body, 100) }}</p>
                                </div>
                                <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center">
                                    <span class="text-muted small"><i class="far fa-clock"></i> {{ $item->created_at->diffForHumans() }}</span>
                                    <span class="text-muted small"><i class="far fa-eye"></i> {{ number_format($item->views ?? 0) }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $news->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                    <p class="text-muted">इस श्रेणी में अभी कोई खबर नहीं है।</p>
                    <a href="{{ url('/') }}" class="btn btn-primary">होम पेज पर जाएं</a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
        transition: all 0.3s ease;
    }
    .hover-text-brand:hover {
        color: #c62828 !important;
    }
    .border-left-4 {
        border-left-width: 4px !important;
    }
    .transition {
        transition: all 0.3s ease;
    }
</style>
@endsection