<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Password Reset Requests — ExamSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>
    <style>
        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}
        .row-hover:hover{background:#f8fafc;}
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

            <a href="{{ route('superadmin.admins.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-users text-xs text-slate-400"></i></span><span>User Management</span>
            </a>

            {{-- PASSWORD RESET REQUESTS --}}
            <a href="{{ route('superadmin.passwordRequests.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-key text-xs text-white"></i></span>
                <span class="flex-1">Password Requests</span>
                @if($requests->where('status', 'pending')->count() > 0)
                    <span class="w-5 h-5 flex items-center justify-center rounded-full bg-white text-blue-700 text-[10px] font-extrabold">{{ $requests->where('status', 'pending')->count() }}</span>
                @endif
            </a>

            <a href="{{ route('superadmin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-building-columns text-xs text-slate-400"></i></span><span>Department Directory</span>
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
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Password Reset Requests</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Admins can't self-service reset — resolve their requests here.</p>
            </div>
        </header>

        <div class="p-8 flex-1">

            @php
                $pending  = $requests->where('status', 'pending');
                $resolved = $requests->where('status', 'resolved');
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border border-slate-100 rounded-2xl p-5" style="box-shadow:0 2px 10px rgba(148,163,184,0.08);">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Pending</div>
                    <div class="text-2xl font-extrabold text-amber-600">{{ $pending->count() }}</div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5" style="box-shadow:0 2px 10px rgba(148,163,184,0.08);">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Resolved</div>
                    <div class="text-2xl font-extrabold text-emerald-600">{{ $resolved->count() }}</div>
                </div>
                <div class="bg-white border border-slate-100 rounded-2xl p-5" style="box-shadow:0 2px 10px rgba(148,163,184,0.08);">
                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Total</div>
                    <div class="text-2xl font-extrabold text-slate-800">{{ $requests->count() }}</div>
                </div>
            </div>

            <div class="bg-white border border-slate-100 rounded-2xl overflow-hidden" style="box-shadow:0 2px 10px rgba(148,163,184,0.08);">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-slate-50 text-left text-[10px] font-bold text-slate-400 uppercase tracking-widest">
                            <th class="px-5 py-3">Admin</th>
                            <th class="px-5 py-3">Message</th>
                            <th class="px-5 py-3">Requested</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="requests-tbody">
                        @forelse ($requests as $row)
                            <tr class="row-hover border-t border-slate-50" id="request-row-{{ $row->id }}">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-slate-800">{{ $row->user->full_name ?? '(deleted account)' }}</div>
                                    <div class="text-xs text-slate-400">{{ $row->email }}</div>
                                </td>
                                <td class="px-5 py-4 text-slate-500 max-w-xs">
                                    {{ $row->message ?: '—' }}
                                </td>
                                <td class="px-5 py-4 text-slate-500 whitespace-nowrap">
                                    {{ $row->created_at->diffForHumans() }}
                                </td>
                                <td class="px-5 py-4">
                                    @if($row->status === 'pending')
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider bg-amber-50 text-amber-700 border border-amber-200 px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Pending
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Resolved
                                        </span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 text-right whitespace-nowrap">
                                    @if($row->status === 'pending' && $row->user)
                                        <button onclick="resolveRequest({{ $row->id }}, '{{ addslashes($row->user->full_name) }}')"
                                                class="text-xs font-bold text-blue-600 hover:text-blue-800 mr-3">
                                            <i class="fa-solid fa-key mr-1"></i> Reset & Resolve
                                        </button>
                                        <button onclick="dismissRequest({{ $row->id }})"
                                                class="text-xs font-bold text-slate-400 hover:text-slate-600">
                                            Dismiss
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-slate-400 text-sm">
                                    No password reset requests yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</div>

<div id="toast" class="toast-enter fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl text-sm font-semibold shadow-lg"></div>

<script>
    const CSRF = document.querySelector('meta[name="csrf-token"]').content;

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.className = 'toast-enter fixed bottom-6 right-6 z-50 px-4 py-3 rounded-xl text-sm font-semibold shadow-lg ' +
            (type === 'success' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white');
        requestAnimationFrame(() => toast.classList.add('toast-visible'));
        setTimeout(() => toast.classList.remove('toast-visible'), 3000);
    }

    window.resolveRequest = function (id, name) {
        if (!confirm(`Reset password for ${name || 'this admin'}? Their current password will stop working immediately.`)) return;

        fetch(`/super-admin/password-requests/${id}/resolve`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showResetPasswordModal(data.admin_name, data.new_password);
                markRowResolved(id);
            } else {
                showToast(data.message || 'Failed to reset password.', 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
    };

    window.dismissRequest = function (id) {
        if (!confirm('Dismiss this request without resetting the password?')) return;

        fetch(`/super-admin/password-requests/${id}/dismiss`, {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                showToast('Request dismissed.', 'success');
                markRowResolved(id);
            } else {
                showToast('Failed to dismiss request.', 'error');
            }
        })
        .catch(() => showToast('Network error.', 'error'));
    };

    function markRowResolved(id) {
        const row = document.getElementById(`request-row-${id}`);
        if (row) {
            const statusCell = row.children[3];
            const actionsCell = row.children[4];
            statusCell.innerHTML = `<span class="inline-flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-700 border border-emerald-200 px-2.5 py-1 rounded-full"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Resolved</span>`;
            actionsCell.innerHTML = `<span class="text-xs text-slate-300">—</span>`;
        }
    }

    window.showResetPasswordModal = function (name, newPassword) {
        let modal = document.getElementById('reset-password-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'reset-password-modal';
            modal.style.cssText = 'display:none;position:fixed;inset:0;background:rgba(15,23,42,.45);z-index:9999;align-items:center;justify-content:center;';
            modal.innerHTML = `
                <div style="background:#fff;border-radius:16px;padding:24px;width:360px;max-width:90vw;box-shadow:0 20px 60px rgba(0,0,0,.2);">
                    <h3 style="font-size:14px;font-weight:800;color:#0f172a;margin-bottom:4px;">Password Reset</h3>
                    <p id="reset-pw-subtitle" style="font-size:11px;color:#64748b;margin-bottom:14px;">Share this temporary password with the admin. It will not be shown again.</p>
                    <div style="display:flex;align-items:center;gap:8px;">
                        <input id="reset-pw-value" readonly style="flex:1;font-family:monospace;font-size:13px;font-weight:700;border:1px solid #e2e8f0;background:#f8fafc;border-radius:10px;padding:10px 12px;color:#0f172a;">
                        <button onclick="copyResetPassword()" style="border:1px solid #e2e8f0;background:#f8fafc;border-radius:10px;padding:10px 12px;cursor:pointer;color:#334155;"><i class="fa-solid fa-copy"></i></button>
                    </div>
                    <button onclick="document.getElementById('reset-password-modal').style.display='none'" style="margin-top:16px;width:100%;padding:10px;border-radius:10px;border:none;background:#2563eb;color:#fff;font-size:12px;font-weight:700;cursor:pointer;">Done</button>
                </div>`;
            document.body.appendChild(modal);
        }
        document.getElementById('reset-pw-subtitle').textContent = `New temporary password for ${name || 'this admin'}. Share it securely — it won't be shown again.`;
        document.getElementById('reset-pw-value').value = newPassword;
        modal.style.display = 'flex';
    };

    window.copyResetPassword = function () {
        const input = document.getElementById('reset-pw-value');
        input.select();
        navigator.clipboard?.writeText(input.value);
        showToast('Password copied to clipboard.', 'success');
    };
</script>
</body>
</html>