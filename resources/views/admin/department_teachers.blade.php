<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $department->name }} — Teachers | ExamSystem</title>
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
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-topbar { background: linear-gradient(120deg, #1d4ed8 0%, #2563eb 45%, #1e3a8a 100%); }
        .card { background:#fff; border:1px solid #e8edf5; box-shadow:0 2px 10px rgba(15,23,42,0.04); }
        .form-input { border:1px solid #e2e8f0; background:#fff; }
        .form-input:focus { outline:none; border-color:#2563eb; box-shadow:0 0 0 3px rgba(37,99,235,0.12); }
        #search-results { position:absolute; z-index:10; }
    </style>
</head>
<body class="antialiased bg-slate-50 text-slate-800">

<div class="flex min-h-screen">

    @include('partials.admin-sidebar')

    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <header class="flex items-center justify-between px-7 py-4 border-b sticky top-0 z-20 admin-topbar" style="box-shadow:0 4px 24px rgba(29,78,216,0.28)">
            <div>
                <h1 class="text-white font-bold text-lg">{{ $department->name }} — Teaching Roster</h1>
                <p class="text-blue-100 text-xs mt-0.5">Add teachers who already exist elsewhere so they can also teach in this department.</p>
            </div>
            <div class="text-right">
                <h4 class="text-sm font-semibold text-white">{{ Auth::user()->full_name ?? 'Admin' }}</h4>
                <span class="text-xs text-blue-200">{{ Auth::user()->role === 'super_admin' ? 'Super Administrator' : 'Department Admin' }}</span>
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
                        <form action="{{ route('admin.departments.teachers.remove', [$department->id, $teacher->user_id]) }}" method="POST" onsubmit="return confirm('Remove this teacher from {{ $department->name }}?');">
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
const searchUrl     = @json(route('admin.departments.teachers.search', $department->id));
const assignUrl     = @json(route('admin.departments.teachers.assign', $department->id));
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
                            <button type="button" data-id="${t.user_id}" class="assign-btn text-xs font-bold text-white px-3 py-1.5 rounded-lg admin-brand-gradient" style="background:linear-gradient(135deg,#2563eb,#1e40af)">Add</button>
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
