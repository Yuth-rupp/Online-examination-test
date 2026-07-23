<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments | ExamSystem</title>
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .card { background:#fff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(148,163,184,0.06); transition:all 0.25s ease; }
        .card:hover { box-shadow:0 8px 24px rgba(148,163,184,0.16); transform:translateY(-2px); }
        .modal-overlay { background: rgba(15,23,42,0.50); backdrop-filter: blur(8px); }
        .form-input { background:#f8fafc; border:1px solid #e2e8f0; }
        .form-input:focus { outline:none; border-color:#2563eb; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
        @keyframes modalIn{from{opacity:0;transform:translateY(16px) scale(0.97)}to{opacity:1;transform:none}}
        .modal-in{animation:modalIn 0.28s cubic-bezier(0.22,1,0.36,1) forwards;}
        @keyframes countUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .count-animate{animation:countUp 0.4s ease-out forwards;}
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800">

<div class="flex min-h-screen">

    {{-- ===================== SUPER ADMIN SIDEBAR ===================== --}}
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

            {{-- DEPARTMENT DIRECTORY (active on this page) --}}
            <a href="{{ route('superadmin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-building-columns text-xs text-white"></i></span><span>Department Directory</span>
            </a>

            <a href="{{ route('superadmin.institutions.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-landmark text-xs text-slate-400"></i></span><span>Institutions</span>
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
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Department Directory</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Create departments and put an admin in charge of each one</p>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>
                <button type="button" onclick="document.getElementById('add-dept-modal').style.display='flex'"
                        class="flex items-center gap-2 text-xs font-bold text-white px-4 py-2.5 rounded-xl transition-all"
                        style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);"
                        onmouseenter="this.style.background='#1d4ed8'" onmouseleave="this.style.background='#2563eb'">
                    <i class="fa-solid fa-plus text-xs"></i> New Department
                </button>
            </div>
        </header>

        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:20px;">

            {{-- Flash messages --}}
            @if(session('success'))
            <div class="flex items-center gap-2.5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3">
                <i class="fa-solid fa-check-circle text-emerald-500"></i>
                <p class="text-xs font-semibold text-emerald-700">{{ session('success') }}</p>
            </div>
            @endif

            @if($errors->any())
            <div class="bg-rose-50 border border-rose-200 rounded-xl px-4 py-3">
                <ul class="list-disc pl-4 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li class="text-xs font-semibold text-rose-700">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- ========== METRIC CARDS ========== --}}
            @php
                $totalDepartments = $departments->count();
                $activeDepartments = $departments->where('is_active', true)->count();
                $managedDepartments = $departments->filter(fn($d) => $d->admins->count() > 0)->count();
                $unassignedCount = $unassignedAdmins->count();
            @endphp
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#eff6ff,#dbeafe);">
                        <i class="fa-solid fa-building-columns text-blue-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total Departments</p>
                        <p class="text-2xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $totalDepartments }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#f0fdf4,#dcfce7);">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Active Departments</p>
                        <p class="text-2xl font-black text-emerald-600 leading-none tabular-nums count-animate">{{ $activeDepartments }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#f5f3ff,#ede9fe);">
                        <i class="fa-solid fa-user-shield text-violet-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">With Admin Assigned</p>
                        <p class="text-2xl font-black text-violet-600 leading-none tabular-nums count-animate">{{ $managedDepartments }}</p>
                    </div>
                </div>
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default transition-all duration-300"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);"
                     onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.16)';this.style.transform='translateY(-2px)'"
                     onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)';this.style.transform='none'">
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center flex-shrink-0" style="background:linear-gradient(135deg,#fffbeb,#fef3c7);">
                        <i class="fa-solid fa-user-clock text-amber-500 text-sm"></i>
                    </div>
                    <div>
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Unassigned Admins</p>
                        <p class="text-2xl font-black text-amber-600 leading-none tabular-nums count-animate">{{ $unassignedCount }}</p>
                    </div>
                </div>
            </div>

            {{-- ========== SCOPE NOTICE ========== --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4"
                 style="box-shadow:0 1px 4px rgba(37,99,235,0.06);">
                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-circle-info text-blue-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-blue-900 mb-0.5">How Departments Work</p>
                    <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
                        Each department is its own space inside the university. Give a department to one admin
                        and they can only see and manage that department's teachers, students, and exams —
                        everything else stays hidden from them. Only you, as super admin, see every department at once.
                    </p>
                </div>
            </div>

            {{-- ========== DEPARTMENT CARDS ========== --}}
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($departments as $dept)
                <div class="card rounded-2xl p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-slate-900 text-sm">{{ $dept->name }}</h3>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ $dept->code }}</span>
                        </div>
                        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full {{ $dept->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            {{ $dept->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="rounded-xl p-3" style="background:#eff6ff">
                            <p class="text-lg font-black text-blue-700 tabular-nums">{{ $dept->students_count }}</p>
                            <p class="text-[10px] text-blue-500 font-bold uppercase tracking-wide">Students</p>
                        </div>
                        <div class="rounded-xl p-3" style="background:#f0fdf4">
                            <p class="text-lg font-black text-emerald-700 tabular-nums">{{ $dept->teachers_count }}</p>
                            <p class="text-[10px] text-emerald-500 font-bold uppercase tracking-wide">Teachers</p>
                        </div>
                    </div>

                    <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400 mb-2">Department Admin(s)</p>
                    @forelse($dept->admins as $admin)
                        <div class="flex items-center justify-between mb-1.5 text-xs bg-slate-50 rounded-lg px-2.5 py-1.5">
                            <span class="text-slate-700 font-medium">{{ $admin->full_name }} <span class="text-slate-400 font-normal">({{ $admin->email }})</span></span>
                            <form action="{{ route('superadmin.departments.removeAdmin', [$dept->id, $admin->user_id]) }}" method="POST" onsubmit="return confirm('Remove this admin from the department?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-400 hover:text-rose-600 transition-colors"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic mb-2">No admin assigned yet.</p>
                    @endforelse

                    <div class="flex items-center gap-2 mt-3">
                        <form action="{{ route('superadmin.departments.assignAdmin', $dept->id) }}" method="POST" class="flex-1 flex items-center gap-2">
                            @csrf
                            <select name="user_id" required class="form-input flex-1 px-3 py-2 rounded-lg text-xs font-medium text-slate-700">
                                <option value="">Assign an admin...</option>
                                @foreach($unassignedAdmins as $u)
                                    <option value="{{ $u->user_id }}">{{ $u->full_name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-bold text-white transition-all flex-shrink-0"
                                    style="background:#2563eb;" onmouseenter="this.style.background='#1d4ed8'" onmouseleave="this.style.background='#2563eb'">Assign</button>
                        </form>
                    </div>

                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-50">
                        <a href="{{ route('admin.departments.teachers', $dept->id) }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800 transition-colors">
                            <i class="fa-solid fa-chalkboard-user"></i> Teaching roster
                        </a>
                        <button type="button" onclick="document.getElementById('edit-dept-{{ $dept->id }}').style.display='flex'"
                            class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700 transition-colors">
                            <i class="fa-solid fa-pen"></i> Edit
                        </button>
                    </div>
                </div>

                <!-- Edit modal for this department -->
                <div id="edit-dept-{{ $dept->id }}" class="modal-overlay fixed inset-0 z-50 items-center justify-center p-4" style="display:none;">
                    <div class="modal-in bg-white rounded-2xl w-full max-w-md border border-slate-100 overflow-hidden" style="box-shadow:0 24px 64px rgba(15,23,42,0.22);" onclick="event.stopPropagation()">
                        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-pen text-blue-600 text-sm"></i></div>
                                <h3 class="font-bold text-sm text-slate-900">Edit {{ $dept->name }}</h3>
                            </div>
                            <button type="button" onclick="document.getElementById('edit-dept-{{ $dept->id }}').style.display='none'" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all"><i class="fa-solid fa-xmark text-sm"></i></button>
                        </div>
                        <form action="{{ route('superadmin.departments.update', $dept->id) }}" method="POST" class="p-6 space-y-4">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Name</label>
                                <input name="name" value="{{ $dept->name }}" required class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Code</label>
                                <input name="code" value="{{ $dept->code }}" required class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Description</label>
                                <textarea name="description" rows="2" class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all">{{ $dept->description }}</textarea>
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                                <input type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }}> Active
                            </label>
                            <div class="flex gap-3 pt-1">
                                <button type="button" onclick="document.getElementById('edit-dept-{{ $dept->id }}').style.display='none'" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Cancel</button>
                                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all" style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-full card rounded-2xl p-10 text-center text-slate-400">
                    <i class="fa-solid fa-building-columns text-3xl mb-3"></i>
                    <p class="text-sm font-medium">No departments yet. Create your first one to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
    </main>
</div>

{{-- ===================== CREATE MODAL ===================== --}}
<div id="add-dept-modal" class="modal-overlay fixed inset-0 z-50 items-center justify-center p-4" style="display:none;">
    <div class="modal-in bg-white rounded-2xl w-full max-w-md border border-slate-100 overflow-hidden" style="box-shadow:0 24px 64px rgba(15,23,42,0.22);" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-building-columns text-blue-600 text-sm"></i></div>
                <h3 class="font-bold text-sm text-slate-900">New Department</h3>
            </div>
            <button type="button" onclick="document.getElementById('add-dept-modal').style.display='none'" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 hover:text-slate-700 transition-all"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>
        <form action="{{ route('superadmin.departments.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Institution</label>
                <select name="institution_id" required class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all cursor-pointer">
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Department Name</label>
                <input name="name" required placeholder="e.g. Data Science" class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Short Code</label>
                <input name="code" required placeholder="e.g. DS" maxlength="20" class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all">
            </div>
            <div>
                <label class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-2">Description (optional)</label>
                <textarea name="description" rows="2" class="form-input w-full px-4 py-2.5 rounded-xl text-sm font-medium text-slate-800 transition-all"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('add-dept-modal').style.display='none'" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all" style="background:#2563eb;box-shadow:0 4px 14px rgba(37,99,235,0.30);">
                    <i class="fa-solid fa-plus mr-1.5"></i> Create
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateClock() {
        const el = document.getElementById('live-clock');
        if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>

</body>
</html>