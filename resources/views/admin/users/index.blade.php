@extends('layouts.admin')

@section('title', 'Users Management')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h5 class="mb-0">👥 All Users</h5>
            <small class="text-muted">Manage all registered users</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-user-plus"></i> Add User
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-2 align-items-center">
                <div class="col-12 col-sm-3">
                    <input type="text" name="search" class="form-control form-control-sm" 
                           placeholder="Search by name, email or phone..." value="{{ request('search') }}">
                </div>
                <div class="col-6 col-sm-2">
                    <select name="role" class="form-select form-select-sm">
                        <option value="">All Roles</option>
                        <option value="super_admin" {{ request('role') == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="state_admin" {{ request('role') == 'state_admin' ? 'selected' : '' }}>State Admin</option>
                        <option value="district_admin" {{ request('role') == 'district_admin' ? 'selected' : '' }}>District Admin</option>
                        <option value="tehsil_admin" {{ request('role') == 'tehsil_admin' ? 'selected' : '' }}>Tehsil Admin</option>
                        <option value="block_admin" {{ request('role') == 'block_admin' ? 'selected' : '' }}>Block Admin</option>
                        <option value="state_reporter" {{ request('role') == 'state_reporter' ? 'selected' : '' }}>State Reporter</option>
                        <option value="district_reporter" {{ request('role') == 'district_reporter' ? 'selected' : '' }}>District Reporter</option>
                        <option value="tehsil_reporter" {{ request('role') == 'tehsil_reporter' ? 'selected' : '' }}>Tehsil Reporter</option>
                        <option value="block_reporter" {{ request('role') == 'block_reporter' ? 'selected' : '' }}>Block Reporter</option>
                        <option value="national_reporter" {{ request('role') == 'national_reporter' ? 'selected' : '' }}>National Reporter</option>
                        <option value="reporter" {{ request('role') == 'reporter' ? 'selected' : '' }}>General Reporter</option>
                        <option value="subscriber" {{ request('role') == 'subscriber' ? 'selected' : '' }}>Subscriber</option>
                    </select>
                </div>
                <div class="col-6 col-sm-2">
                    <select name="status" class="form-select form-select-sm">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-6 col-sm-2">
                    <select name="approved" class="form-select form-select-sm">
                        <option value="">All Approval</option>
                        <option value="approved" {{ request('approved') == 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="pending" {{ request('approved') == 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-6 col-sm-3">
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
                <strong>{{ $users->total() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-2">
            <div class="card bg-danger text-white text-center py-2">
                <small>Admins</small>
                <strong>{{ $stats['admins'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-2">
            <div class="card bg-primary text-white text-center py-2">
                <small>Reporters</small>
                <strong>{{ $stats['reporters'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-secondary text-white text-center py-2">
                <small>Subscribers</small>
                <strong>{{ $stats['subscribers'] ?? 0 }}</strong>
            </div>
        </div>
        <div class="col-6 col-sm-3">
            <div class="card bg-success text-white text-center py-2">
                <small>Active</small>
                <strong>{{ $stats['active'] ?? 0 }}</strong>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width:50px;">ID</th>
                            <th>Name</th>
                            <th class="d-none d-lg-table-cell">Email/Phone</th>
                            <th>Role</th>
                            <th class="d-none d-sm-table-cell">Location</th>
                            <th>Status</th>
                            <th style="width:200px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name ?? 'N/A' }}</strong>
                                @if(!$user->is_approved)
                                    <span class="badge bg-warning text-dark ms-1">Pending</span>
                                @endif
                            </td>
                            <td class="d-none d-lg-table-cell">
                                <small>{{ $user->email ?? $user->phone ?? 'N/A' }}</small>
                            </td>
                            <td>
                                @if($user->role == 'super_admin')
                                    <span class="badge bg-danger">Super Admin</span>
                                @elseif($user->role == 'admin')
                                    <span class="badge bg-danger">Admin</span>
                                @elseif(in_array($user->role, ['state_admin', 'district_admin', 'tehsil_admin', 'block_admin']))
                                    <span class="badge bg-warning text-dark">{{ str_replace('_', ' ', $user->role) }}</span>
                                @elseif(in_array($user->role, ['state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter']))
                                    <span class="badge bg-primary">{{ str_replace('_', ' ', $user->role) }}</span>
                                @elseif($user->role == 'reporter')
                                    <span class="badge bg-primary">
                                        Reporter
                                        @if($user->is_verified)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @endif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Subscriber</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">
                                @if($user->assigned_block_id)
                                    <small>Block: {{ $user->block->name ?? 'N/A' }}</small>
                                @elseif($user->assigned_tehsil_id)
                                    <small>Tehsil: {{ $user->tehsil->name ?? 'N/A' }}</small>
                                @elseif($user->assigned_district_id)
                                    <small>District: {{ $user->district->name ?? 'N/A' }}</small>
                                @elseif($user->assigned_state_id)
                                    <small>State: {{ $user->state->name ?? 'N/A' }}</small>
                                @else
                                    <span class="text-muted">None</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info" title="View">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline">
                                        @csrf
                                        <button type="submit" class="btn {{ $user->is_active ? 'btn-secondary' : 'btn-success' }}" title="Toggle Status">
                                            <i class="fas {{ $user->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                        </button>
                                    </form>
                                    @if($user->role == 'reporter' || in_array($user->role, ['state_reporter', 'district_reporter', 'tehsil_reporter', 'block_reporter', 'national_reporter']))
                                        <form method="POST" action="{{ route('admin.users.verify-reporter', $user) }}" style="display:inline">
                                            @csrf
                                            <button type="submit" class="btn {{ $user->is_verified ? 'btn-secondary' : 'btn-primary' }}" title="Verify Reporter">
                                                <i class="fas {{ $user->is_verified ? 'fa-times' : 'fa-check' }}"></i>
                                            </button>
                                        </form>
                                    @endif
                                    @if($user->role != 'admin' && $user->role != 'super_admin')
                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline" 
                                              onsubmit="return confirm('Delete this user?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-dark" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x d-block mb-2"></i>
                                No users found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none">
                @forelse($users as $user)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">
                                {{ $user->name ?? 'N/A' }}
                                @if(!$user->is_approved)
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @endif
                            </h6>
                            <div class="d-flex flex-wrap gap-2 small text-muted">
                                <span>📧 {{ $user->email ?? $user->phone ?? 'N/A' }}</span>
                                <span>📍 {{ $user->location_label ?? 'None' }}</span>
                            </div>
                        </div>
                        <div>
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
                            @if($user->is_active)
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-danger">Inactive</span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-1 mt-2">
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-sm btn-info">View</a>
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-warning">Edit</a>
                        <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm {{ $user->is_active ? 'btn-secondary' : 'btn-success' }}">
                                {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                            </button>
                        </form>
                        @if($user->role != 'admin' && $user->role != 'super_admin')
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline" 
                                  onsubmit="return confirm('Delete this user?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                            </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users fa-2x d-block mb-2"></i>
                    No users found
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $users->appends(request()->query())->links() }}
    </div>
</div>
@endsection