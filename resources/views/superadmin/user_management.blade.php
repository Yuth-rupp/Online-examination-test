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

        @keyframes shimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skeleton{background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);
                  background-size:1000px 100%;animation:shimmer 1.5s infinite linear;border-radius:8px;display:inline-block;}

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

            {{-- ACTIVE --}}
            <a href="{{ route('superadmin.admins.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-users text-xs text-white"></i>
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
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">User Management</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    All system roles — super admins, admins, teachers, students &nbsp;
                    <span id="total-record-count" class="font-bold text-slate-600">...</span>
                </p>
            </div>

            <div class="flex items-center gap-3 ml-auto">
                {{-- Clock --}}
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>

                {{-- Poll badge --}}
                <div class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    Polling: <span id="poll-countdown" class="font-mono font-black ml-1 mr-0.5 text-emerald-900 w-3 text-center">3</span>s
                </div>

                {{-- Add button --}}
                <button onclick="openModal()"
                        class="flex items-center gap-2 text-xs font-bold text-white px-4 py-2.5 rounded-xl transition-all"
                        style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);"
                        onmouseenter="this.style.background='#1d4ed8'" onmouseleave="this.style.background='#2563eb'">
                    <i class="fa-solid fa-user-plus text-xs"></i> Add Account
                </button>
            </div>
        </header>

        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:20px;">

            {{-- ========== METRIC CARDS ========== --}}
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Total --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                        <i class="fa-solid fa-users text-blue-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Users</p>
                        <p id="card-total" class="text-2xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:28px;width:40px;"></span>
                        </p>
                    </div>
                </div>

                {{-- Super Admins --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
                        <i class="fa-solid fa-user-shield text-violet-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Super Admins</p>
                        <p id="card-superadmin" class="text-2xl font-black text-violet-600 leading-none tabular-nums">
                            <span class="skeleton" style="height:28px;width:32px;"></span>
                        </p>
                    </div>
                </div>

                {{-- Teachers --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                        <i class="fa-solid fa-chalkboard-user text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Teachers</p>
                        <p id="card-teacher" class="text-2xl font-black text-amber-600 leading-none tabular-nums">
                            <span class="skeleton" style="height:28px;width:32px;"></span>
                        </p>
                    </div>
                </div>

                {{-- Students --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                        <i class="fa-solid fa-graduation-cap text-emerald-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Students</p>
                        <p id="card-student" class="text-2xl font-black text-emerald-600 leading-none tabular-nums">
                            <span class="skeleton" style="height:28px;width:32px;"></span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ========== TABLE CARD ========== --}}
            <div class="bg-white rounded-2xl border border-slate-100 flex flex-col overflow-hidden"
                 style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">

                {{-- Table Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-wrap gap-3">
                    {{-- Tab filters --}}
                    <div class="flex items-center gap-1 p-1 rounded-xl" style="background:#f1f5f9;">
                        <button class="tab-btn active" data-role="all"         onclick="setTab('all',this)">All</button>
                        <button class="tab-btn"        data-role="super_admin" onclick="setTab('super_admin',this)">Super Admins</button>
                        <button class="tab-btn"        data-role="admin"       onclick="setTab('admin',this)">Admins</button>
                        <button class="tab-btn"        data-role="teacher"     onclick="setTab('teacher',this)">Teachers</button>
                        <button class="tab-btn"        data-role="student"     onclick="setTab('student',this)">Students</button>
                    </div>

                    {{-- Search --}}
                    <div class="flex items-center gap-2 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2"
                         style="min-width:220px;">
                        <i class="fa-solid fa-magnifying-glass text-slate-300 text-xs"></i>
                        <input id="search-input" type="text" placeholder="Search name or email..."
                               oninput="renderTable()"
                               class="text-xs font-medium text-slate-700 bg-transparent outline-none flex-1 placeholder-slate-300"
                               style="min-width:0;">
                    </div>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid #f1f5f9;background:#fafafa;">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Account</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Role</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Status</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Last Active</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="user-table-body" class="divide-y divide-slate-50">
                            <tr><td colspan="5" style="padding:40px;text-align:center;">
                                <div style="display:flex;flex-direction:column;gap:10px;align-items:center;">
                                    <div class="skeleton rounded" style="height:14px;width:50%;"></div>
                                    <div class="skeleton rounded" style="height:14px;width:35%;"></div>
                                    <div class="skeleton rounded" style="height:14px;width:45%;"></div>
                                </div>
                            </td></tr>
                        </tbody>
                    </table>
                </div>

                {{-- Table footer --}}
                <div class="flex items-center justify-between px-6 py-3.5 border-t border-slate-100 bg-slate-50">
                    <p id="table-footer-count" class="text-[11px] text-slate-400 font-medium"></p>
                    <p class="text-[11px] text-slate-400 font-medium">Auto-refreshes every 3 seconds</p>
                </div>
            </div>
        </div>
    </main>
</div>


{{-- ===================== CREATE MODAL ===================== --}}
<div id="create-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.50);backdrop-filter:blur(8px);">
    <div class="modal-in bg-white rounded-2xl max-w-md w-full border border-slate-100"
         style="box-shadow:0 24px 64px rgba(15,23,42,0.22);"
         onclick="event.stopPropagation()">

        {{-- Modal Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                    <i class="fa-solid fa-user-plus text-blue-600 text-sm"></i>
                </div>
                <h3 class="font-bold text-sm text-slate-900">Add New Account</h3>
            </div>
            <button onclick="closeModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        {{-- Modal Body --}}
        <div class="p-6 space-y-4">
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Full Name</label>
                <input id="new-name" type="text" placeholder="e.g. John Smith" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all"
                       onfocus="this.style.borderColor='#2563eb';this.style.background='#fff'"
                       onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Email Address</label>
                <input id="new-email" type="email" placeholder="john@school.edu" required
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all"
                       onfocus="this.style.borderColor='#2563eb';this.style.background='#fff'"
                       onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Password</label>
                <div style="position:relative;">
                    <input id="new-password" type="password" placeholder="Min 8 characters" required
                           class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm font-medium text-slate-800 outline-none transition-all"
                           onfocus="this.style.borderColor='#2563eb';this.style.background='#fff'"
                           onblur="this.style.borderColor='#e2e8f0';this.style.background='#f8fafc'">
                    <button type="button" onclick="togglePw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);color:#94a3b8;background:none;border:none;cursor:pointer;">
                        <i id="pw-eye" class="fa-solid fa-eye text-xs"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Role</label>
                <div class="grid grid-cols-2 gap-2" id="role-picker">
                    {{-- Role radio buttons rendered by JS --}}
                </div>
            </div>
        </div>

        {{-- Modal Footer --}}
        <div class="flex items-center gap-3 px-6 pb-6">
            <button onclick="closeModal()"
                    class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                Cancel
            </button>
            <button onclick="submitCreateAccount()"
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);"
                    onmouseenter="this.style.background='#1d4ed8'" onmouseleave="this.style.background='#2563eb'">
                <i class="fa-solid fa-user-plus mr-1.5"></i> Create Account
            </button>
        </div>
    </div>
</div>


{{-- ===================== SCRIPTS ===================== --}}
<script>
// ============================================================
//  CONFIG
// ============================================================
const CURRENT_AUTH_ID = {{ auth()->id() ?? 0 }};
const CSRF = document.querySelector('meta[name=csrf-token]').content;

// Avatar gradient per role
const ROLE_GRADS = {
    super_admin: 'linear-gradient(135deg,#7c3aed,#6d28d9)',
    admin:       'linear-gradient(135deg,#2563eb,#1d4ed8)',
    teacher:     'linear-gradient(135deg,#f59e0b,#d97706)',
    student:     'linear-gradient(135deg,#10b981,#059669)',
};

const ROLE_BADGE = {
    super_admin: {bg:'#f5f3ff',color:'#6d28d9',border:'#ede9fe',label:'Super Admin'},
    admin:       {bg:'#eff6ff',color:'#1d4ed8',border:'#bfdbfe',label:'Admin'},
    teacher:     {bg:'#fffbeb',color:'#b45309',border:'#fde68a',label:'Teacher'},
    student:     {bg:'#f0fdf4',color:'#059669',border:'#a7f3d0',label:'Student'},
};


// ============================================================
//  STATE
// ============================================================
let allUsers = {!! json_encode($admins ?? []) !!};
let activeTab = 'all';
let selectedRole = 'admin';


// ============================================================
//  CLOCK
// ============================================================
function updateClock(){
    document.getElementById('live-clock').textContent =
        new Date().toLocaleTimeString('en-US',{hour12:false,hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
updateClock(); setInterval(updateClock,1000);


// ============================================================
//  POLL COUNTDOWN (3s)
// ============================================================
let pollCount=3;
const pollEl=document.getElementById('poll-countdown');
setInterval(()=>{
    pollCount--;
    if(pollCount<=0){ pollCount=3; fetchLatestUsers(); }
    pollEl.textContent=pollCount;
},1000);


// ============================================================
//  METRIC CARDS
// ============================================================
function setMetric(id,value){
    const el=document.getElementById(id);
    el.innerHTML=''; el.textContent=value;
    el.classList.remove('count-animate'); void el.offsetWidth; el.classList.add('count-animate');
}

function updateCards(){
    const sa=allUsers.filter(u=>u.role==='super_admin').length;
    const ad=allUsers.filter(u=>u.role==='admin').length;
    const te=allUsers.filter(u=>u.role==='teacher').length;
    const st=allUsers.filter(u=>u.role==='student').length;
    setMetric('card-total',     allUsers.length);
    setMetric('card-superadmin', sa);
    setMetric('card-teacher',    te);
    setMetric('card-student',    st);

    const total=allUsers.length;
    document.getElementById('total-record-count').textContent=`(${total} records)`;
}


// ============================================================
//  RENDER TABLE
// ============================================================
function getInitials(name){
    if(!name)return'??';
    return name.split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();
}

function filteredUsers(){
    const search=(document.getElementById('search-input').value||'').toLowerCase();
    return allUsers.filter(u=>{
        const matchRole = activeTab==='all' || u.role===activeTab;
        const matchSearch = !search || (u.full_name||'').toLowerCase().includes(search)
                            || (u.email||'').toLowerCase().includes(search);
        return matchRole && matchSearch;
    });
}

function renderTable(){
    const tbody=document.getElementById('user-table-body');
    const users=filteredUsers();

    document.getElementById('table-footer-count').textContent=
        `Showing ${users.length} of ${allUsers.length} accounts`;

    if(!users.length){
        tbody.innerHTML=`<tr><td colspan="5" style="padding:48px;text-align:center;">
            <i class="fa-solid fa-users-slash" style="font-size:28px;color:#e2e8f0;display:block;margin-bottom:10px;"></i>
            <p style="font-size:12px;font-weight:600;color:#94a3b8;">No users match this filter</p>
            </td></tr>`;
        return;
    }

    tbody.innerHTML=users.map((u,i)=>{
        const rb=ROLE_BADGE[u.role]||{bg:'#f1f5f9',color:'#64748b',border:'#e2e8f0',label:u.role};
        const grad=ROLE_GRADS[u.role]||ROLE_GRADS.student;
        const isSelf=u.user_id===CURRENT_AUTH_ID;
        const isActive=u.status==='active';
        const lastSeen=u.last_seen||'—';
        const delay=i*45;

        const actionCell=isSelf
            ?`<td style="padding:14px 16px;text-align:right;">
                <span style="font-size:10px;font-weight:700;padding:4px 10px;border-radius:8px;
                             background:#eff6ff;color:#2563eb;border:1px solid #bfdbfe;">
                    <i class="fa-solid fa-lock" style="margin-right:4px;font-size:9px;"></i>Self · Protected
                </span>
              </td>`
            :`<td style="padding:14px 16px;text-align:right;">
                <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px;">
                    <select onchange="updateUserRole(${u.user_id},this.value)"
                            style="font-size:11px;font-weight:700;border:1px solid #e2e8f0;border-radius:8px;
                                   padding:5px 8px;background:#f8fafc;color:#475569;cursor:pointer;outline:none;">
                        <option value="student"     ${u.role==='student'    ?'selected':''}>Student</option>
                        <option value="teacher"     ${u.role==='teacher'    ?'selected':''}>Teacher</option>
                        <option value="admin"       ${u.role==='admin'      ?'selected':''}>Admin</option>
                        <option value="super_admin" ${u.role==='super_admin'?'selected':''}>Super Admin</option>
                    </select>
                    <button onclick="toggleUserStatus(${u.user_id})"
                            style="font-size:11px;font-weight:700;padding:5px 12px;border-radius:8px;
                                   border:1px solid ${isActive?'#fecdd3':'#a7f3d0'};
                                   background:${isActive?'#fff1f2':'#ecfdf5'};
                                   color:${isActive?'#e11d48':'#059669'};cursor:pointer;"
                            onmouseenter="this.style.opacity='0.8'" onmouseleave="this.style.opacity='1'">
                        ${isActive?'<i class="fa-solid fa-ban" style="margin-right:5px;"></i>Suspend'
                                  :'<i class="fa-solid fa-check" style="margin-right:5px;"></i>Activate'}
                    </button>
                </div>
              </td>`;

        return `
        <tr class="row-hover fade-in" style="animation-delay:${delay}ms;transition:background 0.15s;cursor:default;">
            <td style="padding:14px 24px;">
                <div style="display:flex;align-items:center;gap:12px;">
                    <div style="width:38px;height:38px;border-radius:11px;flex-shrink:0;
                                background:${grad};display:flex;align-items:center;justify-content:center;
                                font-size:13px;font-weight:800;color:#fff;letter-spacing:0.01em;">
                        ${getInitials(u.full_name)}
                    </div>
                    <div>
                        <p style="font-size:13px;font-weight:700;color:#0f172a;line-height:1.2;">${u.full_name||'—'}</p>
                        <p style="font-size:11px;color:#94a3b8;font-weight:500;margin-top:2px;font-family:'JetBrains Mono',monospace;">${u.email||'—'}</p>
                    </div>
                </div>
            </td>
            <td style="padding:14px 16px;">
                <span style="font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:0.05em;
                             padding:4px 10px;border-radius:8px;border:1px solid;
                             background:${rb.bg};color:${rb.color};border-color:${rb.border};">
                    ${rb.label}
                </span>
            </td>
            <td style="padding:14px 16px;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="width:6px;height:6px;border-radius:50%;flex-shrink:0;
                                 background:${isActive?'#10b981':'#f43f5e'};
                                 box-shadow:0 0 0 2px ${isActive?'rgba(16,185,129,0.2)':'rgba(244,63,94,0.2)'};">
                    </span>
                    <span style="font-size:11px;font-weight:700;color:${isActive?'#059669':'#e11d48'};">
                        ${isActive?'Active':'Suspended'}
                    </span>
                </div>
            </td>
            <td style="padding:14px 16px;font-family:'JetBrains Mono',monospace;font-size:11px;color:#94a3b8;font-weight:500;">
                ${lastSeen}
            </td>
            ${actionCell}
        </tr>`;
    }).join('');
}


// ============================================================
//  TAB SWITCH
// ============================================================
function setTab(role, btn){
    activeTab=role;
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    renderTable();
}


// ============================================================
//  API: FETCH USERS
// ============================================================
async function fetchLatestUsers(){
    try{
        const res=await fetch("{{ route('superadmin.admins.api') }}",{headers:{'Accept':'application/json'}});
        if(res.ok){
            allUsers=await res.json();
            updateCards();
            renderTable();
        }
    }catch(e){}
}


// ============================================================
//  API: TOGGLE STATUS
// ============================================================
async function toggleUserStatus(id){
    try{
        const res=await fetch(`/super-admin/admins/${id}/toggle-status`,{
            method:'PATCH',
            headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
        });
        if(res.ok){
            fetchLatestUsers();
            showToast(`<i class="fa-solid fa-circle-half-stroke" style="color:#60a5fa;font-size:13px;"></i><span style="margin-left:8px;">User status updated successfully.</span>`);
        }
    }catch(e){}
}


// ============================================================
//  API: UPDATE ROLE
// ============================================================
async function updateUserRole(id, role){
    try{
        const res=await fetch(`/super-admin/admins/${id}/change-role`,{
            method:'PATCH',
            headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
            body:JSON.stringify({role})
        });
        if(res.ok){
            fetchLatestUsers();
            const rb=ROLE_BADGE[role]||{label:role};
            showToast(`<i class="fa-solid fa-shield-halved" style="color:#60a5fa;font-size:13px;"></i><span style="margin-left:8px;">Role changed to <strong>${rb.label}</strong>.</span>`);
        }
    }catch(e){}
}


// ============================================================
//  MODAL
// ============================================================
const ROLE_PICKER_OPTS=[
    {value:'student',     label:'Student',     icon:'fa-graduation-cap',  desc:'Exam candidate',     grad:ROLE_GRADS.student    },
    {value:'teacher',     label:'Teacher',     icon:'fa-chalkboard-user', desc:'Instructor / proctor',grad:ROLE_GRADS.teacher    },
    {value:'admin',       label:'Admin',       icon:'fa-shield',          desc:'Department admin',    grad:ROLE_GRADS.admin      },
    {value:'super_admin', label:'Super Admin', icon:'fa-user-shield',     desc:'Root access',         grad:ROLE_GRADS.super_admin},
];

function renderRolePicker(){
    document.getElementById('role-picker').innerHTML=ROLE_PICKER_OPTS.map(o=>`
        <div onclick="selectRole('${o.value}',this)"
             id="rp-${o.value}"
             style="display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;cursor:pointer;
                    border:2px solid ${selectedRole===o.value?'#2563eb':'#e2e8f0'};
                    background:${selectedRole===o.value?'#eff6ff':'#f8fafc'};
                    transition:all 0.2s;">
            <div style="width:32px;height:32px;border-radius:8px;flex-shrink:0;
                        background:${o.grad};display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid ${o.icon}" style="color:#fff;font-size:12px;"></i>
            </div>
            <div>
                <p style="font-size:11px;font-weight:700;color:#0f172a;">${o.label}</p>
                <p style="font-size:10px;color:#94a3b8;font-weight:500;">${o.desc}</p>
            </div>
        </div>`).join('');
}

function selectRole(role, el){
    selectedRole=role;
    document.querySelectorAll('#role-picker > div').forEach(d=>{
        d.style.borderColor='#e2e8f0'; d.style.background='#f8fafc';
    });
    el.style.borderColor='#2563eb'; el.style.background='#eff6ff';
}

function openModal(){
    document.getElementById('create-modal').style.display='flex';
    renderRolePicker();
}
function closeModal(){
    document.getElementById('create-modal').style.display='none';
    ['new-name','new-email','new-password'].forEach(id=>document.getElementById(id).value='');
    selectedRole='admin';
}

function togglePw(){
    const inp=document.getElementById('new-password');
    const eye=document.getElementById('pw-eye');
    if(inp.type==='password'){inp.type='text';eye.className='fa-solid fa-eye-slash text-xs';}
    else{inp.type='password';eye.className='fa-solid fa-eye text-xs';}
}

async function submitCreateAccount(){
    const name  =document.getElementById('new-name').value.trim();
    const email =document.getElementById('new-email').value.trim();
    const pass  =document.getElementById('new-password').value;
    if(!name||!email||!pass){
        showToast('<i class="fa-solid fa-triangle-exclamation" style="color:#fbbf24;font-size:13px;"></i><span style="margin-left:8px;">Please fill in all fields.</span>');
        return;
    }
    try{
        const res=await fetch("{{ route('superadmin.admins.store') }}",{
            method:'POST',
            headers:{'X-CSRF-TOKEN':CSRF,'Content-Type':'application/json','Accept':'application/json'},
            body:JSON.stringify({full_name:name,email,password:pass,role:selectedRole})
        });
        if(res.ok){
            closeModal();
            fetchLatestUsers();
            showToast(`<i class="fa-solid fa-user-check" style="color:#34d399;font-size:13px;"></i><span style="margin-left:8px;"><strong>${name}</strong> created as <strong>${ROLE_BADGE[selectedRole]?.label||selectedRole}</strong>.</span>`);
        }else{
            showToast('<i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i><span style="margin-left:8px;">Failed to create account. Check inputs.</span>');
        }
    }catch(e){
        showToast('<i class="fa-solid fa-circle-xmark" style="color:#f87171;font-size:13px;"></i><span style="margin-left:8px;">Network error. Try again.</span>');
    }
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
//  INIT
// ============================================================
updateCards();
renderTable();
</script>
</body>
</html>