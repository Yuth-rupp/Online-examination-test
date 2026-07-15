<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Desk | ExamSystem Admin</title>
    <meta name="description" content="Manage and resolve student and instructor support tickets in ExamSystem.">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        body { background: #f8fafc; }

        /* ── Sidebar ── */
        .sidebar { background:#fff; border-right:1px solid #e8edf5; box-shadow:2px 0 12px rgba(0,0,0,0.04); }
        .brand-icon { background:linear-gradient(135deg,#2563eb,#1e40af); box-shadow:0 4px 12px rgba(37,99,235,0.3); }
        .nav-active { background:linear-gradient(135deg,#eff6ff,#dbeafe); color:#1d4ed8 !important; border:1px solid #bfdbfe; border-left:3px solid #2563eb; }
        .nav-active i { color:#2563eb !important; }
        .nav-item { border:1px solid transparent; border-left:3px solid transparent; color:#64748b; transition:all 0.18s ease; }
        .nav-item:hover { background:#f8fafc; border-color:#e2e8f0; border-left-color:#94a3b8; color:#1e293b; }

        /* ── Cards ── */
        .metric-card { background:#fff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(0,0,0,0.04),0 4px 16px rgba(0,0,0,0.03); transition:all 0.22s ease; }
        .metric-card:hover { box-shadow:0 4px 20px rgba(37,99,235,0.09); border-color:#bfdbfe; transform:translateY(-1px); }

        /* ── Progress bar ── */
        .progress-bar { height:3px; border-radius:999px; background:#f1f5f9; overflow:hidden; }
        .progress-fill { height:100%; border-radius:999px; transition:width 0.8s ease; }

        /* ── Pulse ── */
        @keyframes outerPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.7);opacity:0} }
        .pulse-dot { animation:outerPulse 1.8s ease-in-out infinite; }

        /* ── Ticket row animations ── */
        @keyframes slideIn { from{opacity:0;transform:translateX(-8px)} to{opacity:1;transform:translateX(0)} }
        .ticket-row-anim { animation:slideIn 0.3s ease forwards; }

        /* ── Filter tabs ── */
        .filter-tab { border:1px solid #e2e8f0; background:#fff; color:#64748b; transition:all 0.18s ease; }
        .filter-tab:hover { background:#f8fafc; color:#334155; }
        .filter-tab-active { background:linear-gradient(135deg,#2563eb,#1d4ed8) !important; border-color:#2563eb !important; color:#fff !important; box-shadow:0 3px 10px rgba(37,99,235,0.3); }

        /* ── Priority borders ── */
        .priority-urgent  { border-left:3px solid #ef4444; }
        .priority-high    { border-left:3px solid #f59e0b; }
        .priority-medium  { border-left:3px solid #3b82f6; }
        .priority-low     { border-left:3px solid #94a3b8; }

        /* ── Priority badges ── */
        .badge-urgent  { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .badge-high    { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
        .badge-medium  { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
        .badge-low     { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

        /* ── Status badges ── */
        .status-pending      { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
        .status-investigating { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }
        .status-resolved     { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .status-closed       { background:#f8fafc; color:#64748b; border:1px solid #e2e8f0; }

        /* ── Ticket row hover ── */
        .ticket-row { transition:background 0.15s ease; cursor:pointer; }
        .ticket-row:hover { background:#f8fafc; }

        /* ── Drawer ── */
        #ticket-drawer { transform:translateX(100%); transition:transform 0.3s cubic-bezier(0.16,1,0.3,1); }
        #ticket-drawer.open { transform:translateX(0); }

        /* ── Form inputs ── */
        .form-input { background:#f8fafc; border:1px solid #e2e8f0; transition:all 0.18s ease; }
        .form-input:focus { background:#fff; border-color:#93c5fd; box-shadow:0 0 0 3px rgba(147,197,253,0.25); outline:none; }

        /* ── Primary button ── */
        .btn-primary { background:linear-gradient(135deg,#2563eb,#1d4ed8); box-shadow:0 2px 8px rgba(37,99,235,0.3); }
        .btn-primary:hover { background:linear-gradient(135deg,#1d4ed8,#1e3a8a); }

        /* ── Table head ── */
        .table-head { background:linear-gradient(135deg,#f8fafc,#f1f5f9); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:#f1f5f9; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }

        /* ── Ticket card surface ── */
        .table-card { background:#fff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(0,0,0,0.04),0 6px 24px rgba(0,0,0,0.03); }

        /* ── Avatar colors ── */
        .av-blue   { background:#eff6ff; color:#1d4ed8; border:2px solid #bfdbfe; }
        .av-purple { background:#f5f3ff; color:#6d28d9; border:2px solid #ddd6fe; }
        .av-green  { background:#f0fdf4; color:#15803d; border:2px solid #bbf7d0; }
        .av-amber  { background:#fffbeb; color:#92400e; border:2px solid #fde68a; }

        /* ── Reply area ── */
        .reply-area { background:#f8fafc; border:1px solid #e2e8f0; }
        .reply-area:focus { background:#fff; border-color:#93c5fd; box-shadow:0 0 0 3px rgba(147,197,253,0.2); outline:none; }

        /* ── Shimmer on refresh ── */
        @keyframes shimmer { 0%,100%{opacity:1} 50%{opacity:0.5} }
        .shimmer { animation:shimmer 0.6s ease; }
    </style>
</head>
<body class="antialiased text-slate-800">
<div class="flex min-h-screen">

    <!-- ════════════ SIDEBAR ════════════ -->
    <aside class="sidebar w-64 flex flex-col justify-between fixed h-full z-20">
        <div>
            <div class="px-6 py-5 flex items-center gap-3 border-b border-slate-100">
                <div class="brand-icon w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-sm leading-tight">ExamSystem</h1>
                    <span class="text-[11px] text-slate-400 font-medium">Admin Console</span>
                </div>
            </div>
            <nav class="p-3 mt-1 space-y-0.5">
                <a href="{{ route('admin.dashboard') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-chart-line w-5 text-center text-slate-400 text-sm"></i><span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-users-gear w-5 text-center text-slate-400 text-sm"></i><span>User Management</span>
                </a>
                <a href="{{ route('admin.exams') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-file-pen w-5 text-center text-slate-400 text-sm"></i><span>Exams</span>
                </a>
                <a href="{{ route('admin.support') }}" class="nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm">
                    <i class="fa-solid fa-headset w-5 text-center text-sm"></i>
                    <span>Support Desk</span>
                    <span id="sidebar-badge" class="ml-auto text-[9px] font-bold bg-red-500 text-white px-1.5 py-0.5 rounded-full hidden">0</span>
                </a>
                <a href="{{ route('admin.security') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-shield-halved w-5 text-center text-slate-400 text-sm"></i><span>Security</span>
                </a>
            </nav>
        </div>
        <div class="p-3 border-t border-slate-100">
            <a href="{{ route('admin.settings') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                <i class="fa-solid fa-gear w-5 text-center text-slate-400 text-sm"></i><span>Settings</span>
            </a>
        </div>
    </aside>

    <!-- ════════════ MAIN CONTENT ════════════ -->
    <main class="flex-1 ml-64 p-7 min-h-screen">

        <!-- HEADER -->
        <header class="flex items-center justify-between mb-7">
            <div class="flex items-center gap-3">
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold text-emerald-700 border" style="background:#f0fdf4;border-color:#bbf7d0;">
                    <span class="relative flex items-center justify-center w-2 h-2">
                        <span class="pulse-dot absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    System Status: <strong class="text-emerald-600">Healthy</strong>
                </span>
                <div class="text-xs font-mono text-slate-400 flex items-center gap-1.5 bg-white border border-slate-200 px-3 py-1.5 rounded-xl">
                    <i class="fa-solid fa-rotate text-[10px] text-slate-300"></i>
                    <span id="live-clock">--:--:--</span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <h4 class="text-sm font-semibold text-slate-900 leading-tight">{{ Auth::user()->full_name ?? 'Admin User' }}</h4>
                    <span class="text-xs text-slate-400">Super Administrator</span>
                </div>
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#f59e0b,#d97706);box-shadow:0 3px 10px rgba(245,158,11,0.3)">
                    {{ Auth::user()->initials ?? 'AU' }}
                </div>
            </div>
        </header>

        <!-- PAGE TITLE -->
        <div class="mb-6">
            <h2 class="text-xl font-bold text-slate-900 flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-headset text-sm"></i>
                </span>
                Support Desk
            </h2>
            <p class="text-sm text-slate-400 mt-1">Review, respond to, and resolve support tickets from students and instructors.</p>
        </div>

        <!-- Flash -->
        @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl flex items-center gap-2.5 text-sm font-medium text-emerald-700" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
        </div>
        @endif

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Users</p>
                        <h3 id="stat-users" class="text-3xl font-black text-slate-900">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div class="progress-fill bg-blue-400" style="width:70%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Registered platform accounts</p>
            </div>

            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Open Tickets</p>
                        <h3 id="stat-open" class="text-3xl font-black text-slate-900">0</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#fffbeb;border:1px solid #fde68a">
                        <i class="fa-solid fa-ticket text-amber-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div id="open-bar" class="progress-fill bg-amber-400" style="width:0%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Awaiting response</p>
            </div>

            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Investigating</p>
                        <h3 id="stat-investigating" class="text-3xl font-black text-slate-900">0</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-magnifying-glass text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div id="inv-bar" class="progress-fill bg-blue-400" style="width:0%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Currently being handled</p>
            </div>

            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Resolved Today</p>
                        <h3 id="stat-resolved" class="text-3xl font-black text-slate-900">0</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="fa-solid fa-circle-check text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div id="res-bar" class="progress-fill bg-emerald-400" style="width:0%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Closed since midnight</p>
            </div>
        </div>

        <!-- FILTER TABS + SEARCH -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-5">
            <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <button data-filter="all"          id="tab-all"          class="filter-tab filter-tab-active px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    All Tickets <span id="count-all" class="ml-1 text-[9px] bg-white/30 px-1.5 py-0.5 rounded-full">0</span>
                </button>
                <button data-filter="pending"      id="tab-pending"      class="filter-tab px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block mr-1.5 align-middle"></span>Pending
                    <span id="count-pending" class="ml-1 text-[9px] text-slate-400">0</span>
                </button>
                <button data-filter="in_progress"  id="tab-in_progress"  class="filter-tab px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-400 inline-block mr-1.5 align-middle"></span>Investigating
                    <span id="count-in_progress" class="ml-1 text-[9px] text-slate-400">0</span>
                </button>
                <button data-filter="resolved"     id="tab-resolved"     class="filter-tab px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block mr-1.5 align-middle"></span>Resolved
                    <span id="count-resolved" class="ml-1 text-[9px] text-slate-400">0</span>
                </button>
            </div>

            <!-- Search -->
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input id="ticket-search" type="text" placeholder="Search by name, email or category…"
                    class="form-input pl-10 pr-4 py-2.5 rounded-xl text-sm text-slate-700 placeholder-slate-400 w-64">
            </div>
        </div>

        <!-- TICKETS TABLE CARD -->
        <div class="table-card rounded-2xl overflow-hidden">

            <!-- Table header -->
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between" style="background:linear-gradient(135deg,#f8fafc,#f1f5f9)">
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Incident Queue</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Click any row to open the ticket detail panel</p>
                </div>
                <span class="text-xs font-mono text-slate-400 px-2.5 py-1 rounded-lg bg-white border border-slate-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block mr-1.5 align-middle"></span>
                    Live · updates every 3s
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="table-head border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Reporter</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Issue / Category</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Priority</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Submitted</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="tickets-body" class="divide-y divide-slate-50">
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#f1f5f9;border:1px solid #e2e8f0">
                                        <i class="fa-solid fa-rotate text-slate-300 text-lg"></i>
                                    </div>
                                    <p class="text-sm text-slate-400">Loading tickets…</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<!-- ════════════ TICKET DETAIL DRAWER ════════════ -->
<div id="drawer-backdrop" class="hidden fixed inset-0 z-30" style="background:rgba(15,23,42,0.3);backdrop-filter:blur(3px)" onclick="closeDrawer()"></div>
<div id="ticket-drawer" class="fixed top-0 right-0 h-full w-full max-w-lg bg-white z-40 shadow-2xl overflow-hidden flex flex-col" style="border-left:1px solid #e8edf5">

    <!-- Drawer header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0" style="background:#f8fafc">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                <i class="fa-solid fa-ticket text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-sm" id="drawer-ticket-no">Ticket</h3>
                <p class="text-[11px] text-slate-400" id="drawer-ticket-time">—</p>
            </div>
        </div>
        <button onclick="closeDrawer()" class="w-7 h-7 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-all">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Drawer body -->
    <div class="flex-1 overflow-y-auto p-6 space-y-5">

        <!-- Reporter info -->
        <div class="flex items-center gap-4 p-4 rounded-2xl" style="background:#f8fafc;border:1px solid #e2e8f0">
            <div id="drawer-avatar" class="av-blue w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm uppercase shrink-0">--</div>
            <div class="flex-1 min-w-0">
                <h4 id="drawer-name" class="font-bold text-slate-900 text-sm leading-tight">—</h4>
                <p id="drawer-email" class="text-xs text-slate-400 font-mono mt-0.5 truncate">—</p>
            </div>
            <span id="drawer-priority-badge" class="badge-high text-[10px] font-bold px-2.5 py-1 rounded-full">HIGH</span>
        </div>

        <!-- Issue details -->
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Issue Details</p>
            <div class="p-4 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0">
                <div class="flex items-center gap-2 mb-2">
                    <span id="drawer-category" class="text-sm font-bold text-slate-800">—</span>
                    <span id="drawer-ticket-ref" class="text-[10px] font-mono text-slate-400 bg-white border border-slate-200 px-2 py-0.5 rounded-lg">—</span>
                </div>
                <p id="drawer-description" class="text-sm text-slate-500 leading-relaxed">—</p>
            </div>
        </div>

        <!-- Status & quick actions -->
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Quick Actions</p>
            <div class="flex flex-wrap gap-2">
                <button id="drawer-action-progress" onclick="updateTicketStatus('in_progress')"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all"
                    style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8"
                    onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                    <i class="fa-solid fa-magnifying-glass text-[10px]"></i> Mark Investigating
                </button>
                <button onclick="updateTicketStatus('resolved')"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all"
                    style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d"
                    onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                    <i class="fa-solid fa-circle-check text-[10px]"></i> Mark Resolved
                </button>
                <button onclick="updateTicketStatus('closed')"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-bold transition-all"
                    style="background:#f8fafc;border:1px solid #e2e8f0;color:#64748b"
                    onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'">
                    <i class="fa-solid fa-ban text-[10px]"></i> Close Ticket
                </button>
            </div>
        </div>

        <!-- Priority change -->
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Change Priority</p>
            <select id="drawer-priority-select" onchange="updatePriority(this.value)"
                class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 bg-white cursor-pointer">
                <option value="low">🔵 Low</option>
                <option value="medium">🟡 Medium</option>
                <option value="high" selected>🟠 High</option>
                <option value="urgent">🔴 Urgent</option>
            </select>
        </div>

        <!-- Assign agent -->
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Assigned Agent</p>
            <select id="drawer-agent-select"
                class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 bg-white cursor-pointer">
                <option value="">— Unassigned —</option>
                @foreach($agents ?? [] as $agent)
                <option value="{{ $agent->user_id }}">{{ $agent->full_name }}</option>
                @endforeach
                <option value="1">Support Agent 1</option>
                <option value="2">Support Agent 2</option>
            </select>
        </div>

        <!-- Reply box -->
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Reply to Reporter</p>
            <textarea id="drawer-reply" rows="4" placeholder="Type your response here…"
                class="reply-area w-full px-4 py-3 rounded-xl text-sm text-slate-800 placeholder-slate-400 resize-none transition-all"></textarea>
        </div>

        <!-- Action log placeholder -->
        <div>
            <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Activity Timeline</p>
            <div id="drawer-timeline" class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-ticket text-blue-500" style="font-size:8px"></i>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-slate-700">Ticket submitted</p>
                        <p class="text-[10px] text-slate-400 font-mono mt-0.5">Reporter opened this ticket</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Drawer footer -->
    <div class="shrink-0 px-6 py-4 border-t border-slate-100 space-y-2.5" style="background:#f8fafc">
        <!-- Connection hint -->
        <div class="flex items-center gap-2 p-2.5 rounded-xl text-[11px] font-medium text-blue-700" style="background:#eff6ff;border:1px solid #bfdbfe">
            <i class="fa-solid fa-link text-blue-500 text-[10px]"></i>
            <span>This ticket is live in the queue. Changes update instantly.</span>
        </div>
        <div class="flex gap-2.5">
            <button onclick="submitReply()" class="flex-1 btn-primary text-white py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all">
                <i class="fa-solid fa-paper-plane"></i> Send Reply
            </button>
            <a id="drawer-fullpage-link" href="#"
                class="flex-1 py-2.5 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition-all"
                style="background:#f0fdf4;border:1px solid #bbf7d0;color:#15803d"
                onmouseover="this.style.background='#dcfce7'" onmouseout="this.style.background='#f0fdf4'">
                <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> Full Resolution
            </a>
        </div>
        <button onclick="closeDrawer()" class="w-full py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-700 transition-all">
            ✕ Close panel
        </button>
    </div>
</div>

<!-- ════════════ SCRIPTS ════════════ -->
<script>
    /* ── Live clock ── */
    setInterval(() => {
        document.getElementById('live-clock').textContent = new Date().toLocaleTimeString();
    }, 1000);
    document.getElementById('live-clock').textContent = new Date().toLocaleTimeString();

    /* ── State ── */
    let activeFilter   = 'all';
    let activeTicketId = null;
    let allTickets     = [];
    let searchQuery    = '';

    /* ── Avatar colors ── */
    const avColors = ['av-blue','av-purple','av-green','av-amber'];

    /* ── Priority config ── */
    const PRIORITY_CFG = {
        urgent: { cls:'badge-urgent', label:'URGENT', border:'priority-urgent' },
        high:   { cls:'badge-high',   label:'HIGH',   border:'priority-high' },
        medium: { cls:'badge-medium', label:'MED',    border:'priority-medium' },
        low:    { cls:'badge-low',    label:'LOW',    border:'priority-low' },
    };
    const STATUS_CFG = {
        pending:     { cls:'status-pending',      label:'Pending',      icon:'fa-clock' },
        in_progress: { cls:'status-investigating',label:'Investigating', icon:'fa-magnifying-glass' },
        resolved:    { cls:'status-resolved',     label:'Resolved',     icon:'fa-circle-check' },
        closed:      { cls:'status-closed',       label:'Closed',       icon:'fa-ban' },
    };

    /* ── Render rows ── */
    function renderTickets(tickets) {
        const body = document.getElementById('tickets-body');
        const q    = searchQuery.toLowerCase();
        const filtered = tickets.filter(t => {
            const matchFilter = activeFilter === 'all' || t.status === activeFilter;
            const matchSearch = !q ||
                (t.reporter_name  || '').toLowerCase().includes(q) ||
                (t.reporter_email || '').toLowerCase().includes(q) ||
                (t.issue_category || '').toLowerCase().includes(q);
            return matchFilter && matchSearch;
        });

        if (filtered.length === 0) {
            body.innerHTML = `
                <tr><td colspan="6" class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl flex items-center justify-center" style="background:#f1f5f9;border:1px solid #e2e8f0">
                            <i class="fa-solid fa-inbox text-slate-300 text-lg"></i>
                        </div>
                        <p class="text-sm font-semibold text-slate-400">No tickets in this queue</p>
                        <p class="text-xs text-slate-300">Try a different filter or search term</p>
                    </div>
                </td></tr>`;
            return;
        }

        body.innerHTML = filtered.map((t, idx) => {
            const status   = (t.status || 'pending').toLowerCase();
            const priority = (t.priority || 'high').toLowerCase();
            const sCfg     = STATUS_CFG[status]   || STATUS_CFG.pending;
            const pCfg     = PRIORITY_CFG[priority]|| PRIORITY_CFG.high;
            const avCls    = avColors[idx % avColors.length];
            const initials = (t.reporter_name || '?').substring(0, 2).toUpperCase();
            const ref      = t.ticket_no || `#SUP-${t.ticket_id}`;
            const cat      = t.issue_category || 'General Support';
            const desc     = (t.description || '—').substring(0, 80) + ((t.description || '').length > 80 ? '…' : '');
            const time     = t.created_at ? new Date(t.created_at).toLocaleString() : 'Just now';

            return `
            <tr class="ticket-row ${pCfg.border} ticket-row-anim" onclick='openDrawer(${JSON.stringify(t)})' data-id="${t.ticket_id}">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="${avCls} w-9 h-9 rounded-xl flex items-center justify-center font-bold text-[11px] shrink-0">${initials}</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 leading-tight">${t.reporter_name || '—'}</p>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">${t.reporter_email || '—'}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-2 mb-0.5">
                        <span class="text-xs font-bold text-slate-800">${cat}</span>
                        <span class="text-[9px] font-mono text-slate-400 bg-slate-50 border border-slate-200 px-1.5 py-0.5 rounded">${ref}</span>
                    </div>
                    <p class="text-[11px] text-slate-400 max-w-xs truncate">${desc}</p>
                </td>
                <td class="px-6 py-4">
                    <span class="${pCfg.cls} text-[10px] font-bold px-2.5 py-1 rounded-full">${pCfg.label}</span>
                </td>
                <td class="px-6 py-4">
                    <span class="${sCfg.cls} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold">
                        <i class="fa-solid ${sCfg.icon}" style="font-size:8px"></i>${sCfg.label}
                    </span>
                </td>
                <td class="px-6 py-4 text-xs text-slate-400 font-mono">${time}</td>
                <td class="px-6 py-4 text-center">
                    <div class="flex items-center justify-center gap-1.5">
                        <!-- Quick preview in drawer -->
                        <button
                            style="background:#f8fafc;border:1px solid #e2e8f0;color:#475569"
                            class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg transition-all flex items-center gap-1"
                            onmouseover="this.style.background='#f1f5f9'" onmouseout="this.style.background='#f8fafc'"
                            onclick="event.stopPropagation(); openDrawer(${JSON.stringify(t)})"
                            title="Quick preview">
                            <i class="fa-solid fa-eye" style="font-size:9px"></i>
                        </button>
                        <!-- Full resolve page link -->
                        <a href="/admin/support/${t.ticket_id}/ticket-review"
                            onclick="event.stopPropagation()"
                            style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8"
                            class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg transition-all flex items-center gap-1"
                            onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'"
                            title="Open full resolution page">
                            <i class="fa-solid fa-arrow-up-right-from-square" style="font-size:9px"></i> Resolve
                        </a>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    /* ── Update stat cards ── */
    function updateStats(tickets) {
        const open  = tickets.filter(t => t.status === 'pending').length;
        const inv   = tickets.filter(t => t.status === 'in_progress').length;
        const res   = tickets.filter(t => t.status === 'resolved').length;
        const total = tickets.length;

        setStatEl('stat-open',        open);
        setStatEl('stat-investigating',inv);
        setStatEl('stat-resolved',    res);
        document.getElementById('open-bar').style.width = Math.min(open  * 12, 100) + '%';
        document.getElementById('inv-bar').style.width  = Math.min(inv   * 12, 100) + '%';
        document.getElementById('res-bar').style.width  = Math.min(res   * 12, 100) + '%';

        /* Tab counts */
        document.getElementById('count-all').textContent        = total;
        document.getElementById('count-pending').textContent    = open;
        document.getElementById('count-in_progress').textContent = inv;
        document.getElementById('count-resolved').textContent   = res;

        /* Sidebar badge */
        const badge = document.getElementById('sidebar-badge');
        if (open > 0) { badge.textContent = open; badge.classList.remove('hidden'); }
        else          { badge.classList.add('hidden'); }
    }
    function setStatEl(id, val) {
        const el = document.getElementById(id);
        if (el && el.textContent !== String(val)) { el.textContent = val; el.classList.add('shimmer'); setTimeout(()=>el.classList.remove('shimmer'),600); }
    }

    /* ── Fetch & refresh ── */
    function refresh() {
        fetch(`/admin/support/telemetry-stream?filter=all`)
            .then(r => r.json())
            .then(data => {
                allTickets = data.tickets || [];
                updateStats(allTickets);
                renderTickets(allTickets);
                if (data.totalUsers) document.getElementById('stat-users').textContent = Number(data.totalUsers).toLocaleString();
            })
            .catch(() => {});
    }
    refresh();
    setInterval(refresh, 3000);

    /* ── Search ── */
    document.getElementById('ticket-search').addEventListener('input', function() {
        searchQuery = this.value;
        renderTickets(allTickets);
    });

    /* ── Filter tabs ── */
    document.querySelectorAll('.filter-tab').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.filter-tab').forEach(b => b.classList.remove('filter-tab-active'));
            btn.classList.add('filter-tab-active');
            activeFilter = btn.dataset.filter;
            renderTickets(allTickets);
        });
    });

    /* ── Drawer open ── */
    function openDrawer(ticket) {
        activeTicketId = ticket.ticket_id;
        const status   = (ticket.status || 'pending').toLowerCase();
        const priority = (ticket.priority || 'high').toLowerCase();
        const pCfg     = PRIORITY_CFG[priority] || PRIORITY_CFG.high;
        const avCls    = avColors[Math.abs(ticket.ticket_id) % avColors.length];
        const ref      = ticket.ticket_no || `#SUP-${ticket.ticket_id}`;

        document.getElementById('drawer-ticket-no').textContent     = 'Ticket ' + ref;
        document.getElementById('drawer-ticket-time').textContent   = ticket.created_at ? new Date(ticket.created_at).toLocaleString() : 'Just now';
        document.getElementById('drawer-avatar').textContent        = (ticket.reporter_name || '??').substring(0,2).toUpperCase();
        document.getElementById('drawer-avatar').className          = `${avCls} w-12 h-12 rounded-2xl flex items-center justify-center font-bold text-sm uppercase shrink-0`;
        document.getElementById('drawer-name').textContent          = ticket.reporter_name  || '—';
        document.getElementById('drawer-email').textContent         = ticket.reporter_email || '—';
        document.getElementById('drawer-category').textContent      = ticket.issue_category || 'General Support';
        document.getElementById('drawer-ticket-ref').textContent    = ref;
        document.getElementById('drawer-description').textContent   = ticket.description    || '—';
        document.getElementById('drawer-priority-badge').textContent = pCfg.label;
        document.getElementById('drawer-priority-badge').className  = `${pCfg.cls} text-[10px] font-bold px-2.5 py-1 rounded-full`;
        document.getElementById('drawer-priority-select').value     = priority;

        document.getElementById('drawer-edit-btn') && (document.getElementById('drawer-edit-btn').href = `/admin/support/${ticket.ticket_id}/ticket-review`);
        const fullLink = document.getElementById('drawer-fullpage-link');
        if (fullLink) fullLink.href = `/admin/support/${ticket.ticket_id}/ticket-review`;
        document.getElementById('drawer-backdrop').classList.remove('hidden');
        document.getElementById('ticket-drawer').classList.add('open');
    }

    /* ── Drawer close ── */
    function closeDrawer() {
        document.getElementById('ticket-drawer').classList.remove('open');
        document.getElementById('drawer-backdrop').classList.add('hidden');
        activeTicketId = null;
    }

    /* ── Update status ── */
    function updateTicketStatus(newStatus) {
        if (!activeTicketId) return;
        fetch(`/admin/support/${activeTicketId}/status`, {
            method: 'PATCH',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content||''},
            body: JSON.stringify({status: newStatus})
        }).then(r => {
            if (r.ok) {
                const sCfg = STATUS_CFG[newStatus] || STATUS_CFG.pending;
                addTimeline(`Status changed to <strong>${sCfg.label}</strong>`);
                refresh();
            }
        }).catch(() => {
            addTimeline(`Status marked as <strong>${newStatus}</strong> (offline)`);
        });
    }

    /* ── Update priority ── */
    function updatePriority(priority) {
        if (!activeTicketId) return;
        const pCfg = PRIORITY_CFG[priority] || PRIORITY_CFG.high;
        document.getElementById('drawer-priority-badge').textContent = pCfg.label;
        document.getElementById('drawer-priority-badge').className  = `${pCfg.cls} text-[10px] font-bold px-2.5 py-1 rounded-full`;
        addTimeline(`Priority changed to <strong>${pCfg.label}</strong>`);
    }

    /* ── Submit reply ── */
    function submitReply() {
        const text = document.getElementById('drawer-reply').value.trim();
        if (!text) return;
        fetch(`/admin/support/${activeTicketId}/reply`, {
            method: 'POST',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content||''},
            body: JSON.stringify({message: text})
        }).catch(()=>{});
        addTimeline(`Reply sent: "<em>${text.substring(0,60)}${text.length>60?'…':''}</em>"`);
        document.getElementById('drawer-reply').value = '';
    }

    /* ── Add timeline entry ── */
    function addTimeline(html) {
        const tl = document.getElementById('drawer-timeline');
        const el = document.createElement('div');
        el.className = 'flex items-start gap-3';
        el.innerHTML = `
            <div class="w-6 h-6 rounded-full flex items-center justify-center shrink-0 mt-0.5" style="background:#f0fdf4;border:1px solid #bbf7d0">
                <i class="fa-solid fa-check text-emerald-500" style="font-size:8px"></i>
            </div>
            <div>
                <p class="text-xs font-semibold text-slate-700">${html}</p>
                <p class="text-[10px] text-slate-400 font-mono mt-0.5">${new Date().toLocaleTimeString()}</p>
            </div>`;
        tl.prepend(el);
    }
</script>
</body>
</html>