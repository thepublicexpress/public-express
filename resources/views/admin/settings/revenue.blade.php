@extends('layouts.admin')
@section('title', 'Revenue & Monetisation Settings')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">💰 Revenue & Monetisation Settings</h5>
            <small class="text-muted">Control reporter earnings, points, and monetisation</small>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-sliders-h"></i> Monetisation Settings
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.settings.revenue.update') }}" method="POST">
                        @csrf

                        <!-- MONETISATION ON/OFF -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2">🔘 Monetisation Status</h6>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_monetisation_active" class="form-check-input" 
                                       value="1" id="monetisationToggle" 
                                       {{ old('is_monetisation_active', $settings->is_monetisation_active ?? false) ? 'checked' : '' }}
                                       onchange="toggleMonetisation(this)">
                                <label class="form-check-label fw-bold" for="monetisationToggle" id="monetisationLabel">
                                    {{ (old('is_monetisation_active', $settings->is_monetisation_active ?? false)) ? '✅ Monetisation Active' : '❌ Monetisation Inactive' }}
                                </label>
                            </div>
                            <small class="text-muted">When inactive, reporters cannot see wallet or withdraw money</small>
                        </div>

                        <!-- WALLET VISIBILITY -->
                        <div class="mb-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2">👁️ Wallet Visibility</h6>
                            <div class="form-check form-switch">
                                <input type="checkbox" name="is_wallet_visible" class="form-check-input" 
                                       value="1" id="walletVisibilityToggle"
                                       {{ old('is_wallet_visible', $settings->is_wallet_visible ?? false) ? 'checked' : '' }}
                                       onchange="toggleWalletVisibility(this)">
                                <label class="form-check-label fw-bold" for="walletVisibilityToggle" id="walletVisibilityLabel">
                                    {{ (old('is_wallet_visible', $settings->is_wallet_visible ?? false)) ? '✅ Wallet Visible' : '❌ Wallet Hidden' }}
                                </label>
                            </div>
                            <small class="text-muted">When hidden, reporters cannot see wallet option in dashboard</small>
                        </div>

                        <hr>

                        <!-- POINTS & RUPEE RATE -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Points to Rupee Rate <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">₹</span>
                                    <input type="number" name="point_to_rupee_rate" class="form-control" 
                                           value="{{ old('point_to_rupee_rate', $settings->point_to_rupee_rate ?? 0.10) }}" 
                                           step="0.01" min="0.01" required>
                                    <span class="input-group-text">per point</span>
                                </div>
                                <small class="text-muted">Example: 0.10 means 10 points = ₹1</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Points per News Published <span class="text-danger">*</span></label>
                                <input type="number" name="points_per_news" class="form-control" 
                                       value="{{ old('points_per_news', $settings->points_per_news ?? 10) }}" 
                                       min="1" required>
                                <small class="text-muted">Points earned when news is approved</small>
                            </div>
                        </div>

                        <!-- MONETISATION CRITERIA -->
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Minimum Followers <span class="text-danger">*</span></label>
                                <input type="number" name="min_followers_for_monetisation" class="form-control" 
                                       value="{{ old('min_followers_for_monetisation', $settings->min_followers_for_monetisation ?? 100) }}" 
                                       min="0" required>
                                <small class="text-muted">Reporter must have at least this many followers</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Minimum Views <span class="text-danger">*</span></label>
                                <input type="number" name="min_views_for_monetisation" class="form-control" 
                                       value="{{ old('min_views_for_monetisation', $settings->min_views_for_monetisation ?? 1000) }}" 
                                       min="0" required>
                                <small class="text-muted">Reporter must have at least this many unique views</small>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Minimum Points <span class="text-danger">*</span></label>
                                <input type="number" name="min_points_for_monetisation" class="form-control" 
                                       value="{{ old('min_points_for_monetisation', $settings->min_points_for_monetisation ?? 100) }}" 
                                       min="0" required>
                                <small class="text-muted">Reporter must have at least this many points</small>
                            </div>
                        </div>

                        <!-- FAKE VIEWS TRACKING -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Max Views Per IP Per Day <span class="text-danger">*</span></label>
                                <input type="number" name="max_views_per_ip_per_day" class="form-control" 
                                       value="{{ old('max_views_per_ip_per_day', $settings->max_views_per_ip_per_day ?? 5) }}" 
                                       min="1" required>
                                <small class="text-muted">Maximum views allowed from same IP in 24 hours</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Minimum Withdrawal Amount <span class="text-danger">*</span></label>
                                <input type="number" name="min_withdrawal" class="form-control" 
                                       value="{{ old('min_withdrawal', $settings->min_withdrawal ?? 100) }}" 
                                       min="10" required>
                                <small class="text-muted">Minimum amount reporter can withdraw</small>
                            </div>
                        </div>

                        <!-- MONETISATION TERMS -->
                        <div class="mb-3">
                            <label class="form-label fw-bold">Monetisation Terms & Conditions</label>
                            <textarea name="monetisation_terms" class="form-control" rows="5" 
                                      placeholder="Enter monetisation terms and conditions...">{{ old('monetisation_terms', $settings->monetisation_terms ?? '') }}</textarea>
                            <small class="text-muted">These terms will be shown to reporters when they request monetisation</small>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Save Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Current Stats -->
            <div class="card mb-3">
                <div class="card-header">
                    <i class="fas fa-chart-bar"></i> Current Stats
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <span>Total Reporters:</span>
                        <strong>{{ $totalReporters ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Monetisation Active:</span>
                        <strong>{{ $activeMonetisation ?? 0 }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Points Earned:</span>
                        <strong>{{ number_format($totalPoints ?? 0) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Total Earnings:</span>
                        <strong>₹{{ number_format($totalEarnings ?? 0, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Fake Views Detected:</span>
                        <strong class="text-danger">{{ number_format($fakeViews ?? 0) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-bolt"></i> Quick Actions
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.reporter-monetisation.index') }}" class="btn btn-info w-100 mb-2">
                        <i class="fas fa-users"></i> Manage Reporter Monetisation
                    </a>
                    <a href="{{ route('admin.fake-views.index') }}" class="btn btn-danger w-100 mb-2">
                        <i class="fas fa-exclamation-triangle"></i> View Fake Views Reports
                    </a>
                    <a href="{{ route('admin.reporter-views.index') }}" class="btn btn-success w-100">
                        <i class="fas fa-eye"></i> View Reporter Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function toggleMonetisation(checkbox) {
    const label = document.getElementById('monetisationLabel');
    if (checkbox.checked) {
        label.innerHTML = '✅ Monetisation Active';
    } else {
        label.innerHTML = '❌ Monetisation Inactive';
    }
}

function toggleWalletVisibility(checkbox) {
    const label = document.getElementById('walletVisibilityLabel');
    if (checkbox.checked) {
        label.innerHTML = '✅ Wallet Visible';
    } else {
        label.innerHTML = '❌ Wallet Hidden';
    }
}
</script>
@endsection