<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Institutions | ExamSystem</title>
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-topbar { background: linear-gradient(120deg, #1d4ed8 0%, #2563eb 45%, #1e3a8a 100%); }
        .card { background:#fff; border:1px solid #e8edf5; box-shadow:0 2px 10px rgba(15,23,42,0.04); }
        .modal-overlay { background: rgba(15,23,42,0.35); backdrop-filter: blur(4px); }
        .form-input { border:1px solid #e2e8f0; background:#fff; }
        .form-input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
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

            <a href="{{ route('superadmin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-building-columns text-xs text-slate-400"></i></span><span>Department Directory</span>
            </a>

            {{-- INSTITUTIONS (active on this page) --}}
            <a href="{{ route('superadmin.institutions.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-landmark text-xs text-white"></i></span><span>Institutions</span>
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

    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <header class="flex items-center justify-between px-7 py-4 border-b sticky top-0 z-20 admin-topbar" style="box-shadow:0 4px 24px rgba(29,78,216,0.28)">
            <div>
                <h1 class="text-white font-bold text-lg">Institution Directory</h1>
                <p class="text-blue-100 text-xs mt-0.5">Onboard a university's email domain so its students and teachers can self-register.</p>
            </div>
            <div class="text-right">
                <h4 class="text-sm font-semibold text-white">{{ Auth::user()->full_name ?? 'Super Admin' }}</h4>
                <span class="text-xs text-blue-200">Super Administrator</span>
            </div>
        </header>

        <div class="p-7">

            @if(session('success'))
            <div class="mb-5 p-3.5 rounded-xl flex items-center gap-2.5 text-sm font-medium text-emerald-700" style="background:#f0fdf4;border:1px solid #bbf7d0">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
            @endif

            @if($errors->any())
            <div class="mb-5 p-3.5 rounded-xl text-sm font-medium text-red-700" style="background:#fef2f2;border:1px solid #fecaca">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
            @endif

            <div class="flex items-center justify-between mb-5">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">How this works</h2>
                    <p class="text-sm text-slate-500 mt-1 max-w-2xl">
                        The registration form checks a new signup's email domain against this list. If nobody
                        has added a university's domain here yet, that university's students and teachers will
                        see: <span class="italic">"Your email domain isn't registered to a university on this
                        platform yet."</span> — add it below and self-registration opens up for that domain immediately.
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('add-inst-modal').classList.remove('hidden')"
                    class="flex items-center gap-2 text-white text-xs font-bold px-4 py-2.5 rounded-xl admin-brand-gradient whitespace-nowrap">
                    <i class="fa-solid fa-plus"></i> Onboard University
                </button>
            </div>

            <div class="card rounded-2xl overflow-hidden">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-left">
                            <th class="px-5 py-3 text-[11px] font-black uppercase tracking-wide text-slate-400">University</th>
                            <th class="px-5 py-3 text-[11px] font-black uppercase tracking-wide text-slate-400">Email Domain</th>
                            <th class="px-5 py-3 text-[11px] font-black uppercase tracking-wide text-slate-400">Users</th>
                            <th class="px-5 py-3 text-[11px] font-black uppercase tracking-wide text-slate-400">Status</th>
                            <th class="px-5 py-3 text-[11px] font-black uppercase tracking-wide text-slate-400 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($institutions as $inst)
                        <tr class="border-b border-slate-50 last:border-0">
                            <td class="px-5 py-4 font-bold text-slate-900">{{ $inst->name }}</td>
                            <td class="px-5 py-4">
                                <code class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded-lg">{{ '@' . $inst->domain }}</code>
                            </td>
                            <td class="px-5 py-4 text-slate-600">{{ $inst->users_count }}</td>
                            <td class="px-5 py-4">
                                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $inst->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    {{ $inst->is_active ? 'Active — registration open' : 'Inactive — registration blocked' }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <button type="button" onclick="document.getElementById('edit-inst-{{ $inst->id }}').classList.remove('hidden')"
                                        class="text-xs font-semibold text-slate-500 hover:text-slate-800">
                                        <i class="fa-solid fa-pen"></i> Edit
                                    </button>
                                    <form action="{{ route('superadmin.institutions.toggleStatus', $inst->id) }}" method="POST"
                                          onsubmit="return confirm('{{ $inst->is_active ? 'Deactivate this university? New self-registrations from this domain will be blocked immediately.' : 'Reactivate this university?' }}');">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs font-semibold {{ $inst->is_active ? 'text-red-500 hover:text-red-700' : 'text-emerald-600 hover:text-emerald-800' }}">
                                            <i class="fa-solid {{ $inst->is_active ? 'fa-ban' : 'fa-circle-check' }}"></i> {{ $inst->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>

                        @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                <i class="fa-solid fa-building-columns text-3xl mb-3 block"></i>
                                No universities onboarded yet. Add one so its students and teachers can self-register.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Edit modals (one per institution, outside the table for valid HTML) -->
            @foreach($institutions as $inst)
            <div id="edit-inst-{{ $inst->id }}" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden">
                    <div class="p-5 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="font-bold text-slate-900">Edit {{ $inst->name }}</h3>
                        <button type="button" onclick="document.getElementById('edit-inst-{{ $inst->id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
                    </div>
                    <form action="{{ route('superadmin.institutions.update', $inst->id) }}" method="POST" class="p-5 space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">University Name</label>
                            <input name="name" value="{{ $inst->name }}" required class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Email Domain</label>
                            <input name="domain" value="{{ $inst->domain }}" required placeholder="e.g. rupp.edu.kh" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
                            <p class="text-[11px] text-slate-400 mt-1">Just the domain — no "@" and no "student.".</p>
                        </div>
                        <div class="flex gap-3 pt-1">
                            <button type="button" onclick="document.getElementById('edit-inst-{{ $inst->id }}').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200">Cancel</button>
                            <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white admin-brand-gradient">Save</button>
                        </div>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </main>
</div>

<!-- Onboard university modal -->
<div id="add-inst-modal" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">Onboard a University</h3>
            <button type="button" onclick="document.getElementById('add-inst-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('superadmin.institutions.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">University Name</label>
                <input name="name" required placeholder="e.g. Royal University of Phnom Penh" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Email Domain</label>
                <input name="domain" required placeholder="e.g. rupp.edu.kh" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
                <p class="text-[11px] text-slate-400 mt-1">Just the domain — no "@". Anyone with an email ending in this domain will be able to self-register.</p>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('add-inst-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white admin-brand-gradient">Onboard</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>