<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forensic Audit Trails — ExamSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>
    <style>
        @keyframes ping-slow{75%,100%{transform:scale(2.2);opacity:0;}}
        .ping-slow{animation:ping-slow 2s cubic-bezier(0,0,.2,1) infinite;}
        @keyframes countUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .count-animate{animation:countUp 0.4s ease-out forwards;}
        @keyframes fadeIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
        .fade-in{opacity:0;animation:fadeIn 0.28s ease-out forwards;}
        @keyframes slideDown{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}
        .slide-down{animation:slideDown 0.3s cubic-bezier(0.22,1,0.36,1) forwards;}
        @keyframes newPulse{0%,100%{box-shadow:0 0 0 0 rgba(59,130,246,0.4)}50%{box-shadow:0 0 0 6px rgba(59,130,246,0)}}
        .new-pulse{animation:newPulse 2s ease-in-out infinite;}
        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}
        .log-row:hover{background:#f8fafc;}
        .tab-btn{padding:7px 16px;border-radius:9px;font-size:11px;font-weight:700;cursor:pointer;
                 letter-spacing:0.04em;text-transform:uppercase;border:none;transition:all 0.2s;}
        .tab-btn.active{background:#2563eb;color:#fff;box-shadow:0 3px 10px rgba(37,99,235,0.26);}
        .tab-btn:not(.active){background:transparent;color:#64748b;}
        .tab-btn:not(.active):hover{background:#fff;color:#1e293b;}
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
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-gauge-high text-xs text-slate-400"></i></span><span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-desktop text-xs text-slate-400"></i></span><span>Live Monitoring</span>
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
            <a href="{{ route('superadmin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-shield-halved text-xs text-white"></i></span>
                <span class="flex-1">Audit Trails</span>
                <span class="text-[9px] bg-white bg-opacity-20 text-white font-bold px-2 py-0.5 rounded-full">ROOT</span>
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
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#3b82f6,#6366f1);"><i class="fa-solid fa-user-astronaut text-white text-xs"></i></div>
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
    {{-- ===================== MAIN ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Forensic Audit Trails</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Immutable ledger · every Super Admin &amp; Admin action, system-wide</p>
            </div>
            <div class="flex items-center gap-3 ml-auto flex-wrap">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-slate-100 text-slate-500 border border-slate-200 px-3 py-1.5 rounded-lg">
                    <i class="fa-solid fa-lock text-slate-400 text-xs"></i> Read-Only · Immutable
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-blue-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-blue-500" style="width:8px;height:8px;"></span>
                    </span>
                    Polling in <span id="poll-countdown" class="font-mono font-black ml-1 mr-0.5 text-blue-900 w-3 text-center">4</span>s
                </div>
            </div>
        </header>
        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:20px;">
            {{-- NEW EVENTS BANNER --}}
            <div id="new-events-banner" class="slide-down hidden items-center justify-between gap-4 rounded-2xl px-5 py-3.5 border"
                 style="background:linear-gradient(135deg,#eff6ff,#dbeafe);border-color:#bfdbfe;box-shadow:0 4px 16px rgba(59,130,246,0.12);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-blue-500 flex items-center justify-center flex-shrink-0 new-pulse"><i class="fa-solid fa-bolt text-white text-xs"></i></div>
                    <p class="text-sm font-bold text-blue-900">
                        <span id="new-events-count" class="text-lg font-black text-blue-700">0</span>
                        <span id="new-events-label"> new events captured — click Sync View to load</span>
                    </p>
                </div>
                <button onclick="flushNewEvents()" class="flex items-center gap-2 text-xs font-bold text-white px-4 py-2 rounded-xl" style="background:#2563eb;box-shadow:0 3px 10px rgba(37,99,235,0.28);">
                    <i class="fa-solid fa-rotate text-xs"></i> Sync View
                </button>
            </div>
            {{-- SCOPE BANNER --}}
            <div class="flex items-start gap-3 bg-violet-50 border border-violet-100 rounded-2xl px-5 py-4" style="box-shadow:0 1px 4px rgba(124,58,237,0.07);">
                <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0 mt-0.5"><i class="fa-solid fa-shield-halved text-violet-600 text-sm"></i></div>
                <div>
                    <p class="text-xs font-bold text-violet-900 mb-0.5">Unified Immutable Ledger — Super Admin Only View</p>
                    <p class="text-[11px] text-violet-700 font-medium leading-relaxed">
                        This view exposes the <strong>unfiltered system-wide trail</strong> across all Super Admins and Admins —
                        including role changes, force-ends, policy overrides, and backup operations.
                        <strong>This log is append-only — no entry can be edited or deleted.</strong>
                    </p>
                </div>
            </div>
            {{-- METRIC CARDS --}}
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);"><i class="fa-solid fa-list-check text-blue-500 text-sm"></i></div>
                    <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Events Today</p><p id="card-today" class="text-2xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $eventsToday }}</p></div>
                </div>
                <div class="bg-white rounded-2xl border border-rose-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);background:linear-gradient(135deg,#fff 70%,rgba(255,241,242,0.4) 100%);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(244,63,94,0.10)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#fff1f2,#fecdd3);"><i class="fa-solid fa-triangle-exclamation text-rose-500 text-sm"></i></div>
                    <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Critical Actions</p><p id="card-critical" class="text-2xl font-black text-rose-600 leading-none tabular-nums count-animate">{{ $criticalCount }}</p></div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);"><i class="fa-solid fa-user-secret text-violet-500 text-sm"></i></div>
                    <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Unique Actors</p><p id="card-actors" class="text-2xl font-black text-violet-600 leading-none tabular-nums count-animate">{{ $uniqueActors }}</p></div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);"><i class="fa-regular fa-clock text-emerald-500 text-sm"></i></div>
                    <div><p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Last Event</p><p id="card-last" class="text-sm font-black text-emerald-600 leading-none">{{ $lastEventTime }}</p></div>
                </div>
            </div>
            {{-- FILTERS + TABLE --}}
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden flex flex-col" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                <div class="px-6 py-4 border-b border-slate-100 flex flex-wrap items-center gap-4">
                    <div class="flex items-center gap-1 p-1 rounded-xl flex-shrink-0" style="background:#f1f5f9;">
                        <button class="tab-btn active" data-scope="all" onclick="setTab('all',this)">All Events</button>
                        <button class="tab-btn" data-scope="root" onclick="setTab('root',this)">Root (Super Admin)</button>
                        <button class="tab-btn" data-scope="admin" onclick="setTab('admin',this)">Admin</button>
                        <button class="tab-btn" data-scope="critical" onclick="setTab('critical',this)">Critical Only</button>
                    </div>
                    <div class="flex items-center gap-3 ml-auto flex-wrap">
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <i class="fa-solid fa-user text-slate-300 text-xs"></i>
                            <input id="search-actor" type="text" placeholder="Search by actor..." oninput="renderLogs()" class="text-xs font-medium text-slate-700 bg-transparent outline-none placeholder-slate-300" style="width:160px;">
                        </div>
                        <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2">
                            <i class="fa-solid fa-terminal text-slate-300 text-xs"></i>
                            <input id="search-action" type="text" placeholder="Search action..." oninput="renderLogs()" class="text-xs font-medium text-slate-700 bg-transparent outline-none placeholder-slate-300" style="width:140px;">
                        </div>
                        <button onclick="clearFilters()" class="text-xs font-bold text-slate-400 hover:text-slate-700 px-3 py-2 rounded-xl hover:bg-slate-100 transition-all"><i class="fa-solid fa-xmark mr-1"></i>Clear</button>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Actor</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Action</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Resource</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Severity</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Timestamp · IP</th>
                            </tr>
                        </thead>
                        <tbody id="log-table-body" class="divide-y divide-slate-50"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 border-t border-slate-100 bg-slate-50 flex-wrap gap-2">
                    <p id="table-footer" class="text-[11px] text-slate-400 font-medium"></p>
                    <div class="flex items-center gap-2 text-[11px] text-slate-400 font-medium">
                        <i class="fa-solid fa-lock text-slate-300 text-xs"></i>
                        Append-only — no entries can be modified or deleted
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>
<script>
(function() {
    'use strict';
    // ── Severity classifier ──
    function getSeverity(action) {
        const a = (action || '').toLowerCase();
        if (a.includes('force_end') || a.includes('emergency') || a.includes('delete') || a.includes('wipe'))
            return { key:'critical', label:'Critical', bg:'#fff1f2', color:'#e11d48', border:'#fecdd3', icon:'fa-circle-xmark', iconC:'#f43f5e' };
        if (a.includes('settings') || a.includes('policy') || a.includes('role') || a.includes('super'))
            return { key:'root', label:'Root', bg:'#f5f3ff', color:'#6d28d9', border:'#ede9fe', icon:'fa-shield-halved', iconC:'#7c3aed' };
        if (a.includes('create') || a.includes('update') || a.includes('suspend') || a.includes('activate') || a.includes('toggle'))
            return { key:'admin', label:'Admin', bg:'#eff6ff', color:'#1d4ed8', border:'#bfdbfe', icon:'fa-shield', iconC:'#3b82f6' };
        return { key:'system', label:'System', bg:'#f8fafc', color:'#64748b', border:'#e2e8f0', icon:'fa-gears', iconC:'#94a3b8' };
    }
    const ACTOR_GRADS = [
        'linear-gradient(135deg,#6d28d9,#7c3aed)', 'linear-gradient(135deg,#2563eb,#1d4ed8)',
        'linear-gradient(135deg,#0284c7,#0369a1)', 'linear-gradient(135deg,#059669,#047857)',
        'linear-gradient(135deg,#d97706,#b45309)', 'linear-gradient(135deg,#dc2626,#b91c1c)',
    ];
    const actorGradMap = {}; let gradIdx = 0;
    function actorGrad(name) { if (!actorGradMap[name]) actorGradMap[name] = ACTOR_GRADS[gradIdx++ % ACTOR_GRADS.length]; return actorGradMap[name]; }
    function getInitials(name) { return (name || '?').split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase(); }
    function esc(s) { const d = document.createElement('div'); d.appendChild(document.createTextNode(s || '')); return d.innerHTML; }
    // ── Clock ──
    function updateClock() {
        document.getElementById('live-clock').textContent =
            new Date().toLocaleTimeString('en-US', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateClock(); setInterval(updateClock, 1000);
    // ── Poll countdown (4s) ──
    let pollCount = 4;
    const pollEl = document.getElementById('poll-countdown');
    setInterval(() => {
        pollCount--;
        if (pollCount <= 0) { pollCount = 4; pollAuditLogs(); }
        pollEl.textContent = pollCount;
    }, 1000);
    // ── State ──
    let allLogs   = @json($logs);
    let cachedNew = [];
    let activeTab = 'all';
    // ── Metric cards ──
    function setMetric(id, value) {
        const el = document.getElementById(id);
        if (!el) return;
        const display = typeof value === 'number' ? value.toLocaleString() : value;
        if (el.textContent !== String(display)) {
            el.textContent = display;
            el.classList.remove('count-animate'); void el.offsetWidth; el.classList.add('count-animate');
        }
    }
    function updateCards() {
        const today = new Date().toISOString().slice(0, 10);
        const todayLogs  = allLogs.filter(l => (l.created_at || '').startsWith(today));
        const criticals  = allLogs.filter(l => getSeverity(l.action).key === 'critical');
        const actors     = [...new Set(allLogs.map(l => l.operator))];
        setMetric('card-today', todayLogs.length);
        setMetric('card-critical', criticals.length);
        setMetric('card-actors', actors.length);
        if (allLogs.length > 0) {
            const latest = allLogs[0];
            document.getElementById('card-last').textContent = latest.created_at ? latest.created_at.slice(11, 16) : '—';
        }
    }
    // ── Filter & render ──
    function filteredLogs() {
        const actor  = (document.getElementById('search-actor').value || '').toLowerCase();
        const action = (document.getElementById('search-action').value || '').toLowerCase();
        return allLogs.filter(log => {
            const sv = getSeverity(log.action);
            const matchTab = activeTab === 'all'
                || (activeTab === 'root' && (log.role === 'super_admin' || sv.key === 'root'))
                || (activeTab === 'admin' && log.role === 'admin')
                || (activeTab === 'critical' && sv.key === 'critical');
            return matchTab && (log.operator || '').toLowerCase().includes(actor) && (log.action || '').toLowerCase().includes(action);
        });
    }
    window.renderLogs = function() {
        const tbody = document.getElementById('log-table-body');
        const logs  = filteredLogs();
        document.getElementById('table-footer').textContent = `Showing ${logs.length} of ${allLogs.length} events`;
        if (!logs.length) {
            tbody.innerHTML = `<tr><td colspan="5" style="padding:48px;text-align:center;color:#94a3b8;">
                <i class="fa-solid fa-rectangle-list" style="font-size:28px;display:block;margin-bottom:10px;color:#e2e8f0;"></i>
                <p style="font-size:12px;font-weight:600;">${allLogs.length === 0 ? 'No audit events recorded yet — perform actions to see them here' : 'No events match your current filters'}</p>
                </td></tr>`;
            return;
        }
        tbody.innerHTML = logs.map((log, i) => {
            const sv   = getSeverity(log.action);
            const grad = actorGrad(log.operator);
            const init = getInitials(log.operator);
            const rolePill = log.role === 'super_admin'
                ? `<span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:4px;background:#f5f3ff;color:#6d28d9;border:1px solid #ede9fe;margin-left:5px;">ROOT</span>`
                : `<span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:4px;background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;margin-left:5px;">ADMIN</span>`;
            return `<tr class="log-row fade-in" style="animation-delay:${i*30}ms;transition:background 0.12s;cursor:default;">
                <td style="padding:14px 24px;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        <div style="width:36px;height:36px;border-radius:10px;flex-shrink:0;background:${grad};display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:800;color:#fff;">${init}</div>
                        <div><div style="display:flex;align-items:center;gap:4px;"><p style="font-size:13px;font-weight:700;color:#0f172a;">${esc(log.operator)}</p>${rolePill}</div></div>
                    </div>
                </td>
                <td style="padding:14px 16px;max-width:220px;">
                    <span style="font-size:11px;font-weight:700;font-family:'JetBrains Mono',monospace;color:#2563eb;background:#eff6ff;border:1px solid #bfdbfe;padding:3px 8px;border-radius:6px;display:inline-block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:200px;">${esc(log.action)}</span>
                </td>
                <td style="padding:14px 16px;font-size:12px;color:#475569;font-weight:500;max-width:200px;">
                    <span style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis;display:block;max-width:190px;" title="${esc(log.resource)}">${esc(log.resource)}</span>
                </td>
                <td style="padding:14px 16px;text-align:center;">
                    <span style="display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;padding:4px 10px;border-radius:8px;border:1px solid;background:${sv.bg};color:${sv.color};border-color:${sv.border};">
                        <i class="fa-solid ${sv.icon}" style="color:${sv.iconC};font-size:9px;"></i>${sv.label}
                    </span>
                </td>
                <td style="padding:14px 16px;text-align:right;">
                    <p style="font-size:11px;font-weight:700;color:#475569;font-family:'JetBrains Mono',monospace;">${esc(log.created_at)}</p>
                    <p style="font-size:10px;color:#94a3b8;font-weight:500;margin-top:2px;font-family:'JetBrains Mono',monospace;"><i class="fa-solid fa-location-dot" style="font-size:9px;margin-right:3px;"></i>${esc(log.ip)}</p>
                </td>
            </tr>`;
        }).join('');
    };
    // ── Tabs ──
    window.setTab = function(scope, btn) {
        activeTab = scope;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderLogs();
    };
    window.clearFilters = function() {
        document.getElementById('search-actor').value = '';
        document.getElementById('search-action').value = '';
        activeTab = 'all';
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        document.querySelector('[data-scope="all"]').classList.add('active');
        renderLogs();
    };
    // ── New events banner ──
    window.flushNewEvents = function() {
        if (cachedNew.length > 0) { allLogs = cachedNew; cachedNew = []; updateCards(); renderLogs(); }
        document.getElementById('new-events-banner').style.display = 'none';
    };
    // ── Poll API (real data, every 4s) ──
    function pollAuditLogs() {
        fetch('{{ route("superadmin.audit-logs.api") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const newLogs = data.logs || [];
            if (newLogs.length > allLogs.length && allLogs.length > 0) {
                // New events arrived — show banner
                const diff = newLogs.length - allLogs.length;
                cachedNew = newLogs;
                const banner = document.getElementById('new-events-banner');
                document.getElementById('new-events-count').textContent = diff;
                document.getElementById('new-events-label').textContent = ` new event${diff > 1 ? 's' : ''} captured — click Sync View to load`;
                banner.style.display = 'flex';
            } else if (allLogs.length === 0 && newLogs.length > 0) {
                // First data load
                allLogs = newLogs;
                updateCards();
                renderLogs();
            }
            // Update metric cards from server
            if (data.events_today !== undefined) setMetric('card-today', data.events_today);
            if (data.critical !== undefined) setMetric('card-critical', data.critical);
            if (data.unique_actors !== undefined) setMetric('card-actors', data.unique_actors);
            if (data.last_event) document.getElementById('card-last').textContent = data.last_event;
        })
        .catch(err => console.error('Audit poll failed:', err));
    }
    // ── INIT ──
    updateCards();
    renderLogs();
})();
</script>
</body>
</html>