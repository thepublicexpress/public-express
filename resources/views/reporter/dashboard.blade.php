@extends('layouts.reporter')

@section('title', 'Reporter Dashboard')

@section('content')
<div class="container-fluid px-4">

    {{-- ============================================================ --}}
    {{-- WELCOME HEADER – Home Button Added --}}
    {{-- ============================================================ --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1 class="h2 fw-bold text-dark mb-1">
                        👋 नमस्ते, {{ Auth::user()->name ?? 'Reporter' }}
                    </h1>
                    <p class="text-muted small">
                        <i class="fas fa-calendar-alt me-1"></i> 
                        {{ \Carbon\Carbon::now()->format('l, d F Y') }}
                        &nbsp;|&nbsp;
                        <span class="badge bg-brand text-white">{{ Auth::user()->display_role ?? 'Reporter' }}</span>
                        @if(Auth::user()->getLocationString())
                            <span class="badge bg-secondary text-white ms-1">
                                📍 {{ Auth::user()->getLocationString() }}
                            </span>
                        @endif
                        @if(($points ?? 0) > 0)
                            <span class="badge bg-warning text-dark ms-1">
                                ⭐ {{ $points ?? 0 }} पॉइंट्स
                            </span>
                        @endif
                    </p>
                </div>
                <div class="d-flex gap-2">
                    {{-- ✅ HOME BUTTON – सीधे Website Home Page पर जाएगा --}}
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg shadow-sm" title="होम पेज पर जाएँ">
                        <i class="fas fa-home"></i> होम
                    </a>
                    <a href="{{ route('reporter.news.create') }}" class="btn btn-brand btn-lg shadow-sm">
                        <i class="fas fa-plus-circle"></i> नई खबर लिखें
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- STATS CARDS – सभी Clickable हैं --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4">
        {{-- Total News Card --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reporter.news.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-newspaper fa-2x text-primary"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">कुल खबरें</h6>
                            <h2 class="fw-bold mb-0">{{ $totalNews ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Published News Card --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reporter.news.index', ['status' => 'published']) }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-check-circle fa-2x text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">प्रकाशित</h6>
                            <h2 class="fw-bold mb-0">{{ $publishedNews ?? 0 }}</h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Total Views Card --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reporter.news.index') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-eye fa-2x text-warning"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">कुल व्यूज</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($totalViews ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        {{-- Points Card --}}
        <div class="col-xl-3 col-md-6">
            <a href="{{ route('reporter.wallet') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-info bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-star fa-2x text-info"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">पॉइंट्स</h6>
                            <h2 class="fw-bold mb-0">{{ number_format($points ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- SECOND ROW: PENDING, REJECTED, DRAFT + WALLET --}}
    {{-- ============================================================ --}}
    <div class="row g-3 mb-4">
        @if($pendingNews > 0)
            <div class="col-md-3">
                <a href="{{ route('reporter.news.index', ['status' => 'pending']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 hover-lift">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="fas fa-clock fa-2x text-warning"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold small mb-1">समीक्षा में</h6>
                                <h3 class="fw-bold mb-0">{{ $pendingNews }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if($rejectedNews > 0)
            <div class="col-md-3">
                <a href="{{ route('reporter.news.index', ['status' => 'rejected']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 hover-lift">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-danger bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="fas fa-times-circle fa-2x text-danger"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold small mb-1">अस्वीकृत</h6>
                                <h3 class="fw-bold mb-0">{{ $rejectedNews }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        @if($draftNews > 0)
            <div class="col-md-3">
                <a href="{{ route('reporter.news.index', ['status' => 'draft']) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm rounded-4 hover-lift">
                        <div class="card-body d-flex align-items-center">
                            <div class="bg-secondary bg-opacity-10 rounded-3 p-3 me-3">
                                <i class="fas fa-pencil-alt fa-2x text-secondary"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase fw-semibold small mb-1">ड्राफ्ट</h6>
                                <h3 class="fw-bold mb-0">{{ $draftNews }}</h3>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endif

        {{-- Wallet Balance Card --}}
        <div class="col-md-3">
            <a href="{{ route('reporter.wallet') }}" class="text-decoration-none">
                <div class="card h-100 border-0 shadow-sm rounded-4 hover-lift">
                    <div class="card-body d-flex align-items-center">
                        <div class="bg-success bg-opacity-10 rounded-3 p-3 me-3">
                            <i class="fas fa-wallet fa-2x text-success"></i>
                        </div>
                        <div>
                            <h6 class="text-muted text-uppercase fw-semibold small mb-1">वॉलेट</h6>
                            <h3 class="fw-bold mb-0">₹{{ number_format($walletBalance ?? 0, 2) }}</h3>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- MAIN CONTENT: Recent News Table + Quick Actions Sidebar --}}
    {{-- ============================================================ --}}
    <div class="row g-4">
        {{-- LEFT: Recent News Table --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-3 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">
                        <i class="fas fa-clock me-2 text-brand"></i> हाल की खबरें
                    </h5>
                    <a href="{{ route('reporter.news.index') }}" class="btn btn-outline-brand btn-sm">
                        सभी देखें <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
                <div class="card-body pt-0">
                    @if(isset($recentNews) && $recentNews->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>शीर्षक</th>
                                        <th>श्रेणी</th>
                                        <th>स्थिति</th>
                                        <th>व्यूज</th>
                                        <th>तारीख</th>
                                        <th class="text-end">कार्रवाई</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentNews as $news)
                                    <tr>
                                        <td class="fw-semibold">
                                            <a href="{{ route('reporter.news.show', $news->id) }}" 
                                               class="text-decoration-none text-dark hover-brand">
                                                {{ Str::limit($news->title, 40) }}
                                            </a>
                                        </td>
                                        <td>{{ $news->category->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $statusColors = [
                                                    'published' => 'success',
                                                    'pending' => 'warning',
                                                    'rejected' => 'danger',
                                                    'draft' => 'secondary',
                                                ];
                                                $statusLabels = [
                                                    'published' => 'प्रकाशित',
                                                    'pending' => 'समीक्षा में',
                                                    'rejected' => 'अस्वीकृत',
                                                    'draft' => 'ड्राफ्ट',
                                                ];
                                            @endphp
                                            <span class="badge bg-{{ $statusColors[$news->status] ?? 'secondary' }}">
                                                {{ $statusLabels[$news->status] ?? $news->status }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($news->views ?? 0) }}</td>
                                        <td>{{ $news->created_at->format('d M Y') }}</td>
                                        <td class="text-end">
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('reporter.news.show', $news->id) }}" 
                                                   class="btn btn-outline-info" title="देखें">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('reporter.news.edit', $news->id) }}" 
                                                   class="btn btn-outline-warning" title="संपादित करें">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($news->status == 'published' || $news->status == 'rejected')
                                                    <form action="{{ route('reporter.news.resubmit', $news->id) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-success" title="पुनः सबमिट करें">
                                                            <i class="fas fa-redo"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper fa-3x text-muted mb-3"></i>
                            <p class="text-muted">अभी तक कोई खबर नहीं लिखी गई।</p>
                            <a href="{{ route('reporter.news.create') }}" class="btn btn-brand">
                                <i class="fas fa-plus-circle"></i> पहली खबर लिखें
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT: Sidebar Cards --}}
        <div class="col-lg-4">
            {{-- Quick Actions – Home Button Added Here --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-bolt me-2 text-brand"></i> त्वरित कार्रवाई</h5>
                </div>
                <div class="card-body pt-0">
                    <div class="d-grid gap-2">
                        {{-- ✅ HOME BUTTON – सीधे Website Home Page पर जाएगा --}}
                        <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-home"></i> होम पेज
                        </a>
                        <a href="{{ route('reporter.news.create') }}" class="btn btn-brand">
                            <i class="fas fa-plus-circle"></i> नई खबर लिखें
                        </a>
                        <a href="{{ route('reporter.news.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-list"></i> सभी खबरें
                        </a>
                        <a href="{{ route('reporter.wallet') }}" class="btn btn-outline-info">
                            <i class="fas fa-wallet"></i> वॉलेट
                        </a>
                        <a href="{{ route('reporter.profile') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-user-edit"></i> प्रोफ़ाइल
                        </a>
                    </div>
                </div>
            </div>

            {{-- Earnings / Wallet Card (Clickable) --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <a href="{{ route('reporter.wallet') }}" class="text-decoration-none">
                    <div class="card-header bg-white border-0 pt-4 pb-3">
                        <h5 class="fw-bold mb-0"><i class="fas fa-coins me-2 text-warning"></i> आय</h5>
                    </div>
                    <div class="card-body text-center pt-0">
                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 mb-3">
                            <h2 class="fw-bold text-dark mb-0">₹{{ number_format($walletBalance ?? 0, 2) }}</h2>
                        </div>
                        <p class="text-muted small">कुल उपलब्ध राशि</p>
                        <span class="btn btn-outline-warning btn-sm w-100">
                            <i class="fas fa-money-bill-wave"></i> विवरण देखें
                        </span>
                    </div>
                </a>
            </div>

            {{-- Quick Stats / Tips --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="fw-bold mb-0"><i class="fas fa-lightbulb me-2 text-brand"></i> सुझाव</h5>
                </div>
                <div class="card-body pt-0">
                    <ul class="list-unstyled small">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            प्रतिदिन कम से कम 2-3 खबरें लिखें।
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            खबर में मुख्य फोटो और शीर्षक आकर्षक रखें।
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            स्थानीय घटनाओं को प्राथमिकता दें।
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            अपनी प्रोफ़ाइल को पूरा भरें (Bio, Photo, Social Media)।
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- ============================================================ --}}
{{-- EXTRA CSS --}}
{{-- ============================================================ --}}
@push('styles')
<style>
    .bg-brand {
        background-color: #c62828 !important;
    }
    .btn-brand {
        background-color: #c62828;
        color: #fff;
        border: none;
        transition: all 0.2s;
    }
    .btn-brand:hover {
        background-color: #a51d1d;
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(198, 40, 40, 0.3);
    }
    .btn-outline-brand {
        color: #c62828;
        border-color: #c62828;
    }
    .btn-outline-brand:hover {
        background-color: #c62828;
        color: #fff;
    }
    .text-brand {
        color: #c62828;
    }
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        cursor: pointer;
    }
    .hover-lift:hover {
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.12) !important;
    }
    .card {
        border: none;
    }
    .table th {
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
    }
    .table td {
        vertical-align: middle;
        font-size: 0.9rem;
    }
    .badge {
        font-weight: 500;
        padding: 0.4rem 0.7rem;
    }
    .hover-brand:hover {
        color: #c62828 !important;
    }
</style>
@endpush