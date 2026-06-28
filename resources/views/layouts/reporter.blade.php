{{-- resources/views/layouts/reporter.blade.php --}}
<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'रिपोर्टर पैनल') - द पब्लिक एक्सप्रेस</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Noto Sans Devanagari', sans-serif; background: #f0f2f5; overflow-x: hidden; }
        
        /* Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 100%);
            z-index: 1050;
            overflow-y: auto;
            transition: transform 0.3s ease-in-out;
            transform: translateX(0);
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .sidebar.closed { transform: translateX(-280px); }
        .sidebar .brand { padding: 20px; text-align: center; color: white; border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar .brand h4 { font-weight: 700; margin-bottom: 0; }
        .sidebar .brand small { opacity: 0.8; }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 8px;
            border-radius: 10px;
            transition: all 0.2s;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            text-decoration: none;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar .nav-link.active { background: rgba(255,255,255,0.15); color: white; font-weight: 600; }
        .sidebar .nav-link i { width: 28px; font-size: 1.1rem; text-align: center; margin-right: 12px; }
        .sidebar .logout-btn {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            margin: 2px 8px;
            border-radius: 10px;
            width: calc(100% - 16px);
            text-align: left;
            font-size: 0.95rem;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        .sidebar .logout-btn:hover { background: rgba(255,255,255,0.1); color: white; }
        .sidebar .user-card {
            padding: 16px 20px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        .sidebar .user-card .avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: #c62828;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
            font-weight: 700;
        }
        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1040;
            display: none;
        }
        .sidebar-overlay.active { display: block; }
        
        /* Main Content */
        .main-content { margin-left: 280px; min-height: 100vh; padding: 20px; transition: margin-left 0.3s; }
        .main-content.expanded { margin-left: 0; }
        .topbar {
            background: white;
            border-radius: 12px;
            padding: 12px 20px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid #e9ecef;
        }
        .topbar .menu-toggle {
            background: none;
            border: none;
            font-size: 1.5rem;
            color: #333;
            cursor: pointer;
            padding: 4px 8px;
            display: none;
            border-radius: 8px;
        }
        .topbar .menu-toggle:hover { background: #f0f0f0; }
        .topbar .page-title { font-size: 1.2rem; font-weight: 700; color: #1a1a2e; margin: 0; }
        .topbar .user-info { display: flex; align-items: center; gap: 12px; font-size: 0.9rem; }
        .topbar .user-info .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #c62828;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.9rem;
        }
        .topbar .points-badge {
            background: #fef3c7;
            color: #92400e;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
        }
        .card { border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.04); border: 1px solid #e9ecef; }
        .card-header { background: white; border-bottom: 1px solid #eee; font-weight: 600; padding: 16px 20px; }
        .btn-primary { background-color: #c62828; border-color: #c62828; }
        .btn-primary:hover { background-color: #8e0000; border-color: #8e0000; }
        .badge-published { background: #28a745; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
        .badge-pending { background: #ffc107; color: #333; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
        .badge-rejected { background: #dc3545; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
        .badge-draft { background: #6c757d; color: white; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; }
        
        @media (max-width: 991px) {
            .sidebar { transform: translateX(-280px); }
            .sidebar.open { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .topbar .menu-toggle { display: block; }
        }
        @media (max-width: 576px) {
            .main-content { padding: 12px; }
            .topbar { padding: 10px 16px; flex-wrap: wrap; gap: 8px; }
            .topbar .user-info .name { display: none; }
            .sidebar { width: 260px; }
        }
        .sidebar::-webkit-scrollbar { width: 4px; }
        .sidebar::-webkit-scrollbar-track { background: transparent; }
        .sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.3); border-radius: 4px; }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h4>📰 द पब्लिक</h4>
            <small>रिपोर्टर पैनल</small>
        </div>

        <div class="user-card">
            <div class="d-flex align-items-center gap-3">
                <div class="avatar">{{ substr(auth()->user()->name ?? 'R', 0, 1) }}</div>
                <div>
                    <div class="fw-bold">{{ auth()->user()->name ?? 'Reporter' }}</div>
                    <small class="opacity-75">{{ auth()->user()->role_label ?? 'Reporter' }}</small>
                </div>
            </div>
            <div class="mt-2 d-flex gap-3">
                <span>⭐ {{ number_format(auth()->user()->points ?? 0) }}</span>
                <span>💰 ₹{{ number_format(auth()->user()->wallet_balance ?? 0, 2) }}</span>
            </div>
        </div>

        <nav class="nav flex-column mt-2">
            <a class="nav-link {{ request()->routeIs('reporter.dashboard') ? 'active' : '' }}" href="{{ route('reporter.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('reporter.news.*') ? 'active' : '' }}" href="{{ route('reporter.news.index') }}">
                <i class="fas fa-newspaper"></i> मेरी खबरें
            </a>
            <a class="nav-link {{ request()->routeIs('reporter.news.create') ? 'active' : '' }}" href="{{ route('reporter.news.create') }}">
                <i class="fas fa-plus-circle"></i> नई खबर
            </a>
            <a class="nav-link {{ request()->routeIs('reporter.wallet') ? 'active' : '' }}" href="{{ route('reporter.wallet') }}">
                <i class="fas fa-wallet"></i> वॉलेट
            </a>
            <a class="nav-link {{ request()->routeIs('reporter.notifications*') ? 'active' : '' }}" href="{{ route('reporter.notifications') }}">
                <i class="fas fa-bell"></i> सूचनाएं
                @php
                    $unreadCount = App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
                @endif
            </a>
            <a class="nav-link {{ request()->routeIs('reporter.profile') ? 'active' : '' }}" href="{{ route('reporter.profile') }}">
                <i class="fas fa-user"></i> प्रोफाइल
            </a>

            <hr class="my-3 mx-3" style="border-color: rgba(255,255,255,0.15);">

            <form method="POST" action="{{ route('reporter.logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> लॉगआउट
                </button>
            </form>
        </nav>
    </div>

    <div class="main-content" id="mainContent">
        <div class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="menu-toggle" id="menuToggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h5 class="page-title">@yield('title', 'Dashboard')</h5>
            </div>
            <div class="user-info">
                <span class="points-badge">⭐ {{ number_format(auth()->user()->points ?? 0) }} पॉइंट्स</span>
                <span class="name d-none d-sm-inline">{{ auth()->user()->name ?? 'Reporter' }}</span>
                <div class="avatar">{{ substr(auth()->user()->name ?? 'R', 0, 1) }}</div>
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

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (window.innerWidth <= 991) {
                sidebar.classList.toggle('open');
                overlay.classList.toggle('active');
            } else {
                sidebar.classList.toggle('closed');
                document.getElementById('mainContent').classList.toggle('expanded');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            if (window.innerWidth <= 991) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('active');
            }
        });

        window.addEventListener('resize', function() {
            if (window.innerWidth > 991) {
                document.getElementById('sidebar').classList.remove('open');
                document.getElementById('sidebarOverlay').classList.remove('active');
            }
        });
    </script>
</body>
</html>