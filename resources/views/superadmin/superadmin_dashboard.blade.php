<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Command Center | ExamSystem</title>
    {{-- Tailwind CSS v3 --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- FontAwesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes ping-slow {
            75%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .ping-slow { animation: ping-slow 2s cubic-bezier(0,0,0.2,1) infinite; }
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .count-animate { animation: countUp 0.4s ease-out forwards; }
        @keyframes shimmer {
            0%   { background-position: -600px 0; }
            100% { background-position: 600px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 1000px 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 8px;
        }
        .thin-scroll::-webkit-scrollbar { width: 4px; }
        .thin-scroll::-webkit-scrollbar-track { background: transparent; }
        .thin-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .feed-row { opacity: 0; animation: fadeSlideIn 0.35s ease-out forwards; }
        #lockdown-modal { display: none; }
        #lockdown-modal.open { display: flex; }
        .toast-enter { opacity: 0; transform: translateY(12px); transition: opacity 0.3s, transform 0.3s; }
        .toast-visible { opacity: 1; transform: translateY(0); }
        .toast-leave { opacity: 0; transform: translateY(12px); }
        @keyframes pulse-border {
            0%, 100% { border-color: rgba(239,68,68,0.3); }
            50% { border-color: rgba(239,68,68,0.8); }
        }
        .pulse-border { animation: pulse-border 2s ease-in-out infinite; }
    </style>
    @include('partials.notification-styles')
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" style="font-family: 'Inter', sans-serif;">
<div class="flex min-h-screen">
    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-20" style="box-shadow: 4px 0 24px rgba(148,163,184,0.08);">
        {{-- Logo --}}
        <div class="h-16 flex items-center px-5 gap-3 border-b border-slate-100 flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0" style="box-shadow: 0 4px 14px rgba(59,130,246,0.45);">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-slate-900 text-sm tracking-tight leading-none">ExamSystem</h1>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest">Super Admin</span>
                </div>
            </div>
        </div>
        {{-- Navigation --}}
        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>
            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow: 0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-gauge-high text-xs text-white"></i>
                </span>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-desktop text-xs text-slate-400"></i>
                </span>
                <span class="flex-1">Live Monitoring</span>
                <span class="text-[9px] bg-rose-100 text-rose-600 font-bold px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
            </a>
            <a href="{{ route('superadmin.exams.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-file-signature text-xs text-slate-400"></i>
                </span>
                <span>Exams Oversight</span>
            </a>
            <a href="{{ route('superadmin.reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-xs text-slate-400"></i>
                </span>
                <span>Reports & Analytics</span>
            </a>
            <div class="pt-4 pb-1">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2">Root Access</p>
            </div>
            <a href="{{ route('superadmin.admins.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-users text-xs text-slate-400"></i>
                </span>
                <span>User Management</span>
            </a>
            <a href="{{ route('superadmin.passwordRequests.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-key text-xs text-slate-400"></i>
                </span>
                <span>Password Requests</span>
            </a>
            <a href="{{ route('superadmin.audit-logs.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-xs text-slate-400"></i>
                </span>
                <span>Audit Trails</span>
            </a>
            <a href="{{ route('superadmin.backups.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-database text-xs text-slate-400"></i>
                </span>
                <span>Database & Backup</span>
            </a>
        </nav>
        {{-- Bottom: Settings + User --}}
        <div class="p-3 border-t border-slate-100 flex-shrink-0">
            <a href="{{ route('superadmin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm transition-all duration-200 mb-1">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-sliders text-xs text-slate-400"></i>
                </span>
                <span>Global Settings</span>
            </a>
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <img class="w-8 h-8 rounded-lg object-cover flex-shrink-0"
                     src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name ?? 'Super Admin') . '&background=3b82f6&color=fff&size=64' }}"
                     alt="{{ Auth::user()->full_name ?? 'Super Admin' }}">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->full_name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Super Admin · Root</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-rose-50 hover:text-rose-500 text-slate-400 transition-all duration-150"
                            title="Logout">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>
    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
        {{-- ---- STICKY TOP BAR ---- --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background: rgba(248,250,252,0.85); backdrop-filter: blur(12px); box-shadow: 0 1px 8px rgba(148,163,184,0.10);">
            <div class="relative" style="width: 300px;">
                <i class="fa-solid fa-magnifying-glass absolute text-slate-300 text-xs"
                   style="left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                <input type="text"
                       placeholder="Search exams, users, logs..."
                       class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 focus:outline-none focus:ring-2 focus:ring-blue-200 focus:border-blue-400 transition-all">
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock">--:--:--</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width: 8px; height: 8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    All Systems Operational
                </div>
                @include('partials.superadmin-notification-bell')
            </div>
        </header>
        {{-- ---- PAGE BODY ---- --}}
        <div class="p-8 flex-1" style="display: flex; flex-direction: column; gap: 28px;">
            {{-- Page Title Row --}}
            <div class="flex items-start justify-between">
                <div>
                    <div class="mb-1.5">
                        <span class="text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-0.5 rounded-full uppercase tracking-widest">
                            Root Access
                        </span>
                    </div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight leading-tight">Infrastructure Command Center</h2>
                    <p class="text-sm text-slate-400 font-medium mt-1">Real-time oversight of all examination infrastructure and active sessions.</p>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-100 px-3 py-2 rounded-xl flex-shrink-0" style="box-shadow: 0 1px 4px rgba(148,163,184,0.08);">
                    <i id="refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                    <span>Auto-refresh in</span>
                    <span id="refresh-countdown" class="font-mono font-bold text-slate-700 w-5 text-center">30</span>
                    <span>s</span>
                </div>
            </div>
            {{-- ========== METRIC CARDS (REAL DATA) ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Total Users --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-4 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-users text-blue-500 text-sm"></i>
                        </div>
                        @if($userGrowth != 0)
                        <span class="text-[10px] font-bold {{ $userGrowth >= 0 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : 'text-rose-600 bg-rose-50 border-rose-100' }} border px-2 py-0.5 rounded-full flex items-center gap-1">
                            <i class="fa-solid {{ $userGrowth >= 0 ? 'fa-arrow-trend-up' : 'fa-arrow-trend-down' }} text-[8px]"></i> {{ $userGrowth > 0 ? '+' : '' }}{{ $userGrowth }}%
                        </span>
                        @else
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 border-slate-100 border px-2 py-0.5 rounded-full">New</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Users</p>
                        <p id="live-total-users" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">
                            {{ number_format($totalUsers) }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            {{ $totalUsers === 0 ? 'No accounts created yet' : 'Registered accounts in your institution' }}
                        </p>
                    </div>
                </div>
                {{-- Active Exams --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-4 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-graduation-cap text-violet-500 text-sm"></i>
                        </div>
                        @if($activeExams > 0)
                        <span class="text-[10px] font-bold text-violet-600 bg-violet-50 border border-violet-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-[8px]"></i> Running
                        </span>
                        @else
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">Idle</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Active Exams</p>
                        <p id="live-active-exams" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">
                            {{ $activeExams }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            {{ $activeExams === 0 ? 'No exams running right now' : 'Exams currently in progress' }}
                        </p>
                    </div>
                </div>
                {{-- Live Sessions --}}
                <div class="bg-white rounded-2xl border {{ $liveSessions > 0 ? 'border-rose-100' : 'border-slate-100' }} p-5 flex flex-col gap-4 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06); {{ $liveSessions > 0 ? 'background: linear-gradient(135deg, #fff 70%, rgba(255,228,230,0.3) 100%);' : '' }}"
                     onmouseenter="this.style.boxShadow='{{ $liveSessions > 0 ? '0 8px 24px rgba(244,63,94,0.12)' : '0 8px 24px rgba(148,163,184,0.18)' }}'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 {{ $liveSessions > 0 ? 'bg-rose-50' : 'bg-slate-50' }} rounded-xl flex items-center justify-center relative">
                            <i class="fa-solid fa-tower-broadcast {{ $liveSessions > 0 ? 'text-rose-500' : 'text-slate-400' }} text-sm"></i>
                            @if($liveSessions > 0)
                            <span class="absolute rounded-full bg-rose-500 ring-2 ring-white animate-pulse"
                                  style="width:10px;height:10px;top:-2px;right:-2px;"></span>
                            @endif
                        </div>
                        @if($liveSessions > 0)
                        <span class="text-[10px] font-black text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full animate-pulse uppercase tracking-wider">● LIVE</span>
                        @else
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">Offline</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Live Sessions</p>
                        <p id="live-sessions-count" class="text-3xl font-black {{ $liveSessions > 0 ? 'text-rose-600' : 'text-slate-900' }} leading-none tabular-nums count-animate">
                            {{ $liveSessions }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">
                            {{ $liveSessions === 0 ? 'No candidates testing right now' : 'Candidates actively testing now' }}
                        </p>
                    </div>
                </div>
                {{-- Server Load --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-4 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-microchip text-emerald-500 text-sm"></i>
                        </div>
                        <span id="load-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ $serverLoad < 50 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : ($serverLoad < 80 ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-rose-600 bg-rose-50 border-rose-100') }}">
                            {{ $serverLoad < 50 ? 'Healthy' : ($serverLoad < 80 ? 'Moderate' : 'High') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Server CPU Load</p>
                        <p id="live-server-load" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">
                            {{ $serverLoad }}%
                        </p>
                        <div class="mt-3 rounded-full overflow-hidden bg-slate-100" style="height:6px;">
                            <div id="load-bar" class="h-full rounded-full transition-all duration-700
                                {{ $serverLoad < 50 ? 'bg-emerald-400' : ($serverLoad < 80 ? 'bg-amber-400' : 'bg-rose-400') }}"
                                style="width:{{ $serverLoad }}%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ========== BOTTOM ROW: FEED + ACTIONS ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Activity Feed --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 flex flex-col overflow-hidden"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex" style="width:10px;height:10px;">
                                <span class="ping-slow absolute inline-flex rounded-full bg-blue-400 opacity-75" style="width:100%;height:100%;"></span>
                                <span class="relative inline-flex rounded-full bg-blue-500" style="width:10px;height:10px;"></span>
                            </span>
                            <h4 class="font-bold text-sm text-slate-900">Live Activity Feed</h4>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="feed-count-badge" class="text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-0.5 rounded-full">
                                {{ count($recentLogs) }} events
                            </span>
                            <a href="{{ route('superadmin.audit-logs.index') }}"
                               class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-all"
                               style="text-underline-offset: 2px;">
                               View Full Audit Trail →
                            </a>
                        </div>
                    </div>
                    <div id="async-dashboard-activity-feed"
                         class="overflow-y-auto thin-scroll flex-1 px-3 py-2 divide-y divide-slate-50"
                         style="max-height: 420px;">
                        @if(count($recentLogs) === 0)
                        {{-- EMPTY STATE — shown on fresh installs --}}
                        <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                            <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                                <i class="fa-solid fa-inbox text-slate-300 text-2xl"></i>
                            </div>
                            <h5 class="text-sm font-bold text-slate-400 mb-1">No Activity Yet</h5>
                            <p class="text-xs text-slate-300 max-w-xs">
                                Your activity feed is empty. Actions like creating users, managing exams, and changing settings will appear here in real time.
                            </p>
                        </div>
                        @else
                        {{-- REAL FEED ITEMS --}}
                        @foreach($recentLogs as $index => $log)
                        <div class="feed-row flex gap-3.5 p-3.5 hover:bg-slate-50 rounded-xl transition-all duration-150 cursor-default"
                             style="animation-delay: {{ $index * 0.06 }}s;">
                            {{-- Icon --}}
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0
                                @if(str_contains($log['action'], 'create'))
                                    bg-emerald-50
                                @elseif(str_contains($log['action'], 'delete') || str_contains($log['action'], 'force'))
                                    bg-rose-50
                                @elseif(str_contains($log['action'], 'update') || str_contains($log['action'], 'toggle'))
                                    bg-amber-50
                                @elseif(str_contains($log['action'], 'login') || str_contains($log['action'], 'auth'))
                                    bg-blue-50
                                @else
                                    bg-slate-50
                                @endif
                            ">
                                <i class="text-xs
                                    @if(str_contains($log['action'], 'create'))
                                        fa-solid fa-plus text-emerald-500
                                    @elseif(str_contains($log['action'], 'delete') || str_contains($log['action'], 'force'))
                                        fa-solid fa-trash text-rose-500
                                    @elseif(str_contains($log['action'], 'update') || str_contains($log['action'], 'toggle'))
                                        fa-solid fa-pen text-amber-500
                                    @elseif(str_contains($log['action'], 'login') || str_contains($log['action'], 'auth'))
                                        fa-solid fa-right-to-bracket text-blue-500
                                    @else
                                        fa-solid fa-bolt text-slate-400
                                    @endif
                                "></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-0.5">
                                    <span class="text-xs font-bold text-slate-800 truncate">{{ $log['operator'] }}</span>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded font-mono uppercase
                                        @if(str_contains($log['action'], 'create'))
                                            bg-emerald-50 text-emerald-600
                                        @elseif(str_contains($log['action'], 'delete') || str_contains($log['action'], 'force'))
                                            bg-rose-50 text-rose-600
                                        @elseif(str_contains($log['action'], 'update') || str_contains($log['action'], 'toggle'))
                                            bg-amber-50 text-amber-600
                                        @else
                                            bg-slate-50 text-slate-500
                                        @endif
                                    ">{{ strtoupper(str_replace('.', '_', $log['action'])) }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 truncate">on {{ $log['resource'] }}</p>
                                <div class="flex items-center gap-3 mt-1">
                                    <span class="text-[10px] text-slate-300 flex items-center gap-1">
                                        <i class="fa-solid fa-signal text-[8px]"></i> {{ $log['ip'] }}
                                    </span>
                                </div>
                            </div>
                            <span class="text-[10px] text-slate-300 font-medium whitespace-nowrap flex-shrink-0 pt-0.5">
                                {{ $log['created_at'] }}
                            </span>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
                {{-- Right Column --}}
                <div class="flex flex-col gap-5">
                    {{-- Quick Actions Card --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-5"
                         style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Quick Actions</p>
                        <div class="space-y-2.5">
                            <button onclick="window.location.href='{{ route('superadmin.settings.index') }}'"
                                    class="w-full p-3.5 rounded-xl flex items-center gap-3.5 text-left cursor-pointer border border-slate-900 bg-slate-900 text-white font-semibold transition-all duration-200 hover:bg-slate-800 hover:-translate-y-0.5 group"
                                    style="box-shadow: 0 2px 8px rgba(15,23,42,0.12);">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 bg-white bg-opacity-10">
                                    <i class="fa-solid fa-sliders text-white text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold leading-tight">Global Settings</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Configure SMTP, mail routing & variables</p>
                                </div>
                            </button>
                            <button onclick="window.location.href='{{ route('superadmin.backups.index') }}'"
                                    class="w-full p-3.5 rounded-xl flex items-center gap-3.5 text-left cursor-pointer border border-slate-200 bg-white text-slate-700 font-semibold transition-all duration-200 hover:bg-slate-50 hover:border-slate-300 group">
                                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-database text-violet-500 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold leading-tight">Database Backup</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Trigger a manual backup snapshot</p>
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono flex-shrink-0">{{ $lastBackupHuman }}</span>
                            </button>
                            <button onclick="triggerLockdown()"
                                    class="w-full p-3.5 rounded-xl flex items-center gap-3.5 text-left cursor-pointer border border-slate-200 bg-white text-slate-700 font-semibold transition-all duration-200 group"
                                    style="transition: background 0.2s, border-color 0.2s, color 0.2s;"
                                    onmouseenter="this.style.background='#fff1f2';this.style.borderColor='#fecdd3';this.querySelector('.lock-icon').style.color='#f43f5e';this.querySelector('.lock-wrap').style.background='#ffe4e6';"
                                    onmouseleave="this.style.background='#fff';this.style.borderColor='#e2e8f0';this.querySelector('.lock-icon').style.color='#94a3b8';this.querySelector('.lock-wrap').style.background='#f8fafc';">
                                <div class="lock-wrap w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center flex-shrink-0" style="transition: background 0.2s;">
                                    <i class="lock-icon fa-solid fa-lock text-slate-400 text-xs" style="transition: color 0.2s;"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold leading-tight">Emergency Lockdown</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Isolate all active exam sessions</p>
                                </div>
                                <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                            </button>
                            <button onclick="window.location.href='{{ route('superadmin.admins.index') }}'"
                                    class="w-full p-3.5 rounded-xl flex items-center gap-3.5 text-left cursor-pointer border border-slate-200 bg-white text-slate-700 font-semibold transition-all duration-200 hover:bg-slate-50 hover:border-slate-300 group">
                                <div class="w-8 h-8 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-user-plus text-blue-500 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold leading-tight">Add User</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Create a new account in your institution</p>
                                </div>
                            </button>
                        </div>
                    </div>
                    {{-- Infrastructure Status Card --}}
                    <div class="rounded-2xl text-white p-5 flex flex-col gap-4 relative overflow-hidden"
                         style="background: linear-gradient(135deg, #2563eb, #4f46e5); box-shadow: 0 8px 32px rgba(59,130,246,0.35);">
                        <div class="absolute top-0 left-0 right-0" style="height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.25),transparent);"></div>
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-lg bg-white bg-opacity-15 flex items-center justify-center">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                            </div>
                            <h4 class="font-bold text-sm">Infrastructure Status</h4>
                        </div>
                        <div class="space-y-3 text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-blue-100">Total Exams</span>
                                <span id="infra-total-exams" class="font-bold font-mono">{{ $totalExams }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-100">Stuck Exams</span>
                                <span id="infra-stuck" class="font-bold font-mono {{ $stuckExams > 0 ? 'text-amber-300' : '' }}">{{ $stuckExams }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-100">Flag Rate</span>
                                <span id="infra-flag-rate" class="font-bold font-mono">{{ $flagRate }}%</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-blue-100">Last Backup</span>
                                <span id="infra-backup" class="font-bold font-mono text-[11px]">{{ $lastBackupHuman }}</span>
                            </div>
                        </div>
                        @if($stuckExams > 0)
                        <div class="bg-white bg-opacity-10 rounded-xl p-3 border border-white border-opacity-10 pulse-border">
                            <div class="flex items-center gap-2 text-[11px]">
                                <i class="fa-solid fa-triangle-exclamation text-amber-300"></i>
                                <span class="font-semibold text-amber-100">{{ $stuckExams }} exam(s) stuck for 15+ minutes</span>
                            </div>
                        </div>
                        @endif
                        @if($totalExams === 0 && $totalUsers <= 1)
                        <div class="bg-white bg-opacity-10 rounded-xl p-3 border border-white border-opacity-10">
                            <div class="flex items-center gap-2 text-[11px]">
                                <i class="fa-solid fa-sparkles text-blue-200"></i>
                                <span class="text-blue-100">Welcome! Start by adding users and creating exams.</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {{-- Footer --}}
        <footer class="px-8 py-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-300">
            <span>© {{ date('Y') }} ExamSystem — Super Admin Console</span>
            <span class="font-mono">v2.0 · Real-time Engine Active</span>
        </footer>
    </main>
</div>
{{-- ===================== LOCKDOWN MODAL ===================== --}}
<div id="lockdown-modal" class="open:flex fixed inset-0 z-50 items-center justify-center" style="display:none;">
    <div class="absolute inset-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm" onclick="closeLockdown()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 border border-slate-200" style="box-shadow: 0 24px 48px rgba(15,23,42,0.25);">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center">
                <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
            </div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">Emergency Lockdown</h3>
                <p class="text-xs text-slate-400">This action cannot be undone automatically</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-6">
            This will <strong>immediately end all active exam sessions</strong> across the system. 
            All students currently testing will be disconnected. Are you sure?
        </p>
        <div class="flex gap-3">
            <button onclick="closeLockdown()" class="flex-1 py-2.5 px-4 bg-slate-100 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-200 transition-all">
                Cancel
            </button>
            <button onclick="executeLockdown()" id="lockdown-confirm-btn" class="flex-1 py-2.5 px-4 bg-rose-500 text-white rounded-xl font-semibold text-sm hover:bg-rose-600 transition-all"
                    style="box-shadow: 0 4px 12px rgba(244,63,94,0.35);">
                <i class="fa-solid fa-lock mr-1.5"></i> Confirm Lockdown
            </button>
        </div>
    </div>
</div>
{{-- ===================== TOAST CONTAINER ===================== --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2" style="pointer-events:none;"></div>
{{-- ===================== JAVASCRIPT — REAL-TIME ENGINE ===================== --}}
<script>
(function() {
    'use strict';
    const REFRESH_INTERVAL = 30; // seconds between API polls
    let countdown = REFRESH_INTERVAL;
    let pollingTimer = null;
    let clockTimer = null;
    // ── Live clock ──────────────────────────────────────────────
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent =
            now.toLocaleTimeString('en-US', { hour12: false });
    }
    clockTimer = setInterval(updateClock, 1000);
    updateClock();
    // ── Countdown + auto-refresh ────────────────────────────────
    function tickCountdown() {
        countdown--;
        const el = document.getElementById('refresh-countdown');
        if (el) el.textContent = countdown;
        if (countdown <= 0) {
            fetchLiveData();
            countdown = REFRESH_INTERVAL;
        }
    }
    pollingTimer = setInterval(tickCountdown, 1000);
    // ── Fetch real-time data from API ───────────────────────────
    function fetchLiveData() {
        const icon = document.getElementById('refresh-icon');
        if (icon) icon.classList.add('animate-spin');
        fetch('{{ route("superadmin.live-feed") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            updateMetrics(data);
            updateFeed(data.feed || []);
            updateInfraCard(data);
            if (icon) icon.classList.remove('animate-spin');
        })
        .catch(err => {
            console.error('Dashboard poll failed:', err);
            if (icon) icon.classList.remove('animate-spin');
        });
    }
    // ── Update metric cards ─────────────────────────────────────
    function updateMetrics(data) {
        animateValue('live-total-users', data.totalUsers ?? 0, true);
        animateValue('live-active-exams', data.activeExams ?? 0);
        animateValue('live-sessions-count', data.liveSessions ?? 0);
        
        const load = data.serverLoad ?? 0;
        const loadEl = document.getElementById('live-server-load');
        if (loadEl) loadEl.textContent = load + '%';
        const loadBar = document.getElementById('load-bar');
        if (loadBar) {
            loadBar.style.width = load + '%';
            loadBar.className = 'h-full rounded-full transition-all duration-700 ' +
                (load < 50 ? 'bg-emerald-400' : (load < 80 ? 'bg-amber-400' : 'bg-rose-400'));
        }
        const badge = document.getElementById('load-badge');
        if (badge) {
            if (load < 50) {
                badge.textContent = 'Healthy';
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full border text-emerald-600 bg-emerald-50 border-emerald-100';
            } else if (load < 80) {
                badge.textContent = 'Moderate';
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full border text-amber-600 bg-amber-50 border-amber-100';
            } else {
                badge.textContent = 'High';
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full border text-rose-600 bg-rose-50 border-rose-100';
            }
        }
    }
    function animateValue(elId, newVal, formatNumber) {
        const el = document.getElementById(elId);
        if (!el) return;
        const display = formatNumber ? Number(newVal).toLocaleString() : newVal;
        if (el.textContent !== String(display)) {
            el.classList.remove('count-animate');
            void el.offsetWidth; // trigger reflow
            el.textContent = display;
            el.classList.add('count-animate');
        }
    }
    // ── Update activity feed ────────────────────────────────────
    function updateFeed(feed) {
        const container = document.getElementById('async-dashboard-activity-feed');
        if (!container) return;
        const countBadge = document.getElementById('feed-count-badge');
        if (countBadge) countBadge.textContent = feed.length + ' events';
        if (feed.length === 0) {
            container.innerHTML = `
                <div class="flex flex-col items-center justify-center py-16 px-6 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fa-solid fa-inbox text-slate-300 text-2xl"></i>
                    </div>
                    <h5 class="text-sm font-bold text-slate-400 mb-1">No Activity Yet</h5>
                    <p class="text-xs text-slate-300 max-w-xs">
                        Your activity feed is empty. Actions like creating users, managing exams, and changing settings will appear here in real time.
                    </p>
                </div>`;
            return;
        }
        let html = '';
        feed.forEach((item, i) => {
            const action = (item.action || '').toLowerCase();
            let iconClass, iconColor, bgColor, badgeBg, badgeColor;
            if (action.includes('create')) {
                iconClass = 'fa-plus'; iconColor = 'text-emerald-500'; bgColor = 'bg-emerald-50';
                badgeBg = 'bg-emerald-50'; badgeColor = 'text-emerald-600';
            } else if (action.includes('delete') || action.includes('force')) {
                iconClass = 'fa-trash'; iconColor = 'text-rose-500'; bgColor = 'bg-rose-50';
                badgeBg = 'bg-rose-50'; badgeColor = 'text-rose-600';
            } else if (action.includes('update') || action.includes('toggle')) {
                iconClass = 'fa-pen'; iconColor = 'text-amber-500'; bgColor = 'bg-amber-50';
                badgeBg = 'bg-amber-50'; badgeColor = 'text-amber-600';
            } else if (action.includes('login') || action.includes('auth')) {
                iconClass = 'fa-right-to-bracket'; iconColor = 'text-blue-500'; bgColor = 'bg-blue-50';
                badgeBg = 'bg-blue-50'; badgeColor = 'text-blue-600';
            } else {
                iconClass = 'fa-bolt'; iconColor = 'text-slate-400'; bgColor = 'bg-slate-50';
                badgeBg = 'bg-slate-50'; badgeColor = 'text-slate-500';
            }
            const actionLabel = (item.action || 'unknown').replace(/\./g, '_').toUpperCase();
            html += `
            <div class="feed-row flex gap-3.5 p-3.5 hover:bg-slate-50 rounded-xl transition-all duration-150 cursor-default"
                 style="animation-delay: ${i * 0.06}s;">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0 ${bgColor}">
                    <i class="fa-solid ${iconClass} ${iconColor} text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-bold text-slate-800 truncate">${escapeHtml(item.operator || 'System')}</span>
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded font-mono uppercase ${badgeBg} ${badgeColor}">${actionLabel}</span>
                    </div>
                    <p class="text-[11px] text-slate-500 truncate">on ${escapeHtml(item.resource || '—')}</p>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-[10px] text-slate-300 flex items-center gap-1">
                            <i class="fa-solid fa-signal text-[8px]"></i> ${escapeHtml(item.ip || '—')}
                        </span>
                    </div>
                </div>
                <span class="text-[10px] text-slate-300 font-medium whitespace-nowrap flex-shrink-0 pt-0.5">
                    ${escapeHtml(item.created_at || '')}
                </span>
            </div>`;
        });
        container.innerHTML = html;
    }
    // ── Update infrastructure status card ───────────────────────
    function updateInfraCard(data) {
        const metrics = data.examsMetrics || {};
        setText('infra-total-exams', metrics.total ?? 0);
        setText('infra-stuck', data.stuckExams ?? 0);
        setText('infra-flag-rate', (metrics.flagRate ?? 0) + '%');
        setText('infra-backup', data.lastBackupHuman ?? 'No backups yet');
    }
    function setText(id, val) {
        const el = document.getElementById(id);
        if (el) el.textContent = val;
    }
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
    // ── Lockdown modal ──────────────────────────────────────────
    window.triggerLockdown = function() {
        document.getElementById('lockdown-modal').style.display = 'flex';
    };
    window.closeLockdown = function() {
        document.getElementById('lockdown-modal').style.display = 'none';
    };
    window.executeLockdown = function() {
        const btn = document.getElementById('lockdown-confirm-btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Processing...';
        btn.disabled = true;
        // Force-end all active exams
        fetch('{{ route("superadmin.live-feed") }}', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            const stuck = data.examsMetrics?.stuck || [];
            const activeIds = stuck.map(e => e.id);
            if (activeIds.length === 0) {
                showToast('No active exams to lock down.', 'info');
                closeLockdown();
                btn.innerHTML = '<i class="fa-solid fa-lock mr-1.5"></i> Confirm Lockdown';
                btn.disabled = false;
                return;
            }
            Promise.all(activeIds.map(id =>
                fetch(`/super-admin/exams/${id}/force-end`, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
            ))
            .then(() => {
                showToast(`Lockdown executed. ${activeIds.length} exam(s) terminated.`, 'success');
                closeLockdown();
                fetchLiveData();
                btn.innerHTML = '<i class="fa-solid fa-lock mr-1.5"></i> Confirm Lockdown';
                btn.disabled = false;
            });
        })
        .catch(() => {
            showToast('Lockdown failed. Check console.', 'error');
            btn.innerHTML = '<i class="fa-solid fa-lock mr-1.5"></i> Confirm Lockdown';
            btn.disabled = false;
        });
    };
    // ── Toast notifications ─────────────────────────────────────
    function showToast(message, type) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const colors = {
            success: 'bg-emerald-600',
            error: 'bg-rose-600',
            info: 'bg-blue-600',
        };
        const icons = {
            success: 'fa-check-circle',
            error: 'fa-exclamation-circle',
            info: 'fa-info-circle',
        };
        toast.className = `toast-enter flex items-center gap-2.5 px-4 py-3 rounded-xl text-white text-xs font-semibold ${colors[type] || colors.info}`;
        toast.style.pointerEvents = 'auto';
        toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
        toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i> ${escapeHtml(message)}`;
        container.appendChild(toast);
        requestAnimationFrame(() => {
            toast.classList.add('toast-visible');
        });
        setTimeout(() => {
            toast.classList.remove('toast-visible');
            toast.classList.add('toast-leave');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }
    // ── Initial data load on page ready ─────────────────────────
    // Data is already server-rendered via Blade — no skeleton needed.
    // The first API poll will happen after REFRESH_INTERVAL seconds.
})();
</script>
@include('partials.superadmin-notification-realtime')
</body>
</html>