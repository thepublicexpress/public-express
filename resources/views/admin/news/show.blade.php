@extends('layouts.admin')
@section('title', 'News Details - ' . $news->title)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">📰 News Details</h5>
            <small class="text-muted">{{ Str::limit($news->title, 60) }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.news.edit', $news) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <!-- News Content -->
            <div class="card mb-3">
                <div class="card-body">
                    <h2 class="fw-bold">{{ $news->title }}</h2>
                    
                    @if($news->featured_image)
                        <img src="{{ $news->image_url }}" alt="{{ $news->title }}" 
                             class="img-fluid rounded mb-3" style="max-height:400px;width:100%;object-fit:cover;">
                    @endif

                    @if($news->summary)
                        <div class="bg-light p-3 rounded mb-3 border-start border-4 border-primary">
                            <strong>Summary:</strong> {{ $news->summary }}
                        </div>
                    @endif

                    <div class="news-body" style="line-height:1.8;font-size:15px;">
                        {!! nl2br(e($news->body)) !!}
                    </div>

                    @if($news->video_url)
                        <div class="mt-3">
                            <a href="{{ $news->video_url }}" target="_blank" class="btn btn-danger">
                                <i class="fas fa-play"></i> Watch Video
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Approval History -->
            <div class="card">
                <div class="card-header">📋 Approval History</div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Status:</strong></span>
                            <span>
                                @if($news->status == 'published')
                                    <span class="badge-published">✅ Published</span>
                                @elseif($news->status == 'pending')
                                    <span class="badge-pending">⏳ Pending</span>
                                @elseif($news->status == 'rejected')
                                    <span class="badge-rejected">❌ Rejected</span>
                                @else
                                    <span class="badge-draft">📝 Draft</span>
                                @endif
                            </span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>Submitted:</strong></span>
                            <span>{{ $news->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        @if($news->published_at)
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>Published:</strong></span>
                                <span>{{ $news->published_at->format('d M Y, h:i A') }}</span>
                            </div>
                        @endif
                        @if($news->approved_by)
                            <div class="d-flex justify-content-between mb-2">
                                <span><strong>Approved By:</strong></span>
                                <span>{{ $news->approver->name ?? 'N/A' }} ({{ $news->approved_by_level ?? 'N/A' }})</span>
                            </div>
                        @endif
                        @if($news->rejection_reason)
                            <div class="alert alert-danger mt-2">
                                <strong>Rejection Reason:</strong><br>
                                {{ $news->rejection_reason }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Location Info -->
            <div class="card mb-3">
                <div class="card-header">📍 Location Info</div>
                <div class="card-body">
                    <p><strong>State:</strong> {{ $news->state->name ?? 'N/A' }}</p>
                    <p><strong>District:</strong> {{ $news->district->name ?? 'N/A' }}</p>
                    <p><strong>Tehsil:</strong> {{ $news->tehsil->name ?? 'N/A' }}</p>
                    <p><strong>Block:</strong> {{ $news->block->name ?? 'N/A' }}</p>
                    <p><strong>Level:</strong> {{ ucfirst($news->location_level) }}</p>
                    @if($news->is_national)
                        <span class="badge bg-primary">🌍 National</span>
                    @endif
                    @if($news->is_state)
                        <span class="badge bg-info">🏛️ State</span>
                    @endif
                </div>
            </div>

            <!-- Reporter Info -->
            <div class="card mb-3">
                <div class="card-header">👤 Reporter Info</div>
                <div class="card-body">
                    <p><strong>Name:</strong> {{ $news->user->name ?? 'N/A' }}</p>
                    <p><strong>Email:</strong> {{ $news->user->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $news->user->phone ?? 'N/A' }}</p>
                    <p><strong>Points:</strong> {{ number_format($news->user->points ?? 0) }}</p>
                    <a href="{{ route('admin.users.show', $news->user) }}" class="btn btn-sm btn-primary">
                        View Reporter
                    </a>
                </div>
            </div>

            <!-- Stats -->
            <div class="card mb-3">
                <div class="card-header">📊 Stats</div>
                <div class="card-body">
                    <p><strong>Views:</strong> {{ number_format($news->views ?? 0) }}</p>
                    <p><strong>Likes:</strong> {{ number_format($news->likes ?? 0) }}</p>
                    <p><strong>Shares:</strong> {{ number_format($news->shares ?? 0) }}</p>
                    @if($news->is_breaking)
                        <span class="badge bg-danger">🔴 Breaking News</span>
                    @endif
                    @if($news->is_featured)
                        <span class="badge bg-warning">⭐ Featured</span>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header">⚡ Actions</div>
                <div class="card-body">
                    @if($news->status == 'pending')
                        <form action="{{ route('admin.news.approve', $news) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm w-100 mb-2">
                                <i class="fas fa-check"></i> Approve (+10 Points)
                            </button>
                        </form>
                    @endif

                    @if($news->status == 'pending' || $news->status == 'published')
                        <button type="button" class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" 
                                data-bs-target="#rejectModal">
                            <i class="fas fa-times"></i> Reject
                        </button>
                    @endif

                    <form action="{{ route('admin.news.breaking', $news) }}" method="POST" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="btn btn-warning btn-sm w-100 mb-2">
                            <i class="fas fa-bolt"></i> 
                            {{ $news->is_breaking ? 'Remove Breaking' : 'Make Breaking' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.news.featured', $news) }}" method="POST" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="btn btn-info btn-sm w-100 mb-2">
                            <i class="fas fa-star"></i> 
                            {{ $news->is_featured ? 'Remove Featured' : 'Make Featured' }}
                        </button>
                    </form>

                    <form action="{{ route('admin.news.destroy', $news) }}" method="POST" class="d-inline w-100"
                          onsubmit="return confirm('Delete this news?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-dark btn-sm w-100">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('admin.news.reject', $news) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-times-circle"></i> Reject News</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>News:</strong> {{ $news->title }}</p>
                    <p><strong>Reporter:</strong> {{ $news->user->name ?? 'N/A' }}</p>
                    <div class="mb-3">
                        <label class="fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" 
                                  placeholder="Reason for rejection..." required></textarea>
                        <small class="text-muted">This will be sent as notification to the reporter.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject News</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection