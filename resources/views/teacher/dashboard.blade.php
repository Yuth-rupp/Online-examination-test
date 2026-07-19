<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; -webkit-font-smoothing: antialiased; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 99px; }

        /* ── SIDEBAR NAV LINK ── */
        .nav-link {
            display: flex; align-items: center; gap: 12px;
            padding: 10px 14px; border-radius: 12px;
            text-decoration: none; font-size: 13.5px; font-weight: 500;
            color: #64748B; transition: all .2s ease;
        }
        .nav-link:hover { background: #F8FAFC; color: #1E293B; }
        .nav-link.active { background: #EFF6FF; color: #1D4ED8; font-weight: 700; }
        .nav-link .nav-icon-wrap {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; transition: all .2s; flex-shrink: 0;
        }
        .nav-link:hover .nav-icon-wrap { background: #F1F5F9; }
        .nav-link.active .nav-icon-wrap { background: #1D4ED8; color: #fff; }

        /* ── LIVE DOT ── */
        @keyframes pulse-dot {
            0%, 100% { opacity: 1; transform: scale(1); }
            50%       { opacity: .5; transform: scale(.75); }
        }
        .live-dot { animation: pulse-dot 1.6s infinite; }

        /* ── STAT CARDS ── */
        .stat-card { position: relative; overflow: hidden; transition: all .25s ease; }
        .stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 3px 3px 0 0;
        }
        .stat-card.blue::before   { background: linear-gradient(90deg, #2563EB, #60A5FA); }
        .stat-card.green::before  { background: linear-gradient(90deg, #10B981, #34D399); }
        .stat-card.purple::before { background: linear-gradient(90deg, #8B5CF6, #A78BFA); }
        .stat-card.orange::before { background: linear-gradient(90deg, #F97316, #FB923C); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 20px rgba(0,0,0,.07); }

        /* ── MINI SPARKLINE ── */
        .sparkline { display: flex; align-items: flex-end; gap: 3px; height: 24px; }
        .spark-bar { width: 5px; border-radius: 3px 3px 0 0; transition: height .5s ease; }

        /* ── FORM INPUT FOCUS ── */
        .form-input:focus { outline: none; border-color: #2563EB; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }

        /* ── TABLE ROW ── */
        .tbl-row { transition: background .15s; }
        .tbl-row:hover { background: #F8FAFC; }

        /* ── PROGRESS BAR ── */
        .prog-bar { height: 4px; background: #E2E8F0; border-radius: 99px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 99px; background: linear-gradient(90deg, #2563EB, #60A5FA); transition: width .5s ease; }
        .prog-fill.urgent { background: linear-gradient(90deg, #EF4444, #F87171); }

        /* ── ACTIVITY TIMELINE ── */
        @keyframes fadeSlideIn { from { opacity: 0; transform: translateY(-6px); } to { opacity: 1; transform: translateY(0); } }
        .act-item { animation: fadeSlideIn .4s ease; }

        /* ── NOTIFICATION DRAWER ── */
        .drawer-slide { transform: translateX(100%); transition: transform .3s cubic-bezier(.4,0,.2,1); }
        .drawer-slide.open { transform: translateX(0); }

        /* ── TOAST ── */
        #toast-wrap { position: fixed; bottom: 22px; right: 22px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
        @keyframes toastIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        .toast-item { display: flex; align-items: center; gap: 10px; color: #fff; border-radius: 12px; padding: 11px 15px; font-size: 13px; font-weight: 500; box-shadow: 0 8px 24px rgba(0,0,0,.15); animation: toastIn .3s ease; min-width: 240px; }
        .toast-success { background: #10B981; }
        .toast-warning { background: #F59E0B; }
        .toast-info    { background: #2563EB; }

        /* ── BADGE PILL ── */
        @keyframes pulse-badge { 0%,100% { opacity:1 } 50% { opacity:.6 } }
        .badge-live { animation: pulse-badge 2s infinite; }

        /* ── HIGH CONTRAST MODE ── */
        .high-contrast-mode { background-color: #030712 !important; color: #F9FAFB !important; }
        .high-contrast-mode aside,
        .high-contrast-mode section,
        .high-contrast-mode header,
        .high-contrast-mode .bg-white { background-color: #111827 !important; border-color: #374151 !important; color: #F9FAFB !important; }
        .high-contrast-mode td, .high-contrast-mode th { color: #E5E7EB !important; border-color: #374151 !important; }
        .high-contrast-mode tr:hover { background-color: #1F2937 !important; }
    </style>

    <script>
        if (localStorage.getItem('high-contrast-enabled') === 'true') {
            document.documentElement.classList.add('high-contrast-mode');
        }
    </script>
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] min-h-screen flex overflow-x-hidden">

<!-- ════════════════════════════════════════
     SIDEBAR
════════════════════════════════════════ -->
<aside class="w-[260px] bg-white border-r border-[#E2E8F0] flex flex-col flex-shrink-0 sticky top-0 h-screen z-20">

    <!-- Logo -->
    <div class="h-[72px] flex items-center px-5 gap-3 border-b border-[#E2E8F0]">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-base flex-shrink-0"
             style="background: linear-gradient(135deg,#2563EB 0%,#1E40AF 100%); box-shadow: 0 4px 12px rgba(37,99,235,.35);">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span class="font-black text-[18px] text-[#0F172A] tracking-tight">ExamSystem</span>
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-1 pb-2">Main Menu</p>

        <a href="{{ route('teacher.dashboard') }}"
           class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
            <span class="nav-icon-wrap"><i class="fa-solid fa-house"></i></span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('teacher.question-bank') }}"
           class="nav-link {{ request()->routeIs('teacher.question-bank') ? 'active' : '' }}">
            <span class="nav-icon-wrap"><i class="fa-solid fa-database"></i></span>
            <span>Question Bank</span>
        </a>

        <a href="{{ route('teacher.monitoring.show') }}"
           class="nav-link {{ request()->routeIs('teacher.monitoring.show') ? 'active' : '' }}">
            <span class="nav-icon-wrap"><i class="fa-solid fa-display"></i></span>
            <span>Monitoring</span>
            @if(isset($activeExams) && count($activeExams) > 0)
                <span class="ml-auto text-[10px] font-bold bg-emerald-500 text-white rounded-full px-2 py-0.5">{{ count($activeExams) }}</span>
            @endif
        </a>

        <a href="{{ route('teacher.grading.queue') }}"
           class="nav-link {{ request()->routeIs('teacher.grading.*') ? 'active' : '' }}">
            <span class="nav-icon-wrap"><i class="fa-solid fa-pen-to-square"></i></span>
            <span>Grading</span>
            @if(($pendingGradingCount ?? 0) > 0)
            <span class="ml-auto text-[10px] font-bold bg-red-500 text-white rounded-full px-2 py-0.5">{{ $pendingGradingCount }}</span>
            @endif
        </a>

        <a href="{{ route('teacher.analytics') }}"
           class="nav-link {{ request()->routeIs('teacher.analytics') ? 'active' : '' }}">
            <span class="nav-icon-wrap"><i class="fa-solid fa-chart-line"></i></span>
            <span>Analytics</span>
        </a>

        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-4 pb-2">Account</p>

        <a href="{{ route('teacher.settings') }}"
           class="nav-link {{ request()->routeIs('teacher.settings') ? 'active' : '' }}">
            <span class="nav-icon-wrap"><i class="fa-solid fa-gear"></i></span>
            <span>Settings</span>
        </a>
    </nav>

    <!-- User -->
    <div class="p-3 border-t border-[#E2E8F0]">
        <a href="{{ route('teacher.settings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8FAFC] transition-colors cursor-pointer">
            <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-[#E2E8F0] flex-shrink-0">
                <img src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'Instructor') }}"
                     class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-[#0F172A] truncate">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                <p class="text-xs text-[#94A3B8] font-medium">Senior Faculty</p>
            </div>
            <i class="fa-solid fa-ellipsis-vertical text-[#94A3B8] text-sm"></i>
        </a>
    </div>
</aside>

<!-- ════════════════════════════════════════
     MAIN CONTENT
════════════════════════════════════════ -->
<div class="flex-1 flex flex-col min-w-0">

    <!-- ── HEADER ── -->
    <header class="h-[72px] bg-white border-b border-[#E2E8F0] flex items-center justify-between px-7 sticky top-0 z-10 flex-shrink-0">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-xl font-black text-[#0F172A] tracking-tight">
                    Good <span id="tod-greeting">Morning</span>, {{ Str::before(Auth::user()->full_name ?? 'Yun', ' ') }} 👋
                </h1>
            </div>
            <!-- Live sessions pill -->
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold text-emerald-700 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 live-dot"></span>
                <span id="live-count-label">{{ isset($activeExams) ? count($activeExams) : 0 }} Sessions Live</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Live Clock -->
            <div class="hidden md:block text-xs font-bold text-[#64748B] bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-2 rounded-lg font-mono tabular-nums" id="live-clock">06:41:24</div>

            <!-- Notification Bell -->
            <button onclick="toggleDrawer()" id="bell-btn"
                    class="relative w-9 h-9 flex items-center justify-center rounded-xl border border-[#E2E8F0] bg-white text-[#64748B] hover:bg-[#F8FAFC] hover:text-[#1E293B] transition-all">
                <i class="fa-regular fa-bell text-sm"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
            </button>

            <!-- Avatar -->
            <div class="flex items-center gap-2.5 pl-3 border-l border-[#E2E8F0] cursor-pointer hover:opacity-80 transition-opacity"
                 onclick="window.location.href='{{ route('teacher.settings') }}'">
                <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-[#E2E8F0]">
                    <img src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'Instructor') }}"
                         class="w-full h-full object-cover" alt="Avatar">
                </div>
                <span class="text-sm font-semibold text-[#475569] hidden sm:block">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</span>
                <i class="fa-solid fa-chevron-down text-[10px] text-[#94A3B8]"></i>
            </div>
        </div>
    </header>

    <!-- ── PAGE BODY ── -->
    <main class="flex-1 overflow-y-auto" id="dashboard-main-view">
        <div class="p-7 max-w-[1440px] mx-auto w-full space-y-6">

            <!-- ① SUCCESS BANNER -->
            @if(session('success'))
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-emerald-50 border border-emerald-200 rounded-2xl p-4 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500 flex items-center justify-center text-white flex-shrink-0">
                        <i class="fa-solid fa-check text-base"></i>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-[#0F172A]">Exam Session Deployed Successfully!</p>
                        <p class="text-xs text-emerald-700 mt-0.5">{{ session('success') }}</p>
                    </div>
                </div>
                @if(Str::contains(session('success'), ': '))
                <div class="flex items-center gap-2 bg-white border border-emerald-200 rounded-xl px-4 py-2 self-start sm:self-center">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Code:</span>
                    <span class="text-base font-black text-emerald-600 font-mono tracking-widest">{{ Str::afterLast(session('success'), ': ') }}</span>
                    <button onclick="copyText('{{ Str::afterLast(session('success'), ': ') }}')"
                            class="ml-1 text-slate-400 hover:text-slate-600 transition-colors p-1 rounded-md hover:bg-slate-50">
                        <i class="fa-regular fa-copy text-sm"></i>
                    </button>
                </div>
                @endif
            </div>
            @endif

            <!-- ② STAT CARDS ROW -->
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                <!-- Total Exams -->
                <div class="stat-card blue bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                            <i class="fa-regular fa-file-lines text-lg"></i>
                        </div>
                        @if(($examsThisWeek ?? 0) > 0)
                        <span class="text-[10px] font-bold bg-[#DCFCE7] text-[#15803D] px-2 py-0.5 rounded-full">+{{ $examsThisWeek }} this week</span>
                        @endif
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Total Exams</p>
                    <p class="text-4xl font-black text-[#0F172A] leading-none">{{ $totalExams ?? 0 }}</p>
                    @if(($totalExams ?? 0) > 0)
                    <p class="text-[11px] text-[#10B981] font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-arrow-trend-up text-[10px]"></i> {{ $examsThisWeek ?? 0 }} created this week
                    </p>
                    @else
                    <p class="text-[11px] text-[#94A3B8] font-semibold mt-2">No exams yet</p>
                    @endif
                    <!-- sparkline -->
                    <div class="sparkline mt-3" id="spark-exams"></div>
                </div>

                <!-- Active Sessions -->
                <div class="stat-card green bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#ECFDF5] flex items-center justify-center text-[#10B981]">
                            <i class="fa-solid fa-satellite-dish text-lg"></i>
                        </div>
                        <span class="badge-live text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] px-2 py-0.5 rounded-full flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] live-dot inline-block"></span> Live Now
                        </span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Active Sessions</p>
                    <p class="text-4xl font-black text-[#0F172A] leading-none" id="stat-active">{{ $activeSessionsCount ?? 0 }}</p>
                    <p class="text-[11px] text-[#64748B] font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-users text-[10px]"></i> <span id="stat-online">{{ $enrolledStudentsCount ?? 0 }}</span> students enrolled
                    </p>
                    <div class="sparkline mt-3" id="spark-sessions"></div>
                </div>

                <!-- Pending Grading -->
                <a href="{{ route('teacher.grading.queue') }}"
                   class="stat-card purple bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm block group">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#F5F3FF] flex items-center justify-center text-[#8B5CF6]">
                            <i class="fa-solid fa-pen-to-square text-lg"></i>
                        </div>
                        <span class="text-[10px] font-bold bg-[#FEE2E2] text-[#991B1B] px-2 py-0.5 rounded-full">Urgent</span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Pending Grading</p>
                    <div class="flex items-end justify-between">
                        <p class="text-4xl font-black text-[#0F172A] leading-none">{{ $pendingGradingCount ?? 0 }}</p>
                        <span class="text-[11px] font-bold text-[#2563EB] opacity-0 group-hover:opacity-100 transition-all translate-x-1 group-hover:translate-x-0 flex items-center gap-1">
                            Grade now <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </span>
                    </div>
                    <div class="mt-3 space-y-1">
                        <div class="flex justify-between text-[10px] font-semibold text-[#94A3B8]">
                            <span>Completion</span><span>{{ $gradingCompletionPercent ?? 0 }}%</span>
                        </div>
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $gradingCompletionPercent ?? 0 }}%;background:linear-gradient(90deg,#8B5CF6,#A78BFA);"></div></div>
                    </div>
                </a>

                <!-- Pass Rate -->
                <div class="stat-card orange bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FFF7ED] flex items-center justify-center text-[#F97316]">
                            <i class="fa-solid fa-chart-pie text-lg"></i>
                        </div>
                        <span class="text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] px-2 py-0.5 rounded-full">Avg Score</span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Pass Rate</p>
                    <p class="text-4xl font-black text-[#0F172A] leading-none">{{ $passRate ?? 0 }}<span class="text-2xl font-bold">%</span></p>
                    @if(($gradedCount ?? 0) > 0)
                    <p class="text-[11px] text-[#10B981] font-semibold mt-2 flex items-center gap-1">
                        <i class="fa-solid fa-chart-simple text-[10px]"></i> Based on {{ $gradedCount }} graded paper{{ $gradedCount == 1 ? '' : 's' }}
                    </p>
                    @else
                    <p class="text-[11px] text-[#94A3B8] font-semibold mt-2">No graded papers yet</p>
                    @endif
                    <div class="mt-3 space-y-1">
                        <div class="prog-bar"><div class="prog-fill" style="width:{{ $passRate ?? 0 }}%;background:linear-gradient(90deg,#F97316,#FB923C);"></div></div>
                    </div>
                </div>

            </div><!-- /stat cards -->

            <!-- ③ QUICK ACTIONS -->
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs font-bold text-[#94A3B8] mr-1">Quick:</span>
                <button onclick="document.getElementById('exam-title-input').focus();document.getElementById('deploy-card').scrollIntoView({behavior:'smooth'})"
                        class="inline-flex items-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold px-4 py-2 rounded-xl transition-all shadow-sm hover:shadow-blue-500/20 hover:shadow-md">
                    <i class="fa-solid fa-bolt"></i> Deploy Exam
                </button>
                <a href="{{ route('teacher.question-bank') }}"
                   class="inline-flex items-center gap-2 bg-white border border-[#E2E8F0] text-[#475569] hover:text-[#1E293B] hover:border-[#CBD5E1] text-xs font-semibold px-4 py-2 rounded-xl transition-all">
                    <i class="fa-solid fa-plus text-[#2563EB]"></i> Add Questions
                </a>
                <button onclick="toggleDrawer()"
                        class="inline-flex items-center gap-2 bg-white border border-[#E2E8F0] text-[#475569] hover:text-[#1E293B] hover:border-[#CBD5E1] text-xs font-semibold px-4 py-2 rounded-xl transition-all">
                    <i class="fa-regular fa-bell text-[#EF4444]"></i> Alerts
                    <span class="bg-red-500 text-white text-[9px] font-bold rounded-full w-4 h-4 flex items-center justify-center">3</span>
                </button>
                <a href="{{ route('teacher.monitoring.show') }}"
                   class="inline-flex items-center gap-2 bg-white border border-[#E2E8F0] text-[#475569] hover:text-[#1E293B] hover:border-[#CBD5E1] text-xs font-semibold px-4 py-2 rounded-xl transition-all">
                    <i class="fa-solid fa-display text-[#10B981]"></i> Live Monitor
                </a>
            </div>

            <!-- ④ DEPLOY EXAM CARD -->
            <div id="deploy-card" class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <!-- Card Header -->
                <div class="flex items-center gap-3 px-6 py-4 border-b border-[#E2E8F0] bg-[#FAFCFF]">
                    <div class="w-9 h-9 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                        <i class="fa-solid fa-rocket text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#0F172A]">Deploy New Examination Session</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Create a live exam and instantly generate a unique student access code.</p>
                    </div>
                </div>

                <!-- Form -->
                <form action="{{ route('exams.store') }}" method="POST" class="p-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 items-end">

                        <!-- Exam Title -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-regular fa-file-lines text-[#2563EB]"></i> Examination Title
                            </label>
                            <input type="text" id="exam-title-input" name="title"
                                   placeholder="e.g., DBMS Midterm Exam" required
                                   class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-3.5 py-2.5 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] transition-all">
                        </div>

                        <!-- Course -->
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center justify-between">
                                <span class="flex items-center gap-1.5"><i class="fa-solid fa-book-open text-[#2563EB]"></i> Course</span>
                                <a href="{{ route('teacher.courses.create') }}"
                                   class="text-[11px] font-bold text-[#2563EB] hover:text-[#1D4ED8] transition-colors flex items-center gap-1 normal-case tracking-normal">
                                    <i class="fa-solid fa-plus text-[9px]"></i> New Course
                                </a>
                            </label>
                            <div class="relative">
                                <select name="course_id" id="dashboard-course-dropdown-menu" required
                                        class="form-input w-full appearance-none bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-3.5 py-2.5 text-sm text-[#1E293B] font-medium pr-9 transition-all">
                                    @if(isset($courses) && count($courses) > 0)
                                        @foreach($courses as $courseItem)
                                            <option value="{{ $courseItem->id }}">{{ $courseItem->name }} ({{ $courseItem->code }})</option>
                                        @endforeach
                                    @else
                                        <option value="" disabled selected>Create a course first</option>
                                    @endif
                                </select>
                                <i class="fa-solid fa-chevron-down text-[10px] text-[#94A3B8] absolute right-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Duration + Pass Mark -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                    <i class="fa-regular fa-clock text-[#2563EB]"></i> Duration
                                </label>
                                <input type="number" name="duration" placeholder="60" min="1" required
                                       class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-3 py-2.5 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] transition-all">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                    <i class="fa-solid fa-percent text-[#2563EB]"></i> Pass Mark
                                </label>
                                <input type="number" name="pass_mark" placeholder="50" min="0" max="100" required
                                       class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-3 py-2.5 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] transition-all">
                            </div>
                        </div>

                        <!-- Submit -->
                        <div>
                            <button type="submit"
                                    @if(!isset($courses) || count($courses) == 0) disabled @endif
                                    class="w-full flex items-center justify-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] active:scale-[.98] text-white text-sm font-bold py-[11px] px-4 rounded-xl transition-all shadow-md shadow-blue-500/15 disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-[#2563EB]">
                                <i class="fa-solid fa-bolt"></i> Generate Access Token
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Courses strip -->
                <div class="mx-6 mb-5 p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl">
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-3">Active Courses</p>
                    <div class="flex flex-wrap gap-2" id="active-courses-container-box">
                        @if(isset($courses) && count($courses) > 0)
                            @foreach($courses as $courseItem)
                            <div class="flex items-center gap-2 bg-white border border-[#E2E8F0] rounded-xl px-3 py-1.5 text-xs shadow-sm">
                                <div class="w-2 h-2 rounded-full bg-[#2563EB]"></div>
                                <span class="font-bold text-[#1E293B]">{{ $courseItem->name }}</span>
                                <span class="text-[10px] text-[#94A3B8] font-mono">({{ $courseItem->code }})</span>
                                <form action="{{ route('teacher.courses.destroy', $courseItem->id) }}" method="POST"
                                      onsubmit="return confirm('Remove this course?');" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="text-[#CBD5E1] hover:text-red-400 transition-colors pl-1 border-l border-[#E2E8F0] ml-1">
                                        <i class="fa-solid fa-xmark text-[10px]"></i>
                                    </button>
                                </form>
                            </div>
                            @endforeach
                        @else
                            <div class="w-full flex items-center justify-between gap-3 py-1">
                                <p class="text-xs text-[#94A3B8]">You haven't created any courses yet.</p>
                                <a href="{{ route('teacher.courses.create') }}"
                                   class="text-[11px] font-bold text-[#2563EB] hover:text-[#1D4ED8] flex items-center gap-1 whitespace-nowrap">
                                    <i class="fa-solid fa-plus text-[10px]"></i> Create your first course
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- ⑤ BOTTOM GRID: Sessions Table + Activity Feed -->
            <!-- Fixed: Completely bound container visibility state parameters to block mockup display errors during customization tracks[cite: 3] -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 {{ isset($activeExams) && count($activeExams) > 0 ? '' : 'hidden' }}" id="bottom-layout-data-grid">

                <!-- ACTIVE SESSIONS TABLE (2/3 width) -->
                <div class="lg:col-span-2 bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="px-6 py-4 border-b border-[#E2E8F0] flex items-center justify-between bg-[#FAFCFF]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#ECFDF5] flex items-center justify-center text-[#10B981]">
                                <i class="fa-solid fa-tower-broadcast text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#0F172A]">Active Exam Sessions</h3>
                                <p class="text-[11px] text-[#64748B] mt-0.5">Real-time supervision tokens</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="hidden sm:flex items-center gap-1.5 text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 live-dot"></span> Auto-refreshing
                            </div>
                            <a href="{{ route('teacher.monitoring.show') }}"
                               class="text-xs font-bold text-[#2563EB] hover:text-[#1D4ED8] transition-colors flex items-center gap-1">
                                View All <i class="fa-solid fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto flex-1">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-[#FAFCFF] border-b border-[#E2E8F0]">
                                    <th class="px-5 py-3 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Exam</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Token</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Progress</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Status</th>
                                    <th class="px-5 py-3 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[#F1F5F9]" id="sessions-tbody">
                                @if(isset($activeExams) && count($activeExams) > 0)
                                    @foreach($activeExams as $activeSession)
                                    <tr class="tbl-row">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-[#1E293B] text-sm">{{ $activeSession->title }}</p>
                                            <p class="text-[11px] text-[#94A3B8] mt-0.5">{{ $activeSession->course->name ?? 'General' }}</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="inline-flex items-center gap-1.5 bg-[#F1F5F9] border border-[#E2E8F0] rounded-lg px-2.5 py-1 font-mono text-[11px] font-bold text-[#1E293B]">
                                                {{ $activeSession->access_code }}
                                                <button onclick="copyText('{{ $activeSession->access_code }}')"
                                                        class="text-[#94A3B8] hover:text-[#2563EB] transition-colors">
                                                    <i class="fa-regular fa-copy text-[10px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="prog-bar w-20"><div class="prog-fill" style="width:60%;"></div></div>
                                                <span class="text-[11px] font-bold text-[#64748B] font-mono">{{ $activeSession->duration }}m</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] px-2.5 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] live-dot"></span> Live
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <a href="{{ route('teacher.monitoring.show') }}"
                                               class="inline-flex items-center gap-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                                                <i class="fa-solid fa-eye"></i> Monitor
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                @else
                                    <!-- Placeholder mockup row nodes[cite: 3] -->
                                    <tr class="tbl-row placeholder-row-node" id="demo-row-1">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-[#1E293B] text-sm">Database Systems Midterm</p>
                                            <p class="text-[11px] text-[#94A3B8] mt-0.5">Database (DAT-464)</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="inline-flex items-center gap-1.5 bg-[#F1F5F9] border border-[#E2E8F0] rounded-lg px-2.5 py-1 font-mono text-[11px] font-bold text-[#1E293B]">
                                                DB-9901
                                                <button onclick="copyText('DB-9901')" class="text-[#94A3B8] hover:text-[#2563EB] transition-colors">
                                                    <i class="fa-regular fa-copy text-[10px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="prog-bar w-20"><div class="prog-fill" style="width:61%;"></div></div>
                                                <span class="text-[11px] font-bold text-[#64748B] font-mono countdown" data-total="90" data-elapsed="55">35m</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] px-2.5 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] live-dot"></span> Live
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <a href="{{ route('teacher.monitoring.show') }}"
                                               class="inline-flex items-center gap-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                                                <i class="fa-solid fa-eye"></i> Monitor
                                            </a>
                                        </td>
                                    </tr>
                                    <tr class="tbl-row placeholder-row-node" id="demo-row-2">
                                        <td class="px-5 py-4">
                                            <p class="font-bold text-[#1E293B] text-sm">Physics 101 Final Exam</p>
                                            <p class="text-[11px] text-[#94A3B8] mt-0.5">Physics (PHY-454)</p>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="inline-flex items-center gap-1.5 bg-[#F1F5F9] border border-[#E2E8F0] rounded-lg px-2.5 py-1 font-mono text-[11px] font-bold text-[#1E293B]">
                                                PHY-7731
                                                <button onclick="copyText('PHY-7731')" class="text-[#94A3B8] hover:text-[#2563EB] transition-colors">
                                                    <i class="fa-regular fa-copy text-[10px]"></i>
                                                </button>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <div class="flex items-center gap-2">
                                                <div class="prog-bar w-20"><div class="prog-fill" style="width:25%;"></div></div>
                                                <span class="text-[11px] font-bold text-[#64748B] font-mono countdown" data-total="120" data-elapsed="30">90m</span>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4">
                                            <span class="inline-flex items-center gap-1.5 text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] px-2.5 py-1 rounded-full">
                                                <span class="w-1.5 h-1.5 rounded-full bg-[#F59E0B] live-dot"></span> Live
                                            </span>
                                        </td>
                                        <td class="px-5 py-4 text-center">
                                            <a href="{{ route('teacher.monitoring.show') }}"
                                               class="inline-flex items-center gap-1.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-[11px] font-bold px-3 py-1.5 rounded-lg transition-colors shadow-sm">
                                                <i class="fa-solid fa-eye"></i> Monitor
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <div class="px-5 py-3 bg-[#FAFCFF] border-t border-[#E2E8F0] flex items-center justify-between">
                        <span class="text-[11px] text-[#94A3B8] font-medium">Updated: <span id="last-updated">just now</span></span>
                        <span class="text-[11px] text-[#94A3B8]">Auto-refreshes every 30s</span>
                    </div>
                </div>

                <!-- RECENT ACTIVITY FEED (1/3 width) -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden flex flex-col">
                    <div class="px-5 py-4 border-b border-[#E2E8F0] bg-[#FAFCFF] flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#F5F3FF] flex items-center justify-center text-[#8B5CF6]">
                                <i class="fa-solid fa-clock-rotate-left text-sm"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-[#0F172A]">Recent Activity</h3>
                                <p class="text-[11px] text-[#64748B]">Live student actions</p>
                            </div>
                        </div>
                        <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 live-dot"></span> Live
                        </span>
                    </div>

                    <div class="flex-1 overflow-y-auto max-h-72" id="activity-feed">
                        <!-- Activity items injected here[cite: 3] -->
                        <div class="placeholder-activity-node px-5 py-3.5 flex gap-3 border-b border-[#F1F5F9] act-item" id="act-item-calculus">
                            <div class="w-8 h-8 rounded-full bg-[#ECFDF5] flex items-center justify-center text-[#10B981] flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#1E293B]"><span class="font-bold">Sarah Jenkins</span> submitted Calculus Midterm</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">2 min ago</p>
                                <span class="inline-block text-[10px] font-bold text-[#10B981] bg-[#ECFDF5] px-2 py-0.5 rounded-full mt-1">87/100</span>
                            </div>
                        </div>
                        <div class="placeholder-activity-node px-5 py-3.5 flex gap-3 border-b border-[#F1F5F9] act-item" id="act-item-database">
                            <div class="w-8 h-8 rounded-full bg-[#FEF2F2] flex items-center justify-center text-[#EF4444] flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#1E293B]"><span class="font-bold">Marcus Reid</span> flagged for tab switching</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">5 min ago</p>
                                <span class="inline-block text-[10px] font-bold text-[#EF4444] bg-[#FEF2F2] px-2 py-0.5 rounded-full mt-1">×3 attempts</span>
                            </div>
                        </div>
                        <div class="placeholder-activity-node px-5 py-3.5 flex gap-3 border-b border-[#F1F5F9] act-item" id="act-item-physics">
                            <div class="w-8 h-8 rounded-full bg-[#ECFDF5] flex items-center justify-center text-[#10B981] flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-check text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#1E293B]"><span class="font-bold">Anya Patel</span> submitted Physics Final</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">8 min ago</p>
                                <span class="inline-block text-[10px] font-bold text-[#10B981] bg-[#ECFDF5] px-2 py-0.5 rounded-full mt-1">92/100</span>
                            </div>
                        </div>
                        <div class="px-5 py-3.5 flex gap-3 border-b border-[#F1F5F9] act-item" id="general-exam-feed-item">
                            <div class="w-8 h-8 rounded-full bg-[#EFF6FF] flex items-center justify-center text-[#2563EB] flex-shrink-0 mt-0.5">
                                <i class="fa-solid fa-circle-play text-sm"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs font-semibold text-[#1E293B]"><span class="font-bold">David Miller</span> started General Exam Track</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">15 min ago</p>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 border-t border-[#E2E8F0]">
                        <a href="{{ route('teacher.monitoring.show') }}"
                           class="flex items-center justify-center gap-2 w-full py-2.5 bg-[#F8FAFC] hover:bg-[#F1F5F9] border border-[#E2E8F0] text-[#475569] text-xs font-bold rounded-xl transition-colors">
                            View Full Activity Log <i class="fa-solid fa-arrow-right text-[10px]"></i>
                        </a>
                    </div>
                </div>

            </div><!-- /bottom grid -->

            <!-- Clean Empty Template fallback box injected via JS when no courses remain -->
            <div class="flex flex-col items-center justify-center p-12 bg-white border border-[#E2E8F0] rounded-2xl shadow-sm text-center {{ isset($courses) && count($courses) > 0 && (!isset($activeExams) || count($activeExams) == 0) ? 'flex' : 'hidden' }}" id="empty-state-placeholder-card">
                <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center text-slate-400 mb-3">
                    <i class="fa-solid fa-inbox text-lg"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-800">No Active Sessions Running</h4>
                <p class="text-xs text-slate-400 mt-1 max-w-xs">Deploy a live exam session using the control panel above to begin dashboard data tracking.</p>
            </div>

        </div><!-- /page body inner -->
    </main>
</div>

<!-- ════════════════════════════════════════
     NOTIFICATION DRAWER
════════════════════════════════════════ -->
<div id="drawer-overlay" class="fixed inset-0 z-40 bg-slate-950/40 backdrop-blur-sm hidden" onclick="toggleDrawer()"></div>

<div id="drawer-panel"
     class="drawer-slide fixed inset-y-0 right-0 z-50 w-full max-w-sm bg-white shadow-2xl border-l border-[#E2E8F0] flex flex-col">

    <!-- Drawer Header -->
    <div class="flex items-center justify-between px-5 py-4 border-b border-[#E2E8F0] bg-[#FAFCFF]">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                <i class="fa-solid fa-bell text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-bold text-[#0F172A]">Live Notifications</h2>
                <p class="text-[10px] text-[#94A3B8]">3 new alerts</p>
            </div>
        </div>
        <button onclick="toggleDrawer()" class="w-8 h-8 flex items-center justify-center rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] transition-colors">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Drawer Body -->
    <div class="flex-1 overflow-y-auto p-4 space-y-3">

        <div class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold bg-[#FEE2E2] text-[#991B1B] px-2 py-0.5 rounded-md uppercase tracking-wider">🔴 High Alert</span>
                <span class="text-[10px] text-[#EF4444] font-medium">Just Now</span>
            </div>
            <p class="text-xs font-semibold text-[#7F1D1D] leading-relaxed">Suspicious tab navigation detected on candidate J. Doe — Database Systems Midterm.</p>
            <button class="text-[11px] font-bold text-[#EF4444] hover:underline">View Student →</button>
        </div>

        <div class="p-4 bg-[#FFFBEB] border border-[#FDE68A] rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold bg-[#FEF3C7] text-[#92400E] px-2 py-0.5 rounded-md uppercase tracking-wider">⚠️ Warning</span>
                <span class="text-[10px] text-[#F59E0B] font-medium">4 min ago</span>
            </div>
            <p class="text-xs font-semibold text-[#78350F] leading-relaxed">Marcus Reid switched tabs 3 times during Physics 101 Final Exam.</p>
            <button class="text-[11px] font-bold text-[#D97706] hover:underline">Send Warning →</button>
        </div>

        <div class="p-4 bg-[#EFF6FF] border border-[#BFDBFE] rounded-2xl space-y-2">
            <div class="flex items-center justify-between">
                <span class="text-[10px] font-extrabold bg-[#DBEAFE] text-[#1E40AF] px-2 py-0.5 rounded-md uppercase tracking-wider">ℹ️ Info</span>
                <span class="text-[10px] text-[#2563EB] font-medium">12 min ago</span>
            </div>
            <p class="text-xs font-semibold text-[#1E3A8A] leading-relaxed">Kevin Adams reconnected to Calculus Problem Set after a disconnection.</p>
        </div>

    </div>

    <!-- Drawer Footer -->
    <div class="p-4 border-t border-[#E2E8F0]">
        <a href="{{ route('teacher.monitoring.show') }}"
           class="flex items-center justify-center gap-2 w-full py-2.5 bg-[#2563EB] hover:bg-[#1D4ED8] text-white text-xs font-bold rounded-xl transition-colors shadow-md shadow-blue-500/15">
            <i class="fa-solid fa-display"></i> Open Live Monitor
        </a>
    </div>
</div>

<!-- TOAST CONTAINER -->
<div id="toast-wrap"></div>

<!-- ════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════ -->
<script>
// ── CLEAR ANY LEFTOVER DEMO ROWS ──────────
// If there are no active exam sessions, the demo/placeholder rows inside
// the (already CSS-hidden) sessions table and activity feed are removed
// outright so they can never accidentally be shown or counted as real data.
document.addEventListener('DOMContentLoaded', () => {
    const hasLiveSessions = @json(isset($activeExams) && count($activeExams) > 0);

    if (!hasLiveSessions) {
        document.querySelectorAll('.placeholder-row-node').forEach(el => el.remove());
        document.querySelectorAll('.placeholder-activity-node').forEach(el => el.remove());
    }
});

// ── CLOCK ────────────────────────────────
function updateClock() {
    const now = new Date();
    const el = document.getElementById('live-clock');
    if (el) el.textContent = now.toLocaleTimeString('en-US', { hour12: false });
    const h = now.getHours();
    const g = document.getElementById('tod-greeting');
    if (g) g.textContent = h < 12 ? 'Morning' : h < 17 ? 'Afternoon' : 'Evening';
}
updateClock();
setInterval(updateClock, 1000);

// ── SPARKLINES ───────────────────────────
function buildSparkline(id, heights, color) {
    const el = document.getElementById(id);
    if (!el) return;
    el.innerHTML = heights.map((h, i) => {
        const isLast = i === heights.length - 1;
        return `<div class="spark-bar" style="height:${h}px;background:${isLast ? color : color + '44'};"></div>`;
    }).join('');
}
buildSparkline('spark-exams',    [7,11,8,14,9,17,13,19,15,23,17,28], '#2563EB');
buildSparkline('spark-sessions', [3,2,5,3,7,4,6,8,5,8,7,10],          '#10B981');

// ── NOTIFICATION DRAWER ───────────────────
const overlay = document.getElementById('drawer-overlay');
const panel   = document.getElementById('drawer-panel');

function toggleDrawer() {
    const isOpen = panel.classList.contains('open');
    if (isOpen) {
        panel.classList.remove('open');
        overlay.classList.add('hidden');
    } else {
        overlay.classList.remove('hidden');
        requestAnimationFrame(() => panel.classList.add('open'));
    }
}

// ── COPY TO CLIPBOARD ─────────────────────
function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        showToast('Copied: ' + text, 'success');
    }).catch(() => {
        showToast('Code: ' + text, 'info');
    });
}

// ── TOAST ────────────────────────────────
function showToast(msg, type = 'info') {
    const icons = { success: 'fa-circle-check', info: 'fa-circle-info', warning: 'fa-triangle-exclamation' };
    const colors = { success: '#10B981', info: '#2563EB', warning: '#F59E0B' };
    const wrap = document.getElementById('toast-wrap');
    const t = document.createElement('div');
    t.className = 'toast-item';
    t.style.background = colors[type] || colors.info;
    t.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i>${msg}`;
    wrap.appendChild(t);
    setTimeout(() => {
        t.style.transition = 'all .3s';
        t.style.opacity = '0';
        t.style.transform = 'translateY(8px)';
        setTimeout(() => t.remove(), 300);
    }, 3000);
}

// ── LIVE STUDENT COUNTER PULSE ────────────
setInterval(() => {
    const el = document.getElementById('stat-online');
    if (el && el.textContent !== '0') {
        const v = parseInt(el.textContent);
        el.textContent = Math.max(55, v + Math.floor(Math.random() * 5 - 2));
    }
}, 6000);

// ── LIVE ACTIVITY INJECT ──────────────────
const newActivities = [
    { icon: 'fa-circle-check', bg: '#ECFDF5', color: '#10B981', name: 'Emma Thompson', action: 'submitted Physics Final', time: 'just now', badge: '88/100', badgeCls: '#10B981', badgeBg: '#ECFDF5' },
    { icon: 'fa-triangle-exclamation', bg: '#FEF2F2', color: '#EF4444', name: 'Ray Kim', action: 'copy-paste attempt detected', time: 'just now', badge: '×2', badgeCls: '#EF4444', badgeBg: '#FEF2F2' },
    { icon: 'fa-circle-play', bg: '#EFF6FF', color: '#2563EB', name: 'Nadia J.', action: 'started Database Midterm', time: 'just now', badge: null },
];
let actIdx = 0;
setInterval(() => {
    const feed = document.getElementById('activity-feed');
    const currentActiveCount = document.getElementById('stat-active').textContent;
    if (!feed || currentActiveCount === '0') return;
    
    const a = newActivities[actIdx++ % newActivities.length];
    const item = document.createElement('div');
    item.className = 'px-5 py-3.5 flex gap-3 border-b border-[#F1F5F9] act-item';
    item.innerHTML = `
        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5" style="background:${a.bg};color:${a.color};">
            <i class="fa-solid ${a.icon} text-sm"></i>
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-xs font-semibold text-[#1E293B]"><span class="font-bold">${a.name}</span> ${a.action}</p>
            <p class="text-[10px] text-[#94A3B8] mt-0.5">${a.time}</p>
            ${a.badge ? `<span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full mt-1" style="color:${a.badgeCls};background:${a.badgeBg};">${a.badge}</span>` : ''}
        </div>`;
    feed.insertBefore(item, feed.firstChild);
    const items = feed.querySelectorAll('.act-item');
    if (items.length > 8) items[items.length - 1].remove();
}, 12000);

// ── COUNTDOWN TIMERS ──────────────────────
function tickCountdowns() {
    document.querySelectorAll('.countdown').forEach(el => {
        let elapsed = parseInt(el.dataset.elapsed || 0);
        const total = parseInt(el.dataset.total || 60);
        elapsed = Math.min(total, elapsed + 1/60);
        el.dataset.elapsed = elapsed;
        const remaining = Math.max(0, total - elapsed);
        const h = Math.floor(remaining / 60);
        const m = Math.round(remaining % 60);
        el.textContent = h > 0 ? `${h}h ${m}m` : `${m}m`;
        if (remaining <= 10) el.style.color = '#EF4444';
    });
}
setInterval(tickCountdowns, 1000);

// ── LAST UPDATED TIMER ────────────────────
let secondsAgo = 0;
setInterval(() => {
    secondsAgo++;
    const el = document.getElementById('last-updated');
    if (!el) return;
    if (secondsAgo < 60) el.textContent = secondsAgo + 's ago';
    else el.textContent = Math.floor(secondsAgo / 60) + 'm ago';
}, 1000);
</script>

</body>
</html>