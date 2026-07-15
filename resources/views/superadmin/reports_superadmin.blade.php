<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics — ExamSystem</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">

    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>

    <style>
        @keyframes ping-slow{75%,100%{transform:scale(2.2);opacity:0;}}
        .ping-slow{animation:ping-slow 2s cubic-bezier(0,0,.2,1) infinite;}

        @keyframes shimmer{0%{background-position:-600px 0}100%{background-position:600px 0}}
        .skeleton{background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%);
                  background-size:1000px 100%;animation:shimmer 1.5s infinite linear;border-radius:8px;display:inline-block;}

        @keyframes countUp{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        .count-animate{animation:countUp 0.4s ease-out forwards;}

        @keyframes fadeIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
        .fade-in{opacity:0;animation:fadeIn 0.4s ease-out forwards;}

        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}

        .card-hover{transition:box-shadow 0.25s,transform 0.25s;}
        .card-hover:hover{box-shadow:0 10px 28px rgba(148,163,184,0.18);transform:translateY(-2px);}

        .chart-container{position:relative;width:100%;}

        /* Range selector button */
        .range-btn{padding:5px 14px;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;
                   border:1px solid #e2e8f0;background:#fff;color:#64748b;transition:all 0.2s;}
        .range-btn.active{background:#2563eb;color:#fff;border-color:#2563eb;box-shadow:0 3px 10px rgba(37,99,235,0.28);}
        .range-btn:not(.active):hover{background:#f1f5f9;border-color:#cbd5e1;}
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

            <a href="{{ route('superadmin.dashboard') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-gauge-high text-xs text-slate-400"></i>
                </span><span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-desktop text-xs text-slate-400"></i>
                </span><span>Live Monitoring</span>
            </a>
            <a href="{{ route('superadmin.exams.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-file-signature text-xs text-slate-400"></i>
                </span><span>Exams Oversight</span>
            </a>

            {{-- ACTIVE --}}
            <a href="{{ route('superadmin.reports.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200"
               style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-chart-line text-xs text-white"></i>
                </span><span>Reports & Analytics</span>
            </a>

            <div class="pt-4 pb-1">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2">Root Access</p>
            </div>

            <a href="{{ route('superadmin.admins.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-users text-xs text-slate-400"></i>
                </span><span>User Management</span>
            </a>
            <a href="{{ route('superadmin.audit-logs.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-shield-halved text-xs text-slate-400"></i>
                </span><span>Audit Trails</span>
            </a>
            <a href="{{ route('superadmin.backups.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-database text-xs text-slate-400"></i>
                </span><span>Database & Backup</span>
            </a>
        </nav>

        <div class="p-3 border-t border-slate-100 flex-shrink-0">
            <a href="{{ route('superadmin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm transition-all duration-200 mb-1">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0">
                    <i class="fa-solid fa-sliders text-xs text-slate-400"></i>
                </span><span>Global Settings</span>
            </a>
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                     style="background:linear-gradient(135deg,#3b82f6,#6366f1);">
                    <i class="fa-solid fa-user-astronaut text-white text-xs"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Super Admin · Root</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-rose-50 hover:text-rose-500 text-slate-400 transition-all" title="Logout">
                        <i class="fa-solid fa-power-off text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        {{-- STICKY TOP BAR --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Reports & Analytics</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">
                    System-wide structural patterns — every department, trend-based view
                </p>
            </div>

            <div class="flex items-center gap-3 ml-auto flex-wrap">
                {{-- Time range --}}
                <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl p-1.5"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.08);">
                    <button class="range-btn active" data-range="7"  onclick="setRange(7,this)">7 Days</button>
                    <button class="range-btn"        data-range="30" onclick="setRange(30,this)">30 Days</button>
                    <button class="range-btn"        data-range="90" onclick="setRange(90,this)">90 Days</button>
                </div>

                {{-- Clock --}}
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>

                {{-- Live counter refresh badge --}}
                <div class="flex items-center gap-2 text-xs font-semibold bg-blue-50 text-blue-700 border border-blue-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-blue-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span class="relative inline-flex rounded-full bg-blue-500" style="width:8px;height:8px;"></span>
                    </span>
                    Counters refresh in <span id="counter-countdown" class="font-mono font-black ml-1 mr-0.5 text-blue-900 w-3 text-center">5</span>s
                </div>
            </div>
        </header>

        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:24px;">

            {{-- SCOPE BANNER --}}
            <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-2xl px-5 py-4"
                 style="box-shadow:0 1px 4px rgba(59,130,246,0.07);">
                <div class="w-8 h-8 rounded-xl bg-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-magnifying-glass-chart text-blue-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-blue-900 mb-0.5">Scope: System-Wide Pattern Analysis</p>
                    <p class="text-[11px] text-blue-700 font-medium leading-relaxed">
                        Answers: <em>"Is any department showing a pattern worth investigating?"</em>
                        Metrics include exam volume, average flag rates, and user growth across <strong>all departments</strong>.
                        No individual student drill-down — that's a scope violation.
                        For live sessions, use <a href="{{ route('superadmin.monitoring.index') }}"
                           class="font-bold underline hover:text-blue-900" style="text-underline-offset:2px;">Live Monitoring →</a>
                    </p>
                </div>
            </div>

            {{-- ========== TODAY'S LIVE COUNTERS ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

                {{-- Exams Created Today --}}
                <div class="card-hover bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-file-circle-plus text-blue-500"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Exams Today</p>
                        <p id="today-exams" class="text-2xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:30px;width:40px;"></span>
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1">Created system-wide</p>
                    </div>
                </div>

                {{-- New Users Today --}}
                <div class="card-hover bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-user-plus text-emerald-500"></i>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">New Users Today</p>
                        <p id="today-users" class="text-2xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:30px;width:40px;"></span>
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1">Registrations across orgs</p>
                    </div>
                </div>

                {{-- Active Right Now --}}
                <div class="card-hover bg-white rounded-2xl border border-emerald-100 p-5 flex items-center gap-4 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);background:linear-gradient(135deg,#fff 70%,rgba(209,250,229,0.2) 100%);">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 flex items-center justify-center flex-shrink-0 relative">
                        <i class="fa-solid fa-tower-broadcast text-emerald-500"></i>
                        <span class="absolute rounded-full bg-emerald-500 ring-2 ring-white animate-pulse"
                              style="width:8px;height:8px;top:-2px;right:-2px;"></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Active Right Now</p>
                        <p id="active-now" class="text-2xl font-black text-emerald-600 leading-none tabular-nums">
                            <span class="skeleton" style="height:30px;width:40px;"></span>
                        </p>
                        <p class="text-[10px] text-slate-400 mt-1">Live exam sessions</p>
                    </div>
                </div>

                {{-- Avg System Flag Rate --}}
                <div class="card-hover bg-white rounded-2xl border border-slate-100 p-5 flex items-center gap-4 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i class="fa-solid fa-flag text-amber-500"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Avg Flag Rate</p>
                        <p id="avg-flag-live" class="text-2xl font-black text-slate-900 leading-none tabular-nums">
                            <span class="skeleton" style="height:30px;width:50px;"></span>
                        </p>
                        <div class="mt-1.5 rounded-full overflow-hidden bg-slate-100" style="height:4px;">
                            <div id="avg-flag-bar-live" class="h-full rounded-full" style="width:0%;background:#34d399;transition:width 0.7s,background 0.4s;"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ========== CHARTS ROW 1 ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Exam Volume Bar Chart --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6 card-hover"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center">
                                <i class="fa-solid fa-chart-column text-blue-500 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Exam Volume Over Time</h3>
                                <p class="text-[10px] text-slate-400 font-medium" id="exam-chart-label">Across all departments</p>
                            </div>
                        </div>
                        <span id="exam-peak-badge" class="text-[10px] font-bold text-blue-600 bg-blue-50 border border-blue-100 px-2.5 py-1 rounded-full"></span>
                    </div>
                    <div class="chart-container" style="height:200px;">
                        <canvas id="examChart"></canvas>
                    </div>
                </div>

                {{-- Flag Rate Trend Line Chart --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-6 card-hover"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 flex items-center justify-center">
                                <i class="fa-solid fa-flag text-rose-500 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Avg Flag Rate Trend</h3>
                                <p class="text-[10px] text-slate-400 font-medium">System-wide violation pattern</p>
                            </div>
                        </div>
                        <span id="flag-trend-badge" class="text-[10px] font-bold px-2.5 py-1 rounded-full border"></span>
                    </div>
                    <div class="chart-container" style="height:200px;">
                        <canvas id="flagChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ========== BOTTOM ROW ========== --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- User Growth Chart (spans 2) --}}
                <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-6 card-hover"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                    <div class="flex items-center justify-between mb-5">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-emerald-50 flex items-center justify-center">
                                <i class="fa-solid fa-users text-emerald-500 text-sm"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">User Growth Timeline</h3>
                                <p class="text-[10px] text-slate-400 font-medium">Cumulative registrations across all orgs</p>
                            </div>
                        </div>
                        <span id="user-growth-badge" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2.5 py-1 rounded-full"></span>
                    </div>
                    <div class="chart-container" style="height:180px;">
                        <canvas id="userChart"></canvas>
                    </div>
                </div>

                {{-- Pattern Watch + Dept Leaderboard --}}
                <div class="flex flex-col gap-4">

                    {{-- Pattern Watch --}}
                    <div class="bg-white rounded-2xl border border-amber-100 overflow-hidden"
                         style="box-shadow:0 1px 4px rgba(245,158,11,0.08);">
                        <div class="px-5 py-3.5 border-b border-amber-100" style="background:linear-gradient(135deg,#fffbeb,#fff);">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-magnifying-glass text-amber-500 text-sm"></i>
                                <p class="text-xs font-bold text-amber-900">Pattern Watch</p>
                                <span id="pattern-count" class="ml-auto text-[9px] font-bold bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full">0 alerts</span>
                            </div>
                        </div>
                        <div id="pattern-list" class="p-3 space-y-2" style="max-height:150px;overflow-y:auto;"></div>
                    </div>

                    {{-- Dept Flag Rate Leaderboard --}}
                    <div class="bg-white rounded-2xl border border-slate-100 flex-1"
                         style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                        <div class="px-5 py-3.5 border-b border-slate-100">
                            <p class="text-xs font-bold text-slate-900">Highest Flag Rates</p>
                            <p class="text-[10px] text-slate-400 font-medium mt-0.5">By department — flag threshold ≥ 8%</p>
                        </div>
                        <div id="dept-leaderboard" class="p-3 space-y-2 overflow-y-auto thin-scroll" style="max-height:220px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<script>
// ============================================================
//  CHART.JS GLOBAL DEFAULTS
// ============================================================
Chart.defaults.font.family = "'Inter', sans-serif";
Chart.defaults.font.size   = 11;
Chart.defaults.color       = '#94a3b8';

const chartInstances = {};

function makeGradient(ctx, color1, color2) {
    const g = ctx.createLinearGradient(0, 0, 0, 200);
    g.addColorStop(0, color1);
    g.addColorStop(1, color2);
    return g;
}

// ============================================================
//  CLOCK
// ============================================================
function updateClock(){
    document.getElementById('live-clock').textContent =
        new Date().toLocaleTimeString('en-US',{hour12:false,hour:'2-digit',minute:'2-digit',second:'2-digit'});
}
updateClock(); setInterval(updateClock, 1000);


// ============================================================
//  LIVE COUNTER COUNTDOWN  (5s)
// ============================================================
let cCount=5;
const cEl=document.getElementById('counter-countdown');
setInterval(()=>{
    cCount--;
    if(cCount<=0){ cCount=5; pollLiveCounters(); }
    cEl.textContent=cCount;
},1000);


// ============================================================
//  METRIC HELPERS
// ============================================================
function setMetric(id,value){
    const el=document.getElementById(id);
    el.innerHTML=''; el.textContent=value;
    el.classList.remove('count-animate'); void el.offsetWidth; el.classList.add('count-animate');
}

function styleBadge(el,text,color,bg,border){
    if(!el)return;
    el.textContent=text; el.style.color=color; el.style.background=bg;
    el.style.borderColor=border; el.style.borderWidth='1px'; el.style.borderStyle='solid';
}

function updateFlagBarLive(pct){
    const bar=document.getElementById('avg-flag-bar-live');
    bar.style.width=Math.min(pct*6,100)+'%';
    bar.style.background=pct>=8?'#f43f5e':pct>=5?'#fbbf24':'#34d399';
}


// ============================================================
//  MOCK DATA  ← Replace with fetch() to your Laravel routes
// ============================================================
function mockCounters(){
    return {
        today_exams: 14 + Math.floor(Math.random()*3),
        today_users: 47 + Math.floor(Math.random()*5),
        active_now:   8,
        avg_flag_rate: 4.7
    };
}

function mockChartData(range){
    if(range===7) return {
        examLabels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        examValues:[4,7,5,9,12,3,8],
        flagLabels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        flagValues:[2.1,3.4,2.8,5.1,7.2,3.0,4.6],
        userLabels:['Mon','Tue','Wed','Thu','Fri','Sat','Sun'],
        userValues:[120,135,140,158,171,173,182],
    };
    if(range===30) return {
        examLabels:['Wk1','Wk2','Wk3','Wk4'],
        examValues:[38,52,47,61],
        flagLabels:['Wk1','Wk2','Wk3','Wk4'],
        flagValues:[3.2,4.8,4.1,6.3],
        userLabels:['Wk1','Wk2','Wk3','Wk4'],
        userValues:[780,920,1050,1184],
    };
    return {
        examLabels:['Jan','Feb','Mar'],
        examValues:[112,148,163],
        flagLabels:['Jan','Feb','Mar'],
        flagValues:[4.1,5.6,6.2],
        userLabels:['Jan','Feb','Mar'],
        userValues:[3200,4100,5480],
    };
}

function mockDepartments(){
    return [
        {name:'Humanities',   exam_count:38, flag_rate:11.4, trend:'up'  },
        {name:'Business',     exam_count:29, flag_rate:7.8,  trend:'up'  },
        {name:'Science',      exam_count:52, flag_rate:3.2,  trend:'down'},
        {name:'Mathematics',  exam_count:41, flag_rate:1.4,  trend:'stable'},
        {name:'Technology',   exam_count:24, flag_rate:0.5,  trend:'stable'},
        {name:'Health Sci.',  exam_count:18, flag_rate:2.1,  trend:'down'},
    ];
}


// ============================================================
//  RENDER: CHARTS
// ============================================================
let currentRange = 7;

function buildCharts(range){
    const d=mockChartData(range);
    // PRODUCTION: fetch(`{{ route('superadmin.reports.api') }}?range=${range}`).then(r=>r.json()).then(d=>{...})

    // --- EXAM VOLUME ---
    if(chartInstances.exam) chartInstances.exam.destroy();
    const examCtx=document.getElementById('examChart').getContext('2d');
    chartInstances.exam=new Chart(examCtx,{
        type:'bar',
        data:{
            labels:d.examLabels,
            datasets:[{
                data:d.examValues,
                backgroundColor: makeGradient(examCtx,'rgba(37,99,235,0.85)','rgba(37,99,235,0.35)'),
                borderRadius:8,
                borderSkipped:false,
                maxBarThickness:36,
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>' '+c.parsed.y+' exams'}}},
            scales:{
                y:{beginAtZero:true,grid:{color:'#f1f5f9',drawBorder:false},ticks:{stepSize:4}},
                x:{grid:{display:false},border:{display:false}}
            }
        }
    });
    const peak=Math.max(...d.examValues);
    document.getElementById('exam-peak-badge').textContent='Peak: '+peak+' exams';

    // --- FLAG RATE TREND ---
    if(chartInstances.flag) chartInstances.flag.destroy();
    const flagCtx=document.getElementById('flagChart').getContext('2d');
    const lastFlag=d.flagValues[d.flagValues.length-1];
    const prevFlag=d.flagValues[d.flagValues.length-2]||lastFlag;
    const trendUp=lastFlag>prevFlag;
    chartInstances.flag=new Chart(flagCtx,{
        type:'line',
        data:{
            labels:d.flagLabels,
            datasets:[{
                data:d.flagValues,
                borderColor:'#e11d48',
                backgroundColor:makeGradient(flagCtx,'rgba(225,29,72,0.12)','rgba(225,29,72,0.01)'),
                fill:true, tension:0.4, borderWidth:2.5,
                pointBackgroundColor:'#e11d48', pointRadius:4, pointHoverRadius:6,
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>' '+c.parsed.y.toFixed(1)+'%'}}},
            scales:{
                y:{beginAtZero:true,grid:{color:'#f1f5f9',drawBorder:false},ticks:{callback:v=>v+'%'}},
                x:{grid:{display:false},border:{display:false}}
            }
        }
    });
    const tBadge=document.getElementById('flag-trend-badge');
    if(trendUp){styleBadge(tBadge,'↑ Rising','#e11d48','#fff1f2','#fecdd3');}
    else{styleBadge(tBadge,'↓ Declining','#059669','#ecfdf5','#a7f3d0');}

    // --- USER GROWTH ---
    if(chartInstances.user) chartInstances.user.destroy();
    const userCtx=document.getElementById('userChart').getContext('2d');
    chartInstances.user=new Chart(userCtx,{
        type:'line',
        data:{
            labels:d.userLabels,
            datasets:[{
                data:d.userValues,
                borderColor:'#059669',
                backgroundColor:makeGradient(userCtx,'rgba(5,150,105,0.14)','rgba(5,150,105,0.01)'),
                fill:true, tension:0.4, borderWidth:2.5,
                pointBackgroundColor:'#059669', pointRadius:4, pointHoverRadius:6,
            }]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            plugins:{legend:{display:false},tooltip:{callbacks:{label:c=>' '+c.parsed.y.toLocaleString()+' users'}}},
            scales:{
                y:{beginAtZero:false,grid:{color:'#f1f5f9',drawBorder:false}},
                x:{grid:{display:false},border:{display:false}}
            }
        }
    });
    const latest=d.userValues[d.userValues.length-1];
    const earliest=d.userValues[0];
    const growth=latest-earliest;
    document.getElementById('user-growth-badge').textContent='+'+growth.toLocaleString()+' this period';
}


// ============================================================
//  RENDER: DEPARTMENT LEADERBOARD + PATTERN WATCH
// ============================================================
function renderDeptLeaderboard(){
    const depts=mockDepartments();
    // PRODUCTION: fetch("{{ route('superadmin.reports.departments') }}").then(r=>r.json()).then(...)

    const sorted=[...depts].sort((a,b)=>b.flag_rate-a.flag_rate);

    // Dept leaderboard
    document.getElementById('dept-leaderboard').innerHTML=sorted.map((d,i)=>{
        const isH=d.flag_rate>=8,isM=d.flag_rate>=5;
        const bC=isH?'#f43f5e':isM?'#fbbf24':'#34d399';
        const tC=isH?'#e11d48':isM?'#d97706':'#059669';
        const bg=isH?'#fff1f2':isM?'#fffbeb':'#ecfdf5';
        const bc=isH?'#fecdd3':isM?'#fde68a':'#a7f3d0';
        const trendIcon=d.trend==='up'
            ?'<i class="fa-solid fa-arrow-trend-up" style="color:#f43f5e;font-size:10px;"></i>'
            :d.trend==='down'
            ?'<i class="fa-solid fa-arrow-trend-down" style="color:#10b981;font-size:10px;"></i>'
            :'<i class="fa-solid fa-minus" style="color:#94a3b8;font-size:10px;"></i>';
        return `
        <div class="fade-in" style="animation-delay:${i*50}ms;padding:10px 12px;border-radius:10px;background:#f8fafc;margin-bottom:6px;">
            <div style="display:flex;align-items:center;justify-content:space-between;gap:8px;margin-bottom:6px;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:10px;font-weight:800;color:#94a3b8;width:16px;flex-shrink:0;">#${i+1}</span>
                    <span style="font-size:12px;font-weight:700;color:#0f172a;">${d.name}</span>
                    ${isH?`<span style="font-size:9px;font-weight:800;padding:1px 6px;border-radius:4px;background:#fff1f2;color:#e11d48;border:1px solid #fecdd3;">HIGH</span>`:''}
                </div>
                <div style="display:flex;align-items:center;gap:5px;">
                    ${trendIcon}
                    <span style="font-size:11px;font-weight:800;padding:2px 8px;border-radius:999px;border:1px solid;background:${bg};color:${tC};border-color:${bc};">${d.flag_rate}%</span>
                </div>
            </div>
            <div style="height:4px;border-radius:999px;background:#e2e8f0;overflow:hidden;">
                <div style="height:100%;border-radius:999px;background:${bC};width:${Math.min(d.flag_rate*5,100)}%;transition:width 0.7s;"></div>
            </div>
            <p style="font-size:10px;color:#94a3b8;margin-top:4px;font-weight:500;">${d.exam_count} exams this period</p>
        </div>`;
    }).join('');

    // Pattern watch (flag rate >= 5% and trending up)
    const flagged=sorted.filter(d=>d.flag_rate>=5&&d.trend==='up');
    const pList=document.getElementById('pattern-list');
    const pCount=document.getElementById('pattern-count');
    pCount.textContent=flagged.length+' alert'+(flagged.length!==1?'s':'');
    if(!flagged.length){
        pList.innerHTML=`<div style="text-align:center;padding:16px 0;color:#cbd5e1;">
            <i class="fa-solid fa-circle-check" style="font-size:20px;margin-bottom:6px;display:block;color:#d1fae5;"></i>
            <p style="font-size:11px;font-weight:600;color:#94a3b8;">No patterns flagged</p></div>`;
    } else {
        pList.innerHTML=flagged.map(d=>`
            <div style="display:flex;align-items:center;gap:8px;padding:8px 10px;border-radius:8px;background:#fffbeb;border:1px solid #fde68a;margin-bottom:4px;">
                <i class="fa-solid fa-triangle-exclamation" style="color:#f59e0b;font-size:11px;flex-shrink:0;"></i>
                <div style="flex:1;min-width:0;">
                    <p style="font-size:11px;font-weight:700;color:#92400e;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${d.name}</p>
                    <p style="font-size:10px;color:#b45309;font-weight:500;">Flag rate ${d.flag_rate}% — rising trend</p>
                </div>
            </div>`).join('');
    }
}


// ============================================================
//  POLL LIVE COUNTERS (every 5s)
// ============================================================
function pollLiveCounters(){
    // PRODUCTION: fetch("{{ route('superadmin.reports.live') }}").then(r=>r.json()).then(d=>{...})
    const d=mockCounters();
    setMetric('today-exams', d.today_exams);
    setMetric('today-users', d.today_users);
    setMetric('active-now',  d.active_now);
    setMetric('avg-flag-live', d.avg_flag_rate+'%');
    updateFlagBarLive(d.avg_flag_rate);
}


// ============================================================
//  RANGE SELECTOR
// ============================================================
function setRange(days, btn){
    currentRange=days;
    document.querySelectorAll('.range-btn').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    buildCharts(days);
    // PRODUCTION: Also re-fetch dept data for the new range
}


// ============================================================
//  INIT
// ============================================================
pollLiveCounters();
buildCharts(7);
renderDeptLeaderboard();
</script>

</body>
</html>