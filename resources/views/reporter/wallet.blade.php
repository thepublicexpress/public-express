@extends('layouts.reporter')
@section('title', 'My Wallet')

@section('content')
<div class="container-fluid px-0">

    <!-- Balance Card -->
    <div class="card bg-gradient-to-r from-red-600 to-red-800 text-white mb-4" style="background: linear-gradient(135deg, #c62828, #8e0000);">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-8">
                    <p class="mb-1 opacity-75">Available Balance</p>
                    <h2 class="fw-bold mb-0">₹{{ number_format($user->wallet_balance ?? 0, 2) }}</h2>
                    <p class="mb-0 mt-1 opacity-75">⭐ {{ number_format($user->points ?? 0) }} Points = ₹{{ number_format(($user->points ?? 0) / 100, 2) }}</p>
                </div>
                <div class="col-4 text-end">
                    <i class="fas fa-wallet fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row g-2 mb-3">
        <div class="col-6">
            <div class="card text-center">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Points</p>
                    <h4 class="fw-bold text-primary">{{ number_format($user->points ?? 0) }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card text-center">
                <div class="card-body py-2">
                    <p class="text-muted small mb-0">Total Earnings</p>
                    <h4 class="fw-bold text-success">₹{{ number_format(($user->points ?? 0) / 100, 2) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <!-- Withdrawal Form -->
    <div class="card mb-3">
        <div class="card-header">
            <i class="fas fa-hand-holding-usd text-success"></i> Request Withdrawal
        </div>
        <div class="card-body">
            @if(($user->wallet_balance ?? 0) >= 100)
                <form action="{{ route('reporter.wallet.withdraw') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Amount (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" 
                               min="100" max="{{ $user->wallet_balance }}" 
                               placeholder="Enter amount (min ₹100)" required>
                        <small class="text-muted">Min: ₹100, Max: ₹{{ number_format($user->wallet_balance, 0) }}</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">UPI ID <span class="text-danger">*</span></label>
                        <input type="text" name="upi_id" class="form-control" 
                               value="{{ $user->upi_id }}" placeholder="yourname@upi" required>
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="fas fa-paper-plane"></i> Submit Withdrawal Request
                    </button>
                </form>
            @else
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-exclamation-triangle"></i>
                    Minimum balance ₹100 required for withdrawal.
                    You currently have ₹{{ number_format($user->wallet_balance ?? 0, 2) }}.
                </div>
                @if(!$user->upi_id)
                    <div class="alert alert-info mt-2 mb-0">
                        <i class="fas fa-info-circle"></i>
                        Please add your UPI ID in your <a href="{{ route('reporter.profile') }}" class="fw-bold">profile</a> to withdraw money.
                    </div>
                @endif
            @endif
        </div>
    </div>

    <!-- Withdrawal History -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="fas fa-history"></i> Withdrawal History</span>
            <span class="badge bg-secondary">{{ $withdrawals->count() }} requests</span>
        </div>
        <div class="card-body p-0">
            @if($withdrawals && $withdrawals->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Txn ID</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($withdrawals as $w)
                            <tr>
                                <td class="fw-bold">₹{{ number_format($w->amount, 2) }}</td>
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
                                <td>{{ $w->created_at->format('d M Y') }}</td>
                                <td>{{ $w->transaction_id ?? 'N/A' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-receipt fa-2x d-block mb-2"></i>
                    No withdrawal history
                </div>
            @endif
        </div>
    </div>

    <!-- Points History -->
    <div class="card mt-3">
        <div class="card-header">
            <i class="fas fa-star text-warning"></i> Points History
        </div>
        <div class="card-body p-0">
            @if($points_history && $points_history->count() > 0)
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>Points</th>
                                <th>Reason</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($points_history as $p)
                            <tr>
                                <td>
                                    <span class="fw-bold {{ $p->points > 0 ? 'text-success' : 'text-danger' }}">
                                        {{ $p->points > 0 ? '+' : '' }}{{ $p->points }}
                                    </span>
                                </td>
                                <td>{{ $p->reason ?? 'N/A' }}</td>
                                <td>{{ $p->created_at->format('d M Y') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">No points history</div>
            @endif
        </div>
    </div>
</div>
@endsection