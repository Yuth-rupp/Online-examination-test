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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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

    @include('partials.admin-sidebar')

    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <header class="flex items-center justify-between px-7 py-4 border-b sticky top-0 z-20 admin-topbar" style="box-shadow:0 4px 24px rgba(29,78,216,0.28)">
            <div>
                <h1 class="text-white font-bold text-lg">Department Directory</h1>
                <p class="text-blue-100 text-xs mt-0.5">Create departments and put an admin in charge of each one.</p>
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
                        Each department is its own space inside the university. Give a department to one admin
                        and they can only see and manage that department's teachers, students, and exams —
                        everything else stays hidden from them. Only you (super admin) see every department at once.
                    </p>
                </div>
                <button type="button" onclick="document.getElementById('add-dept-modal').classList.remove('hidden')"
                    class="flex items-center gap-2 text-white text-xs font-bold px-4 py-2.5 rounded-xl admin-brand-gradient">
                    <i class="fa-solid fa-plus"></i> New Department
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($departments as $dept)
                <div class="card rounded-2xl p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <h3 class="font-bold text-slate-900">{{ $dept->name }}</h3>
                            <span class="text-[11px] font-semibold uppercase tracking-wide text-slate-400">{{ $dept->code }}</span>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $dept->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                            {{ $dept->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 mb-4">
                        <div class="rounded-xl p-3" style="background:#eff6ff">
                            <p class="text-lg font-black text-blue-700">{{ $dept->students_count }}</p>
                            <p class="text-[11px] text-blue-500 font-semibold">Students</p>
                        </div>
                        <div class="rounded-xl p-3" style="background:#f0fdf4">
                            <p class="text-lg font-black text-emerald-700">{{ $dept->teachers_count }}</p>
                            <p class="text-[11px] text-emerald-500 font-semibold">Teachers</p>
                        </div>
                    </div>

                    <p class="text-[11px] font-bold uppercase tracking-wide text-slate-400 mb-2">Department Admin(s)</p>
                    @forelse($dept->admins as $admin)
                        <div class="flex items-center justify-between mb-1.5 text-sm">
                            <span class="text-slate-700">{{ $admin->full_name }} <span class="text-slate-400">({{ $admin->email }})</span></span>
                            <form action="{{ route('superadmin.departments.removeAdmin', [$dept->id, $admin->user_id]) }}" method="POST" onsubmit="return confirm('Remove this admin from the department?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700"><i class="fa-solid fa-xmark"></i></button>
                            </form>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 italic mb-2">No admin assigned yet.</p>
                    @endforelse

                    <div class="flex items-center gap-2 mt-3">
                        <form action="{{ route('superadmin.departments.assignAdmin', $dept->id) }}" method="POST" class="flex-1 flex items-center gap-2">
                            @csrf
                            <select name="user_id" required class="form-input flex-1 px-3 py-2 rounded-lg text-xs">
                                <option value="">Assign an admin...</option>
                                @foreach($unassignedAdmins as $u)
                                    <option value="{{ $u->user_id }}">{{ $u->full_name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-3 py-2 rounded-lg text-xs font-bold text-white admin-brand-gradient">Assign</button>
                        </form>
                    </div>

                    <a href="{{ route('admin.departments.teachers', $dept->id) }}" class="mt-3 inline-flex items-center gap-1.5 text-xs font-semibold text-blue-600 hover:text-blue-800">
                        <i class="fa-solid fa-chalkboard-user"></i> Manage teaching roster
                    </a>

                    <button type="button" onclick="document.getElementById('edit-dept-{{ $dept->id }}').classList.remove('hidden')"
                        class="mt-1 flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-slate-700">
                        <i class="fa-solid fa-pen"></i> Edit department
                    </button>
                </div>

                <!-- Edit modal for this department -->
                <div id="edit-dept-{{ $dept->id }}" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
                    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden">
                        <div class="p-5 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900">Edit {{ $dept->name }}</h3>
                        </div>
                        <form action="{{ route('superadmin.departments.update', $dept->id) }}" method="POST" class="p-5 space-y-4">
                            @csrf @method('PUT')
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Name</label>
                                <input name="name" value="{{ $dept->name }}" required class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Code</label>
                                <input name="code" value="{{ $dept->code }}" required class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Description</label>
                                <textarea name="description" rows="2" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">{{ $dept->description }}</textarea>
                            </div>
                            <label class="flex items-center gap-2 text-sm text-slate-600">
                                <input type="checkbox" name="is_active" value="1" {{ $dept->is_active ? 'checked' : '' }}> Active
                            </label>
                            <div class="flex gap-3 pt-1">
                                <button type="button" onclick="document.getElementById('edit-dept-{{ $dept->id }}').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200">Cancel</button>
                                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white admin-brand-gradient">Save</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <div class="col-span-full card rounded-2xl p-10 text-center text-slate-400">
                    <i class="fa-solid fa-building-columns text-3xl mb-3"></i>
                    <p>No departments yet. Create your first one to get started.</p>
                </div>
                @endforelse
            </div>
        </div>
    </main>
</div>

<!-- Create department modal -->
<div id="add-dept-modal" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-bold text-slate-900">New Department</h3>
            <button type="button" onclick="document.getElementById('add-dept-modal').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="{{ route('superadmin.departments.store') }}" method="POST" class="p-5 space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Institution</label>
                <select name="institution_id" required class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm bg-white">
                    @foreach($institutions as $inst)
                        <option value="{{ $inst->id }}">{{ $inst->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Department Name</label>
                <input name="name" required placeholder="e.g. Data Science" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Short Code</label>
                <input name="code" required placeholder="e.g. DS" maxlength="20" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1.5">Description (optional)</label>
                <textarea name="description" rows="2" class="form-input w-full px-3.5 py-2.5 rounded-xl text-sm"></textarea>
            </div>
            <div class="flex gap-3 pt-1">
                <button type="button" onclick="document.getElementById('add-dept-modal').classList.add('hidden')" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white admin-brand-gradient">Create</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
