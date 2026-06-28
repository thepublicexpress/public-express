<!DOCTYPE html>
<html lang="hi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>द पब्लिक एक्सप्रेस - पब्लिक की आवाज</title>
    
    <!-- फेविकॉन -->
    <link rel="icon" type="image/x-icon" href="<?php echo e(asset('images/favicon.ico')); ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo e(asset('images/favicon.ico')); ?>">

    <!-- फॉन्ट ऑसम आइकन्स -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- गूगल फॉन्ट्स -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&family=Noto+Sans+Devanagari:wght@400;600;700;900&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            light: '#ff1e1e',
                            DEFAULT: '#e30613',
                            dark: '#b3000b',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        * { 
            font-family: 'Noto Sans Devanagari', 'Inter', sans-serif; 
            box-sizing: border-box;
            -webkit-tap-highlight-color: transparent;
        }
        html, body {
            overflow-x: hidden;
            margin: 0;
            padding: 0;
            width: 100%;
            background-color: #f1f5f9;
            color: #0f172a;
        }
        
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }

        .mobile-menu-panel {
            position: fixed;
            top: 0;
            left: -100%;
            width: 85%;
            max-width: 360px;
            height: 100vh;
            background: white;
            z-index: 100000;
            overflow-y: auto;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 25px 0 50px -12px rgba(0, 0, 0, 0.25);
        }
        .mobile-menu-panel.open { left: 0; }
        .mobile-menu-overlay {
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(15, 23, 42, 0.6);
            backdrop-filter: blur(4px);
            z-index: 99999;
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .mobile-menu-overlay.active { display: block; opacity: 1; }
    </style>
</head>
<body class="antialiased">

    <!-- मोबाइल ओवरले -->
    <div id="mobileOverlay" class="mobile-menu-overlay" onclick="closeMobileMenu()"></div>

    <!-- मोबाइल साइडबार मेनू -->
    <div id="mobileMenuPanel" class="mobile-menu-panel flex flex-col justify-between">
        <div>
            <div class="p-4 bg-slate-900 flex items-center justify-between text-white border-b-4 border-brand">
                <a href="/" class="flex flex-col">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="" class="h-10 w-auto object-contain" onerror="this.style.display='none'; document.getElementById('mob-text-logo').style.display='block';">
                    <div id="mob-text-logo" style="display:none;" class="flex flex-col">
                        <span class="text-xl font-black tracking-tighter text-white">द <span class="text-brand">पब्लिक</span></span>
                        <span class="text-xs font-bold tracking-widest text-slate-400 -mt-1">एक्सप्रेस</span>
                    </div>
                </a>
                <button onclick="closeMobileMenu()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-lg hover:bg-white/20 transition">✕</button>
            </div>
            
            <div class="p-4 space-y-1">
                <a href="/" class="flex items-center gap-3 p-3 rounded-xl bg-brand text-white font-bold transition">🏠 मुख्य पृष्ठ (होम)</a>
                
                <div class="pt-4">
                    <p class="text-xs font-black text-slate-400 px-3 uppercase tracking-wider mb-2">मुख्य श्रेणियां</p>
                    <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="flex items-center p-3 text-slate-800 hover:bg-red-50 hover:text-brand rounded-xl font-bold transition mb-1 border-b border-slate-100">
                            <?php echo e($cat->icon ?? '📰'); ?> <?php echo e($cat->display_name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        </div>
        
        <div class="p-4 border-t border-slate-100 bg-slate-50 space-y-2">
            <a href="<?php echo e(route('leaderboard')); ?>" class="flex justify-center items-center gap-2 p-3 w-full bg-slate-900 text-white font-bold rounded-xl shadow-sm text-sm">🏆 सर्वश्रेष्ठ रिपोर्टर सूची</a>
            <a href="<?php echo e(route('login')); ?>" class="flex justify-center items-center gap-2 p-3 w-full bg-brand text-white font-bold rounded-xl shadow-sm text-sm">🔐 लॉगिन</a>
            <a href="<?php echo e(route('otp.login')); ?>" class="flex justify-center items-center gap-2 p-3 w-full bg-green-600 text-white font-bold rounded-xl shadow-sm text-sm">📱 OTP लॉगिन</a>
        </div>
    </div>

    <!-- टॉप पट्टी -->
    <div class="bg-slate-950 text-slate-300 text-xs py-2.5 hidden md:block border-b border-slate-800">
        <div class="container mx-auto px-6 flex justify-between items-center">
            <div class="flex items-center gap-6 font-semibold">
                <span class="bg-brand text-white font-black px-2.5 py-0.5 rounded text-[11px] tracking-wider animate-pulse">मुख्य समाचार</span>
                <span>📅 <?php echo e(date('d M Y, l')); ?></span>
                <span class="flex items-center gap-1">📍 क्षेत्र: <span class="text-white font-bold">उत्तर प्रदेश</span></span>
                <span>🌤️ तापमान: <span class="text-amber-400 font-bold">29°C</span></span>
            </div>
            <div class="flex gap-5 font-bold">
                <a href="#" class="hover:text-white transition flex items-center gap-1 text-brand">📱 हमारा mobile ऐप</a>
                <span class="text-slate-700">|</span>
                <a href="#" class="hover:text-white transition">🤝 विज्ञापन के लिए संपर्क करें</a>
            </div>
        </div>
    </div>

    <!-- मुख्य हेडर -->
    <header class="bg-white shadow-md sticky top-0 z-50 border-b-4 border-brand">
        <div class="container mx-auto px-4 md:px-6 py-2 flex items-center justify-between gap-4 relative">
            
            <button class="p-2 -ml-2 rounded-xl text-slate-800 hover:bg-slate-100 md:hidden focus:outline-none transition z-10" onclick="openMobileMenu()">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            
            <div class="absolute inset-x-0 mx-auto flex justify-center md:static md:inset-auto md:mx-0 md:justify-start pointer-events-none md:pointer-events-auto">
                <a href="/" class="flex items-center flex-shrink-0 group py-1 min-w-[160px] md:min-w-[200px] justify-center md:justify-start pointer-events-auto">
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="" class="h-11 md:h-16 w-auto object-contain transform group-hover:scale-102 transition duration-300" onerror="this.style.display='none'; document.getElementById('desk-text-logo').style.display='flex';">
                    
                    <div id="desk-text-logo" style="display:none;" class="items-center gap-3">
                        <div class="bg-brand text-white px-4 py-2 rounded-none flex flex-col justify-center items-center shadow-lg">
                            <span class="text-2xl md:text-3xl font-black tracking-tighter leading-none">पब्लिक</span>
                            <div class="bg-white text-slate-950 px-2 py-0.5 text-[9px] md:text-[10px] font-black tracking-[0.25em] uppercase mt-1 w-full text-center">एक्सप्रेस</div>
                        </div>
                        <div class="hidden sm:block border-l-2 border-slate-300 pl-3">
                            <h1 class="text-lg font-black text-slate-950 leading-tight tracking-tight">द पब्लिक एक्सप्रेस</h1>
                            <p class="text-[12px] font-bold tracking-wider text-brand uppercase">पब्लिक की आवाज</p>
                        </div>
                    </div>
                </a>
            </div>
            
            <!-- खोजें बार -->
            <form action="<?php echo e(route('news.search')); ?>" method="GET" class="flex-1 max-w-md hidden md:block z-10">
                <div class="flex relative group">
                    <input type="text" name="q" placeholder="खबरें, राजनीति या जिला खोजें..." 
                        class="w-full px-5 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-brand focus:bg-white text-sm font-semibold transition-all pl-12">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                    <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-slate-950 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-brand transition shadow">खोजें</button>
                </div>
            </form>
            
            <div class="flex gap-3 flex-shrink-0 items-center z-10">
                <a href="<?php echo e(route('login')); ?>" class="bg-brand text-white px-5 py-2.5 rounded-none text-xs font-black uppercase tracking-wider hover:bg-slate-950 transition shadow-md items-center gap-2 hidden md:flex">
                    <span>🔐 लॉगिन</span>
                </a>
            </div>
        </div>

        <!-- ==================== 🛠️ मुख्य नेविगेशन बार - ONLY 5 CATEGORIES ==================== -->
        <nav class="bg-slate-900 hidden md:block shadow-inner border-t border-slate-800">
            <div class="container mx-auto px-6 py-0.5 flex items-center gap-1 text-sm font-black text-white whitespace-nowrap overflow-x-visible relative">
                
                <a href="/" class="px-5 py-3 bg-brand text-white text-xs font-black tracking-wider flex items-center gap-1.5 transition hover:bg-red-700">
                    <i class="fas fa-home"></i> होम
                </a>

                <!-- ONLY 5 CATEGORIES - Display in Hindi -->
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="px-4 py-3 hover:bg-brand hover:text-white text-slate-200 text-xs tracking-wider transition font-bold">
                        <?php echo e($cat->icon ?? '📰'); ?> <?php echo e($cat->display_name); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                <!-- 🗺️ उत्तर प्रदेश -->
                <div class="relative group">
                    <button class="px-4 py-3 bg-slate-800 hover:bg-brand hover:text-white text-slate-200 text-xs tracking-wider transition font-bold flex items-center gap-1.5 focus:outline-none">
                        🚩 उत्तर प्रदेश <i class="fas fa-chevron-down text-[10px] opacity-80"></i>
                    </button>
                    
                    <div class="absolute left-0 mt-0 w-52 bg-slate-900 border-t-2 border-brand shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                        <?php $__currentLoopData = $states ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($state->districts->count() > 0): ?>
                                <div class="relative group/district border-b border-slate-800/60">
                                    <a href="<?php echo e(route('news.state', $state->slug)); ?>" class="w-full text-left px-4 py-3 hover:bg-brand transition-colors flex justify-between items-center text-xs text-slate-200 font-bold">
                                        📍 <?php echo e($state->display_name); ?> <i class="fas fa-chevron-right text-[9px] opacity-60"></i>
                                    </a>
                                    <div class="absolute left-full top-0 w-44 bg-slate-900 shadow-xl opacity-0 invisible group-hover/district:opacity-100 group-hover/district:visible transition-all duration-150 z-50 border-l border-slate-800">
                                        <?php $__currentLoopData = $state->districts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $district): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <a href="<?php echo e(route('news.district', $district->slug)); ?>" class="block px-4 py-2.5 hover:bg-brand text-xs text-slate-300 font-semibold border-b border-slate-800/40">
                                                <?php echo e($district->display_name); ?>

                                            </a>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>

                <span class="w-px h-5 bg-slate-800 mx-2"></span>
                
                <a href="<?php echo e(route('leaderboard')); ?>" class="px-4 py-3 hover:bg-amber-500 hover:text-slate-950 text-amber-400 text-xs tracking-wider transition font-bold flex items-center gap-1">🏆 लीडरबोर्ड</a>
            </div>
        </nav>
    </header>

    <!-- ===== मुख्य सामग्री एरिया ===== -->
    <main class="container mx-auto px-4 md:px-6 py-6 max-w-7xl pb-24 md:pb-12">

        <!-- 🛠️ हाइपरलोकल खोज पट्टी -->
        <div class="bg-gradient-to-br from-slate-950 to-slate-900 rounded-none border-t-4 border-brand p-5 md:p-6 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5 relative z-10">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2.5 h-2.5 rounded-full bg-brand animate-ping"></span>
                        <p class="text-brand text-xs font-black tracking-widest">📍 लाइव लोकल सर्च</p>
                    </div>
                    <h2 class="text-xl md:text-2xl font-black tracking-tight">हर कस्बे गाँव और सिटी की खबरें</h2>
                </div>
                <div class="flex gap-2.5 flex-wrap w-full lg:w-auto">
                    <!-- राज्य -->
                    <select id="stateSelect" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-slate-900 text-xs md:text-sm font-bold bg-white border-2 border-slate-200 focus:outline-none focus:border-brand shadow-sm">
                        <option value="">राज्य चुनें</option>
                        <?php $__currentLoopData = $states ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $state): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($state->id); ?>"><?php echo e($state->display_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                    <!-- जिला -->
                    <select id="districtSelect" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-slate-900 text-xs md:text-sm font-bold bg-white border-2 border-slate-200 focus:outline-none focus:border-brand shadow-sm">
                        <option value="">जिला चुनें</option>
                    </select>
                    <!-- तहसील -->
                    <select id="tehsilSelect" class="flex-1 lg:flex-none px-4 py-3 rounded-xl text-slate-900 text-xs md:text-sm font-bold bg-white border-2 border-slate-200 focus:outline-none focus:border-brand shadow-sm">
                        <option value="">तहसील चुनें</option>
                    </select>
                    <button onclick="goToLocation()" class="w-full lg:w-auto px-6 py-3 bg-brand text-white font-black text-sm hover:bg-white hover:text-slate-950 transition-all transform active:scale-95 shadow-md tracking-wider">
                        <span>खबरें देखें</span> ➔
                    </button>
                </div>
            </div>
        </div>

        <!-- ग्रिड सेक्शन -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-12">
            <!-- बड़ी मुख्य खबर -->
            <div class="lg:col-span-2">
                <?php if($latestNews->first()): ?>
                <div class="relative rounded-none overflow-hidden shadow-2xl group bg-slate-900 h-[360px] md:h-[460px] border-b-4 border-brand">
                    <img src="<?php echo e(url('/serve-image/' . urlencode($latestNews->first()->featured_image))); ?>" 
                         alt="<?php echo e($latestNews->first()->title); ?>" 
                         class="w-full h-full object-cover opacity-90 group-hover:opacity-75 group-hover:scale-105 transition duration-700"
                         onerror="this.src='<?php echo e(asset('images/default-news.jpg')); ?>'">
                    <div class="absolute inset-0 bg-gradient-to-t from-black via-black/70 to-transparent p-5 md:p-8 flex flex-col justify-end">
                        <div class="flex gap-2 mb-3">
                            <span class="bg-brand text-white text-[10px] font-black tracking-widest px-3 py-1">🔥 मुख्य समाचार</span>
                            <span class="bg-white text-slate-950 text-[10px] font-black px-3 py-1"><?php echo e($latestNews->first()->category->display_name ?? 'ताजा खबर'); ?></span>
                        </div>
                        <h2 class="text-xl md:text-3xl font-black text-white mb-3 leading-tight tracking-tight">
                            <a href="<?php echo e(route('news.show', $latestNews->first()->slug)); ?>" class="hover:text-brand transition"><?php echo e($latestNews->first()->title); ?></a>
                        </h2>
                        <p class="text-slate-300 text-xs md:text-sm line-clamp-2 font-semibold"><?php echo e(Str::limit($latestNews->first()->summary ?? $latestNews->first()->body, 120)); ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- साइडबार आंकड़े -->
            <div class="flex flex-col justify-between gap-6">
                <!-- लाइव आंकड़े -->
                <div class="bg-white rounded-none shadow-md p-5 border-t-4 border-slate-950 flex-1">
                    <h3 class="font-black text-slate-950 text-xs tracking-widest mb-4 flex items-center gap-2 border-b-2 border-slate-100 pb-2">📊 आज के लाइव आंकड़े</h3>
                    <div class="grid grid-cols-2 gap-3.5">
                        <div class="bg-slate-50 p-3.5 border-l-4 border-brand">
                            <p class="text-2xl font-black text-slate-950"><?php echo e($totalNews ?? $latestNews->total() ?? 0); ?></p>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 tracking-wider">कुल खबरें</p>
                        </div>
                        <div class="bg-slate-50 p-3.5 border-l-4 border-slate-900">
                            <p class="text-2xl font-black text-slate-900"><?php echo e($todayNews ?? 0); ?></p>
                            <p class="text-[10px] font-bold text-slate-400 mt-0.5 tracking-wider">आज की खबरें</p>
                        </div>
                    </div>
                </div>

                <!-- रिपोर्टर रैंकिंग कार्ड -->
                <div class="bg-white rounded-none shadow-md p-5 border-t-4 border-brand flex-1">
                    <div class="flex justify-between items-center mb-4 border-b border-slate-100 pb-2">
                        <h3 class="font-black text-slate-950 text-xs tracking-widest flex items-center gap-2">🏆 हमारे सर्वश्रेष्ठ रिपोर्टर</h3>
                        <a href="<?php echo e(route('leaderboard')); ?>" class="text-brand text-xs font-black hover:underline">सूची देखें →</a>
                    </div>
                    <div class="space-y-3">
                        <?php $__currentLoopData = $topReporters ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $reporter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="flex items-center gap-3 p-1.5 hover:bg-slate-50 transition border-b border-slate-50 last:border-0">
                            <div class="w-5 text-center font-black text-slate-900 text-xs">#<?php echo e($index + 1); ?></div>
                            <div class="w-9 h-9 bg-slate-950 flex items-center justify-center text-white font-black text-xs shadow-sm border-b-2 border-brand">
                                <?php echo e(substr($reporter->name, 0, 1)); ?>

                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-black text-slate-900 truncate"><?php echo e($reporter->name); ?></p>
                                <p class="text-[10px] font-bold text-slate-400 mt-0.5"><?php echo e(number_format($reporter->points ?? 0)); ?> पॉइंट</p>
                            </div>
                        </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- मुख्य स्ट्रीम खबरें -->
        <div class="mb-12">
            <div class="flex justify-between items-center mb-6 border-b-2 border-slate-950 pb-2">
                <h2 class="text-base md:text-lg font-black text-slate-950 tracking-wide">📰 ताज़ा मुख्य बुलेटिन समाचार</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php $__currentLoopData = $latestNews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="bg-white rounded-none shadow-md border border-slate-200 overflow-hidden transform hover:-translate-y-1 hover:shadow-lg transition flex flex-col justify-between">
                    <div>
                        <div class="relative overflow-hidden aspect-video bg-slate-100 border-b-2 border-slate-100">
                            <img src="<?php echo e(url('/serve-image/' . urlencode($item->featured_image))); ?>" 
                                 alt="<?php echo e($item->title); ?>" 
                                 class="w-full h-full object-cover"
                                 loading="lazy"
                                 onerror="this.src='<?php echo e(asset('images/default-news.jpg')); ?>'">
                            <span class="absolute top-3 left-3 bg-brand text-white text-[9px] font-black tracking-wider px-2 py-0.5 shadow-md"><?php echo e($item->category->display_name ?? 'ताजा समाचार'); ?></span>
                        </div>
                        <div class="p-4">
                            <div class="flex items-center gap-1.5 text-[10px] text-slate-400 font-black tracking-wider mb-2">
                                <span>⏱️ <?php echo e($item->created_at->diffForHumans()); ?></span>
                            </div>
                            <h3 class="font-black text-base text-slate-950 line-clamp-2 mb-2 leading-tight hover:text-brand transition">
                                <a href="<?php echo e(route('news.show', $item->slug)); ?>"><?php echo e($item->title); ?></a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 font-semibold"><?php echo e(Str::limit($item->summary ?? $item->body, 100)); ?></p>
                        </div>
                    </div>
                    <div class="px-4 pb-4 pt-3 flex justify-between items-center border-t border-slate-100">
                        <span class="text-[10px] font-black text-slate-500">👁️ <?php echo e(number_format($item->views ?? 0)); ?> पाठक</span>
                        <a href="<?php echo e(route('news.show', $item->slug)); ?>" class="text-brand text-xs font-black hover:underline flex items-center gap-0.5 tracking-wider">पूरी खबर →</a>
                    </div>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <div class="mt-8 flex justify-center">
                <?php echo e($latestNews->links()); ?>

            </div>
        </div>
    </main>

    <!-- फुटर -->
    <footer class="bg-slate-950 text-slate-400 pt-12 pb-24 md:pb-8 border-t-4 border-brand">
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8 mb-8">
                <div>
                    <img src="<?php echo e(asset('images/logo.png')); ?>" alt="द पब्लिक एक्सप्रेस" class="h-12 w-auto object-contain mb-3 brightness-95" onerror="this.style.display='none';">
                    <p class="text-xs text-slate-500 mt-2 leading-relaxed font-semibold">उत्तर प्रदेश का सबसे तेजी से बढ़ता हुआ डिजिटल न्यूज नेटवर्क। निष्पक्षता और सत्यता ही हमारी पहचान है।</p>
                </div>
                <div>
                    <h4 class="font-black text-xs text-white tracking-widest mb-3 border-b border-slate-900 pb-1">महत्वपूर्ण लिंक</h4>
                    <ul class="space-y-2 text-xs font-bold">
                        <li><a href="/" class="hover:text-white transition">🏠 मुख्य पेज</a></li>
                        <li><a href="<?php echo e(route('leaderboard')); ?>" class="hover:text-white transition">🏆 रिपोर्टर लीडरबोर्ड</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-black text-xs text-white tracking-widest mb-3 border-b border-slate-900 pb-1">अकाउंट</h4>
                    <ul class="space-y-2 text-xs font-bold">
                        <li><a href="<?php echo e(route('login')); ?>" class="hover:text-white transition">🔐 लॉगिन</a></li>
                        <li><a href="<?php echo e(route('register')); ?>" class="hover:text-white transition">📝 रजिस्टर</a></li>
                        <li><a href="<?php echo e(route('otp.login')); ?>" class="hover:text-white transition">📱 OTP लॉगिन</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-black text-xs text-white tracking-widest mb-3 border-b border-slate-900 pb-1">संपर्क</h4>
                    <p class="text-xs leading-relaxed font-bold">📧 admin@thepublicexpress.com</p>
                </div>
            </div>
            <div class="border-t border-slate-900 pt-6 text-center text-xs font-black text-slate-600 tracking-wider">
                © <?php echo e(date('Y')); ?> द पब्लिक एक्सप्रेस नेटवर्क. ऑल राइट्स रिजर्व्ड।
            </div>
        </div>
    </footer>

    <!-- ===== मोबाइल नीचे की तैरती हुई पट्टी ===== -->
    <nav class="md:hidden fixed bottom-4 left-4 right-4 h-16 glass-nav shadow-2xl border-2 border-slate-950/10 z-[9999] flex justify-around items-center px-2 rounded-xl">
        <a href="/" class="flex flex-col items-center justify-center w-12 h-12 text-brand rounded-xl">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M3 13h1v7c0 1.103.897 2 2 2h12c1.103 0 2-.897 2-2v-7h1a1 1 0 00.707-1.707l-9-9a.999.999 0 00-1.414 0l-9 9A1 1 0 003 13z"/></svg>
            <span class="text-[9px] font-black mt-1">होम</span>
        </a>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($cat->slug == 'national'): ?>
                <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl">
                    <span class="text-base">🌐</span>
                    <span class="text-[9px] font-black mt-0.5">देश</span>
                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($cat->slug == 'politics'): ?>
                <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl">
                    <span class="text-base">⚖️</span>
                    <span class="text-[9px] font-black mt-0.5">राजनीति</span>
                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if($cat->slug == 'education'): ?>
                <a href="<?php echo e(route('category.show', $cat->slug)); ?>" class="flex flex-col items-center justify-center w-12 h-12 text-slate-950 hover:text-brand rounded-xl">
                    <span class="text-base">🎓</span>
                    <span class="text-[9px] font-black mt-0.5">शिक्षा</span>
                </a>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </nav>

    <!-- स्क्रिप्ट्स -->
    <script>
        function openMobileMenu() {
            document.getElementById('mobileMenuPanel').classList.add('open');
            document.getElementById('mobileOverlay').classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        function closeMobileMenu() {
            document.getElementById('mobileMenuPanel').classList.remove('open');
            document.getElementById('mobileOverlay').classList.remove('active');
            document.body.style.overflow = '';
        }

        // ===== LOCATION SEARCH - DYNAMIC DROPDOWNS WITH HINDI NAMES =====
        document.getElementById('stateSelect').addEventListener('change', function() {
            const stateId = this.value;
            const districtSelect = document.getElementById('districtSelect');
            const tehsilSelect = document.getElementById('tehsilSelect');
            
            districtSelect.innerHTML = '<option value="">जिला चुनें</option>';
            tehsilSelect.innerHTML = '<option value="">तहसील चुनें</option>';

            if (!stateId) return;

            fetch('/api/get-districts/' + stateId)
                .then(res => res.json())
                .then(data => {
                    data.forEach(d => {
                        // Show Hindi name if available, else English
                        const displayName = d.name_hi || d.name;
                        districtSelect.innerHTML += `<option value="${d.id}">${displayName}</option>`;
                    });
                });
        });

        document.getElementById('districtSelect').addEventListener('change', function() {
            const districtId = this.value;
            const tehsilSelect = document.getElementById('tehsilSelect');
            
            tehsilSelect.innerHTML = '<option value="">तहसील चुनें</option>';

            if (!districtId) return;

            fetch('/api/get-tehsils/' + districtId)
                .then(res => res.json())
                .then(data => {
                    data.forEach(t => {
                        const displayName = t.name_hi || t.name;
                        tehsilSelect.innerHTML += `<option value="${t.id}">${displayName}</option>`;
                    });
                });
        });

        function goToLocation() {
            const stateSelect = document.getElementById('stateSelect');
            const districtSelect = document.getElementById('districtSelect');
            const tehsilSelect = document.getElementById('tehsilSelect');

            const stateId = stateSelect.value;
            const districtId = districtSelect.value;
            const tehsilId = tehsilSelect.value;

            if (tehsilId) {
                fetch('/api/get-tehsils/' + districtId)
                    .then(res => res.json())
                    .then(data => {
                        const tehsil = data.find(t => t.id == tehsilId);
                        if (tehsil && tehsil.slug) {
                            window.location.href = '/news/tehsil/' + tehsil.slug;
                        } else {
                            window.location.href = '/news/district/' + districtId;
                        }
                    });
            } else if (districtId) {
                fetch('/api/get-districts/' + stateId)
                    .then(res => res.json())
                    .then(data => {
                        const district = data.find(d => d.id == districtId);
                        if (district && district.slug) {
                            window.location.href = '/news/district/' + district.slug;
                        }
                    });
            } else if (stateId) {
                fetch('/api/get-states')
                    .then(res => res.json())
                    .then(data => {
                        const state = data.find(s => s.id == stateId);
                        if (state && state.slug) {
                            window.location.href = '/news/state/' + state.slug;
                        }
                    });
            } else {
                alert('कृपया राज्य, जिला या तहसील चुनें।');
            }
        }
    </script>
</body>
</html><?php /**PATH C:\xampp\htdocs\public-express\resources\views/home.blade.php ENDPATH**/ ?>