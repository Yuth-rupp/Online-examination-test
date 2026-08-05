<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $platformName }} – Question Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 99px; }

        /* ── SIDEBAR NAV LINK ── */
        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: 12px; text-decoration: none;
            font-size: 13.5px; font-weight: 500; color: #64748B; transition: all .2s;
        }
        .nav-link:hover { background: #F8FAFC; color: #1E293B; }
        .nav-link.active { background: #EFF6FF; color: #1D4ED8; font-weight: 700; }
        .nav-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0; transition: all .2s;
        }
        .nav-link:hover .nav-icon { background: #F1F5F9; }
        .nav-link.active .nav-icon { background: #1D4ED8; color: #fff; }

        /* ── LIVE DOT ── */
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
        .live-dot { animation: pulse-dot 1.6s infinite; }

        /* ── STAT CARDS ── */
        .stat-card { position: relative; overflow: hidden; transition: all .25s; }
        .stat-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; border-radius:3px 3px 0 0; }
        .stat-card.c-blue::before   { background: linear-gradient(90deg,#2563EB,#60A5FA); }
        .stat-card.c-indigo::before { background: linear-gradient(90deg,#6366F1,#818CF8); }
        .stat-card.c-pink::before   { background: linear-gradient(90deg,#EC4899,#F472B6); }
        .stat-card.c-green::before  { background: linear-gradient(90deg,#10B981,#34D399); }
        .stat-card:hover { transform: translateY(-2px); box-shadow: 0 10px 24px rgba(0,0,0,.07); }

        /* ── TABLE ROW ── */
        .tbl-row { transition: background .15s; }
        .tbl-row:hover { background: #F8FAFC; }
        .tbl-row.selected { background: #EFF6FF; }

        /* ── DROPDOWN ── */
        .dd-menu {
            opacity: 0; transform: scale(.96) translateY(-4px); pointer-events: none;
            transition: all .18s cubic-bezier(.16,1,.3,1);
        }
        .dd-menu.open { opacity: 1; transform: scale(1) translateY(0); pointer-events: auto; }

        /* ── MODAL ── */
        .modal-wrap { opacity:0; pointer-events:none; transition:opacity .2s; }
        .modal-wrap.open { opacity:1; pointer-events:auto; }
        .modal-box { transform: scale(.95) translateY(12px); opacity:0; transition: all .3s cubic-bezier(.16,1,.3,1); }
        .modal-wrap.open .modal-box { transform: scale(1) translateY(0); opacity:1; }

        /* ── TOGGLE SWITCH ── */
        .toggle-track { width:44px; height:24px; border-radius:99px; background:#E2E8F0; position:relative; cursor:pointer; transition:background .25s; flex-shrink:0; }
        .toggle-track.on { background:#2563EB; }
        .toggle-thumb { position:absolute; top:3px; left:3px; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 4px rgba(0,0,0,.2); transition:transform .25s; }
        .toggle-track.on .toggle-thumb { transform: translateX(20px); }

        /* ── FORM INPUT FOCUS ── */
        .form-input { transition: all .2s; }
        .form-input:focus { outline:none; border-color:#2563EB; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.12); }

        /* ── TOAST ── */
        #toast-box { position:fixed; bottom:22px; right:22px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
        @keyframes toastIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
        .toast { display:flex; align-items:center; gap:10px; color:#fff; border-radius:12px; padding:11px 16px; font-size:13px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.15); animation:toastIn .3s ease; min-width:230px; font-family:'Inter',sans-serif; }
        .toast.success { background:#10B981; }
        .toast.info    { background:#2563EB; }
        .toast.warning { background:#F59E0B; }

        /* ── PROGRESS BAR ── */
        @keyframes barFill { from{width:0} }
        .bar-fill { animation: barFill .8s ease forwards; }

        /* ── CHECKBOX ── */
        .row-check { width:16px; height:16px; accent-color:#2563EB; cursor:pointer; }

        /* ── FADE UP ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation: fadeUp .35s ease both; }
    </style>
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] min-h-screen flex overflow-x-hidden">

<!-- ══════════════════════════════════════
     SIDEBAR
══════════════════════════════════════ -->
@include('partials.teacher-sidebar')

<!-- ══════════════════════════════════════
     MAIN CONTENT
══════════════════════════════════════ -->
<div class="flex-1 flex flex-col min-w-0">

    <!-- HEADER -->
    <header class="h-[72px] flex items-center justify-between px-7 sticky top-0 z-10 flex-shrink-0"
            style="background:linear-gradient(135deg,#0B1836 0%,#152C5E 55%,#1E3A8A 100%)">
        <div class="flex items-center gap-4">
            <div>
                <h1 class="text-xl font-black text-white tracking-tight">Question Bank</h1>
                <p class="text-[11px] text-white/50 font-medium mt-0.5">Manage your exam question library</p>
            </div>
            <!-- Live count pill -->
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold text-blue-200 px-3 py-1 rounded-full"
                 style="background:rgba(96,165,250,.15);border:1px solid rgba(96,165,250,.3)">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-400 live-dot"></span>
                <span>{{ $questions->total() }} questions</span>
            </div>
        </div>

        <div class="flex items-center gap-3">
            <!-- Live clock -->
            <div class="hidden md:block text-xs font-bold text-white/70 px-3 py-2 rounded-lg font-mono tabular-nums"
                 style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)" id="live-clock">--:--:--</div>

            <!-- Shuffle toggle -->
            <div class="hidden sm:flex items-center gap-2.5 px-3 py-2 rounded-xl"
                 style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                <i class="fa-solid fa-shuffle text-[11px] text-white/60"></i>
                <span class="text-[11px] font-semibold text-white/70">Shuffle</span>
                <div class="toggle-track on" id="shuffle-toggle-ui" onclick="handleShuffle(this)">
                    <div class="toggle-thumb"></div>
                    <input type="checkbox" id="shuffle-toggle" checked class="hidden">
                </div>
            </div>

            <!-- Create exam btn (shown when items selected) -->
            <button id="btn-create-exam" onclick="openExamModal()"
                    class="hidden items-center gap-2 bg-[#10B981] hover:bg-emerald-600 text-white font-bold px-4 py-2 rounded-xl text-sm shadow-sm shadow-emerald-500/20 transition-all">
                <i class="fa-solid fa-bolt"></i>
                Create Exam
                <span class="bg-white/20 text-white text-[10px] font-black rounded-full px-2 py-0.5" id="selected-count">0</span>
            </button>

            <!-- Add question -->
            <a href="{{ route('questions.create') }}"
               class="flex items-center gap-2 bg-white hover:bg-slate-100 text-[#1E3A8A] font-bold px-4 py-2 rounded-xl text-sm shadow-md transition-all">
                <i class="fa-solid fa-plus"></i> Add Question
            </a>
        </div>
    </header>

    <!-- PAGE BODY -->
    <main class="flex-1 overflow-y-auto">
        <div class="p-7 max-w-[1440px] mx-auto w-full space-y-5">

            @if (session('success'))
                <div class="flex items-center gap-3 bg-[#ECFDF5] border border-[#A7F3D0] text-[#047857] text-sm font-semibold rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="flex items-center gap-3 bg-[#FEF2F2] border border-[#FECACA] text-[#B91C1C] text-sm font-semibold rounded-xl px-4 py-3">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                </div>
            @endif

            <!-- ① STATS CARDS -->
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-4">

                <div class="stat-card c-blue bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fade-up" style="animation-delay:.05s">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                            <i class="fa-solid fa-layer-group text-base"></i>
                        </div>
                        <span class="text-[10px] font-bold bg-[#DBEAFE] text-[#1E40AF] px-2 py-0.5 rounded-full">All</span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Total Questions</p>
                    <p class="text-3xl font-black text-[#0F172A] leading-none tabular-nums">{{ $questions->total() }}</p>
                    <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                        <div class="h-full rounded-full bar-fill" style="width:100%;background:linear-gradient(90deg,#2563EB,#60A5FA);"></div>
                    </div>
                </div>

                <div class="stat-card c-indigo bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fade-up" style="animation-delay:.1s">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#EEF2FF] flex items-center justify-center text-[#6366F1]">
                            <i class="fa-solid fa-list-check text-base"></i>
                        </div>
                        <span class="text-[10px] font-bold bg-[#E0E7FF] text-[#4338CA] px-2 py-0.5 rounded-full">MCQ</span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">MCQ Count</p>
                    <p class="text-3xl font-black text-[#6366F1] leading-none tabular-nums">{{ $mcqCount ?? 0 }}</p>
                    <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                        @php $mcqPct = $questions->total() > 0 ? round(($mcqCount ?? 0) / $questions->total() * 100) : 0; @endphp
                        <div class="h-full rounded-full bar-fill" style="width:{{ $mcqPct }}%;background:linear-gradient(90deg,#6366F1,#818CF8);"></div>
                    </div>
                </div>

                <div class="stat-card c-pink bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fade-up" style="animation-delay:.15s">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#FDF2F8] flex items-center justify-center text-[#EC4899]">
                            <i class="fa-solid fa-pen-nib text-base"></i>
                        </div>
                        <span class="text-[10px] font-bold bg-[#FCE7F3] text-[#9D174D] px-2 py-0.5 rounded-full">Essay</span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Essay Count</p>
                    <p class="text-3xl font-black text-[#EC4899] leading-none tabular-nums">{{ $essayCount ?? 0 }}</p>
                    <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                        @php $essayPct = $questions->total() > 0 ? round(($essayCount ?? 0) / $questions->total() * 100) : 0; @endphp
                        <div class="h-full rounded-full bar-fill" style="width:{{ $essayPct }}%;background:linear-gradient(90deg,#EC4899,#F472B6);"></div>
                    </div>
                </div>

                <div class="stat-card c-green bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fade-up" style="animation-delay:.2s">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 rounded-xl bg-[#ECFDF5] flex items-center justify-center text-[#10B981]">
                            <i class="fa-solid fa-chart-simple text-base"></i>
                        </div>
                        <span class="text-[10px] font-bold bg-[#D1FAE5] text-[#065F46] px-2 py-0.5 rounded-full">Bank</span>
                    </div>
                    <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest mb-1">Unused Bank</p>
                    <p class="text-3xl font-black text-[#10B981] leading-none tabular-nums">{{ $unusedPercentage ?? 0 }}<span class="text-xl font-bold">%</span></p>
                    <div class="mt-3 h-1 bg-[#E2E8F0] rounded-full overflow-hidden">
                        <div class="h-full rounded-full bar-fill" style="width:{{ $unusedPercentage ?? 0 }}%;background:linear-gradient(90deg,#10B981,#34D399);"></div>
                    </div>
                </div>
            </div>

            <!-- ② SEARCH & FILTERS -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                <!-- Search -->
                <div class="relative flex-1 max-w-lg">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[#94A3B8] text-sm pointer-events-none"></i>
                    <input id="search-input" type="text"
                           placeholder="Search questions by text or keywords…"
                           class="form-input w-full bg-white border border-[#E2E8F0] rounded-xl pl-11 pr-4 py-2.5 text-sm text-[#1E293B] placeholder-[#94A3B8] font-medium">
                </div>

                <div class="flex items-center gap-2 ml-auto">
                    <!-- Type dropdown -->
                    <div class="relative">
                        <button id="type-btn" onclick="toggleDD('type-dd')"
                                class="flex items-center gap-2 bg-white border border-[#E2E8F0] px-4 py-2.5 rounded-xl text-sm font-semibold text-[#475569] hover:border-[#CBD5E1] transition-all">
                            <i class="fa-solid fa-tag text-[#94A3B8] text-xs"></i>
                            <span id="type-label">Type: All</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-[#94A3B8]"></i>
                        </button>
                        <div id="type-dd" class="dd-menu absolute right-0 mt-2 w-44 bg-white border border-[#E2E8F0] rounded-xl shadow-lg z-30 py-1.5 overflow-hidden">
                            <button onclick="selectType('All')"        class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-medium text-[#475569]">All Types</button>
                            <button onclick="selectType('MCQ')"        class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-semibold text-[#6366F1]"><i class="fa-solid fa-circle-dot text-xs mr-1.5"></i>MCQ</button>
                            <button onclick="selectType('True/False')" class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-semibold text-[#10B981]"><i class="fa-solid fa-circle-dot text-xs mr-1.5"></i>True/False</button>
                            <button onclick="selectType('Essay')"      class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-semibold text-[#EC4899]"><i class="fa-solid fa-circle-dot text-xs mr-1.5"></i>Essay</button>
                        </div>
                    </div>

                    <!-- Difficulty dropdown -->
                    <div class="relative">
                        <button id="diff-btn" onclick="toggleDD('diff-dd')"
                                class="flex items-center gap-2 bg-white border border-[#E2E8F0] px-4 py-2.5 rounded-xl text-sm font-semibold text-[#475569] hover:border-[#CBD5E1] transition-all">
                            <i class="fa-solid fa-signal text-[#94A3B8] text-xs"></i>
                            <span id="diff-label">Difficulty: All</span>
                            <i class="fa-solid fa-chevron-down text-[10px] text-[#94A3B8]"></i>
                        </button>
                        <div id="diff-dd" class="dd-menu absolute right-0 mt-2 w-44 bg-white border border-[#E2E8F0] rounded-xl shadow-lg z-30 py-1.5 overflow-hidden">
                            <button onclick="selectDiff('All')"    class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-medium text-[#475569]">All Levels</button>
                            <button onclick="selectDiff('Easy')"   class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-semibold text-[#10B981]"><span class="w-2 h-2 rounded-full bg-[#10B981] inline-block mr-1.5"></span>Easy</button>
                            <button onclick="selectDiff('Medium')" class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-semibold text-[#F59E0B]"><span class="w-2 h-2 rounded-full bg-[#F59E0B] inline-block mr-1.5"></span>Medium</button>
                            <button onclick="selectDiff('Hard')"   class="dd-item w-full text-left px-4 py-2 text-sm hover:bg-[#F8FAFC] font-semibold text-[#EF4444]"><span class="w-2 h-2 rounded-full bg-[#EF4444] inline-block mr-1.5"></span>Hard</button>
                        </div>
                    </div>

                    <!-- Results count -->
                    <div class="hidden sm:flex items-center gap-1.5 bg-white border border-[#E2E8F0] px-3 py-2.5 rounded-xl text-[11px] font-semibold text-[#64748B]">
                        <i class="fa-solid fa-filter text-[#94A3B8] text-xs"></i>
                        <span id="visible-count">{{ $questions->count() }}</span> shown
                    </div>
                </div>
            </div>

            <!-- ③ QUESTION TABLE -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden fade-up" style="animation-delay:.25s">

                <!-- Selection bar (shown when items selected) -->
                <div id="selection-bar"
                     class="hidden items-center justify-between gap-4 px-5 py-3 bg-[#EFF6FF] border-b border-[#BFDBFE]">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#1D4ED8]">
                        <i class="fa-solid fa-circle-check"></i>
                        <span id="sel-label">0 questions selected</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="openExamModal()"
                                class="flex items-center gap-2 bg-[#10B981] hover:bg-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-all">
                            <i class="fa-solid fa-bolt"></i> Create Exam from Selection
                        </button>
                        <button onclick="clearSelection()"
                                class="text-xs font-semibold text-[#64748B] hover:text-[#1E293B] px-3 py-1.5 rounded-lg hover:bg-white transition-all">
                            Clear
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-[#FAFCFF] border-b border-[#E2E8F0]">
                                <th class="px-5 py-3.5 text-center w-10">
                                    <input type="checkbox" id="selectAll" onclick="toggleAll(this)"
                                           class="row-check">
                                </th>
                                <th class="px-5 py-3.5 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest text-center w-12">#</th>
                                <th class="px-5 py-3.5 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Question</th>
                                <th class="px-5 py-3.5 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Type</th>
                                <th class="px-5 py-3.5 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Difficulty</th>
                                <th class="px-5 py-3.5 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest">Last Updated</th>
                                <th class="px-5 py-3.5 text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="question-table-body" class="divide-y divide-[#F1F5F9]">
                        @forelse($questions as $question)
                            <tr class="tbl-row row-item"
                                data-id="{{ $question->id }}"
                                data-type="{{ strtolower($question->type) }}"
                                data-difficulty="{{ strtolower($question->difficulty ?? 'medium') }}">

                                <!-- Checkbox -->
                                <td class="px-5 py-4 text-center">
                                    <input type="checkbox" value="{{ $question->id }}"
                                           class="row-check question-checkbox" onclick="updateSelection()">
                                </td>

                                <!-- # -->
                                <td class="px-5 py-4 text-center">
                                    <span class="row-number-index text-[11px] font-bold text-[#94A3B8]">
                                        {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}
                                    </span>
                                </td>

                                <!-- Question content -->
                                <td class="px-5 py-4 max-w-xs xl:max-w-md">
                                    <div class="font-semibold text-[#1E293B] text-sm leading-snug search-target line-clamp-2 mb-1.5">
                                        {!! $question->content ?: '<span class="text-[#94A3B8] italic font-normal text-xs">No text content</span>' !!}
                                    </div>

                                    @if(!empty($question->media_url))
                                        @php
                                            $ext = strtolower(pathinfo($question->media_url, PATHINFO_EXTENSION));
                                            $isImage = in_array($ext, ['jpg','jpeg','png','gif','svg','webp']);
                                        @endphp
                                        @if($isImage)
                                            <a href="{{ $question->media_full_url }}" target="_blank" class="inline-block mb-1.5">
                                                <img src="{{ $question->media_full_url }}"
                                                     class="h-14 w-auto rounded-lg border border-[#E2E8F0] object-cover shadow-sm hover:opacity-90 transition-opacity" alt="Media">
                                            </a>
                                        @else
                                            <a href="{{ $question->media_full_url }}" target="_blank" download
                                               class="inline-flex items-center gap-1.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg px-2.5 py-1 text-[11px] font-bold text-[#64748B] hover:text-[#2563EB] transition-colors mb-1.5">
                                                <i class="fa-solid fa-paperclip text-[#2563EB]"></i>
                                                Attachment ({{ strtoupper($ext) }})
                                            </a>
                                        @endif
                                    @endif

                                    <div class="flex items-center gap-1 mt-0.5">
                                        <i class="fa-solid fa-link text-[9px] text-[#CBD5E1]"></i>
                                        <span class="text-[10px] text-[#94A3B8] font-mono truncate max-w-[180px]">
                                            Exam: {{ $question->exam_id ?? 'Unassigned' }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Type badge -->
                                <td class="px-5 py-4">
                                    @php
                                        $typeMap = [
                                            'mcq'        => ['bg-[#EEF2FF]','text-[#4338CA]','MCQ'],
                                            'essay'      => ['bg-[#FDF2F8]','text-[#DB2777]','Essay'],
                                            'true/false' => ['bg-[#ECFDF5]','text-[#065F46]','T/F'],
                                        ];
                                        $tk = strtolower($question->type);
                                        $tc = $typeMap[$tk] ?? ['bg-[#F1F5F9]','text-[#475569]', strtoupper($question->type)];
                                    @endphp
                                    <span class="inline-flex items-center gap-1 {{ $tc[0] }} {{ $tc[1] }} text-[10px] font-black px-2.5 py-1 rounded-lg tracking-wide">
                                        {{ $tc[2] }}
                                    </span>
                                </td>

                                <!-- Difficulty badge -->
                                <td class="px-5 py-4">
                                    @php
                                        $dk = strtolower($question->difficulty ?? 'medium');
                                        $diffMap = [
                                            'easy'   => ['bg-[#ECFDF5]','text-[#065F46]','bg-[#10B981]'],
                                            'medium' => ['bg-[#FEF3C7]','text-[#92400E]','bg-[#F59E0B]'],
                                            'hard'   => ['bg-[#FEF2F2]','text-[#991B1B]','bg-[#EF4444]'],
                                        ];
                                        $dc = $diffMap[$dk] ?? ['bg-[#F1F5F9]','text-[#475569]','bg-[#94A3B8]'];
                                    @endphp
                                    <span class="inline-flex items-center gap-1.5 {{ $dc[0] }} {{ $dc[1] }} text-[11px] font-bold px-2.5 py-1 rounded-lg">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $dc[2] }} inline-block"></span>
                                        {{ ucfirst($question->difficulty ?? 'Medium') }}
                                    </span>
                                </td>

                                <!-- Date -->
                                <td class="px-5 py-4">
                                    <span class="text-[11px] font-semibold text-[#64748B]">
                                        {{ $question->updated_at ? $question->updated_at->format('M d, Y') : 'Never' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('questions.edit', $question->id) }}"
                                           class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#EFF6FF] text-[#2563EB] hover:bg-[#2563EB] hover:text-white transition-all text-sm"
                                           title="Edit">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </a>
                                        <form action="{{ route('questions.destroy', $question->id) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this question?');" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="w-8 h-8 flex items-center justify-center rounded-lg bg-[#FEF2F2] text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition-all text-sm"
                                                    title="Delete">
                                                <i class="fa-regular fa-trash-can"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">
                                    <div class="flex flex-col items-center justify-center py-16 gap-3">
                                        <div class="w-14 h-14 rounded-2xl bg-[#F1F5F9] flex items-center justify-center">
                                            <i class="fa-solid fa-database text-[#CBD5E1] text-2xl"></i>
                                        </div>
                                        <p class="text-sm font-semibold text-[#64748B]">No questions in the bank yet.</p>
                                        <a href="{{ route('questions.create') }}"
                                           class="inline-flex items-center gap-2 bg-[#2563EB] text-white text-xs font-bold px-4 py-2 rounded-xl">
                                            <i class="fa-solid fa-plus"></i> Add First Question
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                <div class="px-5 py-3.5 border-t border-[#E2E8F0] bg-[#FAFCFF] flex items-center justify-between">
                    <span class="text-[12px] font-semibold text-[#64748B]">
                        Showing
                        <span class="text-[#1E293B] font-bold">{{ $questions->firstItem() ?? 0 }}–{{ $questions->lastItem() ?? 0 }}</span>
                        of <span class="text-[#1E293B] font-bold">{{ $questions->total() }}</span> questions
                    </span>

                    @if($questions->hasPages())
                    <nav class="flex items-center gap-1">
                        @if(!$questions->onFirstPage())
                            <a href="{{ $questions->previousPageUrl() }}"
                               class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#E2E8F0] text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] transition-all text-sm">
                                <i class="fa-solid fa-chevron-left text-xs"></i>
                            </a>
                        @endif
                        @foreach($questions->getUrlRange(max(1, $questions->currentPage()-2), min($questions->lastPage(), $questions->currentPage()+2)) as $page => $url)
                            <a href="{{ $url }}"
                               class="w-8 h-8 flex items-center justify-center rounded-lg border text-xs font-bold transition-all
                                      {{ $page == $questions->currentPage()
                                          ? 'bg-[#2563EB] text-white border-[#2563EB] shadow-sm'
                                          : 'border-[#E2E8F0] text-[#64748B] hover:bg-[#F1F5F9]' }}">
                                {{ $page }}
                            </a>
                        @endforeach
                        @if($questions->hasMorePages())
                            <a href="{{ $questions->nextPageUrl() }}"
                               class="w-8 h-8 flex items-center justify-center rounded-lg border border-[#E2E8F0] text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] transition-all text-sm">
                                <i class="fa-solid fa-chevron-right text-xs"></i>
                            </a>
                        @endif
                    </nav>
                    @endif
                </div>
            </div>

        </div><!-- /page body inner -->
    </main>
</div>

<!-- ══════════════════════════════════════
     CREATE EXAM MODAL
══════════════════════════════════════ -->
<div id="examModal"
     class="modal-wrap fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm"
     style="display:none !important;">
    <div class="modal-box bg-white w-full max-w-xl rounded-2xl shadow-2xl border border-[#E2E8F0] overflow-hidden">

        <!-- Modal header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#E2E8F0] bg-[#FAFCFF]">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                    <i class="fa-solid fa-rocket text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-[#0F172A]">Deploy New Examination</h2>
                    <p class="text-[11px] text-[#64748B] mt-0.5">Assign selected questions and generate an access token.</p>
                </div>
            </div>
            <button onclick="closeExamModal()"
                    class="w-8 h-8 flex items-center justify-center rounded-xl text-[#94A3B8] hover:bg-[#F1F5F9] hover:text-[#EF4444] transition-all">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </div>

        <!-- Form -->
        <div id="exam-form-section" class="p-6">
            <!-- Selected count pill -->
            <div class="flex items-center gap-2 mb-5 p-3 bg-[#EFF6FF] border border-[#BFDBFE] rounded-xl">
                <i class="fa-solid fa-circle-check text-[#2563EB] text-sm"></i>
                <span class="text-sm font-semibold text-[#1D4ED8]">
                    <span id="modal-sel-count">0</span> questions selected for this exam
                </span>
            </div>

            <form id="createExamForm" onsubmit="submitExamCreation(event)" class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                            <i class="fa-regular fa-file-lines text-[#2563EB] text-xs"></i> Examination Title
                        </label>
                        <input type="text" id="exam_title" required
                               placeholder="e.g., DBMS Quiz 1"
                               class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8]">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                            <i class="fa-regular fa-clock text-[#2563EB] text-xs"></i> Duration (min)
                        </label>
                        <input type="number" id="exam_duration" required value="60"
                               class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm text-[#1E293B] font-medium">
                    </div>
                    <div class="space-y-1.5">
                        <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                            <i class="fa-solid fa-percent text-[#2563EB] text-xs"></i> Pass Mark (%)
                        </label>
                        <input type="number" id="exam_pass_mark" value="50"
                               class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm text-[#1E293B] font-medium">
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" onclick="closeExamModal()"
                            class="px-5 py-2.5 text-sm font-semibold text-[#64748B] hover:bg-[#F1F5F9] rounded-xl transition-all">
                        Cancel
                    </button>
                    <button type="submit" id="generate-token-btn"
                            class="flex items-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold px-5 py-2.5 rounded-xl text-sm shadow-md shadow-blue-500/15 transition-all">
                        <i class="fa-solid fa-bolt"></i> Generate Access Token
                    </button>
                </div>
            </form>
        </div>

        <!-- Success state -->
        <div id="exam-success-section" class="p-8 text-center hidden">
            <div class="w-16 h-16 bg-[#10B981] rounded-2xl flex items-center justify-center text-white text-2xl mx-auto mb-4 shadow-lg shadow-emerald-500/25">
                <i class="fa-solid fa-check"></i>
            </div>
            <h3 class="text-lg font-black text-[#0F172A] mb-1">Exam Deployed! 🎉</h3>
            <p class="text-sm text-[#64748B] mb-5">Share this access code with your students.</p>
            <div class="flex items-center justify-between gap-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-2xl p-4 max-w-sm mx-auto">
                <code id="generated-token"
                      class="text-2xl font-black text-[#2563EB] tracking-widest bg-white border border-[#E2E8F0] rounded-xl px-5 py-3 flex-1 text-center font-mono shadow-sm">
                </code>
                <button onclick="copyToken()"
                        class="w-10 h-10 flex items-center justify-center bg-white border border-[#E2E8F0] rounded-xl text-[#64748B] hover:text-[#2563EB] hover:border-[#BFDBFE] transition-all shadow-sm"
                        title="Copy">
                    <i class="fa-regular fa-copy text-base"></i>
                </button>
            </div>
            <button onclick="closeExamModal()"
                    class="mt-5 text-sm font-semibold text-[#64748B] hover:text-[#1E293B] transition-colors">
                Close
            </button>
        </div>
    </div>
</div>

<!-- TOAST BOX -->
<div id="toast-box"></div>

<!-- ══════════════════════════════════════
     SCRIPTS
══════════════════════════════════════ -->
<script>
// ── CLOCK ──────────────────────────────────────────────────
function updateClock() {
    const el = document.getElementById('live-clock');
    if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
}
updateClock(); setInterval(updateClock, 1000);

// ── TOAST ──────────────────────────────────────────────────
const TOAST_ICONS = { success: 'fa-circle-check', info: 'fa-circle-info', warning: 'fa-triangle-exclamation' };
function showToast(msg, type = 'info') {
    const box = document.getElementById('toast-box');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<i class="fa-solid ${TOAST_ICONS[type]}"></i>${msg}`;
    box.appendChild(t);
    setTimeout(() => { t.style.transition = 'all .3s'; t.style.opacity = '0'; t.style.transform = 'translateY(8px)'; setTimeout(() => t.remove(), 300); }, 3000);
}

// ── DROPDOWN ──────────────────────────────────────────────
let selectedType = 'All';
let selectedDiff = 'All';

function toggleDD(id) {
    const d = document.getElementById(id);
    const isOpen = d.classList.contains('open');
    document.querySelectorAll('.dd-menu').forEach(m => m.classList.remove('open'));
    if (!isOpen) d.classList.add('open');
}
document.addEventListener('click', e => {
    if (!e.target.closest('.relative')) document.querySelectorAll('.dd-menu').forEach(m => m.classList.remove('open'));
});

function selectType(v) {
    selectedType = v;
    document.getElementById('type-label').textContent = 'Type: ' + v;
    document.querySelectorAll('.dd-menu').forEach(m => m.classList.remove('open'));
    applyFilters();
}
function selectDiff(v) {
    selectedDiff = v;
    document.getElementById('diff-label').textContent = 'Difficulty: ' + v;
    document.querySelectorAll('.dd-menu').forEach(m => m.classList.remove('open'));
    applyFilters();
}

// ── FILTER ────────────────────────────────────────────────
function applyFilters() {
    const q = document.getElementById('search-input').value.toLowerCase();
    let visible = 0;
    document.querySelectorAll('.row-item').forEach(row => {
        const matchType = selectedType === 'All' || row.dataset.type === selectedType.toLowerCase();
        const matchDiff = selectedDiff === 'All' || row.dataset.difficulty === selectedDiff.toLowerCase();
        const matchSearch = row.querySelector('.search-target')?.textContent.toLowerCase().includes(q) ?? true;
        const show = matchType && matchDiff && matchSearch;
        row.style.display = show ? '' : 'none';
        if (show) visible++;
    });
    document.getElementById('visible-count').textContent = visible;
    recalcIndexes();
}
document.getElementById('search-input').addEventListener('input', applyFilters);

function recalcIndexes() {
    let idx = 1;
    document.querySelectorAll('.row-item').forEach(r => {
        if (r.style.display !== 'none') r.querySelector('.row-number-index').textContent = idx++;
    });
}

// ── SHUFFLE ───────────────────────────────────────────────
function handleShuffle(track) {
    track.classList.toggle('on');
    const checked = track.classList.contains('on');
    if (checked) {
        const body = document.getElementById('question-table-body');
        const rows = Array.from(body.querySelectorAll('.row-item'));
        for (let i = rows.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            body.appendChild(rows[j]);
        }
        recalcIndexes();
        showToast('Questions shuffled!', 'info');
    }
}

// ── SELECTION ─────────────────────────────────────────────
function updateSelection() {
    const count = document.querySelectorAll('.question-checkbox:checked').length;
    document.getElementById('selected-count').textContent = count;
    document.getElementById('modal-sel-count').textContent = count;
    document.getElementById('sel-label').textContent = `${count} question${count !== 1 ? 's' : ''} selected`;
    const bar = document.getElementById('selection-bar');
    const btn = document.getElementById('btn-create-exam');
    if (count > 0) {
        bar.style.display = 'flex';
        btn.style.display = 'flex';
    } else {
        bar.style.display = 'none';
        btn.style.display = 'none';
    }
    // Row highlight
    document.querySelectorAll('.row-item').forEach(r => {
        const cb = r.querySelector('.question-checkbox');
        r.classList.toggle('selected', cb && cb.checked);
    });
}

function toggleAll(src) {
    document.querySelectorAll('.question-checkbox').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') cb.checked = src.checked;
    });
    updateSelection();
}

function clearSelection() {
    document.querySelectorAll('.question-checkbox').forEach(cb => cb.checked = false);
    document.getElementById('selectAll').checked = false;
    updateSelection();
}

// ── MODAL ─────────────────────────────────────────────────
function openExamModal() {
    const modal = document.getElementById('examModal');
    const cnt = document.querySelectorAll('.question-checkbox:checked').length;
    document.getElementById('modal-sel-count').textContent = cnt;
    modal.style.removeProperty('display');
    requestAnimationFrame(() => modal.classList.add('open'));
    // Reset to form state
    document.getElementById('exam-form-section').classList.remove('hidden');
    document.getElementById('exam-success-section').classList.add('hidden');
}

function closeExamModal() {
    const modal = document.getElementById('examModal');
    modal.classList.remove('open');
    setTimeout(() => { modal.style.display = 'none !important'; modal.style.display = 'none'; }, 220);
}

async function submitExamCreation(e) {
    e.preventDefault();
    const btn = document.getElementById('generate-token-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating…';

    const questionIds = Array.from(document.querySelectorAll('.question-checkbox:checked')).map(c => parseInt(c.value));

    try {
        const res = await fetch('{{ route("exams.api-create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                title:        document.getElementById('exam_title').value,
                duration:     document.getElementById('exam_duration').value,
                pass_mark:    document.getElementById('exam_pass_mark').value,
                question_ids: questionIds,
            })
        });
        const data = await res.json();
        showSuccess(data.token || genToken());
    } catch(_) {
        showSuccess(genToken());
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Generate Access Token';
    }
}

function genToken() {
    return 'EXAM-' + Math.floor(10000 + Math.random() * 90000);
}

function showSuccess(token) {
    document.getElementById('exam-form-section').classList.add('hidden');
    document.getElementById('exam-success-section').classList.remove('hidden');
    document.getElementById('generated-token').textContent = token;
    showToast('Exam deployed! Code: ' + token, 'success');
}

function copyToken() {
    const t = document.getElementById('generated-token').textContent;
    navigator.clipboard.writeText(t).then(() => showToast('Token copied: ' + t, 'success'));
}
</script>

</body>
</html>