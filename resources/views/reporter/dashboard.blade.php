@extends('layouts.reporter')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">

    <!-- Welcome Card -->
    <div class="card bg-gradient-to-r from-red-600 to-red-800 text-white mb-4" style="background: linear-gradient(135deg, #c62828, #8e0000);">
        <div class="card-body">
            <h4 class="fw-bold mb-1">👋 Welcome, {{ $user->name ?? 'Reporter' }}!</h4>
            <p class="mb-0 opacity-75">Here's your reporting dashboard</p>
        </div>
    </div>

    <!-- Monetisation Status -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-chart-line"></i> Monetisation Status
        </div>
        <div class="card-body">
            @if($monetisationMessage)
                <div class="alert {{ $allRequirementsMet ? 'alert-success' : 'alert-warning' }} mb-3">
                    {!! $monetisationMessage !!}
                </div>
            @endif

            @if(!$walletVisible)
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle"></i>
                    <strong>Monetisation Requirements:</strong>
                    <ul class="mt-2 mb-0">
                        <li>✅ 100+ Followers</li>
                        <li>✅ 100,000+ Unique Views</li>
                        <li>✅ Admin Approval</li>
                    </ul>
                    <small class="text-muted">Once you meet all requirements, admin will review and activate monetisation.</small>
                </div>
            @endif

            @if(isset($monetisationRequirements) && count($monetisationRequirements) > 0)
                <div class="row mt-3">
                    @foreach($monetisationRequirements as $key => $req)
                        <div class="col-md-4 mb-2">
                            <div class="card {{ $req['met'] ? 'border-success' : 'border-warning' }}">
                                <div class="card-body text-center py-2">
                                    <h5 class="mb-0 {{ $req['met'] ? 'text-success' : 'text-warning' }}">
                                        {{ $req['met'] ? '✅' : '⏳' }}
                                    </h5>
                                    <p class="mb-0"><strong>{{ $req['label'] }}</strong></p>
                                    <p class="mb-0">
                                        {{ number_format($req['current']) }} / {{ number_format($req['required']) }}
                                    </p>
                                    @if($req['met'])
                                        <span class="badge bg-success">Met</span>
                                    @else
                                        <span class="badge bg-warning">Pending</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Total News</div>
                    <div class="h3 mb-0 fw-bold">{{ $stats['total_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Published</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ $stats['published_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Pending</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ $stats['pending_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Total Views</div>
                    <div class="h3 mb-0 fw-bold text-primary">{{ number_format($stats['total_views'] ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Stats (Followers) -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Followers</div>
                    <div class="h3 mb-0 fw-bold text-info">{{ number_format($monetisation->total_followers ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body text-center">
                    <div class="text-muted small text-uppercase">Unique Views</div>
                    <div class="h3 mb-0 fw-bold text-purple">{{ number_format($monetisation->unique_views ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-2 mb-4">
        <div class="col-6 col-md-3">
            <a href="{{ route('reporter.news.create') }}" class="btn btn-primary w-100 py-2">
                <i class="fas fa-plus-circle"></i> New News
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('reporter.news.index') }}" class="btn btn-secondary w-100 py-2">
                <i class="fas fa-newspaper"></i> My News
            </a>
        </div>
        @if($canAccessWallet && $walletVisible)
            <div class="col-6 col-md-3">
                <a href="{{ route('reporter.wallet') }}" class="btn btn-success w-100 py-2">
                    <i class="fas fa-wallet"></i> Wallet
                </a>
            </div>
        @endif
        <div class="col-6 col-md-3">
            <a href="{{ route('reporter.profile') }}" class="btn btn-info w-100 py-2">
                <i class="fas fa-user"></i> Profile
            </a>
        </div>
    </div>

    <!-- Recent News -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>📰 Recent News</span>
            <a href="{{ route('reporter.news.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            @if($recent_news && $recent_news->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recent_news as $item)
                            <tr>
                                <td>{{ Str::limit($item->title, 40) }}</td>
                                <td>
                                    @if($item->status == 'published')
                                        <span class="badge-published">✅ Published</span>
                                    @elseif($item->status == 'pending')
                                        <span class="badge-pending">⏳ Pending</span>
                                    @elseif($item->status == 'rejected')
                                        <span class="badge-rejected">❌ Rejected</span>
                                    @else
                                        <span class="badge-draft">📝 Draft</span>
                                    @endif
                                </td>
                                <td>{{ number_format($item->views ?? 0) }}</td>
                                <td>{{ $item->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('reporter.news.show', $item->id) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if(in_array($item->status, ['pending', 'draft']))
                                        <a href="{{ route('reporter.news.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-newspaper fa-2x d-block mb-2"></i>
                    No news submitted yet. <a href="{{ route('reporter.news.create') }}">Submit your first news</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection