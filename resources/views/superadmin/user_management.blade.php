<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>User Management — ExamSystem</title>
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
        .fade-in{opacity:0;animation:fadeIn 0.3s ease-out forwards;}
        @keyframes modalIn{from{opacity:0;transform:translateY(16px) scale(0.97)}to{opacity:1;transform:none}}
        .modal-in{animation:modalIn 0.28s cubic-bezier(0.22,1,0.36,1) forwards;}
        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}
        .row-hover:hover{background:#f8fafc;}
        .tab-btn{padding:8px 18px;border-radius:10px;font-size:11px;font-weight:700;cursor:pointer;
                 letter-spacing:0.04em;text-transform:uppercase;border:none;transition:all 0.2s;}
        .tab-btn.active{background:#2563eb;color:#fff;box-shadow:0 4px 12px rgba(37,99,235,0.28);}
        .tab-btn:not(.active){background:transparent;color:#64748b;}
        .tab-btn:not(.active):hover{background:#fff;color:#1e293b;}
        .toast-enter{opacity:0;transform:translateY(12px);transition:opacity 0.3s,transform 0.3s;}
        .toast-visible{opacity:1;transform:translateY(0);}
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

            <a href="{{ route('superadmin.admins.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-users text-xs text-white"></i></span><span>User Management</span>
            </a>

            {{-- 🚀 DEPARTMENT DIRECTORY LINK ADDED HERE --}}
            <a href="{{ route('superadmin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-building-columns text-xs text-slate-400"></i></span><span>Department Directory</span>
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
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">User Management</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    All system roles — super admins, admins, teachers, students &nbsp;
                    <span id="total-record-count" class="font-bold text-slate-600">...</span>
                </p>
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
                    Polling: <span id="poll-countdown" class="font-mono font-black ml-1 mr-0.5 text-emerald-900 w-3 text-center">3</span>s
                </div>
                <button onclick="openModal()" class="flex items-center gap-2 text-xs font-bold text-white px-4 py-2.5 rounded-xl transition-all"
                        style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);"
                        onmouseenter="this.style.background='#1d4ed8'" onmouseleave="this.style.background='#2563eb'">
                    <i class="fa-solid fa-user-plus text-xs"></i> Add Account
                </button>
            </div>
        </header>

        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:20px;">

            {{-- ========== METRIC CARDS ========== --}}
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                @php
                    $cards = [
                        ['id'=>'card-total','label'=>'Total Users','icon'=>'fa-users','gradient'=>'#eff6ff,#dbeafe','iconColor'=>'text-blue-500','valueColor'=>'text-slate-900'],
                        ['id'=>'card-superadmin','label'=>'Super Admins','icon'=>'fa-user-shield','gradient'=>'#f5f3ff,#ede9fe','iconColor'=>'text-violet-500','valueColor'=>'text-violet-600'],
                        ['id'=>'card-teacher','label'=>'Teachers','icon'=>'fa-chalkboard-user','gradient'=>'#fffbeb,#fef3c7','iconColor'=>'text-amber-500','valueColor'=>'text-amber-600'],
                        ['id'=>'card-student','label'=>'Students','icon'=>'fa-graduation-cap','gradient'=>'#f0fdf4,#dcfce7','iconColor'=>'text-emerald-500','valueColor'=>'text-emerald-600'],
                    ];
                @endphp
                @foreach($cards as $card)
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,{{ $card['gradient'] }});">
                        <i class="fa-solid {{ $card['icon'] }} {{ $card['iconColor'] }} text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">{{ $card['label'] }}</p>
                        <p id="{{ $card['id'] }}" class="text-2xl font-black {{ $card['valueColor'] }} leading-none tabular-nums count-animate">0</p>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ========== TABLE ========== --}}
            <div class="bg-white rounded-2xl border border-slate-100 flex flex-col overflow-hidden"
                 style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-wrap gap-3">
                    <div class="flex items-center gap-1 p-1 rounded-xl" style="background:#f1f5f9;">
                        <button class="tab-btn active" data-role="all" onclick="setTab('all',this)">All</button>
                        <button class="tab-btn" data-role="super_admin" onclick="setTab('super_admin',this)">Super Admins</button>
                        <button class="tab-btn" data-role="admin" onclick="setTab('admin',this)">Admins</button>
                        <button class="tab-btn" data-role="teacher" onclick="setTab('teacher',this)">Teachers</button>
                        <button class="tab-btn" data-role="student" onclick="setTab('student',this)">Students</button>
                    </div>
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2" style="min-width:220px;">
                        <i class="fa-solid fa-magnifying-glass text-slate-300 text-xs"></i>
                        <input id="search-input" type="text" placeholder="Search name or email..." oninput="renderTable()"
                               class="text-xs font-medium text-slate-700 bg-transparent outline-none flex-1 placeholder-slate-300" style="min-width:0;">
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Role</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Department</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body" class="divide-y divide-slate-50"></tbody>
                    </table>
                </div>
                <div class="flex items-center justify-between px-6 py-3.5 border-t border-slate-100 bg-slate-50">
                    <p id="table-footer-count" class="text-[11px] text-slate-400 font-medium"></p>
                    <p class="text-[11px] text-slate-400 font-medium">Auto-refreshes seamlessly in live background</p>
                </div>
            </div>
        </div>
    </main>
</div>


{{-- ===================== CREATE MODAL ===================== --}}
<div id="create-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.50);backdrop-filter:blur(8px);">
    <div class="modal-in bg-white rounded-2xl max-w-md w-full border border-slate-100"
         style="box-shadow:0 24px 64px rgba(15,23,42,0.22);" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-user-plus text-blue-600 text-sm"></i></div>
                <h3 class="font-bold text-sm text-slate-900">Add New Account</h3>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                <input id="new-name" type="text" placeholder="e.g. John Smith" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all focus:border-blue-500 focus:bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                <input id="new-email" type="email" placeholder="john@school.edu" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all focus:border-blue-500 focus:bg-white">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Password</label>
                <div style="position:relative;">
                    <input id="new-password" type="password" placeholder="Min 8 characters" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all focus:border-blue-500 focus:bg-white">
                    <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;background:none;border:none;cursor:pointer;">
                        <i id="pw-eye" class="fa-solid fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Role</label>
                <div class="grid grid-cols-2 gap-2" id="role-picker"></div>
            </div>

            {{-- ═══ DEPARTMENT DROPDOWN ═══ --}}
            <div id="dept-field" style="display:none;">
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">
                    <i class="fa-solid fa-building-columns mr-1 text-blue-400"></i> Assign to Department
                </label>
                <select id="new-department"
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all focus:border-blue-500 focus:bg-white cursor-pointer">
                    <option value="">— No department (assign later) —</option>
                </select>
                <p id="dept-hint" class="text-[10px] text-slate-400 font-medium mt-1.5 leading-relaxed"></p>
            </div>
        </div>
        <div class="flex items-center gap-3 px-6 pb-6">
            <button onclick="closeModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Cancel</button>
            <button id="submit-btn" onclick="submitCreateAccount()" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);">
                <i class="fa-solid fa-user-plus mr-1.5"></i> Create Account
            </button>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2" style="pointer-events:none;"></div>

{{-- ===================== SCRIPTS ===================== --}}
<script>
(function() {
    'use strict';
    const CURRENT_AUTH_ID = {{ auth()->id() ?? 0 }};
    const CSRF = document.querySelector('meta[name=csrf-token]').content;

    const ALL_DEPARTMENTS = {!! json_encode($departments ?? []) !!};

    const ROLE_GRADS = {
        super_admin: 'linear-gradient(135deg,#7c3aed,#6d28d9)',
        admin:       'linear-gradient(135deg,#2563eb,#1d4ed8)',
        teacher:     'linear-gradient(135deg,#f59e0b,#d97706)',
        student:     'linear-gradient(135deg,#10b981,#059669)',
    };
    const ROLE_BADGE = {
        super_admin: { bg:'#f5f3ff', color:'#6d28d9', border:'#ede9fe', label:'Super Admin' },
        admin:       { bg:'#eff6ff', color:'#1d4ed8', border:'#bfdbfe', label:'Admin' },
        teacher:     { bg:'#fffbeb', color:'#b45309', border:'#fde68a', label:'Teacher' },
        student:     { bg:'#f0fdf4', color:'#059669', border:'#a7f3d0', label:'Student' },
    };

    let allUsers = {!! json_encode($admins ?? []) !!};
    let activeTab = 'all';
    let selectedRole = 'admin';
    let isInitialRender = true;

    function updateClock() {
        document.getElementById('live-clock').textContent =
            new Date().toLocaleTimeString('en-US', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateClock(); setInterval(updateClock, 1000);

    let pollCount = 3;
    const pollEl = document.getElementById('poll-countdown');
    setInterval(() => {
        pollCount--;
        if (pollCount <= 0) { pollCount = 3; fetchLatestUsers(); }
        pollEl.textContent = pollCount;
    }, 1000);

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
        const sa = allUsers.filter(u => u.role === 'super_admin').length;
        const ad = allUsers.filter(u => u.role === 'admin').length;
        const te = allUsers.filter(u => u.role === 'teacher').length;
        const st = allUsers.filter(u => u.role === 'student').length;
        setMetric('card-total', allUsers.length);
        setMetric('card-superadmin', sa);
        setMetric('card-teacher', te);
        setMetric('card-student', st);
        document.getElementById('total-record-count').textContent = `(${allUsers.length} records)`;
    }

    function getInitials(name) {
        if (!name) return '??';
        return name.split(' ').map(w => w[0]).slice(0, 2).join('').toUpperCase();
    }
    function esc(s) { const d = document.createElement('div'); d.appendChild(document.createTextNode(s || '')); return d.innerHTML; }

    function filteredUsers() {
        const search = (document.getElementById('search-input').value || '').toLowerCase();
        return allUsers.filter(u => {
            const matchRole = activeTab === 'all' || u.role === activeTab;
            const matchSearch = !search || (u.full_name || '').toLowerCase().includes(search) || (u.email || '').toLowerCase().includes(search);
            return matchRole && matchSearch;
        });
    }

    function getDeptName(u) {
        if (u.department && u.department.name) return u.department.name;
        if (u.department_id) {
            const d = ALL_DEPARTMENTS.find(dep => dep.id === u.department_id);
            if (d) return d.name;
        }
        return null;
    }

    // ============================================================
    //  RENDER TABLE
    // ============================================================
    window.renderTable = function(forceAnimation = false) {
        const tbody = document.getElementById('user-table-body');
        const users = filteredUsers();
        document.getElementById('table-footer-count').textContent = `Showing ${users.length} of ${allUsers.length} accounts`;

        if (!users.length) {
            tbody.innerHTML = `<tr><td colspan="5" style="padding:48px;text-align:center;">
                <i class="fa-solid fa-users-slash" style="font-size:28px;color:#e2e8f0;display:block;margin-bottom:10px;"></i>
                <p style="font-size:12px;font-weight:600;color:#94a3b8;">No users match this filter</p></td></tr>`;
            return;
        }

        const useAnim = isInitialRender || forceAnimation;

        users.forEach((u, i) => {
            let row = document.getElementById(`user-row-${u.user_id}`);
            const rb = ROLE_BADGE[u.role] || { bg:'#f1f5f9', color:'#64748b', border:'#e2e8f0', label:u.role };
            const grad = ROLE_GRADS[u.role] || ROLE_GRADS.student;
            const isSelf = u.user_id === CURRENT_AUTH_ID;
            const isActive = u.status === 'active';
            const deptName = getDeptName(u);

            // Department cell dropdown
            let deptCellHtml;
            if (isSelf) {
                deptCellHtml = `<span class="text-[11px] text-slate-300 font-medium">—</span>`;
            } else if (!ALL_DEPARTMENTS || ALL_DEPARTMENTS.length === 0) {
                deptCellHtml = `<span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">No Depts Created</span>`;
            } else {
                deptCellHtml = `
                    <select onchange="updateUserDepartment(${u.user_id},this.value)"
                            class="text-[11px] font-bold border ${deptName ? 'border-slate-200 bg-slate-50 text-slate-600' : 'border-amber-200 bg-amber-50 text-amber-700'} rounded-lg px-2 py-1.5 cursor-pointer outline-none focus:border-blue-500 transition-all">
                        <option value="" ${!u.department_id ? 'selected' : ''}>${deptName ? '— Unassign —' : '⚠ Not assigned'}</option>
                        ${ALL_DEPARTMENTS.map(d => `<option value="${d.id}" ${u.department_id == d.id ? 'selected' : ''}>${esc(d.name)} (${esc(d.code || '')})</option>`).join('')}
                    </select>`;
            }

            // Actions cell
            const actionCellHtml = isSelf
                ? `<span class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600 border border-blue-100 inline-block">
                    <i class="fa-solid fa-lock mr-1" style="font-size:9px;"></i>Self · Protected
                   </span>`
                : `<div class="flex items-center justify-end gap-2">
                    <select onchange="updateUserRole(${u.user_id},this.value)"
                            class="text-[11px] font-bold border border-slate-200 rounded-lg px-2 py-1.5 bg-slate-50 text-slate-600 cursor-pointer outline-none focus:border-blue-500">
                        <option value="student" ${u.role==='student'?'selected':''}>Student</option>
                        <option value="teacher" ${u.role==='teacher'?'selected':''}>Teacher</option>
                        <option value="admin" ${u.role==='admin'?'selected':''}>Admin</option>
                        <option value="super_admin" ${u.role==='super_admin'?'selected':''}>Super Admin</option>
                    </select>
                    <button onclick="toggleUserStatus(${u.user_id})"
                            class="text-[11px] font-bold px-3 py-1.5 rounded-lg border cursor-pointer transition-all"
                            style="border-color:${isActive?'#fecdd3':'#a7f3d0'};background:${isActive?'#fff1f2':'#ecfdf5'};color:${isActive?'#e11d48':'#059669'};">
                        <i class="fa-solid ${isActive?'fa-ban':'fa-check'} mr-1"></i>${isActive?'Suspend':'Activate'}
                    </button>
                </div>`;

            const innerContent = `
                <td class="px-6 py-3.5">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center text-xs font-extrabold text-white" style="background:${grad};">${getInitials(u.full_name)}</div>
                        <div>
                            <p class="text-[13px] font-bold text-slate-900 leading-tight">${esc(u.full_name||'—')}</p>
                            <p class="text-[11px] text-slate-400 font-medium font-mono mt-0.5">${esc(u.email||'—')}</p>
                        </div>
                    </div>
                </td>
                <td class="px-4 py-3.5">
                    <span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-lg border"
                          style="background:${rb.bg};color:${rb.color};border-color:${rb.border};">${rb.label}</span>
                </td>
                <td class="px-4 py-3.5">
                    <div class="flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full inline-block" style="background:${isActive?'#10b981':'#f43f5e'};"></span>
                        <span class="text-[11px] font-bold" style="color:${isActive?'#059669':'#e11d48'};">${isActive?'Active':'Suspended'}</span>
                    </div>
                </td>
                <td class="px-4 py-3.5">${deptCellHtml}</td>
                <td class="px-4 py-3.5 text-right">${actionCellHtml}</td>
            `;

            if (!row) {
                row = document.createElement('tr');
                row.id = `user-row-${u.user_id}`;
                row.className = `row-hover ${useAnim ? 'fade-in' : ''}`;
                if (useAnim) row.style.animationDelay = `${i * 30}ms`;
                row.style.transition = 'background 0.15s';
                row.style.cursor = 'default';
                row.innerHTML = innerContent;
                tbody.appendChild(row);
            } else {
                if (row.innerHTML !== innerContent) {
                    row.innerHTML = innerContent;
                }
                tbody.appendChild(row);
            }
        });

        Array.from(tbody.children).forEach(tr => {
            if (tr.id && tr.id.startsWith('user-row-')) {
                const uid = parseInt(tr.id.replace('user-row-', ''), 10);
                if (!users.some(u => u.user_id === uid)) {
                    tr.remove();
                }
            }
        });

        isInitialRender = false;
    };

    window.setTab = function(role, btn) {
        activeTab = role;
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        renderTable(true);
    };

    function fetchLatestUsers() {
        fetch('{{ route("superadmin.admins.api") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (JSON.stringify(data) !== JSON.stringify(allUsers)) {
                allUsers = data;
                updateCards();
                renderTable(false);
            }
        })
        .catch(err => console.error('User poll failed:', err));
    }

    window.updateUserDepartment = function(userId, deptId) {
        const body = { department_id: deptId || null };

        fetch(`/super-admin/admins/${userId}/change-department`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                const deptName = deptId ? (ALL_DEPARTMENTS.find(d => d.id == deptId)?.name || 'Department') : 'none';
                showToast(`Department updated to ${deptName}.`, 'success');
                fetchLatestUsers();
            } else {
                showToast(data.message || 'Failed to update department.', 'error');
                fetchLatestUsers();
            }
        })
        .catch(() => { showToast('Network error.', 'error'); fetchLatestUsers(); });
    };

    window.toggleUserStatus = function(userId) {
        fetch(`/super-admin/admins/${userId}/toggle-status`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Status updated successfully.', 'success');
                fetchLatestUsers();
            } else {
                showToast(data.message || 'Failed to update status.', 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
    };

    window.updateUserRole = function(userId, role) {
        fetch(`/super-admin/admins/${userId}/change-role`, {
            method: 'PATCH',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ role: role })
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Role updated to ' + role.replace('_', ' ') + '.', 'success');
                fetchLatestUsers();
            } else {
                showToast(data.message || 'Failed to change role.', 'error');
                fetchLatestUsers();
            }
        })
        .catch(() => { showToast('Network error.', 'error'); fetchLatestUsers(); });
    };

    window.openModal = function() {
        document.getElementById('create-modal').style.display = 'flex';
        document.getElementById('new-name').value = '';
        document.getElementById('new-email').value = '';
        document.getElementById('new-password').value = '';
        selectedRole = 'admin';
        renderRolePicker();
        updateDeptField();
    };

    window.closeModal = function() {
        document.getElementById('create-modal').style.display = 'none';
    };

    function renderRolePicker() {
        const roles = [
            { key: 'student', label: 'Student', icon: 'fa-graduation-cap', color: '#059669' },
            { key: 'teacher', label: 'Teacher', icon: 'fa-chalkboard-user', color: '#d97706' },
            { key: 'admin', label: 'Admin', icon: 'fa-user-tie', color: '#2563eb' },
            { key: 'super_admin', label: 'Super Admin', icon: 'fa-user-shield', color: '#7c3aed' },
        ];
        document.getElementById('role-picker').innerHTML = roles.map(r => {
            const active = r.key === selectedRole;
            return `<button type="button" onclick="selectRole('${r.key}')"
                class="flex items-center gap-2 px-3 py-2.5 rounded-xl text-xs font-bold border transition-all"
                style="background:${active ? r.color : '#f8fafc'};color:${active ? '#fff' : '#475569'};border-color:${active ? r.color : '#e2e8f0'};${active ? 'box-shadow:0 4px 12px rgba(0,0,0,0.15);' : ''}">
                <i class="fa-solid ${r.icon}" style="font-size:11px;"></i> ${r.label}
            </button>`;
        }).join('');
    }

    window.selectRole = function(role) {
        selectedRole = role;
        renderRolePicker();
        updateDeptField();
    };

    function updateDeptField() {
        const field = document.getElementById('dept-field');
        const select = document.getElementById('new-department');
        const hint = document.getElementById('dept-hint');

        if (['admin', 'teacher', 'student'].includes(selectedRole)) {
            field.style.display = 'block';

            if (!ALL_DEPARTMENTS || ALL_DEPARTMENTS.length === 0) {
                select.innerHTML = `<option value="">⚠️ No departments created yet</option>`;
                hint.innerHTML = `<span class="text-amber-600 font-semibold"><i class="fa-solid fa-triangle-exclamation mr-1"></i> Please create a department in the Department Directory first.</span>`;
                return;
            }

            let html = `<option value="">— No department (assign later) —</option>`;
            ALL_DEPARTMENTS.forEach(d => {
                html += `<option value="${d.id}">${esc(d.name)} (${esc(d.code || '')})</option>`;
            });
            select.innerHTML = html;

            if (selectedRole === 'admin') {
                hint.innerHTML = `<i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>
                    The admin will manage users, exams, and data inside this department.`;
            } else if (selectedRole === 'teacher') {
                hint.innerHTML = `<i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>
                    The teacher will be linked to this department's courses and exams.`;
            } else {
                hint.innerHTML = `<i class="fa-solid fa-circle-info text-blue-400 mr-1"></i>
                    The student will be enrolled in this department.`;
            }
        } else {
            field.style.display = 'none';
        }
    }

    window.togglePw = function() {
        const pw = document.getElementById('new-password');
        const eye = document.getElementById('pw-eye');
        if (pw.type === 'password') { pw.type = 'text'; eye.className = 'fa-solid fa-eye-slash text-xs'; }
        else { pw.type = 'password'; eye.className = 'fa-solid fa-eye text-xs'; }
    };

    window.submitCreateAccount = function() {
        const name = document.getElementById('new-name').value.trim();
        const email = document.getElementById('new-email').value.trim();
        const password = document.getElementById('new-password').value;
        const deptId = document.getElementById('new-department').value || null;

        if (!name || !email || !password) { showToast('All fields are required.', 'error'); return; }
        if (password.length < 8) { showToast('Password must be at least 8 characters.', 'error'); return; }

        const btn = document.getElementById('submit-btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Creating...';
        btn.disabled = true;

        const payload = {
            full_name: name,
            email: email,
            password: password,
            role: selectedRole,
        };

        if (deptId) {
            payload.department_id = parseInt(deptId, 10);
        }

        fetch('{{ route("superadmin.admins.store") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(payload)
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = '<i class="fa-solid fa-user-plus mr-1.5"></i> Create Account';
            btn.disabled = false;
            if (data.status === 'success') {
                const deptInfo = deptId ? ` → assigned to ${ALL_DEPARTMENTS.find(d => d.id == deptId)?.name || 'department'}` : '';
                showToast(`Account created for ${name}${deptInfo}.`, 'success');
                closeModal();
                fetchLatestUsers();
            } else {
                showToast(data.message || 'Failed to create account.', 'error');
            }
        })
        .catch(() => {
            btn.innerHTML = '<i class="fa-solid fa-user-plus mr-1.5"></i> Create Account';
            btn.disabled = false;
            showToast('Network error.', 'error');
        });
    };

    function showToast(message, type) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const colors = { success: 'bg-emerald-600', error: 'bg-rose-600', info: 'bg-blue-600' };
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        toast.className = `toast-enter flex items-center gap-2.5 px-4 py-3 rounded-xl text-white text-xs font-semibold ${colors[type] || colors.info}`;
        toast.style.pointerEvents = 'auto';
        toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
        toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i> ${esc(message)}`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('toast-visible'));
        setTimeout(() => { toast.classList.remove('toast-visible'); setTimeout(() => toast.remove(), 300); }, 4000);
    }

    updateCards();
    renderTable();
    renderRolePicker();
})();
</script>
</body>
</html>