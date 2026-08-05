<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $department->name }} — Teaching Roster | {{ $platformName }}</title>
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
        .card { background:#fff; border:1px solid #e8edf5; box-shadow:0 2px 10px rgba(15,23,42,0.04); }
        .form-input { border:1px solid #e2e8f0; background:#fff; }
        .form-input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
        #search-results { position:absolute; z-index:10; }
        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}
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
                <h1 class="font-extrabold text-slate-900 text-sm tracking-tight leading-none">{{ $platformName }}</h1>
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

            <a href="{{ route('superadmin.passwordRequests.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-key text-xs text-slate-400"></i></span><span>Password Requests</span>
            </a>

            {{-- DEPARTMENT DIRECTORY (active section) --}}
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

    {{-- ===================== MAIN ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <header class="flex items-center justify-between px-7 py-4 border-b sticky top-0 z-20"
                style="background:linear-gradient(120deg, #1d4ed8 0%, #2563eb 45%, #1e3a8a 100%);box-shadow:0 4px 24px rgba(29,78,216,0.28)">
            <div>
                <a href="{{ route('superadmin.departments.index') }}" class="text-blue-100 text-[11px] font-semibold hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left mr-1"></i> Department Directory
                </a>
                <h1 class="text-white font-bold text-lg mt-1">{{ $department->name }} — Teaching Roster</h1>
                <p class="text-blue-100 text-xs mt-0.5">Add teachers who already exist elsewhere so they can also teach in this department.</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <h4 class="text-sm font-semibold text-white">{{ Auth::user()->full_name ?? 'Super Admin' }}</h4>
                    <span class="text-xs text-blue-200">Super Administrator</span>
                </div>
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->full_name }}"
                         class="w-9 h-9 rounded-xl object-cover ring-2 ring-white/40" style="box-shadow:0 3px 10px rgba(0,0,0,0.25)">
                @else
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-blue-700 font-bold text-sm bg-white ring-2 ring-white/40" style="box-shadow:0 3px 10px rgba(0,0,0,0.25)">
                        {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'SA' }}
                    </div>
                @endif
            </div>
        </header>

        <div class="p-7 max-w-3xl">

            @if(session('success'))
            <div class="mb-5 p-3.5 rounded-xl flex items-center gap-2.5 text-sm font-medium text-emerald-700" style="background:#f0fdf4;border:1px solid #bbf7d0">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
            </div>
            @endif

            <!-- Add a teacher -->
            <div class="card rounded-2xl p-5 mb-6">
                <h3 class="font-bold text-slate-900 mb-1">Add a teacher to this department</h3>
                <p class="text-xs text-slate-400 mb-4">
                    Search by name or email. A teacher can teach in more than one department at the same time —
                    adding them here does not remove them from any department they already teach in.
                </p>

                <div class="relative">
                    <input id="teacher-search" type="text" placeholder="Search teachers by name or email..."
                        class="form-input w-full px-4 py-2.5 rounded-xl text-sm" autocomplete="off">
                    <div id="search-results" class="w-full mt-1 bg-white rounded-xl border border-slate-200 shadow-lg hidden max-h-64 overflow-y-auto"></div>
                </div>
            </div>

            <!-- Current roster -->
            <div class="card rounded-2xl overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Currently teaching in {{ $department->name }}</h3>
                </div>
                <div class="divide-y divide-slate-100">
                    @forelse($teachers as $teacher)
                    <div class="px-5 py-3.5 flex items-center justify-between">
                        <div>
                            <p class="text-sm font-semibold text-slate-800">{{ $teacher->full_name }}</p>
                            <p class="text-xs text-slate-400">{{ $teacher->email }}</p>
                        </div>
                        <form action="{{ route('superadmin.departments.teachers.remove', [$department->id, $teacher->user_id]) }}" method="POST" onsubmit="return confirm('Remove this teacher from {{ $department->name }}?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-semibold text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-user-minus mr-1"></i> Remove
                            </button>
                        </form>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-slate-400 text-sm">
                        No teachers assigned to this department yet. Use the search box above to add one.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</div>

<script>
const searchInput  = document.getElementById('teacher-search');
const resultsBox    = document.getElementById('search-results');
const searchUrl     = @json(route('superadmin.departments.teachers.search', $department->id));
const assignUrl     = @json(route('superadmin.departments.teachers.assign', $department->id));
const csrfToken      = @json(csrf_token());
let debounceTimer;

searchInput.addEventListener('input', function () {
    clearTimeout(debounceTimer);
    const q = this.value.trim();
    if (q.length < 2) { resultsBox.classList.add('hidden'); resultsBox.innerHTML = ''; return; }

    debounceTimer = setTimeout(() => {
        fetch(`${searchUrl}?q=${encodeURIComponent(q)}`)
            .then(r => r.json())
            .then(teachers => {
                if (!teachers.length) {
                    resultsBox.innerHTML = '<div class="px-4 py-3 text-sm text-slate-400">No matching teachers found.</div>';
                } else {
                    resultsBox.innerHTML = teachers.map(t => `
                        <div class="px-4 py-3 flex items-center justify-between hover:bg-slate-50 cursor-pointer border-b border-slate-100 last:border-0">
                            <div>
                                <p class="text-sm font-semibold text-slate-800">${t.full_name}</p>
                                <p class="text-xs text-slate-400">${t.email}</p>
                            </div>
                            <button type="button" data-id="${t.user_id}" class="assign-btn text-xs font-bold text-white px-3 py-1.5 rounded-lg" style="background:linear-gradient(135deg,#2563eb,#1e40af)">Add</button>
                        </div>
                    `).join('');
                }
                resultsBox.classList.remove('hidden');
            });
    }, 300);
});

resultsBox.addEventListener('click', function (e) {
    const btn = e.target.closest('.assign-btn');
    if (!btn) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = assignUrl;
    form.innerHTML = `@csrf <input type="hidden" name="user_id" value="${btn.dataset.id}">`;
    document.body.appendChild(form);
    form.submit();
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('#teacher-search') && !e.target.closest('#search-results')) {
        resultsBox.classList.add('hidden');
    }
});
</script>

</body>
</html>
