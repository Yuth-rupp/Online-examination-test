<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ExamSystem Admin</title>
    <meta name="description" content="Admin dashboard overview for ExamSystem — students, exams, tickets, and activity.">
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }

        body { background: #f8fafc; }

        /* ── Sidebar ── */
        .sidebar { background:#ffffff; border-right:1px solid #e8edf5; box-shadow:2px 0 12px rgba(0,0,0,0.04); }
        .brand-icon { background:linear-gradient(135deg,#2563eb 0%,#1e40af 100%); box-shadow:0 4px 12px rgba(37,99,235,0.3); }
        .nav-active { background:linear-gradient(135deg,#eff6ff 0%,#dbeafe 100%); color:#1d4ed8 !important; border:1px solid #bfdbfe; border-left:3px solid #2563eb; }
        .nav-active i { color:#2563eb !important; }
        .nav-item { border:1px solid transparent; border-left:3px solid transparent; color:#64748b; transition:all 0.18s ease; }
        .nav-item:hover { background:#f8fafc; border-color:#e2e8f0; border-left-color:#94a3b8; color:#1e293b; }

        /* ── Cards ── */
        .metric-card { background:#ffffff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(0,0,0,0.04),0 4px 16px rgba(0,0,0,0.03); transition:all 0.22s ease; }
        .metric-card:hover { box-shadow:0 4px 24px rgba(37,99,235,0.09); border-color:#bfdbfe; transform:translateY(-2px); }
        .chart-card { background:#ffffff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(0,0,0,0.04),0 6px 24px rgba(0,0,0,0.03); }

        /* ── Progress bar ── */
        .progress-bar { height:3px; border-radius:999px; background:#f1f5f9; overflow:hidden; }
        .progress-fill { height:100%; border-radius:999px; transition:width 1s ease; }

        /* ── Pulse ── */
        @keyframes outerPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.7);opacity:0} }
        .pulse-dot { animation:outerPulse 1.8s ease-in-out infinite; }

        /* ── Count up animation ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation:fadeUp 0.5s ease forwards; }

        /* ── Table row hover ── */
        .activity-row { transition:background 0.15s ease; }
        .activity-row:hover { background:#f8fafc; }

        /* ── Quick action buttons ── */
        .quick-btn { border:1px solid #e2e8f0; background:#ffffff; color:#475569; transition:all 0.18s ease; }
        .quick-btn:hover { background:#f8fafc; border-color:#cbd5e1; color:#1e293b; transform:translateY(-1px); box-shadow:0 3px 10px rgba(0,0,0,0.06); }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:#f1f5f9; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }

        /* ── Action icon colors ── */
        .action-login    { color:#2563eb; background:#eff6ff; border-color:#bfdbfe; }
        .action-exam     { color:#7c3aed; background:#f5f3ff; border-color:#ddd6fe; }
        .action-user     { color:#059669; background:#f0fdf4; border-color:#bbf7d0; }
        .action-settings { color:#d97706; background:#fffbeb; border-color:#fde68a; }
        .action-security { color:#dc2626; background:#fff1f2; border-color:#fecdd3; }

        /* ── Shimmer for refresh ── */
        @keyframes shimmer { 0%{opacity:1} 50%{opacity:0.45} 100%{opacity:1} }
        .refreshing { animation:shimmer 0.7s ease; }
    </style>
</head>
<body class="antialiased text-slate-800">
<div class="flex min-h-screen">

    <!-- ════════════ SIDEBAR ════════════ -->
    <aside class="sidebar w-64 flex flex-col justify-between fixed h-full z-20">
        <div>
            <!-- Brand -->
            <div class="px-6 py-5 flex items-center gap-3 border-b border-slate-100">
                <div class="brand-icon w-10 h-10 rounded-xl flex items-center justify-center text-white shrink-0">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <div>
                    <h1 class="font-bold text-slate-900 text-sm leading-tight">ExamSystem</h1>
                    <span class="text-[11px] text-slate-400 font-medium">Admin Console</span>
                </div>
            </div>

            <!-- Nav -->
            <nav class="p-3 mt-1 space-y-0.5">
                <a href="{{ route('admin.dashboard') }}" class="nav-active flex items-center gap-3 px-3 py-2.5 rounded-xl font-semibold text-sm">
                    <i class="fa-solid fa-chart-line w-5 text-center text-sm"></i>
                    <span>Dashboard</span>
                </a>
                <a href="{{ route('admin.users') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-users-gear w-5 text-center text-slate-400 text-sm"></i>
                    <span>User Management</span>
                </a>
                <a href="{{ route('admin.exams') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-file-pen w-5 text-center text-slate-400 text-sm"></i>
                    <span>Exams</span>
                    <span class="ml-auto text-[9px] font-bold uppercase tracking-wide bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full">New</span>
                </a>
                <a href="{{ route('admin.support') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-headset w-5 text-center text-slate-400 text-sm"></i>
                    <span>Support Desk</span>
                    @if($openTickets ?? 0)
                    <span class="ml-auto text-[10px] font-bold bg-rose-100 text-rose-600 px-2 py-0.5 rounded-full">{{ $openTickets }}</span>
                    @endif
                </a>
                <a href="{{ route('admin.security') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                    <i class="fa-solid fa-shield-halved w-5 text-center text-slate-400 text-sm"></i>
                    <span>Security</span>
                </a>
            </nav>
        </div>

        <!-- Settings -->
        <div class="p-3 border-t border-slate-100">
            <a href="{{ route('admin.settings') }}" class="nav-item flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-sm">
                <i class="fa-solid fa-gear w-5 text-center text-slate-400 text-sm"></i>
                <span>Settings</span>
            </a>
        </div>
    </aside>

    <!-- ════════════ MAIN CONTENT ════════════ -->
    <main class="flex-1 ml-64 p-7 min-h-screen">

        <!-- TOP HEADER -->
        <header class="flex items-center justify-between mb-7">
            <div>
                <h2 class="text-xl font-bold text-slate-900 mb-1">
                    Good to see you, <span class="text-blue-600">{{ Auth::user()->first_name ?? 'Admin' }}</span> 👋
                </h2>
                <div class="flex items-center gap-3 flex-wrap">
                    <p class="text-sm text-slate-400">Here's your overview for today.</p>
                    <span class="text-xs font-mono text-slate-400 flex items-center gap-1.5 bg-white border border-slate-200 px-2.5 py-1 rounded-lg">
                        <i class="fa-regular fa-calendar text-slate-300"></i>
                        <span id="live-date">--</span>
                    </span>
                    <span class="text-xs font-mono text-slate-400 flex items-center gap-1.5 bg-white border border-slate-200 px-2.5 py-1 rounded-lg">
                        <i class="fa-regular fa-clock text-slate-300"></i>
                        <span id="live-clock">--:--:--</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-xl">
                    <span class="relative flex items-center justify-center w-2 h-2">
                        <span class="pulse-dot absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="font-medium text-slate-500">Live</span>
                </div>
                <div class="flex items-center gap-3 pl-3 border-l border-slate-200">
                    <div class="text-right">
                        <h4 class="text-sm font-semibold text-slate-900 leading-tight">{{ Auth::user()->full_name ?? 'Admin User' }}</h4>
                        <span class="text-xs text-slate-400">Administrator</span>
                    </div>
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm"
                        style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3)">
                        {{ Auth::user()->initials ?? 'AD' }}
                    </div>
                </div>
            </div>
        </header>

        <!-- QUICK ACTIONS -->
        <div class="flex flex-wrap items-center gap-2.5 mb-7">
            <span class="text-xs font-bold uppercase tracking-widest text-slate-400 mr-1">Quick Actions:</span>
            <a href="{{ route('admin.users') }}?action=new" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-user-plus text-blue-500 text-[10px]"></i> Add User
            </a>
            <a href="{{ route('admin.exams') }}?action=new" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-file-plus text-violet-500 text-[10px]"></i> Create Exam
            </a>
            <a href="{{ route('admin.users.export') }}" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-file-export text-emerald-500 text-[10px]"></i> Export CSV
            </a>
            <a href="{{ route('admin.security') }}" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold">
                <i class="fa-solid fa-shield-halved text-red-400 text-[10px]"></i> Security Logs
            </a>
        </div>

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

            <!-- Students & Instructors -->
            <a href="{{ route('admin.users') }}" class="metric-card rounded-2xl p-5 block group">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Students & Instructors</p>
                        <h3 id="stat-users" class="text-3xl font-black text-slate-900 fade-up">{{ number_format($managedUsers) }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-all group-hover:scale-110" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-blue-400" style="width:72%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <i class="fa-solid fa-arrow-trend-up text-[9px]"></i>
                    <span>{{ $newUsersThisWeek ?? 0 }} added this week</span>
                </div>
            </a>

            <!-- Active Exams -->
            <a href="{{ route('admin.exams') }}" class="metric-card rounded-2xl p-5 block group">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Active Exams</p>
                        <h3 id="stat-exams" class="text-3xl font-black text-slate-900 fade-up">{{ $activeExams }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-all group-hover:scale-110" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="fa-solid fa-file-invoice text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-emerald-400" style="width:40%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                    <i class="fa-regular fa-clock text-[9px]"></i>
                    <span>{{ $examsEndingToday ?? 0 }} closing today</span>
                </div>
            </a>

            <!-- Open Tickets -->
            <a href="{{ route('admin.support') }}" class="metric-card rounded-2xl p-5 block group">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Open Tickets</p>
                        <h3 id="stat-tickets" class="text-3xl font-black text-slate-900 fade-up">{{ $openTickets ?? 0 }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-all group-hover:scale-110" style="background:#fffbeb;border:1px solid #fde68a">
                        <i class="fa-solid fa-headset text-amber-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-amber-400" style="width:{{ min(($openTickets ?? 0) * 5, 100) }}%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-amber-600">
                    <i class="fa-solid fa-triangle-exclamation text-[9px]"></i>
                    <span id="stat-urgent">{{ $urgentTickets ?? 0 }}</span>&nbsp;marked urgent
                </div>
            </a>

            <!-- Today's Submissions -->
            <div class="metric-card rounded-2xl p-5">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Today's Submissions</p>
                        <h3 id="stat-submissions" class="text-3xl font-black text-slate-900 fade-up">{{ $submissionsToday ?? 0 }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f5f3ff;border:1px solid #ddd6fe">
                        <i class="fa-solid fa-pen-to-square text-violet-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-violet-400" style="width:60%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-violet-600">
                    <i class="fa-solid fa-bolt text-[9px]"></i>
                    <span>Exam activity today</span>
                </div>
            </div>
        </div>

        <!-- CHARTS ROW -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

            <!-- Bar chart — 2/3 width -->
            <div class="chart-card rounded-2xl p-6 xl:col-span-2">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-base text-slate-900">Exam Submissions This Week</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Submissions across all exams you manage</p>
                    </div>
                    <div class="text-right">
                        <span id="today-count" class="text-2xl font-black text-blue-600">{{ $submissionsToday ?? 0 }}</span>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-0.5">Today</p>
                    </div>
                </div>
                <div class="h-56 relative">
                    <canvas id="submissionsChart"></canvas>
                </div>
            </div>

            <!-- Donut chart — 1/3 width -->
            <div class="chart-card rounded-2xl p-6">
                <div class="mb-4">
                    <h3 class="font-bold text-base text-slate-900">Exam Status</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Distribution by current state</p>
                </div>
                <div class="h-40 relative">
                    <canvas id="statusChart"></canvas>
                </div>
                <div class="mt-5 space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>Active
                        </span>
                        <span class="font-black text-slate-800">{{ $activeExams ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>Upcoming
                        </span>
                        <span class="font-black text-slate-800">{{ $upcomingExams ?? 3 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="flex items-center gap-2 text-slate-600">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 inline-block"></span>Closed
                        </span>
                        <span class="font-black text-slate-800">{{ $closedExams ?? 8 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT ACTIVITY TABLE -->
        <div class="chart-card rounded-2xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-base text-slate-900">My Recent Activity</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Actions performed on your account</p>
                </div>
                <a href="{{ route('admin.security') }}"
                    class="text-xs font-semibold text-blue-600 flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all hover:bg-blue-100"
                    style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-arrow-right text-[10px]"></i> Full Audit
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead style="background:linear-gradient(135deg,#f8fafc,#f1f5f9)" class="border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Details</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Device / IP</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">When</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50">
                        @forelse($myLogs as $log)
                        @php
                            $actionLower = strtolower($log->action ?? '');
                            $iconClass = 'fa-bolt'; $colorClass = 'action-settings';
                            if (str_contains($actionLower,'login')||str_contains($actionLower,'sign'))        { $iconClass='fa-right-to-bracket'; $colorClass='action-login'; }
                            elseif (str_contains($actionLower,'exam'))                                        { $iconClass='fa-file-pen';         $colorClass='action-exam'; }
                            elseif (str_contains($actionLower,'user')||str_contains($actionLower,'created'))  { $iconClass='fa-user-plus';        $colorClass='action-user'; }
                            elseif (str_contains($actionLower,'security')||str_contains($actionLower,'pass')) { $iconClass='fa-shield-halved';    $colorClass='action-security'; }
                            $payload = json_decode($log->payload ?? '{}', true);
                        @endphp
                        <tr class="activity-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="{{ $colorClass }} w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border text-[11px]">
                                        <i class="fa-solid {{ $iconClass }}"></i>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">{{ $log->action }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ $payload['summary'] ?? '—' }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-400">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                        </tr>
                        @empty
                        @php
                            $placeholders = [
                                ['icon'=>'fa-right-to-bracket','cls'=>'action-login','action'=>'Signed In','detail'=>'New session started','ip'=>'127.0.0.1','when'=>'Just now'],
                                ['icon'=>'fa-file-pen','cls'=>'action-exam','action'=>'Viewed Exams','detail'=>'Exam list accessed','ip'=>'127.0.0.1','when'=>'2 min ago'],
                                ['icon'=>'fa-user-plus','cls'=>'action-user','action'=>'Created User','detail'=>'New student account registered','ip'=>'127.0.0.1','when'=>'1 hour ago'],
                            ];
                        @endphp
                        @foreach($placeholders as $p)
                        <tr class="activity-row">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <span class="{{ $p['cls'] }} w-7 h-7 rounded-lg flex items-center justify-center shrink-0 border text-[11px]">
                                        <i class="fa-solid {{ $p['icon'] }}"></i>
                                    </span>
                                    <span class="text-sm font-semibold text-slate-800">{{ $p['action'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500">{{ $p['detail'] }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $p['ip'] }}</td>
                            <td class="px-6 py-4 text-sm text-slate-400">{{ $p['when'] }}</td>
                        </tr>
                        @endforeach
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</div>

<script>
    /* ── Live clock ── */
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent = now.toLocaleTimeString();
        document.getElementById('live-date').textContent  = now.toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric',year:'numeric'});
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* ── Bar chart ── */
    const submCtx     = document.getElementById('submissionsChart').getContext('2d');
    const barGradient = submCtx.createLinearGradient(0, 0, 0, 220);
    barGradient.addColorStop(0, 'rgba(37,99,235,0.85)');
    barGradient.addColorStop(1, 'rgba(37,99,235,0.5)');

    const submissionsChart = new Chart(submCtx, {
        type: 'bar',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{
                label: 'Submissions',
                data: {!! json_encode($weeklySubmissions ?? [12,19,14,22,18,6,4]) !!},
                backgroundColor: barGradient,
                borderRadius: 8,
                barThickness: 26,
                hoverBackgroundColor: '#1d4ed8'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#94a3b8',
                    bodyColor: '#ffffff', bodyFont: { weight:'bold', size:14 },
                    padding: 10, cornerRadius: 10, displayColors: false,
                    callbacks: { label: ctx => ctx.parsed.y + ' submissions' }
                }
            },
            scales: {
                x: { grid:{ display:false }, ticks:{ color:'#94a3b8', font:{ size:11,family:'Inter' } }, border:{ display:false } },
                y: { grid:{ color:'#f1f5f9' }, ticks:{ color:'#94a3b8', font:{ size:11 }, stepSize:5 }, border:{ display:false } }
            }
        }
    });

    /* ── Donut chart ── */
    new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Active','Upcoming','Closed'],
            datasets: [{
                data: [{{ $activeExams ?? 0 }}, {{ $upcomingExams ?? 3 }}, {{ $closedExams ?? 8 }}],
                backgroundColor: ['#3b82f6','#fbbf24','#e2e8f0'],
                borderWidth: 0, hoverOffset: 5
            }]
        },
        options: {
            responsive: true, maintainAspectRatio: false, cutout: '70%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#1e293b', titleColor: '#94a3b8',
                    bodyColor: '#ffffff', bodyFont: { weight:'bold', size:13 },
                    padding: 10, cornerRadius: 10, displayColors: false
                }
            }
        }
    });

    /* ── Real-time metric refresh every 10s ── */
    function refreshMetrics() {
        fetch('{{ route("admin.dashboard.api") }}')
            .then(r => r.json())
            .then(data => {
                const map = {
                    'stat-users':       data.managedUsers?.toLocaleString() ?? '—',
                    'stat-exams':       data.activeExams       ?? '—',
                    'stat-tickets':     data.openTickets       ?? '—',
                    'stat-submissions': data.submissionsToday  ?? '—',
                    'stat-urgent':      data.urgentTickets     ?? '0',
                    'today-count':      data.submissionsToday  ?? '—',
                };
                Object.entries(map).forEach(([id, val]) => {
                    const el = document.getElementById(id);
                    if (el && el.textContent !== String(val)) {
                        el.classList.add('refreshing');
                        el.textContent = val;
                        setTimeout(() => el.classList.remove('refreshing'), 700);
                    }
                });
                if (data.weeklySubmissions) {
                    submissionsChart.data.datasets[0].data = data.weeklySubmissions;
                    submissionsChart.update('none');
                }
            })
            .catch(() => {});
    }
    setInterval(refreshMetrics, 10000);
</script>
</body>
</html>