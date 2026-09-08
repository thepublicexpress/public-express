@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid px-0">

    @if(isset($error))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ $error }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- ============================================================
    STATS CARDS - First Row
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">कुल उपयोगकर्ता</div>
                    <div class="h3 mb-0 fw-bold">{{ number_format($totalUsers ?? 0) }}</div>
                    <small class="text-muted">आज: {{ number_format($todayUsers ?? 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">📰 रिपोर्टर</div>
                    <div class="h3 mb-0 fw-bold text-primary">{{ number_format($totalReporters ?? 0) }}</div>
                    <small class="text-muted">लंबित: {{ number_format($pendingReporters ?? 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">📰 कुल खबरें</div>
                    <div class="h3 mb-0 fw-bold">{{ number_format($totalNews ?? 0) }}</div>
                    <small class="text-muted">आज: {{ number_format($todayNews ?? 0) }}</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-success">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">✅ प्रकाशित</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ number_format($publishedNews ?? 0) }}</div>
                    <small class="text-muted">आज: {{ number_format($todayPublished ?? 0) }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    STATS CARDS - Second Row
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-warning">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">⏳ लंबित</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ number_format($pendingNews ?? 0) }}</div>
                    <small class="text-muted">समीक्षा के लिए</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-danger">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">❌ अस्वीकृत</div>
                    <div class="h3 mb-0 fw-bold text-danger">{{ number_format($rejectedNews ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-info">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">👁️ कुल व्यूज</div>
                    <div class="h3 mb-0 fw-bold text-info">{{ number_format($totalViews ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-secondary">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">💳 लंबित निकासी</div>
                    <div class="h3 mb-0 fw-bold text-secondary">{{ number_format($pendingWithdrawals ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    LOCATION STATS CARDS - Third Row
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-primary">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">🏛️ कुल राज्य</div>
                    <div class="h3 mb-0 fw-bold text-primary">{{ number_format($totalStates ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-success">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">🏙️ कुल जिले</div>
                    <div class="h3 mb-0 fw-bold text-success">{{ number_format($totalDistricts ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-warning">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">🏘️ कुल तहसीलें</div>
                    <div class="h3 mb-0 fw-bold text-warning">{{ number_format($totalTehsils ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card h-100 shadow-sm border-secondary">
                <div class="card-body">
                    <div class="text-muted small text-uppercase">🏡 कुल ब्लॉक</div>
                    <div class="h3 mb-0 fw-bold text-secondary">{{ number_format($totalBlocks ?? 0) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    STATE WISE & DISTRICT WISE NEWS
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-map-marker-alt text-primary"></i> राज्यवार खबरें
                </div>
                <div class="card-body">
                    @if(isset($stateWiseNews) && $stateWiseNews->count() > 0)
                        @php $maxNews = $stateWiseNews->max('news_count') ?: 1; @endphp
                        @foreach($stateWiseNews as $state)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fas fa-flag text-primary"></i>
                                    {{ $state->name }}
                                </span>
                                <span class="badge bg-primary rounded-pill">{{ $state->news_count ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-primary" 
                                     style="width: {{ (($state->news_count ?? 0) / $maxNews) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">कोई डेटा उपलब्ध नहीं है</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-city text-success"></i> जिलावार खबरें
                </div>
                <div class="card-body">
                    @if(isset($districtWiseNews) && $districtWiseNews->count() > 0)
                        @php $maxNews = $districtWiseNews->max('news_count') ?: 1; @endphp
                        @foreach($districtWiseNews as $district)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fas fa-building text-success"></i>
                                    {{ $district->name }}
                                    @if($district->state)
                                        <small class="text-muted">({{ $district->state->name }})</small>
                                    @endif
                                </span>
                                <span class="badge bg-success rounded-pill">{{ $district->news_count ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ (($district->news_count ?? 0) / $maxNews) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">कोई डेटा उपलब्ध नहीं है</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    STATE WISE REPORTERS & CATEGORY STATS
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-user-tie text-info"></i> राज्यवार रिपोर्टर
                </div>
                <div class="card-body">
                    @if(isset($stateWiseReporters) && $stateWiseReporters->count() > 0)
                        @php $maxUsers = $stateWiseReporters->max('users_count') ?: 1; @endphp
                        @foreach($stateWiseReporters as $state)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fas fa-flag text-info"></i>
                                    {{ $state->name }}
                                </span>
                                <span class="badge bg-info rounded-pill">{{ $state->users_count ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-info" 
                                     style="width: {{ (($state->users_count ?? 0) / $maxUsers) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">कोई डेटा उपलब्ध नहीं है</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-tags text-warning"></i> श्रेणीवार खबरें
                </div>
                <div class="card-body">
                    @if(isset($categoryStats) && $categoryStats->count() > 0)
                        @php $maxNews = $categoryStats->max('news_count') ?: 1; @endphp
                        @foreach($categoryStats as $category)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>
                                    <i class="fas fa-tag text-warning"></i>
                                    {{ $category->name }}
                                </span>
                                <span class="badge bg-warning text-dark rounded-pill">{{ $category->news_count ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-warning" 
                                     style="width: {{ (($category->news_count ?? 0) / $maxNews) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">कोई डेटा उपलब्ध नहीं है</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    CHARTS
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-chart-bar text-primary"></i> पिछले 7 दिनों की खबरें
                </div>
                <div class="card-body">
                    <canvas id="newsChart" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-chart-line text-success"></i> मासिक आंकड़े
                </div>
                <div class="card-body">
                    @if(isset($monthlyStats) && count($monthlyStats) > 0)
                        @php 
                            $monthlyMax = max(array_column($monthlyStats, 'news')) ?: 1;
                        @endphp
                        @foreach($monthlyStats as $month)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $month['month'] }}</span>
                                <span class="badge bg-primary rounded-pill">{{ $month['news'] ?? 0 }}</span>
                            </div>
                            <div class="progress mb-3" style="height: 6px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ (($month['news'] ?? 0) / $monthlyMax) * 100 }}%">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">कोई डेटा उपलब्ध नहीं है</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    ⭐ PENDING APPROVALS - FIXED ✅
    ============================================================ -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-clock text-warning"></i> ⏳ लंबित स्वीकृतियाँ</span>
                    <a href="{{ route('admin.news.index', ['status' => 'pending']) }}" class="btn btn-sm btn-primary">सभी देखें</a>
                </div>
                <div class="card-body">
                    @if(isset($pendingApprovals) && $pendingApprovals->count() > 0)
                        @foreach($pendingApprovals as $news)
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                <div>
                                    <a href="{{ route('admin.news.show', $news->id) }}" class="text-decoration-none">
                                        {{ Str::limit($news->title, 40) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-user"></i> {{ $news->user->name ?? 'Unknown' }}
                                        | <i class="fas fa-clock"></i> {{ $news->created_at->diffForHumans() }}
                                    </small>
                                </div>
                                <div>
                                    <span class="badge bg-warning text-dark">⏳ लंबित</span>
                                    <a href="{{ route('admin.news.show', $news->id) }}" class="btn btn-sm btn-success">
                                        <i class="fas fa-check"></i>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted"><i class="fas fa-check-circle text-success"></i> कोई लंबित स्वीकृति नहीं है</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <i class="fas fa-trophy text-warning"></i> 🏆 टॉप रिपोर्टर
                </div>
                <div class="card-body">
                    @if(isset($topReporters) && $topReporters->count() > 0)
                        @foreach($topReporters as $reporter)
                            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                                <div>
                                    <i class="fas fa-user-circle text-primary"></i>
                                    {{ $reporter->name }}
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-newspaper"></i> {{ $reporter->news_count ?? 0 }} खबरें
                                    </small>
                                </div>
                                <span class="badge bg-success rounded-pill">🏅 {{ $loop->iteration }}</span>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted">कोई डेटा उपलब्ध नहीं है</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
    RECENT NEWS - FIXED ✅
    ============================================================ -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-newspaper text-primary"></i> 📰 हाल की खबरें</span>
            <a href="{{ route('admin.news.index') }}" class="btn btn-sm btn-primary">सभी देखें</a>
        </div>
        <div class="card-body p-0">
            @if(isset($recentNews) && $recentNews->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>शीर्षक</th>
                            <th class="d-none d-md-table-cell">रिपोर्टर</th>
                            <th>स्थिति</th>
                            <th class="d-none d-sm-table-cell">व्यूज</th>
                            <th class="d-none d-md-table-cell">तारीख</th>
                            <th>कार्रवाई</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentNews as $item)
                        <tr>
                            <td>
                                <a href="{{ route('admin.news.show', $item->id) }}" class="text-decoration-none">
                                    {{ Str::limit($item->title, 40) }}
                                </a>
                                @if($item->is_breaking)
                                    <span class="badge bg-danger ms-1">BREAKING</span>
                                @endif
                            </td>
                            <td class="d-none d-md-table-cell">{{ $item->user->name ?? 'Unknown' }}</td>
                            <td>
                                @if($item->status == 'published')
                                    <span class="badge bg-success">✅ प्रकाशित</span>
                                @elseif($item->status == 'pending')
                                    <span class="badge bg-warning text-dark">⏳ लंबित</span>
                                @elseif($item->status == 'rejected')
                                    <span class="badge bg-danger">❌ अस्वीकृत</span>
                                @else
                                    <span class="badge bg-secondary">📝 ड्राफ्ट</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">{{ number_format($item->views ?? 0) }}</td>
                            <td class="d-none d-md-table-cell">{{ $item->created_at->format('d-m-Y') }}</td>
                            <td>
                                <a href="{{ route('admin.news.show', $item->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.news.edit', $item->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">कोई खबर नहीं मिली</div>
            @endif
        </div>
    </div>

    <!-- ============================================================
    RECENT USERS - FIXED ✅
    ============================================================ -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-users text-primary"></i> 👥 हाल ही में जुड़े उपयोगकर्ता</span>
            <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-primary">सभी देखें</a>
        </div>
        <div class="card-body p-0">
            @if(isset($recentUsers) && $recentUsers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>नाम</th>
                            <th class="d-none d-md-table-cell">ईमेल</th>
                            <th>रोल</th>
                            <th>स्थिति</th>
                            <th class="d-none d-sm-table-cell">तारीख</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentUsers as $user)
                        <tr>
                            <td>{{ $user->name ?? 'N/A' }}</td>
                            <td class="d-none d-md-table-cell">{{ $user->email ?? $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if($user->role == 'admin' || $user->role == 'super_admin')
                                    <span class="badge bg-danger">{{ ucfirst($user->role) }}</span>
                                @elseif($user->role == 'reporter')
                                    <span class="badge bg-primary">रिपोर्टर</span>
                                @else
                                    <span class="badge bg-secondary">सब्सक्राइबर</span>
                                @endif
                            </td>
                            <td>
                                @if($user->is_active)
                                    <span class="badge bg-success">सक्रिय</span>
                                @else
                                    <span class="badge bg-danger">निष्क्रिय</span>
                                @endif
                            </td>
                            <td class="d-none d-sm-table-cell">{{ $user->created_at->format('d-m-Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="text-center py-4 text-muted">कोई उपयोगकर्ता नहीं मिला</div>
            @endif
        </div>
    </div>

</div>

<!-- ============================================================
CHART JS
============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // News Chart
    const ctx = document.getElementById('newsChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [{
                label: 'खबरें',
                data: @json($chartData ?? []),
                backgroundColor: 'rgba(198, 40, 40, 0.7)',
                borderColor: '#c62828',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>
@endsection