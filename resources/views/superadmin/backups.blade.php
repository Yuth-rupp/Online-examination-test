<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Database & Backup — {{ $platformName }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>

    <style>
        @keyframes ping-slow{75%,100%{transform:scale(2.2);opacity:0;}}
        .ping-slow{animation:ping-slow 2s cubic-bezier(0,0,.2,1) infinite;}

        @keyframes pulse-live{0%,100%{opacity:1}50%{opacity:0.5}}
        .pulse-live{animation:pulse-live 1.5s ease-in-out infinite;}

        @keyframes shimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skeleton{background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);
                  background-size:1000px 100%;animation:shimmer 1.5s infinite linear;border-radius:8px;display:inline-block;}

        @keyframes countUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .count-animate{animation:countUp 0.4s ease-out forwards;}

        @keyframes fadeIn{from{opacity:0;transform:translateY(5px)}to{opacity:1;transform:translateY(0)}}
        .fade-in{opacity:0;animation:fadeIn 0.28s ease-out forwards;}

        @keyframes modalIn{from{opacity:0;transform:translateY(16px) scale(0.97)}to{opacity:1;transform:none}}
        .modal-in{animation:modalIn 0.28s cubic-bezier(0.22,1,0.36,1) forwards;}

        @keyframes barGrow{from{width:0%}to{width:var(--bar-w);}}
        .bar-grow{animation:barGrow 0.9s cubic-bezier(0.22,1,0.36,1) forwards;}

        @keyframes progressPulse{0%{opacity:0.7}50%{opacity:1}100%{opacity:0.7}}
        .progress-pulse{animation:progressPulse 1.2s ease-in-out infinite;}

        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}

        .row-hover:hover{background:#fafafa;}

        @keyframes shake{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
        .shake{animation:shake 0.3s ease-in-out;}

        @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
        .spin{animation:spin 1.2s linear infinite;}
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
                <h1 class="font-extrabold text-slate-900 text-sm tracking-tight leading-none">{{ $platformName }}</h1>
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
            <a href="{{ route('superadmin.exams.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-file-signature text-xs text-slate-400"></i>
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

            {{-- ACTIVE --}}
            <a href="{{ route('superadmin.backups.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-database text-xs text-white"></i>
                </span>
                <span class="flex-1">Database & Backup</span>
                <span class="text-[9px] bg-white bg-opacity-20 text-white font-bold px-2 py-0.5 rounded-full">ROOT</span>
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
                <img class="w-8 h-8 rounded-lg object-cover flex-shrink-0"
                     src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name ?? 'Super Admin') . '&background=3b82f6&color=fff&size=64' }}"
                     alt="{{ Auth::user()->full_name ?? 'Super Admin' }}">
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
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Database & Backup</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    Real-time snapshots · manual backups · point-in-time restoration
                </p>
            </div>

            <div class="flex items-center gap-3 ml-auto flex-wrap">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>

                <div class="flex items-center gap-2 text-xs font-bold bg-rose-50 text-rose-700 border border-rose-100 px-3 py-1.5 rounded-lg">
                    <i class="fa-solid fa-lock text-rose-400 text-xs"></i>
                    Super Admin Only
                </div>

                {{-- ═══ REAL-TIME CONNECTION STATUS ═══ --}}
                <div id="ws-status" class="flex items-center gap-2 text-xs font-semibold border px-3 py-1.5 rounded-lg transition-all duration-300"
                     style="background:#f0fdf4;border-color:#a7f3d0;color:#059669;">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span id="ws-ping" class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-60" style="width:100%;height:100%;"></span>
                        <span id="ws-dot" class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    <span id="ws-label">Connecting...</span>
                </div>

                {{-- Trigger Backup Button --}}
                <button id="backup-btn" onclick="openBackupConfirm()"
                        class="flex items-center gap-2 text-xs font-bold text-white px-4 py-2.5 rounded-xl transition-all"
                        style="background:#0f172a;box-shadow:0 4px 14px rgba(15,23,42,0.25);"
                        onmouseenter="this.style.background='#1e293b'" onmouseleave="this.style.background='#0f172a'">
                    <i id="backup-icon" class="fa-solid fa-circle-plus text-xs"></i>
                    <span id="backup-btn-text">Trigger Backup</span>
                </button>
            </div>
        </header>

        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:20px;">

            {{-- ========== REAL-TIME PROGRESS BAR (hidden by default) ========== --}}
            <div id="rt-progress-bar" style="display:none;"
                 class="bg-white rounded-2xl border border-blue-200 p-5 transition-all duration-300"
                 style="box-shadow:0 4px 16px rgba(59,130,246,0.12);">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                        <i id="rt-progress-icon" class="fa-solid fa-database text-blue-500 text-sm spin"></i>
                    </div>
                    <div class="flex-1">
                        <p id="rt-progress-title" class="text-sm font-bold text-slate-900">Creating snapshot...</p>
                        <p id="rt-progress-sub" class="text-[11px] text-slate-400 font-medium">Job dispatched — waiting for server to process</p>
                    </div>
                    <span id="rt-progress-badge" class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full progress-pulse">
                        IN PROGRESS
                    </span>
                </div>
                <div class="w-full rounded-full overflow-hidden" style="height:6px;background:#e2e8f0;">
                    <div id="rt-progress-fill" class="h-full rounded-full transition-all duration-500"
                         style="width:0%;background:linear-gradient(90deg,#3b82f6,#6366f1);"></div>
                </div>
            </div>

            {{-- ========== VISIBILITY NOTICE ========== --}}
            <div class="flex items-start gap-3 bg-rose-50 border border-rose-100 rounded-2xl px-5 py-4"
                 style="box-shadow:0 1px 4px rgba(244,63,94,0.07);">
                <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-shield-halved text-rose-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-rose-900 mb-0.5">Root Access — Super Admin Only</p>
                    <p class="text-[11px] text-rose-700 font-medium leading-relaxed">
                        Standard Admins are blocked from this page at the route/middleware level (403 Forbidden).
                        Every backup and restore action is <strong>automatically written to the Forensic Audit Trail</strong> —
                        including who triggered it, when, and which snapshot was used. A backup is not an audit log:
                        purging a snapshot never touches audit history.
                    </p>
                </div>
            </div>

            {{-- ========== METRIC CARDS ========== --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Last Backup --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                            <i class="fa-solid fa-rotate text-blue-500 text-sm"></i>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Last Sync Backup</p>
                    </div>
                    <p id="card-last-backup" class="text-lg font-black text-slate-900 leading-tight">
                        {{ $lastBackup ? \Carbon\Carbon::parse($lastBackup)->diffForHumans() : 'Never' }}
                    </p>
                    <p class="text-[10px] text-slate-400 font-medium mt-1.5">Last successful snapshot written</p>
                </div>

                {{-- Storage Used --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                            <i class="fa-solid fa-hard-drive text-emerald-500 text-sm"></i>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Storage Allocation</p>
                    </div>
                    <div class="flex items-center gap-3 mb-1.5">
                        <div class="flex-1 rounded-full overflow-hidden" style="height:7px;background:#f1f5f9;">
                            <div id="storage-bar" class="h-full rounded-full transition-all duration-700"
                                 style="width:{{ $storageUsed ?? 14 }}%;background:#10b981;"></div>
                        </div>
                        <span id="storage-pct" class="text-sm font-black text-slate-900 tabular-nums flex-shrink-0">{{ $storageUsed ?? 14 }}%</span>
                    </div>
                    <p id="storage-label" class="text-[10px] font-medium mt-1"></p>
                </div>

                {{-- Auto Schedule --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                            <i class="fa-solid fa-calendar-check text-amber-500 text-sm"></i>
                        </div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Auto-Schedule</p>
                    </div>
                    <p class="text-base font-black text-slate-900 leading-tight">Daily · 03:00</p>
                    <p class="text-[10px] text-slate-400 font-medium mt-1.5">
                        Retention &amp; storage limits are platform policy —
                        <a href="{{ route('superadmin.settings.index') }}" class="text-blue-500 font-bold hover:underline">configure in Global Settings →</a>
                    </p>
                </div>
            </div>

            {{-- ========== SNAPSHOT TABLE ========== --}}
            <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden"
                 style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-sm text-slate-900">Database Snapshot History</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">
                            Append-only ledger — snapshots can be restored or deleted, never edited
                        </p>
                    </div>
                    <span id="refresh-ts" class="text-[10px] font-bold text-slate-300 font-mono">
                        Refreshed: {{ now()->format('H:i:s') }}
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Snapshot ID</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Created</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Size</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Type</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right" style="min-width:200px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="snapshot-table-body"></tbody>
                    </table>
                </div>

                <div class="flex items-center justify-between px-6 py-3.5 border-t border-slate-100 bg-slate-50">
                    <p id="snap-footer" class="text-[11px] text-slate-400 font-medium">Loading snapshots...</p>
                    <p class="text-[11px] text-slate-400 font-medium">
                        <i class="fa-solid fa-bolt text-amber-500 mr-1"></i>
                        Real-time via WebSocket
                    </p>
                </div>
            </div>

            {{-- ========== DANGER ZONE ========== --}}
            <div class="rounded-2xl border-2 border-dashed border-rose-200 p-5"
                 style="background:linear-gradient(135deg,#fff5f5,#fff);">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-100 flex items-center justify-center">
                        <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-sm text-rose-900">Danger Zone</h3>
                        <p class="text-[11px] text-rose-600 font-medium">Actions in this zone are irreversible and require deliberate confirmation</p>
                    </div>
                </div>
                <div class="flex items-start gap-4 flex-wrap">
                    <div class="flex-1 min-w-[260px] bg-white rounded-xl border border-rose-100 p-4">
                        <p class="text-xs font-bold text-slate-900 mb-1">Database Restoration</p>
                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed mb-3">
                            Restoring from a snapshot <strong>permanently overwrites the live database</strong>. All data created since that snapshot — exams, users, results — is destroyed. This cannot be undone. Click Restore on any snapshot above to begin the 3-step authorization flow.
                        </p>
                        <div class="flex items-center gap-2 text-[10px] font-bold text-rose-600">
                            <i class="fa-solid fa-lock text-rose-400"></i>
                            Requires typing <span class="font-mono bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded mx-1">RESTORE</span> to unlock
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>


{{-- ===================== BACKUP CONFIRM MODAL ===================== --}}
<div id="backup-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.40);backdrop-filter:blur(8px);"
     onclick="closeBackupModal()">
    <div class="modal-in bg-white rounded-2xl max-w-sm w-full border border-slate-100"
         style="box-shadow:0 24px 64px rgba(15,23,42,0.18);" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-slate-100 flex items-center justify-center">
                    <i class="fa-solid fa-database text-slate-700 text-sm"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-900">Trigger Manual Backup</h3>
            </div>
            <button onclick="closeBackupModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <div class="p-6">
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5">
                <i class="fa-solid fa-circle-info text-blue-500 text-sm flex-shrink-0 mt-0.5"></i>
                <p class="text-[11px] text-blue-800 font-medium leading-relaxed">
                    A manual snapshot will be created immediately and appended to the snapshot history.
                    This action is <strong>safe and non-destructive</strong> — it only adds a new recovery point, it does not affect the live database.
                    It will be logged to the Forensic Audit Trail.
                </p>
            </div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-slate-500 mb-5">
                <i class="fa-solid fa-check-circle text-emerald-500"></i>
                Safe · Reversible · Audit-logged
            </div>
            <div class="flex items-center gap-2 text-[10px] font-medium text-blue-600 bg-blue-50 border border-blue-100 rounded-lg px-3 py-2">
                <i class="fa-solid fa-bolt text-blue-400"></i>
                You will receive <strong>real-time progress updates</strong> via WebSocket — no refresh needed.
            </div>
        </div>

        <div class="flex gap-3 px-6 pb-6">
            <button onclick="closeBackupModal()"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button onclick="executeManualBackup()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:#0f172a;box-shadow:0 4px 14px rgba(15,23,42,0.22);"
                    onmouseenter="this.style.background='#1e293b'" onmouseleave="this.style.background='#0f172a'">
                <i class="fa-solid fa-database mr-1.5"></i> Create Snapshot
            </button>
        </div>
    </div>
</div>


{{-- ===================== RESTORE MODAL (3-STEP) ===================== --}}
<div id="restore-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.60);backdrop-filter:blur(12px);">
    <div class="modal-in bg-white rounded-2xl max-w-lg w-full border border-rose-200"
         style="box-shadow:0 32px 80px rgba(225,29,72,0.18);" onclick="event.stopPropagation()">

        <div class="flex items-center gap-2 px-6 pt-5 mb-1">
            <div id="step-1-ind" class="flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-black"
                 style="background:#e11d48;color:#fff;">1</div>
            <div style="flex:1;height:2px;background:#fecdd3;border-radius:1px;"></div>
            <div id="step-2-ind" class="flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-black"
                 style="background:#fecdd3;color:#f43f5e;">2</div>
            <div style="flex:1;height:2px;background:#fecdd3;border-radius:1px;"></div>
            <div id="step-3-ind" class="flex items-center justify-center w-6 h-6 rounded-full text-[10px] font-black"
                 style="background:#fecdd3;color:#f43f5e;">3</div>
        </div>
        <p id="step-label" class="text-[10px] font-bold text-rose-400 uppercase tracking-widest px-6 mb-0">
            Step 1 of 3 — Read warning
        </p>

        {{-- STEP 1 --}}
        <div id="restore-step1" class="px-6 pb-6 pt-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-rose-600 text-lg"></i>
                </div>
                <div>
                    <h3 class="font-black text-base text-slate-900">Database Restoration</h3>
                    <p class="text-xs text-rose-600 font-bold">Snapshot: <span id="restore-target-label" class="font-mono"></span></p>
                </div>
            </div>
            <div class="rounded-xl border border-rose-200 overflow-hidden mb-4" style="background:#fff5f5;">
                <div class="px-4 py-2.5 border-b border-rose-100" style="background:#fff1f2;">
                    <p class="text-[10px] font-black text-rose-900 uppercase tracking-widest">What will be permanently destroyed</p>
                </div>
                <ul class="px-4 py-3 space-y-1.5">
                    <li class="flex items-center gap-2 text-xs text-rose-800 font-medium">
                        <i class="fa-solid fa-xmark text-rose-500 text-xs flex-shrink-0"></i>
                        All exams created <em>after</em> this snapshot's timestamp
                    </li>
                    <li class="flex items-center gap-2 text-xs text-rose-800 font-medium">
                        <i class="fa-solid fa-xmark text-rose-500 text-xs flex-shrink-0"></i>
                        All user accounts created or modified since then
                    </li>
                    <li class="flex items-center gap-2 text-xs text-rose-800 font-medium">
                        <i class="fa-solid fa-xmark text-rose-500 text-xs flex-shrink-0"></i>
                        All exam results, student submissions, and flag records in that window
                    </li>
                    <li class="flex items-center gap-2 text-xs text-rose-800 font-medium">
                        <i class="fa-solid fa-xmark text-rose-500 text-xs flex-shrink-0"></i>
                        All configuration changes made since the snapshot
                    </li>
                </ul>
                <div class="px-4 py-2 border-t border-rose-100">
                    <p class="text-[10px] font-bold text-emerald-700">
                        <i class="fa-solid fa-check text-emerald-500 mr-1"></i>
                        Audit Trail history is <strong>never</strong> affected by a restore — it is kept separately.
                    </p>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="closeRestoreModal()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Cancel</button>
                <button onclick="goRestoreStep2()"
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                        style="background:#e11d48;" onmouseenter="this.style.background='#be123c'" onmouseleave="this.style.background='#e11d48'">
                    I understand — Continue →</button>
            </div>
        </div>

        {{-- STEP 2 --}}
        <div id="restore-step2" style="display:none;" class="px-6 pb-6 pt-4">
            <p class="text-xs font-bold text-slate-900 mb-1">Authorization Phrase</p>
            <p class="text-[11px] text-slate-500 font-medium mb-4 leading-relaxed">
                To confirm you are authorizing an irreversible database overwrite, type the exact phrase below:</p>
            <div class="flex items-center justify-center mb-4">
                <span class="font-mono font-black text-lg text-rose-600 bg-rose-50 border border-rose-200 px-4 py-2 rounded-xl tracking-widest">RESTORE</span>
            </div>
            <input id="restore-confirm-input" type="text" placeholder="Type RESTORE here..." oninput="checkRestorePhrase()"
                   autocomplete="off"
                   class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm font-bold text-slate-800 outline-none transition-all text-center tracking-widest font-mono mb-4"
                   onfocus="this.style.borderColor='#e11d48';this.style.background='#fff'"
                   onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
            <p id="phrase-hint" class="text-[11px] text-center mb-4 font-medium text-slate-400"></p>
            <div class="flex gap-3">
                <button onclick="goRestoreStep1()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">← Back</button>
                <button id="restore-proceed-btn" onclick="goRestoreStep3()" disabled
                        class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all opacity-40 cursor-not-allowed"
                        style="background:#e11d48;">Phrase confirmed →</button>
            </div>
        </div>

        {{-- STEP 3 --}}
        <div id="restore-step3" style="display:none;" class="px-6 pb-6 pt-4">
            <div class="flex items-center gap-3 bg-rose-50 border border-rose-200 rounded-xl p-4 mb-5">
                <i class="fa-solid fa-circle-check text-rose-500 text-lg flex-shrink-0"></i>
                <div>
                    <p class="text-xs font-bold text-rose-900">Phrase confirmed. Final step.</p>
                    <p class="text-[11px] text-rose-700 font-medium">This is your last opportunity to stop. Clicking the button below will immediately begin overwriting the live database. This cannot be interrupted once started.</p>
                </div>
            </div>
            <p class="text-[11px] text-slate-500 font-medium mb-4 leading-relaxed text-center">
                This action will be logged as a <strong class="text-rose-700">CRITICAL</strong> event in the Forensic Audit Trail under your account.</p>
            <div class="flex gap-3">
                <button onclick="closeRestoreModal()"
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Abort</button>
                <button id="final-restore-btn" onclick="executeRestore()"
                        class="flex-1 py-3 rounded-xl text-sm font-black text-white transition-all"
                        style="background:linear-gradient(135deg,#dc2626,#e11d48);box-shadow:0 4px 18px rgba(220,38,38,0.38);"
                        onmouseenter="this.style.boxShadow='0 6px 24px rgba(220,38,38,0.50)'"
                        onmouseleave="this.style.boxShadow='0 4px 18px rgba(220,38,38,0.38)'">
                    <i class="fa-solid fa-triangle-exclamation mr-1.5"></i>
                    <span id="restore-btn-text">Authorize Restoration</span>
                </button>
            </div>
        </div>
    </div>
</div>


{{-- ===================== CDN: Pusher.js + Laravel Echo ===================== --}}
<script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>

<script>
// ============================================================
//  CONFIG & STATE
// ============================================================
const CSRF            = document.querySelector('meta[name=csrf-token]').content;
let currentRestoreId  = null;
let isBackingUp       = false;
let isRestoring       = false;
let wsConnected       = false;

// Blade-injected initial data
let allSnapshots = {!! json_encode($snapshots ?? []) !!};
let storageUsed  = {{ $storageUsed ?? 14 }};


// ============================================================
//  LARAVEL ECHO — REAL-TIME WEBSOCKET CONNECTION
// ============================================================
const echo = new Echo({
    broadcaster: 'pusher',
    key: '{{ config('broadcasting.connections.pusher.key') ?: 'examsystemkeyabc123' }}',
    cluster: '{{ config('broadcasting.connections.pusher.options.cluster') ?: 'mt1' }}',
    forceTLS: true,
    authEndpoint: '{{ url('/broadcasting/auth') }}',
    auth: {
        headers: {
            'X-CSRF-TOKEN': CSRF,
            'Accept': 'application/json',
        }
    }
});


// ============================================================
//  WEBSOCKET CONNECTION STATUS MONITOR
// ============================================================
function setConnectionStatus(state) {
    const el    = document.getElementById('ws-status');
    const dot   = document.getElementById('ws-dot');
    const ping  = document.getElementById('ws-ping');
    const label = document.getElementById('ws-label');

    switch (state) {
        case 'connected':
            wsConnected = true;
            el.style.background    = '#f0fdf4';
            el.style.borderColor   = '#a7f3d0';
            el.style.color         = '#059669';
            dot.style.background   = '#10b981';
            ping.style.background  = '#34d399';
            ping.className         = 'ping-slow absolute inline-flex rounded-full opacity-60';
            label.textContent      = 'Live';
            break;

        case 'connecting':
            wsConnected = false;
            el.style.background    = '#fffbeb';
            el.style.borderColor   = '#fde68a';
            el.style.color         = '#b45309';
            dot.style.background   = '#f59e0b';
            ping.style.background  = '#fbbf24';
            ping.className         = 'pulse-live absolute inline-flex rounded-full opacity-60';
            label.textContent      = 'Connecting...';
            break;

        case 'disconnected':
            wsConnected = false;
            el.style.background    = '#fef2f2';
            el.style.borderColor   = '#fecaca';
            el.style.color         = '#dc2626';
            dot.style.background   = '#ef4444';
            ping.style.background  = '#f87171';
            ping.className         = 'absolute inline-flex rounded-full opacity-40';
            label.textContent      = 'Disconnected';
            break;

        case 'reconnecting':
            wsConnected = false;
            el.style.background    = '#fffbeb';
            el.style.borderColor   = '#fde68a';
            el.style.color         = '#b45309';
            dot.style.background   = '#f59e0b';
            ping.className         = 'pulse-live absolute inline-flex rounded-full opacity-60';
            label.textContent      = 'Reconnecting...';
            break;
    }
}

// Monitor Pusher connection states (Reverb uses Pusher protocol)
if (echo.connector && echo.connector.pusher) {
    const conn = echo.connector.pusher.connection;

    conn.bind('connecting',    () => setConnectionStatus('connecting'));
    conn.bind('connected',     () => setConnectionStatus('connected'));
    conn.bind('disconnected',  () => setConnectionStatus('disconnected'));
    conn.bind('unavailable',   () => setConnectionStatus('disconnected'));
    conn.bind('failed',        () => setConnectionStatus('disconnected'));

    conn.bind('state_change', (states) => {
        console.log('[WS] State:', states.previous, '→', states.current);
        if (states.current === 'connecting')   setConnectionStatus('reconnecting');
        if (states.current === 'connected')    setConnectionStatus('connected');
        if (states.current === 'disconnected') setConnectionStatus('disconnected');
        if (states.current === 'unavailable')  setConnectionStatus('disconnected');
    });
}

setConnectionStatus('connecting');


// ============================================================
//  SUBSCRIBE TO PRIVATE CHANNEL & LISTEN FOR EVENTS
// ============================================================
const backupChannel = echo.private('backups.superadmin');

// ─── BACKUP STARTED ───
backupChannel.listen('.backup.started', (e) => {
    console.log('[RT] Backup started:', e);
    isBackingUp = true;

    showProgressBar('Creating snapshot...', `Triggered by ${e.triggered_by} · ${e.type} backup`, 'backup');

    // Animate progress bar (simulated since we don't have granular progress)
    simulateProgress('backup');

    showToast(`
        <i class="fa-solid fa-database" style="color:#60a5fa;font-size:13px;"></i>
        <span style="margin-left:8px;">Backup started by <strong>${e.triggered_by}</strong></span>
    `);

    // Disable backup button
    const btn = document.getElementById('backup-btn');
    const icon = document.getElementById('backup-icon');
    const text = document.getElementById('backup-btn-text');
    btn.disabled = true;
    icon.className = 'fa-solid fa-database spin text-xs';
    text.textContent = 'Creating snapshot...';
});

// ─── BACKUP COMPLETED ───
backupChannel.listen('.backup.completed', (e) => {
    console.log('[RT] Backup completed:', e);
    isBackingUp = false;

    // Complete progress bar
    completeProgressBar('Snapshot created successfully!', 'success');

    // Update snapshot list — prepend new snapshot
    if (e.snapshot) {
        allSnapshots.unshift(e.snapshot);
        renderSnapshots();
    }

    // Update storage
    if (e.storage_used !== undefined) {
        storageUsed = e.storage_used;
        updateStorageUI(storageUsed);
    }

    // Update last backup card
    if (e.last_backup_human) {
        document.getElementById('card-last-backup').textContent = e.last_backup_human;
    }

    showToast(`
        <i class="fa-solid fa-circle-check" style="color:#34d399;font-size:13px;"></i>
        <span style="margin-left:8px;">Snapshot created and logged to Audit Trail.</span>
    `);

    // Reset backup button
    resetBackupButton();
});

// ─── BACKUP FAILED ───
backupChannel.listen('.backup.failed', (e) => {
    console.log('[RT] Backup failed:', e);
    isBackingUp = false;

    completeProgressBar('Backup failed — ' + (e.error_message || 'Unknown error'), 'error');

    showToast(`
        <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
        <span style="margin-left:8px;">${e.error_message || 'Backup creation failed.'}</span>
    `);

    resetBackupButton();
});

// ─── RESTORE STARTED ───
backupChannel.listen('.restore.started', (e) => {
    console.log('[RT] Restore started:', e);
    isRestoring = true;

    showProgressBar('Restoring database...', `Target: ${e.snapshot_id} · Triggered by ${e.triggered_by}`, 'restore');
    simulateProgress('restore');

    showToast(`
        <i class="fa-solid fa-clock-rotate-left" style="color:#60a5fa;font-size:13px;"></i>
        <span style="margin-left:8px;">Database restoration in progress — <strong>${e.snapshot_id}</strong></span>
    `);
});

// ─── RESTORE COMPLETED ───
backupChannel.listen('.restore.completed', (e) => {
    console.log('[RT] Restore completed:', e);
    isRestoring = false;

    completeProgressBar('Database restored successfully!', 'success');

    showToast(`
        <i class="fa-solid fa-clock-rotate-left" style="color:#34d399;font-size:13px;"></i>
        <span style="margin-left:8px;">Database restored to <strong>${e.snapshot_id}</strong> — logged as CRITICAL in Audit Trail.</span>
    `);

    // Refresh data from server
    fetchBackupTelemetry();
});

// ─── RESTORE FAILED ───
backupChannel.listen('.restore.failed', (e) => {
    console.log('[RT] Restore failed:', e);
    isRestoring = false;

    completeProgressBar('Restore failed — ' + (e.error_message || 'Unknown error'), 'error');

    showToast(`
        <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
        <span style="margin-left:8px;">${e.error_message || 'Restoration failed.'}</span>
    `);
});


// ============================================================
//  PROGRESS BAR CONTROL
// ============================================================
let progressInterval = null;

function showProgressBar(title, subtitle, type) {
    const bar = document.getElementById('rt-progress-bar');
    const icon = document.getElementById('rt-progress-icon');
    const titleEl = document.getElementById('rt-progress-title');
    const subEl = document.getElementById('rt-progress-sub');
    const badge = document.getElementById('rt-progress-badge');
    const fill = document.getElementById('rt-progress-fill');

    bar.style.display = 'block';
    bar.style.borderColor = type === 'restore' ? '#fecdd3' : '#bfdbfe';
    titleEl.textContent = title;
    subEl.textContent = subtitle;
    fill.style.width = '0%';
    fill.style.background = type === 'restore'
        ? 'linear-gradient(90deg,#e11d48,#f43f5e)'
        : 'linear-gradient(90deg,#3b82f6,#6366f1)';

    icon.className = type === 'restore'
        ? 'fa-solid fa-clock-rotate-left text-rose-500 text-sm spin'
        : 'fa-solid fa-database text-blue-500 text-sm spin';

    badge.textContent = 'IN PROGRESS';
    badge.className = 'text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-200 px-3 py-1 rounded-full progress-pulse';
}

function simulateProgress(type) {
    if (progressInterval) clearInterval(progressInterval);

    const fill = document.getElementById('rt-progress-fill');
    let progress = 0;

    progressInterval = setInterval(() => {
        // Slow down as we approach 90% — never reach 100% until server confirms
        if (progress < 30)       progress += Math.random() * 8;
        else if (progress < 60)  progress += Math.random() * 4;
        else if (progress < 85)  progress += Math.random() * 2;
        else if (progress < 92)  progress += Math.random() * 0.3;
        else                     progress = Math.min(progress + 0.05, 94);

        fill.style.width = progress + '%';
    }, 300);
}

function completeProgressBar(message, status) {
    if (progressInterval) { clearInterval(progressInterval); progressInterval = null; }

    const fill  = document.getElementById('rt-progress-fill');
    const title = document.getElementById('rt-progress-title');
    const icon  = document.getElementById('rt-progress-icon');
    const badge = document.getElementById('rt-progress-badge');

    fill.style.width = '100%';
    title.textContent = message;

    if (status === 'success') {
        fill.style.background = 'linear-gradient(90deg,#10b981,#34d399)';
        icon.className = 'fa-solid fa-circle-check text-emerald-500 text-sm';
        badge.textContent = 'COMPLETED';
        badge.className = 'text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full';
    } else {
        fill.style.background = 'linear-gradient(90deg,#ef4444,#f87171)';
        icon.className = 'fa-solid fa-circle-xmark text-rose-500 text-sm';
        badge.textContent = 'FAILED';
        badge.className = 'text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-200 px-3 py-1 rounded-full';
    }

    // Auto-hide after 8 seconds
    setTimeout(() => {
        document.getElementById('rt-progress-bar').style.display = 'none';
    }, 8000);
}


// ============================================================
//  CLOCK
// ============================================================
function updateClock() {
    document.getElementById('live-clock').textContent =
        new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
}
updateClock();
setInterval(updateClock, 1000);


// ============================================================
//  HEARTBEAT FALLBACK (60s — only as a safety net)
// ============================================================
setInterval(() => {
    // Only poll if WebSocket is disconnected (fallback mode)
    if (!wsConnected) {
        console.log('[Fallback] WebSocket disconnected — polling via HTTP');
        fetchBackupTelemetry();
    }
}, 60000);


// ============================================================
//  STORAGE BAR
// ============================================================
function updateStorageUI(pct) {
    const bar   = document.getElementById('storage-bar');
    const label = document.getElementById('storage-label');
    const text  = document.getElementById('storage-pct');
    bar.style.width = pct + '%';

    if (pct >= 85) {
        bar.style.background = '#e11d48';
        label.innerHTML = `<span style="color:#e11d48;font-weight:700;font-size:10px;">⚠ ${pct}% used — approaching limit. Review in Global Settings.</span>`;
    } else if (pct >= 65) {
        bar.style.background = '#f59e0b';
        label.innerHTML = `<span style="color:#b45309;font-weight:700;font-size:10px;">Storage at ${pct}% — moderate usage.</span>`;
    } else {
        bar.style.background = '#10b981';
        label.innerHTML = `<span style="color:#059669;font-weight:600;font-size:10px;">Storage healthy at ${pct}%.</span>`;
    }
    text.textContent = pct + '%';
}
updateStorageUI(storageUsed);


// ============================================================
//  RENDER SNAPSHOT TABLE
// ============================================================
function renderSnapshots() {
    const tbody = document.getElementById('snapshot-table-body');
    const snap  = allSnapshots;
    document.getElementById('snap-footer').textContent = `${snap.length} snapshot${snap.length !== 1 ? 's' : ''} in history`;

    if (!snap.length) {
        tbody.innerHTML = `<tr><td colspan="6" style="padding:48px;text-align:center;color:#94a3b8;">
            <i class="fa-solid fa-box-open" style="font-size:28px;display:block;margin-bottom:10px;color:#e2e8f0;"></i>
            <p style="font-size:12px;font-weight:600;">No snapshots compiled yet. Trigger a manual backup to create your first snapshot.</p>
            </td></tr>`;
        return;
    }

    tbody.innerHTML = snap.map((s, i) => {
        const isManual = s.type === 'manual';
        const hasFile = s.has_file !== false; // default true for older cached rows
        const delay = i * 40;
        return `
        <tr class="row-hover fade-in" style="animation-delay:${delay}ms;transition:background 0.12s;border-bottom:1px solid #f8fafc;">
            <td style="padding:14px 24px;">
                <span style="font-size:11px;font-weight:700;font-family:'JetBrains Mono',monospace;color:#2563eb;
                             background:#eff6ff;border:1px solid #bfdbfe;padding:3px 9px;border-radius:6px;">
                    ${s.id}
                </span>
            </td>
            <td style="padding:14px 16px;font-size:12px;color:#475569;font-weight:500;font-family:'JetBrains Mono',monospace;">
                ${s.created_at}
            </td>
            <td style="padding:14px 16px;font-size:12px;font-weight:700;color:#0f172a;font-family:'JetBrains Mono',monospace;">
                ${s.size_mb} MB
            </td>
            <td style="padding:14px 16px;">
                <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;
                             padding:3px 9px;border-radius:7px;border:1px solid;
                             background:${isManual ? '#eff6ff' : '#f8fafc'};
                             color:${isManual ? '#1d4ed8' : '#64748b'};
                             border-color:${isManual ? '#bfdbfe' : '#e2e8f0'};">
                    <i class="fa-solid ${isManual ? 'fa-hand-point-up' : 'fa-rotate'}" style="margin-right:4px;font-size:9px;"></i>
                    ${s.type}
                </span>
            </td>
            <td style="padding:14px 16px;text-align:center;">
                <span style="display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:700;
                             padding:3px 10px;border-radius:7px;background:#f0fdf4;color:#059669;border:1px solid #a7f3d0;">
                    <span style="width:6px;height:6px;border-radius:50%;background:#10b981;"></span>Completed
                </span>
            </td>
            <td style="padding:14px 16px;text-align:right;">
                <div style="display:flex;gap:6px;justify-content:flex-end;align-items:center;">
                    ${!hasFile ? `
                    <span title="This entry was reconstructed from the audit log only — the actual backup file is missing (likely lost on a container restart)."
                          style="font-size:10px;font-weight:700;padding:4px 9px;border-radius:7px;
                                 background:#fffbeb;color:#b45309;border:1px solid #fde68a;cursor:help;">
                        <i class="fa-solid fa-triangle-exclamation" style="margin-right:4px;font-size:9px;"></i>File unavailable
                    </span>
                    <button onclick="dismissSnapshot('${s.id}')"
                            style="font-size:11px;font-weight:700;padding:6px 10px;border-radius:8px;
                                   border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;cursor:pointer;
                                   transition:all 0.2s;"
                            onmouseenter="this.style.background='#fee2e2';this.style.borderColor='#fca5a5';this.style.color='#e11d48'"
                            onmouseleave="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.color='#94a3b8'"
                            title="Remove this row from the list (audit history is never touched)">
                        <i class="fa-solid fa-trash-can" style="font-size:10px;"></i>
                    </button>
                    ` : `
                    <button onclick="openRestoreModal('${s.id}')"
                            style="font-size:11px;font-weight:800;padding:6px 14px;border-radius:8px;
                                   border:1px solid #fecdd3;background:#fff1f2;color:#e11d48;cursor:pointer;
                                   transition:all 0.2s;"
                            onmouseenter="this.style.background='#fee2e2';this.style.borderColor='#fca5a5'"
                            onmouseleave="this.style.background='#fff1f2';this.style.borderColor='#fecdd3'">
                        <i class="fa-solid fa-clock-rotate-left" style="margin-right:5px;font-size:10px;"></i>Restore
                    </button>
                    <button onclick="deleteSnapshot('${s.id}')"
                            style="font-size:11px;font-weight:700;padding:6px 10px;border-radius:8px;
                                   border:1px solid #e2e8f0;background:#f8fafc;color:#94a3b8;cursor:pointer;
                                   transition:all 0.2s;"
                            onmouseenter="this.style.background='#fee2e2';this.style.borderColor='#fca5a5';this.style.color='#e11d48'"
                            onmouseleave="this.style.background='#f8fafc';this.style.borderColor='#e2e8f0';this.style.color='#94a3b8'"
                            title="Delete snapshot">
                        <i class="fa-solid fa-trash-can" style="font-size:10px;"></i>
                    </button>
                    `}
                </div>
            </td>
        </tr>`;
    }).join('');

    document.getElementById('refresh-ts').textContent =
        'Refreshed: ' + new Date().toLocaleTimeString('en-US', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
}


// ============================================================
//  API: FETCH BACKUP TELEMETRY (fallback / initial load)
// ============================================================
async function fetchBackupTelemetry() {
    try {
        const res = await fetch("{{ route('superadmin.backup.api') }}", { headers: { 'Accept': 'application/json' } });
        if (res.ok) {
            const data = await res.json();
            if (data.snapshots)                allSnapshots = data.snapshots;
            if (data.storageUsed !== undefined) { storageUsed = data.storageUsed; updateStorageUI(storageUsed); }
            if (data.lastBackupHuman)          document.getElementById('card-last-backup').textContent = data.lastBackupHuman;
            renderSnapshots();
        }
    } catch (e) {
        console.warn('[Fallback] HTTP poll failed:', e);
    }
}


// ============================================================
//  BACKUP CONFIRM MODAL
// ============================================================
function openBackupConfirm() {
    if (isBackingUp) return;
    document.getElementById('backup-modal').style.display = 'flex';
}

function closeBackupModal() {
    document.getElementById('backup-modal').style.display = 'none';
}

async function executeManualBackup() {
    closeBackupModal();

    const btn  = document.getElementById('backup-btn');
    const icon = document.getElementById('backup-icon');
    const text = document.getElementById('backup-btn-text');
    btn.disabled = true;
    icon.className = 'fa-solid fa-database spin text-xs';
    text.textContent = 'Dispatching...';

    try {
        const res = await fetch("{{ route('superadmin.backup.trigger') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });

        if (res.ok) {
            // Job dispatched — real-time events will handle the rest
            text.textContent = 'Processing...';
            showToast(`
                <i class="fa-solid fa-paper-plane" style="color:#60a5fa;font-size:13px;"></i>
                <span style="margin-left:8px;">Backup job dispatched — watching for real-time updates...</span>
            `);
        } else {
            const err = await res.json().catch(() => ({}));
            showToast(`
                <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
                <span style="margin-left:8px;">${err.message || 'Failed to dispatch backup job.'}</span>
            `);
            resetBackupButton();
        }
    } catch (e) {
        showToast(`
            <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
            <span style="margin-left:8px;">Network error — try again.</span>
        `);
        resetBackupButton();
    }
}

function resetBackupButton() {
    const btn  = document.getElementById('backup-btn');
    const icon = document.getElementById('backup-icon');
    const text = document.getElementById('backup-btn-text');
    btn.disabled = false;
    icon.className = 'fa-solid fa-circle-plus text-xs';
    text.textContent = 'Trigger Backup';
}


// ============================================================
//  DISMISS FILELESS SNAPSHOT (audit-log-only row, no real file)
// ============================================================
async function dismissSnapshot(id) {
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
    if (!confirm(`Remove ${id} from this list? This only hides the row — audit history is never affected, and nothing is un-recoverable because there was no file backing it anyway.`)) return;

    try {
        const res = await fetch(`/super-admin/backups/${id}/dismiss`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });

        if (res.ok) {
            allSnapshots = allSnapshots.filter(s => s.id !== id);
            renderSnapshots();
            showToast(`
                <i class="fa-solid fa-trash-can" style="color:#94a3b8;font-size:13px;"></i>
                <span style="margin-left:8px;">Removed ${id} from the list.</span>
            `);
        } else {
            const err = await res.json().catch(() => ({}));
            console.error('[Backup Dismiss] HTTP', res.status, err);
            showToast(`
                <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
                <span style="margin-left:8px;">${escapeHtml(err.message || ('Failed to remove row (HTTP ' + res.status + ').'))}</span>
            `);
        }
    } catch (e) {
        console.error('[Backup Dismiss] Network error', e);
        showToast(`
            <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
            <span style="margin-left:8px;">Network error.</span>
        `);
    }
}

// ============================================================
//  DELETE SNAPSHOT
// ============================================================
async function deleteSnapshot(id) {
    function escapeHtml(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str));
        return div.innerHTML;
    }
    if (!confirm(`Delete snapshot ${id}? This only removes the backup file — audit logs are never affected.`)) return;

    try {
        const res = await fetch(`/super-admin/backups/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });

        if (res.ok) {
            allSnapshots = allSnapshots.filter(s => s.id !== id);
            renderSnapshots();
            showToast(`
                <i class="fa-solid fa-trash-can" style="color:#94a3b8;font-size:13px;"></i>
                <span style="margin-left:8px;">Snapshot ${id} deleted.</span>
            `);
        } else {
            const err = await res.json().catch(() => ({}));
            console.error('[Backup Delete] HTTP', res.status, err);
            showToast(`
                <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
                <span style="margin-left:8px;">${escapeHtml(err.message || ('Failed to delete snapshot (HTTP ' + res.status + ').'))}</span>
            `);
        }
    } catch (e) {
        console.error('[Backup Delete] Network error', e);
        showToast(`
            <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
            <span style="margin-left:8px;">Network error.</span>
        `);
    }
}


// ============================================================
//  RESTORE MODAL (3-STEP)
// ============================================================
function setStep(n) {
    [1, 2, 3].forEach(s => {
        document.getElementById(`restore-step${s}`).style.display = s === n ? 'block' : 'none';
        const ind = document.getElementById(`step-${s}-ind`);
        if (s < n)      { ind.style.background = '#10b981'; ind.style.color = '#fff'; ind.innerHTML = '<i class="fa-solid fa-check" style="font-size:9px;"></i>'; }
        else if (s === n){ ind.style.background = '#e11d48'; ind.style.color = '#fff'; ind.textContent = s; }
        else             { ind.style.background = '#fecdd3'; ind.style.color = '#f43f5e'; ind.textContent = s; }
    });
    const labels = ['', 'Step 1 of 3 — Read the warning carefully', 'Step 2 of 3 — Authorization phrase', 'Step 3 of 3 — Final confirmation'];
    document.getElementById('step-label').textContent = labels[n];
}

function openRestoreModal(id) {
    if (isRestoring) return;
    currentRestoreId = id;
    document.getElementById('restore-target-label').textContent = id;
    document.getElementById('restore-confirm-input').value = '';
    document.getElementById('phrase-hint').textContent = '';
    document.getElementById('restore-proceed-btn').disabled = true;
    document.getElementById('restore-proceed-btn').style.opacity = '0.4';
    document.getElementById('restore-proceed-btn').style.cursor = 'not-allowed';
    document.getElementById('restore-btn-text').textContent = 'Authorize Restoration';
    setStep(1);
    document.getElementById('restore-modal').style.display = 'flex';
}

function closeRestoreModal() {
    document.getElementById('restore-modal').style.display = 'none';
    currentRestoreId = null;
}

function goRestoreStep1() { setStep(1); }
function goRestoreStep2() { setStep(2); }
function goRestoreStep3() { setStep(3); }

function checkRestorePhrase() {
    const val  = document.getElementById('restore-confirm-input').value;
    const hint = document.getElementById('phrase-hint');
    const btn  = document.getElementById('restore-proceed-btn');

    if (val === 'RESTORE') {
        hint.textContent = '✓ Phrase confirmed'; hint.style.color = '#059669';
        btn.disabled = false; btn.style.opacity = '1'; btn.style.cursor = 'pointer';
    } else if (val.length > 0) {
        hint.textContent = 'Phrase does not match — type exactly: RESTORE'; hint.style.color = '#e11d48';
        btn.disabled = true; btn.style.opacity = '0.4'; btn.style.cursor = 'not-allowed';
    } else {
        hint.textContent = '';
        btn.disabled = true; btn.style.opacity = '0.4'; btn.style.cursor = 'not-allowed';
    }
}

async function executeRestore() {
    if (!currentRestoreId) return;

    const btn = document.getElementById('final-restore-btn');
    document.getElementById('restore-btn-text').textContent = 'Dispatching...';
    btn.disabled = true;

    try {
        const res = await fetch(`/super-admin/backups/${currentRestoreId}/restore`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Content-Type': 'application/json', 'Accept': 'application/json' },
            body: JSON.stringify({ confirm_phrase: 'RESTORE' })
        });

        if (res.ok) {
            closeRestoreModal();
            // Real-time events will handle progress + completion
            showToast(`
                <i class="fa-solid fa-paper-plane" style="color:#60a5fa;font-size:13px;"></i>
                <span style="margin-left:8px;">Restore job dispatched — watching for real-time updates...</span>
            `);
        } else {
            const err = await res.json().catch(() => ({}));
            document.getElementById('restore-btn-text').textContent = 'Authorize Restoration';
            btn.disabled = false;
            showToast(`
                <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
                <span style="margin-left:8px;">${err.message || 'Restoration dispatch failed.'}</span>
            `);
        }
    } catch (e) {
        document.getElementById('restore-btn-text').textContent = 'Authorize Restoration';
        btn.disabled = false;
        showToast(`
            <i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i>
            <span style="margin-left:8px;">Network error during restoration.</span>
        `);
    }
}


// ============================================================
//  TOAST
// ============================================================
function showToast(html) {
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;align-items:center;
        background:#0f172a;color:#fff;padding:14px 20px;border-radius:16px;font-size:13px;
        font-family:'Inter',sans-serif;border:1px solid #1e293b;
        box-shadow:0 16px 48px rgba(0,0,0,0.35);max-width:420px;
        opacity:0;transition:opacity 0.3s,transform 0.3s;transform:translateY(10px);`;
    t.innerHTML = html;
    document.body.appendChild(t);

    requestAnimationFrame(() => { t.style.opacity = '1'; t.style.transform = 'translateY(0)'; });

    setTimeout(() => {
        t.style.opacity = '0';
        t.style.transform = 'translateY(10px)';
        setTimeout(() => t.remove(), 400);
    }, 5000);
}


// ============================================================
//  INIT — Render initial data
// ============================================================
renderSnapshots();

// Fetch fresh data on page load (one-time HTTP call)
fetchBackupTelemetry();
</script>
</body>
</html>