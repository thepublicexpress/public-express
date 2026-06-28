@extends('layouts.admin')
@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">👤 User Details</h5>
            <small class="text-muted">{{ $user->name }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
        </div>
    </div>

    <div class="row">
        <!-- User Info -->
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-3" style="width:80px;height:80px;border-radius:50%;background:#c62828;display:flex;align-items:center;justify-content:center;font-size:32px;color:white;font-weight:700;">
                        {{ substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <h5 class="fw-bold">{{ $user->name }}</h5>
                    <p class="text-muted small">{{ $user->role_label ?? $user->role }}</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        @if($user->is_active)
                            <span class="badge bg-success">✅ Active</span>
                        @else
                            <span class="badge bg-danger">❌ Inactive</span>
                        @endif
                        @if($user->is_approved)
                            <span class="badge bg-success">Approved</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                        @if($user->is_verified)
                            <span class="badge bg-info">Verified</span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-header">📋 Contact Info</div>
                <div class="card-body">
                    <p><strong>Email:</strong> {{ $user->email ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $user->phone ?? 'N/A' }}</p>
                    <p><strong>Joined:</strong> {{ $user->created_at->format('d M Y, h:i A') }}</p>
                </div>
            </div>
        </div>

        <!-- Location & Stats -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header">📍 Assigned Location</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>State:</strong> {{ $user->state->name ?? 'N/A' }}</p>
                            <p><strong>District:</strong> {{ $user->district->name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Tehsil:</strong> {{ $user->tehsil->name ?? 'N/A' }}</p>
                            <p><strong>Block:</strong> {{ $user->block->name ?? 'N/A' }}</p>
                        </div>
                    </div>
                    <p><strong>Location Level:</strong> {{ ucfirst($user->getLocationLevel()) }}</p>
                    <p><strong>Approval Level:</strong> {{ $user->approval_level_label ?? 'None' }}</p>
                    @if($user->can_approve)
                        <span class="badge bg-success">Can Approve</span>
                    @else
                        <span class="badge bg-secondary">Cannot Approve</span>
                    @endif
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">⭐ Points & Wallet</div>
                        <div class="card-body">
                            <h3 class="fw-bold text-primary">{{ number_format($user->points ?? 0) }}</h3>
                            <p class="text-muted">Total Points</p>
                            <hr>
                            <h3 class="fw-bold text-success">₹{{ number_format($user->wallet_balance ?? 0, 2) }}</h3>
                            <p class="text-muted">Wallet Balance</p>
                            @if($user->upi_id)
                                <p><strong>UPI ID:</strong> {{ $user->upi_id }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card mb-3">
                        <div class="card-header">📰 News Stats</div>
                        <div class="card-body">
                            <p><strong>Total News:</strong> {{ $user->news()->count() }}</p>
                            <p><strong>Published:</strong> {{ $user->news()->where('status', 'published')->count() }}</p>
                            <p><strong>Pending:</strong> {{ $user->news()->where('status', 'pending')->count() }}</p>
                            <p><strong>Rejected:</strong> {{ $user->news()->where('status', 'rejected')->count() }}</p>
                            <p><strong>Total Views:</strong> {{ number_format($user->news()->sum('views')) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignments -->
            <div class="card mb-3">
                <div class="card-header">📋 Reporter Assignments</div>
                <div class="card-body p-0">
                    @if($assignments && $assignments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm mb-0">
                                <thead>
                                    <tr>
                                        <th>Location</th>
                                        <th>Category</th>
                                        <th>Status</th>
                                        <th>Assigned At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assignments as $assignment)
                                    <tr>
                                        <td>{{ $assignment->getLocationLabel() }}</td>
                                        <td>{{ $assignment->assignedCategory->name ?? 'All' }}</td>
                                        <td>
                                            @if($assignment->is_active)
                                                <span class="badge bg-success">Active</span>
                                            @else
                                                <span class="badge bg-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>{{ $assignment->assigned_at ? $assignment->assigned_at->format('d M Y') : 'N/A' }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-3">No assignments found</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- News List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span>📰 Recent News by {{ $user->name }}</span>
        </div>
        <div class="card-body p-0">
            @if($news && $news->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Views</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($news as $item)
                            <tr>
                                <td>{{ Str::limit($item->title, 50) }}</td>
                                <td>{{ $item->category->name ?? 'N/A' }}</td>
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
                                    <a href="{{ route('admin.news.show', $item) }}" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $news->links() }}
                </div>
            @else
                <div class="text-center text-muted py-4">No news found</div>
            @endif
        </div>
    </div>
</div>
@endsection