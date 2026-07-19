<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem – Grading Queue</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        [x-cloak] { display: none !important; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 99px; }

        /* ── SIDEBAR NAV ── */
        .nav-link { display:flex; align-items:center; gap:11px; padding:9px 12px; border-radius:12px; text-decoration:none; font-size:13.5px; font-weight:500; color:#64748B; transition:all .2s; }
        .nav-link:hover { background:#F8FAFC; color:#1E293B; }
        .nav-link.active { background:#EFF6FF; color:#1D4ED8; font-weight:700; }
        .nav-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; transition:all .2s; }
        .nav-link:hover .nav-icon { background:#F1F5F9; }
        .nav-link.active .nav-icon { background:#1D4ED8; color:#fff; }

        /* ── STAT CARD ── */
        .stat-card { position:relative; overflow:hidden; transition:all .25s; }
        .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:3px 3px 0 0; }
        .stat-card.c-blue::before   { background:linear-gradient(90deg,#2563EB,#60A5FA); }
        .stat-card.c-amber::before  { background:linear-gradient(90deg,#F59E0B,#FCD34D); }
        .stat-card.c-green::before  { background:linear-gradient(90deg,#10B981,#34D399); }
        .stat-card.c-purple::before { background:linear-gradient(90deg,#8B5CF6,#A78BFA); }
        .stat-card:hover { transform:translateY(-2px); box-shadow:0 10px 24px rgba(0,0,0,.07); }

        /* ── SUBMISSION CARD ── */
        .sub-card { transition:all .2s; }
        .sub-card:hover { transform:translateY(-2px); box-shadow:0 10px 28px rgba(0,0,0,.07); }

        /* ── FILTER TAB ── */
        .ftab { display:flex; align-items:center; gap:2; padding:8px 16px; border-radius:12px; font-size:12px; font-weight:700; cursor:pointer; transition:all .2s; border:1.5px solid transparent; font-family:inherit; }
        .ftab.all     { background:#0F172A; color:#fff; border-color:#0F172A; }
        .ftab.pending { background:#FEF3C7; color:#92400E; border-color:#FDE68A; }
        .ftab.graded  { background:#D1FAE5; color:#065F46; border-color:#A7F3D0; }
        .ftab.inactive { background:#fff; color:#64748B; border-color:#E2E8F0; }
        .ftab.inactive:hover { background:#F8FAFC; color:#1E293B; }

        /* ── AVATAR INITIALS ── */
        .avatar-ring { width:40px; height:40px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:900; border:2px solid #fff; box-shadow:0 2px 8px rgba(0,0,0,.1); }

        /* ── SCORE BAR ── */
        @keyframes barFill { from{width:0} }
        .bar-fill { animation:barFill .8s ease forwards; }

        /* ── LIVE DOT ── */
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
        .ldot { animation:pulse-dot 1.6s infinite; }

        /* ── FADE UP ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .fu { animation:fadeUp .35s ease both; }
        @keyframes cardIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        [data-card] { animation:cardIn .3s ease both; }

        /* ── TOAST ── */
        #toast-box { position:fixed; bottom:22px; right:22px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
        @keyframes toastIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .toast { display:flex; align-items:center; gap:10px; color:#fff; border-radius:12px; padding:11px 16px; font-size:13px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.15); animation:toastIn .3s ease; min-width:220px; font-family:'Inter',sans-serif; pointer-events:auto; }
        .toast.info { background:#2563EB; }
        .toast.success { background:#10B981; }
    </style>
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] min-h-screen flex overflow-x-hidden"
      x-data="queueApp()">

<!-- ══════════════ SIDEBAR ══════════════ -->
<aside class="w-[260px] bg-white border-r border-[#E2E8F0] flex flex-col flex-shrink-0 sticky top-0 h-screen z-20">
    <a href="{{ route('teacher.dashboard') }}"
       class="h-[72px] flex items-center px-5 gap-3 border-b border-[#E2E8F0] hover:opacity-90 transition-opacity">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white flex-shrink-0"
             style="background:linear-gradient(135deg,#2563EB,#1E40AF);box-shadow:0 4px 12px rgba(37,99,235,.35);">
            <i class="fa-solid fa-graduation-cap text-base"></i>
        </div>
        <span class="font-black text-[18px] text-[#0F172A] tracking-tight">ExamSystem</span>
    </a>

    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-1 pb-2">Main Menu</p>
        <a href="{{ route('teacher.dashboard') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span><span>Dashboard</span>
        </a>
        <a href="{{ route('teacher.question-bank') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-database"></i></span><span>Question Bank</span>
        </a>
        <a href="{{ route('teacher.monitoring.show') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-display"></i></span><span>Monitoring</span>
        </a>
        <a href="#" class="nav-link active">
            <span class="nav-icon"><i class="fa-solid fa-pen-to-square"></i></span>
            <span>Grading</span>
            <span class="ml-auto text-[10px] font-bold bg-red-500 text-white rounded-full px-2 py-0.5" x-text="pendingCount"></span>
        </a>
        <a href="{{ route('teacher.analytics') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span><span>Analytics</span>
        </a>
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-4 pb-2">Account</p>
        <a href="{{ route('teacher.settings') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span><span>Settings</span>
        </a>
    </nav>

    <div class="p-3 border-t border-[#E2E8F0]">
        <a href="{{ route('teacher.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8FAFC] transition-colors">
            <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-[#E2E8F0] flex-shrink-0">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ Auth::user()->full_name ?? 'Instructor' }}" class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-[#0F172A] truncate">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                <p class="text-xs text-[#94A3B8] font-medium">Senior Faculty</p>
            </div>
            <i class="fa-solid fa-ellipsis-vertical text-[#94A3B8] text-sm"></i>
        </a>
    </div>
</aside>

<!-- ══════════════ MAIN ══════════════ -->
<div class="flex-1 flex flex-col min-w-0">

    <!-- HEADER -->
    <header class="h-[72px] bg-white border-b border-[#E2E8F0] flex items-center justify-between px-7 sticky top-0 z-10 flex-shrink-0">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-xl font-black text-[#0F172A] tracking-tight">Grading Queue</h1>
                <p class="text-[11px] text-[#94A3B8] font-medium mt-0.5">Student submissions awaiting your review</p>
            </div>
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-full">
                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 ldot"></span>
                <span x-text="pendingCount + ' pending'"></span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-xs font-bold text-[#64748B] bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-2 rounded-lg font-mono tabular-nums" id="live-clock">--:--:--</div>
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8] text-xs pointer-events-none"></i>
                <input type="text" x-model="searchQuery"
                       placeholder="Search student or exam…"
                       class="w-60 pl-9 pr-4 py-2.5 border border-[#E2E8F0] bg-[#F8FAFC] rounded-xl text-xs font-medium focus:outline-none focus:border-[#2563EB] focus:bg-white transition-all">
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto">
    <div class="p-7 max-w-[1440px] mx-auto w-full space-y-5">

        <!-- ① STAT CARDS -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="stat-card c-blue bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fu" style="animation-delay:.05s">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                        <i class="fa-solid fa-inbox text-base"></i>
                    </div>
                    <span class="text-[10px] font-bold bg-[#DBEAFE] text-[#1E40AF] px-2 py-0.5 rounded-full">Total</span>
                </div>
                <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Total Submissions</p>
                <p class="text-3xl font-black text-[#0F172A] leading-none tabular-nums" x-text="submissions.length"></p>
                <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bar-fill" style="width:100%;background:linear-gradient(90deg,#2563EB,#60A5FA);"></div>
                </div>
            </div>

            <div class="stat-card c-amber bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fu" style="animation-delay:.1s">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#FEF3C7] flex items-center justify-center text-[#F59E0B]">
                        <i class="fa-solid fa-clock-rotate-left text-base"></i>
                    </div>
                    <span class="text-[10px] font-bold bg-[#FEF3C7] text-[#92400E] px-2 py-0.5 rounded-full">Pending</span>
                </div>
                <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Awaiting Review</p>
                <p class="text-3xl font-black text-[#F59E0B] leading-none tabular-nums" x-text="pendingCount"></p>
                <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bar-fill"
                         :style="`width:${submissions.length ? Math.round(pendingCount/submissions.length*100) : 0}%;background:linear-gradient(90deg,#F59E0B,#FCD34D);`"></div>
                </div>
            </div>

            <div class="stat-card c-green bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fu" style="animation-delay:.15s">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#ECFDF5] flex items-center justify-center text-[#10B981]">
                        <i class="fa-solid fa-circle-check text-base"></i>
                    </div>
                    <span class="text-[10px] font-bold bg-[#D1FAE5] text-[#065F46] px-2 py-0.5 rounded-full">Graded</span>
                </div>
                <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Graded Papers</p>
                <p class="text-3xl font-black text-[#10B981] leading-none tabular-nums" x-text="gradedCount"></p>
                <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bar-fill"
                         :style="`width:${submissions.length ? Math.round(gradedCount/submissions.length*100) : 0}%;background:linear-gradient(90deg,#10B981,#34D399);`"></div>
                </div>
            </div>

            <div class="stat-card c-purple bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fu" style="animation-delay:.2s">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl bg-[#F5F3FF] flex items-center justify-center text-[#8B5CF6]">
                        <i class="fa-solid fa-chart-simple text-base"></i>
                    </div>
                    <span class="text-[10px] font-bold bg-[#EDE9FE] text-[#6D28D9] px-2 py-0.5 rounded-full">Avg</span>
                </div>
                <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Avg Score</p>
                <p class="text-3xl font-black text-[#8B5CF6] leading-none tabular-nums" x-text="avgScore + ' / 40'"></p>
                <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bar-fill"
                         :style="`width:${Math.round(avgScore/40*100)}%;background:linear-gradient(90deg,#8B5CF6,#A78BFA);`"></div>
                </div>
            </div>
        </div>

        <!-- ② FILTER TABS -->
        <div class="flex items-center gap-2 flex-wrap fu" style="animation-delay:.22s">
            <button @click="statusFilter='All'"
                    :class="statusFilter==='All' ? 'ftab all' : 'ftab inactive'">
                <i class="fa-solid fa-layer-group text-xs mr-1.5"></i>
                All Submissions
                <span class="ml-2 text-[10px] font-black px-1.5 py-0.5 rounded-full"
                      :class="statusFilter==='All' ? 'bg-white/20 text-white' : 'bg-[#F1F5F9] text-[#64748B]'"
                      x-text="submissions.length"></span>
            </button>
            <button @click="statusFilter='pending_grading'"
                    :class="statusFilter==='pending_grading' ? 'ftab pending' : 'ftab inactive'">
                <i class="fa-solid fa-hourglass-half text-xs mr-1.5"></i>
                Needs Grading
                <span class="ml-2 text-[10px] font-black px-1.5 py-0.5 rounded-full"
                      :class="statusFilter==='pending_grading' ? 'bg-amber-200 text-amber-800' : 'bg-[#F1F5F9] text-[#64748B]'"
                      x-text="pendingCount"></span>
            </button>
            <button @click="statusFilter='graded'"
                    :class="statusFilter==='graded' ? 'ftab graded' : 'ftab inactive'">
                <i class="fa-solid fa-circle-check text-xs mr-1.5"></i>
                Graded Papers
                <span class="ml-2 text-[10px] font-black px-1.5 py-0.5 rounded-full"
                      :class="statusFilter==='graded' ? 'bg-emerald-200 text-emerald-800' : 'bg-[#F1F5F9] text-[#64748B]'"
                      x-text="gradedCount"></span>
            </button>

            <!-- Results shown pill -->
            <div class="ml-auto flex items-center gap-1.5 text-[11px] font-semibold text-[#64748B] bg-white border border-[#E2E8F0] px-3 py-2 rounded-xl">
                <i class="fa-solid fa-filter text-[#94A3B8] text-xs"></i>
                <span x-text="filteredSubmissions.length"></span> shown
            </div>
        </div>

        <!-- ③ SUBMISSION CARDS GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
            <template x-for="(sub, idx) in filteredSubmissions" :key="sub.id">
                <div class="sub-card bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden"
                     :style="`animation-delay:${idx * 0.05}s`" data-card>

                    <!-- Card top accent -->
                    <div class="h-1 w-full"
                         :class="sub.status === 'graded' ? 'bg-gradient-to-r from-emerald-400 to-teal-400' : 'bg-gradient-to-r from-amber-400 to-orange-400'">
                    </div>

                    <div class="p-5">
                        <!-- Header row -->
                        <div class="flex items-start justify-between gap-2 mb-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Avatar initials -->
                                <div class="avatar-ring flex-shrink-0"
                                     :style="`background:${avatarColor(sub.student_name)};color:#fff;`"
                                     x-text="initials(sub.student_name)">
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-[#0F172A] truncate" x-text="sub.student_name"></p>
                                    <p class="text-[10px] font-bold text-[#94A3B8] font-mono mt-0.5" x-text="sub.institutional_id"></p>
                                </div>
                            </div>
                            <!-- Status chip -->
                            <span class="flex-shrink-0 flex items-center gap-1.5 text-[10px] font-black uppercase px-2.5 py-1.5 rounded-xl tracking-wide"
                                  :class="sub.status === 'graded'
                                      ? 'bg-[#ECFDF5] text-[#065F46]'
                                      : 'bg-[#FEF3C7] text-[#92400E]'">
                                <span class="w-1.5 h-1.5 rounded-full"
                                      :class="sub.status === 'graded' ? 'bg-[#10B981]' : 'bg-[#F59E0B] ldot'"></span>
                                <span x-text="sub.status === 'graded' ? 'Graded' : 'Pending'"></span>
                            </span>
                        </div>

                        <!-- Exam info -->
                        <div class="space-y-3 mb-4">
                            <div class="flex items-start gap-2">
                                <div class="w-7 h-7 rounded-lg bg-[#EFF6FF] flex items-center justify-center text-[#2563EB] flex-shrink-0 mt-0.5">
                                    <i class="fa-solid fa-file-lines text-xs"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-[10px] text-[#94A3B8] font-bold uppercase tracking-widest">Exam</p>
                                    <p class="text-sm font-bold text-[#1E293B] truncate" x-text="sub.subject_title"></p>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div class="bg-[#F8FAFC] border border-[#F1F5F9] rounded-xl px-3 py-2">
                                    <p class="text-[9px] font-bold text-[#94A3B8] uppercase tracking-widest mb-0.5">Course Code</p>
                                    <span class="text-[11px] font-black text-[#2563EB]" x-text="sub.course_code"></span>
                                </div>
                                <div class="bg-[#F8FAFC] border border-[#F1F5F9] rounded-xl px-3 py-2">
                                    <p class="text-[9px] font-bold text-[#94A3B8] uppercase tracking-widest mb-0.5">Exam ID</p>
                                    <span class="text-[10px] font-bold text-[#64748B] font-mono" x-text="sub.clean_exam_id + '…'"></span>
                                </div>
                            </div>
                        </div>

                        <!-- Score bar -->
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1.5">
                                <span class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Score</span>
                                <span class="text-xs font-black"
                                      :class="sub.status === 'graded' ? 'text-[#1E293B]' : 'text-[#94A3B8]'"
                                      x-text="sub.status === 'graded' ? sub.total_score + ' / 40' : '— / 40'"></span>
                            </div>
                            <div class="h-2 bg-[#F1F5F9] rounded-full overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-700"
                                     :class="sub.status === 'graded'
                                         ? (sub.total_score >= 30 ? 'bg-gradient-to-r from-emerald-400 to-teal-400'
                                           : sub.total_score >= 20 ? 'bg-gradient-to-r from-amber-400 to-orange-400'
                                           : 'bg-gradient-to-r from-red-400 to-rose-400')
                                         : 'bg-[#E2E8F0]'"
                                     :style="`width:${sub.status === 'graded' ? Math.round(sub.total_score/40*100) : 0}%`">
                                </div>
                            </div>
                        </div>

                        <!-- Action button -->
                        <a :href="'/teacher/grading/evaluate/' + sub.id"
                           class="flex items-center justify-center gap-2 w-full py-2.5 text-xs font-bold rounded-xl transition-all"
                           :class="sub.status === 'graded'
                               ? 'bg-[#F8FAFC] border border-[#E2E8F0] text-[#475569] hover:bg-[#F1F5F9] hover:text-[#1E293B]'
                               : 'bg-[#2563EB] hover:bg-[#1D4ED8] text-white shadow-md shadow-blue-500/15'">
                            <i class="fa-solid text-[11px]"
                               :class="sub.status === 'graded' ? 'fa-pen-to-square' : 'fa-graduation-cap'"></i>
                            <span x-text="sub.status === 'graded' ? 'Modify Grade' : 'Grade This Paper'"></span>
                        </a>
                    </div>
                </div>
            </template>
        </div>

        <!-- EMPTY STATE -->
        <div x-show="filteredSubmissions.length === 0" x-cloak
             class="flex flex-col items-center justify-center py-20 bg-white border border-[#E2E8F0] rounded-2xl shadow-sm fu">
            <div class="w-16 h-16 rounded-2xl bg-[#F1F5F9] flex items-center justify-center mb-4">
                <i class="fa-regular fa-folder-open text-3xl text-[#CBD5E1]"></i>
            </div>
            <h3 class="text-sm font-bold text-[#1E293B] mb-1">No submissions found</h3>
            <p class="text-xs text-[#94A3B8] font-medium text-center max-w-xs">
                No student papers match the current filter. Try switching tabs or clearing your search.
            </p>
            <button @click="statusFilter='All'; searchQuery=''"
                    class="mt-4 flex items-center gap-2 text-xs font-bold text-[#2563EB] hover:text-[#1D4ED8] bg-[#EFF6FF] px-4 py-2 rounded-xl transition-all">
                <i class="fa-solid fa-rotate-left text-xs"></i> Reset Filters
            </button>
        </div>

    </div>
    </main>
</div>

<div id="toast-box"></div>

<script>
// ── CLOCK ─────────────────────────────────────
function updateClock(){ document.getElementById('live-clock').textContent = new Date().toLocaleTimeString('en-US',{hour12:false}); }
updateClock(); setInterval(updateClock,1000);

// ── TOAST ─────────────────────────────────────
function showToast(msg,type='info'){
    const box=document.getElementById('toast-box');
    const t=document.createElement('div');
    t.className=`toast ${type}`;
    const icons={info:'fa-circle-info',success:'fa-circle-check'};
    t.innerHTML=`<i class="fa-solid ${icons[type]||'fa-circle-info'}"></i>${msg}`;
    box.appendChild(t);
    setTimeout(()=>{t.style.transition='all .3s';t.style.opacity='0';t.style.transform='translateY(8px)';setTimeout(()=>t.remove(),300);},3200);
}

// ── ALPINE APP ────────────────────────────────
function queueApp(){
    return {
        searchQuery: '',
        statusFilter: 'All',

        submissions: [
            @if(isset($submissions) && count($submissions) > 0)
                @foreach($submissions as $sub)
                {
                    id: '{{ $sub->id }}',
                    student_name: '{{ addslashes($sub->student->full_name ?? "You Phatyuth") }}',
                    institutional_id: '{{ $sub->student->institutional_id ?? "STU-1122-3344" }}',
                    subject_title: '{{ addslashes($sub->exam->title ?? "Database") }}',
                    course_code: '{{ addslashes($sub->exam->course->code ?? "DAT-464") }}',
                    clean_exam_id: '{{ substr($sub->exam_id, 0, 8) }}',
                    status: '{{ $sub->status }}',
                    total_score: {{ $sub->total_score ?? 0 }},
                },
                @endforeach
            @else
                // No submissions yet for this teacher's exams.
            @endif
        ],

        get filteredSubmissions(){
            return this.submissions.filter(s => {
                const q = this.searchQuery.toLowerCase();
                const matchSearch = s.student_name.toLowerCase().includes(q) ||
                                    s.subject_title.toLowerCase().includes(q) ||
                                    s.course_code.toLowerCase().includes(q);
                if(this.statusFilter === 'All') return matchSearch;
                if(this.statusFilter === 'pending_grading') return matchSearch && s.status !== 'graded';
                return matchSearch && s.status === 'graded';
            });
        },

        get pendingCount(){ return this.submissions.filter(s => s.status !== 'graded').length; },
        get gradedCount(){  return this.submissions.filter(s => s.status === 'graded').length; },
        get avgScore(){
            const graded = this.submissions.filter(s => s.status === 'graded');
            if(!graded.length) return 0;
            return Math.round(graded.reduce((a,s) => a + (s.total_score||0), 0) / graded.length);
        },

        // ── AVATAR HELPERS ──
        initials(name){
            if(!name) return '?';
            const parts = name.trim().split(' ');
            return parts.length >= 2 ? (parts[0][0]+parts[1][0]).toUpperCase() : name[0].toUpperCase();
        },
        avatarColor(name){
            const colors = ['#2563EB','#10B981','#8B5CF6','#F59E0B','#EF4444','#06B6D4','#EC4899','#14B8A6'];
            let hash=0; for(let c of (name||'?')) hash=c.charCodeAt(0)+((hash<<5)-hash);
            return colors[Math.abs(hash)%colors.length];
        },
    };
}
</script>
</body>
</html>