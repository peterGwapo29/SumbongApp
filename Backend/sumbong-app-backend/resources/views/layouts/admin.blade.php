<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }} - Admin</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <style>
        :root {
            --sidebar-width: 260px;
            --header-height: 60px;
            --accent: #2563EB;
            --accent-light: #EFF6FF;
            --surface: #ffffff;
            --border: #E5E7EB;
            --text-primary: #111827;
            --text-secondary: #6B7280;
            --text-muted: #9CA3AF;
            --bg: #F3F4F6;
            --sidebar-bg: #111827;
            --sidebar-text: #D1D5DB;
            --sidebar-text-active: #ffffff;
            --sidebar-active-bg: rgba(255,255,255,0.1);
            --sidebar-hover-bg: rgba(255,255,255,0.06);
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 16px rgba(0,0,0,0.10);
            --radius: 10px;
        }
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Instrument Sans', system-ui, sans-serif;
            background: var(--bg);
            color: var(--text-primary);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
        }

        /* ── Layout ── */
        .admin-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ── Sidebar ── */
        .sidebar {
            width: var(--sidebar-width);
            background: var(--sidebar-bg);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            z-index: 50;
            transform: translateX(0);
            transition: transform 0.25s cubic-bezier(0.4,0,0.2,1);
            will-change: transform;
        }
        .sidebar.mobile-hidden {
            transform: translateX(-100%);
        }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 10px;
            height: var(--header-height);
            padding: 0 20px;
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .sidebar-logo-icon {
            width: 32px; height: 32px;
            background: var(--accent);
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .sidebar-logo-icon svg { width: 18px; height: 18px; color: #fff; }
        .sidebar-logo-name {
            font-size: 17px;
            font-weight: 700;
            color: #fff;
            letter-spacing: -0.3px;
        }
        .sidebar-logo-badge {
            margin-left: auto;
            font-size: 10px;
            font-weight: 600;
            background: rgba(37,99,235,0.25);
            color: #93C5FD;
            padding: 2px 7px;
            border-radius: 20px;
            letter-spacing: 0.4px;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 14px 12px;
            overflow-y: auto;
            scrollbar-width: none;
        }
        .sidebar-nav::-webkit-scrollbar { display: none; }

        .nav-section-label {
            font-size: 10px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255,255,255,0.25);
            padding: 0 10px;
            margin: 16px 0 6px;
        }
        .nav-section-label:first-child { margin-top: 4px; }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 9px 12px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: background 0.15s, color 0.15s;
            position: relative;
            margin-bottom: 2px;
        }
        .nav-item:hover {
            background: var(--sidebar-hover-bg);
            color: #fff;
        }
        .nav-item.active {
            background: var(--sidebar-active-bg);
            color: var(--sidebar-text-active);
        }
        .nav-item.active::before {
            content: '';
            position: absolute;
            left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 18px;
            background: var(--accent);
            border-radius: 0 3px 3px 0;
        }
        .nav-item svg {
            width: 17px; height: 17px;
            flex-shrink: 0;
            opacity: 0.6;
            transition: opacity 0.15s;
        }
        .nav-item:hover svg,
        .nav-item.active svg { opacity: 1; }

        .nav-badge {
            margin-left: auto;
            font-size: 11px;
            font-weight: 600;
            background: var(--accent);
            color: #fff;
            padding: 1px 7px;
            border-radius: 20px;
            min-width: 20px;
            text-align: center;
        }

        /* User card at bottom */
        .sidebar-user {
            padding: 14px 12px;
            border-top: 1px solid rgba(255,255,255,0.07);
        }
        .user-card {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px;
            border-radius: 8px;
            background: rgba(255,255,255,0.05);
        }
        .user-avatar {
            width: 34px; height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #2563EB, #7C3AED);
            display: flex; align-items: center; justify-content: center;
            font-size: 13px; font-weight: 700;
            color: #fff;
            flex-shrink: 0;
        }
        .user-info { flex: 1; min-width: 0; }
        .user-name {
            font-size: 13px; font-weight: 600;
            color: #fff;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-email {
            font-size: 11px; color: rgba(255,255,255,0.35);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-actions {
            display: flex; gap: 2px; margin-top: 8px;
        }
        .user-action-btn {
            flex: 1;
            display: flex; align-items: center; justify-content: center; gap: 6px;
            padding: 7px 10px;
            border-radius: 6px;
            font-size: 12px; font-weight: 500;
            color: var(--sidebar-text);
            text-decoration: none;
            border: none; cursor: pointer; background: transparent;
            transition: background 0.15s, color 0.15s;
            font-family: inherit;
        }
        .user-action-btn:hover { background: rgba(255,255,255,0.08); color: #fff; }
        .user-action-btn svg { width: 13px; height: 13px; }
        .user-action-btn.logout { color: #FCA5A5; }
        .user-action-btn.logout:hover { background: rgba(239,68,68,0.15); color: #F87171; }

        /* ── Overlay ── */
        .sidebar-overlay {
            display: none;
            position: fixed; inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 40;
            backdrop-filter: blur(2px);
        }
        .sidebar-overlay.visible { display: block; }

        /* ── Main ── */
        .main-area {
            flex: 1;
            margin-left: var(--sidebar-width);
            display: flex; flex-direction: column;
            min-height: 100vh;
            min-width: 0;
        }

        /* ── Topbar ── */
        .topbar {
            height: var(--header-height);
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            display: flex; align-items: center;
            padding: 0 24px;
            gap: 16px;
            position: sticky; top: 0; z-index: 30;
            box-shadow: var(--shadow-sm);
        }
        .topbar-hamburger {
            display: none;
            padding: 6px;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            cursor: pointer;
            color: var(--text-secondary);
            transition: background 0.15s;
        }
        .topbar-hamburger:hover { background: var(--bg); }
        .topbar-hamburger svg { width: 18px; height: 18px; display: block; }

        .topbar-title {
            font-size: 16px;
            font-weight: 700;
            color: var(--text-primary);
            letter-spacing: -0.2px;
        }
        .topbar-breadcrumb {
            display: flex; align-items: center; gap: 6px;
            font-size: 13px;
            color: var(--text-muted);
        }
        .topbar-breadcrumb span { color: var(--text-secondary); font-weight: 500; }

        .topbar-right {
            margin-left: auto;
            display: flex; align-items: center; gap: 10px;
        }
        .topbar-date {
            display: flex; align-items: center; gap: 6px;
            font-size: 12.5px; font-weight: 500;
            color: var(--text-secondary);
            background: var(--bg);
            padding: 6px 12px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .topbar-date svg { width: 13px; height: 13px; color: var(--text-muted); }

        .topbar-notif {
            position: relative;
            width: 36px; height: 36px;
            display: flex; align-items: center; justify-content: center;
            border-radius: 8px;
            border: 1px solid var(--border);
            background: transparent;
            cursor: pointer;
            color: var(--text-secondary);
            transition: background 0.15s;
        }
        .topbar-notif:hover { background: var(--bg); }
        .topbar-notif svg { width: 17px; height: 17px; }
        .notif-dot {
            position: absolute; top: 7px; right: 7px;
            width: 7px; height: 7px;
            background: #EF4444;
            border-radius: 50%;
            border: 1.5px solid var(--surface);
        }

        /* ── Page Content ── */
        .page-content {
            flex: 1;
            padding: 28px 28px;
        }

        /* ── Responsive ── */
        @media (max-width: 1024px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.open {
                transform: translateX(0);
            }
            .main-area {
                margin-left: 0;
            }
            .topbar-hamburger {
                display: flex; align-items: center;
            }
        }
    </style>
</head>
<body>
<div class="admin-shell" x-data="{ sidebarOpen: false }">

    {{-- Sidebar --}}
    <aside class="sidebar" :class="{ 'open': sidebarOpen }">
        {{-- Logo --}}
        <div class="sidebar-logo">
            <div class="sidebar-logo-icon">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                </svg>
            </div>
            <span class="sidebar-logo-name">Sumbong</span>
            <span class="sidebar-logo-badge">ADMIN</span>
        </div>

        {{-- Navigation --}}
        <nav class="sidebar-nav">
            <div class="nav-section-label">Overview</div>

            <a href="{{ route('admin.dashboard') }}"
               class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                </svg>
                Dashboard
            </a>

            <div class="nav-section-label">Manage</div>

            <a href="{{ route('admin.requests.index') }}"
               class="nav-item {{ request()->routeIs('admin.requests.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Requests
            </a>

            <a href="{{ route('admin.users.index') }}"
               class="nav-item {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                Users
            </a>

            <a href="{{ route('admin.service-types.index') }}"
               class="nav-item {{ request()->routeIs('admin.service-types.*') ? 'active' : '' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
                Service Types
            </a>
        </nav>

        {{-- User Section --}}
        <div class="sidebar-user">
            <div class="user-card">
                <div class="user-avatar">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</div>
                <div class="user-info">
                    <div class="user-name">{{ Auth::user()->name }}</div>
                    <div class="user-email">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="user-actions">
                <a href="{{ route('profile.edit') }}" class="user-action-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" style="flex:1; display:flex;">
                    @csrf
                    <button type="submit" class="user-action-btn logout" style="width:100%;">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- Mobile overlay --}}
    <div class="sidebar-overlay" :class="{ 'visible': sidebarOpen }" @click="sidebarOpen = false"></div>

    {{-- Main --}}
    <div class="main-area">
        {{-- Topbar --}}
        <header class="topbar">
            <button class="topbar-hamburger" @click="sidebarOpen = !sidebarOpen">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
            </button>

            <div>
                <div class="topbar-title">
                    @isset($header){{ $header }}@else Admin Panel @endisset
                </div>
            </div>

            <div class="topbar-right">
                <div class="topbar-date">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ now()->format('M j, Y') }}
                </div>
                <button class="topbar-notif">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span class="notif-dot"></span>
                </button>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            {{ $slot }}
        </main>
    </div>

</div>
</body>
</html>