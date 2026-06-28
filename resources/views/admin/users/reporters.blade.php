@extends('layouts.admin')
@section('title', 'Reporters')
@section('content')
<div class="card">
    <div class="card-header"><h3>👥 All Reporters</h3></div>
    <div class="card-body">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
        
        <table class="table table-bordered">
            <thead>
                <tr><th>ID</th><th>Name</th><th>Email/Phone</th><th>District</th><th>Points</th><th>Wallet</th><th>Verified</th><th>Status</th><th>Action</th></tr>
            </thead>
            <tbody>
                @foreach($reporters as $reporter)
                <tr>
                    <td>{{ $reporter->id }}</td>
                    <td>{{ $reporter->name ?? 'N/A' }}</td>
                    <td>{{ $reporter->email ?? $reporter->phone }}</td>
                    <td>{{ $reporter->district->name ?? 'N/A' }}</td>
                    <td>{{ number_format($reporter->points ?? 0) }}</td>
                    <td>₹{{ number_format($reporter->wallet_balance ?? 0, 2) }}</td>
                    <td>
                        <span class="badge {{ $reporter->is_verified_reporter ? 'bg-success' : 'bg-warning' }}">
                            {{ $reporter->is_verified_reporter ? 'Verified' : 'Pending' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ $reporter->is_active ? 'bg-success' : 'bg-danger' }}">
                            {{ $reporter->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.users.show', $reporter) }}" class="btn btn-sm btn-info">View</a>
                        
                        <form method="POST" action="{{ route('admin.users.toggle', $reporter) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-warning">Toggle</button>
                        </form>
                        
                        <form method="POST" action="{{ route('admin.users.verify', $reporter) }}" style="display:inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-primary">
                                {{ $reporter->is_verified_reporter ? 'Unverify' : 'Verify' }}
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $reporters->links() }}
    </div>
</div>
@endsection