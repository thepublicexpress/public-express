@extends('layouts.admin')
@section('title', 'Withdrawals Management')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">💰 Withdrawal Requests</h5>
            <small class="text-muted">Manage all withdrawal requests from reporters</small>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-2 mb-3">
        <div class="col-4 col-sm-3">
            <div class="card bg-light text-center py-2">
                <small class="text-muted">Total</small>
                <strong>{{ $withdrawals->total() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-warning text-dark text-center py-2">
                <small>Pending</small>
                <strong>{{ $withdrawals->where('status', 'pending')->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-success text-white text-center py-2">
                <small>Completed</small>
                <strong>{{ $withdrawals->where('status', 'completed')->count() }}</strong>
            </div>
        </div>
        <div class="col-4 col-sm-3">
            <div class="card bg-danger text-white text-center py-2">
                <small>Rejected</small>
                <strong>{{ $withdrawals->where('status', 'rejected')->count() }}</strong>
            </div>
        </div>
    </div>

    <!-- Withdrawals Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive d-none d-md-block">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width:60px;">ID</th>
                            <th>Reporter</th>
                            <th>Amount</th>
                            <th>UPI ID</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th style="width:250px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($withdrawals as $w)
                        <tr>
                            <td>#{{ $w->id }}</td>
                            <td>
                                <strong>{{ $w->user->name ?? 'N/A' }}</strong>
                                <br><small class="text-muted">{{ $w->user->phone ?? '' }}</small>
                            </td>
                            <td class="fw-bold">₹{{ number_format($w->amount, 2) }}</td>
                            <td>{{ $w->upi_id ?? 'N/A' }}</td>
                            <td>
                                @if($w->status == 'completed')
                                    <span class="badge bg-success">✅ Completed</span>
                                @elseif($w->status == 'pending')
                                    <span class="badge bg-warning text-dark">⏳ Pending</span>
                                @elseif($w->status == 'processing')
                                    <span class="badge bg-info">🔄 Processing</span>
                                @else
                                    <span class="badge bg-danger">❌ Rejected</span>
                                @endif
                            </td>
                            <td>{{ $w->created_at->format('d M Y, h:i A') }}</td>
                            <td>
                                <form method="POST" action="{{ route('admin.withdrawals.process', $w) }}" class="d-flex gap-1 align-items-center flex-wrap">
                                    @csrf
                                    <select name="status" class="form-select form-select-sm" style="width:120px;">
                                        <option value="pending" {{ $w->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $w->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $w->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="rejected" {{ $w->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                    <input type="text" name="transaction_id" class="form-control form-control-sm" 
                                           style="width:120px;" placeholder="Txn ID" value="{{ $w->transaction_id }}">
                                    <button type="submit" class="btn btn-primary btn-sm">
                                        <i class="fas fa-save"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-money-bill-wave fa-2x d-block mb-2"></i>
                                No withdrawal requests found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile View -->
            <div class="d-md-none">
                @forelse($withdrawals as $w)
                <div class="border-bottom p-3">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $w->user->name ?? 'N/A' }}</h6>
                            <div class="text-muted small">
                                <p class="mb-1">💰 ₹{{ number_format($w->amount, 2) }}</p>
                                <p class="mb-1">📱 {{ $w->upi_id ?? 'N/A' }}</p>
                                <p class="mb-1">📅 {{ $w->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                        <div>
                            @if($w->status == 'completed')
                                <span class="badge bg-success">✅</span>
                            @elseif($w->status == 'pending')
                                <span class="badge bg-warning">⏳</span>
                            @elseif($w->status == 'processing')
                                <span class="badge bg-info">🔄</span>
                            @else
                                <span class="badge bg-danger">❌</span>
                            @endif
                        </div>
                    </div>
                    <div class="mt-2">
                        <form method="POST" action="{{ route('admin.withdrawals.process', $w) }}" class="d-flex gap-1 flex-wrap">
                            @csrf
                            <select name="status" class="form-select form-select-sm" style="width:100px;">
                                <option value="pending" {{ $w->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="processing" {{ $w->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $w->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="rejected" {{ $w->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                            </select>
                            <button type="submit" class="btn btn-primary btn-sm">Update</button>
                        </form>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">
                    <i class="fas fa-money-bill-wave fa-2x d-block mb-2"></i>
                    No withdrawal requests found
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="mt-3 d-flex justify-content-center">
        {{ $withdrawals->appends(request()->query())->links() }}
    </div>
</div>
@endsection