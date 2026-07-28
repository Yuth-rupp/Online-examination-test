<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Exams Oversight — ExamSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { fontFamily: { sans:['Inter','sans-serif'], mono:['JetBrains Mono','monospace'] } } }
        }
    </script>
    <style>
        @keyframes ping-slow { 75%,100%{transform:scale(2.2);opacity:0} }
        .ping-slow { animation: ping-slow 2s cubic-bezier(0,0,.2,1) infinite; }
        @keyframes countUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .count-animate { animation: countUp 0.4s ease-out forwards; }
        @keyframes fadeIn { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-in { opacity:0; animation: fadeIn 0.35s ease-out forwards; }
        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}
        #force-end-modal { display:none; }
        #force-end-modal.open { display:flex; }
        .toast-enter { opacity:0; transform:translateY(12px); transition:opacity 0.3s, transform 0.3s; }
        .toast-visible { opacity:1; transform:translateY(0); }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 font-sans antialiased" style="font-family:'Inter',sans-serif;">
<div class="flex min-h-screen">
    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-20" style="box-shadow:4px 0 24px rgba(148,163,184,0.08);">
        <div class="h-16 flex items-center px-5 gap-3 border-b border-slate-100 flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0" style="box-shadow:0 4px 14px rgba(59,130,246,0.45);"><i class="fa-solid fa-graduation-cap text-white text-sm"></i></div>
            <div>
                <h1 class="font-extrabold text-slate-900 text-sm tracking-tight leading-none">ExamSystem</h1>
                <div class="flex items-center gap-1.5 mt-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span><span class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest">Super Admin</span></div>
            </div>
        </div>
        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-gauge-high text-xs text-slate-400"></i></span><span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-desktop text-xs text-slate-400"></i></span>
                <span class="flex-1">Live Monitoring</span>
                <span class="text-[9px] bg-rose-100 text-rose-600 font-bold px-2 py-0.5 rounded-full animate-pulse">LIVE</span>
            </a>
            <a href="{{ route('superadmin.exams.index') }}" class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-0.5 transition-all duration-200" style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0"><i class="fa-solid fa-file-signature text-xs text-white"></i></span><span>Exams Oversight</span>
            </a>
            <a href="{{ route('superadmin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-chart-line text-xs text-slate-400"></i></span><span>Reports & Analytics</span>
            </a>
            <div class="pt-4 pb-1"><p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2">Root Access</p></div>
            <a href="{{ route('superadmin.admins.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-users text-xs text-slate-400"></i></span><span>User Management</span>
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
        {{-- TOP BAR --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Exams Oversight</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Monitor, manage, and intervene on all examination sessions</p>
            </div>
            <div class="flex items-center gap-3 ml-auto">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock">--:--:--</span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-100 px-3 py-1.5 rounded-lg">
                    <i id="refresh-icon" class="fa-solid fa-rotate text-slate-300 text-xs"></i>
                    <span>Refresh in</span>
                    <span id="refresh-countdown" class="font-mono font-bold text-slate-700 w-4 text-center">15</span><span>s</span>
                </div>
            </div>
        </header>
        {{-- PAGE BODY --}}
        <div class="p-8 flex-1" style="display:flex;flex-direction:column;gap:28px;">
            {{-- ========== METRIC CARDS ========== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
                {{-- Total Exams --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-file-lines text-blue-500 text-sm"></i></div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Total Exams</p>
                        <p id="m-total" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $totalExams }}</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $totalExams === 0 ? 'No exams created yet' : 'All exams in the system' }}</p>
                    </div>
                </div>
                {{-- Active --}}
                <div class="bg-white rounded-2xl border {{ $activeExams > 0 ? 'border-emerald-100' : 'border-slate-100' }} p-5 flex flex-col gap-3 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-bolt text-emerald-500 text-sm"></i></div>
                        @if($activeExams > 0)
                        <span class="text-[10px] font-bold text-emerald-600 bg-emerald-50 border border-emerald-100 px-2 py-0.5 rounded-full flex items-center gap-1 animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span> Running
                        </span>
                        @else
                        <span class="text-[10px] font-bold text-slate-400 bg-slate-50 border border-slate-100 px-2 py-0.5 rounded-full">Idle</span>
                        @endif
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Active</p>
                        <p id="m-active" class="text-3xl font-black {{ $activeExams > 0 ? 'text-emerald-600' : 'text-slate-900' }} leading-none tabular-nums count-animate">{{ $activeExams }}</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $activeExams === 0 ? 'No exams running' : 'Currently in progress' }}</p>
                    </div>
                </div>
                {{-- Completed --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 bg-violet-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-check-double text-violet-500 text-sm"></i></div>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Completed</p>
                        <p id="m-completed" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $completedExams }}</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">Finished exams</p>
                    </div>
                </div>
                {{-- Flag Rate --}}
                <div class="bg-white rounded-2xl border border-slate-100 p-5 flex flex-col gap-3 transition-all duration-300 hover:-translate-y-0.5 cursor-default"
                     style="box-shadow:0 1px 4px rgba(148,163,184,0.06);" onmouseenter="this.style.boxShadow='0 8px 24px rgba(148,163,184,0.18)'" onmouseleave="this.style.boxShadow='0 1px 4px rgba(148,163,184,0.06)'">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 {{ $avgFlagRate > 5 ? 'bg-rose-50' : 'bg-amber-50' }} rounded-xl flex items-center justify-center">
                            <i class="fa-solid fa-flag {{ $avgFlagRate > 5 ? 'text-rose-500' : 'text-amber-500' }} text-sm"></i>
                        </div>
                        <span id="flag-badge" class="text-[10px] font-bold px-2 py-0.5 rounded-full border
                            {{ $avgFlagRate == 0 ? 'text-emerald-600 bg-emerald-50 border-emerald-100' : ($avgFlagRate > 5 ? 'text-rose-600 bg-rose-50 border-rose-100' : 'text-amber-600 bg-amber-50 border-amber-100') }}">
                            {{ $avgFlagRate == 0 ? 'Clean' : ($avgFlagRate > 5 ? 'Elevated' : 'Normal') }}
                        </span>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-widest mb-1">Flag Rate</p>
                        <p id="m-flag" class="text-3xl font-black text-slate-900 leading-none tabular-nums count-animate">{{ $avgFlagRate }}%</p>
                        <p class="text-[11px] text-slate-400 mt-1.5">{{ $avgFlagRate == 0 ? 'No flagged sessions' : 'Of all exam sessions' }}</p>
                    </div>
                </div>
            </div>
            {{-- ========== STUCK EXAMS ALERT ========== --}}
            @if(isset($stuckExams) && $stuckExams->count() > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl px-5 py-4 flex items-start gap-3">
                <div class="w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-amber-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-amber-900 mb-0.5">{{ $stuckExams->count() }} Stuck Exam(s) Detected</p>
                    <p class="text-[11px] text-amber-700 font-medium">These exams have been active for 15+ minutes without any updates. Consider force-ending them.</p>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    @foreach($stuckExams as $stuck)
                    @php $stuckId = $stuck->exam_id ?? $stuck->id; @endphp
                    <button onclick="openForceEnd('{{ $stuckId }}', '{{ addslashes($stuck->title ?? $stuck->name ?? 'Exam #'.$stuckId) }}')"
                            class="text-[10px] font-bold bg-amber-600 text-white px-3 py-1.5 rounded-lg hover:bg-amber-700 transition-all">
                        Force End #{{ Str::limit($stuckId, 8, '') }}
                    </button>
                    @endforeach
                </div>
            </div>
            @endif
            {{-- ========== EXAMS TABLE ========== --}}
            @php $examList = $exams ?? $allExams ?? collect(); @endphp
            <div class="bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-table-list text-blue-500 text-sm"></i></div>
                        <div>
                            <h3 class="font-bold text-sm text-slate-900">All Exams</h3>
                            <p class="text-[11px] text-slate-400 font-medium">Complete list of examinations in the system</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-100 px-2.5 py-0.5 rounded-full">{{ $examList->count() }} total</span>
                </div>
                @if($examList->count() === 0)
                <div class="flex flex-col items-center justify-center py-20 px-6 text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fa-solid fa-file-circle-plus text-slate-300 text-2xl"></i>
                    </div>
                    <h5 class="text-sm font-bold text-slate-400 mb-1">No Exams Yet</h5>
                    <p class="text-xs text-slate-300 max-w-xs">Exams will appear here once teachers or admins create them. All exam data is pulled from the database in real time.</p>
                </div>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left" style="border-collapse:collapse;">
                        <thead>
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <th class="px-6 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">ID</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Exam Name</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Sessions</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Flagged</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest">Created</th>
                                <th class="px-4 py-3 text-[10px] font-bold text-slate-400 uppercase tracking-widest text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            @foreach($examList as $i => $exam)
                            @php
                                $examId = $exam->exam_id ?? $exam->id;
                                // ✅ `exams.status` in the database only ever holds
                                // 'draft'/'published'/'ended' — it is never the literal
                                // string 'active' or 'completed'. Use the derived status
                                // the controller computed (effective_status) so the badge
                                // and Force End button reflect what's actually happening,
                                // instead of silently falling through to the default style
                                // for every single published exam.
                                $status = $exam->effective_status ?? ($exam->status ?? 'draft');
                                $stColors = match($status) {
                                    'active'    => ['bg'=>'bg-emerald-50','text'=>'text-emerald-600','border'=>'border-emerald-100'],
                                    'completed','ended' => ['bg'=>'bg-blue-50','text'=>'text-blue-600','border'=>'border-blue-100'],
                                    'draft'     => ['bg'=>'bg-slate-50','text'=>'text-slate-500','border'=>'border-slate-100'],
                                    'scheduled' => ['bg'=>'bg-amber-50','text'=>'text-amber-600','border'=>'border-amber-100'],
                                    default     => ['bg'=>'bg-slate-50','text'=>'text-slate-500','border'=>'border-slate-100'],
                                };
                            @endphp
                            <tr class="fade-in hover:bg-slate-50 transition-colors" style="animation-delay:{{ $i * 0.04 }}s;">
                                {{-- Line 240 Fix --}}
                                <td class="px-6 py-3.5 font-mono text-xs text-slate-400 font-semibold" title="{{ $examId }}">
                                    #{{ Str::limit($examId, 8, '') }}
                                </td>
                                <td class="px-4 py-3.5">
                                    <p class="text-[13px] font-bold text-slate-800 truncate" style="max-width:260px;">{{ $exam->title ?? $exam->name ?? 'Untitled Exam' }}</p>
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    <span class="text-[10px] font-bold uppercase tracking-wide px-2.5 py-1 rounded-md border {{ $stColors['bg'] }} {{ $stColors['text'] }} {{ $stColors['border'] }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3.5 text-center font-mono text-xs text-slate-600 font-semibold">
                                    {{ $exam->session_count ?? $exam->sessions_count ?? 0 }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if(($exam->flagged_count ?? 0) > 0)
                                    <span class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 px-2 py-0.5 rounded-full">{{ $exam->flagged_count }}</span>
                                    @else
                                    <span class="text-xs text-slate-300">0</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-400 font-medium">
                                    {{ $exam->created_at ? \Carbon\Carbon::parse($exam->created_at)->format('M j, Y') : '—' }}
                                </td>
                                <td class="px-4 py-3.5 text-center">
                                    @if($status === 'active')
                                    <button onclick="openForceEnd('{{ $examId }}', '{{ addslashes($exam->title ?? $exam->name ?? 'Exam #'.$examId) }}')"
                                            class="text-[10px] font-bold text-rose-600 bg-rose-50 border border-rose-100 px-2.5 py-1 rounded-lg hover:bg-rose-100 transition-all">
                                        <i class="fa-solid fa-stop mr-1"></i>Force End
                                    </button>
                                    @else
                                    <span class="text-xs text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
            {{-- ========== DEPARTMENT BREAKDOWN ========== --}}
            @if(isset($departments) && count($departments) > 0)
            <div class="bg-white rounded-2xl border border-slate-100" style="box-shadow:0 1px 4px rgba(148,163,184,0.06);">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-violet-50 flex items-center justify-center"><i class="fa-solid fa-building-columns text-violet-500 text-sm"></i></div>
                        <h3 class="font-bold text-sm text-slate-900">Department Breakdown</h3>
                    </div>
                </div>
                <div class="p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($departments as $dept)
                    <div class="rounded-xl border border-slate-100 p-4 hover:shadow-md hover:-translate-y-0.5 transition-all cursor-default">
                        <p class="text-[13px] font-bold text-slate-800 mb-3">{{ $dept->department ?? $dept->name ?? 'Department' }}</p>
                        <div class="flex gap-3">
                            <div class="flex-1 text-center bg-blue-50 rounded-lg py-2.5">
                                <p class="text-lg font-extrabold text-blue-600 leading-none">{{ $dept->exam_count ?? 0 }}</p>
                                <p class="text-[9px] font-semibold text-blue-300 mt-1">Exams</p>
                            </div>
                            <div class="flex-1 text-center bg-emerald-50 rounded-lg py-2.5">
                                <p class="text-lg font-extrabold text-emerald-600 leading-none">{{ $dept->sessions ?? 0 }}</p>
                                <p class="text-[9px] font-semibold text-emerald-300 mt-1">Active</p>
                            </div>
                            <div class="flex-1 text-center {{ ($dept->avg_flag_rate ?? 0) > 0 ? 'bg-rose-50' : 'bg-slate-50' }} rounded-lg py-2.5">
                                <p class="text-lg font-extrabold {{ ($dept->avg_flag_rate ?? 0) > 0 ? 'text-rose-600' : 'text-slate-400' }} leading-none">{{ $dept->avg_flag_rate ?? 0 }}%</p>
                                <p class="text-[9px] font-semibold {{ ($dept->avg_flag_rate ?? 0) > 0 ? 'text-rose-300' : 'text-slate-300' }} mt-1">Flag Rate</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
        <footer class="px-8 py-4 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-300">
            <span>© {{ date('Y') }} ExamSystem — Exams Oversight</span>
            <span class="font-mono">Real-time · 15s polling</span>
        </footer>
    </main>
</div>
{{-- ===================== FORCE END MODAL ===================== --}}
<div id="force-end-modal" class="fixed inset-0 z-50 items-center justify-center">
    <div class="absolute inset-0 bg-slate-900 bg-opacity-50 backdrop-blur-sm" onclick="closeForceEnd()"></div>
    <div class="relative bg-white rounded-2xl shadow-2xl p-8 max-w-md w-full mx-4 border border-slate-200" style="box-shadow:0 24px 48px rgba(15,23,42,0.25);">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-12 h-12 bg-rose-50 rounded-xl flex items-center justify-center"><i class="fa-solid fa-stop text-rose-500 text-lg"></i></div>
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">Force End Exam</h3>
                <p id="force-end-exam-name" class="text-xs text-slate-400">—</p>
            </div>
        </div>
        <p class="text-sm text-slate-600 mb-6">This will <strong>immediately terminate</strong> the exam and disconnect all active students. This cannot be undone.</p>
        <div class="flex gap-3">
            <button onclick="closeForceEnd()" class="flex-1 py-2.5 px-4 bg-slate-100 text-slate-600 rounded-xl font-semibold text-sm hover:bg-slate-200 transition-all">Cancel</button>
            <button onclick="executeForceEnd()" id="force-end-btn" class="flex-1 py-2.5 px-4 bg-rose-500 text-white rounded-xl font-semibold text-sm hover:bg-rose-600 transition-all" style="box-shadow:0 4px 12px rgba(244,63,94,0.35);">
                <i class="fa-solid fa-stop mr-1.5"></i> Force End
            </button>
        </div>
    </div>
</div>
{{-- Toast --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2" style="pointer-events:none;"></div>
{{-- ===================== JAVASCRIPT ===================== --}}
<script>
(function() {
    'use strict';
    const REFRESH_INTERVAL = 15;
    let countdown = REFRESH_INTERVAL;
    let forceEndExamId = null;
    // ── Clock ──
    function updateClock() {
        document.getElementById('live-clock').textContent =
            new Date().toLocaleTimeString('en-US', { hour12:false });
    }
    setInterval(updateClock, 1000);
    updateClock();
    // ── Countdown + auto-refresh ──
    setInterval(() => {
        countdown--;
        document.getElementById('refresh-countdown').textContent = countdown;
        if (countdown <= 0) {
            countdown = REFRESH_INTERVAL;
            const icon = document.getElementById('refresh-icon');
            icon.classList.add('animate-spin');
            setTimeout(() => icon.classList.remove('animate-spin'), 700);
            fetchExamsData();
        }
    }, 1000);
    // ── Fetch real data ──
    function fetchExamsData() {
        fetch('{{ route("superadmin.exams.api") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            setMetric('m-total', data.totalExams ?? 0);
            setMetric('m-active', data.activeExams ?? 0);
            setMetric('m-completed', data.completedExams ?? 0);
            setMetric('m-flag', (data.avgFlagRate ?? 0) + '%');
        })
        .catch(err => console.error('Exams poll failed:', err));
    }
    function setMetric(id, val) {
        const el = document.getElementById(id);
        if (!el) return;
        const display = typeof val === 'number' ? val.toLocaleString() : val;
        if (el.textContent !== String(display)) {
            el.textContent = display;
            el.classList.remove('count-animate');
            void el.offsetWidth;
            el.classList.add('count-animate');
        }
    }
    // ── Force End Modal ──
    window.openForceEnd = function(id, name) {
        forceEndExamId = id;
        document.getElementById('force-end-exam-name').textContent = name;
        document.getElementById('force-end-modal').style.display = 'flex';
    };
    window.closeForceEnd = function() {
        document.getElementById('force-end-modal').style.display = 'none';
        forceEndExamId = null;
    };
    window.executeForceEnd = function() {
        if (!forceEndExamId) return;
        const btn = document.getElementById('force-end-btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Ending...';
        btn.disabled = true;
        fetch(`/super-admin/exams/${forceEndExamId}/force-end`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            showToast('Exam #' + forceEndExamId + ' forcefully ended.', 'success');
            closeForceEnd();
            btn.innerHTML = '<i class="fa-solid fa-stop mr-1.5"></i> Force End';
            btn.disabled = false;
            setTimeout(() => location.reload(), 1500);
        })
        .catch(() => {
            showToast('Failed to end exam. Check console.', 'error');
            btn.innerHTML = '<i class="fa-solid fa-stop mr-1.5"></i> Force End';
            btn.disabled = false;
        });
    };
    // ── Toast ──
    function showToast(message, type) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const colors = { success:'bg-emerald-600', error:'bg-rose-600', info:'bg-blue-600' };
        const icons = { success:'fa-check-circle', error:'fa-exclamation-circle', info:'fa-info-circle' };
        toast.className = `toast-enter flex items-center gap-2.5 px-4 py-3 rounded-xl text-white text-xs font-semibold ${colors[type]||colors.info}`;
        toast.style.pointerEvents = 'auto';
        toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
        toast.innerHTML = `<i class="fa-solid ${icons[type]||icons.info}"></i> ${esc(message)}`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('toast-visible'));
        setTimeout(() => { toast.classList.remove('toast-visible'); setTimeout(() => toast.remove(), 300); }, 4000);
    }
    function esc(s) { const d=document.createElement('div'); d.appendChild(document.createTextNode(s||'')); return d.innerHTML; }
})();
</script>
</body>
</html>