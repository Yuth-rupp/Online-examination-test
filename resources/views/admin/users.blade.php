<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | ExamSystem</title>
    <meta name="description" content="Manage platform user accounts, roles, and access levels in ExamSystem Admin Console.">
    <!-- Anti-flash dark mode (matches Dashboard) -->
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }

        /* ── Shared sidebar classes (matches Dashboard / partials.admin-sidebar) ── */
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-nav-active { background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
        .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }
        .dark-surface { background:#0f172a; }
        .dark-card { --card-bg:#1e293b; --card-br:#334155; --row-hover:#1e293b; }


        /* ── Sidebar ── */
        .sidebar {
            background: #ffffff;
            border-right: 1px solid #e8edf5;
            box-shadow: 2px 0 12px rgba(0,0,0,0.04);
        }

        /* ── Brand logo ── */
        .brand-icon {
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            box-shadow: 0 4px 12px rgba(37,99,235,0.3);
        }

        /* ── Nav active item ── */
        .nav-active {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            color: #1d4ed8 !important;
            border: 1px solid #bfdbfe;
            border-left: 3px solid #2563eb;
        }
        .nav-active i { color: #2563eb !important; }

        /* ── Nav hover ── */
        .nav-item {
            border: 1px solid transparent;
            border-left: 3px solid transparent;
            color: #64748b;
            transition: all 0.18s ease;
        }
        .nav-item:hover {
            background: #f8fafc;
            border-color: #e2e8f0;
            border-left-color: #94a3b8;
            color: #1e293b;
        }

        /* ── Main background ── */
        .main-bg {
            background: #f8fafc;
            background-image: radial-gradient(ellipse 60% 30% at 70% 0%, rgba(219,234,254,0.4) 0%, transparent 60%);
        }

        /* ── Cards ── */
        .metric-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.03);
            transition: all 0.2s ease;
        }
        .metric-card:hover {
            box-shadow: 0 4px 20px rgba(37,99,235,0.1);
            border-color: #bfdbfe;
            transform: translateY(-1px);
        }

        /* ── Table card ── */
        .table-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 6px 24px rgba(0,0,0,0.03);
        }

        /* ── Table row hover ── */
        .user-row {
            transition: background 0.15s ease;
        }
        .user-row:hover {
            background: #f8fafc;
        }

        /* ── Progress bars ── */
        .progress-bar { height: 3px; border-radius: 999px; background: #f1f5f9; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: 999px; }

        /* ── Role badge colors ── */
        .badge-student { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
        .badge-teacher { background: #f5f3ff; color: #6d28d9; border: 1px solid #ddd6fe; }
        .badge-admin   { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
        .badge-default { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }

        /* ── Avatar colors by role ── */
        .avatar-student { background: #eff6ff; color: #1d4ed8; border: 2px solid #bfdbfe; }
        .avatar-teacher { background: #f5f3ff; color: #6d28d9; border: 2px solid #ddd6fe; }
        .avatar-admin   { background: #fffbeb; color: #92400e; border: 2px solid #fde68a; }
        .avatar-default { background: #f1f5f9; color: #475569; border: 2px solid #e2e8f0; }

        /* ── Status pills ── */
        .status-active   { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
        .status-inactive { background: #fff1f2; color: #be123c; border: 1px solid #fecdd3; }

        /* ── Action buttons ── */
        .btn-reset   { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
        .btn-reset:hover { background:#fef3c7; border-color:#fcd34d; }
        .btn-suspend { background:#f8fafc; color:#475569; border:1px solid #e2e8f0; }
        .btn-suspend:hover { background:#f1f5f9; border-color:#cbd5e1; }
        .btn-activate { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .btn-activate:hover { background:#dcfce7; }
        .btn-delete  { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .btn-delete:hover { background:#ffe4e6; border-color:#fda4af; }

        /* ── Search input ── */
        .search-input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .search-input:focus {
            background: #ffffff;
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(147,197,253,0.3);
            outline: none;
        }

        /* ── Primary button ── */
        .btn-primary {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            box-shadow: 0 2px 8px rgba(37,99,235,0.3);
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);
            box-shadow: 0 4px 14px rgba(37,99,235,0.35);
        }

        /* ── Modal ── */
        .modal-overlay { background: rgba(15,23,42,0.35); backdrop-filter: blur(4px); }
        .modal-card {
            background: #ffffff;
            border: 1px solid #e8edf5;
            box-shadow: 0 20px 60px rgba(0,0,0,0.15);
        }

        /* ── Form inputs ── */
        .form-input {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }
        .form-input:focus {
            background: #ffffff;
            border-color: #93c5fd;
            box-shadow: 0 0 0 3px rgba(147,197,253,0.25);
            outline: none;
        }

        /* ── Pulse dot ── */
        @keyframes outerPulse {
            0%,100% { transform:scale(1); opacity:1; }
            50%      { transform:scale(1.7); opacity:0; }
        }
        .pulse-dot { animation: outerPulse 1.8s ease-in-out infinite; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* ── Table head ── */
        .table-head { background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); }

        /* ── Export button ── */
        .btn-export { background:#f8fafc; border:1px solid #e2e8f0; color:#475569; }
        .btn-export:hover { background:#f1f5f9; border-color:#cbd5e1; }
    </style>
</head>
<body class="antialiased transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="darkMode ? 'dark-surface text-slate-100' : 'bg-slate-50 text-slate-800'">

<div class="flex min-h-screen">

    @include('partials.admin-sidebar')

    <!-- ════════════════════════════
         MAIN CONTENT
    ════════════════════════════ -->
    <main class="flex-1 ml-64 main-bg min-h-screen flex flex-col">

        <!-- STICKY TOPBAR (matches the student portal's persistent topbar,
             in the admin's professional blue palette) -->
        <header class="flex items-center justify-between px-7 py-4 border-b sticky top-0 z-20 backdrop-blur-xl transition-colors duration-300"
                :class="darkMode ? 'bg-slate-900/95 border-slate-800' : 'bg-white/95 border-slate-100'"
                style="box-shadow:0 1px 4px rgba(0,0,0,0.04)">
            <div class="flex items-center gap-3">
                <!-- Status pill -->
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full text-xs font-semibold text-emerald-700 border" style="background:#f0fdf4;border-color:#bbf7d0;">
                    <span class="relative flex items-center justify-center w-2 h-2">
                        <span class="pulse-dot absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    System Status: <strong class="text-emerald-600">Healthy</strong>
                </span>
            </div>

            <!-- Admin info -->
            <div class="flex items-center gap-3">
                @include('partials.admin-darkmode-toggle')
                @include('partials.admin-notification-bell')
                <div class="text-right pl-1">
                    <h4 class="text-sm font-semibold leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ Auth::user()->full_name ?? 'Admin User' }}</h4>
                    <span class="text-xs text-slate-400">Administrator</span>
                </div>
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->full_name }}"
                         class="w-10 h-10 rounded-xl object-cover shadow" style="box-shadow:0 3px 10px rgba(37,99,235,0.3)">
                @else
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3)">
                        {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'AD' }}
                    </div>
                @endif
            </div>
        </header>

        <!-- SCROLLABLE PAGE BODY -->
        <div class="p-7">

        <!-- PAGE TITLE -->
        <div class="mb-6">
            <h2 class="text-xl font-bold flex items-center gap-2.5" :class="darkMode ? 'text-white' : 'text-slate-900'">
                <span class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-users-gear text-sm"></i>
                </span>
                User Management
            </h2>
            <p class="text-sm text-slate-400 mt-1">Manage platform accounts, access levels, and account status.</p>
        </div>

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-7">

            <!-- Total Users -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Total Users</p>
                        <h3 class="text-3xl font-black text-slate-900">{{ number_format($totalUsers) }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div class="progress-fill bg-blue-400" style="width:70%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">All registered accounts</p>
            </div>

            <!-- Active Exams -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Active Exams</p>
                        <h3 class="text-3xl font-black text-slate-900">{{ $activeExams }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="fa-solid fa-file-invoice text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div class="progress-fill bg-emerald-400" style="width:40%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">Currently running sessions</p>
            </div>

            <!-- System Load -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">System Load</p>
                        <h3 class="text-3xl font-black text-slate-900">{{ $cpuUsage }}%</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f5f3ff;border:1px solid #ddd6fe">
                        <i class="fa-solid fa-microchip text-violet-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar"><div class="progress-fill bg-violet-400" style="width:{{ $cpuUsage }}%"></div></div>
                <p class="text-[11px] text-slate-400 mt-2">CPU utilization</p>
            </div>
        </div>

        <!-- USER TABLE CARD -->
        <div class="table-card rounded-2xl overflow-hidden">

            <!-- Toolbar -->
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h3 class="font-bold text-base text-slate-900">User Directory</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Manage accounts, roles, and access overrides.</p>
                </div>

                <form method="GET" action="{{ route('admin.users') }}" class="flex items-center gap-2.5 flex-wrap">
                    <!-- Search -->
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input id="search-input" name="search" value="{{ request('search') }}" type="text"
                            placeholder="Search by name or email..."
                            class="search-input pl-10 pr-4 py-2.5 rounded-xl text-sm w-60 text-slate-700 placeholder-slate-400">
                    </div>

                    <!-- Export -->
                    <a href="{{ route('admin.users.export') }}"
                        class="btn-export flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-semibold transition-all">
                        <i class="fa-solid fa-file-export text-slate-500"></i> Export CSV
                    </a>

                    <!-- New User -->
                    <button type="button"
                        onclick="document.getElementById('add-user-modal').classList.remove('hidden')"
                        class="btn-primary flex items-center gap-2 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all">
                        <i class="fa-solid fa-user-plus"></i> New User
                    </button>
                </form>
            </div>

            <!-- Session Flash -->
            @if(session('success'))
            <div class="mx-6 mt-4 p-3.5 rounded-xl flex items-center gap-2.5 text-sm font-medium text-emerald-700" style="background:#f0fdf4;border:1px solid #bbf7d0">
                <i class="fa-solid fa-circle-check text-emerald-500"></i>
                {{ session('success') }}
            </div>
            @endif

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="table-head border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">User</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Email</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Role</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Status</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Last Active</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">

                        @forelse($users as $u)
                        @php
                            $role = strtolower($u->role ?? 'default');
                            $status = strtolower($u->status ?? 'active');
                            $avatarClass = match($role) {
                                'student' => 'avatar-student',
                                'teacher' => 'avatar-teacher',
                                'admin'   => 'avatar-admin',
                                default   => 'avatar-default'
                            };
                            $badgeClass = match($role) {
                                'student' => 'badge-student',
                                'teacher' => 'badge-teacher',
                                'admin'   => 'badge-admin',
                                default   => 'badge-default'
                            };
                            $roleIcons = [
                                'student' => 'fa-user-graduate',
                                'teacher' => 'fa-chalkboard-user',
                                'admin'   => 'fa-shield-halved',
                            ];
                            $roleIcon = $roleIcons[$role] ?? 'fa-user';
                        @endphp
                        <tr class="user-row">
                            <!-- User -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="{{ $avatarClass }} w-9 h-9 rounded-xl flex items-center justify-center font-bold text-[11px] uppercase shrink-0">
                                        {{ strtoupper(substr($u->full_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="font-semibold text-sm text-slate-900 leading-tight">{{ $u->full_name }}</p>
                                        <p class="text-[11px] text-slate-400 font-mono mt-0.5">ID #{{ $u->user_id }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Email -->
                            <td class="px-6 py-4">
                                <span class="text-sm text-slate-500">{{ $u->email }}</span>
                            </td>

                            <!-- Role badge -->
                            <td class="px-6 py-4">
                                <span class="{{ $badgeClass }} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold uppercase tracking-wide">
                                    <i class="fa-solid {{ $roleIcon }}" style="font-size:9px"></i>
                                    {{ ucfirst($u->role) }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="px-6 py-4">
                                @if($status === 'active')
                                <span class="status-active inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                    Active
                                </span>
                                @else
                                <span class="status-inactive inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-semibold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500 inline-block"></span>
                                    Suspended
                                </span>
                                @endif
                            </td>

                            <!-- Last active -->
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-sm text-slate-400">
                                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                                    {{ $u->last_login_at ? \Carbon\Carbon::parse($u->last_login_at)->diffForHumans() : 'Never logged in' }}
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <!-- Reset password -->
                                    <button
                                        onclick="openPasswordModal('{{ $u->user_id }}', '{{ $u->full_name }}')"
                                        class="btn-reset flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1.5 rounded-lg transition-all"
                                        title="Reset Password">
                                        <i class="fa-solid fa-key text-[9px]"></i> Reset
                                    </button>

                                    <!-- Toggle status -->
                                    <form action="{{ route('admin.users.toggleStatus', $u->user_id) }}" method="POST" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit"
                                            class="{{ $status === 'active' ? 'btn-suspend' : 'btn-activate' }} text-[11px] font-semibold px-2.5 py-1.5 rounded-lg transition-all"
                                            title="{{ $status === 'active' ? 'Suspend Account' : 'Activate Account' }}">
                                            {{ $status === 'active' ? 'Suspend' : 'Activate' }}
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form action="{{ route('admin.users.destroy', $u->user_id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete this user?');" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="btn-delete text-[11px] font-semibold px-2.5 py-1.5 rounded-lg transition-all"
                                            title="Delete User">
                                            <i class="fa-solid fa-trash-can text-[9px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="6" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center" style="background:#f1f5f9;border:1px solid #e2e8f0">
                                        <i class="fa-solid fa-users text-slate-300 text-xl"></i>
                                    </div>
                                    <p class="text-sm font-semibold text-slate-500">No users found</p>
                                    <p class="text-xs text-slate-400">Try a different search or add a new user.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-400">Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users</p>
                {{ $users->links() }}
            </div>
        </div>

        </div><!-- /page body -->
    </main>
</div>

<!-- ════════════════════════════
     PASSWORD RESET MODAL
════════════════════════════ -->
<div id="password-reset-modal" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
    <div class="modal-card rounded-2xl w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between" style="background:#f8fafc">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-amber-600" style="background:#fffbeb;border:1px solid #fde68a">
                    <i class="fa-solid fa-key text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Reset Password</h3>
                    <p class="text-[11px] text-slate-400" id="reset-modal-subtitle">For selected user</p>
                </div>
            </div>
            <button type="button" onclick="closePasswordModal()"
                class="w-7 h-7 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6">
            <form id="password-reset-form" method="POST" class="space-y-4">
                @csrf @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">New Password</label>
                    <div class="relative">
                        <input id="new-password" name="password" required type="password"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400"
                            placeholder="Enter new password">
                        <button type="button" onclick="togglePasswordVis('new-password', 'eye1')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="eye1" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Confirm Password</label>
                    <div class="relative">
                        <input id="confirm-password" name="password_confirmation" required type="password"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400"
                            placeholder="Confirm new password">
                        <button type="button" onclick="togglePasswordVis('confirm-password', 'eye2')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="eye2" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closePasswordModal()"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 btn-primary text-white py-2.5 rounded-xl text-sm font-bold transition-all">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════
     ADD USER MODAL
════════════════════════════ -->
<div id="add-user-modal" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
    <div class="modal-card rounded-2xl w-full max-w-md overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between" style="background:#f8fafc">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-user-plus text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Add New User</h3>
                    <p class="text-[11px] text-slate-400">Register a new platform account</p>
                </div>
            </div>
            <button type="button" onclick="document.getElementById('add-user-modal').classList.add('hidden')"
                class="w-7 h-7 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 max-h-[75vh] overflow-y-auto">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                        <i class="fa-solid fa-id-card mr-1 text-slate-400"></i> Full Name
                    </label>
                    <input name="full_name" required type="text"
                        class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400"
                        placeholder="e.g. John Smith">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                        <i class="fa-solid fa-envelope mr-1 text-slate-400"></i> Email Address
                    </label>
                    <input name="email" required type="email"
                        class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400"
                        placeholder="name@domain.com">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                        <i class="fa-solid fa-shield-halved mr-1 text-slate-400"></i> Role
                    </label>
                    <select name="role" class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 bg-white cursor-pointer">
                        <option value="student">🎓 Student</option>
                        <option value="teacher">📋 Teacher</option>
                        <option value="admin">🛡️ Admin</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                        <i class="fa-solid fa-lock mr-1 text-slate-400"></i> Password
                    </label>
                    <div class="relative">
                        <input id="add-password" name="password" required type="password"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVis('add-password', 'eye3')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="eye3" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                        <i class="fa-solid fa-lock mr-1 text-slate-400"></i> Confirm Password
                    </label>
                    <div class="relative">
                        <input id="add-confirm-password" name="password_confirmation" required type="password"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400"
                            placeholder="••••••••">
                        <button type="button" onclick="togglePasswordVis('add-confirm-password', 'eye4')"
                            class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="eye4" class="fa-regular fa-eye text-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button"
                        onclick="document.getElementById('add-user-modal').classList.add('hidden')"
                        class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit"
                        class="flex-1 btn-primary text-white py-2.5 rounded-xl text-sm font-bold transition-all">
                        <i class="fa-solid fa-user-plus mr-1.5"></i> Create Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════════════════════
     SCRIPTS
════════════════════════════ -->
<script>
    /* Password modal open */
    function openPasswordModal(id, name) {
        document.getElementById('password-reset-form').action = `/admin/users/${id}/force-password`;
        document.getElementById('reset-modal-subtitle').textContent = 'Resetting password for: ' + name;
        document.getElementById('password-reset-modal').classList.remove('hidden');
    }

    /* Password modal close */
    function closePasswordModal() {
        document.getElementById('password-reset-modal').classList.add('hidden');
    }

    /* Password show/hide toggle */
    function togglePasswordVis(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'fa-regular fa-eye-slash text-sm';
        } else {
            input.type = 'password';
            icon.className = 'fa-regular fa-eye text-sm';
        }
    }

    /* Close modals on backdrop click */
    document.getElementById('password-reset-modal').addEventListener('click', function(e) {
        if (e.target === this) closePasswordModal();
    });
    document.getElementById('add-user-modal').addEventListener('click', function(e) {
        if (e.target === this) this.classList.add('hidden');
    });

    /* Live search debounce */
    let searchTimer;
    document.getElementById('search-input').addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => this.closest('form').submit(), 500);
    });

    /* Render the Lucide icon set (sidebar "graduation-cap" logo, search icon, etc.)
       This page was missing this call, which is why the sidebar logo/icons
       never appeared — the <i data-lucide="..."> tags were left un-rendered. */
    if (window.lucide) lucide.createIcons();
</script>
</body>
</html>