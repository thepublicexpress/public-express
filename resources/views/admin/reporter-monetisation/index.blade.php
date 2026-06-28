@extends('layouts.admin')
@section('title', 'Reporter Monetisation')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">💰 Reporter Monetisation</h5>
            <small class="text-muted">Manage reporter monetisation status</small>
        </div>
        <div>
            <form action="{{ route('admin.reporter-monetisation.update-all-stats') }}" method="POST" style="display:inline">
                @csrf
                <button type="submit" class="btn btn-info btn-sm">
                    <i class="fas fa-sync"></i> Update All Stats
                </button>
            </form>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-4 col-sm-3">
            <div class="card bg-light text-center py-2">
                <small class="text-muted">Total Reporters</small>
                <strong>{{ $reporters->total() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-success text-white text-center py-2">
                <small>Monetisation Active</small>
                <strong>{{ $reporters->where('monetisation.is_monetisation_active', true)->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-warning text-dark text-center py-2">
                <small>Monetisation Inactive</small>
                <strong>{{ $reporters->where('monetisation.is_monetisation_active', false)->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.reporter-monetisation.index') }}" class="row g-2">
                <div class="col-12 col-sm-4">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search reporters..." value="{{ request('search') }}">
                </div>
                <div class="col-12 col-sm-4">
                    <select name="monetisation_status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('monetisation_status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('monetisation_status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
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

    <!-- Reporters Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Reporter</th>
                            <th>Points</th>
                            <th>Followers</th>
                            <th>Views</th>
                            <th>Earnings</th>
                            <th>Status</th>
                            <th>Criteria Met</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reporters as $reporter)
                        @php
                            $mon = $reporter->monetisation;
                            $allMet = $mon ? $mon->all_criteria_met : false;
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $reporter->name }}</strong>
                                <br><small class="text-muted">{{ $reporter->email ?? $reporter->phone }}</small>
                            </td>
                            <td>{{ number_format($mon->total_points ?? 0) }}</td>
                            <td>{{ number_format($mon->total_followers ?? 0) }}</td>
                            <td>{{ number_format($mon->unique_views ?? 0) }}</td>
                            <td>₹{{ number_format($mon->total_earnings ?? 0, 2) }}</td>
                            <td>
                                @if($mon && $mon->is_monetisation_active)
                                    <span class="badge bg-success">✅ Active</span>
                                @else
                                    <span class="badge bg-secondary">⏳ Inactive</span>
                                @endif
                            </td>
                            <td>
                                @if($allMet)
                                    <span class="badge bg-success">✅ All Met</span>
                                @else
                                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.reporter-monetisation.show', $reporter) }}" class="btn btn-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.reporter-monetisation.toggle', $reporter) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $mon && $mon->is_monetisation_active ? 'btn-secondary' : 'btn-success' }}">
                                            <i class="fas {{ $mon && $mon->is_monetisation_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">No reporters found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $reporters->appends(request()->query())->links() }}
    </div>
</div>
@endsection