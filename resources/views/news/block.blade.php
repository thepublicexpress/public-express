@extends('layouts.app')
@section('title', $block->name . ' - Block News')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">State</a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/news/district/' . ($block->tehsil->district->slug ?? '')) }}">
                        {{ $block->tehsil->district->name ?? '' }}
                    </a></li>
                    <li class="breadcrumb-item"><a href="{{ url('/news/tehsil/' . ($block->tehsil->slug ?? '')) }}">
                        {{ $block->tehsil->name ?? '' }}
                    </a></li>
                    <li class="breadcrumb-item active">{{ $block->name }}</li>
                </ol>
            </nav>

            <h1 class="mb-4 border-l-4 border-red-600 pl-3 text-2xl font-bold">
                📍 {{ $block->name }} ब्लॉक की खबरें
            </h1>

            <div class="row">
                <div class="col-md-8">
                    @if($news && $news->count() > 0)
                        <div class="row g-3">
                            @foreach($news as $item)
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="row g-0">
                                        @if($item->featured_image)
                                        <div class="col-md-4">
                                            <img src="{{ $item->image_url }}" class="img-fluid rounded-start" 
                                                 style="height:200px;width:100%;object-fit:cover;" alt="{{ $item->title }}">
                                        </div>
                                        @endif
                                        <div class="col-md-{{ $item->featured_image ? '8' : '12' }}">
                                            <div class="card-body">
                                                <h5 class="card-title">
                                                    <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
                                                        {{ $item->title }}
                                                    </a>
                                                </h5>
                                                <p class="card-text text-muted small">{{ Str::limit($item->summary ?? $item->excerpt, 120) }}</p>
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <div>
                                                        <span class="badge bg-primary">{{ $item->category->name ?? 'General' }}</span>
                                                        <span class="text-muted ms-2"><i class="far fa-clock"></i> {{ $item->created_at->diffForHumans() }}</span>
                                                    </div>
                                                    <span class="text-muted"><i class="far fa-eye"></i> {{ number_format($item->views ?? 0) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4">
                            {{ $news->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No news found for this block.</p>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <!-- Block Info -->
                    <div class="card mb-3">
                        <div class="card-header bg-light fw-bold">📍 Block Information</div>
                        <div class="card-body">
                            <p><strong>Name:</strong> {{ $block->name }}</p>
                            <p><strong>Tehsil:</strong> {{ $block->tehsil->name ?? 'N/A' }}</p>
                            <p><strong>District:</strong> {{ $block->tehsil->district->name ?? 'N/A' }}</p>
                            <p><strong>State:</strong> {{ $block->tehsil->district->state->name ?? 'N/A' }}</p>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="card mb-3">
                        <div class="card-header bg-light fw-bold">🏷️ Categories</div>
                        <div class="card-body">
                            @php
                                $categories = App\Models\Category::where('is_active', true)->get();
                            @endphp
                            @foreach($categories as $cat)
                                <a href="{{ route('category.show', $cat->slug) }}" class="badge bg-secondary me-1 mb-1 text-decoration-none">
                                    {{ $cat->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>

                    <!-- Top Reporters -->
                    <div class="card">
                        <div class="card-header bg-light fw-bold">🏆 Top Reporters</div>
                        <div class="card-body">
                            @php
                                $topReporters = App\Models\User::where('role', 'reporter')
                                    ->where('is_active', true)
                                    ->orderBy('points', 'desc')
                                    ->limit(5)
                                    ->get();
                            @endphp
                            @foreach($topReporters as $reporter)
                                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                                    <span>{{ $reporter->name }}</span>
                                    <span class="badge bg-warning text-dark">⭐ {{ number_format($reporter->points ?? 0) }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection