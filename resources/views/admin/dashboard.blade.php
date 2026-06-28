@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Users</div>
                    <div class="h3 mb-0 fw-bold">{{ $stats['total_users'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Reporters</div>
                    <div class="h3 mb-0 fw-bold text-primary">{{ $stats['total_reporters'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">State Reporters</div>
                    <div class="h3 mb-0 fw-bold text-info">{{ $stats['state_reporters'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">District Reporters</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ $stats['district_reporters'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Tehsil Reporters</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ $stats['tehsil_reporters'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Block Reporters</div>
                    <div class="h3 mb-0 fw-bold text-secondary">{{ $stats['block_reporters'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total News</div>
                    <div class="h3 mb-0 fw-bold">{{ $stats['total_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Pending News</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ $stats['pending_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Published</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ $stats['published_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Rejected</div>
                    <div class="h3 mb-0 fw-bold text-danger">{{ $stats['rejected_news'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Total Views</div>
                    <div class="h3 mb-0 fw-bold">{{ number_format($stats['total_views'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">Pending Withdrawals</div>
                    <div class="h3 mb-0 fw-bold text-orange">{{ $stats['pending_withdrawals'] ?? 0 }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent News -->
    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>📰 Recent News</span>
            <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if(isset($recent_news) && $recent_news->count() > 0)
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th class="d-none d-md-table-cell">Reporter</th>
                            <th>Status</th>
                            <th class="d-none d-sm-table-cell">Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_news as $item)
                        <tr>
                            <td>{{ \Illuminate\Support\Str::limit($item->title, 40) }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->user->name ?? 'Unknown' }}</td>
                            <td>
                                @if($item->status == 'published')
                                    <span class="badge-published">Published</span>
                                @elseif($item->status == 'pending')
                                    <span class="badge-pending">Pending</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge-rejected">Rejected</span>
                                @else
                                    <span class="badge-draft">Draft</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.news.show', $item) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-4">No recent news found</div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Users -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>👥 Recent Users</span>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                @if(isset($recent_users) && $recent_users->count() > 0)
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th class="d-none d-sm-table-cell">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent_users as $user)
                        <tr>
                            <td>{{ $user->name ?? 'N/A' }}</td>
                            <td class="d-none d-md-table-cell">{{ $user->email ?? $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if($user->role == 'admin' || $user->role == 'super_admin')
                                    <span class="badge bg-danger">{{ $user->role }}</span>
                                @elseif(in_array($user->role, ['state_admin', 'district_admin', 'tehsil_admin', 'block_admin']))
                                    <span class="badge bg-warning text-dark">{{ str_replace('_', ' ', $user->role) }}</span>
                                @elseif(in_array($user->role, ['state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter']))
                                    <span class="badge bg-primary">{{ str_replace('_', ' ', $user->role) }}</span>
                                @elseif($user->role == 'reporter')
                                    <span class="badge bg-primary">Reporter</span>
                                @else
                                    <span class="badge bg-secondary">Subscriber</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">{{ $user->created_at->format('d-m-Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="text-center text-muted py-4">No recent users found</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection