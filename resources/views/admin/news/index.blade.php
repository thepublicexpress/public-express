@extends('layouts.admin')

@section('title', 'News Management')

@section('content')
<div class="container-fluid px-0">

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h5 class="mb-0">📰 All News</h5>
            <small class="text-muted">Manage all news articles</small>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.news.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-sm-4">
                    <input type="text" name="search" class="form-control form-control-sm" placeholder="Search news..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-3">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3">
                    <select name="category" class="form-select form-select-sm">
                        <option value="">All Categories</option>
                        @foreach($categories ?? [] as $cat)
                            <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-2">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-4 col-sm-2">
            <div class="card bg-light text-center py-2">
                <small class="text-muted">Total</small>
                <strong>{{ $news->total() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-2">
            <div class="card bg-success text-white text-center py-2">
                <small>Published</small>
                <strong>{{ $news->where('status', 'published')->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-2">
            <div class="card bg-warning text-dark text-center py-2">
                <small>Pending</small>
                <strong>{{ $news->where('status', 'pending')->count() }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-danger text-white text-center py-2">
                <small>Rejected</small>
                <strong>{{ $news->where('status', 'rejected')->count() }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-secondary text-white text-center py-2">
                <small>Draft</small>
                <strong>{{ $news->where('status', 'draft')->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- News Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Title</th>
                            <th class="d-none d-lg-table-cell">Reporter</th>
                            <th>Status</th>
                            <th class="d-none d-sm-table-cell">Views</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>
                                <a href="{{ route('admin.news.show', $item) }}" class="text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($item->title, 40) }}
                                </a>
                                @if($item->rejection_reason && $item->status == 'rejected')
                                    <br><small class="text-danger">Reason: {{ Str::limit($item->rejection_reason, 30) }}</small>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">{{ $item->user->name ?? 'N/A' }}</td>
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
                            <td class="d-none d-sm-table-cell">{{ number_format($item->views ?? 0) }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.news.show', $item) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @if($item->status == 'pending')
                                        <form method="POST" action="{{ route('admin.news.approve', $item) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success" title="Approve" onclick="return confirm('Approve this news? Reporter will get 10 points.')">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($item->status == 'pending' || $item->status == 'published')
                                        <button type="button" class="btn btn-danger" title="Reject" data-bs-toggle="modal" 
                                                data-bs-target="#rejectModal{{ $item->id }}">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    @endif
                                    <form method="POST" action="{{ route('admin.news.destroy', $item) }}" style="display:inline" 
                                          onsubmit="return confirm('Delete this news?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-dark" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        <!-- ===== REJECT MODAL ===== -->
                        <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form method="POST" action="{{ route('admin.news.reject', $item) }}">
                                        @csrf
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title"><i class="fas fa-times-circle"></i> Reject News</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="fw-bold">News Title</label>
                                                <p class="text-muted">{{ $item->title }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label class="fw-bold">Reporter</label>
                                                <p class="text-muted">{{ $item->user->name ?? 'N/A' }}</p>
                                            </div>
                                            <div class="mb-3">
                                                <label for="rejection_reason" class="fw-bold">Rejection Reason <span class="text-danger">*</span></label>
                                                <textarea name="rejection_reason" id="rejection_reason" class="form-control" rows="4" 
                                                          placeholder="Reason for rejection (at least 10 characters)..." required></textarea>
                                                <small class="text-muted">This reason will be sent as notification to the reporter.</small>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-times"></i> Reject News
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-newspaper fa-2x d-block mb-2"></i>
                                No news found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-md-none">
                @forelse($news as $item)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                <a href="{{ route('admin.news.show', $item) }}" class="text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($item->title, 50) }}
                                </a>
                            </h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted">
                                <span>👤 {{ $item->user->name ?? 'N/A' }}</span>
                                <span>👁️ {{ number_format($item->views ?? 0) }}</span>
                            </div>
                            @if($item->rejection_reason && $item->status == 'rejected')
                                <div class="text-danger small mt-1">Reason: {{ Str::limit($item->rejection_reason, 50) }}</div>
                            @endif
                        </div>
                        <div>
                            @if($item->status == 'published')
                                <span class="badge-published">✅</span>
                            @elseif($item->status == 'pending')
                                <span class="badge-pending">⏳</span>
                            @elseif($item->status == 'rejected')
                                <span class="badge-rejected">❌</span>
                            @else
                                <span class="badge-draft">📝</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <a href="{{ route('admin.news.show', $item) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('admin.news.edit', $item) }}" class="btn btn-sm btn-warning">Edit</a>
                        @if($item->status == 'pending')
                            <form method="POST" action="{{ route('admin.news.approve', $item) }}" style="display:inline">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Approve?')">Approve</button>
                            </form>
                        @endif
                        @if($item->status == 'pending' || $item->status == 'published')
                            <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" 
                                    data-bs-target="#rejectModal{{ $item->id }}">Reject</button>
                        @endif
                        <form method="POST" action="{{ route('admin.news.destroy', $item) }}" style="display:inline" 
                              onsubmit="return confirm('Delete?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-dark">Delete</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-newspaper fa-2x d-block mb-2"></i>
                    No news found
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-3 d-flex justify-content-center">
        {{ $news->appends(request()->query())->links() }}
    </div>
</div>
@endsection