<!DOCTYPE html>
<html lang="hi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') | द पब्लिक एक्सप्रेस Admin</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:"Noto Sans Devanagari",sans-serif;background:#f0f2f5;color:#1a202c}

/* ===== SIDEBAR ===== */
.sidebar{position:fixed;top:0;left:0;width:250px;height:100vh;background:linear-gradient(180deg,#1a1a2e,#16213e);color:#fff;overflow-y:auto;z-index:200;transition:transform .3s ease}
.sidebar-brand{padding:16px 20px;text-align:center;border-bottom:1px solid rgba(255,255,255,.1)}
.sidebar-brand img{width:130px;border-radius:10px;margin-bottom:6px}
.sidebar-brand p{font-size:11px;color:#a0aec0;margin-top:2px;letter-spacing:1px;text-transform:uppercase}
.sidebar-nav a{display:flex;align-items:center;gap:10px;padding:12px 20px;color:#cbd5e0;text-decoration:none;transition:.2s;font-size:14px}
.sidebar-nav a:hover,.sidebar-nav a.active{background:rgba(229,57,53,.15);color:#ff6b6b;border-right:3px solid #e53935}
.sidebar-nav .nav-group-title{padding:10px 20px 5px;font-size:11px;color:#718096;text-transform:uppercase;letter-spacing:1px}

.sidebar-submenu {
    background: rgba(0, 0, 0, 0.2);
    padding-left: 15px;
}
.sidebar-submenu a {
    padding: 10px 20px;
    font-size: 13px;
}

.sidebar-close{display:none;position:absolute;top:12px;right:12px;background:rgba(255,255,255,.1);border:none;color:#fff;width:32px;height:32px;border-radius:50%;cursor:pointer;font-size:16px;align-items:center;justify-content:center}

.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:150}
.sidebar-overlay.active{display:block}

/* ===== TOPBAR ===== */
.topbar{position:fixed;top:0;left:250px;right:0;height:56px;background:#fff;border-bottom:1px solid #e2e8f0;display:flex;align-items:center;justify-content:space-between;padding:0 20px;z-index:99;box-shadow:0 1px 3px rgba(0,0,0,.05);transition:left .3s ease}
.topbar-left{display:flex;align-items:center;gap:10px}
.topbar-left img{height:32px;border-radius:6px}
.topbar-left span{font-weight:700;color:#e53935;font-size:14px}
.menu-btn{display:none;background:none;border:none;font-size:20px;color:#e53935;cursor:pointer;padding:4px 8px;line-height:1}

/* ===== NOTIFICATION BELL ===== */
.notif-wrapper{position:relative;display:inline-block}
.notif-bell{background:none;border:none;font-size:20px;color:#4a5568;cursor:pointer;padding:4px 6px;position:relative}
.notif-badge{position:absolute;top:-4px;right:-4px;background:#e53e3e;color:#fff;border-radius:50%;padding:2px 6px;font-size:10px;font-weight:700;min-width:18px;text-align:center;line-height:1.2;border:2px solid #fff}
.notif-dropdown{position:absolute;top:calc(100% + 8px);right:-10px;width:360px;max-height:420px;overflow-y:auto;background:#fff;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.15);border:1px solid #e2e8f0;display:none;z-index:1000}
.notif-dropdown.show{display:block}
.notif-item{padding:12px 16px;border-bottom:1px solid #f0f0f0;text-decoration:none;color:#1a202c;display:block;transition:background .15s}
.notif-item:hover{background:#f7fafc}
.notif-item.unread{background:#fef9e7}
.notif-item .title{font-weight:600;font-size:14px}
.notif-item .body{font-size:13px;color:#4a5568;margin:2px 0 4px}
.notif-item .time{font-size:11px;color:#a0aec0}
.notif-item .badge-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#e53935;margin-right:8px}
.notif-empty{padding:20px;text-align:center;color:#a0aec0;font-size:14px}
.notif-footer{padding:10px 16px;border-top:1px solid #e2e8f0;text-align:center}
.notif-footer a{color:#e53935;font-weight:600;text-decoration:none;font-size:13px}
.notif-footer a:hover{text-decoration:underline}

/* ===== MAIN ===== */
.main{margin-left:250px;padding-top:56px;min-height:100vh;transition:margin .3s ease}
.main-content{padding:20px}

/* ===== COMPONENTS ===== */
.page-header{margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px}
.page-header h1{font-size:20px;font-weight:700;color:#1a202c}
.page-header p{color:#718096;font-size:13px;margin-top:2px}
.card{background:#fff;border-radius:12px;padding:20px;box-shadow:0 1px 3px rgba(0,0,0,.08);margin-bottom:20px}
.stat-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:12px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:12px;padding:16px;box-shadow:0 1px 3px rgba(0,0,0,.08);text-align:center}
.stat-card .number{font-size:28px;font-weight:700;color:#1a202c;margin-bottom:4px}
.stat-card .label{font-size:12px;color:#718096}
.stat-card.red .number{color:#e53e3e}
.stat-card.green .number{color:#38a169}
.stat-card.blue .number{color:#3182ce}
.stat-card.orange .number{color:#dd6b20}
.badge{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-pending{background:#fef3c7;color:#92400e}
.badge-published{background:#d1fae5;color:#065f46}
.badge-rejected{background:#fee2e2;color:#991b1b}
.badge-draft{background:#e2e8f0;color:#4a5568}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;text-decoration:none;transition:.2s;white-space:nowrap}
.btn-primary{background:#e53935;color:#fff}.btn-primary:hover{background:#c62828}
.btn-success{background:#38a169;color:#fff}.btn-success:hover{background:#2f855a}
.btn-danger{background:#e53e3e;color:#fff}.btn-danger:hover{background:#c53030}
.btn-warning{background:#dd6b20;color:#fff}.btn-warning:hover{background:#c05621}
.btn-sm{padding:5px 10px;font-size:12px}
.btn-outline{background:transparent;border:1px solid currentColor}
.table-wrap{width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}
table{width:100%;border-collapse:collapse;min-width:600px}
th,td{padding:10px 14px;text-align:left;border-bottom:1px solid #e2e8f0;font-size:13px}
th{background:#f7fafc;font-weight:600;color:#4a5568;font-size:11px;text-transform:uppercase;letter-spacing:.5px}
tr:hover td{background:#f7fafc}
.form-group{margin-bottom:16px}
.form-group label{display:block;font-size:13px;font-weight:600;color:#4a5568;margin-bottom:6px}
.form-control{width:100%;padding:10px 14px;border:1px solid #e2e8f0;border-radius:8px;font-size:14px;transition:.2s;font-family:inherit}
.form-control:focus{outline:none;border-color:#e53935;box-shadow:0 0 0 3px rgba(229,57,53,.1)}
select.form-control{cursor:pointer}
.alert{padding:12px 16px;border-radius:8px;margin-bottom:16px;font-size:14px}
.alert-success{background:#d1fae5;color:#065f46;border:1px solid #a7f3d0}
.alert-error{background:#fee2e2;color:#991b1b;border:1px solid #fecaca}
.pagination{display:flex;gap:6px;margin-top:16px;flex-wrap:wrap}
.pagination a,.pagination span{padding:6px 12px;border-radius:6px;font-size:13px;text-decoration:none;background:#fff;border:1px solid #e2e8f0;color:#4a5568}
.pagination .active span{background:#e53935;color:#fff;border-color:#e53935}
.user-avatar{width:34px;height:34px;border-radius:50%;background:#e53935;color:#fff;display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700}

/* ===== MOBILE ===== */
@media(max-width:768px){
    .sidebar{transform:translateX(-100%)}
    .sidebar.open{transform:translateX(0)}
    .sidebar-close{display:flex}
    .topbar{left:0}
    .menu-btn{display:block}
    .main{margin-left:0}
    .main-content{padding:12px}
    .page-header{flex-direction:column;align-items:flex-start}
    .page-header h1{font-size:17px}
    .stat-grid{grid-template-columns:1fr 1fr;gap:10px}
    .stat-card{padding:12px}
    .stat-card .number{font-size:22px}
    .card{padding:14px;border-radius:8px}
    .btn{padding:7px 11px;font-size:12px}
    .topbar-username{display:none}
    .form-grid-2{grid-template-columns:1fr !important}
    .notif-dropdown{width:300px;right:-20px}
}
@media(max-width:400px){
    .stat-grid{grid-template-columns:1fr 1fr}
    .stat-card .number{font-size:20px}
    .main-content{padding:10px}
    .notif-dropdown{width:280px;right:-30px}
}
</style>
@stack('styles')
</head>
<body>

{{-- Overlay --}}
<div class="sidebar-overlay" id="overlay" onclick="closeSidebar()"></div>

{{-- Sidebar --}}
<div class="sidebar" id="sidebar">
    <button class="sidebar-close" onclick="closeSidebar()"><i class="fas fa-times"></i></button>
    <div class="sidebar-brand">
        <img src="/images/logo.png" alt="The Public Express">
        <p>पब्लिक की आवाज़</p>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-group-title">मुख्य</div>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-th-large"></i> Dashboard
        </a>
        
        <div class="nav-group-title">खबर</div>
        <a href="{{ route('admin.news.index') }}" class="{{ request()->routeIs('admin.news.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-newspaper"></i> सभी खबरें
        </a>
        <a href="{{ route('admin.news.index', ['status'=>'pending']) }}" onclick="closeSidebar()">
            <i class="fas fa-clock"></i> Pending Review
        </a>
        
        <div class="nav-group-title">प्रबंधन</div>
        <a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-users"></i> Users / Reporters
        </a>
        <a href="{{ route('admin.withdrawals.index') }}" class="{{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-wallet"></i> Withdrawals
        </a>

        {{-- ============================================================ --}}
        {{-- ✅ NEW: OPINION POLL GROUP --}}
        {{-- ============================================================ --}}
        <div class="nav-group-title">ओपिनियन पोल</div>
        <a href="{{ route('admin.poll.results') }}" class="{{ request()->routeIs('admin.poll.results') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-chart-bar"></i> Poll Results
        </a>
        
        <div class="nav-group-title">सेटअप</div>
        <a href="{{ route('admin.locations.states') }}" class="{{ request()->routeIs('admin.locations.states*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-map"></i> States
        </a>
        <a href="{{ route('admin.locations.districts') }}" class="{{ request()->routeIs('admin.locations.districts*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-map-marker-alt"></i> Districts
        </a>
        <a href="{{ route('admin.locations.tehsils') }}" class="{{ request()->routeIs('admin.locations.tehsils*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-location-arrow"></i> Tehsils
        </a>
        
        <div class="nav-group-title">सेटिंग्स</div>
        <a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-tags"></i> Categories
        </a>
        
        <a href="{{ route('admin.settings.site') }}" class="{{ request()->routeIs('admin.settings.site') ? 'active' : '' }}" onclick="closeSidebar()">
            <i class="fas fa-cog"></i> सामान्य सेटिंग्स
        </a>
        <div class="sidebar-submenu">
            <a href="{{ route('admin.settings.revenue') }}" class="{{ request()->routeIs('admin.settings.revenue') ? 'active' : '' }}" onclick="closeSidebar()">
                <i class="fas fa-money-bill-wave"></i> रेवेन्यू सेटिंग्स
            </a>
            <a href="{{ route('admin.settings.navigation') }}" class="{{ request()->routeIs('admin.settings.navigation') ? 'active' : '' }}" onclick="closeSidebar()">
                <i class="fas fa-link"></i> नेविगेशन मेनू
            </a>
        </div>

        <div style="padding:20px">
            <form action="{{ route('admin.logout') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-danger" style="width:100%;justify-content:center">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </div>
    </nav>
</div>

{{-- Topbar --}}
<div class="topbar">
    <div class="topbar-left">
        <button class="menu-btn" onclick="openSidebar()">
            <i class="fas fa-bars"></i>
        </button>
        <img src="/images/logo.png" alt="logo">
        <span>द पब्लिक एक्सप्रेस</span>
    </div>
    <div style="display:flex;align-items:center;gap:12px">
        {{-- Notification Bell --}}
        @php
            $admin = auth()->user();
            $unreadCount = \App\Models\Notification::where('user_id', $admin->id)->where('is_read', 0)->count();
            $latestNotifs = \App\Models\Notification::where('user_id', $admin->id)
                            ->orderBy('created_at', 'desc')
                            ->limit(5)
                            ->get();
        @endphp

        <div class="notif-wrapper" id="notifWrapper">
            <button class="notif-bell" onclick="toggleNotif()" id="notifBell">
                <i class="fas fa-bell"></i>
                @if($unreadCount > 0)
                    <span class="notif-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
                @endif
            </button>
            <div class="notif-dropdown" id="notifDropdown">
                @if($latestNotifs->count())
                    @foreach($latestNotifs as $notif)
                        @php $data = json_decode($notif->data, true); @endphp
                        <a href="{{ $data['url'] ?? '#' }}" class="notif-item {{ $notif->is_read ? '' : 'unread' }}" onclick="markRead({{ $notif->id }})">
                            <div class="title">
                                @if(!$notif->is_read) <span class="badge-dot"></span> @endif
                                {{ $notif->title }}
                            </div>
                            <div class="body">{{ \Str::limit($notif->body, 80) }}</div>
                            <div class="time">{{ $notif->created_at->diffForHumans() }}</div>
                        </a>
                    @endforeach
                    <div class="notif-footer">
                        <a href="{{ route('admin.notifications.index') }}">सभी सूचनाएँ देखें</a>
                        @if($unreadCount > 0)
                            <span style="margin:0 6px">|</span>
                            <a href="#" onclick="markAllRead()" style="color:#718096;font-weight:400;">सभी पढ़ें</a>
                        @endif
                    </div>
                @else
                    <div class="notif-empty">
                        <i class="fas fa-bell-slash" style="font-size:24px;display:block;margin-bottom:8px"></i>
                        कोई सूचना नहीं
                    </div>
                @endif
            </div>
        </div>

        <span class="topbar-username" style="font-size:13px;color:#718096">
            <i class="fas fa-user-circle" style="color:#e53935"></i>
            {{ auth()->user()->name ?? 'Admin' }}
        </span>
        <form action="{{ route('admin.logout') }}" method="POST" style="margin:0">
            @csrf
            <button type="submit" class="btn btn-sm" style="background:#fee2e2;color:#e53e3e">
                <i class="fas fa-sign-out-alt"></i>
                <span class="topbar-username">Logout</span>
            </button>
        </form>
    </div>
</div>

{{-- Main --}}
<div class="main">
    <div class="main-content">
        @if(session('success'))
            <div class="alert alert-success"><i class="fas fa-check-circle"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error"><i class="fas fa-exclamation-circle"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script>
function openSidebar(){
    document.getElementById('sidebar').classList.add('open');
    document.getElementById('overlay').classList.add('active');
    document.body.style.overflow='hidden';
}
function closeSidebar(){
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('overlay').classList.remove('active');
    document.body.style.overflow='';
}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeSidebar()});

// ===== NOTIFICATIONS =====
function toggleNotif(){
    document.getElementById('notifDropdown').classList.toggle('show');
}
document.addEventListener('click', function(e) {
    const wrapper = document.getElementById('notifWrapper');
    if (!wrapper.contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('show');
    }
});

function markRead(id){
    fetch('{{ route("admin.notifications.mark-read", "") }}/' + id, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {
        window.location.reload();
    });
}

function markAllRead(){
    fetch('{{ route("admin.notifications.mark-all-read") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    }).then(() => {
        window.location.reload();
    });
}
</script>
@stack('scripts')
</body>
</html>