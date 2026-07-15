<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Live Monitoring — ExamSystem</title>

    {{-- Tailwind CSS v3 (stable, all classes processed reliably) --}}
    <script src="https://cdn.tailwindcss.com"></script>

    {{-- FontAwesome Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    {{-- Google Fonts: Inter + JetBrains Mono --}}
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
        /* ---- Ping / live pulse ---- */
        @keyframes ping-slow {
            75%, 100% { transform: scale(2.2); opacity: 0; }
        }
        .ping-slow { animation: ping-slow 2s cubic-bezier(0,0,0.2,1) infinite; }

        /* ---- Skeleton shimmer ---- */
        @keyframes shimmer {
            0%   { background-position: -600px 0; }
            100% { background-position:  600px 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            background-size: 1000px 100%;
            animation: shimmer 1.5s infinite linear;
            border-radius: 8px;
            display: inline-block;
        }

        /* ---- Count-up ---- */
        @keyframes countUp {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .count-animate { animation: countUp 0.4s ease-out forwards; }

        /* ---- Feed slide-in ---- */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .fade-in { opacity: 0; animation: fadeIn 0.4s ease-out forwards; }

        /* ---- Thin scrollbar ---- */
        .thin-scroll::-webkit-scrollbar { width: 4px; }
        .thin-scroll::-webkit-scrollbar-track { background: transparent; }
        .thin-scroll::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 99px; }

        /* ---- Progress bar transition ---- */
        .load-bar { transition: width 0.7s ease, background 0.4s ease; }

        /* ---- Teacher card hover ---- */
        .teacher-card { transition: box-shadow 0.25s, transform 0.25s; }
        .teacher-card:hover { box-shadow: 0 10px 28px rgba(148,163,184,0.20); transform: translateY(-2px); }
    </style>
</head>

<body class="bg-slate-50 text-slate-800 font-sans antialiased" style="font-family:'Inter',sans-serif;">
<div class="flex min-h-screen">

    {{-- ===================== SIDEBAR (matches Dashboard) ===================== --}}
    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-20"
           style="box-shadow: 4px 0 24px rgba(148,163,184,0.08);">

        {{-- Logo --}}
        <div class="h-16 flex items-center px-5 gap-3 border-b border-slate-100 flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="box-shadow: 0 4px 14px rgba(59,130,246,0.45);">
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

        {{-- Nav --}}
        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>

            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-gauge-high text-xs text-slate-400"></i>
                </span>
                <span>Dashboard</span>
            </a>

            {{-- ACTIVE --}}
            <a href="{{ route('superadmin.monitoring.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow: 0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-desktop text-xs text-white"></i>
                </span>
                <span class="flex-1">Live Monitoring</span>
                <span class="text-[9px] bg-rose-500 text-white font-bold px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
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

        {{-- Bottom --}}
        <div class="p-3 border-t border-slate-100 flex-shrink-0">
            <a href="{{ route('superadmin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm transition-all duration-200 mb-1">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-sliders text-xs text-slate-400"></i>
                </span>
                <span>Global Settings</span>
            </a>
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background: linear-gradient(135deg,#3b82f6,#6366f1);">
                    <i class="fa-solid fa-user-astronaut text-white text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Super Admin · Root</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-rose-50 hover:text-rose-500 text-slate-400 transition-all"
                            title="Logout">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        {{-- ---- TOP BAR ---- --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Live Monitoring</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Infrastructure & proctoring-pipeline health across every active exam</p>
            </div>

            <div class="flex items-center gap-3 ml-auto">
                {{-- Last updated --}}
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span>Updated: </span>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>

                {{-- System status --}}
                <div class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    All Systems Operational
                </div>

                {{-- Refresh countdown --}}
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-100 px-3 py-1.5 rounded-lg"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.08);">
                    <i id="refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                    <span>Refresh in</span>
                    <span id="refresh-countdown" class="font-mono font-bold text-slate-700 w-3 text-center">5</span>
                    <span>s</span>
                </div>
            </div>
        </header>

        {{-- ---- PAGE BODY ---- --}}
        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:28px;">

            {{-- Info Banner --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 text-blue-800 rounded-2xl px-5 py-4"
                 style="box-shadow:0 1px 4px rgba(59,130,246,0.07);">
                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-900 mb-0.5">Scope: Infrastructure & Capacity Only</p>
                    <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
                        Shows server load, regional socket session counts, and network latency.
                        Individual student webcams and screen shares are scoped to Instructors/Proctors inside their console hubs.
                    </p>
                </div>
            </div>

            {{-- ========== METRIC CARDS ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Total Live Sessions --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <i class="fa-solid fa-tower-broadcast text-rose-500 text-sm"></i>
                        </div>
                        <span class="relative flex" style="width:10px;height:10px;">
                            <span class="ping-slow absolute inline-flex rounded-full bg-rose-400 opacity-75" style="width:100%;height:100%;"></span>
                            <span class="relative inline-flex rounded-full bg-rose-500" style="width:10px;height:10px;"></span>
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Live Sessions</p>
                        <p id="metric-sessions" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:60px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Active across all cluster nodes</p>
                    </div>
                </div>

                {{-- Average Node Load --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-microchip text-amber-500 text-sm"></i>
                        </div>
                        <span id="avg-load-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border text-slate-400 bg-slate-50 border-slate-100">—</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Average Node Load</p>
                        <p id="metric-avg-load" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:56px;"></span>
                        </p>
                        <div class="mt-2 rounded-full overflow-hidden bg-slate-100" style="height:5px;">
                            <div id="avg-load-bar" class="h-full rounded-full load-bar bg-emerald-400" style="width:0%;"></div>
                        </div>
                    </div>
                </div>

                {{-- Avg Proctoring Latency --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                            <i class="fa-solid fa-wifi text-violet-500 text-sm"></i>
                        </div>
                        <span id="latency-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border text-emerald-600 bg-emerald-50 border-emerald-100">Good</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Avg Proctoring Latency</p>
                        <p id="metric-latency" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:64px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Student-facing network roundtrip</p>
                    </div>
                </div>

                {{-- Nodes Online --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fa-solid fa-server text-emerald-500 text-sm"></i>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">All Regions Active</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Nodes Online</p>
                        <p id="metric-nodes" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:52px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Across all cluster regions</p>
                    </div>
                </div>
            </div>

            {{-- ========== TEACHER / PROCTOR LIVE MONITOR ========== --}}
            <div class="bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                            <i class="fa-solid fa-chalkboard-user text-indigo-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Teacher / Proctor Live Monitor</h3>
                            <p class="text-[11px] text-slate-400 font-medium">All proctors currently active in an exam session</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="teacher-count-badge" class="text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100 px-2.5 py-0.5 rounded-full">loading...</span>
                        <span class="text-[11px] text-slate-400 font-medium">proctors online</span>
                    </div>
                </div>

                {{-- Teacher Cards Grid --}}
                <div id="teacher-grid" class="p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    {{-- Skeleton cards on first load --}}
                    <div class="rounded-xl border border-slate-100 p-4" style="background:#f8fafc;">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="skeleton rounded-xl flex-shrink-0" style="width:44px;height:44px;"></div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
                                <div class="skeleton rounded" style="height:12px;width:120px;"></div>
                                <div class="skeleton rounded" style="height:10px;width:80px;"></div>
                            </div>
                        </div>
                        <div class="skeleton rounded" style="height:10px;width:100%;margin-bottom:6px;"></div>
                        <div class="skeleton rounded" style="height:10px;width:70%;"></div>
                    </div>
                    <div class="rounded-xl border border-slate-100 p-4" style="background:#f8fafc;">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="skeleton rounded-xl flex-shrink-0" style="width:44px;height:44px;"></div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
                                <div class="skeleton rounded" style="height:12px;width:100px;"></div>
                                <div class="skeleton rounded" style="height:10px;width:60px;"></div>
                            </div>
                        </div>
                        <div class="skeleton rounded" style="height:10px;width:90%;margin-bottom:6px;"></div>
                        <div class="skeleton rounded" style="height:10px;width:55%;"></div>
                    </div>
                    <div class="rounded-xl border border-slate-100 p-4" style="background:#f8fafc;">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="skeleton rounded-xl flex-shrink-0" style="width:44px;height:44px;"></div>
                            <div style="flex:1;display:flex;flex-direction:column;gap:6px;">
                                <div class="skeleton rounded" style="height:12px;width:140px;"></div>
                                <div class="skeleton rounded" style="height:10px;width:90px;"></div>
                            </div>
                        </div>
                        <div class="skeleton rounded" style="height:10px;width:85%;margin-bottom:6px;"></div>
                        <div class="skeleton rounded" style="height:10px;width:65%;"></div>
                    </div>
                </div>
            </div>

            {{-- ========== NODES TABLE + ALERTS ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Node Infrastructure Table --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center">
                                <i class="fa-solid fa-network-wired text-slate-500 text-sm"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900">Nodes & Cluster Regions</h3>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                            <i id="node-refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                            <span>Auto-polling: 5s</span>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left" style="border-collapse:collapse;">
                            <thead>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Node</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Sessions</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">CPU Load</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Latency</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody id="node-table-body" class="divide-y divide-slate-50">
                                {{-- Populated by JS --}}
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- System Alerts Panel --}}
                <div class="bg-white rounded-2xl border border-slate-100 flex flex-col"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center">
                                <i class="fa-solid fa-bell text-amber-500 text-sm"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900">System Alerts</h3>
                        </div>
                        <span id="alerts-badge" class="text-[10px] font-bold bg-slate-50 text-slate-400 border border-slate-100 px-2 py-0.5 rounded-full">0</span>
                    </div>
                    <div id="alerts-panel" class="flex-1 p-4 overflow-y-auto thin-scroll" style="max-height:340px;">
                        {{-- Populated by JS --}}
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>


{{-- ===================== SCRIPTS ===================== --}}
<script>
// ============================================================
//  LIVE CLOCK
// ============================================================
function updateClock() {
    document.getElementById('live-clock').textContent =
        new Date().toLocaleTimeString('en-US', { hour12: false, hour:'2-digit', minute:'2-digit', second:'2-digit' });
}
updateClock();
setInterval(updateClock, 1000);


// ============================================================
//  REFRESH COUNTDOWN (5s interval)
// ============================================================
let countdown = 5;
const countdownEl  = document.getElementById('refresh-countdown');
const refreshIcon  = document.getElementById('refresh-icon');
const nodeRefIcon  = document.getElementById('node-refresh-icon');

setInterval(() => {
    countdown--;
    if (countdown <= 0) {
        countdown = 5;
        [refreshIcon, nodeRefIcon].forEach(ic => {
            ic.classList.add('animate-spin');
            setTimeout(() => ic.classList.remove('animate-spin'), 700);
        });
        syncMonitoring();
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
    void el.offsetWidth;
    el.classList.add('count-animate');
}

function updateAvgLoad(pct) {
    const bar   = document.getElementById('avg-load-bar');
    const badge = document.getElementById('avg-load-badge');
    bar.style.width = pct + '%';
    if (pct < 50) {
        bar.style.background    = '#34d399';
        badge.textContent       = 'Stable';
        badge.style.color       = '#059669';
        badge.style.background  = '#ecfdf5';
        badge.style.borderColor = '#a7f3d0';
    } else if (pct < 75) {
        bar.style.background    = '#fbbf24';
        badge.textContent       = 'Elevated';
        badge.style.color       = '#d97706';
        badge.style.background  = '#fffbeb';
        badge.style.borderColor = '#fde68a';
    } else {
        bar.style.background    = '#f43f5e';
        badge.textContent       = 'Critical';
        badge.style.color       = '#e11d48';
        badge.style.background  = '#fff1f2';
        badge.style.borderColor = '#fecdd3';
    }
}

function updateLatencyBadge(ms) {
    const badge = document.getElementById('latency-badge');
    if (ms < 100) {
        badge.textContent = 'Excellent'; badge.style.color='#059669'; badge.style.background='#ecfdf5'; badge.style.borderColor='#a7f3d0';
    } else if (ms < 250) {
        badge.textContent = 'Good';      badge.style.color='#2563eb'; badge.style.background='#eff6ff'; badge.style.borderColor='#bfdbfe';
    } else if (ms < 500) {
        badge.textContent = 'Fair';      badge.style.color='#d97706'; badge.style.background='#fffbeb'; badge.style.borderColor='#fde68a';
    } else {
        badge.textContent = 'High';      badge.style.color='#e11d48'; badge.style.background='#fff1f2'; badge.style.borderColor='#fecdd3';
    }
    badge.style.borderWidth = '1px';
    badge.style.borderStyle = 'solid';
    badge.style.padding     = '2px 8px';
    badge.style.borderRadius= '999px';
    badge.style.fontWeight  = '700';
    badge.style.fontSize    = '10px';
}


// ============================================================
//  NODE TABLE RENDER
// ============================================================
function getNodeStatusStyle(status) {
    const s = status.toLowerCase();
    if (s === 'healthy')  return { bg:'#ecfdf5', color:'#059669', border:'#a7f3d0', label:'Healthy'  };
    if (s === 'warning')  return { bg:'#fffbeb', color:'#d97706', border:'#fde68a', label:'Warning'  };
    if (s === 'critical') return { bg:'#fff1f2', color:'#e11d48', border:'#fecdd3', label:'Critical' };
    return { bg:'#f1f5f9', color:'#64748b', border:'#e2e8f0', label:status };
}

function getLoadColor(pct) {
    if (pct >= 85) return '#f43f5e';
    if (pct >= 65) return '#fbbf24';
    return '#34d399';
}

function renderNodeTable(nodes) {
    const tbody = document.getElementById('node-table-body');
    if (!nodes || nodes.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" style="padding:32px;text-align:center;color:#94a3b8;font-size:12px;">No nodes reported.</td></tr>`;
        return;
    }
    tbody.innerHTML = nodes.map((node, i) => {
        const st    = getNodeStatusStyle(node.status);
        const lc    = getLoadColor(node.load);
        const delay = i * 60;
        return `
        <tr class="fade-in" style="animation-delay:${delay}ms;transition:background 0.15s;"
            onmouseenter="this.style.background='#f8fafc'" onmouseleave="this.style.background='transparent'">
            <td style="padding:14px 24px;font-weight:700;color:#0f172a;font-size:13px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:6px;height:6px;border-radius:50%;background:${lc};display:inline-block;flex-shrink:0;"></span>
                    ${node.name}
                </div>
            </td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:12px;color:#475569;font-weight:600;">${node.sessions}</td>
            <td style="padding:14px 16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:80px;height:6px;border-radius:999px;background:#f1f5f9;overflow:hidden;flex-shrink:0;">
                        <div class="load-bar" style="height:100%;border-radius:999px;background:${lc};width:${node.load}%;"></div>
                    </div>
                    <span style="font-family:'JetBrains Mono',monospace;font-size:11px;font-weight:700;color:#475569;">${node.load}%</span>
                </div>
            </td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:12px;color:#64748b;font-weight:600;">${node.latency} ms</td>
            <td style="padding:14px 16px;text-align:center;">
                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;
                             padding:3px 10px;border-radius:6px;border:1px solid;
                             background:${st.bg};color:${st.color};border-color:${st.border};">
                    ${st.label}
                </span>
            </td>
        </tr>`;
    }).join('');
}


// ============================================================
//  TEACHER CARD RENDER
// ============================================================
const AVATAR_GRADIENTS = [
    'linear-gradient(135deg,#3b82f6,#6366f1)',
    'linear-gradient(135deg,#10b981,#059669)',
    'linear-gradient(135deg,#f59e0b,#d97706)',
    'linear-gradient(135deg,#ef4444,#dc2626)',
    'linear-gradient(135deg,#8b5cf6,#7c3aed)',
    'linear-gradient(135deg,#06b6d4,#0284c7)',
];

function getTeacherStatusStyle(status) {
    const s = status.toLowerCase();
    if (s === 'active')   return { bg:'#ecfdf5', color:'#059669', border:'#a7f3d0', dot:'#10b981', label:'Active'   };
    if (s === 'idle')     return { bg:'#f8fafc', color:'#64748b', border:'#e2e8f0', dot:'#94a3b8', label:'Idle'     };
    if (s === 'flagging') return { bg:'#fffbeb', color:'#d97706', border:'#fde68a', dot:'#f59e0b', label:'Flagging' };
    return                       { bg:'#eff6ff', color:'#2563eb', border:'#bfdbfe', dot:'#3b82f6', label:status     };
}

function getInitials(name) {
    return name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
}

function renderTeacherGrid(teachers) {
    const grid  = document.getElementById('teacher-grid');
    const badge = document.getElementById('teacher-count-badge');

    if (!teachers || teachers.length === 0) {
        badge.textContent = '0';
        grid.innerHTML = `
            <div style="grid-column:1/-1;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:48px 0;color:#cbd5e1;">
                <i class="fa-solid fa-chalkboard-user" style="font-size:32px;margin-bottom:12px;"></i>
                <p style="font-size:13px;font-weight:600;color:#94a3b8;">No proctors currently active</p>
            </div>`;
        return;
    }

    badge.textContent = teachers.length;

    grid.innerHTML = teachers.map((t, i) => {
        const st      = getTeacherStatusStyle(t.status);
        const grad    = AVATAR_GRADIENTS[i % AVATAR_GRADIENTS.length];
        const initials= getInitials(t.name);
        const delay   = i * 70;
        return `
        <div class="teacher-card fade-in rounded-xl border border-slate-100 p-4 cursor-default"
             style="animation-delay:${delay}ms;background:#fff;box-shadow:0 1px 4px rgba(148,163,184,0.06);">

            {{-- Top row --}}
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;">
                {{-- Avatar --}}
                <div style="width:44px;height:44px;border-radius:12px;flex-shrink:0;
                            background:${grad};
                            display:flex;align-items:center;justify-content:center;
                            font-size:14px;font-weight:800;color:#fff;letter-spacing:0.02em;">
                    ${initials}
                </div>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:13px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${t.name}</p>
                    <p style="font-size:10px;color:#94a3b8;font-weight:500;margin-top:2px;">${t.role ?? 'Proctor'}</p>
                </div>
                {{-- Status badge --}}
                <span style="font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;
                             padding:3px 8px;border-radius:999px;border:1px solid;
                             background:${st.bg};color:${st.color};border-color:${st.border};flex-shrink:0;display:flex;align-items:center;gap:4px;">
                    <span style="width:5px;height:5px;border-radius:50%;background:${st.dot};display:inline-block;"></span>
                    ${st.label}
                </span>
            </div>

            {{-- Exam info --}}
            <div style="background:#f8fafc;border-radius:10px;padding:10px 12px;margin-bottom:10px;">
                <p style="font-size:9px;font-weight:700;color:#94a3b8;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:3px;">Current Exam</p>
                <p style="font-size:12px;font-weight:700;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${t.exam}</p>
            </div>

            {{-- Stats row --}}
            <div style="display:flex;gap:10px;">
                <div style="flex:1;text-align:center;background:#f0fdf4;border-radius:8px;padding:8px 6px;">
                    <p style="font-size:16px;font-weight:800;color:#059669;line-height:1;">${t.students}</p>
                    <p style="font-size:9px;font-weight:600;color:#86efac;margin-top:3px;">Students</p>
                </div>
                <div style="flex:1;text-align:center;background:#eff6ff;border-radius:8px;padding:8px 6px;">
                    <p style="font-size:16px;font-weight:800;color:#2563eb;line-height:1;">${t.flags ?? 0}</p>
                    <p style="font-size:9px;font-weight:600;color:#93c5fd;margin-top:3px;">Flags</p>
                </div>
                <div style="flex:1;text-align:center;background:#fdf4ff;border-radius:8px;padding:8px 6px;">
                    <p style="font-size:12px;font-weight:800;color:#7c3aed;line-height:1;font-family:'JetBrains Mono',monospace;">${t.duration ?? '--'}</p>
                    <p style="font-size:9px;font-weight:600;color:#c4b5fd;margin-top:3px;">Running</p>
                </div>
            </div>
        </div>`;
    }).join('');
}


// ============================================================
//  ALERTS PANEL RENDER
// ============================================================
function renderAlerts(alerts) {
    const panel = document.getElementById('alerts-panel');
    const badge = document.getElementById('alerts-badge');
    badge.textContent = alerts.length;

    if (!alerts || alerts.length === 0) {
        badge.style.background = '#f0fdf4'; badge.style.color = '#059669'; badge.style.borderColor = '#a7f3d0';
        panel.innerHTML = `
            <div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 16px;color:#cbd5e1;text-align:center;">
                <i class="fa-solid fa-shield-cat" style="font-size:28px;margin-bottom:12px;color:#cbd5e1;"></i>
                <p style="font-size:12px;font-weight:600;color:#94a3b8;">All clusters nominal</p>
                <p style="font-size:11px;color:#cbd5e1;margin-top:4px;">No active alerts to display</p>
            </div>`;
        return;
    }

    badge.style.background  = '#fff1f2';
    badge.style.color       = '#e11d48';
    badge.style.borderColor = '#fecdd3';
    badge.style.border      = '1px solid';

    const SEVERITY = {
        critical: { icon:'fa-circle-xmark', bg:'#fff1f2', color:'#e11d48', border:'#fecdd3', iconColor:'#f43f5e' },
        warning:  { icon:'fa-triangle-exclamation', bg:'#fffbeb', color:'#92400e', border:'#fde68a', iconColor:'#f59e0b' },
        info:     { icon:'fa-circle-info',  bg:'#eff6ff', color:'#1e40af', border:'#bfdbfe', iconColor:'#3b82f6' },
    };

    panel.innerHTML = alerts.map((alert, i) => {
        const s = SEVERITY[alert.severity] || SEVERITY.warning;
        const delay = i * 80;
        return `
        <div class="fade-in" style="animation-delay:${delay}ms;
             display:flex;align-items:flex-start;gap:10px;
             padding:12px;border-radius:12px;margin-bottom:8px;
             background:${s.bg};border:1px solid ${s.border};">
            <i class="fa-solid ${s.icon}" style="color:${s.iconColor};font-size:13px;flex-shrink:0;margin-top:1px;"></i>
            <div>
                <p style="font-size:11px;font-weight:700;color:${s.color};">${alert.title ?? 'Alert'}</p>
                <p style="font-size:10px;color:${s.color};opacity:0.8;margin-top:2px;line-height:1.5;">${alert.message}</p>
                <p style="font-size:9px;color:${s.color};opacity:0.55;margin-top:4px;font-family:'JetBrains Mono',monospace;">${alert.time ?? ''}</p>
            </div>
        </div>`;
    }).join('');
}


// ============================================================
//  MOCK DATA
//  Replace these with real fetch() calls to your Laravel routes:
//    fetch("{{ route('superadmin.telemetry.livefeed') }}")
//    fetch("{{ route('superadmin.monitoring.teachers') }}")
// ============================================================
function mockData() {
    const loadVariance = () => Math.floor(Math.random() * 10);
    return {
        metrics: {
            total_sessions: 284,
            avg_load:       38 + loadVariance(),
            avg_latency_ms: 82 + Math.floor(Math.random() * 30),
            nodes_online:   6,
            nodes_total:    6,
        },
        nodes: [
            { name: 'SG-CORE-01',  sessions: 64, load: 42 + loadVariance(), latency: 72,  status: 'healthy'  },
            { name: 'SG-CORE-02',  sessions: 58, load: 37 + loadVariance(), latency: 68,  status: 'healthy'  },
            { name: 'US-EAST-01',  sessions: 71, load: 68 + loadVariance(), latency: 140, status: 'warning'  },
            { name: 'EU-WEST-01',  sessions: 49, load: 31 + loadVariance(), latency: 95,  status: 'healthy'  },
            { name: 'AP-SEA-01',   sessions: 28, load: 22 + loadVariance(), latency: 110, status: 'healthy'  },
            { name: 'US-WEST-02',  sessions: 14, load: 91 + loadVariance(), latency: 210, status: 'critical' },
        ],
        teachers: [
            { name:'Dr. Sarah Mitchell',  role:'Senior Proctor', exam:'Physics Final — Cohort A', students: 28, flags: 2,  duration:'1h 12m', status:'active'   },
            { name:'Prof. James Okafor',  role:'Exam Proctor',   exam:'Advanced Mathematics',     students: 35, flags: 0,  duration:'45m',    status:'active'   },
            { name:'Ms. Lena Torres',     role:'Proctor',        exam:'English Literature',       students: 22, flags: 5,  duration:'2h 03m', status:'flagging' },
            { name:'Mr. David Chen',      role:'Proctor',        exam:'Computer Science — Unit 3',students: 19, flags: 1,  duration:'30m',    status:'active'   },
            { name:'Dr. Amara Nwosu',     role:'Senior Proctor', exam:'Biology Midterm',          students: 41, flags: 0,  duration:'1h 40m', status:'active'   },
            { name:'Mr. Lucas Ferreira',  role:'Proctor',        exam:'History & Society',        students: 17, flags: 0,  duration:'—',      status:'idle'     },
        ],
        alerts: [
            { severity:'critical', title:'US-WEST-02 Overload', message:'CPU at 91%. Recommend session redistribution immediately.',         time:'Just now'  },
            { severity:'warning',  title:'US-EAST-01 Elevated', message:'Load at 68%. Monitor for further increases over next 10 minutes.', time:'2m ago'    },
            { severity:'info',     title:'Backup Completed',    message:'Nightly database snapshot completed successfully.',                  time:'14m ago'   },
        ]
    };
}


// ============================================================
//  MAIN SYNC
// ============================================================
function syncMonitoring() {
    //
    // PRODUCTION: Replace mockData() with:
    //   const res  = await fetch("{{ route('superadmin.monitoring.api') }}");
    //   const data = await res.json();
    //
    const data = mockData();
    const m    = data.metrics;

    setMetric('metric-sessions',  m.total_sessions.toLocaleString());
    setMetric('metric-avg-load',  m.avg_load + '%');
    setMetric('metric-latency',   m.avg_latency_ms + 'ms');
    setMetric('metric-nodes',     m.nodes_online + ' / ' + m.nodes_total);

    updateAvgLoad(m.avg_load);
    updateLatencyBadge(m.avg_latency_ms);

    renderNodeTable(data.nodes);
    renderTeacherGrid(data.teachers);
    renderAlerts(data.alerts);
}

// ---- INIT ----
syncMonitoring();
</script>

</body>
</html>