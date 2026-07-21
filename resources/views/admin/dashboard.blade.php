<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | ExamSystem Admin</title>
    <meta name="description" content="Admin dashboard overview for ExamSystem — students, exams, tickets, and activity, live.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-flash dark mode (matches student/teacher portals) -->
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        [x-cloak] { display: none !important; }

        /* Brand + nav (shared visual language with the student portal) */
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-nav-active { background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
        .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }

        /* Cards */
        .metric-card { background: var(--card-bg,#ffffff); border:1px solid var(--card-br,#e8edf5); box-shadow:0 1px 4px rgba(0,0,0,0.04),0 4px 16px rgba(0,0,0,0.03); transition:all 0.22s ease; }
        .metric-card:hover { box-shadow:0 4px 24px rgba(37,99,235,0.09); border-color:#bfdbfe; transform:translateY(-2px); }
        .chart-card { background: var(--card-bg,#ffffff); border:1px solid var(--card-br,#e8edf5); box-shadow:0 1px 4px rgba(0,0,0,0.04),0 6px 24px rgba(0,0,0,0.03); }

        .progress-bar { height:3px; border-radius:999px; background:#f1f5f9; overflow:hidden; }
        .progress-fill { height:100%; border-radius:999px; transition:width 1s ease; }

        @keyframes outerPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.7);opacity:0} }
        .pulse-dot { animation:outerPulse 1.8s ease-in-out infinite; }

        @keyframes fadeUp { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation:fadeUp 0.5s ease forwards; }

        .activity-row { transition:background 0.15s ease; }
        .activity-row:hover { background: var(--row-hover,#f8fafc); }

        .quick-btn { border:1px solid #e2e8f0; background:#ffffff; color:#475569; transition:all 0.18s ease; }
        .quick-btn:hover { background:#f8fafc; border-color:#cbd5e1; color:#1e293b; transform:translateY(-1px); box-shadow:0 3px 10px rgba(0,0,0,0.06); }

        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:transparent; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }

        .action-login    { color:#2563eb; background:#eff6ff; border-color:#bfdbfe; }
        .action-exam     { color:#7c3aed; background:#f5f3ff; border-color:#ddd6fe; }
        .action-user     { color:#059669; background:#f0fdf4; border-color:#bbf7d0; }
        .action-settings { color:#d97706; background:#fffbeb; border-color:#fde68a; }
        .action-security { color:#dc2626; background:#fff1f2; border-color:#fecdd3; }

        @keyframes shimmer { 0%{opacity:1} 50%{opacity:0.45} 100%{opacity:1} }
        .refreshing { animation:shimmer 0.7s ease; }

        @keyframes toastIn { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }
        @keyframes toastOut { from { opacity:1; } to { opacity:0; } }
        .toast { animation: toastIn 0.3s ease, toastOut 0.3s ease 4.7s forwards; }

        /* Dark mode surface overrides — driven by Alpine's `darkMode`, not the Tailwind `dark:` variant */
        .dark-surface { background:#0f172a; }
        .dark-card { --card-bg:#1e293b; --card-br:#334155; --row-hover:#1e293b; }
    </style>
    @include('partials.notification-styles')
</head>
<body class="antialiased transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="darkMode ? 'dark-surface text-slate-100' : 'bg-slate-50 text-slate-800'">
<div class="flex min-h-screen">

    @include('partials.admin-sidebar')

    <!-- ════════════ MAIN CONTENT ════════════ -->
    <main class="flex-1 ml-64 p-7 min-h-screen">

        <!-- TOP HEADER -->
        <header class="flex items-center justify-between mb-7 flex-wrap gap-4">
            <div>
                <h2 class="text-xl font-bold mb-1" :class="darkMode ? 'text-white' : 'text-slate-900'">
                    Good to see you, <span class="text-blue-600">{{ Auth::user()->full_name ?? 'Admin' }}</span> 👋
                </h2>
                <div class="flex items-center gap-3 flex-wrap">
                    <p class="text-sm text-slate-400">Here's your live overview.</p>
                    <span class="text-xs font-mono flex items-center gap-1.5 border px-2.5 py-1 rounded-lg"
                          :class="darkMode ? 'text-slate-400 bg-slate-800 border-slate-700' : 'text-slate-400 bg-white border-slate-200'">
                        <i class="fa-regular fa-calendar text-slate-300"></i>
                        <span id="live-date">--</span>
                    </span>
                    <span class="text-xs font-mono flex items-center gap-1.5 border px-2.5 py-1 rounded-lg"
                          :class="darkMode ? 'text-slate-400 bg-slate-800 border-slate-700' : 'text-slate-400 bg-white border-slate-200'">
                        <i class="fa-regular fa-clock text-slate-300"></i>
                        <span id="live-clock">--:--:--</span>
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs border px-3 py-1.5 rounded-xl"
                     :class="darkMode ? 'text-slate-400 bg-slate-800 border-slate-700' : 'text-slate-400 bg-white border-slate-200'">
                    <span class="relative flex items-center justify-center w-2 h-2">
                        <span class="pulse-dot absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span class="font-medium">Live</span>
                </div>

                @include('partials.admin-notification-bell')

                <div class="flex items-center gap-3 pl-3 border-l" :class="darkMode ? 'border-slate-700' : 'border-slate-200'">
                    <div class="text-right hidden sm:block">
                        <h4 class="text-sm font-semibold leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ Auth::user()->full_name ?? 'Admin User' }}</h4>
                        <span class="text-xs text-slate-400">Administrator</span>
                    </div>
                    @if(Auth::user()->avatar_url)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->full_name }}"
                             class="w-10 h-10 rounded-xl object-cover shadow" style="box-shadow:0 3px 10px rgba(37,99,235,0.3)">
                    @else
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm"
                            style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3)">
                            {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'AD' }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <!-- QUICK ACTIONS -->
        <div class="flex flex-wrap items-center gap-2.5 mb-7">
            <span class="text-xs font-bold uppercase tracking-widest text-slate-400 mr-1">Quick Actions:</span>
            <a href="{{ route('admin.users') }}?action=new" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold" :class="darkMode ? 'dark-card !text-slate-200 !border-slate-700' : ''">
                <i class="fa-solid fa-user-plus text-blue-500 text-[10px]"></i> Add User
            </a>
            <a href="{{ route('admin.exams') }}?action=new" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold" :class="darkMode ? 'dark-card !text-slate-200 !border-slate-700' : ''">
                <i class="fa-solid fa-file-plus text-violet-500 text-[10px]"></i> Create Exam
            </a>
            <a href="{{ route('admin.users.export') }}" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold" :class="darkMode ? 'dark-card !text-slate-200 !border-slate-700' : ''">
                <i class="fa-solid fa-file-export text-emerald-500 text-[10px]"></i> Export CSV
            </a>
            <a href="{{ route('admin.security') }}" class="quick-btn flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold" :class="darkMode ? 'dark-card !text-slate-200 !border-slate-700' : ''">
                <i class="fa-solid fa-shield-halved text-red-400 text-[10px]"></i> Security Logs
            </a>
        </div>

        <!-- METRIC CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 mb-6">

            <a href="{{ route('admin.users') }}" class="metric-card rounded-2xl p-5 block group" :class="darkMode ? 'dark-card' : ''">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Students & Instructors</p>
                        <h3 id="stat-users" class="text-3xl font-black fade-up" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ number_format($managedUsers) }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-all group-hover:scale-110" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-users text-blue-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-blue-400" style="width:{{ $managedUsers > 0 ? 72 : 4 }}%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600">
                    <i class="fa-solid fa-arrow-trend-up text-[9px]"></i>
                    <span id="stat-new-users">{{ $newUsersThisWeek ?? 0 }}</span>&nbsp;added this week
                </div>
            </a>

            <a href="{{ route('admin.exams') }}" class="metric-card rounded-2xl p-5 block group" :class="darkMode ? 'dark-card' : ''">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Active Exams</p>
                        <h3 id="stat-exams" class="text-3xl font-black fade-up" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $activeExams }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 transition-all group-hover:scale-110" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="fa-solid fa-file-invoice text-emerald-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-emerald-400" style="width:{{ $activeExams > 0 ? 40 : 4 }}%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                    <i class="fa-regular fa-clock text-[9px]"></i>
                    <span id="stat-ending-today">{{ $examsEndingToday ?? 0 }}</span>&nbsp;closing today
                </div>
            </a>

            <a href="{{ route('admin.support') }}" class="metric-card rounded-2xl p-5 block group" :class="darkMode ? 'dark-card' : ''">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Open Tickets</p>
                        <h3 id="stat-tickets" class="text-3xl font-black fade-up" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $openTickets ?? 0 }}</h3>
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

            <div class="metric-card rounded-2xl p-5" :class="darkMode ? 'dark-card' : ''">
                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="text-xs font-bold uppercase tracking-widest text-slate-400 mb-1">Today's Submissions</p>
                        <h3 id="stat-submissions" class="text-3xl font-black fade-up" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $submissionsToday ?? 0 }}</h3>
                    </div>
                    <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0" style="background:#f5f3ff;border:1px solid #ddd6fe">
                        <i class="fa-solid fa-pen-to-square text-violet-600 text-sm"></i>
                    </div>
                </div>
                <div class="progress-bar mb-2"><div class="progress-fill bg-violet-400" style="width:{{ ($submissionsToday ?? 0) > 0 ? 60 : 4 }}%"></div></div>
                <div class="flex items-center gap-1.5 text-[11px] font-semibold text-violet-600">
                    <i class="fa-solid fa-bolt text-[9px]"></i>
                    <span>Live exam activity</span>
                </div>
            </div>
        </div>

        <!-- CHARTS ROW -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 mb-6">

            <div class="chart-card rounded-2xl p-6 xl:col-span-2 relative" :class="darkMode ? 'dark-card' : ''">
                <div class="flex items-start justify-between mb-5">
                    <div>
                        <h3 class="font-bold text-base" :class="darkMode ? 'text-white' : 'text-slate-900'">Exam Submissions This Week</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Submissions across all exams you manage</p>
                    </div>
                    <div class="text-right">
                        <span id="today-count" class="text-2xl font-black text-blue-600">{{ $submissionsToday ?? 0 }}</span>
                        <p class="text-[10px] uppercase tracking-widest text-slate-400 font-bold mt-0.5">Today</p>
                    </div>
                </div>
                <div class="h-56 relative">
                    <canvas id="submissionsChart"></canvas>
                    <div id="submissions-empty" class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none" style="display:none">
                        <i class="fa-regular fa-chart-bar text-2xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-semibold text-slate-400">No submissions yet this week</p>
                        <p class="text-[11px] text-slate-300 mt-0.5">This chart fills in automatically as students submit exams.</p>
                    </div>
                </div>
            </div>

            <div class="chart-card rounded-2xl p-6" :class="darkMode ? 'dark-card' : ''">
                <div class="mb-4">
                    <h3 class="font-bold text-base" :class="darkMode ? 'text-white' : 'text-slate-900'">Exam Status</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Distribution by current state</p>
                </div>
                <div class="h-40 relative">
                    <canvas id="statusChart"></canvas>
                    <div id="status-empty" class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none" style="display:none">
                        <i class="fa-regular fa-file-lines text-2xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-semibold text-slate-400">No exams created yet</p>
                    </div>
                </div>
                <div class="mt-5 space-y-2.5">
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="flex items-center gap-2 text-slate-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>Active
                        </span>
                        <span id="status-active" class="font-black" :class="darkMode ? 'text-white' : 'text-slate-800'">{{ $activeExams ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="flex items-center gap-2 text-slate-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-400 inline-block"></span>Upcoming
                        </span>
                        <span id="status-upcoming" class="font-black" :class="darkMode ? 'text-white' : 'text-slate-800'">{{ $upcomingExams ?? 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between text-xs font-semibold">
                        <span class="flex items-center gap-2 text-slate-500">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-300 inline-block"></span>Closed
                        </span>
                        <span id="status-closed" class="font-black" :class="darkMode ? 'text-white' : 'text-slate-800'">{{ $closedExams ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECENT ACTIVITY TABLE -->
        <div class="chart-card rounded-2xl overflow-hidden" :class="darkMode ? 'dark-card' : ''">
            <div class="px-6 py-5 border-b flex items-center justify-between" :class="darkMode ? 'border-slate-700' : 'border-slate-100'">
                <div>
                    <h3 class="font-bold text-base" :class="darkMode ? 'text-white' : 'text-slate-900'">My Recent Activity</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Actions performed on your account</p>
                </div>
                <a href="{{ route('admin.security') }}"
                    class="text-xs font-semibold text-blue-600 flex items-center gap-1.5 px-3 py-1.5 rounded-lg transition-all hover:bg-blue-100"
                    style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-arrow-right text-[10px]"></i> Full Audit
                </a>
            </div>
            <div class="overflow-x-auto">
                @if($myLogs->isEmpty())
                <!-- FRESH / EMPTY STATE — no fabricated demo rows for a brand-new admin -->
                <div class="flex flex-col items-center justify-center text-center py-14 px-6">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center mb-3" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-clock-rotate-left text-blue-500 text-lg"></i>
                    </div>
                    <p class="text-sm font-bold" :class="darkMode ? 'text-white' : 'text-slate-700'">No activity yet</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-xs">Actions you take — creating exams, adding users, resolving tickets — will show up here in real time.</p>
                </div>
                @else
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b" :class="darkMode ? 'border-slate-700' : 'border-slate-100'">
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Action</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Details</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">Device / IP</th>
                            <th class="px-6 py-3.5 text-[10px] font-bold uppercase tracking-widest text-slate-400">When</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y" :class="darkMode ? 'divide-slate-800' : 'divide-slate-50'">
                        @foreach($myLogs as $log)
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
                                    <span class="text-sm font-semibold" :class="darkMode ? 'text-slate-200' : 'text-slate-800'">{{ $log->action }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500 max-w-xs truncate">{{ $payload['summary'] ?? '—' }}</td>
                            <td class="px-6 py-4 font-mono text-xs text-slate-400">{{ $log->ip_address ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-slate-400">{{ \Carbon\Carbon::parse($log->created_at)->diffForHumans() }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>

    </main>
</div>

<script>
    lucide.createIcons();

    /* Live clock */
    function updateClock() {
        const now = new Date();
        document.getElementById('live-clock').textContent = now.toLocaleTimeString();
        document.getElementById('live-date').textContent  = now.toLocaleDateString('en-US',{weekday:'short',month:'short',day:'numeric',year:'numeric'});
    }
    updateClock();
    setInterval(updateClock, 1000);

    /* Bar chart — starts with real (possibly all-zero) data from the server */
    const submCtx     = document.getElementById('submissionsChart').getContext('2d');
    const barGradient = submCtx.createLinearGradient(0, 0, 0, 220);
    barGradient.addColorStop(0, 'rgba(37,99,235,0.85)');
    barGradient.addColorStop(1, 'rgba(37,99,235,0.5)');

    let weeklyData = {!! json_encode($weeklySubmissions ?? [0,0,0,0,0,0,0]) !!};

    const submissionsChart = new Chart(submCtx, {
        type: 'bar',
        data: {
            labels: ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
            datasets: [{
                label: 'Submissions',
                data: weeklyData,
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
                y: { grid:{ color:'#f1f5f9' }, ticks:{ color:'#94a3b8', font:{ size:11 }, stepSize:5 }, border:{ display:false }, beginAtZero:true }
            }
        }
    });

    function toggleSubmissionsEmptyState(data) {
        const isEmpty = !data || data.every(v => !v);
        document.getElementById('submissions-empty').style.display = isEmpty ? 'flex' : 'none';
    }
    toggleSubmissionsEmptyState(weeklyData);

    /* Donut chart */
    let statusData = [{{ $activeExams ?? 0 }}, {{ $upcomingExams ?? 0 }}, {{ $closedExams ?? 0 }}];

    const statusChart = new Chart(document.getElementById('statusChart').getContext('2d'), {
        type: 'doughnut',
        data: {
            labels: ['Active','Upcoming','Closed'],
            datasets: [{
                data: statusData,
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

    function toggleStatusEmptyState(data) {
        const isEmpty = !data || data.every(v => !v);
        document.getElementById('status-empty').style.display = isEmpty ? 'flex' : 'none';
    }
    toggleStatusEmptyState(statusData);

    /* Real-time metric refresh — polls the (now fixed) telemetry endpoint every 8s */
    function refreshMetrics() {
        fetch('{{ route("admin.dashboard.api") }}', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                const map = {
                    'stat-users':        (data.managedUsers ?? 0).toLocaleString(),
                    'stat-new-users':    data.newUsersThisWeek ?? 0,
                    'stat-exams':        data.activeExams ?? 0,
                    'stat-ending-today': data.examsEndingToday ?? 0,
                    'stat-tickets':      data.openTickets ?? 0,
                    'stat-submissions':  data.submissionsToday ?? 0,
                    'stat-urgent':       data.urgentTickets ?? 0,
                    'today-count':       data.submissionsToday ?? 0,
                    'status-active':     data.activeExams ?? 0,
                    'status-upcoming':   data.upcomingExams ?? 0,
                    'status-closed':     data.closedExams ?? 0,
                };
                Object.entries(map).forEach(([id, val]) => {
                    const el = document.getElementById(id);
                    if (el && el.textContent !== String(val)) {
                        el.classList.add('refreshing');
                        el.textContent = val;
                        setTimeout(() => el.classList.remove('refreshing'), 700);
                    }
                });

                if (Array.isArray(data.weeklySubmissions)) {
                    submissionsChart.data.datasets[0].data = data.weeklySubmissions;
                    submissionsChart.update('none');
                    toggleSubmissionsEmptyState(data.weeklySubmissions);
                }

                const newStatusData = [data.activeExams ?? 0, data.upcomingExams ?? 0, data.closedExams ?? 0];
                statusChart.data.datasets[0].data = newStatusData;
                statusChart.update('none');
                toggleStatusEmptyState(newStatusData);
            })
            .catch(() => {});
    }
    setInterval(refreshMetrics, 8000);
</script>
@include('partials.admin-notification-realtime')
</body>
</html>
