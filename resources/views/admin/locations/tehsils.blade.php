@extends('layouts.admin')
@section('title', 'Tehsils Management')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">🗺️ Tehsils Management</h5>
            <small class="text-muted">Manage all tehsils</small>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addTehsilModal">
            <i class="fas fa-plus"></i> Add Tehsil
        </button>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-4 col-sm-3">
            <div class="card bg-light text-center py-2">
                <small class="text-muted">Total</small>
                <strong>{{ $tehsils->total() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-success text-white text-center py-2">
                <small>Active</small>
                <strong>{{ $tehsils->where('is_active', true)->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-secondary text-white text-center py-2">
                <small>Inactive</small>
                <strong>{{ $tehsils->where('is_active', false)->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-info text-white text-center py-2">
                <small>Blocks</small>
                <strong>{{ $tehsils->sum('blocks_count') }}</strong>
            </div>
        </div>
    </div>

    <!-- Search & Filter -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.locations.tehsils') }}" class="row g-2">
                <div class="col-12 col-sm-4">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search tehsils..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-sm-4">
                    <select name="district_id" class="form-select form-select-sm">
                        <option value="">All Districts</option>
                        @foreach($districts as $district)
                            <option value="{{ $district->id }}" {{ request('district_id') == $district->id ? 'selected' : '' }}>
                                {{ $district->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tehsils Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">#</th>
                            <th>Name (English)</th>
                            <th>Name (Hindi)</th>
                            <th>District</th>
                            <th>Blocks</th>
                            <th>Status</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tehsils as $tehsil)
                        <tr>
                            <td>{{ $tehsil->id }}</td>
                            <td>{{ $tehsil->name }}</td>
                            <td>{{ $tehsil->name_hi ?? 'N/A' }}</td>
                            <td>{{ $tehsil->district->name ?? 'N/A' }}</td>
                            <td>{{ $tehsil->blocks_count ?? 0 }}</td>
                            <td>
                                @if($tehsil->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-warning" data-bs-toggle="modal" 
                                            data-bs-target="#editTehsilModal{{ $tehsil->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" action="{{ route('admin.locations.tehsils.toggle', $tehsil) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $tehsil->is_active ? 'btn-secondary' : 'btn-success' }}">
                                            <i class="fas {{ $tehsil->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.locations.tehsils.destroy', $tehsil) }}" style="display:inline"
                                          onsubmit="return confirm('Delete this tehsil?')">
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
                            <td colspan="7" class="text-center text-muted py-4">No tehsils found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $tehsils->appends(request()->query())->links() }}
    </div>
</div>

<!-- Add Tehsil Modal -->
<div class="modal fade" id="addTehsilModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.locations.tehsils.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="fas fa-plus"></i> Add Tehsil</h5>
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
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select name="district_id" class="form-select" required>
                            <option value="">-- Select District --</option>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}">{{ $district->name }}</option>
                            @endforeach
                        </select>
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

<!-- Edit Tehsil Modals -->
@foreach($tehsils as $tehsil)
<div class="modal fade" id="editTehsilModal{{ $tehsil->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.locations.tehsils.update', $tehsil) }}">
                @csrf @method('PUT')
                <div class="modal-header bg-warning">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Edit Tehsil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Name (English) <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $tehsil->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Name (Hindi) <span class="text-danger">*</span></label>
                        <input type="text" name="name_hi" class="form-control" value="{{ $tehsil->name_hi }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">District <span class="text-danger">*</span></label>
                        <select name="district_id" class="form-select" required>
                            @foreach($districts as $district)
                                <option value="{{ $district->id }}" {{ $tehsil->district_id == $district->id ? 'selected' : '' }}>
                                    {{ $district->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" class="form-check-input" value="1" 
                                   {{ $tehsil->is_active ? 'checked' : '' }}>
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