<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>VirtuGym Admin - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <script src="/js/virtugym-icons.js" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .vg-inline-icon { width: 1em; height: 1em; display: inline-block; vertical-align: -0.14em; margin-right: .35em; stroke-width: 2.4; }
        body { background: #f3f4f6; }
        .sidebar { background: linear-gradient(135deg, #1e1b4b, #4c1d95); }
        .sidebar-item:hover { background: rgba(255,255,255,0.1); transform: translateX(5px); transition: all 0.3s; }
        .sidebar-item.active { background: rgba(255,255,255,0.15); border-left: 3px solid #ec4899; }
        .stat-card { transition: transform 0.3s; }
        .stat-card:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
    <div class="flex min-h-screen">
        <aside class="sidebar w-64 text-white fixed h-full overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center space-x-3 mb-8">
                    <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center">
                        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="w-8 h-8 rounded-full object-cover">
                    </div>
                    <span class="text-xl font-bold">VirtuGym Admin</span>
                </div>
                <nav class="space-y-2">
                    <a href="{{ route('admin.dashboard') }}" class="sidebar-item block px-4 py-2 rounded-lg {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('admin.users') }}" class="sidebar-item block px-4 py-2 rounded-lg {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        👥 Users
                    </a>
                    <a href="{{ route('admin.trainers') }}" class="sidebar-item block px-4 py-2 rounded-lg {{ request()->routeIs('admin.trainers') ? 'active' : '' }}">
                        🏋️ Trainers
                    </a>
                    <a href="{{ route('admin.bookings') }}" class="sidebar-item block px-4 py-2 rounded-lg {{ request()->routeIs('admin.bookings') ? 'active' : '' }}">
                        📅 Bookings
                    </a>
                    <a href="{{ route('admin.withdrawals') }}" class="sidebar-item block px-4 py-2 rounded-lg {{ request()->routeIs('admin.withdrawals') ? 'active' : '' }}">
                        💰 Withdrawals
                    </a>
                </nav>
            </div>
        </aside>
        
        <main class="flex-1 ml-64">
            <nav class="bg-white shadow px-6 py-3 flex justify-between items-center sticky top-0 z-10">
                <h1 class="text-xl font-bold">@yield('title')</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-700">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="text-red-600 hover:text-red-800 transition">Logout</button>
                    </form>
                </div>
            </nav>
            <div class="p-6">
                @if(session('success'))
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4 rounded">
                        {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4 rounded">
                        {{ session('error') }}
                    </div>
                @endif
                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
