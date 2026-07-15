<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Exams Oversight — ExamSystem</title>

    {{-- Tailwind CSS v3 (stable) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    {{-- FontAwesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>

    <style>
        @keyframes ping-slow { 75%,100%{transform:scale(2.2);opacity:0;} }
        .ping-slow { animation: ping-slow 2s cubic-bezier(0,0,.2,1) infinite; }

        @keyframes shimmer { 0%{background-position:-600px 0} 100%{background-position:600px 0} }
        .skeleton {
            background: linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);
            background-size:1000px 100%; animation: shimmer 1.5s infinite linear; border-radius:8px; display:inline-block;
        }

        @keyframes countUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .count-animate { animation: countUp 0.4s ease-out forwards; }

        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { opacity:0; animation: fadeIn 0.35s ease-out forwards; }

        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}

        .load-bar { transition: width 0.7s ease; }
        .dept-row:hover, .exam-row:hover { background:#f8fafc; }
    </style>
</head>

<body class="bg-slate-50 text-slate-800" style="font-family:'Inter',sans-serif;">
<div class="flex min-h-screen">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-20"
           style="box-shadow:4px 0 24px rgba(148,163,184,0.08);">

        <div class="h-16 flex items-center px-5 gap-3 border-b border-slate-100 flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="box-shadow:0 4px 14px rgba(59,130,246,0.45);">
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

        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>

            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-gauge-high text-xs text-slate-400"></i>
                </span><span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-desktop text-xs text-slate-400"></i>
                </span><span>Live Monitoring</span>
            </a>

            {{-- ACTIVE --}}
            <a href="{{ route('superadmin.exams.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-file-signature text-xs text-white"></i>
                </span><span>Exams Oversight</span>
            </a>

            <a href="{{ route('superadmin.reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-xs text-slate-400"></i>
                </span><span>Reports & Analytics</span>
            </a>

            <div class="pt-4 pb-1">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2">Root Access</p>
            </div>

            <a href="{{ route('superadmin.admins.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-users text-xs text-slate-400"></i>
                </span><span>User Management</span>
            </a>
            <a href="{{ route('superadmin.audit-logs.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-xs text-slate-400"></i>
                </span><span>Audit Trails</span>
            </a>
            <a href="{{ route('superadmin.backups.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-database text-xs text-slate-400"></i>
                </span><span>Database & Backup</span>
            </a>
        </nav>

        <div class="p-3 border-t border-slate-100 flex-shrink-0">
            <a href="{{ route('superadmin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm transition-all duration-200 mb-1">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-sliders text-xs text-slate-400"></i>
                </span><span>Global Settings</span>
            </a>
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(135deg,#3b82f6,#6366f1);">
                    <i class="fa-solid fa-user-astronaut text-white text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Super Admin · Root</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-rose-50 hover:text-rose-500 text-slate-400 transition-all" title="Logout">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        {{-- TOP BAR --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Exams Oversight</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Aggregate health across all departments — read-only, emergency intervention only</p>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    All Systems Operational
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-100 px-3 py-1.5 rounded-lg"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.08);">
                    <i id="refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                    <span>Refresh in</span>
                    <span id="refresh-countdown" class="font-mono font-bold text-slate-700 w-3 text-center">5</span><span>s</span>
                </div>
            </div>
        </header>

        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:24px;">

            {{-- SCOPE BANNER --}}
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-2xl px-5 py-4"
                 style="box-shadow:0 1px 4px rgba(245,158,11,0.08);">
                <div class="w-8 h-8 rounded-xl bg-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-eye text-amber-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-amber-900 mb-0.5">Oversight Scope — Read-Only View</p>
                    <p class="text-[11px] text-amber-700 font-medium leading-relaxed">
                        You can <strong>view aggregate stats</strong> and identify high flag-rate departments.
                        <strong>Creating or editing exams stays with each department's Admin.</strong>
                        Your emergency powers: <span class="font-bold text-amber-900">force-end a stuck exam</span> or
                        <span class="font-bold text-amber-900">push a system-wide policy override</span> (e.g. disable tab-switch detection during outages).
                    </p>
                </div>
            </div>

            {{-- ========== METRIC CARDS ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3 transition-all duration-300 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                            <i class="fa-solid fa-file-signature text-blue-500 text-sm"></i>
                        </div>
                        <span class="text-[10px] font-bold text-slate-500 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">All Orgs</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Exams</p>
                        <p id="metric-total" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:56px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Registered across all departments</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-emerald-100 p-5 flex flex-col gap-3 transition-all duration-300 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);background:linear-gradient(135deg,#fff 70%,rgba(209,250,229,0.25) 100%);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(16,185,129,0.12)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center relative">
                            <i class="fa-solid fa-circle-play text-emerald-500 text-sm"></i>
                            <span class="absolute rounded-full bg-emerald-500 ring-2 ring-white animate-pulse"
                                  style="width:9px;height:9px;top:-2px;right:-2px;"></span>
                        </div>
                        <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 border border-emerald-200 px-2 py-0.5 rounded-full animate-pulse">● LIVE</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Currently Active</p>
                        <p id="metric-active" class="text-3xl font-black text-emerald-600 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:48px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Exams running right now</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3 transition-all duration-300 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-rose-50 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i>
                        </div>
                        <span id="flag-dept-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border text-slate-400 bg-slate-50 border-slate-100">—</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">High-Flag Depts</p>
                        <p id="metric-flag-depts" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:48px;"></span>
                        </p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Departments with flag rate ≥ 8%</p>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3 transition-all duration-300 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                            <i class="fa-solid fa-chart-line text-violet-500 text-sm"></i>
                        </div>
                        <span id="avg-flag-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border text-emerald-600 bg-emerald-50 border-emerald-100"
                              style="border-width:1px;border-style:solid;border-radius:999px;padding:2px 8px;font-weight:700;font-size:10px;">Normal</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Avg Flag Rate</p>
                        <p id="metric-avg-flag" class="text-3xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:36px;width:56px;"></span>
                        </p>
                        <div class="mt-2 rounded-full overflow-hidden bg-slate-100" style="height:5px;">
                            <div id="avg-flag-bar" class="h-full rounded-full load-bar bg-emerald-400" style="width:0%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== LIVE ACTIVE EXAMS TABLE ========== --}}
            <div class="bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <span class="relative flex" style="width:10px;height:10px;">
                            <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                            <span class="relative inline-flex rounded-full bg-emerald-500" style="width:10px;height:10px;"></span>
                        </span>
                        <h3 class="font-bold text-sm text-slate-900">Live Active Exams</h3>
                    </div>
                    <span id="active-exams-count" class="text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-100 px-2.5 py-0.5 rounded-full">loading...</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Exam</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Department</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Proctor</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Students</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Flag Rate</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Running</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="active-exams-table" class="divide-y divide-slate-50">
                            <tr><td colspan="8" style="padding:28px;">
                                <div style="display:flex;flex-direction:column;gap:10px;">
                                    <div class="skeleton rounded" style="height:12px;width:60%;"></div>
                                    <div class="skeleton rounded" style="height:12px;width:42%;"></div>
                                    <div class="skeleton rounded" style="height:12px;width:54%;"></div>
                                </div>
                            </td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- ========== DEPARTMENTS + RIGHT PANELS ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {{-- Department Table --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                                <i class="fa-solid fa-building-columns text-blue-500 text-sm"></i>
                            </div>
                            <h3 class="font-bold text-sm text-slate-900">By Department / Org</h3>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium">Flag alert threshold: <span class="font-bold text-rose-500">≥ 8%</span></span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left" style="border-collapse:collapse;">
                            <thead>
                                <tr style="border-bottom:1px solid #f1f5f9;">
                                    <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Department</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Exams</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Live</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Students</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Flag Rate</th>
                                    <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Health</th>
                                </tr>
                            </thead>
                            <tbody id="dept-table" class="divide-y divide-slate-50"></tbody>
                        </table>
                    </div>
                </div>

                {{-- Right column --}}
                <div class="flex flex-col gap-5">

                    {{-- Intervention Panel --}}
                    <div class="bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                                    <i class="fa-solid fa-bolt text-rose-500 text-sm"></i>
                                </div>
                                <h3 class="font-bold text-sm text-slate-900">Needs Intervention</h3>
                            </div>
                            <span id="stuck-badge" class="text-[10px] font-bold bg-slate-50 text-slate-400 border border-slate-100 px-2 py-0.5 rounded-full"
                                  style="border-width:1px;border-style:solid;border-radius:999px;padding:2px 8px;">0</span>
                        </div>
                        <div id="stuck-exams-panel" class="p-4 overflow-y-auto thin-scroll" style="max-height:260px;"></div>
                    </div>

                    {{-- System Policy Controls --}}
                    <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                        <div class="px-5 py-4 border-b border-slate-800" style="background:linear-gradient(135deg,#1e293b,#334155);">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,0.1);">
                                    <i class="fa-solid fa-sliders text-white text-sm"></i>
                                </div>
                                <div>
                                    <h3 class="font-bold text-sm text-white">System-Wide Policies</h3>
                                    <p class="text-[10px] text-slate-400 mt-0.5">Emergency overrides — affects all active exams</p>
                                </div>
                            </div>
                        </div>
                        <div class="p-4 space-y-2.5" id="policy-controls"></div>
                    </div>

                </div>
            </div>
        </div>
    </main>
</div>


{{-- ===================== FORCE-END MODAL ===================== --}}
<div id="force-end-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.55);backdrop-filter:blur(8px);">
    <div class="relative bg-white rounded-2xl max-w-md w-full p-7 border border-rose-100"
         style="box-shadow:0 24px 64px rgba(15,23,42,0.25);"
         onclick="event.stopPropagation()">
        <div class="w-14 h-14 bg-rose-100 rounded-2xl flex items-center justify-center mx-auto mb-4">
            <i class="fa-solid fa-triangle-exclamation text-rose-600 text-2xl"></i>
        </div>
        <h3 class="text-lg font-extrabold text-slate-900 text-center">Force-End This Exam?</h3>
        <p class="text-sm text-slate-500 text-center mt-2 leading-relaxed">
            This will <strong class="text-rose-600">immediately terminate</strong>
            <strong id="modal-exam-title" class="text-slate-900"></strong>
            for every connected student. Use this only when the exam is stuck and affecting system stability.
        </p>
        <div class="flex items-center justify-center gap-1.5 mt-2">
            <i class="fa-solid fa-building-columns text-slate-300 text-xs"></i>
            <span id="modal-exam-dept" class="text-xs text-slate-400 font-medium"></span>
        </div>
        <div class="mt-6 flex gap-3">
            <button onclick="closeForceEnd()"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button onclick="executeForceEnd()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:#e11d48;box-shadow:0 4px 14px rgba(225,29,72,0.30);"
                    onmouseenter="this.style.background='#be123c'"
                    onmouseleave="this.style.background='#e11d48'">
                <i class="fa-solid fa-power-off mr-1.5"></i> Force-End Now
            </button>
        </div>
    </div>
</div>


{{-- ===================== SCRIPTS ===================== --}}
<script>
// ============================================================
//  LIVE CLOCK
// ============================================================
function updateClock(){
    document.getElementById('live-clock').textContent =
        new Date().toLocaleTimeString('en-US',{hour12:false,hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
updateClock(); setInterval(updateClock,1000);


// ============================================================
//  REFRESH COUNTDOWN  (5s)
// ============================================================
let countdown=5;
const countdownEl=document.getElementById('refresh-countdown');
const refreshIcon=document.getElementById('refresh-icon');
setInterval(()=>{
    countdown--;
    if(countdown<=0){
        countdown=5;
        refreshIcon.classList.add('animate-spin');
        setTimeout(()=>refreshIcon.classList.remove('animate-spin'),700);
        syncExams();
    }
    countdownEl.textContent=countdown;
},1000);


// ============================================================
//  METRIC HELPERS
// ============================================================
function setMetric(id,value){
    const el=document.getElementById(id);
    el.innerHTML=''; el.textContent=value;
    el.classList.remove('count-animate'); void el.offsetWidth; el.classList.add('count-animate');
}

function styleBadge(el,text,color,bg,border){
    el.textContent=text; el.style.color=color; el.style.background=bg; el.style.borderColor=border;
    el.style.borderWidth='1px'; el.style.borderStyle='solid'; el.style.borderRadius='999px';
    el.style.padding='2px 8px'; el.style.fontWeight='700'; el.style.fontSize='10px';
}

function updateAvgFlagBar(pct){
    const bar=document.getElementById('avg-flag-bar');
    const badge=document.getElementById('avg-flag-badge');
    bar.style.width=Math.min(pct*5,100)+'%';
    if(pct<5){bar.style.background='#34d399';styleBadge(badge,'Normal','#059669','#ecfdf5','#a7f3d0');}
    else if(pct<8){bar.style.background='#fbbf24';styleBadge(badge,'Moderate','#d97706','#fffbeb','#fde68a');}
    else{bar.style.background='#f43f5e';styleBadge(badge,'Elevated','#e11d48','#fff1f2','#fecdd3');}
}

function updateFlagDeptBadge(count){
    const badge=document.getElementById('flag-dept-badge');
    if(count===0)styleBadge(badge,'None','#059669','#ecfdf5','#a7f3d0');
    else if(count<=2)styleBadge(badge,count+' dept'+(count>1?'s':''),'#d97706','#fffbeb','#fde68a');
    else styleBadge(badge,count+' depts','#e11d48','#fff1f2','#fecdd3');
}


// ============================================================
//  RENDER: ACTIVE EXAMS TABLE
// ============================================================
function statusStyle(s){
    const m={
        'running': {bg:'#ecfdf5',color:'#059669',border:'#a7f3d0',label:'Running'},
        'at risk': {bg:'#fffbeb',color:'#d97706',border:'#fde68a',label:'At Risk'},
        'critical':{bg:'#fff1f2',color:'#e11d48',border:'#fecdd3',label:'Critical'},
    };
    return m[s.toLowerCase()]||{bg:'#f1f5f9',color:'#64748b',border:'#e2e8f0',label:s};
}

function renderActiveExams(exams){
    const tbody=document.getElementById('active-exams-table');
    const badge=document.getElementById('active-exams-count');
    badge.textContent=exams.length+' active';
    if(!exams.length){
        tbody.innerHTML=`<tr><td colspan="8" style="padding:40px;text-align:center;color:#94a3b8;font-size:13px;font-weight:600;">
            <i class="fa-solid fa-circle-check" style="font-size:28px;margin-bottom:10px;display:block;color:#d1fae5;"></i>No active exams right now.</td></tr>`;
        return;
    }
    tbody.innerHTML=exams.map((ex,i)=>{
        const st=statusStyle(ex.status);
        const fc=ex.flag_rate>=8?'#e11d48':ex.flag_rate>=5?'#d97706':'#059669';
        const canForce=ex.status.toLowerCase()==='critical'||ex.stuck;
        return `
        <tr class="exam-row fade-in" style="animation-delay:${i*55}ms;transition:background 0.15s;cursor:default;">
            <td style="padding:14px 24px;min-width:180px;">
                <p style="font-size:12px;font-weight:700;color:#0f172a;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:170px;">${ex.title}</p>
                <p style="font-size:10px;color:#94a3b8;font-weight:500;margin-top:2px;font-family:'JetBrains Mono',monospace;">#${ex.id}</p>
            </td>
            <td style="padding:14px 16px;font-size:12px;font-weight:600;color:#475569;white-space:nowrap;">${ex.department}</td>
            <td style="padding:14px 16px;font-size:12px;color:#64748b;font-weight:500;white-space:nowrap;">${ex.proctor}</td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;color:#475569;">${ex.students}</td>
            <td style="padding:14px 16px;">
                <span style="font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:700;color:${fc};">${ex.flag_rate}%</span>
            </td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#94a3b8;font-weight:500;">${ex.duration}</td>
            <td style="padding:14px 16px;text-align:center;">
                <span style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;
                             padding:3px 10px;border-radius:6px;border:1px solid;
                             background:${st.bg};color:${st.color};border-color:${st.border};">${st.label}</span>
            </td>
            <td style="padding:14px 16px;text-align:center;">
                ${canForce
                    ? `<button onclick="openForceEnd(${ex.id},'${ex.title.replace(/'/g,"\\'")}','${ex.department}')"
                               style="font-size:10px;font-weight:700;padding:5px 12px;border-radius:8px;border:1px solid #fecdd3;
                                      background:#fff1f2;color:#e11d48;cursor:pointer;"
                               onmouseenter="this.style.background='#ffe4e6'" onmouseleave="this.style.background='#fff1f2'">
                               <i class='fa-solid fa-power-off' style='margin-right:4px;'></i>Force-End</button>`
                    : `<span style="font-size:10px;color:#cbd5e1;font-weight:500;">—</span>`}
            </td>
        </tr>`;
    }).join('');
}


// ============================================================
//  RENDER: DEPARTMENT TABLE
// ============================================================
function renderDeptTable(departments){
    const tbody=document.getElementById('dept-table');
    const highFlag=departments.filter(d=>d.flag_rate>=8).length;
    setMetric('metric-flag-depts',highFlag);
    updateFlagDeptBadge(highFlag);

    tbody.innerHTML=departments.map((dept,i)=>{
        const isH=dept.flag_rate>=8,isM=dept.flag_rate>=5;
        const fBg=isH?'#fff1f2':isM?'#fffbeb':'#ecfdf5';
        const fC =isH?'#e11d48':isM?'#d97706':'#059669';
        const fB =isH?'#fecdd3':isM?'#fde68a':'#a7f3d0';
        const bC =isH?'#f43f5e':isM?'#fbbf24':'#34d399';
        const health=isH
            ?'<i class="fa-solid fa-circle-exclamation" style="color:#f43f5e;font-size:14px;"></i>'
            :isM
            ?'<i class="fa-solid fa-circle-exclamation" style="color:#fbbf24;font-size:14px;"></i>'
            :'<i class="fa-solid fa-circle-check" style="color:#10b981;font-size:14px;"></i>';
        return `
        <tr class="dept-row fade-in" style="animation-delay:${i*50}ms;transition:background 0.15s;">
            <td style="padding:14px 24px;">
                <div style="display:flex;align-items:center;gap:8px;">
                    <span style="width:6px;height:6px;border-radius:50%;background:${bC};display:inline-block;flex-shrink:0;"></span>
                    <span style="font-size:13px;font-weight:700;color:#0f172a;">${dept.name}</span>
                    ${isH?'<span style="font-size:9px;font-weight:800;padding:2px 6px;border-radius:4px;background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;margin-left:4px;">HIGH FLAG</span>':''}
                </div>
            </td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;color:#475569;">${dept.exam_count}</td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;color:#475569;">${dept.live}</td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;color:#475569;">${dept.students}</td>
            <td style="padding:14px 16px;">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div style="width:60px;height:5px;border-radius:999px;background:#f1f5f9;overflow:hidden;flex-shrink:0;">
                        <div class="load-bar" style="height:100%;border-radius:999px;background:${bC};width:${Math.min(dept.flag_rate*5,100)}%;"></div>
                    </div>
                    <span style="font-size:10px;font-weight:700;padding:2px 8px;border-radius:999px;border:1px solid;background:${fBg};color:${fC};border-color:${fB};">${dept.flag_rate}%</span>
                </div>
            </td>
            <td style="padding:14px 16px;text-align:center;">${health}</td>
        </tr>`;
    }).join('');
}


// ============================================================
//  RENDER: STUCK EXAMS PANEL
// ============================================================
function renderStuckExams(stuck){
    const panel=document.getElementById('stuck-exams-panel');
    const badge=document.getElementById('stuck-badge');
    badge.textContent=stuck.length;
    if(!stuck.length){
        styleBadge(badge,'0','#059669','#f0fdf4','#a7f3d0');
        panel.innerHTML=`<div style="display:flex;flex-direction:column;align-items:center;justify-content:center;padding:32px 12px;text-align:center;">
            <i class="fa-solid fa-square-check" style="font-size:28px;margin-bottom:10px;color:#d1fae5;"></i>
            <p style="font-size:12px;font-weight:600;color:#94a3b8;">All clear</p>
            <p style="font-size:11px;color:#cbd5e1;margin-top:3px;">No stuck exams detected</p></div>`;
        return;
    }
    styleBadge(badge,String(stuck.length),'#e11d48','#fff1f2','#fecdd3');
    panel.innerHTML=stuck.map((ex,i)=>`
        <div class="fade-in" style="animation-delay:${i*70}ms;background:#fff1f2;border:1px solid #fecdd3;border-radius:12px;padding:14px;margin-bottom:10px;">
            <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:8px;margin-bottom:8px;">
                <div style="flex:1;min-width:0;">
                    <p style="font-size:12px;font-weight:700;color:#9f1239;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${ex.title}</p>
                    <p style="font-size:10px;color:#fb7185;font-weight:500;margin-top:3px;">${ex.department} · Stuck ${ex.stuck_for}</p>
                </div>
                <span style="font-size:9px;font-weight:800;padding:2px 7px;border-radius:6px;background:#fecdd3;color:#e11d48;flex-shrink:0;">STUCK</span>
            </div>
            <button onclick="openForceEnd(${ex.id},'${ex.title.replace(/'/g,"\\'")}','${ex.department}')"
                    style="width:100%;padding:8px;border-radius:10px;background:#e11d48;color:#fff;
                           font-size:11px;font-weight:700;border:none;cursor:pointer;"
                    onmouseenter="this.style.background='#be123c'" onmouseleave="this.style.background='#e11d48'">
                <i class="fa-solid fa-power-off" style="margin-right:6px;"></i>Force-End Exam
            </button>
        </div>`).join('');
}


// ============================================================
//  POLICY TOGGLE CONTROLS
// ============================================================
const POLICIES=[
    {id:'tab_switch',  label:'Tab-Switch Detection', desc:'Pause browser focus-loss flagging',   on:true },
    {id:'camera_check',label:'Camera Verification',  desc:'Require webcam for all candidates',  on:true },
    {id:'copy_paste',  label:'Copy-Paste Block',     desc:'Block clipboard access in exam UI',  on:true },
    {id:'lockdown',    label:'Full Lockdown Mode',   desc:'Isolate all candidate endpoints',     on:false},
];
const pState={};
POLICIES.forEach(p=>pState[p.id]=p.on);

function renderPolicies(){
    document.getElementById('policy-controls').innerHTML=POLICIES.map(p=>`
        <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;padding:10px 12px;border-radius:10px;background:#f8fafc;">
            <div style="flex:1;min-width:0;">
                <p style="font-size:12px;font-weight:700;color:#1e293b;">${p.label}</p>
                <p style="font-size:10px;color:#94a3b8;font-weight:500;margin-top:1px;">${p.desc}</p>
            </div>
            <div onclick="togglePolicy('${p.id}',this)" style="flex-shrink:0;cursor:pointer;"
                 title="${pState[p.id]?'Disable':'Enable'} ${p.label}">
                <div style="width:36px;height:20px;border-radius:999px;position:relative;
                            background:${pState[p.id]?'#2563eb':'#e2e8f0'};transition:background 0.25s;"
                     id="track-${p.id}">
                    <div id="thumb-${p.id}"
                         style="width:16px;height:16px;border-radius:50%;background:#fff;
                                position:absolute;top:2px;box-shadow:0 1px 3px rgba(0,0,0,0.20);
                                transition:transform 0.2s;
                                transform:translateX(${pState[p.id]?18:2}px);">
                    </div>
                </div>
            </div>
        </div>`).join('');
}

function togglePolicy(id){
    pState[id]=!pState[id];
    document.getElementById('track-'+id).style.background=pState[id]?'#2563eb':'#e2e8f0';
    document.getElementById('thumb-'+id).style.transform=`translateX(${pState[id]?18:2}px)`;
    const label=POLICIES.find(p=>p.id===id).label;
    showToast(pState[id]
        ?`<i class="fa-solid fa-toggle-on" style="color:#34d399;font-size:14px;"></i><span style="margin-left:8px;"><strong>${label}</strong> enabled system-wide</span>`
        :`<i class="fa-solid fa-toggle-off" style="color:#fbbf24;font-size:14px;"></i><span style="margin-left:8px;"><strong>${label}</strong> disabled for all active exams</span>`);
    // PRODUCTION: PATCH /api/superadmin/policies/{id} with { enabled: pState[id] }
}


// ============================================================
//  FORCE-END MODAL
// ============================================================
let feTarget={id:null,title:'',dept:''};
function openForceEnd(id,title,dept){
    feTarget={id,title,dept};
    document.getElementById('modal-exam-title').textContent=` "${title}"`;
    document.getElementById('modal-exam-dept').textContent=dept;
    document.getElementById('force-end-modal').style.display='flex';
}
function closeForceEnd(){document.getElementById('force-end-modal').style.display='none';}
async function executeForceEnd(){
    closeForceEnd();
    // PRODUCTION: await fetch(`/super-admin/exams/${feTarget.id}/force-end`,{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}});
    showToast(`<i class="fa-solid fa-power-off" style="color:#f87171;font-size:14px;"></i><span style="margin-left:8px;">Exam <strong>"${feTarget.title}"</strong> force-ended successfully.</span>`);
    setTimeout(syncExams,800);
}


// ============================================================
//  TOAST
// ============================================================
function showToast(html){
    const t=document.createElement('div');
    t.style.cssText=`position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;align-items:center;
        background:#0f172a;color:#fff;padding:14px 20px;border-radius:16px;font-size:13px;font-family:'Inter',sans-serif;
        border:1px solid #1e293b;box-shadow:0 16px 48px rgba(0,0,0,0.35);
        opacity:0;transition:opacity 0.3s,transform 0.3s;transform:translateY(10px);`;
    t.innerHTML=html;
    document.body.appendChild(t);
    requestAnimationFrame(()=>{t.style.opacity='1';t.style.transform='translateY(0)';});
    setTimeout(()=>{t.style.opacity='0';t.style.transform='translateY(10px)';setTimeout(()=>t.remove(),350);},4500);
}


// ============================================================
//  MOCK DATA  ← Replace with fetch() to your Laravel routes
//    GET {{ route('superadmin.exams.api') }} → { metrics, active_exams, departments, stuck_exams }
// ============================================================
function mockData(){
    return {
        metrics:{total:42,active:8,avg_flag_rate:4.7},
        active_exams:[
            {id:1001,title:'Physics Final — Cohort A',  department:'Science',    proctor:'Dr. Mitchell', students:28,flag_rate:2.1, duration:'1h 12m',status:'running', stuck:false},
            {id:1002,title:'Advanced Mathematics',      department:'Mathematics',proctor:'Prof. Okafor',  students:35,flag_rate:1.4, duration:'45m',    status:'running', stuck:false},
            {id:1003,title:'English Literature Essay',  department:'Humanities', proctor:'Ms. Torres',   students:22,flag_rate:11.4,duration:'2h 03m', status:'at risk', stuck:false},
            {id:1004,title:'Intro to Computer Science', department:'Technology', proctor:'Mr. Chen',     students:19,flag_rate:0.5, duration:'30m',    status:'running', stuck:false},
            {id:1005,title:'Biology Midterm Exam',      department:'Science',    proctor:'Dr. Nwosu',    students:41,flag_rate:3.2, duration:'1h 40m', status:'running', stuck:false},
            {id:1006,title:'History — World War II',    department:'Humanities', proctor:'Ms. Patel',    students:17,flag_rate:0.0, duration:'55m',    status:'running', stuck:false},
            {id:1007,title:'Accounting Principles',     department:'Business',   proctor:'Mr. Santos',   students:30,flag_rate:7.8, duration:'3h 12m', status:'critical',stuck:true },
            {id:1008,title:'Chemistry Lab Assessment',  department:'Science',    proctor:'Dr. Kim',      students:25,flag_rate:2.9, duration:'1h 05m', status:'running', stuck:false},
        ],
        departments:[
            {name:'Science',    exam_count:12,live:3,students:94, flag_rate:2.4},
            {name:'Mathematics',exam_count:8, live:1,students:35, flag_rate:1.4},
            {name:'Humanities', exam_count:7, live:2,students:39, flag_rate:9.2},
            {name:'Technology', exam_count:6, live:1,students:19, flag_rate:0.5},
            {name:'Business',   exam_count:5, live:1,students:30, flag_rate:7.8},
            {name:'Health Sci.',exam_count:4, live:0,students:0,  flag_rate:0.0},
        ],
        stuck_exams:[
            {id:1007,title:'Accounting Principles',department:'Business',stuck_for:'47m with no updates'},
        ]
    };
}


// ============================================================
//  MAIN SYNC
// ============================================================
function syncExams(){
    // PRODUCTION: const data = await (await fetch("{{ route('superadmin.exams.api') }}")).json();
    const data=mockData();
    const m=data.metrics;
    setMetric('metric-total',  m.total);
    setMetric('metric-active', m.active);
    setMetric('metric-avg-flag',m.avg_flag_rate+'%');
    updateAvgFlagBar(m.avg_flag_rate);
    document.getElementById('active-exams-count').textContent=m.active+' active';
    renderActiveExams(data.active_exams);
    renderDeptTable(data.departments);
    renderStuckExams(data.stuck_exams);
}


// ============================================================
//  INIT
// ============================================================
renderPolicies();
syncExams();
</script>
</body>
</html>