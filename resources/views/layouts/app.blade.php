<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VirtuGym - @yield('title', 'Dashboard')</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        (function(){
            const savedTheme = localStorage.getItem('virtugym-theme') || 'aurora';
            document.documentElement.dataset.theme = savedTheme;
        })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/dashboard-v2.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <script src="/js/virtugym-icons.js" defer></script>

    <style>
        :root {
            --vg-bg: #08081a;
            --vg-nav: rgba(8,8,26,.9);
            --vg-panel: rgba(255,255,255,.04);
            --vg-panel-strong: rgba(16,16,40,.97);
            --vg-sidebar: rgba(255,255,255,.03);
            --vg-border: rgba(139,92,246,.2);
            --vg-border-strong: rgba(139,92,246,.3);
            --vg-accent: #8b5cf6;
            --vg-accent-2: #ec4899;
            --vg-accent-soft: rgba(139,92,246,.15);
            --vg-accent-glow: rgba(139,92,246,.45);
            --vg-text: #fff;
            --vg-text-strong: #e2d9f3;
            --vg-text-muted: rgba(255,255,255,.42);
            --vg-text-faint: rgba(255,255,255,.25);
            --vg-gradient: linear-gradient(135deg, var(--vg-accent), var(--vg-accent-2));
            --vg-title-gradient: linear-gradient(135deg, #fff 20%, #c4b5fd 60%, #f9a8d4 90%);
            --vg-orb-1: rgba(139,92,246,.1);
            --vg-orb-2: rgba(236,72,153,.08);
            --vg-orb-3: rgba(59,130,246,.06);
        }
        html[data-theme="ember"] {
            --vg-bg: #160b08;
            --vg-nav: rgba(22,11,8,.92);
            --vg-panel: rgba(255,244,232,.055);
            --vg-panel-strong: rgba(38,18,12,.97);
            --vg-sidebar: rgba(255,236,214,.04);
            --vg-border: rgba(251,146,60,.22);
            --vg-border-strong: rgba(244,114,182,.34);
            --vg-accent: #f97316;
            --vg-accent-2: #e11d48;
            --vg-accent-soft: rgba(249,115,22,.16);
            --vg-accent-glow: rgba(249,115,22,.4);
            --vg-text-strong: #ffe4d2;
            --vg-text-muted: rgba(255,239,225,.46);
            --vg-text-faint: rgba(255,239,225,.26);
            --vg-title-gradient: linear-gradient(135deg, #fff7ed 20%, #fdba74 60%, #fb7185 90%);
            --vg-orb-1: rgba(249,115,22,.12);
            --vg-orb-2: rgba(225,29,72,.1);
            --vg-orb-3: rgba(251,191,36,.08);
        }
        html[data-theme="ocean"] {
            --vg-bg: #06131f;
            --vg-nav: rgba(6,19,31,.92);
            --vg-panel: rgba(236,253,245,.045);
            --vg-panel-strong: rgba(8,32,48,.97);
            --vg-sidebar: rgba(224,242,254,.035);
            --vg-border: rgba(14,165,233,.22);
            --vg-border-strong: rgba(45,212,191,.32);
            --vg-accent: #0ea5e9;
            --vg-accent-2: #14b8a6;
            --vg-accent-soft: rgba(14,165,233,.15);
            --vg-accent-glow: rgba(14,165,233,.36);
            --vg-text-strong: #d7f7ff;
            --vg-text-muted: rgba(224,242,254,.46);
            --vg-text-faint: rgba(224,242,254,.25);
            --vg-title-gradient: linear-gradient(135deg, #f0fdfa 20%, #7dd3fc 58%, #5eead4 90%);
            --vg-orb-1: rgba(14,165,233,.12);
            --vg-orb-2: rgba(20,184,166,.1);
            --vg-orb-3: rgba(59,130,246,.08);
        }
        html[data-theme="forest"] {
            --vg-bg: #07130d;
            --vg-nav: rgba(7,19,13,.92);
            --vg-panel: rgba(240,253,244,.045);
            --vg-panel-strong: rgba(10,32,22,.97);
            --vg-sidebar: rgba(220,252,231,.035);
            --vg-border: rgba(34,197,94,.22);
            --vg-border-strong: rgba(132,204,22,.32);
            --vg-accent: #22c55e;
            --vg-accent-2: #84cc16;
            --vg-accent-soft: rgba(34,197,94,.15);
            --vg-accent-glow: rgba(34,197,94,.34);
            --vg-text-strong: #dcfce7;
            --vg-text-muted: rgba(220,252,231,.46);
            --vg-text-faint: rgba(220,252,231,.25);
            --vg-title-gradient: linear-gradient(135deg, #f7fee7 20%, #86efac 58%, #bef264 90%);
            --vg-orb-1: rgba(34,197,94,.12);
            --vg-orb-2: rgba(132,204,22,.1);
            --vg-orb-3: rgba(20,184,166,.07);
        }
        html[data-theme="graphite"] {
            --vg-bg: #0c0f14;
            --vg-nav: rgba(12,15,20,.92);
            --vg-panel: rgba(248,250,252,.045);
            --vg-panel-strong: rgba(23,28,36,.97);
            --vg-sidebar: rgba(248,250,252,.035);
            --vg-border: rgba(148,163,184,.22);
            --vg-border-strong: rgba(203,213,225,.3);
            --vg-accent: #94a3b8;
            --vg-accent-2: #38bdf8;
            --vg-accent-soft: rgba(148,163,184,.15);
            --vg-accent-glow: rgba(56,189,248,.28);
            --vg-text-strong: #f1f5f9;
            --vg-text-muted: rgba(226,232,240,.45);
            --vg-text-faint: rgba(226,232,240,.25);
            --vg-title-gradient: linear-gradient(135deg, #ffffff 20%, #cbd5e1 58%, #7dd3fc 90%);
            --vg-orb-1: rgba(148,163,184,.1);
            --vg-orb-2: rgba(56,189,248,.08);
            --vg-orb-3: rgba(99,102,241,.06);
        }
        *{font-family:'Inter',sans-serif;box-sizing:border-box;}

        body {
            background: var(--vg-bg);
            min-height: 100vh;
            color: var(--vg-text);
            overflow-x: hidden;
            transition: background .35s ease, color .35s ease;
        }

        /* Background layers */
        #stars{position:fixed;inset:0;z-index:0;pointer-events:none;}
        .orb{position:fixed;border-radius:50%;filter:blur(90px);pointer-events:none;z-index:0;}
        .o1{width:500px;height:500px;background:var(--vg-orb-1);top:-200px;left:-150px;animation:od 22s ease-in-out infinite;}
        .o2{width:400px;height:400px;background:var(--vg-orb-2);bottom:-100px;right:-100px;animation:od 28s ease-in-out infinite reverse;}
        .o3{width:250px;height:250px;background:var(--vg-orb-3);top:50%;right:15%;animation:od 18s ease-in-out infinite 4s;}
        @keyframes od{0%,100%{transform:translate(0,0);}33%{transform:translate(30px,-40px);}66%{transform:translate(-20px,25px);}}

        /* Navbar */
        .nav-dark {
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 50;
            background: var(--vg-nav);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--vg-border);
        }
        .nav-inner {
            max-width: 1600px;
            margin: 0 auto;
            padding: 0 1.25rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .logo-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        .logo-badge {
            width: 38px;
            height: 38px;
            border-radius: 11px;
            background: var(--vg-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 16px var(--vg-accent-glow);
            flex-shrink: 0;
        }
        .brand-name {
            font-size: .95rem;
            font-weight: 800;
            background: var(--vg-title-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            letter-spacing: .05em;
        }
        .brand-sub {
            font-size: .58rem;
            color: var(--vg-text-faint);
            letter-spacing: .12em;
        }

        /* User avatar */
        .user-avatar {
            width: 38px;
            height: 38px;
            background: var(--vg-gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 800;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            box-shadow: 0 4px 12px var(--vg-accent-glow);
        }
        .user-avatar:hover {
            transform: scale(1.07);
            box-shadow: 0 6px 20px var(--vg-accent-glow);
        }

        /* Dropdown */
        .dropdown-menu {
            background: var(--vg-panel-strong);
            backdrop-filter: blur(20px);
            border-radius: 16px;
            box-shadow: 0 20px 40px rgba(0,0,0,.5), 0 0 0 1px var(--vg-border);
            min-width: 200px;
        }
        .dropdown-menu a, .dropdown-menu button {
            transition: all 0.2s ease;
            display: block;
            width: 100%;
        }
        .dropdown-menu a:hover, .dropdown-menu button:not(.theme-choice):hover {
            background: var(--vg-accent-soft);
            color: var(--vg-text-strong);
            padding-left: 20px;
        }
        .theme-grid {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 7px;
            padding: 10px 12px 12px;
            border-bottom: 1px solid var(--vg-border);
            margin-bottom: 4px;
        }
        .theme-choice {
            width: 28px;
            height: 28px;
            border: 1px solid var(--vg-border);
            border-radius: 50%;
            cursor: pointer;
            box-shadow: inset 0 0 0 3px rgba(255,255,255,.08);
            transition: transform .2s ease, border-color .2s ease, box-shadow .2s ease;
        }
        .theme-choice:hover,
        .theme-choice.active {
            transform: translateY(-2px);
            border-color: var(--vg-border-strong);
            box-shadow: 0 8px 20px var(--vg-accent-glow), inset 0 0 0 3px rgba(255,255,255,.12);
        }
        .theme-choice[data-theme-choice="aurora"] { background: linear-gradient(135deg,#8b5cf6,#ec4899); }
        .theme-choice[data-theme-choice="ember"] { background: linear-gradient(135deg,#f97316,#e11d48); }
        .theme-choice[data-theme-choice="ocean"] { background: linear-gradient(135deg,#0ea5e9,#14b8a6); }
        .theme-choice[data-theme-choice="forest"] { background: linear-gradient(135deg,#22c55e,#84cc16); }
        .theme-choice[data-theme-choice="graphite"] { background: linear-gradient(135deg,#94a3b8,#38bdf8); }
        .appearance-panel {
            background: linear-gradient(135deg, var(--vg-accent-soft), rgba(255,255,255,.025));
            border: 1px solid var(--vg-border);
            border-radius: 16px;
            padding: 14px;
        }
        .appearance-title {
            color: var(--vg-text-strong);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .06em;
            margin-bottom: 10px;
        }
        .theme-list {
            display: grid;
            grid-template-columns: 1fr;
            gap: 7px;
        }
        .theme-row {
            display: flex;
            align-items: center;
            gap: 9px;
            width: 100%;
            background: rgba(255,255,255,.04);
            border: 1px solid transparent;
            color: var(--vg-text-muted);
            border-radius: 10px;
            padding: 7px 9px;
            cursor: pointer;
            font-size: .78rem;
            font-weight: 700;
            transition: all .2s ease;
        }
        .theme-row:hover,
        .theme-row.active {
            background: var(--vg-accent-soft);
            border-color: var(--vg-border-strong);
            color: var(--vg-text-strong);
        }
        .theme-dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .vg-inline-icon {
            width: 1em;
            height: 1em;
            display: inline-block;
            vertical-align: -0.14em;
            margin-right: .35em;
            stroke-width: 2.4;
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 64px;
            height: calc(100vh - 64px);
            width: 240px;
            background: var(--vg-sidebar);
            backdrop-filter: blur(20px);
            border-right: 1px solid var(--vg-border);
            overflow-y: auto;
            z-index: 40;
        }
        .sidebar::-webkit-scrollbar{width:4px;}
        .sidebar::-webkit-scrollbar-thumb{background:var(--vg-gradient);border-radius:4px;}

        .sidebar-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 8px 12px;
            margin: 1px 8px;
            border-radius: 8px;
            color: var(--vg-text-muted);
            font-size: 12px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.2s ease;
            position: relative;
            margin-bottom: 2px;
        }
        .sidebar-item:hover {
            background: var(--vg-accent-soft);
            color: var(--vg-text-strong);
            transform: translateX(4px);
        }
        .sidebar-item.active {
            background: linear-gradient(135deg, var(--vg-accent-soft), rgba(255,255,255,.04));
            color: var(--vg-text-strong);
            border: 1px solid var(--vg-border-strong);
            box-shadow: 0 4px 16px var(--vg-accent-glow);
        }
        .sidebar-item .s-icon {
            font-size: 1.2rem;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
        }
        .sidebar-item .s-icon i {
            width: 1.05rem;
            height: 1.05rem;
            margin: 0 auto;
            stroke-width: 2.35;
        }

        /* Sidebar divider */
        .s-divider {
            height: 1px;
            background: var(--vg-border);
            margin: 12px 16px;
        }

        /* Streak widget */
        .streak-widget {
            background: linear-gradient(135deg, var(--vg-accent-soft), rgba(255,255,255,.04));
            border: 1px solid var(--vg-border-strong);
            border-radius: 16px;
            padding: 16px;
            text-align: center;
        }
        .streak-widget .s-num {
            font-size: 1.8rem;
            font-weight: 900;
            background: var(--vg-title-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }
        .streak-widget .s-lbl {
            font-size: .73rem;
            color: var(--vg-text-muted);
            font-weight: 500;
            margin-top: 2px;
        }

        /* Main content */
        .main-content {
            margin-left: 240px;
            padding: 88px 1.5rem 2rem;
            min-height: 100vh;
            position: relative;
            z-index: 10;
        }

        /* Alert banners */
        .alert-success {
            background: rgba(16,185,129,.12);
            border: 1px solid rgba(16,185,129,.3);
            border-left: 4px solid #10b981;
            border-radius: 12px;
            padding: 14px 18px;
            color: #6ee7b7;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }
        .alert-error {
            background: rgba(239,68,68,.12);
            border: 1px solid rgba(239,68,68,.3);
            border-left: 4px solid #ef4444;
            border-radius: 12px;
            padding: 14px 18px;
            color: #fca5a5;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 1.5rem;
        }

        /* Animations */
        .fade-in-up {
            animation: fadeInUp 0.55s cubic-bezier(.23,1,.32,1) forwards;
            opacity: 0;
        }
        .delay-1 { animation-delay: 0.08s; }
        .delay-2 { animation-delay: 0.16s; }
        .delay-3 { animation-delay: 0.24s; }
        .delay-4 { animation-delay: 0.32s; }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Unread badge */
        #unreadBadge {
            background: #ef4444;
            color: white;
            font-size: .65rem;
            padding: 1px 6px;
            border-radius: 50px;
            font-weight: 700;
        }
        .music-toggle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 1px solid var(--vg-border);
            background: var(--vg-panel);
            color: var(--vg-text-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all .2s ease;
        }
        .music-toggle:hover,
        .music-toggle.playing {
            color: var(--vg-text-strong);
            border-color: var(--vg-border-strong);
            background: var(--vg-accent-soft);
            box-shadow: 0 6px 18px var(--vg-accent-glow);
        }

        /* Mobile */
        @media (max-width: 900px) {
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.3s ease;
            }
            .sidebar.mobile-open {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .hamburger { display: flex !important; }
        }
        .hamburger {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            padding: 4px;
        }
        .hamburger span {
            width: 22px;
            height: 2px;
            background: var(--vg-text-strong);
            border-radius: 2px;
            transition: all .3s;
        }

        /* User info in nav */
        .nav-user-info p { line-height: 1.2; }
        .nav-user-name { font-size: .85rem; font-weight: 600; color: var(--vg-text-strong); }
        .nav-user-email { font-size: .72rem; color: var(--vg-text-faint); }
        html[data-theme] .bg-white,
        html[data-theme] .bg-gray-800\/50 {
            background-color: var(--vg-panel) !important;
            border-color: var(--vg-border) !important;
            color: var(--vg-text-strong);
        }
        html[data-theme] .bg-gray-700\/50,
        html[data-theme] .bg-gray-700,
        html[data-theme] .bg-gray-900\/50 {
            background-color: rgba(255,255,255,.055) !important;
        }
        html[data-theme] .border-gray-700,
        html[data-theme] .border-gray-600,
        html[data-theme] .border-gray-300 {
            border-color: var(--vg-border) !important;
        }
        html[data-theme] .text-gray-900,
        html[data-theme] .text-gray-800,
        html[data-theme] .text-gray-700,
        html[data-theme] .text-gray-300,
        html[data-theme] .text-gray-200,
        html[data-theme] .text-white {
            color: var(--vg-text-strong) !important;
        }
        html[data-theme] .text-gray-600,
        html[data-theme] .text-gray-500,
        html[data-theme] .text-gray-400 {
            color: var(--vg-text-muted) !important;
        }
        html[data-theme] input,
        html[data-theme] textarea,
        html[data-theme] select {
            background-color: rgba(255,255,255,.055) !important;
            border-color: var(--vg-border) !important;
            color: var(--vg-text-strong) !important;
        }
        html[data-theme] input::placeholder,
        html[data-theme] textarea::placeholder {
            color: var(--vg-text-faint) !important;
        }
    </style>
</head>
<body>
    <canvas id="stars"></canvas>
    <div class="orb o1"></div>
    <div class="orb o2"></div>
    <div class="orb o3"></div>
    <div id="youtubeBackgroundPlayer" style="position:fixed;left:-9999px;top:-9999px;width:1px;height:1px;opacity:0;pointer-events:none;" aria-hidden="true"></div>

    <!-- NAVBAR -->
    <nav class="nav-dark">
        <div class="nav-inner">
            <!-- Logo -->
            <a href="{{ route('dashboard') }}" class="logo-pill">
                <div class="logo-badge">
                    <img src="/images/logo.png" alt="VG" style="width:24px;height:24px;border-radius:50%;object-fit:cover;" onerror="this.style.display='none';this.parentElement.innerHTML='<span style=\'font-size:.75rem;font-weight:900;color:#fff;\'>VG</span>';">
                </div>
                <div>
                    <div class="brand-name">VIRTU GYM</div>
                    <div class="brand-sub">{{ ucwords(strtolower(Auth::user()->name ?? 'Virtual Trainer')) }}</div>
                </div>
            </a>

            <!-- Search Bar (Optional) -->
            <div class="hidden md:flex items-center bg-gray-800/50 border border-gray-700 rounded-full px-4 py-1.5 ml-8 mr-auto max-w-sm w-full">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 mr-2"></i>
                <input type="text" placeholder="Search clients, bookings..." class="bg-transparent border-none text-sm text-white placeholder-gray-500 w-full focus:outline-none">
            </div>

            <div style="display:flex;align-items:center;gap:1.2rem;">
                <button type="button" id="musicToggle" class="music-toggle" title="Toggle background music" aria-label="Toggle background music">
                    <i data-lucide="music" class="w-4 h-4"></i>
                </button>
                
                <!-- Notification Bell -->
                <button class="relative text-gray-400 hover:text-white transition focus:outline-none flex items-center justify-center">
                    <i data-lucide="bell" class="w-5 h-5"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center border border-gray-900">3</span>
                </button>

                <!-- User info -->
                <div class="nav-user-info hidden lg:block text-right border-l border-gray-700/50 pl-4">
                    <p class="nav-user-name">{{ ucwords(strtolower(Auth::user()->name ?? 'User')) }}</p>
                    <p class="nav-user-email">{{ Auth::user()->email ?? '' }}</p>
                </div>

                <!-- Avatar + dropdown -->
                <div class="relative" id="userDropdown">
                    <button onclick="toggleDropdown()" class="focus:outline-none flex items-center">
                        <div class="user-avatar">{{ substr(Auth::user()->name ?? 'U', 0, 1) }}</div>
                    </button>
                    <div id="dropdownMenu" class="dropdown-menu absolute right-0 mt-3 py-2 z-50 hidden">
                        <div style="padding:10px 16px 8px;border-bottom:1px solid rgba(139,92,246,.15);margin-bottom:4px;">
                            <p style="font-size:.82rem;font-weight:700;color:var(--vg-text-strong);">{{ Auth::user()->name ?? 'User' }}</p>
                            <p style="font-size:.72rem;color:var(--vg-text-faint);">{{ Auth::user()->email ?? '' }}</p>
                        </div>
                        <div class="theme-grid" aria-label="Appearance themes">
                            <button type="button" class="theme-choice" data-theme-choice="aurora" title="Aurora theme" aria-label="Aurora theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="ember" title="Ember theme" aria-label="Ember theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="ocean" title="Ocean theme" aria-label="Ocean theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="forest" title="Forest theme" aria-label="Forest theme"></button>
                            <button type="button" class="theme-choice" data-theme-choice="graphite" title="Graphite theme" aria-label="Graphite theme"></button>
                        </div>
                        <a href="{{ route('profile.edit') }}" style="padding:9px 16px;color:var(--vg-text-muted);font-size:.83rem;"><i data-lucide="user" class="vg-inline-icon"></i>Profile</a>
                        <a href="#" style="padding:9px 16px;color:var(--vg-text-muted);font-size:.83rem;"><i data-lucide="settings" class="vg-inline-icon"></i>Settings</a>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="padding:9px 16px;color:#f87171;font-size:.83rem;text-align:left;background:none;border:none;cursor:pointer;width:100%;">
                                <i data-lucide="log-out" class="vg-inline-icon"></i>Logout
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Hamburger (mobile) -->
                <div class="hamburger" id="hamburger" onclick="toggleSidebar()">
                    <span></span><span></span><span></span>
                </div>
            </div>
        </div>
    </nav>

    <!-- SIDEBAR -->
    <aside id="sidebar" class="sidebar">
        <div class="logo-v2">
            <div class="logo-icon-v2">💪</div>
            <div>
                <div class="logo-text-v2">VIRTU GYM</div>
                <div class="logo-sub-v2">Virtual Trainer</div>
            </div>
        </div>
        <div style="padding:1.2rem 1rem;">
            <p style="font-size:.65rem;color:rgba(255,255,255,.2);font-weight:700;letter-spacing:.12em;padding:0 8px;margin-bottom:.6rem;">MAIN</p>
            <nav style="display:flex;flex-direction:column;">
                @if(Auth::user()->role == 'admin')
                <a href="{{ route('admin.dashboard') }}" class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="chart-no-axes-combined"></i></span><span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="sidebar-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="users"></i></span><span>Users</span>
                </a>
                <a href="{{ route('admin.trainers') }}" class="sidebar-item {{ request()->routeIs('admin.trainers') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="dumbbell"></i></span><span>Trainers</span>
                </a>
                <a href="{{ route('admin.bookings') }}" class="sidebar-item {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="calendar-days"></i></span><span>Bookings</span>
                </a>
                <a href="{{ route('admin.withdrawals') }}" class="sidebar-item {{ request()->routeIs('admin.withdrawals') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="wallet"></i></span><span>Withdrawals</span>
                </a>
                @else
                <a href="{{ route('dashboard') }}" class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="chart-no-axes-combined"></i></span><span>Dashboard</span>
                </a>
                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('analytics.index') }}" class="sidebar-item {{ request()->routeIs('analytics.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="trending-up"></i></span>
                    <span>Analytics</span>
                </a>
                @endif
                <a href="{{ route('workouts.index') }}" class="sidebar-item {{ request()->routeIs('workouts.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="dumbbell"></i></span>
                    <span>{{ Auth::user()->role == 'trainer' ? 'Client Workouts' : 'Workouts' }}</span>
                </a>
                <a href="{{ route('exercises.index') }}" class="sidebar-item {{ request()->routeIs('exercises.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="activity"></i></span>
                    <span>{{ Auth::user()->role == 'trainer' ? 'Exercise Library' : 'Exercises' }}</span>
                </a>
                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('progress.index') }}" class="sidebar-item {{ request()->routeIs('progress.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="target"></i></span><span>Progress</span>
                </a>
                @endif
                <a href="{{ route('chat.index') }}" class="sidebar-item {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="message-circle"></i></span><span>Messages</span>
                    <span id="unreadBadge" class="hidden" style="margin-left:auto;"></span>
                </a>

                @if(Auth::user()->role == 'trainee')
                <!-- AI COACH SIDEBAR LINK -->
                <a href="{{ route('ai.dashboard') }}" class="sidebar-item {{ request()->routeIs('ai.dashboard') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="bot"></i></span>
                    <span>AI Coach</span>
                </a>
                
                <a href="{{ route('ai.live-coach') }}" class="sidebar-item {{ request()->routeIs('ai.live-coach') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="camera"></i></span>
                    <span>Live Form Check</span>
                </a>

                <a href="{{ route('music.index') }}" class="sidebar-item {{ request()->routeIs('music.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="music"></i></span>
                    <span>Workout Music</span>
                </a>
                @endif

                <a href="{{ route('water.index') }}" class="sidebar-item {{ request()->routeIs('water.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="droplets"></i></span><span>Water Tracker</span>
                </a>

                <a href="{{ route('mindfulness.index') }}" class="sidebar-item {{ request()->routeIs('mindfulness.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="leaf"></i></span><span>Mindfulness</span>
                </a>

                @if(Auth::user()->role == 'trainer')
                <a href="{{ route('trainer.availability.index') }}" class="sidebar-item {{ request()->routeIs('trainer.availability.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="clock"></i></span><span>Availability</span>
                </a>
                <a href="{{ route('bookings.index') }}" class="sidebar-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="calendar-days"></i></span><span>Bookings</span>
                </a>
                <a href="{{ route('trainer.withdrawals') }}" class="sidebar-item {{ request()->routeIs('trainer.withdrawals') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="wallet"></i></span><span>Withdrawals</span>
                </a>
                @endif

                @if(Auth::user()->role == 'trainee')
                <a href="{{ route('bookings.index') }}" class="sidebar-item {{ request()->routeIs('bookings.*') ? 'active' : '' }}">
                    <span class="s-icon"><i data-lucide="calendar-days"></i></span><span>My Sessions</span>
                </a>
                @endif
                @endif
            </nav>

            <div class="s-divider" style="margin-top:1rem;"></div>

            <div class="appearance-panel" style="margin:0 0 1rem;">
                <div class="appearance-title">APPEARANCE</div>
                <div class="theme-list">
                    <button type="button" class="theme-row" data-theme-choice="aurora">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#8b5cf6,#ec4899);"></span>
                        Aurora
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="ember">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#f97316,#e11d48);"></span>
                        Ember
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="ocean">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#0ea5e9,#14b8a6);"></span>
                        Ocean
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="forest">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#22c55e,#84cc16);"></span>
                        Forest
                    </button>
                    <button type="button" class="theme-row" data-theme-choice="graphite">
                        <span class="theme-dot" style="background:linear-gradient(135deg,#94a3b8,#38bdf8);"></span>
                        Graphite
                    </button>
                </div>
            </div>


        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content" id="mainContent">
        @if(session('success'))
            <div class="alert-success fade-in-up">
                <span style="font-size:1.2rem;"><i data-lucide="circle-check"></i></span>
                <p style="font-weight:500;">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="alert-error fade-in-up">
                <span style="font-size:1.2rem;"><i data-lucide="circle-x"></i></span>
                <p style="font-weight:500;">{{ session('error') }}</p>
            </div>
        @endif

        <div class="fade-in-up">
            @yield('content')
        </div>
    </main>



    <script>
    // Starfield
    (function(){
        const c=document.getElementById('stars'),ctx=c.getContext('2d');let W,H,S=[];
        function resize(){W=c.width=innerWidth;H=c.height=innerHeight;}
        function init(){S=Array.from({length:160},()=>({x:Math.random()*W,y:Math.random()*H,r:Math.random()*1.1+.2,a:Math.random(),da:(Math.random()-.5)*.005}));}
        let cachedAccent = null;
        let lastTheme = null;

        function getAccentColor(alpha) {
            const currentTheme = document.documentElement.dataset.theme;
            if (currentTheme !== lastTheme || !cachedAccent) {
                lastTheme = currentTheme;
                const accent = getComputedStyle(document.documentElement).getPropertyValue('--vg-accent').trim() || '#c4b5fd';
                if (accent.startsWith('#')) {
                    const hex = accent.replace('#','');
                    const value = hex.length === 3 ? hex.split('').map(ch => ch + ch).join('') : hex;
                    const num = parseInt(value, 16);
                    cachedAccent = [(num >> 16) & 255, (num >> 8) & 255, num & 255];
                } else {
                    cachedAccent = [196, 181, 253];
                }
            }
            return `rgba(${cachedAccent[0]},${cachedAccent[1]},${cachedAccent[2]},${alpha})`;
        }
        function draw(){ctx.clearRect(0,0,W,H);S.forEach(s=>{s.a=Math.max(.05,Math.min(1,s.a+s.da));if(s.a<=.05||s.a>=1)s.da*=-1;ctx.beginPath();ctx.arc(s.x,s.y,s.r,0,Math.PI*2);ctx.fillStyle=getAccentColor(s.a);ctx.fill();});requestAnimationFrame(draw);}
        window.addEventListener('resize',()=>{resize();init();});resize();init();draw();
    })();

    // Appearance themes
    (function(){
        const themes = ['aurora', 'ember', 'ocean', 'forest', 'graphite'];
        const buttons = document.querySelectorAll('[data-theme-choice]');

        function setTheme(theme) {
            const nextTheme = themes.includes(theme) ? theme : 'aurora';
            document.documentElement.dataset.theme = nextTheme;
            localStorage.setItem('virtugym-theme', nextTheme);
            buttons.forEach(button => {
                button.classList.toggle('active', button.dataset.themeChoice === nextTheme);
            });
        }

        buttons.forEach(button => {
            button.addEventListener('click', function(){
                setTheme(this.dataset.themeChoice);
            });
        });

        setTheme(localStorage.getItem('virtugym-theme') || 'aurora');
    })();

    // Dropdown
    function toggleDropdown() {
        document.getElementById('dropdownMenu').classList.toggle('hidden');
    }
    document.addEventListener('click', function(e) {
        const d = document.getElementById('userDropdown');
        const m = document.getElementById('dropdownMenu');
        if (d && m && !d.contains(e.target)) m.classList.add('hidden');
    });

    // Mobile sidebar
    function toggleSidebar() {
        document.getElementById('sidebar').classList.toggle('mobile-open');
    }

    // Auto-dismiss alerts
    setTimeout(function() {
        document.querySelectorAll('.alert-success, .alert-error').forEach(function(el) {
            el.style.transition = 'opacity .4s';
            el.style.opacity = '0';
            setTimeout(()=>el.remove(), 400);
        });
    }, 5000);
    
    // Smooth Page Exit
    window.addEventListener('beforeunload', () => {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.25s ease';
    });

    // YouTube-backed gym background music. Browsers allow the player to load on
    // page load, but audible playback still needs the first user gesture.
    (function(){
        const toggle = document.getElementById('musicToggle');
        const STORAGE_KEY = 'virtugym-background-music';
        const endpoint = @json(route('music.background'));
        let player = null;
        let videoId = null;
        let isPlaying = false;
        let enabled = localStorage.getItem(STORAGE_KEY) !== 'off';

        function refreshToggle() {
            toggle?.classList.toggle('playing', enabled && isPlaying);
            toggle?.setAttribute('aria-pressed', enabled && isPlaying ? 'true' : 'false');
            toggle?.setAttribute('title', enabled ? 'Background music on' : 'Background music off');
        }

        function loadYouTubeApi() {
            if (window.YT && window.YT.Player) return Promise.resolve();
            if (window.virtugymYouTubeApiPromise) return window.virtugymYouTubeApiPromise;

            window.virtugymYouTubeApiPromise = new Promise(resolve => {
                const previousReady = window.onYouTubeIframeAPIReady;
                window.onYouTubeIframeAPIReady = function() {
                    if (typeof previousReady === 'function') previousReady();
                    resolve();
                };

                const script = document.createElement('script');
                script.src = 'https://www.youtube.com/iframe_api';
                document.head.appendChild(script);
            });

            return window.virtugymYouTubeApiPromise;
        }

        async function getBackgroundVideoId() {
            if (videoId) return videoId;

            try {
                const response = await fetch(endpoint, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                });
                const payload = await response.json();
                videoId = payload?.song?.video_id || null;
            } catch (error) {
                videoId = null;
            }

            return videoId;
        }

        async function createPlayer() {
            if (player) return player;

            const nextVideoId = await getBackgroundVideoId();
            if (!nextVideoId) return null;

            await loadYouTubeApi();

            player = new YT.Player('youtubeBackgroundPlayer', {
                width: '1',
                height: '1',
                videoId: nextVideoId,
                playerVars: {
                    autoplay: 0,
                    controls: 0,
                    disablekb: 1,
                    fs: 0,
                    loop: 1,
                    modestbranding: 1,
                    playsinline: 1,
                    playlist: nextVideoId,
                    rel: 0,
                },
                events: {
                    onReady: function(event) {
                        event.target.setVolume(38);
                        if (enabled) startMusic(false);
                    },
                    onStateChange: function(event) {
                        if (event.data === YT.PlayerState.PLAYING) {
                            isPlaying = true;
                            refreshToggle();
                        } else if (event.data === YT.PlayerState.PAUSED || event.data === YT.PlayerState.ENDED) {
                            isPlaying = false;
                            refreshToggle();
                        }
                    },
                    onError: function() {
                        isPlaying = false;
                        refreshToggle();
                    },
                },
            });

            return player;
        }

        async function startMusic(force = false) {
            if (!enabled && !force) return;
            const nextPlayer = await createPlayer();
            if (!nextPlayer || isPlaying || typeof nextPlayer.playVideo !== 'function') return;

            try {
                enabled = true;
                localStorage.setItem(STORAGE_KEY, 'on');
                nextPlayer.unMute();
                nextPlayer.setVolume(38);
                nextPlayer.playVideo();
                isPlaying = true;
                refreshToggle();
            } catch (error) {
                isPlaying = false;
                refreshToggle();
            }
        }

        function stopMusic() {
            isPlaying = false;
            if (player && typeof player.pauseVideo === 'function') {
                player.pauseVideo();
            }
            refreshToggle();
        }

        toggle?.addEventListener('click', function(){
            if (isPlaying) {
                enabled = false;
                localStorage.setItem(STORAGE_KEY, 'off');
                stopMusic();
            } else {
                enabled = true;
                localStorage.setItem(STORAGE_KEY, 'on');
                startMusic(true);
            }
        });

        function unlockFromGesture(event) {
            if (toggle && event.target && toggle.contains(event.target)) return;
            startMusic(false);
        }

        refreshToggle();
        window.addEventListener('load', () => createPlayer());
        ['pointerdown', 'keydown', 'touchstart'].forEach(eventName => {
            window.addEventListener(eventName, unlockFromGesture, { passive: true });
        });
    })();
    </script>
    @stack('modals')
    @stack('scripts')
</body>
</html>
