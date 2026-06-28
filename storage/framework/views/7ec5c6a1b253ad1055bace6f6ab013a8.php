<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'द पब्लिक एक्सप्रेस'); ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Noto+Sans+Devanagari:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Noto Sans Devanagari', 'Inter', sans-serif; background: #f1f5f9; color: #0f172a; }
        
        .top-bar { background: #0f172a; color: #fff; font-size: 12px; padding: 8px 0; border-bottom: 3px solid #b91c1c; }
        .top-badge { background: #b91c1c; color: white; padding: 2px 8px; font-weight: 900; font-size: 11px; margin-right: 10px; }
        .top-link { color: #facc15; text-decoration: none; font-weight: bold; margin-left: 15px; }

        .main-header { background: #fff; padding: 15px 0; }
        .logo-img { height: 60px; object-fit: contain; }
        
        .search-container { position: relative; max-width: 500px; width: 100%; }
        .search-input { width: 100%; padding: 10px 90px 10px 20px; border: 2px solid #cbd5e1; border-radius: 6px; font-size: 14px; background: #f8fafc; height: 44px; outline: none; }
        .search-btn { position: absolute; right: 4px; top: 4px; bottom: 4px; background: #0f172a; color: white; border: none; padding: 0 20px; border-radius: 4px; font-weight: bold; font-size: 13px; height: 36px; }
        
        .btn-login { background: #b91c1c; color: white; border: none; padding: 10px 20px; font-weight: 800; border-radius: 4px; text-decoration: none; font-size: 14px; transition: 0.2s; display: inline-flex; align-items: center; gap: 6px; }
        .btn-login:hover { background: #991b1b; color: white; }

        /* ===== NAVIGATION BAR - SAME AS HOME PAGE ===== */
        .nav-bar { background: #0f172a; border-bottom: 4px solid #b91c1c; }
        .nav-link-custom { 
            color: #fff; 
            text-decoration: none; 
            font-weight: 800; 
            font-size: 13px; 
            padding: 12px 18px; 
            display: inline-block; 
            transition: 0.2s; 
            white-space: nowrap;
            letter-spacing: 0.3px;
        }
        .nav-link-custom:hover, .nav-link-custom.active { background: #b91c1c; color: white; }
        .nav-link-custom.home-btn { background: #b91c1c; padding: 12px 25px; }

        /* Dropdown Styling */
        .nav-bar .dropdown-menu {
            background: #1e293b;
            border: none;
            border-radius: 0;
            margin-top: 0;
            padding: 0;
            min-width: 220px;
            border-top: 3px solid #b91c1c;
        }
        .nav-bar .dropdown-item {
            color: #e2e8f0;
            padding: 10px 20px;
            font-weight: 700;
            font-size: 13px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            transition: 0.2s;
        }
        .nav-bar .dropdown-item:hover {
            background: #b91c1c;
            color: white;
        }
        .nav-bar .dropdown-item i { margin-right: 8px; }
        .nav-bar .dropdown-toggle::after { margin-left: 6px; }

        .mobile-bottom-nav { position: fixed; bottom: 0; left: 0; right: 0; background: #fff; box-shadow: 0 -2px 10px rgba(0,0,0,0.1); display: flex; justify-content: space-around; padding: 8px 0; z-index: 1000; border-top: 1px solid #e2e8f0; }
        .mobile-nav-item { text-decoration: none; color: #64748b; font-size: 11px; font-weight: 800; display: flex; flex-direction: column; align-items: center; gap: 2px; }
        .mobile-nav-item.active { color: #b91c1c; }
        .mobile-nav-item i { font-size: 18px; }

        @media (max-width: 767.98px) {
            .desktop-nav { display: none !important; }
            .top-bar-right { display: none !important; }
            .login-btn-box { display: none !important; }
            .main-header { padding: 10px 0; border-bottom: 2px solid #e2e8f0; }
            .logo-img { height: 45px; }
        }
        @media (min-width: 768px) {
            .mobile-bottom-nav { display: none !important; }
            .mobile-menu-hamburger { display: none !important; }
        }
    </style>
</head>
<body>

    <!-- TOP BAR -->
    <div class="top-bar">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <span class="top-badge">मुख्य समाचार</span>
                <span class="text-white fw-semibold"><i class="far fa-calendar-alt me-1"></i> <?php echo e(date('d M Y, l')); ?></span>
                <span class="ms-3 text-white-50 d-none d-sm-inline">📍 क्षेत्र: उत्तर प्रदेश</span>
            </div>
            <div class="top-bar-right d-flex align-items-center">
                <a href="#" class="top-link"><i class="fas fa-mobile-alt me-1"></i> हमारा मोबाइल ऐप</a>
                <a href="#" class="top-link text-warning"><i class="fas fa-ad me-1"></i> विज्ञापन के लिए संपर्क करें</a>
            </div>
        </div>
    </div>

    <!-- MAIN HEADER -->
    <header class="main-header">
        <div class="container">
            <div class="row align-items-center g-3">
                <div class="col-12 col-md-3 d-flex align-items-center justify-content-between">
                    <button class="btn mobile-menu-hamburger p-0 fs-3 text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavCollapse">
                        <i class="fas fa-bars"></i>
                    </button>
                    <a href="<?php echo e(url('/')); ?>" class="mx-auto mx-md-0">
                        <img src="<?php echo e(asset('images/logo.png')); ?>" class="logo-img" alt="द पब्लिक एक्सप्रेस">
                    </a>
                    <div style="width: 30px;" class="mobile-menu-hamburger"></div>
                </div>
                
                <div class="col-12 col-md-6 d-flex justify-content-center">
                    <form action="<?php echo e(route('news.search')); ?>" method="GET" class="search-container">
                        <input type="text" name="q" placeholder="खबरें, राजनीति या जिला खोजें..." class="search-input">
                        <button type="submit" class="search-btn">खोजें</button>
                    </form>
                </div>
                
                <div class="col-12 col-md-3 text-end login-btn-box">
                    <a href="<?php echo e(route('login')); ?>" class="btn-login">
                        <i class="fas fa-sign-in-alt"></i> लॉगिन
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- ============================================================ -->
    <!-- ===== NAVIGATION BAR - COMPLETE WITH ICON + NAME ===== -->
    <!-- ============================================================ -->
    <nav class="nav-bar desktop-nav">
        <div class="container p-0">
            <div class="d-flex align-items-center flex-wrap" style="overflow-x: auto; white-space: nowrap; -webkit-overflow-scrolling: touch;">
                
                <!-- होम -->
                <a href="<?php echo e(url('/')); ?>" class="nav-link-custom home-btn <?php echo e(request()->is('/') ? 'active' : ''); ?>">
                    <i class="fas fa-home me-1"></i> होम
                </a>

                <!-- 🌐 राष्ट्रीय समाचार -->
                <?php
                    $categories = \App\Models\NewsCategory::where('is_active', true)
                                ->whereIn('slug', ['national', 'politics', 'education', 'sports', 'entertainment'])
                                ->orderBy('sort_order')
                                ->get();
                ?>

                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="nav-link-custom <?php echo e(request()->is('category/'.$cat->slug) ? 'active' : ''); ?>">
                        <?php echo e($cat->icon ?? '📰'); ?> <?php echo e($cat->display_name); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- 🗺️ उत्तर प्रदेश -->
                <?php
                    $states = \App\Models\State::where('is_active', true)
                                ->with(['districts' => function($q) { $q->where('is_active', true); }])
                                ->orderBy('name')
                                ->get();
                ?>

                <div class="dropdown d-inline-block">
                    <a href="#" class="nav-link-custom dropdown-toggle" data-bs-toggle="dropdown">
                        🚩 उत्तर प्रदेश
                    </a>
                    <ul class="dropdown-menu">
                        <?php $__currentLoopData = $states; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($state->districts->count() > 0): ?>
                                <li><a class="dropdown-item fw-bold" href="<?php echo e(route('news.state', $state->slug)); ?>">
                                    <i class="fas fa-flag me-1"></i> <?php echo e($state->display_name); ?>

                                </a></li>
                                <?php $__currentLoopData = $state->districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><a class="dropdown-item" href="<?php echo e(route('news.district', $district->slug)); ?>">
                                        <i class="fas fa-location-dot me-1"></i> <?php echo e($district->display_name); ?>

                                    </a></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>

                <!-- 🏆 लीडरबोर्ड -->
                <a href="<?php echo e(route('leaderboard')); ?>" class="nav-link-custom text-warning <?php echo e(request()->routeIs('leaderboard') ? 'active' : ''); ?>">
                    🏆 लीडरबोर्ड
                </a>

            </div>
        </div>
    </nav>

    <!-- MOBILE MENU COLLAPSE -->
    <div class="collapse bg-dark" id="mobileNavCollapse">
        <div class="p-3 d-flex flex-column gap-2">
            <a href="<?php echo e(url('/')); ?>" class="text-white text-decoration-none py-2 border-bottom border-secondary fw-bold">🏠 होम</a>
            
            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="text-white text-decoration-none py-2 border-bottom border-secondary fw-bold">
                    <?php echo e($cat->icon ?? '📰'); ?> <?php echo e($cat->display_name); ?>

                </a>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            
            <a href="<?php echo e(route('login')); ?>" class="text-white text-decoration-none py-2 fw-bold text-danger">🔐 लॉगिन</a>
            <a href="<?php echo e(route('otp.login')); ?>" class="text-white text-decoration-none py-2 fw-bold text-success">📱 OTP लॉगिन</a>
        </div>
    </div>

    <!-- MAIN CONTENT -->
    <main class="container py-4">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <!-- MOBILE BOTTOM NAV -->
    <div class="mobile-bottom-nav">
        <a href="<?php echo e(url('/')); ?>" class="mobile-nav-item active"><i class="fas fa-home"></i><span>होम</span></a>
        <a href="<?php echo e(route('category.show', 'national')); ?>" class="mobile-nav-item"><i class="fas fa-globe"></i><span>देश</span></a>
        <a href="<?php echo e(route('category.show', 'politics')); ?>" class="mobile-nav-item"><i class="fas fa-balance-scale"></i><span>राजनीति</span></a>
        <a href="<?php echo e(route('category.show', 'education')); ?>" class="mobile-nav-item"><i class="fas fa-graduation-cap"></i><span>शिक्षा</span></a>
    </div>

    <!-- FOOTER -->
    <footer class="bg-dark text-white text-center py-4 mt-5 d-none d-md-block">
        &copy; <?php echo e(date('Y')); ?> द पब्लिक एक्सप्रेस. सर्वाधिकार सुरक्षित।
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html><?php /**PATH C:\xampp\htdocs\public-express\resources\views/layouts/app.blade.php ENDPATH**/ ?>