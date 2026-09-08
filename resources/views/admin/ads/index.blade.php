@extends('layouts.admin')

@section('title', 'Manage Ads - द पब्लिक एक्सप्रेस')

@push('styles')
<style>
    /* ✅ Mobile Friendly Table */
    @media (max-width: 768px) {
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .table td, .table th {
            white-space: nowrap;
            font-size: 12px;
            padding: 8px 6px;
        }
        .btn-group-sm .btn {
            padding: 2px 6px;
            font-size: 10px;
        }
        .card-body {
            padding: 10px !important;
        }
        .filter-row .col-md-3 {
            margin-bottom: 8px;
        }
        .ad-title {
            max-width: 120px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            display: inline-block;
        }
        .badge {
            font-size: 9px;
            padding: 3px 6px;
        }
        .modal-dialog {
            margin: 10px;
        }
        .modal-content {
            border-radius: 12px;
        }
    }

    /* ✅ Mobile Card View for Ads */
    .ad-card-mobile {
        display: none;
    }

    @media (max-width: 576px) {
        .ad-table-view {
            display: none;
        }
        .ad-card-mobile {
            display: block;
        }
        .ad-card-item {
            background: white;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            border: 1px solid #eef2f6;
        }
        .ad-card-item .ad-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 8px;
        }
        .ad-card-item .ad-title-mobile {
            font-weight: 700;
            font-size: 14px;
            color: #0f172a;
            flex: 1;
        }
        .ad-card-item .ad-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin: 8px 0;
        }
        .ad-card-item .ad-meta .badge {
            font-size: 10px;
        }
        .ad-card-item .ad-actions {
            display: flex;
            gap: 6px;
            margin-top: 10px;
            flex-wrap: wrap;
        }
        .ad-card-item .ad-actions .btn {
            font-size: 11px;
            padding: 4px 10px;
        }
        .ad-card-item .ad-stats {
            font-size: 11px;
            color: #64748b;
            margin-top: 6px;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-2 px-md-3">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <h4 class="mb-2 mb-md-0">📢 Manage Ads</h4>
        <a href="{{ route('admin.ads.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Create Ad
        </a>
    </div>

    <!-- ✅ Stats Summary -->
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body py-2 px-3">
                    <h6 class="mb-0">{{ \App\Models\Ad::count() }}</h6>
                    <small>Total Ads</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body py-2 px-3">
                    <h6 class="mb-0">{{ \App\Models\Ad::where('status', 'active')->where('is_active', 1)->count() }}</h6>
                    <small>Active</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body py-2 px-3">
                    <h6 class="mb-0">{{ \App\Models\Ad::where('status', 'inactive')->count() }}</h6>
                    <small>Inactive</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body py-2 px-3">
                    <h6 class="mb-0">{{ \App\Models\Ad::where('position', 'header')->count() }}</h6>
                    <small>Header Ads</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ Filters - Mobile Friendly -->
    <div class="card mb-3">
        <div class="card-body py-2 px-3">
            <form method="GET" action="{{ route('admin.ads.index') }}" class="row g-2 filter-row">
                <div class="col-12 col-md-3">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-md-3">
                    <select name="position" class="form-select form-select-sm">
                        <option value="">All Positions</option>
                        @foreach($positions as $pos)
                            <option value="{{ $pos }}" {{ request('position') == $pos ? 'selected' : '' }}>
                                {{ ucfirst(str_replace('_', ' ', $pos)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        @foreach($statuses as $status)
                            <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ✅ DESKTOP VIEW - Table                                       -->
    <!-- ============================================================ -->
    <div class="card ad-table-view">
        <div class="card-body p-0 p-md-3">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-2" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-2" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Active</th>
                            <th>Stats</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ads as $ad)
                            <tr>
                                <td>{{ $ad->id }}</td>
                                <td>
                                    <span class="ad-title" title="{{ $ad->title }}">
                                        <strong>{{ Str::limit($ad->title, 25) }}</strong>
                                    </span>
                                    @if($ad->description)
                                        <br><small class="text-muted">{{ Str::limit($ad->description, 30) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $ad->type_label ?? $ad->type }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $ad->position_label ?? $ad->position }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ad->status == 'active' ? 'success' : ($ad->status == 'inactive' ? 'danger' : 'warning') }}">
                                        {{ ucfirst($ad->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ad->is_active ? 'success' : 'secondary' }}">
                                        {{ $ad->is_active ? '✅' : '❌' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        👁️ {{ number_format($ad->impressions ?? 0) }}
                                        <br>🖱️ {{ number_format($ad->clicks ?? 0) }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ route('admin.ads.toggle', $ad->id) }}" class="btn btn-{{ $ad->is_active ? 'secondary' : 'success' }}" title="{{ $ad->is_active ? 'Deactivate' : 'Activate' }}">
                                            <i class="fas fa-{{ $ad->is_active ? 'pause' : 'play' }}"></i>
                                        </a>
                                        <a href="{{ route('admin.ads.toggle-status', $ad->id) }}" class="btn btn-{{ $ad->status == 'active' ? 'danger' : 'success' }}">
                                            <i class="fas fa-{{ $ad->status == 'active' ? 'stop' : 'play' }}"></i>
                                        </a>
                                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ad->id }}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fas fa-ad fa-2x d-block mb-2"></i>
                                    No ads found. <a href="{{ route('admin.ads.create') }}">Create your first ad</a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-end mt-2 px-2">
                {{ $ads->appends(request()->query())->links() }}
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- ✅ MOBILE VIEW - Cards                                        -->
    <!-- ============================================================ -->
    <div class="ad-card-mobile">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @forelse($ads as $ad)
            <div class="ad-card-item">
                <div class="ad-header">
                    <span class="ad-title-mobile">{{ Str::limit($ad->title, 30) }}</span>
                    <div>
                        <span class="badge bg-{{ $ad->status == 'active' ? 'success' : ($ad->status == 'inactive' ? 'danger' : 'warning') }}">
                            {{ ucfirst($ad->status) }}
                        </span>
                        <span class="badge bg-{{ $ad->is_active ? 'success' : 'secondary' }}">
                            {{ $ad->is_active ? '✅' : '❌' }}
                        </span>
                    </div>
                </div>
                <div class="ad-meta">
                    <span class="badge bg-info">{{ $ad->type_label ?? $ad->type }}</span>
                    <span class="badge bg-secondary">{{ $ad->position_label ?? $ad->position }}</span>
                    @if($ad->description)
                        <span class="badge bg-light text-dark">{{ Str::limit($ad->description, 40) }}</span>
                    @endif
                </div>
                <div class="ad-stats">
                    👁️ {{ number_format($ad->impressions ?? 0) }} views &nbsp;|&nbsp; 🖱️ {{ number_format($ad->clicks ?? 0) }} clicks
                </div>
                <div class="ad-actions">
                    <a href="{{ route('admin.ads.edit', $ad->id) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Edit
                    </a>
                    <a href="{{ route('admin.ads.toggle', $ad->id) }}" class="btn btn-{{ $ad->is_active ? 'secondary' : 'success' }} btn-sm">
                        <i class="fas fa-{{ $ad->is_active ? 'pause' : 'play' }}"></i>
                        {{ $ad->is_active ? 'Deactivate' : 'Activate' }}
                    </a>
                    <a href="{{ route('admin.ads.toggle-status', $ad->id) }}" class="btn btn-{{ $ad->status == 'active' ? 'danger' : 'success' }} btn-sm">
                        <i class="fas fa-{{ $ad->status == 'active' ? 'stop' : 'play' }}"></i>
                        {{ $ad->status == 'active' ? 'Disable' : 'Enable' }}
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $ad->id }}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        @empty
            <div class="text-center text-muted py-4">
                <i class="fas fa-ad fa-2x d-block mb-2"></i>
                No ads found. <a href="{{ route('admin.ads.create') }}">Create your first ad</a>
            </div>
        @endforelse

        <!-- Pagination Mobile -->
        <div class="mt-3">
            {{ $ads->appends(request()->query())->links() }}
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ✅ DELETE MODALS - One for each ad                            -->
<!-- ============================================================ -->
@foreach($ads as $ad)
<div class="modal fade" id="deleteModal{{ $ad->id }}" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title">Confirm Delete</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Delete <strong>"{{ Str::limit($ad->title, 50) }}"</strong>?</p>
                <p class="text-danger small">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection