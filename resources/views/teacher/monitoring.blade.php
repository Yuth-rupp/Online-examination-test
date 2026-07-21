{{-- ============================================================
     FILE:   resources/views/teacher/monitoring.blade.php
     ROLE:   TEACHER ONLY — Live proctoring room
             Teachers watch student webcam feeds, approve keys,
             and detect cheating in real-time.
     ============================================================ --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ExamSystem – Live Proctoring | {{ $exam->title ?? 'Active Session' }}</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *,*::before,*::after { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }

        /* ── SIDEBAR NAV ── */
        .nl { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:10px; text-decoration:none; font-size:13px; font-weight:500; color:#64748B; transition:all .18s; }
        .nl:hover { background:#F8FAFC; color:#1E293B; }
        .nl.act { background:#EFF6FF; color:#2563EB; font-weight:700; border:1px solid #BFDBFE; }
        .ni { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; transition:all .18s; }
        .nl.act .ni { background:#2563EB; color:#fff; }
        .nl:hover .ni { background:#F1F5F9; }

        /* ── ANIMATIONS ── */
        @keyframes pdot  { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.4;transform:scale(.7)} }
        .ld { animation: pdot 1.5s infinite; }

        @keyframes fu    { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .fu { animation: fu .35s ease both; }

        @keyframes tin   { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

        @keyframes cheatFlash {
            0%,100% { border-color:#EF4444; box-shadow:0 0 0 3px rgba(239,68,68,.2); }
            50%      { border-color:#DC2626; box-shadow:0 0 0 6px rgba(239,68,68,.1); }
        }

        @keyframes spin  { from{transform:rotate(0deg)} to{transform:rotate(360deg)} }
        .spin { animation: spin .8s linear infinite; }

        @keyframes livePulse {
            0%   { box-shadow:0 0 0 0 rgba(16,185,129,.5); }
            70%  { box-shadow:0 0 0 6px rgba(16,185,129,0); }
            100% { box-shadow:0 0 0 0 rgba(16,185,129,0); }
        }
        .live-dot { animation: livePulse 1.5s infinite; }

        @keyframes cheatBadge { 0%,100%{background:#EF4444} 50%{background:#DC2626} }
        .cheat-badge { animation: cheatBadge .5s ease infinite; }

        /* ── STUDENT CARD ── */
        .scard {
            background: #fff;
            border-radius: 18px;
            border: 2px solid #F1F5F9;
            overflow: hidden;
            transition: all .25s;
            box-shadow: 0 1px 3px rgba(0,0,0,.05);
        }
        .scard:hover { box-shadow: 0 8px 24px rgba(0,0,0,.09); transform: translateY(-2px); }
        .scard.flagged { border-color: #EF4444 !important; animation: cheatFlash .8s ease infinite; }

        /* ── WEBCAM AREA ── */
        .webcam-wrap { position:relative; background:#0F172A; aspect-ratio:4/3; overflow:hidden; }
        .webcam-wrap img { width:100%; height:100%; object-fit:cover; transform:scaleX(-1); }

        /* ── TOAST ── */
        #tbox { position:fixed; bottom:22px; right:22px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
        .toast { display:flex; align-items:center; gap:9px; color:#fff; border-radius:14px; padding:11px 18px; font-size:12px; font-weight:700; box-shadow:0 10px 30px rgba(0,0,0,.22); animation:tin .3s ease; min-width:220px; pointer-events:auto; }
    </style>
</head>

<body class="bg-slate-100 text-slate-800 min-h-screen overflow-x-hidden">
<div class="flex h-screen overflow-hidden">

{{-- ═══════════════════════════════════════════════════════════
     SIDEBAR — matches all other teacher pages exactly
══════════════════════════════════════════════════════════════ --}}
@include('partials.teacher-sidebar')

{{-- ═══════════════════════════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════════════════════════════ --}}
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    {{-- ── PREMIUM GRADIENT HEADER ── --}}
    <div class="flex-shrink-0"
         style="background:linear-gradient(135deg,#0B1836 0%,#152C5E 55%,#1E3A8A 100%)">
        <div class="px-6 py-3.5 flex items-center gap-4 flex-wrap">

            {{-- Title --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2.5 flex-wrap">
                    <h1 class="text-[15px] font-black text-white tracking-tight">Live Proctoring Room</h1>
                    <span class="flex items-center gap-1.5 text-[10px] font-black text-emerald-300 px-2.5 py-1 rounded-lg"
                          style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.3)">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span> LIVE BROADCAST
                    </span>
                </div>
                <p class="text-[10px] text-white/50 mt-0.5">Real-time monitoring &amp; integrity enforcement</p>
            </div>

            {{-- Stats chips --}}
            <div class="hidden lg:flex items-center gap-2">
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <i class="fa-solid fa-users text-white/50 text-xs"></i>
                    <span class="text-[10px] font-bold text-white/50">Admitted</span>
                    <span class="text-[13px] font-black text-white tabular-nums" id="hAdmitted">0</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <i class="fa-solid fa-video text-emerald-400 text-xs"></i>
                    <span class="text-[10px] font-bold text-white/50">Active</span>
                    <span class="text-[13px] font-black text-emerald-400 tabular-nums" id="hActive">0</span>
                </div>
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     style="background:rgba(239,68,68,.15);border:1px solid rgba(239,68,68,.3)">
                    <i class="fa-solid fa-triangle-exclamation text-red-400 text-xs"></i>
                    <span class="text-[10px] font-bold text-red-300">Flagged</span>
                    <span class="text-[13px] font-black text-red-400 tabular-nums" id="hFlagged">0</span>
                </div>

                {{-- WebSocket connection status --}}
                <div id="wsChip" class="flex items-center gap-1.5 px-3 py-2 rounded-xl"
                     style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <span id="wsDot" class="w-1.5 h-1.5 rounded-full bg-amber-400 ld"></span>
                    <span id="wsLabel" class="text-[9px] font-black text-white/50 uppercase tracking-widest">WS…</span>
                </div>
            </div>

            {{-- Clock + Refresh + End --}}
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="px-3 py-2 rounded-xl hidden xl:block"
                     style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-0.5">Clock</p>
                    <p class="text-[12px] font-black text-white tabular-nums" id="lc">--:--:--</p>
                </div>

                <button onclick="loadPendingKeys(true)"
                        class="flex items-center gap-1.5 text-[11px] font-black px-3 py-2 rounded-xl transition-all"
                        style="background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.15);color:#fff"
                        title="Reload pending keys from server now">
                    <i class="fa-solid fa-arrows-rotate text-[10px]" id="refreshIcon"></i> Refresh
                </button>

                <button onclick="confirmEnd()"
                        class="flex items-center gap-1.5 text-[11px] font-black px-4 py-2 rounded-xl transition-all"
                        style="background:linear-gradient(135deg,#EF4444,#DC2626);color:#fff;box-shadow:0 4px 14px rgba(239,68,68,.4)">
                    <i class="fa-solid fa-circle-stop text-[10px]"></i> End Session
                </button>
            </div>
        </div>
    </div>

    {{-- ── CONTENT ROW ── --}}
    <div class="flex-1 flex overflow-hidden">

        {{-- ── WEBCAM GRID ── --}}
        <div class="flex-1 flex flex-col overflow-hidden">

            {{-- Sub-header: exam name + layout buttons --}}
            <div class="flex items-center justify-between px-5 py-3 bg-white border-b border-slate-100 flex-shrink-0">
                <div class="flex items-center gap-2">
                    <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Exam:</span>
                    <span class="text-[12px] font-black text-slate-900">{{ $exam->title ?? 'Active Exam Session' }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <span class="text-[9px] font-bold text-slate-400 mr-1">Grid:</span>
                    <button onclick="setLayout(2)" id="lay2" title="2 columns"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-table-columns text-xs"></i>
                    </button>
                    <button onclick="setLayout(3)" id="lay3" title="3 columns"
                            class="w-7 h-7 rounded-lg flex items-center justify-center bg-slate-100 text-slate-700 transition-colors">
                        <i class="fa-solid fa-border-all text-xs"></i>
                    </button>
                    <button onclick="setLayout(4)" id="lay4" title="4 columns"
                            class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                        <i class="fa-solid fa-th text-xs"></i>
                    </button>
                </div>
            </div>

            {{-- Grid / Empty state --}}
            <div class="flex-1 overflow-y-auto p-4">

                {{-- Empty state --}}
                <div id="emptyState" class="h-full flex flex-col items-center justify-center fu">
                    <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-4"
                         style="background:linear-gradient(135deg,#1E293B,#0F172A)">
                        <i class="fa-solid fa-video-slash text-3xl text-white/20"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-700 mb-1">No Active Streams</h3>
                    <p class="text-[11px] text-slate-400 font-medium text-center max-w-xs">
                        Approve student requests from the <strong>Pending Approvals</strong> panel on the right.
                    </p>
                    <div class="mt-4 flex items-center gap-2 text-[10px] font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-2 rounded-xl">
                        <i class="fa-solid fa-key text-xs"></i> Student keys appear in the right panel
                    </div>
                    <div class="mt-2 flex items-center gap-2 text-[10px] font-bold text-blue-700 bg-blue-50 border border-blue-200 px-3 py-2 rounded-xl">
                        <i class="fa-solid fa-circle-info text-xs"></i> Keys auto-load from server every 5 s
                    </div>
                </div>

                {{-- Live student grid --}}
                <div id="studentGrid" class="hidden grid grid-cols-3 gap-4"></div>

            </div>
        </div>

        {{-- ── RIGHT PANEL ── --}}
        <div class="w-72 flex-shrink-0 flex flex-col h-full bg-white border-l border-slate-100 overflow-hidden">

            {{-- Pending Approvals --}}
            <div class="flex-shrink-0">
                <div class="px-4 py-3.5 border-b border-slate-100"
                     style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-amber-600"
                                 style="background:#FEF3C7">
                                <i class="fa-solid fa-key text-xs"></i>
                            </div>
                            <span class="text-[12px] font-black text-slate-900">Pending Approvals</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-[9px] font-black text-white bg-amber-500 px-2 py-0.5 rounded-full"
                                  id="pendingCount">0</span>
                            <button onclick="loadPendingKeys(true)"
                                    class="w-5 h-5 rounded flex items-center justify-center text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors"
                                    title="Reload now">
                                <i class="fa-solid fa-rotate text-[9px]"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-1.5 flex items-center gap-1.5 text-[9px] font-bold text-slate-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span>
                        <span id="pollStatus">Checking server…</span>
                    </div>
                </div>

                <div class="p-3 space-y-2.5 max-h-80 overflow-y-auto" id="pendingBox">
                    <div id="noRequests"
                         class="flex flex-col items-center justify-center py-8 rounded-xl"
                         style="background:#FAFCFF;border:1.5px dashed #E2E8F0">
                        <i class="fa-solid fa-key text-2xl text-slate-300 mb-2"></i>
                        <p class="text-[10px] font-bold text-slate-400">Awaiting student requests…</p>
                        <p class="text-[9px] text-slate-300 mt-1">Auto-refreshes every 5 s</p>
                    </div>
                </div>
            </div>

            {{-- Live Exam Rules (read-only, admin-controlled, real-time) --}}
            <div class="flex-shrink-0 border-t border-slate-100">
                <div class="px-4 py-3.5"
                     style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600"
                                 style="background:#DBEAFE">
                                <i class="fa-solid fa-shield-halved text-xs"></i>
                            </div>
                            <span class="text-[12px] font-black text-slate-900">Live Exam Rules</span>
                        </div>
                        <span class="flex items-center gap-1 text-[9px] font-black text-blue-600">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 ld"></span> SYNCED
                        </span>
                    </div>
                    <p class="mt-1 text-[9px] text-slate-400 font-bold">Set by your admin — updates automatically</p>
                </div>
                <div class="p-3 space-y-1.5 text-[10px] font-bold text-slate-600" id="examRulesBox">
                    <div class="flex items-center justify-between"><span>Max tab switches</span><span id="ruleMaxSwitches" class="text-slate-900">—</span></div>
                    <div class="flex items-center justify-between"><span>Warning threshold</span><span id="ruleWarnThreshold" class="text-slate-900">—</span></div>
                    <div class="flex items-center justify-between"><span>Right-click blocked</span><span id="ruleRightClick" class="text-slate-900">—</span></div>
                    <div class="flex items-center justify-between"><span>Fullscreen enforced</span><span id="ruleFullscreen" class="text-slate-900">—</span></div>
                    <div class="flex items-center justify-between"><span>Webcam monitoring</span><span id="ruleWebcam" class="text-slate-900">—</span></div>
                </div>
            </div>

            {{-- Cheat Activity Log --}}
            <div class="flex-1 flex flex-col overflow-hidden border-t border-slate-100">
                <div class="px-4 py-3 flex-shrink-0"
                     style="background:linear-gradient(135deg,#FFF5F5,#FEF2F2);border-bottom:1px solid #FECACA">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center text-red-600"
                                 style="background:#FEE2E2">
                                <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                            </div>
                            <span class="text-[12px] font-black text-slate-900">Cheat Log</span>
                        </div>
                        <button onclick="clearCheatLog()"
                                class="text-[9px] font-black text-slate-400 hover:text-slate-600 transition-colors px-2 py-1 rounded-lg hover:bg-slate-100">
                            Clear
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-3 space-y-2" id="cheatLog">
                    <div id="noAlerts" class="flex flex-col items-center justify-center py-8">
                        <i class="fa-solid fa-shield-check text-2xl text-emerald-300 mb-2"></i>
                        <p class="text-[10px] font-bold text-slate-400">No alerts detected</p>
                    </div>
                </div>
            </div>

        </div>{{-- /right panel --}}
    </div>{{-- /content row --}}
</div>{{-- /main --}}
</div>{{-- /flex wrapper --}}

{{-- TOAST CONTAINER --}}
<div id="tbox"></div>

{{-- FULLSCREEN OVERLAY --}}
<div id="fsOverlay"
     class="hidden fixed inset-0 z-50 flex items-center justify-center p-6"
     style="background:rgba(15,23,42,.95);backdrop-filter:blur(6px)">
    <div class="relative w-full max-w-4xl rounded-2xl overflow-hidden shadow-2xl">
        <div class="flex items-center justify-between px-5 py-4" style="background:#0F172A">
            <div class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full bg-emerald-400 ld"></span>
                <span class="text-sm font-black text-white" id="fsName">Student Feed</span>
                <span class="text-[10px] font-mono font-bold text-white/40" id="fsKey"></span>
            </div>
            <button onclick="closeFS()"
                    class="w-8 h-8 rounded-xl flex items-center justify-center text-white/60 hover:text-white hover:bg-white/10 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div style="background:#000;aspect-ratio:4/3;overflow:hidden">
            <img id="fsFeed" src="" class="w-full h-full object-contain" style="transform:scaleX(-1)" alt="Live Feed">
        </div>
    </div>
</div>

{{-- WebSocket libraries --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>

<script>
// ═══════════════════════════════════════════════════════════════
// UTILITIES
// ═══════════════════════════════════════════════════════════════

// Live clock
(function tick(){
    const el = document.getElementById('lc');
    if (el) el.textContent = new Date().toLocaleTimeString('en-US', {hour12:false});
    setTimeout(tick, 1000);
})();

// Toast
function toast(msg, type = 'info') {
    const colors = { info:'#2563EB', success:'#10B981', warning:'#F59E0B', error:'#EF4444' };
    const icons  = { info:'fa-circle-info', success:'fa-circle-check', warning:'fa-triangle-exclamation', error:'fa-circle-xmark' };
    const box = document.getElementById('tbox');
    const el  = document.createElement('div');
    el.className   = 'toast';
    el.style.background = colors[type] || colors.info;
    el.innerHTML   = `<i class="fa-solid ${icons[type] || icons.info}"></i>${msg}`;
    box.appendChild(el);
    setTimeout(() => {
        el.style.transition  = 'all .3s';
        el.style.opacity     = '0';
        el.style.transform   = 'translateY(8px)';
        setTimeout(() => el.remove(), 300);
    }, 4000);
}

// ═══════════════════════════════════════════════════════════════
// STATE
// ═══════════════════════════════════════════════════════════════
let onlineUsers  = {};   // { student_id: { id, name, key } }
let cheatCounts  = {};   // { student_id: number }
let flaggedCount = 0;
let gridCols     = 3;

// ═══════════════════════════════════════════════════════════════
// WS STATUS INDICATOR
// ═══════════════════════════════════════════════════════════════
function setWSStatus(state) {
    const dot = document.getElementById('wsDot');
    const lbl = document.getElementById('wsLabel');
    if (!dot || !lbl) return;
    const map = {
        connecting:   ['#F59E0B', 'WS…'],
        connected:    ['#10B981', 'WS OK'],
        disconnected: ['#EF4444', 'WS OFF'],
        failed:       ['#EF4444', 'WS ERR'],
    };
    const [color, text] = map[state] || ['#64748B', 'WS?'];
    dot.style.background = color;
    lbl.textContent      = text;
    if (state === 'disconnected' || state === 'failed') {
        toast('WebSocket disconnected — HTTP polling still active', 'warning');
    }
}

// ═══════════════════════════════════════════════════════════════
// GRID LAYOUT SWITCHER
// ═══════════════════════════════════════════════════════════════
function setLayout(n) {
    gridCols = n;
    const g  = document.getElementById('studentGrid');
    g.className = `grid gap-4 grid-cols-${n}`;
    ['lay2','lay3','lay4'].forEach(id => {
        const btn = document.getElementById(id);
        if (!btn) return;
        btn.className = btn.id === `lay${n}`
            ? 'w-7 h-7 rounded-lg flex items-center justify-center bg-slate-100 text-slate-700 transition-colors'
            : 'w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors';
    });
}

// ═══════════════════════════════════════════════════════════════
// COUNTER BAR
// ═══════════════════════════════════════════════════════════════
function updateCounters() {
    const cnt = Object.keys(onlineUsers).length;
    document.getElementById('hAdmitted').textContent = cnt;
    document.getElementById('hActive').textContent   = cnt;
    document.getElementById('hFlagged').textContent  = flaggedCount;
    const grid  = document.getElementById('studentGrid');
    const empty = document.getElementById('emptyState');
    if (cnt === 0) { grid.classList.add('hidden'); empty.classList.remove('hidden'); }
    else           { empty.classList.add('hidden'); grid.classList.remove('hidden'); }
}

// ═══════════════════════════════════════════════════════════════
// PENDING PANEL
// ═══════════════════════════════════════════════════════════════
function updatePendingCount() {
    const n = document.querySelectorAll('[id^="krow_"]').length;
    document.getElementById('pendingCount').textContent = n;
    document.getElementById('noRequests').classList.toggle('hidden', n > 0);
}

// Inject a key request card (idempotent — won't duplicate)
function injectKeyRequest(data, silent = false) {
    if (!data.proctor_key) return;
    if (document.getElementById(`krow_${data.proctor_key}`)) return;

    const name = data.student_name || 'Unknown';
    const sid  = data.student_id   || '?';
    const initials = name.split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();

    const html = `
        <div id="krow_${data.proctor_key}" class="rounded-xl overflow-hidden fu"
             style="border:1.5px solid #FDE68A;background:#FFFBEB">

            <div class="flex items-center justify-between px-3 py-2.5"
                 style="border-bottom:1px solid #FDE68A">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-amber-700 text-[10px] font-black"
                         style="background:#FEF3C7">${initials}</div>
                    <div>
                        <p class="text-[11px] font-black text-slate-900 leading-tight">${name}</p>
                        <p class="text-[9px] font-mono text-slate-400">#${sid}</p>
                    </div>
                </div>
                {{-- The big PR key badge --}}
                <span class="text-[13px] font-black text-amber-800 px-2.5 py-1 rounded-lg tracking-widest"
                      style="background:#FDE68A;letter-spacing:.1em">${data.proctor_key}</span>
            </div>

            <div class="px-3 py-2.5 flex gap-2">
                <button id="admitBtn_${data.proctor_key}"
                        onclick="admitStudent('${data.proctor_key}','${sid}','${name.replace(/'/g,"\\'")}')"
                        class="flex-1 flex items-center justify-center gap-1.5 text-[10px] font-black py-2 rounded-lg text-white transition-all"
                        style="background:linear-gradient(135deg,#2563EB,#1E40AF);box-shadow:0 2px 8px rgba(37,99,235,.3)">
                    <i class="fa-solid fa-circle-check text-[9px]"></i> Admit Student
                </button>
                <button onclick="denyStudent('${data.proctor_key}')"
                        class="flex items-center justify-center gap-1 text-[10px] font-bold py-2 px-2.5 rounded-lg transition-all"
                        style="background:#FEF2F2;color:#EF4444;border:1px solid #FECACA">
                    <i class="fa-solid fa-xmark text-[9px]"></i>
                </button>
            </div>
        </div>`;

    document.getElementById('pendingBox').insertAdjacentHTML('afterbegin', html);
    updatePendingCount();
    if (!silent) toast(`🔑 New request: ${name} · ${data.proctor_key}`, 'warning');
}

// ═══════════════════════════════════════════════════════════════
// HTTP POLLING  ← THE CORE FIX
// Fetches ALL pending keys from the server DB/cache every 5s.
// This ensures the teacher never misses a key that was registered
// before the monitoring page was opened.
// ═══════════════════════════════════════════════════════════════
async function loadPendingKeys(manual = false) {
    const icon = document.getElementById('refreshIcon');
    if (manual && icon) icon.classList.add('spin');

    try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
        const res  = await fetch('{{ route("teacher.monitoring.pending-keys") }}', {
            headers: {
                'Accept':           'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN':     csrf,
            },
            credentials: 'same-origin',
        });

        if (!res.ok) {
            document.getElementById('pollStatus').textContent = `Server error ${res.status}`;
            console.warn('[Monitoring] pending-keys →', res.status);
            return;
        }

        const list = await res.json();
        const now  = new Date().toLocaleTimeString('en-US', {hour12:false,hour:'2-digit',minute:'2-digit',second:'2-digit'});
        document.getElementById('pollStatus').textContent =
            `Updated ${now} · ${Array.isArray(list) ? list.length : 0} pending`;

        if (Array.isArray(list)) {
            list.forEach(item => injectKeyRequest({
                proctor_key:  item.proctor_key  || item.proctorKey  || item.key,
                student_id:   item.student_id   || item.studentId   || item.id,
                student_name: item.student_name || item.studentName || item.name || 'Unknown',
            }, true));

            if (manual) {
                if (list.length > 0) toast(`Loaded ${list.length} pending request(s)`, 'success');
                else                 toast('No pending requests on server', 'info');
            }
        }
    } catch (e) {
        console.error('[Monitoring] loadPendingKeys failed:', e);
        document.getElementById('pollStatus').textContent = 'Could not reach server';
    } finally {
        if (manual && icon) icon.classList.remove('spin');
    }
}

// Auto-poll every 5 seconds
setInterval(() => loadPendingKeys(false), 5000);

// ═══════════════════════════════════════════════════════════════
// LIVE EXAM RULES (read-only, admin-controlled, real-time)
// ═══════════════════════════════════════════════════════════════
let rulesPollMs = 10000;
async function loadExamRules() {
    try {
        const res = await fetch('{{ route("exam.rules.live") }}');
        const data = await res.json();
        document.getElementById('ruleMaxSwitches').textContent   = data.proctor_max_switches;
        document.getElementById('ruleWarnThreshold').textContent = data.proctor_warn_threshold;
        document.getElementById('ruleRightClick').textContent    = data.block_right_click ? 'Yes' : 'No';
        document.getElementById('ruleFullscreen').textContent    = data.force_fullscreen ? 'Yes' : 'No';
        document.getElementById('ruleWebcam').textContent        = data.webcam_monitor ? 'Yes' : 'No';
        rulesPollMs = Math.max(data.sync_interval, 5) * 1000;
    } catch (e) {
        console.debug('[Monitoring] loadExamRules failed:', e);
    }
}
function scheduleRulesPoll() {
    loadExamRules().finally(() => setTimeout(scheduleRulesPoll, rulesPollMs));
}
scheduleRulesPoll();

// ═══════════════════════════════════════════════════════════════
// ADMIT / DENY
// ═══════════════════════════════════════════════════════════════
function admitStudent(key, studentId, studentName) {
    const btn  = document.getElementById(`admitBtn_${key}`);
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';
    if (btn) { btn.innerHTML = '<i class="fa-solid fa-circle-notch spin text-[9px]"></i> Admitting…'; btn.disabled = true; }

    fetch('{{ route("teacher.monitoring.approveKey") }}', {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrf },
        body: JSON.stringify({ proctor_key: key, student_id: studentId, student_name: studentName }),
    })
    .then(r => r.ok ? r.json() : Promise.reject(r.status))
    .then(() => {
        document.getElementById(`krow_${key}`)?.remove();
        updatePendingCount();
        const user = { id: studentId, name: studentName, key };
        onlineUsers[studentId] = user;
        createCard(user);
        updateCounters();
        toast(`✅ ${studentName} admitted`, 'success');
    })
    .catch(err => {
        toast(`Failed to admit (${err})`, 'error');
        if (btn) { btn.innerHTML = '<i class="fa-solid fa-circle-check text-[9px]"></i> Admit Student'; btn.disabled = false; }
    });
}

function denyStudent(key) {
    document.getElementById(`krow_${key}`)?.remove();
    updatePendingCount();
    toast('Request denied', 'info');
}

// ═══════════════════════════════════════════════════════════════
// STUDENT WEBCAM CARD
// ═══════════════════════════════════════════════════════════════
function cardId(sid) { return `card_${String(sid).replace(/[^a-zA-Z0-9]/g, '_')}`; }

function createCard(user) {
    const cid = cardId(user.id);
    if (document.getElementById(cid)) return;

    const initials = (user.name || '?').trim().split(' ').map(w => w[0]).slice(0,2).join('').toUpperCase();
    const palette  = ['#2563EB','#10B981','#8B5CF6','#F59E0B','#EF4444','#06B6D4'];
    let h = 0;
    for (const c of (user.name || 'X')) h = c.charCodeAt(0) + ((h << 5) - h);
    const bg = palette[Math.abs(h) % palette.length];

    const safeName = (user.name || '').replace(/'/g, "\\'");
    const html = `
        <div id="${cid}" class="scard fu">

            {{-- WEBCAM AREA --}}
            <div class="webcam-wrap" id="wc_${cid}">

                {{-- LIVE badge --}}
                <div class="absolute top-2 left-2 z-20">
                    <span id="liveBadge_${cid}"
                          class="flex items-center gap-1 text-[9px] font-black text-white px-2 py-0.5 rounded-md live-dot"
                          style="background:#10B981">
                        <span class="w-1.5 h-1.5 rounded-full bg-white ld"></span> LIVE
                    </span>
                </div>

                {{-- PR key overlay --}}
                <div class="absolute top-2 right-2 z-20">
                    <span class="text-[10px] font-black text-white font-mono px-2 py-0.5 rounded-md tracking-widest"
                          style="background:rgba(0,0,0,.6)">${user.key}</span>
                </div>

                {{-- Cheat badge (shown on violation) --}}
                <div id="cheatBadge_${cid}" class="hidden absolute top-8 left-2 z-20">
                    <span class="flex items-center gap-1 text-[9px] font-black text-white px-2 py-0.5 rounded-md cheat-badge">
                        <i class="fa-solid fa-triangle-exclamation text-[8px]"></i>
                        <span id="cheatText_${cid}">VIOLATION</span>
                    </span>
                </div>

                {{-- Violation count --}}
                <div id="cheatCount_${cid}" class="hidden absolute bottom-2 left-2 z-20">
                    <span class="text-[9px] font-black text-white px-2 py-0.5 rounded-md"
                          style="background:rgba(239,68,68,.85)">
                        ⚠ <span id="cheatNum_${cid}">0</span> violations
                    </span>
                </div>

                {{-- Loading placeholder --}}
                <div id="placeholder_${cid}"
                     class="absolute inset-0 flex flex-col items-center justify-center gap-2"
                     style="background:#1E293B">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center text-white text-base font-black"
                         style="background:${bg}">${initials}</div>
                    <div class="flex items-center gap-1.5 text-[10px] font-bold text-white/50">
                        <i class="fa-solid fa-circle-notch spin text-xs text-blue-400"></i>
                        Awaiting stream…
                    </div>
                </div>

                {{-- Live webcam image (updated every frame) --}}
                <img id="feed_${cid}" src="" class="hidden w-full h-full object-cover"
                     style="transform:scaleX(-1)" alt="Live Feed">

                {{-- Fullscreen button --}}
                <button onclick="openFS('${cid}','${safeName}','${user.key}')"
                        class="absolute bottom-2 right-2 z-20 w-7 h-7 rounded-lg flex items-center justify-center text-white opacity-0 hover:opacity-100 transition-all"
                        style="background:rgba(0,0,0,.5)">
                    <i class="fa-solid fa-expand text-[10px]"></i>
                </button>
            </div>

            {{-- CARD FOOTER --}}
            <div class="flex items-center justify-between px-3.5 py-2.5 bg-white">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-[10px] font-black flex-shrink-0"
                         style="background:${bg}">${initials}</div>
                    <div class="min-w-0">
                        <p class="text-[11px] font-black text-slate-900 truncate">${user.name}</p>
                        <p class="text-[9px] font-mono text-slate-400">#${user.id}</p>
                    </div>
                </div>
                <div class="flex items-center gap-1">
                    <button onclick="flagStudent('${cid}','${safeName}')"
                            class="flex items-center gap-1 text-[9px] font-black px-2 py-1 rounded-lg"
                            style="background:#FEF2F2;color:#EF4444;border:1px solid #FECACA">
                        <i class="fa-solid fa-flag text-[8px]"></i> Flag
                    </button>
                    <button onclick="openFS('${cid}','${safeName}','${user.key}')"
                            class="flex items-center gap-1 text-[9px] font-bold px-2 py-1 rounded-lg"
                            style="background:#EFF6FF;color:#2563EB;border:1px solid #BFDBFE">
                        <i class="fa-solid fa-expand text-[8px]"></i>
                    </button>
                </div>
            </div>
        </div>`;

    document.getElementById('studentGrid').insertAdjacentHTML('beforeend', html);
    document.getElementById('studentGrid').className = `grid gap-4 grid-cols-${gridCols}`;
}

// ═══════════════════════════════════════════════════════════════
// CHEAT DETECTION
// ═══════════════════════════════════════════════════════════════
function flagStudent(cid, name, reason = 'Manual flag by proctor') {
    const card = document.getElementById(cid);
    if (!card) return;
    card.classList.add('flagged');
    document.getElementById(`cheatBadge_${cid}`)?.classList.remove('hidden');
    document.getElementById(`cheatCount_${cid}`)?.classList.remove('hidden');
    flaggedCount++;
    updateCounters();
    logCheat(name, reason);
    toast(`⚠ ${name} flagged`, 'error');
}

function markCheat(studentId, reason = 'Violation detected') {
    const user = onlineUsers[studentId];
    if (!user) return;
    const cid = cardId(studentId);
    cheatCounts[studentId] = (cheatCounts[studentId] || 0) + 1;

    const card = document.getElementById(cid);
    if (card && !card.classList.contains('flagged')) {
        card.classList.add('flagged');
        flaggedCount++;
        updateCounters();
    }

    document.getElementById(`cheatBadge_${cid}`)?.classList.remove('hidden');
    document.getElementById(`cheatCount_${cid}`)?.classList.remove('hidden');
    const ne = document.getElementById(`cheatNum_${cid}`);   if (ne) ne.textContent = cheatCounts[studentId];
    const te = document.getElementById(`cheatText_${cid}`);  if (te) te.textContent = reason.toUpperCase().slice(0, 12);

    logCheat(user.name, reason);
    toast(`⚠ ${user.name}: ${reason}`, 'error');
}

// ═══════════════════════════════════════════════════════════════
// CHEAT LOG
// ═══════════════════════════════════════════════════════════════
function logCheat(name, reason) {
    document.getElementById('noAlerts')?.classList.add('hidden');
    const t = new Date().toLocaleTimeString('en-US', {hour12:false,hour:'2-digit',minute:'2-digit',second:'2-digit'});
    document.getElementById('cheatLog').insertAdjacentHTML('afterbegin', `
        <div class="flex gap-2.5 p-2.5 rounded-xl fu" style="background:#FEF2F2;border:1px solid #FECACA">
            <div class="w-6 h-6 rounded-lg flex items-center justify-center flex-shrink-0 text-red-600 mt-0.5 text-[9px]"
                 style="background:#FEE2E2"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <div class="min-w-0">
                <p class="text-[10px] font-black text-red-900 truncate">${name}</p>
                <p class="text-[9px] font-semibold text-red-600">${reason}</p>
                <p class="text-[8px] font-mono text-red-400 mt-0.5">${t}</p>
            </div>
        </div>`);
}

function clearCheatLog() {
    document.getElementById('cheatLog').innerHTML = `
        <div id="noAlerts" class="flex flex-col items-center justify-center py-8">
            <i class="fa-solid fa-shield-check text-2xl text-emerald-300 mb-2"></i>
            <p class="text-[10px] font-bold text-slate-400">No alerts detected</p>
        </div>`;
}

// ═══════════════════════════════════════════════════════════════
// FRAME UPDATE (called each time student sends a webcam frame)
// ═══════════════════════════════════════════════════════════════
function updateFrame(studentId, imageData) {
    const cid    = cardId(studentId);
    const feed   = document.getElementById(`feed_${cid}`);
    const ph     = document.getElementById(`placeholder_${cid}`);
    const fsFeed = document.getElementById('fsFeed');
    if (feed && ph) {
        feed.src = imageData;
        feed.classList.remove('hidden');
        ph.classList.add('hidden');
        if (fsFeed && fsFeed.dataset.cid === cid) fsFeed.src = imageData;
    }
}

// ═══════════════════════════════════════════════════════════════
// FULLSCREEN OVERLAY
// ═══════════════════════════════════════════════════════════════
function openFS(cid, name, key) {
    const feed   = document.getElementById(`feed_${cid}`);
    const fsFeed = document.getElementById('fsFeed');
    if (fsFeed) { fsFeed.src = feed?.src || ''; fsFeed.dataset.cid = cid; }
    document.getElementById('fsName').textContent = name;
    document.getElementById('fsKey').textContent  = key;
    document.getElementById('fsOverlay').classList.remove('hidden');
}
function closeFS() { document.getElementById('fsOverlay').classList.add('hidden'); }
document.getElementById('fsOverlay').addEventListener('click', function (e) {
    if (e.target === this) closeFS();
});

// ═══════════════════════════════════════════════════════════════
// END SESSION
// ═══════════════════════════════════════════════════════════════
function confirmEnd() {
    if (confirm('⚠ End this exam session? All student feeds will be disconnected.')) {
        window.location.href = "{{ route('teacher.monitoring.endConfirmation') }}";
    }
}

// ═══════════════════════════════════════════════════════════════
// WEBSOCKET INIT (Laravel Reverb via Pusher protocol)
// ═══════════════════════════════════════════════════════════════
function initWS() {
    setWSStatus('connecting');
    window.Pusher = Pusher;
    window.Echo   = new Echo({
        broadcaster: 'pusher',
        key:         '{{ config("broadcasting.connections.reverb.key", "examsystemkeyabc123") }}',
        wsHost:      '{{ config("broadcasting.connections.reverb.options.host", "127.0.0.1") }}',
        wsPort:       {{ config('broadcasting.connections.reverb.options.port', 8080) }},
        wssPort:      {{ config('broadcasting.connections.reverb.options.port', 8080) }},
        cluster:     'mt1',
        forceTLS:    false,
        encrypted:   false,
        disableStats: true,
        enabledTransports: ['ws', 'wss'],
    });

    // Connection lifecycle → updates the WS chip in the header
    window.Echo.connector.pusher.connection.bind('connected',    () => { setWSStatus('connected');    console.log('[WS] Connected'); });
    window.Echo.connector.pusher.connection.bind('disconnected', () => { setWSStatus('disconnected'); console.warn('[WS] Disconnected'); });
    window.Echo.connector.pusher.connection.bind('failed',       () => { setWSStatus('failed');       console.error('[WS] Failed'); });

    // ── HANDSHAKE CHANNEL ──────────────────────────────────────────────────
    // Student registered a new proctor key (real-time)
    window.Echo.channel('exam-room-handshake').listen('.ProctorKeyRegistered', data => {
        console.log('[WS] ProctorKeyRegistered:', data);
        injectKeyRequest(data, false);
    });

    // Teacher approved a key on another tab / broadcast echoed back
    window.Echo.channel('exam-room-handshake').listen('.ProctorKeyApproved', data => {
        if (!onlineUsers[data.student_id]) {
            const user = { id: data.student_id, name: data.student_name, key: data.proctor_key };
            onlineUsers[data.student_id] = user;
            createCard(user);
            updateCounters();
            document.getElementById(`krow_${data.proctor_key}`)?.remove();
            updatePendingCount();
        }
    });

    // ── MONITORING CHANNEL ─────────────────────────────────────────────────
    // Live webcam frame from student
    window.Echo.channel('exam-monitoring').listen('.StudentFrameSubmitted', data => {
        if (!onlineUsers[data.student_id]) {
            const user = { id: data.student_id, name: data.student_name || 'Unknown', key: data.proctor_key || '—' };
            onlineUsers[data.student_id] = user;
            createCard(user);
            updateCounters();
        }
        updateFrame(data.student_id, data.image_frame);
    });

    // Tab switch detected on student machine
    window.Echo.channel('exam-monitoring').listen('.StudentTabSwitched', data => {
        markCheat(data.student_id, 'Tab switch detected');
    });

    // Student disabled their camera
    window.Echo.channel('exam-monitoring').listen('.StudentCameraOff', data => {
        markCheat(data.student_id, 'Camera disabled');
        const cid  = cardId(data.student_id);
        const ph   = document.getElementById(`placeholder_${cid}`);
        const feed = document.getElementById(`feed_${cid}`);
        if (ph) {
            ph.classList.remove('hidden');
            ph.innerHTML = `
                <div class="w-10 h-10 rounded-xl flex items-center justify-center mb-2"
                     style="background:#EF4444">
                    <i class="fa-solid fa-video-slash text-white text-base"></i>
                </div>
                <p class="text-[10px] font-bold text-red-400">Camera Disabled</p>`;
        }
        if (feed) feed.classList.add('hidden');
    });

    // Student disconnected entirely
    window.Echo.channel('exam-monitoring').listen('.StudentDisconnected', data => {
        const cid   = cardId(data.student_id);
        const badge = document.getElementById(`liveBadge_${cid}`);
        if (badge) {
            badge.style.background = '#64748B';
            const dot = badge.querySelector('span');
            if (dot) dot.textContent = 'OFFLINE';
        }
        markCheat(data.student_id, 'Student disconnected');
    });
}

// ═══════════════════════════════════════════════════════════════
// BOOT
// ═══════════════════════════════════════════════════════════════
window.addEventListener('DOMContentLoaded', () => {
    initWS();            // Start WebSocket
    loadPendingKeys();   // Immediately fetch any already-pending keys via HTTP
});
</script>
</body>
</html>