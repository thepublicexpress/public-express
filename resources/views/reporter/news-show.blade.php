@extends('layouts.reporter')

@section('title', 'खबर देखें')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h1 class="h3 mb-4 text-gray-800">📰 खबर देखें</h1>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">खबर की विस्तृत जानकारी</h6>
                    <div>
                        <a href="{{ route('reporter.news.edit', $news->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i> संपादित करें
                        </a>
                        <a href="{{ route('reporter.news.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left"></i> वापस
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Featured Image -->
                    @if($news->featured_image)
                        <div class="text-center mb-4">
                            <img src="{{ asset($news->featured_image) }}" alt="{{ $news->alt_text ?? $news->title }}" 
                                 class="img-fluid rounded" style="max-height: 400px; width: 100%; object-fit: cover;">
                        </div>
                    @endif

                    <!-- Category & Badges -->
                    <div class="mb-3">
                        @if($news->category)
                            <span class="badge bg-primary">{{ $news->category->name }}</span>
                        @endif
                        @if($news->is_breaking)
                            <span class="badge bg-danger">🔴 BREAKING</span>
                        @endif
                        @if($news->is_featured)
                            <span class="badge bg-warning text-dark">⭐ फीचर्ड</span>
                        @endif
                        <span class="badge bg-{{ $news->status == 'published' ? 'success' : ($news->status == 'draft' ? 'secondary' : 'warning') }}">
                            {{ $news->status == 'published' ? '✅ प्रकाशित' : ($news->status == 'draft' ? '📝 ड्राफ्ट' : '⏳ समीक्षा के लिए') }}
                        </span>
                        @if($news->approval_status == 'approved')
                            <span class="badge bg-success">✅ स्वीकृत</span>
                        @elseif($news->approval_status == 'rejected')
                            <span class="badge bg-danger">❌ अस्वीकृत</span>
                        @else
                            <span class="badge bg-warning text-dark">⏳ लंबित</span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h2 class="mb-3">{{ $news->title }}</h2>

                    <!-- Reporter Info (with Photo & Bio) -->
                    <div class="row mb-4 p-3 bg-light rounded">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                @if($news->user && $news->user->photo_url)
                                    <img src="{{ $news->user->photo_url }}" alt="{{ $news->user->name }}" 
                                         style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover; margin-right: 12px;">
                                @else
                                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; margin-right: 12px; font-size: 20px;">
                                        👤
                                    </div>
                                @endif
                                <div>
                                    <strong>{{ $news->user->name ?? 'N/A' }}</strong>
                                    @if($news->user && $news->user->bio)
                                        <br><small class="text-muted">{{ Str::limit($news->user->bio, 60) }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <p class="mb-0"><strong>📅 प्रकाशित:</strong> {{ $news->published_at ? $news->published_at->format('d M Y, h:i A') : 'अभी तक नहीं' }}</p>
                            <p class="mb-0"><strong>🔄 अपडेट:</strong> {{ $news->updated_at->format('d M Y, h:i A') }}</p>
                            <p class="mb-0"><strong>👁️ व्यूज:</strong> {{ number_format($news->views ?? 0) }}</p>
                        </div>
                    </div>

                    <!-- Full Location Hierarchy -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-2">
                                @if($news->state)
                                    <span class="badge bg-secondary">🏛️ {{ $news->state->name }}</span>
                                @endif
                                @if($news->district)
                                    <span class="badge bg-secondary">🏛️ {{ $news->district->name }}</span>
                                @endif
                                @if($news->tehsil)
                                    <span class="badge bg-secondary">🏛️ {{ $news->tehsil->name }}</span>
                                @endif
                                @if($news->block)
                                    <span class="badge bg-secondary">🏛️ {{ $news->block->name }}</span>
                                @endif
                                @if(!$news->state && !$news->district && !$news->tehsil && !$news->block)
                                    <span class="text-muted">📍 कोई स्थान निर्दिष्ट नहीं</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    @if($news->summary)
                        <div class="alert alert-info">
                            <strong>📌 सारांश:</strong><br>
                            {{ $news->summary }}
                        </div>
                    @endif

                    <!-- Body -->
                    <div class="news-body">
                        {!! $news->body !!}
                    </div>

                    <!-- Video -->
                    @if($news->type == 'video' && $news->video_url)
                        <div class="mt-4">
                            <h5>🎬 वीडियो</h5>
                            <div class="ratio ratio-16x9">
                                <iframe src="{{ $news->video_url }}" allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif

                    <!-- Short (Reels/Shorts) -->
                    @if($news->type == 'short' && $news->video_url)
                        <div class="mt-4">
                            <h5>📱 शॉर्ट वीडियो</h5>
                            <div class="ratio ratio-9x16" style="max-width: 400px; margin: 0 auto;">
                                <iframe src="{{ $news->video_url }}" allowfullscreen></iframe>
                            </div>
                        </div>
                    @endif

                    <!-- Approval Info -->
                    @if($news->approval_status == 'approved' || $news->approval_status == 'rejected')
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold">📋 अनुमोदन जानकारी</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>स्थिति:</strong> 
                                        @if($news->approval_status == 'approved')
                                            <span class="text-success">✅ स्वीकृत</span>
                                        @else
                                            <span class="text-danger">❌ अस्वीकृत</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    @if($news->approved_at)
                                        <p><strong>अनुमोदित दिनांक:</strong> {{ $news->approved_at->format('d M Y, h:i A') }}</p>
                                    @endif
                                    @if($news->approver)
                                        <p><strong>अनुमोदक:</strong> {{ $news->approver->name }}</p>
                                    @endif
                                </div>
                            </div>
                            @if($news->rejection_reason)
                                <div class="alert alert-danger">
                                    <strong>❌ अस्वीकृति कारण:</strong><br>
                                    {{ $news->rejection_reason }}
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- SEO Info -->
                    <div class="mt-4 pt-3 border-top">
                        <h6 class="fw-bold">🔍 SEO जानकारी</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Slug:</strong> <code>{{ $news->slug }}</code></p>
                                <p><strong>Alt Text:</strong> {{ $news->alt_text ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>SEO Description:</strong> {{ $news->seo_description ?? 'N/A' }}</p>
                                <p><strong>Type:</strong> {{ $news->type == 'video' ? '🎬 वीडियो' : ($news->type == 'short' ? '📱 शॉर्ट' : '📝 टेक्स्ट') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">⚡ त्वरित कार्रवाई</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('reporter.news.edit', $news->id) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> संपादित करें
                        </a>
                        
                        @if($news->status == 'published' || $news->status == 'rejected')
                            <form action="{{ route('reporter.news.resubmit', $news->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-info w-100">
                                    <i class="fas fa-redo"></i> पुनः सबमिट करें
                                </button>
                            </form>
                        @endif
                        
                        <form action="{{ route('reporter.news.destroy', $news->id) }}" method="POST" 
                              onsubmit="return confirm('क्या आप इस खबर को डिलीट करना चाहते हैं?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fas fa-trash"></i> डिलीट करें
                            </button>
                        </form>
                        <a href="{{ route('reporter.news.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> सभी खबरें
                        </a>
                    </div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">📊 आंकड़े</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <h3 class="text-primary">{{ number_format($news->views ?? 0) }}</h3>
                        <p class="text-muted">कुल व्यूज</p>
                        <hr>
                        <p><strong>Status:</strong> {{ $news->status == 'published' ? '✅ प्रकाशित' : ($news->status == 'draft' ? '📝 ड्राफ्ट' : '⏳ समीक्षा के लिए') }}</p>
                        <p><strong>Type:</strong> {{ $news->type == 'video' ? '🎬 वीडियो' : ($news->type == 'short' ? '📱 शॉर्ट' : '📝 टेक्स्ट') }}</p>
                        @if($news->type != 'text' && $news->video_url)
                            <p><strong>Video URL:</strong> <a href="{{ $news->video_url }}" target="_blank">देखें</a></p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reporter Info (if not already shown in main) -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">👤 रिपोर्टर</h6>
                </div>
                <div class="card-body text-center">
                    @if($news->user && $news->user->photo_url)
                        <img src="{{ $news->user->photo_url }}" alt="{{ $news->user->name }}" 
                             style="width: 80px; height: 80px; border-radius: 50%; object-fit: cover; margin-bottom: 10px;">
                    @else
                        <div style="width: 80px; height: 80px; border-radius: 50%; background: #ddd; display: flex; align-items: center; justify-content: center; margin: 0 auto 10px; font-size: 32px;">
                            👤
                        </div>
                    @endif
                    <h6>{{ $news->user->name ?? 'N/A' }}</h6>
                    <p class="text-muted small">{{ $news->user->email ?? '' }}</p>
                    @if($news->user && $news->user->bio)
                        <p class="small">{{ Str::limit($news->user->bio, 100) }}</p>
                    @endif
                    @if($news->user && $news->user->getLocationString())
                        <p class="small text-muted">📍 {{ $news->user->getLocationString() }}</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection