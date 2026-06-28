<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel') - द पब्लिक एक्सप्रेस</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { box-sizing: border-box; }
        body { background: #f0f2f5; overflow-x: hidden; }
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            width: 280px;
            background: linear-gradient(180deg, #c62828 0%, #8e0000 100%);
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
            color: rgba(255,255,255,0.85);
            padding: 12px 20px;
            margin: 2px 8px;
            border-radius: 10px;
            transition: all 0.2s;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            cursor: pointer;
        }
        .sidebar .nav-link:hover { background: rgba(255,255,255,0.12); color: white; }
        .sidebar .nav-link.active { background: rgba(255,255,255,0.2); color: white; font-weight: 600; }
        .sidebar .nav-link i { width: 28px; font-size: 1.1rem; text-align: center; margin-right: 12px; }
        .sidebar .dropdown-menu {
            background: rgba(255,255,255,0.1);
            border: none;
            border-radius: 10px;
            padding: 5px 0;
            margin: 0 8px;
            position: relative;
            width: auto;
            display: none;
        }
        .sidebar .dropdown-menu.show {
            display: block;
        }
        .sidebar .dropdown-menu .dropdown-item {
            color: rgba(255,255,255,0.85);
            padding: 10px 20px 10px 48px;
            border-radius: 8px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            text-decoration: none;
            background: transparent;
        }
        .sidebar .dropdown-menu .dropdown-item:hover {
            background: rgba(255,255,255,0.12);
            color: white;
        }
        .sidebar .dropdown-menu .dropdown-item i {
            width: 24px;
            margin-right: 10px;
            font-size: 0.9rem;
        }
        .sidebar .logout-btn {
            background: transparent;
            border: none;
            color: rgba(255,255,255,0.85);
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
        .sidebar .logout-btn:hover { background: rgba(255,255,255,0.12); color: white; }
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
        .dropdown-menu {
            animation: fadeIn 0.2s ease-in-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .sidebar .dropdown-menu .dropdown-item.active {
            background: rgba(255,255,255,0.2);
            color: white;
            font-weight: 600;
        }
    </style>
</head>
<body>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="sidebar" id="sidebar">
        <div class="brand">
            <h4>📰 द पब्लिक</h4>
            <small>Admin Panel</small>
        </div>

        <nav class="nav flex-column mt-2">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.news.*') ? 'active' : '' }}" href="{{ route('admin.news.index') }}">
                <i class="fas fa-newspaper"></i> News
            </a>
            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                <i class="fas fa-users"></i> Users
            </a>
            <a class="nav-link {{ request()->routeIs('admin.withdrawals.*') ? 'active' : '' }}" href="{{ route('admin.withdrawals.index') }}">
                <i class="fas fa-money-bill-wave"></i> Withdrawals
            </a>

            <!-- Locations Dropdown -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" 
                   href="#" onclick="event.preventDefault(); toggleDropdown(this);">
                    <i class="fas fa-map-marker-alt"></i> Locations
                </a>
                <ul class="dropdown-menu" id="locationsMenu">
                    <li><a class="dropdown-item {{ request()->routeIs('admin.locations.states') ? 'active' : '' }}" 
                           href="{{ route('admin.locations.states') }}">
                        <i class="fas fa-flag"></i> States
                    </a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.locations.districts') ? 'active' : '' }}" 
                           href="{{ route('admin.locations.districts') }}">
                        <i class="fas fa-city"></i> Districts
                    </a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.locations.tehsils') ? 'active' : '' }}" 
                           href="{{ route('admin.locations.tehsils') }}">
                        <i class="fas fa-layer-group"></i> Tehsils
                    </a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.locations.blocks') ? 'active' : '' }}" 
                           href="{{ route('admin.locations.blocks') }}">
                        <i class="fas fa-th"></i> Blocks
                    </a></li>
                </ul>
            </div>

            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="fas fa-tags"></i> Categories
            </a>

            <a class="nav-link {{ request()->routeIs('admin.reporter-assignments.*') ? 'active' : '' }}" href="{{ route('admin.reporter-assignments.index') }}">
                <i class="fas fa-user-check"></i> Reporter Assignments
            </a>

            <!-- Settings Dropdown -->
            <div class="dropdown">
                <a class="nav-link dropdown-toggle {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                   href="#" onclick="event.preventDefault(); toggleDropdown(this);">
                    <i class="fas fa-cog"></i> Settings
                </a>
                <ul class="dropdown-menu" id="settingsMenu">
                    <li><a class="dropdown-item {{ request()->routeIs('admin.settings.revenue') ? 'active' : '' }}" 
                           href="{{ route('admin.settings.revenue') }}">
                        <i class="fas fa-money-bill-wave"></i> Revenue Settings
                    </a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.settings.site') ? 'active' : '' }}" 
                           href="{{ route('admin.settings.site') }}">
                        <i class="fas fa-globe"></i> Site Settings
                    </a></li>
                    <li><a class="dropdown-item {{ request()->routeIs('admin.settings.navigation') ? 'active' : '' }}" 
                           href="{{ route('admin.settings.navigation') }}">
                        <i class="fas fa-bars"></i> Navigation Menu
                    </a></li>
                </ul>
            </div>

            <hr class="my-3 mx-3" style="border-color: rgba(255,255,255,0.15);">

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
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
                <span class="name d-none d-sm-inline">{{ auth()->user()->name ?? 'Admin' }}</span>
                <div class="avatar">{{ substr(auth()->user()->name ?? 'A', 0, 1) }}</div>
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

        // =======================================================
        // DROPDOWN TOGGLE FUNCTION - YAHAN SE ADD KIYA HAI
        // =======================================================
        function toggleDropdown(element) {
            var dropdownMenu = element.nextElementSibling;
            
            // Close all other dropdowns
            document.querySelectorAll('.sidebar .dropdown-menu.show').forEach(function(menu) {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                }
            });
            
            // Toggle current dropdown
            if (dropdownMenu.classList.contains('show')) {
                dropdownMenu.classList.remove('show');
            } else {
                dropdownMenu.classList.add('show');
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            var isInsideSidebar = event.target.closest('.sidebar');
            if (!isInsideSidebar) {
                document.querySelectorAll('.sidebar .dropdown-menu.show').forEach(function(menu) {
                    menu.classList.remove('show');
                });
            }
        });
        // =======================================================
        // DROPDOWN TOGGLE FUNCTION - YAHAN TAK
        // =======================================================

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