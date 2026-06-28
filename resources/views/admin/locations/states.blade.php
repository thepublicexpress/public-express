@extends('layouts.admin')
@section('title', 'States Management')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">🗺️ States Management</h5>
            <small class="text-muted">Manage all states</small>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStateModal">
            <i class="fas fa-plus"></i> Add State
        </button>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-4 col-sm-3">
            <div class="card bg-light text-center py-2">
                <small class="text-muted">Total</small>
                <strong>{{ $states->total() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-success text-white text-center py-2">
                <small>Active</small>
                <strong>{{ $states->where('is_active', true)->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-secondary text-white text-center py-2">
                <small>Inactive</small>
                <strong>{{ $states->where('is_active', false)->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-info text-white text-center py-2">
                <small>Districts</small>
                <strong>{{ $states->sum('districts_count') }}</strong>
            </div>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.locations.states') }}" class="row g-2">
                <div class="col-10 col-sm-8">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search states..." value="{{ request('search') }}">
                </div>
                <div class="col-2 col-sm-4">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- States Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Name (English)</th>
                            <th>Name (Hindi)</th>
                            <th>Districts</th>
                            <th>Status</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($states as $state)
                        <tr>
                            <td>{{ $state->id }}</td>
                            <td>{{ $state->name }}</td>
                            <td>{{ $state->name_hi ?? 'N/A' }}</td>
                            <td>{{ $state->districts_count ?? 0 }}</td>
                            <td>
                                @if($state->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editStateModal{{ $state->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.locations.states.toggle', $state) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $state->is_active ? 'btn-secondary' : 'btn-success' }}">
                                            <i class="fas {{ $state->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.locations.states.destroy', $state) }}" style="display:inline"
                                          onsubmit="return confirm('Delete this state?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">No states found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $states->appends(request()->query())->links() }}
    </div>
</div>

<!-- Add State Modal -->
<div class="modal fade" id="addStateModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.locations.states.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add State</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name (Hindi) <span class="text-danger">*</span></label>
                        <input type="text" name="name_hi" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit State Modals -->
@foreach($states as $state)
<div class="modal fade" id="editStateModal{{ $state->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.locations.states.update', $state) }}">
                @csrf @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit State</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $state->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name (Hindi) <span class="text-danger">*</span></label>
                        <input type="text" name="name_hi" class="form-control" value="{{ $state->name_hi }}" required>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                   {{ $state->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection