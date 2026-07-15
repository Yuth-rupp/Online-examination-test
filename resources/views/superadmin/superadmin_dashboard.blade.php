<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Command Center | ExamSystem</title>

    {{-- Tailwind CSS v3 (reliable, processes all classes correctly) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- FontAwesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <script>
        // Extend Tailwind with custom config
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
        /* ---- Ping animation for live indicators ---- */
        @keyframes ping-slow {
            75%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .ping-slow { animation: ping-slow 2s cubic-bezier(0,0,0.2,1) infinite; }

        /* ---- Count-up slide animation ---- */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(10px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .count-animate { animation: countUp 0.4s ease-out forwards; }

        /* ---- Skeleton shimmer ---- */
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

        /* ---- Thin scrollbar ---- */
        .thin-scroll::-webkit-scrollbar { width: 4px; }
        .thin-scroll::-webkit-scrollbar-track { background: transparent; }
        .thin-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }

        /* ---- Feed stagger ---- */
        @keyframes fadeSlideIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .feed-row { opacity: 0; animation: fadeSlideIn 0.35s ease-out forwards; }

        /* ---- Modal backdrop ---- */
        #lockdown-modal { display: none; }
        #lockdown-modal.open { display: flex; }

        /* ---- Toast ---- */
        .toast-enter { opacity: 0; transform: translateY(12px); transition: opacity 0.3s, transform 0.3s; }
        .toast-visible { opacity: 1; transform: translateY(0); }
        .toast-leave { opacity: 0; transform: translateY(12px); }
    </style>
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
        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4" style="space-y: 2px;">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>

            {{-- Active link --}}
            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow: 0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-gauge-high text-xs text-white"></i>
                </span>
                <span>Dashboard</span>
            </a>

            {{-- Monitoring --}}
            <a href="{{ route('superadmin.monitoring.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-desktop text-xs text-slate-400"></i>
                </span>
                <span class="flex-1">Live Monitoring</span>
                <span class="text-[9px] bg-rose-100 text-rose-600 font-bold px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
            </a>

            {{-- Exams --}}
            <a href="{{ route('superadmin.exams.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-file-signature text-xs text-slate-400"></i>
                </span>
                <span>Exams Oversight</span>
            </a>

            {{-- Reports --}}
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

            {{-- User Management --}}
            <a href="{{ route('superadmin.admins.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-users text-xs text-slate-400"></i>
                </span>
                <span>User Management</span>
            </a>

            {{-- Audit Trails --}}
            <a href="{{ route('superadmin.audit-logs.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-xs text-slate-400"></i>
                </span>
                <span>Audit Trails</span>
            </a>

            {{-- Database --}}
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

            {{-- User Row --}}
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg, #3b82f6, #6366f1);">
                    <i class="fa-solid fa-user-astronaut text-white text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name ?? 'Super Admin' }}</p>
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

            {{-- Search --}}
            <div class="relative" style="width: 300px;">
                <i class="fa-solid fa-magnifying-glass absolute text-slate-300 text-xs"
                   style="left: 14px; top: 50%; transform: translateY(-50%); pointer-events: none;"></i>
                <input type="text"
                       placeholder="Search exams, users, logs..."
                       class="w-full pl-9 pr-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-medium text-slate-600 focus:outline-none focus:ring-2 focus:border-blue-400 transition-all"
                       style="focus:ring-color: rgba(59,130,246,0.25);">
            </div>

            <div class="flex items-center gap-3 ml-auto">
                {{-- Live Clock --}}
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock">--:--:--</span>
                </div>

                {{-- System Status --}}
                <div class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width: 8px; height: 8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    All Systems Operational
                </div>

                {{-- Notification Bell --}}
                <button class="relative w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-500 transition-all">
                    <i class="fa-regular fa-bell text-sm"></i>
                    <span class="absolute bg-rose-500 rounded-full ring-2 ring-white" style="width:8px;height:8px;top:6px;right:6px;"></span>
                </button>
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

                {{-- Refresh Countdown --}}
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-100 px-3 py-2 rounded-xl flex-shrink-0" style="box-shadow: 0 1px 4px rgba(148,163,184,0.08);">
                    <i id="refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                    <span>Auto-refresh in</span>
                    <span id="refresh-countdown" class="font-mono font-bold text-slate-700 w-3 text-center">3</span>
                    <span>s</span>
                </div>
            </div>

            {{-- ========== METRIC CARDS ========== --}}
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
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                            <i class="fa-solid fa-arrow-trend-up text-[8px]"></i> +2.4%
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Users</p>
                        <p id="live-total-users" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton inline-block" style="height:36px;width:80px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Registered accounts system-wide</p>
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
                        <span class="text-[10px] font-bold text-violet-600 bg-violet-50 border border-violet-100 px-2 py-0.5 rounded-full flex items-center gap-1">
                            <i class="fa-solid fa-bolt text-[8px]"></i> Running
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Active Exams</p>
                        <p id="live-active-exams" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton inline-block" style="height:36px;width:48px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Exams currently in progress</p>
                    </div>
                </div>

                {{-- Live Sessions --}}
                <div class="bg-white rounded-2xl border border-rose-100 p-5 flex flex-col gap-4 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06); background: linear-gradient(135deg, #fff 70%, rgba(255,228,230,0.3) 100%);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(244,63,94,0.12)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-rose-50 rounded-xl flex items-center justify-center relative">
                            <i class="fa-solid fa-tower-broadcast text-rose-500 text-sm"></i>
                            <span class="absolute rounded-full bg-rose-500 ring-2 ring-white animate-pulse"
                                  style="width:10px;height:10px;top:-2px;right:-2px;"></span>
                        </div>
                        <span class="text-[10px] font-black text-rose-600 bg-rose-50 border border-rose-200 px-2 py-0.5 rounded-full animate-pulse uppercase tracking-wider">● LIVE</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Live Sessions</p>
                        <p id="live-sessions-count" class="text-3xl font-black text-rose-600 leading-none tabular-nums">
                            <span class="skeleton inline-block" style="height:36px;width:56px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Candidates actively testing now</p>
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
                        <span id="load-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border text-slate-400 bg-slate-50 border-slate-100">Loading</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Server CPU Load</p>
                        <p id="live-server-load" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton inline-block" style="height:36px;width:56px;"></span>
                        </p>
                        {{-- Progress bar --}}
                        <div class="mt-3 rounded-full overflow-hidden bg-slate-100" style="height:6px;">
                            <div id="load-bar" class="h-full rounded-full bg-emerald-400 transition-all duration-700" style="width:0%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== BOTTOM ROW: FEED + ACTIONS ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Activity Feed --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 flex flex-col overflow-hidden"
                     style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);">

                    {{-- Feed Header --}}
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <span class="relative flex" style="width:10px;height:10px;">
                                <span class="ping-slow absolute inline-flex rounded-full bg-blue-400 opacity-75" style="width:100%;height:100%;"></span>
                                <span class="relative inline-flex rounded-full bg-blue-500" style="width:10px;height:10px;"></span>
                            </span>
                            <h4 class="font-bold text-sm text-slate-900">Live Activity Feed</h4>
                        </div>
                        <div class="flex items-center gap-3">
                            <span id="feed-count-badge" class="text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-0.5 rounded-full">loading...</span>
                            <a href="{{ route('superadmin.audit-logs.index') }}"
                               class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 hover:underline transition-all"
                               style="text-underline-offset: 2px;">
                               View Full Audit Trail →
                            </a>
                        </div>
                    </div>

                    {{-- Feed Body --}}
                    <div id="async-dashboard-activity-feed"
                         class="overflow-y-auto thin-scroll flex-1 px-3 py-2 divide-y divide-slate-50"
                         style="max-height: 420px;">
                        {{-- Skeleton rows shown on first load --}}
                        <div class="flex gap-3.5 p-3.5">
                            <div class="skeleton rounded-xl flex-shrink-0" style="width:36px;height:36px;"></div>
                            <div class="flex-1 py-1" style="display:flex;flex-direction:column;gap:8px;">
                                <div class="skeleton rounded" style="height:12px;width:130px;"></div>
                                <div class="skeleton rounded" style="height:10px;width:100%;"></div>
                                <div class="skeleton rounded" style="height:8px;width:80px;"></div>
                            </div>
                        </div>
                        <div class="flex gap-3.5 p-3.5">
                            <div class="skeleton rounded-xl flex-shrink-0" style="width:36px;height:36px;"></div>
                            <div class="flex-1 py-1" style="display:flex;flex-direction:column;gap:8px;">
                                <div class="skeleton rounded" style="height:12px;width:160px;"></div>
                                <div class="skeleton rounded" style="height:10px;width:75%;"></div>
                                <div class="skeleton rounded" style="height:8px;width:64px;"></div>
                            </div>
                        </div>
                        <div class="flex gap-3.5 p-3.5">
                            <div class="skeleton rounded-xl flex-shrink-0" style="width:36px;height:36px;"></div>
                            <div class="flex-1 py-1" style="display:flex;flex-direction:column;gap:8px;">
                                <div class="skeleton rounded" style="height:12px;width:110px;"></div>
                                <div class="skeleton rounded" style="height:10px;width:85%;"></div>
                                <div class="skeleton rounded" style="height:8px;width:90px;"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="flex flex-col gap-5">

                    {{-- Quick Actions Card --}}
                    <div class="bg-white rounded-2xl border border-slate-100 p-5"
                         style="box-shadow: 0 1px 4px rgba(148,163,184,0.06);">
                        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest mb-4">Quick Actions</p>

                        {{-- Global Settings --}}
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

                            {{-- Database Backup --}}
                            <button onclick="window.location.href='{{ route('superadmin.backups.index') }}'"
                                    class="w-full p-3.5 rounded-xl flex items-center gap-3.5 text-left cursor-pointer border border-slate-200 bg-white text-slate-700 font-semibold transition-all duration-200 hover:bg-slate-50 hover:border-slate-300 group">
                                <div class="w-8 h-8 rounded-lg bg-violet-50 flex items-center justify-center flex-shrink-0">
                                    <i class="fa-solid fa-database text-violet-500 text-xs"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-xs font-bold leading-tight">Database Backup</p>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Trigger a manual backup snapshot</p>
                                </div>
                                <span class="text-[10px] text-slate-400 font-mono flex-shrink-0">2h ago</span>
                            </button>

                            {{-- Emergency Lockdown --}}
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
                        </div>
                    </div>

                    {{-- Infrastructure Guard Card --}}
                    <div class="rounded-2xl text-white p-5 flex flex-col gap-4 relative overflow-hidden"
                         style="background: linear-gradient(135deg, #2563eb, #4f46e5); box-shadow: 0 8px 32px rgba(59,130,246,0.35);">

                        {{-- Top shimmer line --}}
                        <div class="absolute top-0 left-0 right-0" style="height:1px;background:linear-gradient(90deg,transparent,rgba(255,255,255,0.25),transparent);"></div>

                        {{-- BG icon --}}
                        <div class="absolute pointer-events-none" style="right:-20px;bottom:-20px;opacity:0.06;font-size:140px;line-height:1;">
                            <i class="fa-solid fa-microchip"></i>
                        </div>

                        <div>
                            <span class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border"
                                  style="background:rgba(255,255,255,0.12);border-color:rgba(255,255,255,0.15);color:rgba(191,219,254,1);">
                                Infrastructure Guard
                            </span>
                            <h4 class="text-base font-bold mt-3 leading-snug">System Health<br>Optimization</h4>
                            <p class="text-[11px] font-medium mt-1.5 leading-relaxed" style="color:rgba(191,219,254,0.9);">
                                Auto-telemetry active. Cache is clean. SQL index performance is nominal.
                            </p>
                        </div>

                        {{-- Efficiency Bar --}}
                        <div>
                            <div class="flex items-center justify-between mb-1.5" style="font-size:10px;color:rgba(191,219,254,0.9);">
                                <span>Query Pool Efficiency</span>
                                <span class="font-bold text-white">94%</span>
                            </div>
                            <div class="rounded-full overflow-hidden" style="height:6px;background:rgba(255,255,255,0.12);">
                                <div class="h-full rounded-full" style="width:94%;background:linear-gradient(90deg,#93c5fd,#67e8f9);"></div>
                            </div>
                        </div>

                        <button class="w-full py-2.5 rounded-xl font-bold text-xs text-center transition-all duration-200"
                                style="background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.15);"
                                onmouseenter="this.style.background='rgba(255,255,255,0.22)'"
                                onmouseleave="this.style.background='rgba(255,255,255,0.12)'">
                            <i class="fa-solid fa-bolt mr-1.5" style="color:#fde047;"></i>
                            Tune SQL Resource Pools
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>


{{-- ===================== LOCKDOWN MODAL ===================== --}}
<div id="lockdown-modal"
     class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.55);backdrop-filter:blur(8px);">
    <div onclick="event.stopPropagation()"
         class="relative bg-white rounded-2xl max-w-md w-full p-7 border border-rose-100"
         style="box-shadow: 0 24px 64px rgba(15,23,42,0.25);">
        <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-lock text-rose-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-extrabold text-slate-900 text-center">Confirm Emergency Lockdown</h3>
        <p class="text-sm text-slate-500 text-center mt-2 leading-relaxed">
            This will <strong class="text-rose-600">immediately isolate</strong> all active exam sessions and block candidate access. This action is logged and reversible from the Audit Trail.
        </p>
        <div class="mt-6 flex gap-3">
            <button onclick="closeLockdown()"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button onclick="confirmLockdown()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:#e11d48;box-shadow:0 4px 14px rgba(225,29,72,0.30);"
                    onmouseenter="this.style.background='#be123c'"
                    onmouseleave="this.style.background='#e11d48'">
                <i class="fa-solid fa-lock mr-1.5"></i> Enforce Lockdown
            </button>
        </div>
    </div>
</div>


{{-- ===================== SCRIPTS ===================== --}}
<script>
// ============================================================
//  LIVE CLOCK
// ============================================================
function updateClock() {
    document.getElementById('live-clock').textContent =
        new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
updateClock();
setInterval(updateClock, 1000);


// ============================================================
//  AUTO-REFRESH COUNTDOWN  (3s interval)
// ============================================================
let countdown = 3;
const countdownEl = document.getElementById('refresh-countdown');
const refreshIcon  = document.getElementById('refresh-icon');

setInterval(() => {
    countdown--;
    if (countdown <= 0) {
        countdown = 3;
        refreshIcon.classList.add('animate-spin');
        setTimeout(() => refreshIcon.classList.remove('animate-spin'), 700);
        syncDashboard();
    }
    countdownEl.textContent = countdown;
}, 1000);


// ============================================================
//  METRIC HELPERS
// ============================================================
function setMetric(id, value) {
    const el = document.getElementById(id);
    el.innerHTML = '';
    el.textContent = value;
    el.classList.remove('count-animate');
    void el.offsetWidth; // reflow trick to restart animation
    el.classList.add('count-animate');
}

function updateLoadBar(pct) {
    const bar   = document.getElementById('load-bar');
    const badge = document.getElementById('load-badge');
    bar.style.width = pct + '%';

    if (pct < 40) {
        bar.style.background   = '#34d399';
        badge.textContent      = 'Healthy';
        badge.style.color      = '#059669';
        badge.style.background = '#ecfdf5';
        badge.style.borderColor= '#a7f3d0';
    } else if (pct < 75) {
        bar.style.background   = '#fbbf24';
        badge.textContent      = 'Moderate';
        badge.style.color      = '#d97706';
        badge.style.background = '#fffbeb';
        badge.style.borderColor= '#fde68a';
    } else {
        bar.style.background   = '#f43f5e';
        badge.textContent      = 'High Load';
        badge.style.color      = '#e11d48';
        badge.style.background = '#fff1f2';
        badge.style.borderColor= '#fecdd3';
    }
}


// ============================================================
//  FEED: ACTION-TYPE → ICON + COLOR
// ============================================================
const ACTION_META = {
    'CREATE': { icon: 'fa-plus',              bg: '#d1fae5', color: '#059669' },
    'UPDATE': { icon: 'fa-pen',               bg: '#dbeafe', color: '#2563eb' },
    'DELETE': { icon: 'fa-trash',             bg: '#ffe4e6', color: '#e11d48' },
    'LOGIN':  { icon: 'fa-right-to-bracket',  bg: '#ede9fe', color: '#7c3aed' },
    'EXPORT': { icon: 'fa-download',          bg: '#fef3c7', color: '#d97706' },
    'BACKUP': { icon: 'fa-database',          bg: '#e0e7ff', color: '#4338ca' },
};

function getActionMeta(action) {
    const key = (action || '').split('_')[0].toUpperCase();
    return ACTION_META[key] || { icon: 'fa-bolt', bg: '#f1f5f9', color: '#64748b' };
}


// ============================================================
//  MOCK DATA  ← Replace these two functions with real fetch() calls
//  Example:
//    fetch("{{ route('admin.dashboard.api') }}").then(r=>r.json()).then(updateMetrics)
//    fetch("{{ route('superadmin.telemetry.livefeed') }}").then(r=>r.json()).then(renderFeed)
// ============================================================
function mockMetrics() {
    return {
        totalUsers:  8214 + Math.floor(Math.random() * 8),
        activeExams: 7,
        cpuUsage:    30 + Math.floor(Math.random() * 15)
    };
}

function mockFeed() {
    return { feed: [
        { operator: 'Sarah Mitchell',  action: 'CREATE_EXAM',    resource: 'Physics Final — Module 3', ip: '192.168.1.10',  created_at: 'Just now'  },
        { operator: 'James Okafor',    action: 'UPDATE_USER',    resource: 'Candidate #3892',          ip: '10.0.0.5',      created_at: '4m ago'    },
        { operator: 'Root: Meow Meow', action: 'DELETE_SESSION', resource: 'Session #44',              ip: '127.0.0.1',     created_at: '9m ago'    },
        { operator: 'Lena Torres',     action: 'LOGIN',          resource: 'Admin Portal',             ip: '172.16.0.22',   created_at: '16m ago'   },
        { operator: 'System Daemon',   action: 'EXPORT_LOGS',   resource: 'Audit Archive Q2-2026',    ip: 'sys-internal',  created_at: '23m ago'   },
        { operator: 'Backup Service',  action: 'BACKUP_DB',     resource: 'Primary Database',         ip: 'sys-internal',  created_at: '1h ago'    },
    ]};
}


// ============================================================
//  MAIN SYNC
// ============================================================
function syncDashboard() {
    const data = mockMetrics(); // ← swap with fetch()
    setMetric('live-total-users',    data.totalUsers.toLocaleString());
    setMetric('live-active-exams',   data.activeExams);
    setMetric('live-sessions-count', (data.activeExams * 12).toLocaleString());
    setMetric('live-server-load',    data.cpuUsage + '%');
    updateLoadBar(data.cpuUsage);

    renderFeed(mockFeed()); // ← swap with fetch()
}


// ============================================================
//  RENDER ACTIVITY FEED
// ============================================================
function renderFeed(data) {
    const container = document.getElementById('async-dashboard-activity-feed');
    const badge     = document.getElementById('feed-count-badge');

    if (!data.feed || data.feed.length === 0) {
        badge.textContent = '0 events';
        container.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:56px 0;color:#cbd5e1;">
                <i class="fa-solid fa-satellite-dish" style="font-size:36px;margin-bottom:12px;"></i>
                <p style="font-size:13px;font-weight:600;color:#94a3b8;">No recent activity</p>
                <p style="font-size:11px;color:#cbd5e1;margin-top:4px;">Infrastructure events will appear here in real time.</p>
            </div>`;
        return;
    }

    badge.textContent = data.feed.length + ' events';

    container.innerHTML = data.feed.map((log, i) => {
        const meta = getActionMeta(log.action);
        return `
        <div class="feed-row" style="animation-delay:${i * 65}ms; display:flex; gap:14px; padding:14px 12px; border-radius:12px; transition:background 0.15s;"
             onmouseenter="this.style.background='#f8fafc'"
             onmouseleave="this.style.background='transparent'">
            <div style="width:36px;height:36px;border-radius:10px;background:${meta.bg};color:${meta.color};display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;font-size:12px;">
                <i class="fa-solid ${meta.icon}"></i>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;">
                    <p style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${log.operator}</p>
                    <span style="font-size:10px;color:#94a3b8;font-weight:500;white-space:nowrap;flex-shrink:0;">${log.created_at}</span>
                </div>
                <p style="font-size:11px;color:#64748b;margin-top:3px;line-height:1.5;">
                    Executed
                    <span style="font-family:'JetBrains Mono',monospace;font-size:10px;font-weight:600;color:#2563eb;background:#eff6ff;padding:1px 6px;border-radius:4px;">${log.action}</span>
                    on <span style="font-weight:600;color:#1e293b;">${log.resource}</span>
                </p>
                <div style="display:flex;align-items:center;gap:6px;margin-top:6px;">
                    <i class="fa-solid fa-network-wired" style="font-size:9px;color:#cbd5e1;"></i>
                    <span style="font-family:'JetBrains Mono',monospace;font-size:9px;color:#94a3b8;background:#f8fafc;border:1px solid #f1f5f9;padding:1px 6px;border-radius:4px;">${log.ip}</span>
                </div>
            </div>
        </div>`;
    }).join('');
}


// ============================================================
//  LOCKDOWN MODAL
// ============================================================
function triggerLockdown() {
    document.getElementById('lockdown-modal').style.display = 'flex';
}
function closeLockdown() {
    document.getElementById('lockdown-modal').style.display = 'none';
}
function confirmLockdown() {
    closeLockdown();

    // PRODUCTION: Call your lockdown API here
    // fetch('/api/superadmin/lockdown', { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content } })

    // Show toast
    const toast = document.createElement('div');
    toast.className = 'toast-enter';
    toast.style.cssText = `
        position:fixed;bottom:24px;right:24px;z-index:9999;
        display:flex;align-items:center;gap:12px;
        background:#0f172a;color:#fff;
        padding:14px 20px;border-radius:16px;
        font-size:13px;font-weight:600;font-family:'Inter',sans-serif;
        border:1px solid #1e293b;
        box-shadow:0 16px 48px rgba(0,0,0,0.35);
    `;
    toast.innerHTML = `<i class="fa-solid fa-lock" style="color:#f43f5e;"></i><span>Lockdown enforced. All exam sessions have been isolated.</span>`;
    document.body.appendChild(toast);
    requestAnimationFrame(() => toast.classList.add('toast-visible'));
    setTimeout(() => {
        toast.classList.add('toast-leave');
        setTimeout(() => toast.remove(), 400);
    }, 4500);
}


// ============================================================
//  INIT
// ============================================================
syncDashboard();
</script>

</body>
</html>