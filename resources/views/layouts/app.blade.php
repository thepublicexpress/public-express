<!DOCTYPE html>
<html lang="hi">
<head>
    @php
        $siteName = \App\Models\SiteSetting::get('site_name', 'द पब्लिक एक्सप्रेस');
        $logo = \App\Models\SiteSetting::get('site_logo', 'images/logo.png');
        if (!str_starts_with($logo, 'images/')) {
            $logo = 'images/' . $logo;
        }
        $favicon = \App\Models\SiteSetting::get('favicon', 'images/favicon.ico');
        if (!str_starts_with($favicon, 'images/')) {
            $favicon = 'images/' . $favicon;
        }
        $metaDesc = \App\Models\SiteSetting::get('meta_description', 'द पब्लिक एक्सप्रेस – हर कस्बे, गाँव और सिटी की खबरें। ताजा हिंदी समाचार, राजनीति, शिक्षा, खेल और मनोरंजन।');
        $primaryColor = \App\Models\SiteSetting::get('primary_color', '#e30613');
        $footerText = \App\Models\SiteSetting::get('footer_text', 'सर्वाधिकार सुरक्षित। हर कस्बे, गाँव और सिटी की खबरें');
        $contactEmail = \App\Models\SiteSetting::get('contact_email', 'admin@thepublicexpress.com');
        $contactPhone = \App\Models\SiteSetting::get('contact_phone', '+91-999-999-9999');
        $facebook = \App\Models\SiteSetting::get('facebook_url', '#');
        $twitter = \App\Models\SiteSetting::get('twitter_url', '#');
        $instagram = \App\Models\SiteSetting::get('instagram_url', '#');
        $youtube = \App\Models\SiteSetting::get('youtube_url', '#');
        $logoVersion = file_exists(public_path($logo)) ? filemtime(public_path($logo)) : time();
        $faviconVersion = file_exists(public_path($favicon)) ? filemtime(public_path($favicon)) : time();
        $vapidPublicKey = env('FIREBASE_VAPID_KEY', '');
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $metaDesc }}">
    <title>@yield('title', $siteName)</title>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-ER43752WWY"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-ER43752WWY');
    </script>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset($favicon) . '?v=' . $faviconVersion }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset($favicon) . '?v=' . $faviconVersion }}">
    <link rel="manifest" href="/manifest.json">

    @yield('meta_tags')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&family=Noto+Sans+Devanagari:wght@400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: { brand: { light: '#ff1e1e', DEFAULT: '{{ $primaryColor }}', dark: '#b3000b' } }
                }
            }
        }
    </script>
    <style>
        * { font-family: 'Noto Sans Devanagari', 'Inter', sans-serif; box-sizing: border-box; }
        html, body { overflow-x: hidden; background-color: #f1f5f9; color: #0f172a; }
        .glass-nav { background: rgba(255,255,255,0.95); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); }
        .mobile-menu-panel { position: fixed; top: 0; left: -100%; width: 85%; max-width: 360px; height: 100vh; background: white; z-index: 100000; overflow-y: auto; transition: all 0.4s cubic-bezier(0.4,0,0.2,1); box-shadow: 25px 0 50px -12px rgba(0,0,0,0.25); }
        .mobile-menu-panel.open { left: 0; }
        .mobile-menu-overlay { position: fixed; top:0; left:0; right:0; bottom:0; background: rgba(15,23,42,0.6); backdrop-filter: blur(4px); z-index:99999; display:none; opacity:0; transition: opacity 0.3s ease; }
        .mobile-menu-overlay.active { display:block; opacity:1; }
        
        /* ===== PUSH NOTIFICATION BANNER STYLES ===== */
        .permission-banner {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            min-height: 80px;
            box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
        }
        .permission-banner.show { display: flex; }
        .permission-banner .btn-enable {
            background: white;
            color: #667eea;
            border: none;
            padding: 8px 24px;
            border-radius: 50px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        .permission-banner .btn-enable:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }
        .permission-banner .btn-disable {
            background: rgba(255,255,255,0.15);
            color: white;
            border: 1px solid rgba(255,255,255,0.3);
            padding: 8px 20px;
            border-radius: 50px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 14px;
        }
        .permission-banner .btn-disable:hover {
            background: rgba(255,255,255,0.25);
        }
        .permission-banner .status-badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 14px;
            border-radius: 50px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
        }
        .permission-banner .banner-icon {
            font-size: 28px;
            margin-right: 10px;
        }
        .permission-banner .banner-content {
            flex: 1;
            min-width: 200px;
        }
        .permission-banner .banner-title {
            font-weight: 700;
            font-size: 16px;
        }
        .permission-banner .banner-subtitle {
            font-size: 13px;
            opacity: 0.9;
            margin-top: 2px;
        }
        .permission-banner .banner-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .footer-newsletter { background: #0f172a; border-top: 4px solid {{ $primaryColor }}; }
        .footer-newsletter .form-control { background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.2); color: white; border-radius: 6px; padding: 12px 18px; font-size: 14px; width: 100%; }
        .footer-newsletter .form-control::placeholder { color: rgba(255,255,255,0.5); }
        .footer-newsletter .form-control:focus { background: rgba(255,255,255,0.12); border-color: {{ $primaryColor }}; outline: none; }
        .footer-newsletter .btn-subscribe { background: {{ $primaryColor }}; border: none; color: white; font-weight: 800; padding: 12px 25px; border-radius: 6px; transition: 0.2s; width: 100%; }
        .footer-newsletter .btn-subscribe:hover { background: #b3000b; }
        .footer-newsletter a { color: rgba(255,255,255,0.7); text-decoration: none; transition: 0.2s; }
        .footer-newsletter a:hover { color: #facc15; text-decoration: underline; }
        .footer-newsletter .text-muted { color: rgba(255,255,255,0.5) !important; }
        .footer-newsletter hr { border-color: rgba(255,255,255,0.1); }
        .footer-newsletter .social-icon { display: inline-flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 50%; background: rgba(255,255,255,0.08); color: white; transition: 0.2s; }
        .footer-newsletter .social-icon:hover { background: rgba(255,255,255,0.2); transform: translateY(-3px); }

        .share-btn { transition: all 0.3s ease; font-weight: 700; padding: 10px 18px; border-radius: 8px; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; border: none; cursor: pointer; text-decoration: none; color: white; }
        .share-btn:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,0.2); }
        .share-x { background: #000000; } .share-x:hover { background: #1a1a1a; }
        .share-fb { background: #1877f2; } .share-fb:hover { background: #0d65d9; }
        .share-wa { background: #25d366; } .share-wa:hover { background: #1da851; }
        .share-tg { background: #0088cc; } .share-tg:hover { background: #006699; }
        .share-copy { background: #6c757d; } .share-copy:hover { background: #5a6268; }
        .share-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 8px; }

        #notificationBadge { position: absolute; top: -5px; right: -5px; background: {{ $primaryColor }}; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 10px; display: none; align-items: center; justify-content: center; font-weight: bold; }
        .notification-btn-wrapper { position: relative; display: inline-block; }
        .prose { max-width: 100%; }
        .prose p { margin-bottom: 1rem; line-height: 1.8; font-size: 1.05rem; }
        .prose img { max-width: 100%; height: auto; border-radius: 8px; margin: 1.5rem 0; }
        .prose h2, .prose h3, .prose h4 { margin-top: 1.5rem; margin-bottom: 0.75rem; font-weight: 700; }
        .prose ul, .prose ol { margin: 1rem 0; padding-left: 1.5rem; }
        .prose li { margin-bottom: 0.3rem; }

        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 2px solid {{ $primaryColor }};
            outline-offset: 2px;
        }
        .skip-link {
            position: absolute;
            top: -100%;
            left: 10px;
            padding: 10px 20px;
            background: {{ $primaryColor }};
            color: white;
            font-weight: bold;
            z-index: 99999;
            text-decoration: none;
        }
        .skip-link:focus { top: 10px; }

        /* ===== AD STYLES ===== */
        .ad-container { text-align: center; margin: 0 auto; overflow: hidden; }
        .ad-container img { max-width: 100%; height: auto; }
        .ad-badge { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px; }
        .header-ad-wrapper { background: #f8fafc; border-bottom: 1px solid #eef2f6; padding: 8px 0; }
        .ad-item { margin-bottom: 10px; }
        .ad-item:last-child { margin-bottom: 0; }

        /* ============================================================ */
        /* ✅ OPINION POLL POPUP STYLES                                  */
        /* ============================================================ */
        .opinion-poll-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100dvh;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
            z-index: 999999;
            display: none;
            align-items: center;
            justify-content: center;
            overflow-y: auto;
            padding: 12px;
        }
        .opinion-poll-overlay.show {
            display: flex;
        }
        .opinion-poll-container {
            background: white;
            border-radius: 20px;
            max-width: 700px;
            width: 100%;
            max-height: calc(100dvh - 24px);
            overflow-y: auto;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            position: relative;
            animation: pollSlideUp 0.4s ease;
        }
        @keyframes pollSlideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .poll-header {
            background: linear-gradient(135deg, #b91c1c 0%, #dc2626 62%, #991b1b 100%);
            color: white;
            padding: 20px 24px;
            border-radius: 20px 20px 0 0;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        .poll-header h2 {
            font-size: 22px;
            margin: 0;
            font-weight: 700;
        }
        .poll-header .poll-subtitle {
            font-size: 14px;
            opacity: 0.8;
            margin: 5px 0 0 0;
        }
        .poll-leader-strip {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
            margin-top: 14px;
            padding: 9px 52px 9px 8px;
            border-radius: 12px;
            background: linear-gradient(rgba(127,29,29,0.52), rgba(127,29,29,0.68)), url('https://upload.wikimedia.org/wikipedia/commons/8/86/Vidhan_Bhawan_Lucknow.jpg') center/cover;
            border: 1px solid rgba(255,255,255,0.35);
        }
        .poll-party-flags {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            width: 48px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            padding: 4px 3px;
            background: rgba(255,255,255,0.82);
            z-index: 4;
        }
        .poll-party-flag {
            position: relative;
            flex: 1;
            min-height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            color: white;
            font-size: 8px;
            font-weight: 900;
            text-shadow: 0 1px 2px #000;
            border: 1px solid rgba(255,255,255,0.6);
        }
        .poll-party-flag::before {
            content: '';
            position: absolute;
            width: 2px;
            top: -4px;
            bottom: -4px;
            left: 4px;
            background: #e5e7eb;
        }
        .flag-sp { background: linear-gradient(#39a852 0 33%, #fff 33% 66%, #e63946 66%); color: #111827; }
        .flag-bjp { background: linear-gradient(#ff8c00 0 33%, #fff 33% 66%, #138808 66%); color: #111827; }
        .flag-bsp { background: #2364aa; }
        .flag-inc { background: linear-gradient(#138808 0 33%, #fff 33% 66%, #ff8c00 66%); color: #111827; }
        .flag-rld { background: #2f8f46; }
        .flag-sbsp { background: #f3c316; color: #111827; }
        .flag-apna { background: #f28c28; }
        .flag-nishad { background: #8b1e3f; }
        .poll-leader-card {
            position: relative;
            overflow: hidden;
            min-width: 0;
            text-align: center;
            color: white;
            font-size: 12px;
            font-weight: 700;
            padding: 6px 3px 5px;
            border-radius: 10px;
            background: rgba(127,29,29,0.52);
            border: 1px solid rgba(255,255,255,0.25);
        }
        .poll-leader-card::before {
            content: '';
            position: absolute;
            inset: 0;
            opacity: 0.8;
            background: repeating-linear-gradient(155deg, rgba(255,255,255,0.18) 0 8px, transparent 8px 16px);
            z-index: 0;
        }
        .poll-leader-card.party-sp::after { background: linear-gradient(180deg, #39a852 0 34%, #ffffff 34% 67%, #e63946 67%); }
        .poll-leader-card.party-bjp::after { background: linear-gradient(180deg, #ff8c00 0 35%, #ffffff 35% 65%, #138808 65%); }
        .poll-leader-card.party-bsp::after { background: linear-gradient(180deg, #2364aa 0 50%, #101010 50%); }
        .poll-leader-card::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 5px;
            z-index: 1;
        }
        .poll-leader-card img,
        .poll-leader-card span {
            position: relative;
            z-index: 2;
        }
        .poll-leader-card img {
            display: block;
            width: 48px;
            height: 48px;
            object-fit: cover;
            margin: 0 auto 5px;
            border: 2px solid rgba(255,255,255,0.85);
            border-radius: 50%;
            background: rgba(255,255,255,0.2);
        }
        .poll-assembly-image {
            display: block;
            width: 100%;
            height: 92px;
            object-fit: cover;
            object-position: center 35%;
            margin-top: 14px;
            border-radius: 10px;
            border: 1px solid rgba(255,255,255,0.35);
            background: #991b1b;
        }
        .poll-assembly-fallback {
            display: none;
            align-items: center;
            justify-content: center;
            height: 92px;
            margin-top: 14px;
            border-radius: 10px;
            color: white;
            font-weight: 800;
            background: linear-gradient(#b91c1c 0 45%, #fff 45% 52%, #b77b42 52% 100%);
            border: 1px solid rgba(255,255,255,0.35);
        }
        .poll-close-btn {
            position: absolute;
            right: 20px;
            top: 20px;
            background: rgba(255,255,255,0.2);
            border: none;
            color: white;
            width: 36px;
            height: 36px;
            border-radius: 50%;
            font-size: 20px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .poll-close-btn:hover {
            background: rgba(255,255,255,0.3);
            transform: rotate(90deg);
        }
        .poll-body { padding: 22px 24px; min-height: 0; }
        .poll-step { display: block; }
        .poll-step-header { margin-bottom: 20px; }
        .step-number {
            display: inline-block;
            background: #1a56db;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: 700;
            font-size: 14px;
            margin-right: 10px;
        }
        .poll-step-header h3 {
            display: inline-block;
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin: 0;
        }
        .selected-seat-info {
            font-size: 14px;
            color: #16a34a;
            font-weight: 600;
            margin: 8px 0 0 40px;
            padding: 8px 15px;
            background: #dcfce7;
            border-radius: 8px;
            display: inline-block;
        }
        .seat-search-box { margin-bottom: 15px; }
        .seat-search-box input {
            width: 100%;
            padding: 12px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 15px;
            outline: none;
            transition: 0.2s;
        }
        .seat-search-box input:focus {
            border-color: #1a56db;
            box-shadow: 0 0 0 4px rgba(26,86,219,0.1);
        }
        .poll-select {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: white;
            color: #334155;
            font-size: 15px;
            font-weight: 600;
            outline: none;
        }
        .poll-select:focus { border-color: #1a56db; box-shadow: 0 0 0 4px rgba(26,86,219,0.1); }
        .seat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 8px;
            max-height: 300px;
            overflow-y: auto;
            padding: 5px;
        }
        .seat-grid .seat-btn {
            padding: 10px 12px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            text-align: center;
            font-size: 13px;
            font-weight: 500;
            color: #334155;
        }
        .seat-grid .seat-btn:hover {
            border-color: #1a56db;
            background: #eff6ff;
            transform: translateY(-2px);
        }
        .seat-grid .seat-btn.selected {
            border-color: #1a56db;
            background: #1a56db;
            color: white;
        }
        .seat-grid .seat-btn .seat-number {
            font-size: 10px;
            opacity: 0.7;
            display: block;
            margin-top: 2px;
            font-weight: 400;
        }
        .btn-next-step {
            width: 100%;
            padding: 14px;
            background: #1a56db;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-next-step:hover:not(:disabled) {
            background: #0f2b7a;
            transform: translateY(-2px);
        }
        .btn-next-step:disabled {
            background: #94a3b8;
            cursor: not-allowed;
        }
        .questions-container {
            max-height: 400px;
            overflow-y: auto;
            padding-right: 5px;
        }
        .question-item {
            background: #f8fafc;
            border-radius: 12px;
            padding: 18px 20px;
            margin-bottom: 15px;
            border: 2px solid #eef2f6;
            transition: 0.2s;
        }
        .question-item .question-text {
            font-weight: 600;
            color: #0f172a;
            font-size: 15px;
            margin-bottom: 12px;
        }
        .question-item .options-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .question-item .option-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 14px;
            border-radius: 8px;
            background: white;
            border: 2px solid #e2e8f0;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }
        .question-item .option-label:hover {
            border-color: #1a56db;
            background: #eff6ff;
        }
        .question-item .option-label input[type="radio"],
        .question-item .option-label input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: #1a56db;
            cursor: pointer;
        }
        .question-item .option-label.selected {
            border-color: #1a56db;
            background: #dbeafe;
        }
        .poll-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }
        .btn-prev-step {
            padding: 12px 24px;
            background: #e2e8f0;
            color: #334155;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: 0.2s;
            flex: 1;
        }
        .btn-prev-step:hover { background: #cbd5e1; }
        .btn-submit-poll {
            padding: 12px 24px;
            background: #16a34a;
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s;
            flex: 2;
        }
        .btn-submit-poll:hover {
            background: #15803d;
            transform: translateY(-2px);
        }
        .btn-submit-poll:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }
        .poll-success {
            text-align: center;
            padding: 40px 20px;
        }
        .poll-success .success-icon { font-size: 60px; display: block; margin-bottom: 15px; }
        .poll-success h3 { color: #16a34a; font-size: 22px; }
        .poll-success p { color: #64748b; font-size: 15px; margin: 10px 0 0; }
        @media (max-width: 640px) {
            .opinion-poll-overlay { padding: 8px; }
            .opinion-poll-container { border-radius: 12px; max-height: calc(100dvh - 16px); }
            .poll-header { padding: 15px 16px; }
            .poll-header h2 { font-size: 18px; }
            .poll-header .poll-subtitle { font-size: 12px; }
            .poll-leader-strip { gap: 5px; margin-top: 10px; padding: 7px 44px 7px 5px; }
            .poll-leader-card { font-size: 10px; padding: 4px 2px; }
            .poll-leader-card img { width: 40px; height: 40px; margin-bottom: 3px; }
            .poll-body { padding: 16px; }
            .seat-grid { grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); }
            .seat-grid .seat-btn { font-size: 11px; padding: 8px 6px; }
            .question-item { padding: 14px 16px; }
            .poll-actions { flex-direction: column; }
        }
        .seat-grid::-webkit-scrollbar,
        .questions-container::-webkit-scrollbar {
            width: 5px;
        }
        .seat-grid::-webkit-scrollbar-track,
        .questions-container::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }
        .seat-grid::-webkit-scrollbar-thumb,
        .questions-container::-webkit-scrollbar-thumb {
            background: #94a3b8;
            border-radius: 10px;
        }
    </style>

    @stack('styles')
</head>
<body>

<a href="#main-content" class="skip-link">सीधे मुख्य सामग्री पर जाएँ</a>

<!-- ===== MOBILE OVERLAY & MENU ===== -->
<div id="mobileOverlay" class="mobile-menu-overlay" onclick="closeMobileMenu()"></div>
<div id="mobileMenuPanel" class="mobile-menu-panel flex flex-col justify-between">
    <div>
        <div class="p-4 bg-slate-900 flex items-center justify-between text-white border-b-4 border-brand">
            <a href="/" class="flex flex-col">
                <img src="{{ asset($logo) . '?v=' . $logoVersion }}" alt="{{ $siteName }}" class="h-10 w-auto object-contain" loading="lazy" onerror="this.style.display='none'; document.getElementById('mob-text-logo').style.display='block';">
                <div id="mob-text-logo" style="display:none;" class="flex flex-col">
                    <span class="text-xl font-black tracking-tighter text-white">द <span class="text-brand">पब्लिक</span></span>
                    <span class="text-xs font-bold tracking-widest text-slate-400 -mt-1">एक्सप्रेस</span>
                </div>
            </a>
            <button onclick="closeMobileMenu()" class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center text-lg hover:bg-white/20 transition">✕</button>
        </div>
        <div class="p-4 space-y-1">
            <a href="/" class="flex items-center gap-3 p-3 rounded-xl bg-brand text-white font-bold transition">🏠 मुख्य पृष्ठ</a>
            <button type="button" onclick="closeMobileMenu(); showPollPopup();" class="w-full text-left flex items-center gap-3 p-3 rounded-xl bg-amber-50 text-amber-700 font-bold transition">📊 Opinion Poll</button>
            <div class="pt-4">
                <p class="text-xs font-black text-slate-400 px-3 uppercase tracking-wider mb-2">मुख्य श्रेणियां</p>
                @php
                    $cats = \App\Models\Category::where('is_active', 1)->whereIn('slug', ['national', 'politics', 'education', 'sports', 'entertainment'])->orderBy('order')->get();
                @endphp
                @foreach($cats as $cat)
                    <a href="{{ route('category.show', $cat->slug) }}" class="flex items-center p-3 text-slate-800 hover:bg-red-50 hover:text-brand rounded-xl font-bold transition mb-1 border-b border-slate-100">
                        {{ $cat->icon ?? '📰' }} {{ $cat->display_name ?? $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    <div class="p-4 border-t border-slate-100 bg-slate-50 space-y-2">
        @auth
            <div class="flex items-center gap-3 p-3 bg-slate-900 text-white rounded-xl">
                <span class="text-xl">👋</span>
                <div><p class="font-bold text-sm">{{ Auth::user()->name }}</p><p class="text-[10px] text-slate-400">{{ Auth::user()->role }}</p></div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="flex justify-center items-center gap-2 p-3 w-full bg-red-600 text-white font-bold rounded-xl shadow-sm text-sm">🚪 Logout</button>
            </form>
        @else
            <a href="{{ route('leaderboard') }}" class="flex justify-center items-center gap-2 p-3 w-full bg-slate-900 text-white font-bold rounded-xl shadow-sm text-sm">🏆 लीडरबोर्ड</a>
            <a href="{{ route('advertise') }}" class="flex justify-center items-center gap-2 p-3 w-full bg-amber-600 text-white font-bold rounded-xl shadow-sm text-sm">📢 विज्ञापन दें</a>
            <a href="{{ route('login') }}" class="flex justify-center items-center gap-2 p-3 w-full bg-brand text-white font-bold rounded-xl shadow-sm text-sm">🔐 Login</a>
            <a href="{{ route('register') }}" class="flex justify-center items-center gap-2 p-3 w-full bg-green-600 text-white font-bold rounded-xl shadow-sm text-sm">📝 Register</a>
        @endauth
    </div>
</div>

<!-- ===== TOP BAR ===== -->
<div class="bg-slate-950 text-slate-300 text-xs py-2.5 hidden md:block border-b border-slate-800">
    <div class="container mx-auto px-6 flex justify-between items-center">
        <div class="flex items-center gap-6 font-semibold">
            <span class="bg-brand text-white font-black px-2.5 py-0.5 rounded text-[11px] tracking-wider animate-pulse">मुख्य समाचार</span>
            <span>📅 {{ date('d M Y, l') }}</span>
            <span class="flex items-center gap-1">📍 क्षेत्र: <span class="text-white font-bold">उत्तर प्रदेश</span></span>
            <span>🌤️ तापमान: <span class="text-amber-400 font-bold">29°C</span></span>
        </div>
        <div class="flex gap-5 font-bold items-center">
            <a href="#" class="hover:text-white transition flex items-center gap-1 text-brand">📱 हमारा mobile ऐप</a>
            <span class="text-slate-700">|</span>
            <a href="{{ route('advertise') }}" class="hover:text-white transition">🤝 विज्ञापन के लिए संपर्क करें</a>
            @auth
                @php
                    $dashboardUrl = (Auth::user()->role === 'admin' || Auth::user()->role === 'super_admin') ? route('admin.dashboard') : (Auth::user()->role === 'reporter' ? route('reporter.dashboard') : route('home'));
                @endphp
                <span class="text-slate-700">|</span>
                <span class="text-green-400 flex items-center gap-1.5">
                    👋 <a href="{{ $dashboardUrl }}" class="text-green-400 hover:text-white transition font-bold">{{ Auth::user()->name }}</a>
                    <span class="bg-brand text-white text-[9px] px-1.5 py-0.5 rounded font-black">{{ Auth::user()->role }}</span>
                </span>
                <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                    @csrf
                    <button type="submit" class="text-red-400 hover:text-red-300 transition cursor-pointer text-xs font-bold">🚪 Logout</button>
                </form>
            @else
                <span class="text-slate-700">|</span>
                <a href="{{ route('login') }}" class="hover:text-white transition flex items-center gap-1 text-brand">🔐 Login</a>
                <span class="text-slate-700">|</span>
                <a href="{{ route('register') }}" class="hover:text-white transition text-green-400">📝 Register</a>
            @endauth
        </div>
    </div>
</div>

<!-- ============================================================ -->
<!-- ✅ HEADER ADS                                                -->
<!-- ============================================================ -->
@php
    try {
        $headerAds = \App\Models\Ad::where('position', 'header')
            ->where('status', 'active')
            ->where('is_active', 1)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->orderBy('priority', 'desc')
            ->orderBy('id', 'desc')
            ->get();
    } catch (\Exception $e) {
        $headerAds = collect();
    }
@endphp

@if($headerAds->isNotEmpty())
    <div class="header-ad-wrapper">
        <div class="container mx-auto px-4">
            <div class="ad-container">
                <span class="ad-badge">— विज्ञापन —</span>
                @foreach($headerAds as $ad)
                    <div class="ad-item">
                        @if($ad->type == 'image' && $ad->image)
                            @php
                                $imageExists = file_exists(storage_path('app/public/' . $ad->image));
                            @endphp
                            @if($imageExists)
                                <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow">
                                    <img src="{{ asset('storage/' . $ad->image) }}" 
                                         alt="{{ $ad->title }}" 
                                         style="max-width: 100%; height: auto; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
                                </a>
                            @else
                                <div style="background: #fef2f2; padding: 10px; border: 2px dashed #c62828; border-radius: 8px; color: #c62828; font-weight: 600;">
                                    ⚠️ Image not found: {{ $ad->image }}
                                    <br><small style="font-size: 12px; color: #64748b;">Please upload image</small>
                                </div>
                            @endif
                        @elseif($ad->type == 'code')
                            {!! $ad->code !!}
                        @elseif($ad->type == 'text')
                            <a href="{{ route('ads.click', $ad->id) }}" target="_blank" rel="nofollow" style="color: #2563eb; text-decoration: underline; font-weight: 600; font-size: 16px; display: block; padding: 5px 0;">
                                {{ $ad->description ?? $ad->title }}
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

<!-- ===== HEADER ===== -->
<header class="bg-white shadow-md sticky top-0 z-50 border-b-4 border-brand">
    <div class="container mx-auto px-4 md:px-6 py-2 flex items-center justify-between gap-4 relative">
        <button class="p-2 -ml-2 rounded-xl text-slate-800 hover:bg-slate-100 md:hidden focus:outline-none transition z-10" onclick="openMobileMenu()">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <div class="absolute inset-x-0 mx-auto flex justify-center md:static md:inset-auto md:mx-0 md:justify-start pointer-events-none md:pointer-events-auto">
            <a href="/" class="flex items-center flex-shrink-0 group py-1 min-w-[160px] md:min-w-[200px] justify-center md:justify-start pointer-events-auto">
                <img src="{{ asset($logo) . '?v=' . $logoVersion }}" alt="{{ $siteName }}" class="h-11 md:h-16 w-auto object-contain" loading="lazy" onerror="this.style.display='none'; document.getElementById('desk-text-logo').style.display='flex';">
                <div id="desk-text-logo" style="display:none;" class="items-center gap-3">
                    <div class="bg-brand text-white px-4 py-2 rounded-none flex flex-col justify-center items-center shadow-lg">
                        <span class="text-2xl md:text-3xl font-black tracking-tighter leading-none">पब्लिक</span>
                        <div class="bg-white text-slate-950 px-2 py-0.5 text-[9px] md:text-[10px] font-black tracking-[0.25em] uppercase mt-1 w-full text-center">एक्सप्रेस</div>
                    </div>
                    <div class="hidden sm:block border-l-2 border-slate-300 pl-3">
                        <h1 class="text-lg font-black text-slate-950 leading-tight tracking-tight">{{ $siteName }}</h1>
                        <p class="text-[12px] font-bold tracking-wider text-brand uppercase">पब्लिक की आवाज</p>
                    </div>
                </div>
            </a>
        </div>

        <!-- Search Form (desktop only) -->
        <form action="{{ route('news.search') }}" method="GET" class="flex-1 max-w-md hidden md:block z-10">
            <div class="flex relative group">
                <input type="text" name="q" placeholder="खबरें, राजनीति या जिला खोजें..." class="w-full px-5 py-2.5 bg-slate-50 border-2 border-slate-200 rounded-xl focus:outline-none focus:border-brand focus:bg-white text-sm font-semibold transition-all pl-12">
                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">🔍</span>
                <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-slate-950 text-white px-4 py-1.5 rounded-lg text-xs font-bold hover:bg-brand transition shadow">खोजें</button>
            </div>
        </form>

        <!-- ===== BELL ICON (Top‑Right) ===== -->
        <div class="flex gap-3 flex-shrink-0 items-center z-10">
            <div class="notification-btn-wrapper">
                <button onclick="toggleNotification()" class="bg-slate-100 hover:bg-slate-200 p-2 rounded-full transition relative" aria-label="सूचनाएँ">
                    🔔
                    <span id="notificationBadge" style="display:none;">0</span>
                </button>
            </div>
            <a href="#opinionPollPopup" onclick="event.preventDefault(); showPollPopup()" class="bg-brand text-white px-3 py-2 rounded-lg text-xs font-black whitespace-nowrap hover:bg-red-700 transition" aria-label="Opinion Poll खोलें">
                📊 Poll 2027
            </a>
        </div>
    </div>

    <!-- Navigation (desktop) -->
    <nav class="bg-slate-900 hidden md:block shadow-inner border-t border-slate-800">
        <div class="container mx-auto px-6 py-0.5 flex items-center gap-1 text-sm font-black text-white whitespace-nowrap overflow-x-visible relative">
            <a href="/" class="px-5 py-3 bg-brand text-white text-xs font-black tracking-wider flex items-center gap-1.5 transition hover:bg-red-700"><i class="fas fa-home"></i> होम</a>
            @php
                $categories = \App\Models\Category::where('is_active', 1)->whereIn('slug', ['national', 'politics', 'education', 'sports', 'entertainment'])->orderBy('order')->get();
            @endphp
            @foreach($categories as $cat)
                <a href="{{ route('category.show', $cat->slug) }}" class="px-4 py-3 hover:bg-brand hover:text-white text-slate-200 text-xs tracking-wider transition font-bold">{{ $cat->icon ?? '📰' }} {{ $cat->display_name ?? $cat->name }}</a>
            @endforeach
            
            <!-- ===== LOCATION DROPDOWN ===== -->
            <div class="relative group">
                <button class="px-4 py-3 bg-slate-800 hover:bg-brand hover:text-white text-slate-200 text-xs tracking-wider transition font-bold flex items-center gap-1.5 focus:outline-none">🚩 उत्तर प्रदेश <i class="fas fa-chevron-down text-[10px] opacity-80"></i></button>
                <div class="absolute left-0 mt-0 w-52 bg-slate-900 border-t-2 border-brand shadow-xl opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                    @php $states = \App\Models\State::where('is_active',1)->with(['districts'=>function($q){$q->where('is_active',1);}])->orderBy('name')->get(); @endphp
                    @foreach($states as $state)
                        @if($state->districts->count() > 0)
                            <div class="relative group/district border-b border-slate-800/60">
                                <a href="{{ route('news.state', $state->slug) }}" class="w-full text-left px-4 py-3 hover:bg-brand transition-colors flex justify-between items-center text-xs text-slate-200 font-bold">📍 {{ $state->display_name ?? $state->name }} <i class="fas fa-chevron-right text-[9px] opacity-60"></i></a>
                                <div class="absolute left-full top-0 w-44 bg-slate-900 shadow-xl opacity-0 invisible group-hover/district:opacity-100 group-hover/district:visible transition-all duration-150 z-50 border-l border-slate-800">
                                    @foreach($state->districts as $district)
                                        <a href="{{ route('news.district', $district->slug) }}" class="block px-4 py-2.5 hover:bg-brand text-xs text-slate-300 font-semibold border-b border-slate-800/40">{{ $district->display_name ?? $district->name }}</a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            
            <span class="w-px h-5 bg-slate-800 mx-2"></span>
            <a href="{{ route('leaderboard') }}" class="px-4 py-3 hover:bg-amber-500 hover:text-slate-950 text-amber-400 text-xs tracking-wider transition font-bold flex items-center gap-1">🏆 लीडरबोर्ड</a>
            <a href="{{ route('advertise') }}" class="px-4 py-3 hover:bg-amber-500 hover:text-slate-950 text-amber-400 text-xs tracking-wider transition font-bold flex items-center gap-1">📢 विज्ञापन</a>
            <a href="#opinionPollPopup" onclick="event.preventDefault(); showPollPopup()" class="px-4 py-3 hover:bg-amber-500 hover:text-slate-950 text-amber-400 text-xs tracking-wider transition font-bold flex items-center gap-1">📊 Opinion Poll UP Vidhan Sabha 2027</a>
        </div>
    </nav>
</header>

<!-- ============================================================
     🔔 NOTIFICATION PERMISSION BANNER (Guest + Logged-in)
     ============================================================ -->
<div id="permissionBanner" class="permission-banner" role="alert" style="display: none;">
    <div class="banner-icon">🔔</div>
    <div class="banner-content">
        <div class="banner-title">रीयल-टाइम समाचार अलर्ट प्राप्त करें!</div>
        <div class="banner-subtitle">
            अपने 10 किमी दायरे में आने वाली खबरों के तुरंत अपडेट पाएं 
            <span class="status-badge" id="statusBadge">लोड हो रहा है...</span>
        </div>
    </div>
    <div class="banner-actions">
        <button class="btn-enable" onclick="enableNotifications()" aria-label="सक्रिय करें">✅ सक्रिय करें</button>
        <button class="btn-disable" onclick="disableNotifications()" aria-label="अभी नहीं">❌ अभी नहीं</button>
    </div>
</div>

<!-- ===== MAIN CONTENT ===== -->
<main id="main-content" class="container mx-auto px-4 md:px-6 py-6 max-w-7xl pb-24 md:pb-12">
    @yield('content')
</main>

<!-- ============================================================ -->
<!-- ✅ OPINION POLL POPUP (HTML)                                  -->
<!-- ============================================================ -->
<div id="opinionPollPopup" class="opinion-poll-overlay" style="display:none;">
    <div class="opinion-poll-container">
        <div class="poll-header">
            <button class="poll-close-btn" onclick="closePollPopup()">✕</button>
            <h2>उत्तर प्रदेश विधानसभा चुनाव 2027 Opinion Poll</h2>
            <p class="poll-subtitle">Opinion Poll का हिस्सा बनें और अपनी विधानसभा सीट के लिए अपनी राय दें</p>
            <div class="poll-leader-strip" aria-label="प्रमुख नेताओं की तस्वीरें">
                <div class="poll-leader-card party-sp"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Akhilesh_Yadav.jpg?width=180" alt="अखिलेश यादव" loading="lazy"><span>अखिलेश यादव · सपा</span></div>
                <div class="poll-leader-card party-bjp"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Yogi_Adityanath.jpg?width=180" alt="योगी आदित्यनाथ" loading="lazy"><span>योगी आदित्यनाथ · भाजपा</span></div>
                <div class="poll-leader-card party-bsp"><img src="https://commons.wikimedia.org/wiki/Special:FilePath/Mayawati.jpg?width=180" alt="मायावती" loading="lazy"><span>मायावती · बसपा</span></div>
                <div class="poll-party-flags" aria-label="प्रमुख राजनीतिक दलों के झंडे">
                    <span class="poll-party-flag flag-sp">सपा</span>
                    <span class="poll-party-flag flag-bjp">भाजपा</span>
                    <span class="poll-party-flag flag-bsp">बसपा</span>
                    <span class="poll-party-flag flag-inc">कांग्रेस</span>
                    <span class="poll-party-flag flag-rld">रालोद</span>
                    <span class="poll-party-flag flag-sbsp">सुभासपा</span>
                    <span class="poll-party-flag flag-apna">अपना दल</span>
                    <span class="poll-party-flag flag-nishad">निषाद</span>
                </div>
            </div>
        </div>
        <div class="poll-body">
            <!-- Step 1: Select Seat -->
            <div id="pollStep1" class="poll-step">
                <div class="poll-step-header">
                    <span class="step-number">1</span>
                    <h3>पहले अपना जिला चुनें</h3>
                </div>
                <div class="seat-search-box">
                    <select id="pollDistrictSelect" class="poll-select" onchange="loadSeatsForDistrict(this.value)">
                        <option value="">जिला चुनें</option>
                    </select>
                </div>
                <div class="seat-search-box">
                    <select id="pollSeatSelect" class="poll-select" onchange="selectSeatFromDropdown(this.value)" disabled>
                        <option value="">पहले जिला चुनें</option>
                    </select>
                </div>
                <button class="btn-next-step" onclick="goToStep2()" id="btnStep1Next" disabled>विधानसभा चुनकर आगे बढ़ें →</button>
            </div>

            <!-- Step 2: Questions -->
            <div id="pollStep2" class="poll-step" style="display:none;">
                <div class="poll-step-header">
                    <span class="step-number">2</span>
                    <h3>अपनी राय दें</h3>
                    <p class="selected-seat-info" id="selectedSeatInfo"></p>
                </div>
                <div class="poll-respondent-details">
                    <label class="poll-field-label" for="pollRespondentName">नाम</label>
                    <input id="pollRespondentName" class="poll-select" type="text" maxlength="100" placeholder="अपना नाम लिखें" autocomplete="name" required>
                    <label class="poll-field-label" for="pollRespondentMobile">मोबाइल नंबर</label>
                    <input id="pollRespondentMobile" class="poll-select" type="tel" inputmode="numeric" maxlength="10" pattern="[0-9]{10}" placeholder="10 अंकों का मोबाइल नंबर" autocomplete="tel" required>
                    <p class="poll-field-note">एक मोबाइल नंबर से इस Opinion Poll में केवल एक बार राय दर्ज की जा सकती है।</p>
                </div>
                <div id="questionsContainer" class="questions-container">
                    <!-- Questions will be loaded via AJAX -->
                </div>
                <div class="poll-actions">
                    <button class="btn-prev-step" onclick="goToStep1()">← पीछे</button>
                    <button class="btn-submit-poll" id="btnSubmitPoll" onclick="submitPoll()" disabled>📨 भेजें</button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== FOOTER ===== -->
<footer class="footer-newsletter pt-5 pb-24 md:pb-8" role="contentinfo">
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
            <div class="lg:col-span-2">
                <h4 class="text-white font-black text-lg mb-2"><i class="far fa-envelope-open text-brand mr-2"></i> न्यूज़लेटर सब्सक्राइब करें</h4>
                <p class="text-muted text-sm mb-4">ताजा खबरों और बड़ी ख़बरों के बुलेटिन सीधे अपने ईमेल पर पाने के लिए सब्सक्राइब करें।</p>
                @if(session('newsletter_success'))
                    <div class="alert alert-success py-2 px-3 mb-3 rounded" style="background:rgba(40,167,69,0.15);border:1px solid rgba(40,167,69,0.3);color:#d4edda;font-size:14px;">{{ session('newsletter_success') }}<button type="button" class="float-right bg-transparent border-0 text-white opacity-75" data-bs-dismiss="alert">&times;</button></div>
                @endif
                @if(session('newsletter_error'))
                    <div class="alert alert-danger py-2 px-3 mb-3 rounded" style="background:rgba(220,53,69,0.15);border:1px solid rgba(220,53,69,0.3);color:#f8d7da;font-size:14px;">{{ session('newsletter_error') }}<button type="button" class="float-right bg-transparent border-0 text-white opacity-75" data-bs-dismiss="alert">&times;</button></div>
                @endif
                <form action="{{ route('newsletter.subscribe') }}" method="POST" class="row g-2">
                    @csrf
                    <div class="col-sm-4"><input type="text" name="name" class="form-control" placeholder="आपका नाम (वैकल्पिक)"></div>
                    <div class="col-sm-5"><input type="email" name="email" class="form-control" placeholder="ईमेल *" required></div>
                    <div class="col-sm-3"><button type="submit" class="btn-subscribe"><i class="fas fa-paper-plane me-1"></i> सब्सक्राइब</button></div>
                </form>
                <p class="text-muted mt-2" style="font-size:12px;">हम आपकी ईमेल जानकारी को सुरक्षित रखेंगे।</p>
            </div>
            <div>
                <h5 class="text-white font-black text-sm tracking-wider mb-3 border-b border-slate-800 pb-1">Quick Links</h5>
                <ul class="list-unstyled text-sm space-y-2 font-semibold">
                    <li><a href="{{ route('about') }}"><i class="fas fa-chevron-right text-brand mr-1" style="font-size:10px;"></i> About Us</a></li>
                    <li><a href="{{ route('contact') }}"><i class="fas fa-chevron-right text-brand mr-1" style="font-size:10px;"></i> Contact Us</a></li>
                    <li><a href="{{ route('privacy') }}"><i class="fas fa-chevron-right text-brand mr-1" style="font-size:10px;"></i> Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}"><i class="fas fa-chevron-right text-brand mr-1" style="font-size:10px;"></i> Terms of Service</a></li>
                    <li><a href="{{ route('advertise') }}" class="text-warning hover:text-white transition"><i class="fas fa-ad mr-1"></i> Advertise</a></li>
                </ul>
            </div>
            <div>
                <h5 class="text-white font-black text-sm tracking-wider mb-3 border-b border-slate-800 pb-1">सोशल मीडिया</h5>
                <div class="flex gap-3 mb-4">
                    <a href="{{ $facebook }}" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="{{ $twitter }}" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="{{ $instagram }}" class="social-icon"><i class="fab fa-instagram"></i></a>
                    <a href="{{ $youtube }}" class="social-icon"><i class="fab fa-youtube"></i></a>
                </div>
                <p class="text-muted text-sm leading-relaxed font-semibold">
                    <i class="fas fa-envelope text-brand mr-2"></i> {{ $contactEmail }}<br>
                    <i class="fas fa-phone text-brand mr-2"></i> {{ $contactPhone }}
                </p>
            </div>
        </div>
        <hr>
        <div class="text-center text-muted text-sm font-bold tracking-wider pt-4">
            &copy; {{ date('Y') }} <strong class="text-white">{{ $siteName }}</strong>. {{ $footerText }} <span class="block md:inline-block mt-1 md:mt-0">हर कस्बे, गाँव और सिटी की खबरें</span>
        </div>
    </div>
</footer>

<!-- ============================================================
     🔥 FIREBASE PUSH NOTIFICATIONS (Guest + Logged-in Users)
     ============================================================ -->
<script src="https://www.gstatic.com/firebasejs/12.16.0/firebase-app-compat.js"></script>
<script src="https://www.gstatic.com/firebasejs/12.16.0/firebase-messaging-compat.js"></script>

<script>
    // ============================================================
    // ✅ FIREBASE CONFIGURATION
    // ============================================================
    const firebaseConfig = {
        apiKey: "AIzaSyAnKz3f7FCITWYU8zXIYXaBO2Y82i9BK9Q",
        authDomain: "the-public-express-bb2f9.firebaseapp.com",
        projectId: "the-public-express-bb2f9",
        storageBucket: "the-public-express-bb2f9.firebasestorage.app",
        messagingSenderId: "379587060266",
        appId: "1:379587060266:web:d2a0107fc1a39e7b5e9953",
        measurementId: "G-DCV84SRRDV"
    };

    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    // ============================================================
    // ✅ SERVICE WORKER REGISTRATION
    // ============================================================
    
    let swRegistration = null;

    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then(function(registration) {
                console.log('✅ Service Worker registered successfully');
                swRegistration = registration;
                if (registration.active) {
                    console.log('✅ Service Worker is active');
                }
            })
            .catch(function(error) {
                console.error('❌ Service Worker registration failed:', error);
            });
    }

    // ============================================================
    // ✅ MOBILE DETECTION
    // ============================================================
    
    const isMobile = /Android|iPhone|iPad|iPod|BlackBerry|Opera Mini|IEMobile|WPDesktop/i.test(navigator.userAgent);
    
    if (isMobile) {
        console.log('📱 Mobile device detected');
        setTimeout(function() {
            const banner = document.getElementById('permissionBanner');
            const dismissed = localStorage.getItem('notifications_dismissed');
            if (banner && !dismissed && Notification.permission === 'default') {
                banner.classList.add('show');
                banner.style.display = 'flex';
            }
        }, 2000);
    }

    // ============================================================
    // ✅ FCM TOKEN FUNCTIONS
    // ============================================================

    async function getTokenWithRetry(maxRetries = 3) {
        for (let attempt = 1; attempt <= maxRetries; attempt++) {
            try {
                if (!swRegistration) {
                    swRegistration = await navigator.serviceWorker.ready;
                }
                const token = await messaging.getToken({
                    vapidKey: '{{ $vapidPublicKey }}',
                    serviceWorkerRegistration: swRegistration
                });
                if (token) return token;
            } catch (error) {
                console.warn(`⚠️ Token attempt ${attempt} failed:`, error);
                if (attempt < maxRetries) {
                    await new Promise(resolve => setTimeout(resolve, 1000 * attempt));
                }
            }
        }
        return null;
    }

    async function requestFCMToken() {
        try {
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                if (!swRegistration) {
                    swRegistration = await navigator.serviceWorker.ready;
                }
                const token = await getTokenWithRetry();
                if (token) {
                    console.log('✅ FCM Token:', token);
                    await saveFCMToken(token);
                    updateUI('active');
                    return token;
                }
            } else {
                updateUI('denied');
            }
        } catch (error) {
            console.error('❌ Error getting FCM token:', error);
            updateUI('error');
        }
        return null;
    }

    async function saveFCMToken(token) {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        try {
            const response = await fetch('/api/fcm/token', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ fcm_token: token })
            });
            const data = await response.json();
            if (data.success) {
                console.log('✅ FCM token saved successfully');
            }
            return data;
        } catch (error) {
            console.error('❌ Error saving token:', error);
            return null;
        }
    }

    async function removeFCMToken(token) {
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        try {
            const response = await fetch('/api/fcm/token', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify({ fcm_token: token })
            });
            const data = await response.json();
            if (data.success) {
                console.log('✅ FCM token deactivated');
            }
            return data;
        } catch (error) {
            console.error('❌ Error removing token:', error);
            return null;
        }
    }

    // ============================================================
    // ✅ UI UPDATES
    // ============================================================

    function updateUI(status) {
        const badge = document.getElementById('statusBadge');
        const banner = document.getElementById('permissionBanner');
        if (!badge) return;

        if (status === 'active') {
            badge.textContent = '✅ सक्रिय';
            badge.style.background = 'rgba(40,167,69,0.3)';
            if (banner) {
                banner.classList.remove('show');
                banner.style.display = 'none';
            }
            localStorage.setItem('notifications_dismissed', 'false');
        } else if (status === 'denied') {
            badge.textContent = '❌ अस्वीकृत';
            badge.style.background = 'rgba(220,53,69,0.3)';
            if (banner) {
                banner.classList.remove('show');
                banner.style.display = 'none';
            }
        } else if (status === 'error') {
            badge.textContent = '⚠️ त्रुटि';
            badge.style.background = 'rgba(255,193,7,0.3)';
        } else {
            badge.textContent = 'अधिसूचना सक्षम करें';
            badge.style.background = 'rgba(255,255,255,0.2)';
        }
    }

    function checkPermissionBanner() {
        const dismissed = localStorage.getItem('notifications_dismissed');
        const permission = Notification.permission;
        const banner = document.getElementById('permissionBanner');

        if (permission === 'granted') {
            updateUI('active');
            getTokenWithRetry().then(token => { 
                if (token) {
                    console.log('✅ Existing FCM token found');
                    saveFCMToken(token);
                }
            }).catch(() => {});
        } else if (permission === 'denied') {
            updateUI('denied');
        } else {
            if (!dismissed || dismissed === 'false') {
                if (banner) {
                    banner.classList.add('show');
                    banner.style.display = 'flex';
                }
                updateUI('default');
            }
        }
    }

    // ============================================================
    // ✅ ENABLE / DISABLE NOTIFICATIONS
    // ============================================================

    window.enableNotifications = async function() {
        await requestFCMToken();
    };

    window.disableNotifications = function() {
        const banner = document.getElementById('permissionBanner');
        if (banner) {
            banner.classList.remove('show');
            banner.style.display = 'none';
        }
        localStorage.setItem('notifications_dismissed', 'true');
        getTokenWithRetry().then(token => { 
            if (token) {
                removeFCMToken(token);
            }
        }).catch(() => {});
        console.log('ℹ️ Notifications disabled by user');
    };

    window.toggleNotification = function() {
        const permission = Notification.permission;
        if (permission === 'granted') {
            window.location.href = '/notifications';
        } else if (permission === 'denied') {
            if (isMobile) {
                alert('⚠️ सूचनाएँ अवरुद्ध हैं।\nकृपया ब्राउज़र सेटिंग्स → साइट सेटिंग्स → सूचनाएँ → अनुमति दें पर जाएं।');
            } else {
                alert('⚠️ सूचनाएँ अवरुद्ध हैं। कृपया ब्राउज़र सेटिंग्स से सक्षम करें।');
            }
        } else {
            const banner = document.getElementById('permissionBanner');
            if (banner) {
                banner.classList.add('show');
                banner.style.display = 'flex';
                localStorage.setItem('notifications_dismissed', 'false');
            }
        }
    };

    // ============================================================
    // ✅ FOREGROUND MESSAGE HANDLING
    // ============================================================

    messaging.onMessage(function(payload) {
        console.log('📨 Foreground message received:', payload);
        if (payload.notification) {
            const notification = new Notification(
                payload.notification.title || 'द पब्लिक एक्सप्रेस',
                {
                    body: payload.notification.body || 'ताजा खबर!',
                    icon: '/images/logo.png',
                    badge: '/images/badge.png',
                    data: payload.data || {},
                    requireInteraction: true,
                    vibrate: [200, 100, 200]
                }
            );
            notification.onclick = function() {
                const url = payload.data?.click_action || '/';
                window.open(url, '_blank');
                notification.close();
            };
        }
    });

    // ============================================================
    // ✅ MOBILE MENU FUNCTIONS
    // ============================================================

    window.openMobileMenu = function() {
        document.getElementById('mobileMenuPanel').classList.add('open');
        const overlay = document.getElementById('mobileOverlay');
        overlay.style.display = 'block';
        setTimeout(() => overlay.classList.add('active'), 50);
        document.body.style.overflow = 'hidden';
    };

    window.closeMobileMenu = function() {
        document.getElementById('mobileMenuPanel').classList.remove('open');
        const overlay = document.getElementById('mobileOverlay');
        overlay.classList.remove('active');
        setTimeout(() => overlay.style.display = 'none', 300);
        document.body.style.overflow = '';
    };

    // ============================================================
    // ✅ OPINION POLL JAVASCRIPT (FIXED AUTO-TRIGGER)
    // ============================================================

    let selectedSeatId = null;
    let pollQuestions = [];
    let selectedAnswers = {};

    // Show Poll Popup
    function showPollPopup() {
        const popup = document.getElementById('opinionPollPopup');
        if (!popup) return;
        popup.style.display = 'flex';
        popup.classList.add('show');
        document.body.style.overflow = 'hidden';
        loadSeats();
    }

    // Close Poll Popup
    function closePollPopup() {
        const popup = document.getElementById('opinionPollPopup');
        if (!popup) return;
        popup.classList.remove('show');
        popup.style.display = 'none';
        document.body.style.overflow = '';
    }

    // Load districts first, then load only the assembly seats in that district.
    async function loadSeats() {
        const districtSelect = document.getElementById('pollDistrictSelect');
        if (!districtSelect) return;
        districtSelect.innerHTML = '<option value="">⏳ जिले लोड हो रहे हैं...</option>';

        try {
            const response = await fetch('/poll/districts');
            const data = await response.json();
            if (data.success) {
                districtSelect.innerHTML = '<option value="">जिला चुनें</option>';
                data.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district;
                    option.textContent = district;
                    districtSelect.appendChild(option);
                });
            } else {
                districtSelect.innerHTML = '<option value="">❌ जिले लोड नहीं हुए</option>';
            }
        } catch (error) {
            districtSelect.innerHTML = '<option value="">❌ जिले लोड नहीं हुए</option>';
            console.error('Error loading districts:', error);
        }
    }

    async function loadSeatsForDistrict(district) {
        const seatSelect = document.getElementById('pollSeatSelect');
        const nextButton = document.getElementById('btnStep1Next');
        selectedSeatId = null;
        nextButton.disabled = true;
        seatSelect.disabled = true;
        seatSelect.innerHTML = '<option value="">⏳ विधानसभा लोड हो रही हैं...</option>';
        if (!district) {
            seatSelect.innerHTML = '<option value="">पहले जिला चुनें</option>';
            return;
        }

        try {
            const response = await fetch(`/poll/seats/${encodeURIComponent(district)}`);
            const data = await response.json();
            seatSelect.innerHTML = '<option value="">विधानसभा चुनें</option>';
            data.seats.forEach(seat => {
                const option = document.createElement('option');
                option.value = seat.id;
                option.textContent = `${seat.seat_number} - ${seat.seat_name}`;
                seatSelect.appendChild(option);
            });
            seatSelect.disabled = data.seats.length === 0;
            if (!data.seats.length) seatSelect.innerHTML = '<option value="">इस जिले में विधानसभा उपलब्ध नहीं</option>';
        } catch (error) {
            seatSelect.innerHTML = '<option value="">❌ विधानसभा लोड नहीं हुई</option>';
            console.error('Error loading assemblies:', error);
        }
    }

    function selectSeatFromDropdown(seatId) {
        selectedSeatId = seatId || null;
        document.getElementById('btnStep1Next').disabled = !selectedSeatId;
    }

    // Go to Step 2
    async function goToStep2() {
        if (!selectedSeatId) return;

        const seatName = document.getElementById('pollSeatSelect')?.selectedOptions[0]?.textContent || '';
        const infoEl = document.getElementById('selectedSeatInfo');
        if (infoEl) infoEl.textContent = `📍 ${seatName}`;

        await loadQuestions(selectedSeatId);

        document.getElementById('pollStep1').style.display = 'none';
        document.getElementById('pollStep2').style.display = 'block';
        document.getElementById('pollStep2').scrollIntoView({ behavior: 'smooth' });
    }

    // Go to Step 1
    function goToStep1() {
        document.getElementById('pollStep2').style.display = 'none';
        document.getElementById('pollStep1').style.display = 'block';
    }

    // Load Questions
    async function loadQuestions(seatId) {
        const container = document.getElementById('questionsContainer');
        if (!container) return;
        container.innerHTML = '<div class="text-center py-4">⏳ प्रश्न लोड हो रहे हैं...</div>';

        try {
            const response = await fetch(`/poll/questions/${seatId}`);
            const data = await response.json();
            if (data.success) {
                pollQuestions = data.questions;
                renderQuestions(data.questions);
            } else {
                container.innerHTML = '<div class="text-center py-4 text-red-500">❌ प्रश्न लोड करने में त्रुटि</div>';
            }
        } catch (error) {
            container.innerHTML = '<div class="text-center py-4 text-red-500">❌ प्रश्न लोड करने में त्रुटि</div>';
            console.error('Error loading questions:', error);
        }
    }

    // Render Questions
    function renderQuestions(questions) {
        const container = document.getElementById('questionsContainer');
        if (!container) return;
        container.innerHTML = '';

        questions.forEach((q, index) => {
            const div = document.createElement('div');
            div.className = 'question-item';
            div.dataset.qid = q.id;

            div.innerHTML = `
                <div class="question-text">${index + 1}. ${q.question}</div>
                <div class="options-group" id="options-${q.id}">
                    ${q.options.map(opt => `
                        <label class="option-label">
                            <input type="radio" name="question_${q.id}" value="${opt.value}" onchange="selectOption(${q.id}, '${opt.value}', this)">
                            ${opt.label}
                        </label>
                    `).join('')}
                </div>
            `;

            container.appendChild(div);
        });
    }

    // Select Option
    function selectOption(questionId, value, inputElement) {
        const parent = inputElement.closest('.question-item');
        parent.querySelectorAll('.option-label').forEach(el => el.classList.remove('selected'));
        inputElement.closest('.option-label').classList.add('selected');

        selectedAnswers[questionId] = value;
        checkAllAnswered();
    }

    // Check if all questions answered
    function checkAllAnswered() {
        const totalQuestions = pollQuestions.length;
        const answered = Object.keys(selectedAnswers).length;
        const btn = document.getElementById('btnSubmitPoll');
        if (btn) btn.disabled = (answered < totalQuestions);
    }

    // Submit Poll
    async function submitPoll() {
        const btn = document.getElementById('btnSubmitPoll');
        if (!btn) return;
        btn.disabled = true;
        btn.textContent = '⏳ भेजा जा रहा है...';

        const respondentName = document.getElementById('pollRespondentName')?.value.trim() || '';
        const respondentMobile = document.getElementById('pollRespondentMobile')?.value.trim() || '';
        if (respondentName.length < 2 || !/^\d{10}$/.test(respondentMobile)) {
            alert('कृपया अपना नाम और 10 अंकों का सही मोबाइल नंबर भरें।');
            btn.disabled = false;
            btn.textContent = '📨 भेजें';
            return;
        }

        const data = {
            seat_id: selectedSeatId,
            respondent_name: respondentName,
            respondent_mobile: respondentMobile,
            answers: selectedAnswers,
            poll_id: pollQuestions[0]?.poll_id || 1
        };

        try {
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const response = await fetch('/poll/submit', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.success) {
                localStorage.setItem('poll_submitted_2027', 'true');
                showSuccessMessage();
            } else {
                alert(result.message || '❌ सबमिट करने में त्रुटि');
                btn.disabled = false;
                btn.textContent = '📨 भेजें';
            }
        } catch (error) {
            alert('❌ सर्वर से कनेक्ट करने में त्रुटि');
            btn.disabled = false;
            btn.textContent = '📨 भेजें';
            console.error('Submit error:', error);
        }
    }

    // Show Success Message
    function showSuccessMessage() {
        const body = document.querySelector('.poll-body');
        if (!body) return;
        body.innerHTML = `
            <div class="poll-success">
                <span class="success-icon">✅</span>
                <h3>धन्यवाद!</h3>
                <p>आपकी राय सफलतापूर्वक दर्ज कर ली गई है।</p>
                <p style="margin-top:10px;font-size:13px;color:#94a3b8;">
                    आपका वोट उत्तर प्रदेश विधानसभा चुनाव 2027 के Opinion Poll में जोड़ दिया गया है।
                </p>
                <button onclick="closePollPopup()" style="margin-top:20px;padding:10px 30px;background:#1a56db;color:white;border:none;border-radius:10px;font-weight:600;cursor:pointer;">
                    ठीक है
                </button>
            </div>
        `;
    }

    // ============================================================
    // ✅ AUTO-TRIGGER (FIXED – NOW RUNS IMMEDIATELY)
    // ============================================================

    (function() {
        // Show on every public page load; the close button dismisses it for the current page.
        setTimeout(function() {
            pollShown = true;
            showPollPopup();
        }, 700);

        // Show on scroll – after 1500px
        let pollShown = false;
        window.addEventListener('scroll', function() {
            if (!pollShown && window.scrollY > 1500) {
                pollShown = true;
                showPollPopup();
            }
        });

        // Show on exit intent
        document.addEventListener('mouseleave', function(e) {
            if (!pollShown && e.clientY < 0) {
                pollShown = true;
                showPollPopup();
            }
        });
    })();

</script>

@stack('scripts')
@yield('scripts')

</body>
</html>