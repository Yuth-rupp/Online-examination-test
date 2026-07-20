<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ExamSystem – Analytics</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{font-family:'Inter',system-ui,sans-serif;-webkit-font-smoothing:antialiased}

        /* SCROLLBAR */
        ::-webkit-scrollbar{width:4px;height:4px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:99px}

        /* SIDEBAR */
        .nl{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:500;color:#64748B;transition:all .18s}
        .nl:hover{background:#F8FAFC;color:#1E293B}
        .nl.act{background:#EFF6FF;color:#2563EB;font-weight:700;border:1px solid #BFDBFE}
        .ni{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;transition:all .18s}
        .nl.act .ni{background:#2563EB;color:#fff}
        .nl:hover .ni{background:#F1F5F9}

        /* STAT CARD */
        .scard{background:#fff;border-radius:20px;border:1.5px solid #F1F5F9;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.04);transition:all .25s;overflow:hidden;position:relative}
        .scard:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(0,0,0,.09)}
        .scard-top{height:4px;width:100%}

        /* ANIMATIONS */
        @keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}
        .ld{animation:pdot 1.5s infinite}
        @keyframes fu{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .fu{animation:fu .35s ease both}
        @keyframes bfill{from{width:0}}
        .bf{animation:bfill .9s ease both}
        @keyframes tin{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

        /* TOAST */
        #tbox{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none}
        .toast{display:flex;align-items:center;gap:9px;color:#fff;border-radius:14px;padding:11px 18px;font-size:12px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,.22);animation:tin .3s ease;min-width:200px;font-family:'Inter',sans-serif;pointer-events:auto;white-space:nowrap}

        /* TABLE ROW */
        .trow{transition:background .15s}
        .trow:hover{background:#F8FAFC}

        /* NOTIFICATION */
        #notifBox{display:none;position:absolute;right:0;top:calc(100% + 8px);width:310px;background:#fff;border:1.5px solid #E2E8F0;border-radius:16px;box-shadow:0 16px 40px rgba(0,0,0,.12);z-index:99}
        #notifBox.show{display:block;animation:tin .2s ease}

        /* FAIL RATE BAR */
        @keyframes frBar{from{width:0}}
        .fr-bar{animation:frBar .8s ease forwards}
    </style>
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen overflow-x-hidden">

<div class="flex h-screen overflow-hidden">

{{-- ═══════════════════════ SIDEBAR ═══════════════════════ --}}
@include('partials.teacher-sidebar')

{{-- ═══════════════════════ MAIN CONTENT ═══════════════════════ --}}
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    {{-- ── HEADER ── --}}
    <div class="flex-shrink-0" style="background:linear-gradient(135deg,#0B1836 0%,#152C5E 55%,#1E3A8A 100%)">
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-0.5">
                    <span class="flex items-center gap-1.5 text-[10px] font-black text-emerald-300 px-2 py-0.5 rounded-lg" style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.3)">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span> LIVE
                    </span>
                    <span class="text-[10px] text-white/40 font-semibold">Real-Time Analytics</span>
                </div>
                <h1 class="text-[16px] font-black text-white tracking-tight">Analytics Engine</h1>
                <p class="text-[10px] text-white/50 mt-0.5">Evaluation data based on active exam tracking</p>
            </div>

            <div class="flex items-center gap-2.5 flex-shrink-0">
                {{-- Exam Filter --}}
                <div class="relative">
                    <select id="examFilterSelect" onchange="processUpdate()"
                            class="text-xs font-bold py-2 pl-3 pr-8 rounded-xl focus:outline-none cursor-pointer appearance-none"
                            style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18);color:#fff">
                        <option value="ALL" style="color:#1E293B;background:#fff">All Examinations</option>
                        @foreach($teacherExams as $exam)
                            <option value="{{ $exam->exam_id }}" style="color:#1E293B;background:#fff">{{ $exam->title }}</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down text-[9px] text-white/50 absolute right-2.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                </div>

                {{-- Notification --}}
                <div class="relative" id="notifWrap">
                    <button onclick="toggleNotif(event)" id="notif-bell-btn-analytics"
                            class="w-9 h-9 rounded-xl flex items-center justify-center transition-all relative"
                            style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.18)">
                        <i class="fa-solid fa-bell text-white/80 text-sm"></i>
                        <span id="teacher-notif-dot-analytics"
                              class="absolute top-1.5 right-1.5 w-2 h-2 bg-red-400 rounded-full border border-white/50 {{ count($notifications) > 0 ? '' : 'hidden' }}"></span>
                    </button>
                    <div id="notifBox">
                        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100" style="background:#F8FAFC">
                            <span class="text-[10px] font-black text-slate-700 uppercase tracking-widest">System Alerts</span>
                            <div class="flex items-center gap-2">
                                <span id="teacher-notif-pill-analytics"
                                      class="text-[9px] font-black text-white bg-red-500 px-2 py-0.5 rounded-full {{ count($notifications) > 0 ? '' : 'hidden' }}">{{ count($notifications) }}</span>
                                <button type="button" id="teacher-notif-clear-analytics" class="text-[9px] font-bold text-slate-400 hover:text-blue-600 transition-colors">Clear</button>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto divide-y divide-slate-50" id="teacher-notif-list-analytics">
                            @forelse($notifications as $item)
                            <div class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer">
                                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 mt-0.5">
                                    <i class="fa-solid fa-bell text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[11px] text-slate-700 font-medium leading-snug">{{ $item['text'] }}</p>
                                    <span class="text-[9px] text-slate-400 font-bold mt-0.5 flex items-center gap-1">
                                        <i class="fa-regular fa-clock"></i>{{ $item['time'] }}
                                    </span>
                                </div>
                            </div>
                            @empty
                            <div class="py-8 text-center">
                                <i class="fa-regular fa-bell-slash text-2xl text-slate-300 mb-2 block"></i>
                                <p class="text-xs text-slate-400 font-medium">No alerts</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Clock --}}
                <div class="px-3 py-2 rounded-xl hidden lg:block" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-0.5">Local Time</p>
                    <p class="text-[11px] font-black text-white tabular-nums" id="lc">--:--:--</p>
                </div>

                {{-- Export --}}
                <button onclick="exportCSV()"
                        class="flex items-center gap-1.5 text-xs font-black px-4 py-2 rounded-xl transition-all"
                        style="background:#10B981;color:#fff;box-shadow:0 4px 14px rgba(16,185,129,.35)">
                    <i class="fa-solid fa-file-arrow-down"></i> Export CSV
                </button>
            </div>
        </div>
    </div>

    {{-- SCROLL AREA --}}
    <div class="flex-1 overflow-y-auto bg-slate-100 p-5 space-y-5">

        {{-- ── STAT CARDS ── --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

            {{-- Card: Students --}}
            <div class="scard fu" style="animation-delay:.04s">
                <div class="scard-top" style="background:linear-gradient(90deg,#2563EB,#60A5FA)"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-blue-600" style="background:#EFF6FF">
                            <i class="fa-solid fa-users text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-blue-700 bg-blue-100 px-2 py-0.5 rounded-full">TOTAL</span>
                    </div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">System Students</p>
                    <p class="text-3xl font-black text-slate-900 tabular-nums">{{ number_format($totalStudentsCount) }}</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background:#EFF6FF">
                        <div class="h-full rounded-full bf" style="background:linear-gradient(90deg,#2563EB,#60A5FA);width:100%"></div>
                    </div>
                </div>
            </div>

            {{-- Card: Submissions --}}
            <div class="scard fu" style="animation-delay:.08s">
                <div class="scard-top" style="background:linear-gradient(90deg,#F59E0B,#FCD34D)"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-amber-600" style="background:#FEF3C7">
                            <i class="fa-solid fa-file-lines text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">LIVE</span>
                    </div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Submissions</p>
                    <p class="text-3xl font-black text-amber-600 tabular-nums" id="cardSub">{{ $totalSubmissionsCount }}</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background:#FEF3C7">
                        <div class="h-full rounded-full bf" id="cardSubBar" style="background:linear-gradient(90deg,#F59E0B,#FCD34D);width:100%"></div>
                    </div>
                </div>
            </div>

            {{-- Card: Avg Score --}}
            <div class="scard fu" style="animation-delay:.12s">
                <div class="scard-top" style="background:linear-gradient(90deg,#10B981,#34D399)"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-emerald-600" style="background:#ECFDF5">
                            <i class="fa-solid fa-star-half-stroke text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-full">AVG</span>
                    </div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Class Average</p>
                    <p class="text-3xl font-black text-emerald-600 tabular-nums" id="cardAvg">{{ $averageClassScore }}</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background:#ECFDF5">
                        <div class="h-full rounded-full bf" id="cardAvgBar" style="background:linear-gradient(90deg,#10B981,#34D399);width:60%"></div>
                    </div>
                </div>
            </div>

            {{-- Card: Pass Rate --}}
            <div class="scard fu" style="animation-delay:.16s">
                <div class="scard-top" style="background:linear-gradient(90deg,#8B5CF6,#A78BFA)"></div>
                <div class="p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-purple-600" style="background:#F5F3FF">
                            <i class="fa-solid fa-percent text-base"></i>
                        </div>
                        <span class="text-[9px] font-black text-purple-700 bg-purple-100 px-2 py-0.5 rounded-full">RATE</span>
                    </div>
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Pass Rate</p>
                    <p class="text-3xl font-black text-purple-600 tabular-nums" id="cardPR">{{ $examPassRatePercentage }}%</p>
                    <div class="mt-3 h-1.5 rounded-full overflow-hidden" style="background:#F5F3FF">
                        <div class="h-full rounded-full bf" id="cardPRBar" style="background:linear-gradient(90deg,#8B5CF6,#A78BFA);width:{{ $examPassRatePercentage }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── CHARTS ROW ── --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

            {{-- Bar + Line Chart --}}
            <div class="xl:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden fu" style="animation-delay:.2s">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100" style="background:#FAFCFF">
                    <div>
                        <h3 class="text-[13px] font-black text-slate-900">Submissions Progression</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Monthly submission count + average score curve</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500">
                            <span class="w-3 h-3 rounded-sm" style="background:#4F46E5"></span> Papers
                        </div>
                        <div class="flex items-center gap-1.5 text-[10px] font-bold text-slate-500">
                            <span class="w-3 h-1 rounded-full" style="background:#DB2777"></span> Avg Score
                        </div>
                    </div>
                </div>
                <div class="p-5" style="height:280px;position:relative">
                    <canvas id="mainChart"></canvas>
                </div>
            </div>

            {{-- Pass/Fail Donut + Hardest Questions --}}
            <div class="flex flex-col gap-5">
                {{-- Donut --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden fu" style="animation-delay:.24s">
                    <div class="px-5 py-3.5 border-b border-slate-100" style="background:#FAFCFF">
                        <h3 class="text-[13px] font-black text-slate-900">Pass / Fail Split</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Distribution of current filtered results</p>
                    </div>
                    <div class="p-4 flex items-center gap-4">
                        <div style="width:100px;height:100px;flex-shrink:0;position:relative">
                            <canvas id="donutChart"></canvas>
                            <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                                <span class="text-sm font-black text-slate-900" id="donutCenterLabel">—</span>
                            </div>
                        </div>
                        <div class="space-y-2 flex-1">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Passed
                                </div>
                                <span class="text-[11px] font-black text-emerald-600" id="donutPassLabel">—</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-1.5 text-[11px] font-bold text-slate-700">
                                    <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span> Failed
                                </div>
                                <span class="text-[11px] font-black text-red-500" id="donutFailLabel">—</span>
                            </div>
                            <div class="h-1.5 rounded-full overflow-hidden" style="background:#F1F5F9">
                                <div class="h-full rounded-full transition-all duration-700 bg-emerald-500" id="passBarFill" style="width:0%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Hardest Questions --}}
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden fu" style="animation-delay:.28s">
                    <div class="px-5 py-3.5 border-b border-slate-100" style="background:#FAFCFF">
                        <h3 class="text-[13px] font-black text-slate-900">Hardest Questions</h3>
                        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Most-missed by students</p>
                    </div>
                    <div class="p-4 space-y-3">
                        @forelse($hardestQuestions as $qi => $q)
                        <div class="rounded-xl overflow-hidden border border-slate-100" style="background:#FAFCFF">
                            <div class="flex items-center justify-between px-3 py-2 border-b border-slate-100">
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest">Q #{{ $q->id }}</span>
                                <span class="text-[10px] font-black text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-triangle-exclamation text-[8px]"></i>
                                    {{ $q->fail_rate }}% failed
                                </span>
                            </div>
                            <div class="px-3 py-2">
                                <p class="text-[11px] font-semibold text-slate-700 line-clamp-2 mb-2">{{ strip_tags($q->content) }}</p>
                                <div class="h-1.5 rounded-full overflow-hidden" style="background:#FEE2E2">
                                    <div class="h-full rounded-full fr-bar" style="background:linear-gradient(90deg,#EF4444,#F87171);width:{{ $q->fail_rate }}%"></div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="py-6 text-center">
                            <i class="fa-solid fa-circle-check text-2xl text-emerald-300 mb-2 block"></i>
                            <p class="text-xs text-slate-400 font-medium">No difficult questions detected</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- ── GRADE MATRIX TABLE ── --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden fu" style="animation-delay:.32s">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100" style="background:#FAFCFF">
                <div>
                    <h3 class="text-[13px] font-black text-slate-900">Live Student Grade Matrix</h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-0.5">Individual submission scores — updates with filter</p>
                </div>
                <div class="flex items-center gap-2">
                    <span class="flex items-center gap-1 text-[10px] font-bold text-slate-500">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span>
                        <span id="tableCount">0</span> rows
                    </span>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr style="background:#F8FAFC;border-bottom:1px solid #F1F5F9">
                            <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Student</th>
                            <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Course</th>
                            <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest">Exam</th>
                            <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Score</th>
                            <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Pass Mark</th>
                            <th class="px-5 py-3 text-[9px] font-black text-slate-400 uppercase tracking-widest text-center">Result</th>
                        </tr>
                    </thead>
                    <tbody id="gTable" class="divide-y divide-slate-50 text-[13px]"></tbody>
                </table>
            </div>
        </div>

        <div class="h-2"></div>
    </div>{{-- /scroll --}}
</div>{{-- /main --}}

</div>{{-- /flex --}}

<div id="tbox"></div>

<script>
const MONTHS = {!! json_encode($monthsLabels) !!};
const DATASET = {!! json_encode($liveSubmissionsRaw) !!};
let chartBar = null;
let chartDonut = null;

// ── CLOCK ──────────────────────────
(function tick(){
    const el=document.getElementById('lc');
    if(el) el.textContent=new Date().toLocaleTimeString('en-US',{hour12:false});
    setTimeout(tick,1000);
})();

// ── TOAST ──────────────────────────
function toast(msg, type='success'){
    const c={success:'#10B981',info:'#4F46E5',warning:'#F59E0B'};
    const i={success:'fa-circle-check',info:'fa-circle-info',warning:'fa-triangle-exclamation'};
    const b=document.getElementById('tbox'),el=document.createElement('div');
    el.className='toast';el.style.background=c[type];
    el.innerHTML=`<i class="fa-solid ${i[type]}"></i>${msg}`;
    b.appendChild(el);
    setTimeout(()=>{el.style.transition='all .3s';el.style.opacity='0';el.style.transform='translateY(8px)';setTimeout(()=>el.remove(),300)},3000);
}

// ── NOTIFICATIONS ──────────────────
function toggleNotif(e){
    e.stopPropagation();
    document.getElementById('notifBox').classList.toggle('show');
}
document.addEventListener('click',()=>document.getElementById('notifBox').classList.remove('show'));
document.getElementById('notifWrap').addEventListener('click',e=>e.stopPropagation());

// ── MAIN UPDATE ────────────────────
function processUpdate(){
    const sel = document.getElementById('examFilterSelect').value;
    const recs = sel==='ALL' ? DATASET : DATASET.filter(r=>r.exam_id===sel);

    let sum=0, passed=0, failed=0;
    recs.forEach(r=>{
        const s=parseFloat(r.student_score)||0;
        const p=parseFloat(r.passing_mark)||50;
        sum+=s;
        s>=p ? passed++ : failed++;
    });
    const total=recs.length;
    const avg=total>0?(sum/total).toFixed(1):'0.0';
    const pr=total>0?Math.round(passed/total*100):0;

    // Update stat cards
    document.getElementById('cardSub').textContent=total;
    document.getElementById('cardAvg').textContent=avg;
    document.getElementById('cardPR').textContent=pr+'%';
    document.getElementById('cardPRBar').style.width=pr+'%';

    // Donut labels
    document.getElementById('donutCenterLabel').textContent=pr+'%';
    document.getElementById('donutPassLabel').textContent=passed+' students';
    document.getElementById('donutFailLabel').textContent=failed+' students';
    document.getElementById('passBarFill').style.width=(total>0?Math.round(passed/total*100):0)+'%';

    // Monthly data
    const mCount=Array(12).fill(0), mSum=Array(12).fill(0);
    recs.forEach(r=>{
        const m=new Date(r.created_at).getMonth();
        if(m>=0&&m<12){ mCount[m]++; mSum[m]+=parseFloat(r.student_score)||0; }
    });
    const mAvg=mCount.map((c,i)=>c>0?(mSum[i]/c).toFixed(1):0);

    updateBarChart(mCount, mAvg);
    updateDonut(passed, failed);
    renderTable(recs);
}

// ── BAR CHART ──────────────────────
function updateBarChart(counts, avgs){
    const ctx=document.getElementById('mainChart').getContext('2d');
    if(chartBar){
        chartBar.data.datasets[0].data=counts;
        chartBar.data.datasets[1].data=avgs;
        chartBar.update('active');
        return;
    }
    const grad=ctx.createLinearGradient(0,0,0,250);
    grad.addColorStop(0,'rgba(79,70,229,.9)');
    grad.addColorStop(1,'rgba(79,70,229,.55)');

    chartBar=new Chart(ctx,{
        data:{
            labels:MONTHS,
            datasets:[
                {
                    type:'bar', label:'Submissions',
                    data:counts, backgroundColor:grad,
                    borderRadius:8, barThickness:18, order:2
                },
                {
                    type:'line', label:'Avg Score',
                    data:avgs, borderColor:'#DB2777',
                    borderWidth:2.5, pointBackgroundColor:'#fff',
                    pointBorderColor:'#DB2777', pointRadius:4,
                    pointHoverRadius:6, fill:false, tension:.4, order:1
                }
            ]
        },
        options:{
            responsive:true, maintainAspectRatio:false,
            interaction:{mode:'index',intersect:false},
            plugins:{
                legend:{display:false},
                tooltip:{
                    backgroundColor:'#0F172A', titleColor:'#94A3B8',
                    bodyColor:'#fff', borderColor:'#1E293B', borderWidth:1,
                    padding:10, cornerRadius:10,
                    titleFont:{size:10,weight:'700'},
                    bodyFont:{size:11,weight:'700'}
                }
            },
            scales:{
                x:{grid:{display:false},ticks:{color:'#94A3B8',font:{size:10,weight:'600'}}},
                y:{grid:{color:'#F1F5F9'},ticks:{color:'#94A3B8',font:{size:10}},beginAtZero:true}
            }
        }
    });
}

// ── DONUT CHART ────────────────────
function updateDonut(passed,failed){
    const ctx=document.getElementById('donutChart').getContext('2d');
    const data=[passed||0, failed||0];
    if(chartDonut){
        chartDonut.data.datasets[0].data=data;
        chartDonut.update('active');
        return;
    }
    chartDonut=new Chart(ctx,{
        type:'doughnut',
        data:{
            labels:['Passed','Failed'],
            datasets:[{data, backgroundColor:['#10B981','#EF4444'], borderWidth:0, borderRadius:4, hoverOffset:4}]
        },
        options:{
            responsive:true, maintainAspectRatio:false, cutout:'72%',
            plugins:{legend:{display:false},tooltip:{
                backgroundColor:'#0F172A',titleColor:'#94A3B8',bodyColor:'#fff',
                padding:8,cornerRadius:8,borderColor:'#1E293B',borderWidth:1,
                bodyFont:{size:11,weight:'700'}
            }}
        }
    });
}

// ── TABLE ──────────────────────────
function renderTable(recs){
    const tb=document.getElementById('gTable');
    document.getElementById('tableCount').textContent=recs.length;

    if(!recs.length){
        tb.innerHTML=`<tr><td colspan="6" class="px-5 py-10 text-center text-slate-400 text-xs font-medium italic">No submissions match this filter.</td></tr>`;
        return;
    }

    tb.innerHTML=recs.map((r,idx)=>{
        const score=parseFloat(r.student_score)||0;
        const pm=parseFloat(r.passing_mark)||50;
        const passed=score>=pm;
        const name=(r.student_name||'?').trim();
        const parts=name.split(' ');
        const ini=parts.length>=2?(parts[0][0]+parts[1][0]).toUpperCase():name[0].toUpperCase();
        const colors=['#4F46E5','#10B981','#F59E0B','#EF4444','#8B5CF6','#06B6D4','#EC4899'];
        let h=0; for(let c of name) h=c.charCodeAt(0)+((h<<5)-h);
        const avatarBg=colors[Math.abs(h)%colors.length];
        const pct=Math.min(Math.round(score/pm*100),100);

        return `<tr class="trow" style="border-bottom:1px solid #F8FAFC;animation:fu .3s ease ${idx*0.04}s both">
            <td class="px-5 py-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[10px] font-black flex-shrink-0"
                         style="background:${avatarBg}">${ini}</div>
                    <div>
                        <p class="text-[12px] font-bold text-slate-900">${name}</p>
                        <p class="text-[9px] font-mono font-bold text-slate-400">${r.student_id}</p>
                    </div>
                </div>
            </td>
            <td class="px-5 py-3 text-[11px] font-semibold text-slate-500">${r.course_name}</td>
            <td class="px-5 py-3 text-[11px] font-semibold text-slate-500 max-w-[140px]">
                <p class="truncate">${r.exam_title}</p>
            </td>
            <td class="px-5 py-3 text-center">
                <div class="inline-flex flex-col items-center gap-1">
                    <span class="text-sm font-black ${passed?'text-emerald-600':'text-red-600'}">${score}</span>
                    <div class="w-12 h-1 rounded-full overflow-hidden" style="background:#F1F5F9">
                        <div class="h-full rounded-full" style="width:${pct}%;background:${passed?'#10B981':'#EF4444'}"></div>
                    </div>
                </div>
            </td>
            <td class="px-5 py-3 text-center text-[11px] font-bold text-slate-400">${pm}</td>
            <td class="px-5 py-3 text-center">
                <span class="inline-flex items-center gap-1 text-[10px] font-black px-2.5 py-1.5 rounded-lg"
                      style="${passed?'background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0':'background:#FEF2F2;color:#991B1B;border:1px solid #FECACA'}">
                    <i class="fa-solid ${passed?'fa-circle-check':'fa-circle-xmark'} text-[9px]"></i>
                    ${passed?'PASSED':'FAILED'}
                </span>
            </td>
        </tr>`;
    }).join('');
}

// ── CSV EXPORT (clean & readable) ──
function exportCSV(){
    const sel=document.getElementById('examFilterSelect').value;
    const recs=sel==='ALL' ? DATASET : DATASET.filter(r=>r.exam_id===sel);
    const examLabel=sel==='ALL' ? 'All Examinations' : (document.getElementById('examFilterSelect').options[document.getElementById('examFilterSelect').selectedIndex].text);
    const now=new Date();
    const dateStr=now.toLocaleDateString('en-US',{year:'numeric',month:'long',day:'numeric'});
    const timeStr=now.toLocaleTimeString('en-US',{hour12:false});

    let csv='';
    // ── Title Block ──
    csv+=`"ExamSystem — Grade Export Report"\n`;
    csv+=`"Generated:","${dateStr} at ${timeStr}"\n`;
    csv+=`"Filter:","${examLabel}"\n`;
    csv+=`"Total Records:","${recs.length}"\n`;
    csv+=`\n`;

    // ── Summary ──
    let passed=0,sum=0;
    recs.forEach(r=>{const s=parseFloat(r.student_score)||0;const p=parseFloat(r.passing_mark)||50;sum+=s;if(s>=p)passed++;});
    const avg=recs.length?(sum/recs.length).toFixed(1):'0.0';
    const pr=recs.length?Math.round(passed/recs.length*100):0;

    csv+=`"=== SUMMARY ==="\n`;
    csv+=`"Total Submissions:","${recs.length}"\n`;
    csv+=`"Total Passed:","${passed}"\n`;
    csv+=`"Total Failed:","${recs.length-passed}"\n`;
    csv+=`"Class Average:","${avg}"\n`;
    csv+=`"Pass Rate:","${pr}%"\n`;
    csv+=`\n`;

    // ── Headers ──
    csv+=`"=== STUDENT RECORDS ==="\n`;
    csv+=`"#","Student ID","Full Name","Course","Exam Title","Score","Pass Mark","Grade %","Result"\n`;

    // ── Rows ──
    recs.forEach((r,i)=>{
        const s=parseFloat(r.student_score)||0;
        const pm=parseFloat(r.passing_mark)||50;
        const pct=pm>0?Math.round(s/pm*100):0;
        const status=s>=pm?'PASSED':'FAILED';
        csv+=`"${i+1}","${r.student_id}","${(r.student_name||'').replace(/"/g,'""')}","${(r.course_name||'').replace(/"/g,'""')}","${(r.exam_title||'').replace(/"/g,'""')}","${s}","${pm}","${pct}%","${status}"\n`;
    });

    csv+=`\n"--- End of Report ---"\n`;

    const blob=new Blob([csv],{type:'text/csv;charset=utf-8;'});
    const url=URL.createObjectURL(blob);
    const a=document.createElement('a');
    a.href=url;
    a.download=`ExamSystem_GradeReport_${now.toISOString().slice(0,10)}.csv`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);

    toast(`Exported ${recs.length} records successfully`,'success');
}

// ── INIT ───────────────────────────
document.addEventListener('DOMContentLoaded', processUpdate);
</script>

@include('partials.teacher-notification-realtime')

</body>
</html>