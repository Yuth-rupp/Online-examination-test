<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Security Audit Center | {{ $platformName }}</title>
    <meta name="description" content="Real-time security audit center and infrastructure monitoring for {{ $platformName }} administrators.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-flash dark mode (matches the dashboard) -->
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        /* ── Shared admin brand + nav (matches Dashboard/User Management) ── */
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-topbar        { background: linear-gradient(120deg, #1d4ed8 0%, #2563eb 45%, #1e3a8a 100%); }
        .admin-topbar-dark   { background: linear-gradient(120deg, #0b1220 0%, #111f3d 55%, #1e3a8a 100%); }
        .admin-nav-active { background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
        .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }

        .dark-surface { background:#0f172a; }
        .dark-card { --card-bg:#1e293b; --card-br:#334155; --row-hover:#1e293b; }

        /* ── Page background ── */
        body { background: #f8fafc; }

        /* ── Cards ── */
        .metric-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }
        .metric-card:hover {
            box-shadow: 0 4px 20px rgba(37,99,235,0.09);
            border-color: #bfdbfe;
            transform: translateY(-1px);
        }

        /* ── Progress bars ── */
        .progress-bar { height: 3px; border-radius: 999px; background: #f1f5f9; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 999px; transition: width 1s ease; }

        /* ── Pulse ── */
        @keyframes outerPulse {
            0%,100% { transform: scale(1); opacity: 1; }
            50%      { transform: scale(1.7); opacity: 0; }
        }
        .pulse-dot { animation: outerPulse 1.8s ease-in-out infinite; }

        /* ── Threat badge blink ── */
        @keyframes threatBlink { 0%,100%{opacity:1} 50%{opacity:0.45} }
        .threat-blink { animation: threatBlink 1.4s ease-in-out infinite; }

        /* ── Timeline entry animation ── */
        .timeline-entry {
            animation: slideIn 0.4s cubic-bezier(0.16,1,0.3,1) forwards;
        }
        @keyframes slideIn {
            from { opacity:0; transform:translateX(-16px); }
            to   { opacity:1; transform:translateX(0); }
        }

        /* ── Log card ── */
        .log-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 1px 4px rgba(0,0,0,0.03);
            transition: all 0.18s ease;
        }
        .log-card:hover {
            box-shadow: 0 4px 16px rgba(0,0,0,0.07);
            border-color: #cbd5e1;
        }

        /* ── Glow rings (lighter for white theme) ── */
        .glow-warning { box-shadow: 0 0 0 3px rgba(239,68,68,0.12), 0 2px 8px rgba(239,68,68,0.2); }
        .glow-auth    { box-shadow: 0 0 0 3px rgba(59,130,246,0.12), 0 2px 8px rgba(59,130,246,0.2); }
        .glow-data    { box-shadow: 0 0 0 3px rgba(16,185,129,0.12), 0 2px 8px rgba(16,185,129,0.2); }
        .glow-config  { box-shadow: 0 0 0 3px rgba(168,85,247,0.12), 0 2px 8px rgba(168,85,247,0.2); }
        .glow-info    { box-shadow: 0 0 0 3px rgba(234,179,8,0.1),   0 2px 8px rgba(234,179,8,0.15); }

        /* ── Payload expand ── */
        .payload-content { max-height: 0; overflow: hidden; transition: max-height 0.32s ease; }
        .payload-content.open { max-height: 220px; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* ── Filter buttons ── */
        .filter-btn {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            color: #64748b;
            transition: all 0.18s ease;
        }
        .filter-btn:hover { background: #f1f5f9; border-color: #cbd5e1; color: #334155; }
        .filter-active {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            border-color: #3b82f6 !important;
            color: #ffffff !important;
            box-shadow: 0 3px 10px rgba(37,99,235,0.3);
        }

        /* ── Timeline backbone ── */
        .timeline-line {
            background: linear-gradient(180deg, #bfdbfe 0%, #e2e8f0 100%);
        }

        /* ── Score ring ── */
        .score-ring-wrap {
            width: 100px; height: 100px;
            border-radius: 50%;
            background: conic-gradient(#22c55e 0% 87%, #e2e8f0 87% 100%);
            display: flex; align-items: center; justify-content: center;
            position: relative;
        }
        .score-ring-wrap::before {
            content: '';
            position: absolute; inset: 8px;
            border-radius: 50%;
            background: #ffffff;
        }
        .score-ring-wrap span {
            position: relative; z-index: 1;
            font-size: 22px; font-weight: 900;
            color: #16a34a;
        }

        /* ── Modal ── */
        .modal-overlay { background: rgba(15,23,42,0.3); backdrop-filter: blur(4px); }
        .modal-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 20px 60px rgba(0,0,0,0.12);
        }

        /* ── Badges (light) ── */
        .badge-warning { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .badge-auth    { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
        .badge-data    { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .badge-config  { background:#f5f3ff; color:#6d28d9; border:1px solid #ddd6fe; }
        .badge-info    { background:#fefce8; color:#92400e; border:1px solid #fde68a; }

        /* ── Audit canvas ── */
        .audit-canvas {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 6px 24px rgba(0,0,0,0.03);
        }
    </style>
    @include('partials.notification-styles')
</head>
<body class="antialiased transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="darkMode ? 'dark-surface text-slate-100' : 'bg-slate-50 text-slate-800'">
<div class="flex min-h-screen">

    @include('partials.admin-sidebar')

    <!-- ══════════════ MAIN CONTENT ══════════════ -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <!-- STICKY TOPBAR — professional admin-blue gradient bar -->
        <header class="flex items-center justify-between mb-0 flex-wrap gap-4 px-7 py-4 border-b sticky top-0 z-20 backdrop-blur-xl transition-colors duration-300"
                :class="darkMode ? 'admin-topbar-dark border-blue-950/40' : 'admin-topbar border-blue-900/20'"
                style="box-shadow:0 4px 24px rgba(29,78,216,0.28)">
            <div class="flex items-center gap-3 flex-wrap">
                <!-- Status pill -->
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold text-emerald-700 border" style="background:#f0fdf4;border-color:#bbf7d0;">
                    <span class="relative flex items-center justify-center w-2 h-2">
                        <span class="pulse-dot absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    System Status: <strong id="system-status" class="text-emerald-600 ml-0.5">Secure</strong>
                </span>

                <!-- Threat level pill -->
                <span id="threat-level-pill" class="inline-flex items-center gap-2 px-3 py-2 rounded-full text-xs font-bold border" style="background:#f0fdf4;border-color:#bbf7d0;color:#15803d;">
                    <i id="threat-icon" class="fa-solid fa-shield-check text-[10px]"></i>
                    Threat: <span id="threat-level-label" class="ml-0.5">NONE</span>
                </span>
            </div>

            <div class="flex items-center gap-3">
                @include('partials.admin-darkmode-toggle')

                @include('partials.admin-notification-bell')

                <!-- Last refresh -->
                <div class="text-xs font-mono flex items-center gap-1.5 border px-3 py-1.5 rounded-xl text-blue-50 bg-white/10 border-white/20">
                    <i class="fa-solid fa-rotate text-[10px] text-blue-200"></i>
                    <span id="last-refresh">--:--:--</span>
                </div>

                <!-- Admin -->
                <div class="flex items-center gap-3 pl-3 border-l border-white/20">
                    <div class="text-right hidden sm:block">
                        <h4 class="text-sm font-semibold leading-tight text-white">{{ Auth::user()->full_name ?? 'Admin User' }}</h4>
                        <span class="text-xs text-blue-200">Administrator</span>
                    </div>
                    @if(Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->full_name }}"
                             class="w-9 h-9 rounded-xl object-cover ring-2 ring-white/40" style="box-shadow:0 3px 10px rgba(0,0,0,0.25)">
                    @else
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-blue-700 font-bold text-sm bg-white ring-2 ring-white/40" style="box-shadow:0 3px 10px rgba(0,0,0,0.25)">{{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'AD' }}</div>
                    @endif
                </div>
            </div>
        </header>

        <!-- SCROLLABLE PAGE BODY -->
        <div class="p-7">

        <!-- PAGE TITLE -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </span>
                Security Audit Center
            </h2>
            <div class="flex items-center gap-3 flex-wrap mt-1">
                <p class="text-sm text-slate-400 font-mono">Real-time cryptographically signed audit trail &middot; Auto-refresh every 3s</p>
                @if(!empty($isDepartmentAdmin) && !empty($departmentName))
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold text-blue-700" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-building-columns text-[10px]"></i> {{ $departmentName }}
                </span>
                @endif
            </div>
        </div>

        <!-- ── ROW 1: METRIC CARDS ── -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-5">

            <!-- Total Users -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Users</p>
                        <h3 id="realtime-users" class="text-3xl font-black text-slate-900">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div class="progress-fill bg-blue-400" style="width:72%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Registered system accounts</p>
            </div>

            <!-- Active Exams -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Active Exams</p>
                        <h3 id="realtime-exams" class="text-3xl font-black text-slate-900">{{ $activeExams }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="fa-solid fa-file-invoice text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div class="progress-fill bg-emerald-400" style="width:40%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Ongoing exam sessions</p>
            </div>

            <!-- System Load -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">System Load</p>
                        <h3 id="realtime-load" class="text-3xl font-black text-slate-900">{{ number_format($cpuUsage, 1) }}%</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f5f3ff;border:1px solid #ddd6fe">
                        <i class="fa-solid fa-microchip text-violet-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div id="load-bar" class="progress-fill bg-violet-400" style="width:{{ $cpuUsage }}%"></div></div>
                <p id="load-status" class="text-[11px] text-slate-400 mt-2">CPU utilization normal</p>
            </div>

            <!-- Warning Events -->
            <div class="metric-card rounded-2xl p-5" style="border-color:#fecdd3;">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Warning Events</p>
                        <h3 id="warning-count" class="text-3xl font-black text-red-500">0</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#fff1f2;border:1px solid #fecdd3">
                        <i class="fa-solid fa-triangle-exclamation text-red-500 text-sm threat-blink"></i>
                    </div>
                </div>
                <div class="progress-bar"><div id="warn-bar" class="progress-fill bg-red-400" style="width:0%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Critical threshold events</p>
            </div>
        </div>

        <!-- ── ROW 2: SECURITY SCORE ── -->
        <div class="metric-card rounded-2xl p-6 mb-5">
            <div class="flex flex-col sm:flex-row sm:items-center gap-6">

                <!-- Score ring -->
                <div class="flex items-center gap-5 shrink-0">
                    <div class="score-ring-wrap">
                        <span>87</span>
                    </div>
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Overall Security Score</p>
                        <p class="text-2xl font-black text-slate-900">87 <span class="text-base font-semibold text-slate-400">/ 100</span></p>
                        <span class="inline-flex items-center gap-1.5 mt-1 text-[11px] font-bold px-2.5 py-1 rounded-full" style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d">
                            <i class="fa-solid fa-arrow-trend-up text-[9px]"></i> Good Standing
                        </span>
                    </div>
                </div>

                <!-- Divider -->
                <div class="hidden sm:block w-px self-stretch bg-slate-100"></div>

                <!-- Category bars -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#f0fdf4;border:1px solid #bbf7d0">
                                    <i class="fa-solid fa-fingerprint text-emerald-600" style="font-size:10px"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-600">Authentication</span>
                            </div>
                            <span class="text-xs font-bold text-emerald-600">95%</span>
                        </div>
                        <div class="progress-bar" style="height:6px">
                            <div class="progress-fill bg-emerald-500" style="width:95%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5">Strong auth controls</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#eff6ff;border:1px solid #bfdbfe">
                                    <i class="fa-solid fa-lock text-blue-600" style="font-size:10px"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-600">Data Integrity</span>
                            </div>
                            <span class="text-xs font-bold text-blue-600">82%</span>
                        </div>
                        <div class="progress-bar" style="height:6px">
                            <div class="progress-fill bg-blue-500" style="width:82%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5">Tamper-evident logs</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#fffbeb;border:1px solid #fde68a">
                                    <i class="fa-solid fa-shield-halved text-amber-600" style="font-size:10px"></i>
                                </div>
                                <span class="text-xs font-semibold text-slate-600">Threat Coverage</span>
                            </div>
                            <span class="text-xs font-bold text-amber-600">76%</span>
                        </div>
                        <div class="progress-bar" style="height:6px">
                            <div class="progress-fill bg-amber-400" style="width:76%"></div>
                        </div>
                        <p class="text-[10px] text-slate-400 mt-1.5">Active threat monitoring</p>
                    </div>
                </div>

                <!-- Divider -->
                <div class="hidden sm:block w-px self-stretch bg-slate-100"></div>

                <!-- Quick stats -->
                <div class="shrink-0 flex flex-col gap-3">
                    <div class="text-center p-3 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Last Scan</p>
                        <p class="text-xs font-bold text-slate-700 font-mono">Just now</p>
                    </div>
                    <div class="text-center p-3 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0">
                        <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-0.5">Uptime</p>
                        <p class="text-xs font-bold text-slate-700 font-mono">99.9%</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── FILTER BAR ── -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 mb-5 flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2 pr-4 border-r border-slate-200 text-xs font-semibold uppercase tracking-widest text-slate-400 shrink-0">
                <i class="fa-solid fa-sliders text-[10px]"></i> Filter
            </div>
            <div class="flex flex-wrap gap-2">
                <button onclick="switchSecurityFilter('all')" id="filter-all"
                    class="filter-btn filter-active px-4 py-1.5 rounded-lg text-xs font-semibold transition-all">
                    <i class="fa-solid fa-layer-group mr-1.5"></i>All Activities
                </button>
                <button onclick="switchSecurityFilter('account')" id="filter-account"
                    class="filter-btn px-4 py-1.5 rounded-lg text-xs font-semibold">
                    <i class="fa-solid fa-user-shield mr-1.5 text-blue-500"></i>Logins & Accounts
                </button>
                <button onclick="switchSecurityFilter('exam')" id="filter-exam"
                    class="filter-btn px-4 py-1.5 rounded-lg text-xs font-semibold">
                    <i class="fa-solid fa-database mr-1.5 text-emerald-500"></i>Exam Activity
                </button>
                <button onclick="switchSecurityFilter('flag')" id="filter-flag"
                    class="filter-btn px-4 py-1.5 rounded-lg text-xs font-semibold">
                    <i class="fa-solid fa-flag mr-1.5 text-red-500"></i>Proctor Flags
                </button>
            </div>

            <div class="ml-auto flex items-center gap-2.5">
                <span class="text-xs font-mono px-3 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-400">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block mr-1.5 align-middle"></span>
                    <span id="event-counter">0</span> events
                </span>
                <button onclick="clearTimelineDOM()"
                    class="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-lg transition-all"
                    style="background:#fff1f2;border:1px solid #fecdd3;color:#be123c;"
                    onmouseover="this.style.background='#ffe4e6'"
                    onmouseout="this.style.background='#fff1f2'">
                    <i class="fa-solid fa-trash-can text-[10px]"></i> Clear
                </button>
            </div>
        </div>

        <!-- ── AUDIT TIMELINE CANVAS ── -->
        <div class="audit-canvas rounded-2xl p-7">

            <!-- Canvas header -->
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-8">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block" style="box-shadow:0 0 0 3px rgba(34,197,94,0.2)"></span>
                        <h3 class="text-base font-bold text-slate-900">Live Infrastructure Activity Log</h3>
                    </div>
                    <p class="text-xs font-mono text-slate-400">Cryptographically signed &middot; Tamper-evident &middot; Active node telemetry</p>
                </div>

                <!-- Severity Legend -->
                <div class="flex flex-wrap gap-2 shrink-0">
                    <span class="badge-warning inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                        <i class="fa-solid fa-triangle-exclamation" style="font-size:8px"></i>WARNING
                    </span>
                    <span class="badge-auth inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                        <i class="fa-solid fa-user-shield" style="font-size:8px"></i>AUTH
                    </span>
                    <span class="badge-data inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                        <i class="fa-solid fa-database" style="font-size:8px"></i>DATA
                    </span>
                    <span class="badge-config inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                        <i class="fa-solid fa-wrench" style="font-size:8px"></i>CONFIG
                    </span>
                    <span class="badge-info inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                        <i class="fa-solid fa-bolt" style="font-size:8px"></i>INFO
                    </span>
                </div>
            </div>

            <!-- Timeline -->
            <div class="relative min-h-[400px] pl-1">
                <div class="absolute left-[27px] top-0 bottom-0 w-px timeline-line z-0"></div>
                <div id="realtime-timeline-stream" class="space-y-4 relative z-10">
                    <!-- JS populated -->
                </div>
            </div>

            <!-- Bottom cap -->
            <div class="mt-10 pt-5 border-t border-slate-100 flex items-center gap-4">
                <div class="flex-1 h-px bg-slate-100"></div>
                <span class="text-[10px] font-mono uppercase tracking-widest px-4 py-1.5 rounded-full bg-slate-50 border border-slate-200 text-slate-400">
                    &#9632; end of audit log
                </span>
                <div class="flex-1 h-px bg-slate-100"></div>
            </div>
        </div>

        </div><!-- /page body -->
    </main>
</div>

<!-- ══════════════ PROFILE MODAL ══════════════ -->
<div id="identity-profile-modal" class="modal-overlay fixed inset-0 hidden items-center justify-center z-50">
    <div class="modal-card rounded-2xl max-w-sm w-full mx-4 overflow-hidden">
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between" style="background:#f8fafc">
            <div class="flex items-center gap-2.5 text-blue-600">
                <i class="fa-solid fa-id-badge text-base"></i>
                <h3 class="font-bold text-slate-900 text-sm">Node Profile</h3>
            </div>
            <button onclick="closeProfileModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="flex flex-col items-center text-center gap-4">
                <div id="modal-initials-badge" class="w-16 h-16 rounded-2xl flex items-center justify-center font-black text-xl uppercase" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8">--</div>
                <div>
                    <h4 id="modal-user-fullname" class="font-bold text-slate-900 text-lg">--</h4>
                    <span id="modal-user-email" class="text-xs font-mono text-slate-400">--</span>
                </div>
                <div class="w-full grid grid-cols-2 gap-3">
                    <div class="p-3 rounded-xl text-left bg-slate-50 border border-slate-100">
                        <span class="text-[10px] uppercase font-bold tracking-widest block mb-1 text-slate-400">Access Scope</span>
                        <span id="modal-user-role" class="text-xs font-extrabold text-slate-700 uppercase tracking-wide">--</span>
                    </div>
                    <div class="p-3 rounded-xl text-left bg-slate-50 border border-slate-100">
                        <span class="text-[10px] uppercase font-bold tracking-widest block mb-1 text-slate-400">Status</span>
                        <span id="modal-user-status" class="text-xs font-bold">--</span>
                    </div>
                </div>
                <div class="w-full p-3 rounded-xl text-left bg-slate-50 border border-slate-100">
                    <p class="text-[10px] uppercase font-bold tracking-widest mb-2 text-slate-400">Session Metadata</p>
                    <div class="flex flex-wrap gap-3 text-[10px] font-mono text-slate-500">
                        <span><i class="fa-solid fa-network-wired mr-1 text-blue-400"></i>IP: 127.0.0.1</span>
                        <span><i class="fa-solid fa-globe mr-1 text-emerald-400"></i>Webkit/Chrome</span>
                        <span><i class="fa-solid fa-clock mr-1 text-violet-400"></i>Just now</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="px-6 pb-6">
            <button onclick="closeProfileModal()" class="w-full py-2.5 rounded-xl text-sm font-bold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-all border border-slate-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- ══════════════ JAVASCRIPT ══════════════ -->
<script>
    if (window.lucide) lucide.createIcons();

    let recordedEventIds = new Set();
    let currentLogsMemory  = {};
    const streamContainer  = document.getElementById('realtime-timeline-stream');
    let activeFilterTag    = 'all';
    let totalWarnings      = 0;

    /* ── Event type configs — keyed by real category, not the raw action string ── */
    const EVENT_CFG = {
        account: {
            verb: 'updated account access for',
            icon: `<i class="fa-solid fa-user-shield" style="font-size:9px"></i>`,
            iconBg: 'linear-gradient(135deg,#2563eb,#1d4ed8)',
            glowCls: 'glow-auth',
            badgeCls: 'badge-auth',
            badgeLabel: 'ACCOUNT',
            borderColor: '#bfdbfe',
            cardBg: '#f8fbff'
        },
        exam: {
            verb: 'had exam activity on',
            icon: `<i class="fa-solid fa-database" style="font-size:8px"></i>`,
            iconBg: 'linear-gradient(135deg,#059669,#047857)',
            glowCls: 'glow-data',
            badgeCls: 'badge-data',
            badgeLabel: 'EXAM',
            borderColor: '#bbf7d0',
            cardBg: '#f8fffe'
        },
        flag: {
            verb: 'triggered a proctor flag on',
            icon: `<i class="fa-solid fa-triangle-exclamation" style="font-size:8px"></i>`,
            iconBg: 'linear-gradient(135deg,#dc2626,#b91c1c)',
            glowCls: 'glow-warning',
            badgeCls: 'badge-warning',
            badgeLabel: '⚠ FLAG',
            borderColor: '#fecdd3',
            cardBg: '#fff8f8'
        }
    };
    const DEF_CFG = {
        verb: 'executed operation on',
        icon: `<i class="fa-solid fa-bolt" style="font-size:9px"></i>`,
        iconBg: 'linear-gradient(135deg,#d97706,#b45309)',
        glowCls: 'glow-info',
        badgeCls: 'badge-info',
        badgeLabel: 'INFO',
        borderColor: '#fde68a',
        cardBg: '#fffdf0'
    };

    /* Clear */
    function clearTimelineDOM() {
        streamContainer.innerHTML = `
            <div class="flex flex-col items-center justify-center py-16 gap-3">
                <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-slate-50 border border-slate-200">
                    <i class="fa-solid fa-satellite-dish text-blue-400"></i>
                </div>
                <p class="text-sm font-medium text-slate-400">Cleared &middot; Awaiting incoming events...</p>
            </div>`;
        recordedEventIds.clear(); currentLogsMemory = {};
        totalWarnings = 0; updateWarningMetric();
        document.getElementById('event-counter').textContent = '0';
    }

    /* Modal open */
    function openProfileModal(id) {
        const d = currentLogsMemory[id]; if (!d) return;
        document.getElementById('modal-initials-badge').textContent = d.initials || 'SYS';
        document.getElementById('modal-user-fullname').textContent  = d.author  || '—';
        document.getElementById('modal-user-email').textContent     = d.email   || '—';
        document.getElementById('modal-user-role').textContent      = d.role    || '—';
        const el = document.getElementById('modal-user-status');
        el.innerHTML = d.status === 'active'
            ? `<span style="color:#15803d" class="flex items-center gap-1.5"><span style="width:6px;height:6px;border-radius:50%;background:#22c55e;display:inline-block"></span>Active</span>`
            : `<span style="color:#be123c" class="flex items-center gap-1.5"><span style="width:6px;height:6px;border-radius:50%;background:#f87171;display:inline-block"></span>Suspended</span>`;
        const m = document.getElementById('identity-profile-modal');
        m.classList.remove('hidden'); m.classList.add('flex');
    }

    /* Modal close */
    function closeProfileModal() {
        const m = document.getElementById('identity-profile-modal');
        m.classList.add('hidden'); m.classList.remove('flex');
    }

    /* Toggle payload */
    function togglePayload(id) {
        const el = document.getElementById('payload-' + id);
        const ic = document.getElementById('ticon-' + id);
        if (el.classList.contains('open')) {
            el.classList.remove('open');
            ic.style.transform = 'rotate(0deg)';
        } else {
            el.classList.add('open');
            ic.style.transform = 'rotate(90deg)';
        }
    }

    /* Update warning metric */
    function updateWarningMetric() {
        document.getElementById('warning-count').textContent = totalWarnings;
        document.getElementById('warn-bar').style.width = Math.min(totalWarnings * 8, 100) + '%';

        const bell    = document.getElementById('bell-badge');
        const sidebar = document.getElementById('sidebar-alert-count');
        if (totalWarnings > 0) {
            if (bell) { bell.textContent = totalWarnings; bell.classList.remove('hidden'); }
            if (sidebar) { sidebar.textContent = totalWarnings; sidebar.classList.remove('hidden'); }
        } else {
            if (bell) bell.classList.add('hidden');
            if (sidebar) sidebar.classList.add('hidden');
        }

        const pill  = document.getElementById('threat-level-pill');
        const label = document.getElementById('threat-level-label');
        const icon  = document.getElementById('threat-icon');
        if (totalWarnings === 0) {
            pill.style.cssText = 'background:#f0fdf4;border-color:#bbf7d0;color:#15803d;display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:9999px;font-size:12px;font-weight:700;border:1px solid;';
            label.textContent = 'NONE'; icon.className = 'fa-solid fa-shield-check text-[10px]';
        } else if (totalWarnings <= 3) {
            pill.style.cssText = 'background:#fffbeb;border-color:#fde68a;color:#92400e;display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:9999px;font-size:12px;font-weight:700;border:1px solid;';
            label.textContent = 'LOW'; icon.className = 'fa-solid fa-triangle-exclamation text-[10px]';
        } else if (totalWarnings <= 7) {
            pill.style.cssText = 'background:#fff7ed;border-color:#fed7aa;color:#c2410c;display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:9999px;font-size:12px;font-weight:700;border:1px solid;';
            label.textContent = 'MEDIUM'; icon.className = 'fa-solid fa-triangle-exclamation text-[10px]';
        } else {
            pill.style.cssText = 'background:#fff1f2;border-color:#fecdd3;color:#be123c;display:inline-flex;align-items:center;gap:8px;padding:8px 12px;border-radius:9999px;font-size:12px;font-weight:700;border:1px solid;';
            label.textContent = 'HIGH'; icon.className = 'fa-solid fa-skull-crossbones text-[10px]';
        }
    }

    /* Main poll */
    function refreshTelemetryLogStream() {
        document.getElementById('last-refresh').textContent = new Date().toLocaleTimeString();

        fetch(`{{ route('admin.security.api') }}?filter=${activeFilterTag}`)
            .then(r => r.json())
            .then(data => {
                document.getElementById('realtime-users').textContent = data.totalUsers.toLocaleString();
                document.getElementById('realtime-exams').textContent = data.activeExams;
                const load = parseFloat(data.cpuUsage);
                document.getElementById('realtime-load').textContent  = load.toFixed(1) + '%';
                document.getElementById('load-bar').style.width       = load + '%';
                document.getElementById('load-bar').style.background  =
                    load > 80 ? '#f87171' : load > 50 ? '#fbbf24' : '#a78bfa';
                document.getElementById('load-status').textContent    =
                    load > 80 ? '⚠ High CPU — consider scaling' :
                    load > 50 ? 'Moderate CPU utilization' : 'CPU utilization normal';

                const batch = data.events.reverse();

                if (batch.length === 0 && recordedEventIds.size === 0) {
                    streamContainer.innerHTML = `
                        <div class="flex flex-col items-center justify-center py-16 gap-3">
                            <div class="w-12 h-12 rounded-2xl flex items-center justify-center bg-slate-50 border border-slate-200">
                                <i class="fa-solid fa-inbox text-slate-400"></i>
                            </div>
                            <p class="text-sm text-slate-400">No events match this filter.</p>
                        </div>`;
                    return;
                }

                batch.forEach(event => {
                    currentLogsMemory[event.id] = event;
                    if (!recordedEventIds.has(event.id)) {
                        if (recordedEventIds.size === 0) streamContainer.innerHTML = '';
                        recordedEventIds.add(event.id);

                        if (event.category === 'flag') { totalWarnings++; updateWarningMetric(); }

                        const cfg      = EVENT_CFG[event.category] || DEF_CFG;
                        const initials = event.initials || 'SYS';

                        const markup = `
                        <div class="flex items-start gap-4 timeline-entry" id="log-node-${event.id}">

                            <!-- Avatar -->
                            <div class="shrink-0 relative mt-1">
                                <button onclick="openProfileModal(${event.id})"
                                    class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xs tracking-wide transition-all duration-200 bg-slate-100 border border-slate-200 text-slate-600"
                                    onmouseover="this.style.borderColor='#bfdbfe';this.style.color='#1d4ed8';this.style.background='#eff6ff'"
                                    onmouseout="this.style.borderColor='#e2e8f0';this.style.color='#475569';this.style.background='#f1f5f9'"
                                    title="View profile">
                                    ${initials}
                                </button>
                                <span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full flex items-center justify-center text-white ${cfg.glowCls}"
                                    style="background:${cfg.iconBg};border:2px solid #ffffff">
                                    ${cfg.icon}
                                </span>
                            </div>

                            <!-- Card -->
                            <div class="flex-1 log-card rounded-2xl overflow-hidden"
                                style="border-color:${cfg.borderColor};background:${cfg.cardBg};">

                                <!-- Header row -->
                                <div class="px-5 py-3.5 flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100">
                                    <div class="flex items-center flex-wrap gap-2">
                                        <button onclick="openProfileModal(${event.id})"
                                            class="text-sm font-bold text-slate-900 hover:text-blue-600 transition-colors">
                                            ${event.author}
                                        </button>
                                        <span class="text-xs text-slate-400">${cfg.verb}</span>
                                        <span class="text-xs font-bold font-mono px-2 py-0.5 rounded-lg text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                                            ${event.target_item}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        <span class="${cfg.badgeCls} text-[9px] px-2 py-0.5 rounded font-bold tracking-wide">${cfg.badgeLabel}</span>
                                        <span class="text-[10px] font-mono px-2 py-0.5 rounded-lg bg-slate-50 border border-slate-200 text-slate-400">${event.time_span}</span>
                                    </div>
                                </div>

                                <!-- Trace payload -->
                                <div class="px-5 py-3">
                                    <button onclick="togglePayload(${event.id})" class="flex items-center gap-2 text-xs font-semibold mb-2 w-full text-left text-slate-400">
                                        <i id="ticon-${event.id}" class="fa-solid fa-chevron-right text-[9px] text-slate-300 transition-all duration-200" style="transform:rotate(90deg)"></i>
                                        <span class="font-mono text-slate-400">Trace Payload</span>
                                    </button>
                                    <div id="payload-${event.id}" class="payload-content open">
                                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-3 font-mono text-xs leading-relaxed">
                                            <div class="pl-3 break-all text-slate-600" style="border-left:2px solid ${cfg.borderColor}">${event.description}</div>
                                            <div class="mt-2.5 pt-2 border-t border-slate-200 flex flex-wrap gap-x-4 gap-y-1 text-[10px] text-slate-400">
                                                <span><i class="fa-solid fa-network-wired mr-1 text-blue-400"></i>IP: 127.0.0.1</span>
                                                <span><i class="fa-solid fa-window-restore mr-1 text-emerald-400"></i>Webkit/Chrome</span>
                                                <span><i class="fa-solid fa-fingerprint mr-1 text-violet-400"></i>Event #${event.id}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>`;

                        streamContainer.insertAdjacentHTML('afterbegin', markup);
                        document.getElementById('event-counter').textContent = recordedEventIds.size;
                    }
                });
            })
            .catch(err => console.error('Stream Error:', err));
    }

    /* Filter switch */
    function switchSecurityFilter(tag) {
        activeFilterTag = tag;
        document.querySelectorAll('.filter-btn').forEach(b => {
            b.classList.remove('filter-active');
            b.style.background = ''; b.style.borderColor = ''; b.style.color = ''; b.style.boxShadow = '';
        });
        document.getElementById('filter-' + tag)?.classList.add('filter-active');
        recordedEventIds.clear(); currentLogsMemory = {};
        totalWarnings = 0; updateWarningMetric();
        streamContainer.innerHTML = '';
        document.getElementById('event-counter').textContent = '0';
        refreshTelemetryLogStream();
    }

    /* Backdrop dismiss */
    document.getElementById('identity-profile-modal').addEventListener('click', function(e) {
        if (e.target === this) closeProfileModal();
    });

    /* Boot */
    refreshTelemetryLogStream();
    setInterval(refreshTelemetryLogStream, 3000);
</script>
@include('partials.admin-notification-realtime')
</body>
</html>