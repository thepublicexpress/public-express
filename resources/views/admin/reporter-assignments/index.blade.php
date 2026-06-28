@extends('layouts.admin')
@section('title', 'Reporter Assignments')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">📋 Reporter Assignments</h5>
            <small class="text-muted">Manage reporter location and category assignments</small>
        </div>
        <a href="{{ route('admin.reporter-assignments.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> New Assignment
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reporter-assignments.index') }}" class="row g-2">
                <div class="col-12 col-sm-4">
                    <select name="reporter_id" class="form-select form-select-sm">
                        <option value="">All Reporters</option>
                        @foreach($reporters as $reporter)
                            <option value="{{ $reporter->id }}" {{ request('reporter_id') == $reporter->id ? 'selected' : '' }}>
                                {{ $reporter->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-4">
                    <select name="state_id" class="form-select form-select-sm">
                        <option value="">All States</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ request('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
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

    <!-- Assignments Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Reporter</th>
                            <th>Location</th>
                            <th>Category</th>
                            <th>Status</th>
                            <th>Assigned At</th>
                            <th style="width:150px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assignments as $assignment)
                        <tr>
                            <td>
                                <strong>{{ $assignment->reporter->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $assignment->reporter->email ?? '' }}</small>
                            </td>
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
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.reporter-assignments.edit', $assignment) }}" class="btn btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.reporter-assignments.toggle', $assignment) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $assignment->is_active ? 'btn-secondary' : 'btn-success' }}">
                                            <i class="fas {{ $assignment->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.reporter-assignments.destroy', $assignment) }}" style="display:inline"
                                          onsubmit="return confirm('Delete this assignment?')">
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
                            <td colspan="6" class="text-center text-muted py-4">No assignments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $assignments->appends(request()->query())->links() }}
    </div>
</div>
@endsection