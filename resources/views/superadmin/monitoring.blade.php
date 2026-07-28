<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Live Monitoring — ExamSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>
    <style>
        @keyframes ping-slow { 75%,100%{transform:scale(2.2);opacity:0} }
        .ping-slow { animation: ping-slow 2s cubic-bezier(0,0,.2,1) infinite; }
        @keyframes countUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .count-animate { animation: countUp 0.4s ease-out forwards; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { opacity:0; animation: fadeIn 0.4s ease-out forwards; }
        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}
        .load-bar { transition: width 0.7s ease, background 0.4s ease; }
        .teacher-card { transition: box-shadow 0.25s, transform 0.25s; }
        .teacher-card:hover { box-shadow: 0 10px 28px rgba(148,163,184,0.20); transform: translateY(-2px); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" style="font-family:'Inter',sans-serif;">
<div class="flex min-h-screen">
    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-20"
           style="box-shadow: 4px 0 24px rgba(148,163,184,0.08);">
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
        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-gauge-high text-xs text-slate-400"></i></span><span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-desktop text-xs text-white"></i></span>
                <span class="flex-1">Live Monitoring</span>
                <span class="text-[9px] bg-rose-500 text-white font-bold px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
            </a>
            <a href="{{ route('superadmin.exams.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-file-signature text-xs text-slate-400"></i></span><span>Exams Oversight</span>
            </a>
            <a href="{{ route('superadmin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-chart-line text-xs text-slate-400"></i></span><span>Reports & Analytics</span>
            </a>
            <div class="pt-4 pb-1"><p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2">Root Access</p></div>
            <a href="{{ route('superadmin.admins.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-users text-xs text-slate-400"></i></span><span>User Management</span>
            </a>
            <a href="{{ route('superadmin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-shield-halved text-xs text-slate-400"></i></span><span>Audit Trails</span>
            </a>
            <a href="{{ route('superadmin.backups.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-database text-xs text-slate-400"></i></span><span>Database & Backup</span>
            </a>
        </nav>
        <div class="p-3 border-t border-slate-100 flex-shrink-0">
            <a href="{{ route('superadmin.settings.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm transition-all duration-200 mb-1">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-sliders text-xs text-slate-400"></i></span><span>Global Settings</span>
            </a>
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <img class="w-8 h-8 rounded-lg object-cover flex-shrink-0" src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name ?? 'Super Admin') . '&background=3b82f6&color=fff&size=64' }}" alt="{{ Auth::user()->full_name ?? 'Super Admin' }}">
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->full_name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Super Admin · Root</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-rose-50 hover:text-rose-500 text-slate-400 transition-all" title="Logout"><i class="fa-solid fa-power-off text-xs"></i></button>
                </form>
            </div>
        </div>
    </aside>
    {{-- ===================== MAIN CONTENT ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
        {{-- TOP BAR --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Live Monitoring</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Real-time infrastructure & proctoring health</p>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span>Updated: </span><span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>All Systems Operational
                </div>
                <div id="poll-mode-badge" class="flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border {{ $liveSessions > 0 ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-400 border-slate-100' }}">
                    <i class="fa-solid {{ $liveSessions > 0 ? 'fa-bolt' : 'fa-moon' }} text-[10px]"></i>
                    <span id="poll-mode-label">{{ $liveSessions > 0 ? 'Real-time' : 'Idle' }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-100 px-3 py-1.5 rounded-lg" style="box-shadow:0 1px 4px rgba(148,163,184,0.08);">
                    <i id="refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                    <span>Refresh in</span>
                    <span id="refresh-countdown" class="font-mono font-bold text-slate-700 w-4 text-center">{{ $liveSessions > 0 ? 5 : 30 }}</span><span>s</span>
                </div>
            </div>
        </header>
        {{-- PAGE BODY --}}
        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:28px;">
            {{-- Info Banner --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 text-blue-800 rounded-2xl px-5 py-4" style="box-shadow:0 1px 4px rgba(59,130,246,0.07);">
                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-900 mb-0.5">Scope: Infrastructure & Capacity Only</p>
                    <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
                        Shows server load, live session counts, and network latency. Individual student webcams and screen shares are scoped to Instructors/Proctors inside their console hubs.
                    </p>
                </div>
            </div>
            {{-- ========== METRIC CARDS (REAL DATA) ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Total Live Sessions --}}
                <div class="bg-white rounded-2xl border {{ $liveSessions > 0 ? 'border-rose-100' : 'border-slate-100' }} p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl {{ $liveSessions > 0 ? 'bg-rose-50' : 'bg-slate-50' }} flex items-center justify-center">
                            <i class="fa-solid fa-tower-broadcast {{ $liveSessions > 0 ? 'text-rose-500' : 'text-slate-400' }} text-sm"></i>
                        </div>
                        @if($liveSessions > 0)
                        <span class="relative flex" style="width:10px;height:10px;">
                            <span class="ping-slow absolute inline-flex rounded-full bg-rose-400 opacity-75" style="width:100%;height:100%;"></span>
                            <span class="relative inline-flex rounded-full bg-rose-500" style="width:10px;height:10px;"></span>
                        </span>
                        @else
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">Idle</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Live Sessions</p>
                        <p id="metric-sessions" class="text-3xl font-black {{ $liveSessions > 0 ? 'text-rose-600' : 'text-slate-900' }} leading-none tabular-nums count-animate">{{ $liveSessions }}</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $liveSessions === 0 ? 'No active sessions right now' : 'Active across the server' }}</p>
                    </div>
                </div>
                {{-- Server Load --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                            <i class="fa-solid fa-microchip text-amber-500 text-sm"></i>
                        </div>
                        <span id="avg-load-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ $serverLoad < 50 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : ($serverLoad < 80 ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-rose-600 bg-rose-50 border-rose-100') }}">
                            {{ $serverLoad < 50 ? 'Stable' : ($serverLoad < 80 ? 'Elevated' : 'Critical') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Server CPU Load</p>
                        <p id="metric-avg-load" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $serverLoad }}%</p>
                        <div class="mt-2 rounded-full overflow-hidden bg-slate-100" style="height:5px;">
                            <div id="avg-load-bar" class="h-full rounded-full load-bar {{ $serverLoad < 50 ? 'bg-emerald-400' : ($serverLoad < 80 ? 'bg-amber-400' : 'bg-rose-400') }}" style="width:{{ $serverLoad }}%;"></div>
                        </div>
                    </div>
                </div>
                {{-- DB Latency --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center">
                            <i class="fa-solid fa-wifi text-violet-500 text-sm"></i>
                        </div>
                        <span id="latency-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ $dbLatency < 100 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : ($dbLatency < 250 ? 'text-blue-600 bg-blue-50 border-blue-100' : 'text-amber-600 bg-amber-50 border-amber-100') }}">
                            {{ $dbLatency < 100 ? 'Excellent' : ($dbLatency < 250 ? 'Good' : 'Fair') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">DB Latency</p>
                        <p id="metric-latency" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $dbLatency }}ms</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Database round-trip time</p>
                    </div>
                </div>
                {{-- Server Status --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                            <i class="fa-solid fa-server text-emerald-500 text-sm"></i>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full">Online</span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">App Server</p>
                        <p id="metric-nodes" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">1 / 1</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $nodeInfo['name'] }}</p>
                    </div>
                </div>
            </div>
            {{-- ========== TEACHER / PROCTOR LIVE MONITOR ========== --}}
            <div class="bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-50 flex items-center justify-center">
                            <i class="fa-solid fa-chalkboard-user text-indigo-500 text-sm"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">Teacher / Proctor Live Monitor</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Proctors currently supervising active exam sessions</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span id="teacher-count-badge" class="text-[10px] font-bold bg-indigo-50 text-indigo-600 border border-indigo-100 px-2.5 py-0.5 rounded-full">{{ count($activeProctors) }}</span>
                        <span class="text-[11px] text-slate-400 font-medium">proctors online</span>
                    </div>
                </div>
                <div id="teacher-grid" class="p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @if(count($activeProctors) === 0)
                    <div style="grid-column:1/-1;" class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                            <i class="fa-solid fa-chalkboard-user text-slate-300 text-2xl"></i>
                        </div>
                        <p class="text-sm font-bold text-slate-400 mb-1">No Active Proctors</p>
                        <p class="text-xs text-slate-300 max-w-xs">When teachers start proctoring exams, they will appear here in real time.</p>
                    </div>
                    @else
                    @php
                        $gradients = [
                            'linear-gradient(135deg,#3b82f6,#6366f1)', 'linear-gradient(135deg,#10b981,#059669)',
                            'linear-gradient(135deg,#f59e0b,#d97706)', 'linear-gradient(135deg,#ef4444,#dc2626)',
                            'linear-gradient(135deg,#8b5cf6,#7c3aed)', 'linear-gradient(135deg,#06b6d4,#0284c7)',
                        ];
                    @endphp
                    @foreach($activeProctors as $i => $proctor)
                    @php
                        $grad = $gradients[$i % count($gradients)];
                        $initials = collect(explode(' ', $proctor['name']))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                        $stColor = match($proctor['status']) {
                            'active'   => ['bg' => '#ecfdf5', 'color' => '#059669', 'border' => '#a7f3d0', 'dot' => '#10b981'],
                            'flagging' => ['bg' => '#fffbeb', 'color' => '#d97706', 'border' => '#fde68a', 'dot' => '#f59e0b'],
                            'idle'     => ['bg' => '#f8fafc', 'color' => '#64748b', 'border' => '#e2e8f0', 'dot' => '#94a3b8'],
                            default    => ['bg' => '#eff6ff', 'color' => '#2563eb', 'border' => '#bfdbfe', 'dot' => '#3b82f6'],
                        };
                    @endphp
                    <div class="teacher-card fade-in rounded-xl border border-slate-100 p-4 cursor-default bg-white" style="animation-delay:{{ $i * 0.07 }}s; box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                        <div class="flex items-center gap-3 mb-3.5">
                            <div class="w-11 h-11 rounded-xl flex-shrink-0 flex items-center justify-center text-sm font-extrabold text-white" style="background:{{ $grad }};">{{ $initials }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-[13px] font-bold text-slate-900 truncate">{{ $proctor['name'] }}</p>
                                <p class="text-[10px] text-slate-400 font-medium mt-0.5">{{ $proctor['role'] }}</p>
                            </div>
                            <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full border flex items-center gap-1 flex-shrink-0"
                                  style="background:{{ $stColor['bg'] }}; color:{{ $stColor['color'] }}; border-color:{{ $stColor['border'] }};">
                                <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:{{ $stColor['dot'] }};"></span>
                                {{ ucfirst($proctor['status']) }}
                            </span>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2.5 mb-3">
                            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Current Exam</p>
                            <p class="text-xs font-bold text-slate-800 truncate">{{ $proctor['exam'] }}</p>
                            <p class="text-[10px] text-indigo-500 font-semibold mt-1"><i class="fa-solid fa-building-columns mr-1"></i>{{ $proctor['department'] ?? 'General Academic' }}</p>
                        </div>
                        <div class="flex gap-2.5">
                            <div class="flex-1 text-center bg-emerald-50 rounded-lg py-2">
                                <p class="text-base font-extrabold text-emerald-600 leading-none">{{ $proctor['students'] }}</p>
                                <p class="text-[9px] font-semibold text-emerald-300 mt-1">Students</p>
                            </div>
                            <div class="flex-1 text-center bg-blue-50 rounded-lg py-2">
                                <p class="text-base font-extrabold text-blue-600 leading-none">{{ $proctor['flags'] }}</p>
                                <p class="text-[9px] font-semibold text-blue-300 mt-1">Flags</p>
                            </div>
                            <div class="flex-1 text-center bg-purple-50 rounded-lg py-2">
                                <p class="text-xs font-extrabold text-purple-600 leading-none font-mono">{{ $proctor['duration'] }}</p>
                                <p class="text-[9px] font-semibold text-purple-300 mt-1">Running</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
            {{-- ========== NODE TABLE + ALERTS ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                {{-- Node Table --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center"><i class="fa-solid fa-network-wired text-slate-500 text-sm"></i></div>
                            <h3 class="font-bold text-sm text-slate-900">Server Node</h3>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] text-slate-400">
                            <i class="fa-solid fa-rotate text-slate-300 text-xs"></i><span>Auto-polling: 10s</span>
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
                            <tbody id="node-table-body">
                                @php
                                    $loadColor = $nodeInfo['load'] < 50 ? '#34d399' : ($nodeInfo['load'] < 80 ? '#fbbf24' : '#f43f5e');
                                    $stMap = [
                                        'healthy'  => ['bg'=>'#ecfdf5','color'=>'#059669','border'=>'#a7f3d0'],
                                        'warning'  => ['bg'=>'#fffbeb','color'=>'#d97706','border'=>'#fde68a'],
                                        'critical' => ['bg'=>'#fff1f2','color'=>'#e11d48','border'=>'#fecdd3'],
                                    ];
                                    $st = $stMap[$nodeInfo['status']] ?? $stMap['healthy'];
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="px-6 py-3.5 font-bold text-slate-900 text-[13px]">
                                        <div class="flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:{{ $loadColor }};"></span>
                                            {{ $nodeInfo['name'] }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-slate-600 font-semibold">{{ $nodeInfo['sessions'] }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden">
                                                <div class="h-full rounded-full load-bar" style="background:{{ $loadColor }}; width:{{ $nodeInfo['load'] }}%;"></div>
                                            </div>
                                            <span class="font-mono text-[11px] font-bold text-slate-600">{{ $nodeInfo['load'] }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5 font-mono text-xs text-slate-500 font-semibold">{{ $nodeInfo['latency'] }} ms</td>
                                    <td class="px-4 py-3.5 text-center">
                                        <span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-md border"
                                              style="background:{{ $st['bg'] }}; color:{{ $st['color'] }}; border-color:{{ $st['border'] }};">
                                            {{ ucfirst($nodeInfo['status']) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                {{-- System Alerts --}}
                <div class="bg-white rounded-2xl border border-slate-100 flex flex-col" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fa-solid fa-bell text-amber-500 text-sm"></i></div>
                            <h3 class="font-bold text-sm text-slate-900">System Alerts</h3>
                        </div>
                        <span id="alerts-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ count($systemAlerts) === 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100' }}">
                            {{ count($systemAlerts) }}
                        </span>
                    </div>
                    <div id="alerts-panel" class="flex-1 p-4 overflow-y-auto thin-scroll" style="max-height:340px;">
                        @if(count($systemAlerts) === 0)
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-3">
                                <i class="fa-solid fa-shield-halved text-emerald-400 text-xl"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-400">All Systems Nominal</p>
                            <p class="text-[11px] text-slate-300 mt-1">No active alerts to display</p>
                        </div>
                        @else
                        @foreach($systemAlerts as $i => $alert)
                        @php
                            $sev = match($alert['severity']) {
                                'critical' => ['icon'=>'fa-circle-xmark','bg'=>'#fff1f2','color'=>'#e11d48','border'=>'#fecdd3','iconColor'=>'#f43f5e'],
                                'warning'  => ['icon'=>'fa-triangle-exclamation','bg'=>'#fffbeb','color'=>'#92400e','border'=>'#fde68a','iconColor'=>'#f59e0b'],
                                default    => ['icon'=>'fa-circle-info','bg'=>'#eff6ff','color'=>'#1e40af','border'=>'#bfdbfe','iconColor'=>'#3b82f6'],
                            };
                        @endphp
                        <div class="fade-in flex items-start gap-2.5 p-3 rounded-xl mb-2 border"
                             style="animation-delay:{{ $i * 0.08 }}s; background:{{ $sev['bg'] }}; border-color:{{ $sev['border'] }};">
                            <i class="fa-solid {{ $sev['icon'] }} text-sm flex-shrink-0 mt-0.5" style="color:{{ $sev['iconColor'] }};"></i>
                            <div>
                                <p class="text-[11px] font-bold" style="color:{{ $sev['color'] }};">{{ $alert['title'] }}</p>
                                <p class="text-[10px] mt-0.5 leading-relaxed" style="color:{{ $sev['color'] }}; opacity:0.8;">{{ $alert['message'] }}</p>
                                <p class="text-[9px] mt-1 font-mono" style="color:{{ $sev['color'] }}; opacity:0.55;">{{ $alert['time'] }}</p>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <footer class="px-8 py-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-300">
            <span>© {{ date('Y') }} ExamSystem — Live Monitoring</span>
            <span id="footer-poll-text" class="font-mono">Real-time Engine · {{ $liveSessions > 0 ? '5s' : '30s (idle)' }} polling</span>
        </footer>
    </main>
</div>
{{-- ===================== REAL-TIME POLLING JS ===================== --}}
<script>
(function() {
    'use strict';
    // Adaptive polling: while at least one exam session is actually live,
    // poll fast for a true real-time feel. When nothing is happening, back
    // off to a slow idle interval instead of hammering the server for no
    // reason.
    const LIVE_INTERVAL = 5;
    const IDLE_INTERVAL  = 30;
    let isLive = {{ $liveSessions > 0 ? 'true' : 'false' }};
    let REFRESH_INTERVAL = isLive ? LIVE_INTERVAL : IDLE_INTERVAL;
    let countdown = REFRESH_INTERVAL;
    // ── Live clock ──
    function updateClock() {
        document.getElementById('live-clock').textContent =
            new Date().toLocaleTimeString('en-US', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    setInterval(updateClock, 1000);
    updateClock();
    // ── Countdown ──
    setInterval(() => {
        countdown--;
        document.getElementById('refresh-countdown').textContent = countdown;
        if (countdown <= 0) {
            countdown = REFRESH_INTERVAL;
            const icon = document.getElementById('refresh-icon');
            icon.classList.add('animate-spin');
            setTimeout(() => icon.classList.remove('animate-spin'), 700);
            fetchLiveData();
        }
    }, 1000);
    // ── Switch between real-time and idle polling modes ──
    function setPollMode(nowLive) {
        if (nowLive === isLive) return;
        isLive = nowLive;
        REFRESH_INTERVAL = isLive ? LIVE_INTERVAL : IDLE_INTERVAL;
        countdown = REFRESH_INTERVAL;

        const badge = document.getElementById('poll-mode-badge');
        const label = document.getElementById('poll-mode-label');
        const footer = document.getElementById('footer-poll-text');
        if (badge) {
            badge.className = 'flex items-center gap-1.5 text-xs font-semibold px-3 py-1.5 rounded-lg border ' +
                (isLive ? 'bg-rose-50 text-rose-600 border-rose-100' : 'bg-slate-50 text-slate-400 border-slate-100');
            badge.querySelector('i').className = 'fa-solid ' + (isLive ? 'fa-bolt' : 'fa-moon') + ' text-[10px]';
        }
        if (label) label.textContent = isLive ? 'Real-time' : 'Idle';
        if (footer) footer.textContent = 'Real-time Engine · ' + (isLive ? '5s' : '30s (idle)') + ' polling';
    }
    // ── Fetch real data ──
    function fetchLiveData() {
        fetch('{{ route("superadmin.monitoring.api") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            const m = data.metrics || {};
            setMetric('metric-sessions', m.total_sessions ?? 0);
            setMetric('metric-avg-load', (m.avg_load ?? 0) + '%');
            setMetric('metric-latency', (m.avg_latency_ms ?? 0) + 'ms');
            setMetric('metric-nodes', (m.nodes_online ?? 1) + ' / ' + (m.nodes_total ?? 1));
            updateLoadBar(m.avg_load ?? 0);
            updateLatencyBadge(m.avg_latency_ms ?? 0);
            renderNodeTable(data.nodes || []);
            renderTeacherGrid(data.teachers || []);
            renderAlerts(data.alerts || []);
            setPollMode((m.total_sessions ?? 0) > 0);
        })
        .catch(err => console.error('Monitoring poll failed:', err));
    }
    function setMetric(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        el.textContent = typeof value === 'number' ? value.toLocaleString() : value;
        el.classList.remove('count-animate');
        void el.offsetWidth;
        el.classList.add('count-animate');
    }
    function updateLoadBar(pct) {
        const bar = document.getElementById('avg-load-bar');
        const badge = document.getElementById('avg-load-badge');
        if (bar) {
            bar.style.width = pct + '%';
            bar.className = 'h-full rounded-full load-bar ' + (pct < 50 ? 'bg-emerald-400' : pct < 80 ? 'bg-amber-400' : 'bg-rose-400');
        }
        if (badge) {
            badge.textContent = pct < 50 ? 'Stable' : pct < 80 ? 'Elevated' : 'Critical';
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full border ' +
                (pct < 50 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : pct < 80 ? 'text-amber-600 bg-amber-50 border-amber-100' : 'text-rose-600 bg-rose-50 border-rose-100');
        }
    }
    function updateLatencyBadge(ms) {
        const b = document.getElementById('latency-badge');
        if (!b) return;
        if (ms < 100)      { b.textContent='Excellent'; b.className='text-[10px] font-bold px-2 py-0.5 rounded-full border text-emerald-600 bg-emerald-50 border-emerald-100'; }
        else if (ms < 250)  { b.textContent='Good';      b.className='text-[10px] font-bold px-2 py-0.5 rounded-full border text-blue-600 bg-blue-50 border-blue-100'; }
        else if (ms < 500)  { b.textContent='Fair';      b.className='text-[10px] font-bold px-2 py-0.5 rounded-full border text-amber-600 bg-amber-50 border-amber-100'; }
        else                { b.textContent='High';      b.className='text-[10px] font-bold px-2 py-0.5 rounded-full border text-rose-600 bg-rose-50 border-rose-100'; }
    }
    function renderNodeTable(nodes) {
        const tbody = document.getElementById('node-table-body');
        if (!tbody) return;
        if (!nodes.length) {
            tbody.innerHTML = '<tr><td colspan="5" class="py-8 text-center text-slate-400 text-xs">No nodes reported</td></tr>';
            return;
        }
        tbody.innerHTML = nodes.map(n => {
            const lc = n.load < 50 ? '#34d399' : n.load < 80 ? '#fbbf24' : '#f43f5e';
            const sm = { healthy:['#ecfdf5','#059669','#a7f3d0'], warning:['#fffbeb','#d97706','#fde68a'], critical:['#fff1f2','#e11d48','#fecdd3'] };
            const s = sm[n.status] || sm.healthy;
            return `<tr class="hover:bg-slate-50 transition-colors">
                <td class="px-6 py-3.5 font-bold text-slate-900 text-[13px]"><div class="flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:${lc}"></span>${esc(n.name)}</div></td>
                <td class="px-4 py-3.5 font-mono text-xs text-slate-600 font-semibold">${n.sessions}</td>
                <td class="px-4 py-3.5"><div class="flex items-center gap-2.5"><div class="w-20 h-1.5 rounded-full bg-slate-100 overflow-hidden"><div class="h-full rounded-full load-bar" style="background:${lc};width:${n.load}%"></div></div><span class="font-mono text-[11px] font-bold text-slate-600">${n.load}%</span></div></td>
                <td class="px-4 py-3.5 font-mono text-xs text-slate-500 font-semibold">${n.latency} ms</td>
                <td class="px-4 py-3.5 text-center"><span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-md border" style="background:${s[0]};color:${s[1]};border-color:${s[2]}">${n.status.charAt(0).toUpperCase()+n.status.slice(1)}</span></td>
            </tr>`;
        }).join('');
    }
    const GRADS = ['linear-gradient(135deg,#3b82f6,#6366f1)','linear-gradient(135deg,#10b981,#059669)','linear-gradient(135deg,#f59e0b,#d97706)','linear-gradient(135deg,#ef4444,#dc2626)','linear-gradient(135deg,#8b5cf6,#7c3aed)','linear-gradient(135deg,#06b6d4,#0284c7)'];
    function renderTeacherGrid(teachers) {
        const grid = document.getElementById('teacher-grid');
        const badge = document.getElementById('teacher-count-badge');
        if (!grid) return;
        badge.textContent = teachers.length;
        if (!teachers.length) {
            grid.innerHTML = `<div style="grid-column:1/-1" class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4"><i class="fa-solid fa-chalkboard-user text-slate-300 text-2xl"></i></div>
                <p class="text-sm font-bold text-slate-400 mb-1">No Active Proctors</p>
                <p class="text-xs text-slate-300 max-w-xs">When teachers start proctoring exams, they will appear here.</p></div>`;
            return;
        }
        grid.innerHTML = teachers.map((t, i) => {
            const init = t.name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
            const stm = { active:['#ecfdf5','#059669','#a7f3d0','#10b981'], flagging:['#fffbeb','#d97706','#fde68a','#f59e0b'], idle:['#f8fafc','#64748b','#e2e8f0','#94a3b8'] };
            const s = stm[t.status] || stm.active;
            return `<div class="teacher-card fade-in rounded-xl border border-slate-100 p-4 cursor-default bg-white" style="animation-delay:${i*70}ms;box-shadow:0 1px 4px rgba(148,163,184,0.06)">
                <div class="flex items-center gap-3 mb-3.5">
                    <div class="w-11 h-11 rounded-xl flex-shrink-0 flex items-center justify-center text-sm font-extrabold text-white" style="background:${GRADS[i%GRADS.length]}">${init}</div>
                    <div class="flex-1 min-w-0"><p class="text-[13px] font-bold text-slate-900 truncate">${esc(t.name)}</p><p class="text-[10px] text-slate-400 font-medium mt-0.5">${esc(t.role||'Proctor')}</p></div>
                    <span class="text-[9px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded-full border flex items-center gap-1 flex-shrink-0" style="background:${s[0]};color:${s[1]};border-color:${s[2]}"><span class="w-1.5 h-1.5 rounded-full inline-block" style="background:${s[3]}"></span>${t.status.charAt(0).toUpperCase()+t.status.slice(1)}</span>
                </div>
                <div class="bg-slate-50 rounded-lg p-2.5 mb-3"><p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Current Exam</p><p class="text-xs font-bold text-slate-800 truncate">${esc(t.exam)}</p><p class="text-[10px] text-indigo-500 font-semibold mt-1"><i class="fa-solid fa-building-columns mr-1"></i>${esc(t.department||'General Academic')}</p></div>
                <div class="flex gap-2.5">
                    <div class="flex-1 text-center bg-emerald-50 rounded-lg py-2"><p class="text-base font-extrabold text-emerald-600 leading-none">${t.students}</p><p class="text-[9px] font-semibold text-emerald-300 mt-1">Students</p></div>
                    <div class="flex-1 text-center bg-blue-50 rounded-lg py-2"><p class="text-base font-extrabold text-blue-600 leading-none">${t.flags||0}</p><p class="text-[9px] font-semibold text-blue-300 mt-1">Flags</p></div>
                    <div class="flex-1 text-center bg-purple-50 rounded-lg py-2"><p class="text-xs font-extrabold text-purple-600 leading-none font-mono">${esc(t.duration||'--')}</p><p class="text-[9px] font-semibold text-purple-300 mt-1">Running</p></div>
                </div></div>`;
        }).join('');
    }
    function renderAlerts(alerts) {
        const panel = document.getElementById('alerts-panel');
        const badge = document.getElementById('alerts-badge');
        if (!panel) return;
        badge.textContent = alerts.length;
        badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full border ' +
            (alerts.length === 0 ? 'bg-emerald-50 text-emerald-600 border-emerald-100' : 'bg-rose-50 text-rose-600 border-rose-100');
        if (!alerts.length) {
            panel.innerHTML = `<div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center mb-3"><i class="fa-solid fa-shield-halved text-emerald-400 text-xl"></i></div>
                <p class="text-xs font-bold text-slate-400">All Systems Nominal</p>
                <p class="text-[11px] text-slate-300 mt-1">No active alerts</p></div>`;
            return;
        }
        const SEV = {
            critical:{icon:'fa-circle-xmark',bg:'#fff1f2',color:'#e11d48',border:'#fecdd3',ic:'#f43f5e'},
            warning:{icon:'fa-triangle-exclamation',bg:'#fffbeb',color:'#92400e',border:'#fde68a',ic:'#f59e0b'},
            info:{icon:'fa-circle-info',bg:'#eff6ff',color:'#1e40af',border:'#bfdbfe',ic:'#3b82f6'}
        };
        panel.innerHTML = alerts.map((a,i) => {
            const s = SEV[a.severity] || SEV.info;
            return `<div class="fade-in flex items-start gap-2.5 p-3 rounded-xl mb-2 border" style="animation-delay:${i*80}ms;background:${s.bg};border-color:${s.border}">
                <i class="fa-solid ${s.icon} text-sm flex-shrink-0 mt-0.5" style="color:${s.ic}"></i>
                <div><p class="text-[11px] font-bold" style="color:${s.color}">${esc(a.title||'Alert')}</p>
                <p class="text-[10px] mt-0.5 leading-relaxed" style="color:${s.color};opacity:0.8">${esc(a.message)}</p>
                <p class="text-[9px] mt-1 font-mono" style="color:${s.color};opacity:0.55">${esc(a.time||'')}</p></div></div>`;
        }).join('');
    }
    function esc(s) { const d=document.createElement('div'); d.appendChild(document.createTextNode(s||'')); return d.innerHTML; }
})();
</script>
</body>
</html>