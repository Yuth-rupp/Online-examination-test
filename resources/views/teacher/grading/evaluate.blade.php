{{--
    FILE:       resources/views/teacher/grading/evaluate.blade.php
    CONTROLLER: GradingController::show($id)
    VARS:       $submission (with student, exam.questions, exam.course)
                $submissionAnswers  — Collection keyed by question_id → answer_text
                $prev, $next        — adjacent submissions for Save & Next
--}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem – Grading</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{font-family:'Inter',system-ui,sans-serif;-webkit-font-smoothing:antialiased}
        [x-cloak]{display:none!important}
        ::-webkit-scrollbar{width:4px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:99px}

        .nav-link{display:flex;align-items:center;gap:11px;padding:9px 12px;border-radius:12px;text-decoration:none;font-size:13.5px;font-weight:500;color:#64748B;transition:all .2s}
        .nav-link:hover{background:#F8FAFC;color:#1E293B}
        .nav-link.active{background:#EFF6FF;color:#1D4ED8;font-weight:700}
        .nav-icon{width:34px;height:34px;border-radius:99px;display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0;transition:all .2s}
        .nav-link:hover .nav-icon{background:#F1F5F9}
        .nav-link.active .nav-icon{background:#1D4ED8;color:#fff}

        input[type=range]{-webkit-appearance:none;appearance:none;height:7px;border-radius:99px;outline:none;cursor:pointer}
        input[type=range]::-webkit-slider-thumb{-webkit-appearance:none;width:20px;height:20px;border-radius:50%;background:#fff;border:3px solid #4F46E5;box-shadow:0 2px 10px rgba(79,70,229,.45);cursor:pointer;transition:transform .15s}
        input[type=range]::-webkit-slider-thumb:hover{transform:scale(1.2)}

        .rk{fill:none;stroke:#E0E7FF;stroke-width:9}
        .rf{fill:none;stroke-width:9;stroke-linecap:round;transform:rotate(-90deg);transform-origin:center;transition:stroke-dashoffset .8s cubic-bezier(.4,0,.2,1),stroke .4s}

        @keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}
        .ld{animation:pdot 1.5s infinite}
        @keyframes fu{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .fu{animation:fu .3s ease both}
        @keyframes bfill{from{width:0}}
        .bf{animation:bfill .9s ease both}
        @keyframes tin{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}

        #tb{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none}
        .toast{display:flex;align-items:center;gap:9px;color:#fff;border-radius:14px;padding:11px 18px;font-size:12px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,.22);animation:tin .3s ease;min-width:200px;font-family:'Inter',sans-serif;pointer-events:auto;white-space:nowrap}

        .qcard{background:#fff;border-radius:18px;overflow:hidden;box-shadow:0 1px 3px rgba(0,0,0,.06),0 6px 18px rgba(0,0,0,.04);transition:all .22s;border:1px solid #F1F5F9}
        .qcard:hover{box-shadow:0 8px 28px rgba(0,0,0,.1);transform:translateY(-2px)}

        .mopt{display:flex;align-items:center;gap:11px;padding:11px 14px;border-radius:12px;border:1.5px solid #E2E8F0;transition:all .15s}
        .mopt.mc{border-color:#10B981;background:linear-gradient(120deg,#ECFDF5 0%,#F0FDF4 100%)}
        .mopt.mw{border-color:#EF4444;background:linear-gradient(120deg,#FEF2F2 0%,#FFF5F5 100%)}
        .mopt.md{opacity:.4}

        .tfc{flex:1;padding:15px;border-radius:13px;border:2px solid #E2E8F0;transition:all .2s}
        .tfc.tc{border-color:#10B981;background:linear-gradient(135deg,#ECFDF5,#D1FAE510)}
        .tfc.tw{border-color:#EF4444;background:linear-gradient(135deg,#FEF2F2,#FEE2E210)}
        .tfc.tnc{border-color:#10B981;background:#ECFDF5}
    </style>
</head>

@php
    /* ══════════════════════════════════════════════
     * SETUP — use Eloquent relationship as source
     * ══════════════════════════════════════════════ */
    $questions   = $submission->exam->questions ?? collect();
    $hasEssay    = $questions->contains(fn($q) => in_array(strtolower($q->type ?? ''), ['essay','text','essay/text']));
    $essayQs     = $questions->filter(fn($q) =>  in_array(strtolower($q->type ?? ''), ['essay','text','essay/text']));
    $autoQs      = $questions->filter(fn($q) => !in_array(strtolower($q->type ?? ''), ['essay','text','essay/text']));

    $totalPts    = $questions->sum('points') ?: ($questions->count() * 5);
    $autoPts     = $autoQs->sum('points')   ?: ($autoQs->count()  * 5);
    $essayMaxPts = $essayQs->sum('points')  ?: ($hasEssay ? 25 : 0);

    /*
     * ── ANSWER MAP ──────────────────────────────────
     * Merge the controller-loaded $submissionAnswers (keyed by question_id)
     * with standard Eloquent answers if loaded.
     */
    $amap = collect();
    if (isset($submissionAnswers)) {
        foreach ($submissionAnswers as $qId => $ansText) {
            $amap->put($qId, (object)[
                'answer_text' => $ansText,
                'selected_option' => $ansText,
                'is_correct' => false
            ]);
        }
    } else {
        $amap = ($submission->answers ?? collect())->keyBy('question_id');
    }

    /*
     * ── AUTO-SCORE ──────────────────────────────────
     * Walk every non-essay question, find its answer,
     * and award points if is_correct OR the selected
     * option matches the stored correct_option.
     */
    $autoScore   = 0;
    $answeredCnt = $amap->count();

    foreach ($autoQs as $q) {
        $a = $amap->get($q->id);
        if (!$a) continue;

        // Prefer the stored is_correct flag if available on a model
        $isCorrect = (bool)($a->is_correct ?? false);

        // Fallback: string-compare selected_option vs correct_option
        if (!$isCorrect) {
            $sa = strtoupper(trim($a->selected_option ?? $a->answer_text ?? ''));
            $ca = strtoupper(trim($q->correct_option ?? ''));
            if ($sa !== '' && $ca !== '' && $sa === $ca) {
                $isCorrect = true;
            }
        }

        if ($isCorrect) {
            $autoScore += ($q->points ?? 5);
        }
    }

    $passThresh = round($totalPts * 0.5);
@endphp

<body class="bg-slate-100 text-slate-800 min-h-screen overflow-x-hidden"
      x-data="GWS(
          {{ $submission->accuracy_score ?? 0 }},
          {{ $submission->depth_score    ?? 0 }},
          {{ $submission->clarity_score  ?? 0 }},
          {{ $autoScore }},
          {{ $hasEssay ? 'true' : 'false' }},
          {{ $essayMaxPts }},
          {{ $totalPts }},
          {{ $autoPts }}
      )">

<form action="{{ route('teacher.grading.store', $submission->id) }}" method="POST" id="GF">
@csrf
<input type="hidden" name="action"     id="FA"  value="save">
<input type="hidden" name="auto_score"           value="{{ $autoScore }}">

<div class="flex h-screen overflow-hidden">

{{-- ═══════════════════════ SIDEBAR ═══════════════════════ --}}
<aside class="w-[260px] bg-white border-r border-[#E2E8F0] flex flex-col flex-shrink-0 sticky top-0 h-screen z-20">
    <a href="{{ route('teacher.dashboard') }}"
       class="h-[72px] flex items-center px-5 gap-3 border-b border-[#E2E8F0] hover:opacity-90 transition-opacity flex-shrink-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white text-base flex-shrink-0"
             style="background:linear-gradient(135deg,#2563EB,#1E40AF);box-shadow:0 4px 12px rgba(37,99,235,.35)">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <span class="font-black text-[18px] text-[#0F172A] tracking-tight">ExamSystem</span>
    </a>
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-1 pb-2">Main Menu</p>
        <a href="{{ route('teacher.dashboard') }}"        class="nav-link"><span class="nav-icon"><i class="fa-solid fa-house"></i></span><span>Dashboard</span></a>
        <a href="{{ route('teacher.question-bank') }}"   class="nav-link"><span class="nav-icon"><i class="fa-solid fa-database"></i></span><span>Question Bank</span></a>
        <a href="{{ route('teacher.monitoring.show') }}" class="nav-link"><span class="nav-icon"><i class="fa-solid fa-display"></i></span><span>Monitoring</span></a>
        <a href="{{ route('teacher.grading.queue') }}"   class="nav-link active">
            <span class="nav-icon"><i class="fa-solid fa-pen-to-square"></i></span>
            <span>Grading</span>
        </a>
        <a href="{{ route('teacher.analytics') }}"       class="nav-link"><span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span><span>Analytics</span></a>
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-4 pb-2">Account</p>
        <a href="{{ route('teacher.settings') }}"        class="nav-link"><span class="nav-icon"><i class="fa-solid fa-gear"></i></span><span>Settings</span></a>
    </nav>
    <div class="p-3 border-t border-[#E2E8F0] flex-shrink-0">
        <a href="{{ route('teacher.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8FAFC] transition-colors">
            <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-[#E2E8F0] flex-shrink-0">
                <img src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'I') }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-[#0F172A] truncate">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                <p class="text-xs text-[#94A3B8] font-medium">Senior Faculty</p>
            </div>
        </a>
    </div>
</aside>

{{-- ═══════════════════════ CENTER PANE ═══════════════════════ --}}
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    {{-- Header --}}
    <div class="flex-shrink-0" style="background:linear-gradient(135deg,#0F172A 0%,#1E3A5F 55%,#312E81 100%)">
        <div class="h-[3px]" style="background:rgba(255,255,255,.08)">
            <div class="h-full bf"
                 style="background:linear-gradient(90deg,#34D399,#60A5FA);
                        width:{{ $questions->count() ? round($answeredCnt/$questions->count()*100) : 0 }}%"></div>
        </div>
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('teacher.grading.queue') }}" class="flex items-center gap-1 text-[10px] font-bold text-white/50 hover:text-white/80 transition-colors">
                        <i class="fa-solid fa-chevron-left text-[8px]"></i> Queue
                    </a>
                    <span class="text-white/25 text-[10px]">/</span>
                    <span class="text-[10px] font-semibold text-white/50">Evaluate Paper</span>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-[15px] font-black text-white tracking-tight">{{ $submission->exam->title ?? 'Exam' }}</h1>
                    @if($hasEssay)
                    <span class="flex items-center gap-1.5 text-[10px] font-black text-amber-300 px-2.5 py-1 rounded-lg"
                          style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3)">
                        <i class="fa-solid fa-pen-nib text-[8px]"></i> Manual review
                    </span>
                    @else
                    <span class="flex items-center gap-1.5 text-[10px] font-black text-emerald-300 px-2.5 py-1 rounded-lg"
                          style="background:rgba(52,211,153,.15);border:1px solid rgba(52,211,153,.3)">
                        <i class="fa-solid fa-robot text-[8px]"></i> Auto-graded
                    </span>
                    @endif
                </div>
                <div class="flex items-center gap-3 mt-1.5 flex-wrap text-[10px] font-bold text-white/50">
                    <div class="flex items-center gap-1.5">
                        <div class="w-5 h-5 rounded-full flex items-center justify-center text-white text-[9px] font-black"
                             style="background:rgba(255,255,255,.2)">
                            {{ strtoupper(substr($submission->student->full_name ?? 'S', 0, 1)) }}
                        </div>
                        <span class="text-white/70 font-bold">{{ $submission->student->full_name ?? 'Student' }}</span>
                    </div>
                    <span class="text-white/25">|</span>
                    <span class="font-mono">{{ $submission->student->institutional_id ?? 'STU-ID' }}</span>
                    <span class="text-white/25">|</span>
                    <span>{{ $questions->count() }} Questions</span>
                    <span class="text-white/25">|</span>
                    <span>Max {{ $totalPts }} pts</span>
                    <span class="text-white/25">|</span>
                    <span>{{ $answeredCnt }} answered</span>
                </div>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <div class="text-center px-3 py-2 rounded-xl hidden lg:block" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider">Clock</p>
                    <p class="text-[12px] font-black text-white tabular-nums" id="lc">--:--:--</p>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-2 rounded-xl" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span>
                    <span class="text-[10px] font-bold text-white/60" id="ss">Live</span>
                </div>
                <button type="submit" @click="document.getElementById('FA').value='save_next'"
                        class="flex items-center gap-1.5 font-black text-[12px] px-4 py-2 rounded-xl"
                        style="background:#fff;color:#1E40AF;box-shadow:0 4px 14px rgba(0,0,0,.2)">
                    Save & Next <i class="fa-solid fa-arrow-right text-[10px]"></i>
                </button>
            </div>
        </div>
    </div>

    {{-- Questions --}}
    <div class="flex-1 overflow-y-auto bg-slate-100" style="padding:20px 20px 32px">
    <div class="space-y-4 max-w-3xl mx-auto">

    @forelse($questions as $qi => $question)
    @php
        /* ─────────────────────────────────────────────
         * PER-QUESTION SETUP
         * Look up the student's answer by question_id
         * ───────────────────────────────────────────── */
        $ans  = $amap->get($question->id);

        $qt   = strtolower($question->type ?? 'mcq');
        $isMCQ= $qt === 'mcq';
        $isTF = in_array($qt, ['tf','true/false','truefalse','boolean']);
        $isEss= in_array($qt, ['essay','text','essay/text']);
        $pts  = $question->points ?? 5;
        $corr = strtoupper(trim($question->correct_option ?? ''));

        // Student's chosen option
        $sAns = strtoupper(trim($ans->selected_option ?? $ans->answer_text ?? ''));

        // An answer record exists = question was answered
        $hasAnswered = ($ans !== null);

        // Correctness: trust is_correct flag first, then compare strings
        $isOk = false;
        if ($hasAnswered && !$isEss) {
            $isOk = (bool)($ans->is_correct ?? false);
            if (!$isOk && $sAns !== '' && $corr !== '') {
                $isOk = ($sAns === $corr);
            }
        }

        $accent = $isMCQ ? '#4F46E5' : ($isTF ? '#059669' : '#7C3AED');
        $tLabel = $isMCQ ? 'MCQ'     : ($isTF ? 'TRUE / FALSE' : 'ESSAY');
        $tBg    = $isMCQ ? '#E0E7FF' : ($isTF ? '#D1FAE5' : '#EDE9FE');
        $tColor = $isMCQ ? '#3730A3' : ($isTF ? '#065F46' : '#6D28D9');
    @endphp

    <div class="qcard fu" style="animation-delay:{{ $qi * 0.05 }}s">

        {{-- Card header --}}
        <div class="flex items-stretch">
            <div style="width:5px;background:{{ $accent }};flex-shrink:0;align-self:stretch"></div>
            <div class="flex-1 flex items-center justify-between px-5 py-3.5"
                 style="background:linear-gradient(135deg,{{ $accent }}10 0%,transparent 60%)">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white font-black text-sm flex-shrink-0"
                         style="background:{{ $accent }};box-shadow:0 4px 12px {{ $accent }}55">{{ $qi + 1 }}</div>
                    <div>
                        <span class="text-[10px] font-black px-2.5 py-0.5 rounded-lg mr-1.5"
                              style="background:{{ $tBg }};color:{{ $tColor }}">{{ $tLabel }}</span>
                        <span class="text-[10px] font-semibold text-slate-400">{{ $pts }} pts</span>
                    </div>
                </div>

                {{-- Status badge --}}
                @if($isEss)
                    <div class="flex items-center gap-1.5 text-[10px] font-black px-3 py-1.5 rounded-xl"
                         style="background:#EDE9FE;color:#6D28D9;border:1px solid #DDD6FE">
                        <i class="fa-solid fa-pen-nib text-[9px]"></i> Manual Grade
                    </div>
                @elseif($hasAnswered)
                    @if($isOk)
                    <div class="flex items-center gap-1.5 text-[10px] font-black px-3 py-1.5 rounded-xl"
                         style="background:#ECFDF5;color:#065F46;border:1px solid #A7F3D0">
                        <i class="fa-solid fa-circle-check text-[9px]"></i> Correct — {{ $pts }}/{{ $pts }} pts
                    </div>
                    @else
                    <div class="flex items-center gap-1.5 text-[10px] font-black px-3 py-1.5 rounded-xl"
                         style="background:#FEF2F2;color:#991B1B;border:1px solid #FECACA">
                        <i class="fa-solid fa-circle-xmark text-[9px]"></i> Wrong — 0/{{ $pts }} pts
                    </div>
                    @endif
                @else
                    <div class="text-[10px] font-bold px-3 py-1.5 rounded-xl"
                         style="background:#FEF3C7;color:#92400E;border:1px solid #FDE68A">
                        <i class="fa-solid fa-circle-exclamation text-[9px]"></i> Not answered
                    </div>
                @endif
            </div>
        </div>

        {{-- Card body --}}
        <div class="px-6 pt-3 pb-5">
            <p class="text-[14px] font-bold text-slate-900 leading-relaxed mb-4">{{ $question->content }}</p>

            @if(!empty($question->media_url))
            <img src="{{ asset($question->media_url) }}" class="mb-4 rounded-xl border border-slate-200 max-h-44 object-contain shadow-sm" alt="">
            @endif

            {{-- ── MCQ ── --}}
            @if($isMCQ)
            <div class="space-y-2">
                @foreach(['a'=>'A','b'=>'B','c'=>'C','d'=>'D'] as $k=>$L)
                @php
                    $txt = $question->{'option_'.$k} ?? '';
                    if(!$txt) continue;
                    $isCO = ($corr === $L);
                    // Match by letter OR by full option text
                    $isSO = ($sAns === $L || strtolower($sAns) === $k || $sAns === strtoupper($txt));
                    $cls  = 'md';
                    if($isSO && $isCO)      $cls='mc';
                    elseif($isSO && !$isCO) $cls='mw';
                    elseif($isCO)           $cls='mc';
                    $bc=['A'=>['#DBEAFE','#1E40AF'],'B'=>['#D1FAE5','#065F46'],'C'=>['#FEF3C7','#92400E'],'D'=>['#EDE9FE','#6D28D9']];
                    [$bb,$bt]=$bc[$L];
                @endphp
                <div class="mopt {{ $cls }}">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-[10px] font-black flex-shrink-0"
                         style="background:{{ $bb }};color:{{ $bt }}">{{ $L }}</div>
                    <span class="flex-1 text-[13px] font-semibold text-slate-800">{{ $txt }}</span>
                    @if($isCO && $isSO)
                        <span class="text-[10px] font-black text-emerald-700 flex items-center gap-1">
                            <i class="fa-solid fa-check-circle"></i> Correct — Student's Pick
                        </span>
                    @elseif($isSO && !$isCO)
                        <span class="text-[10px] font-black text-red-700 flex items-center gap-1">
                            <i class="fa-solid fa-xmark"></i> Student Picked
                        </span>
                    @elseif($isCO)
                        <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                            <i class="fa-solid fa-check"></i> Correct Answer
                        </span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── TRUE/FALSE ── --}}
            @if($isTF)
            @php
                $tCU = strtoupper($corr);
                $tSU = $sAns; // already uppercased
                // Normalise T/F shorthand
                if($tSU==='1'||$tSU==='T') $tSU='TRUE';
                if($tSU==='0'||$tSU==='F') $tSU='FALSE';
            @endphp
            <div class="flex gap-3">
                @foreach(['TRUE','FALSE'] as $to)
                @php
                    $isCO = ($tCU===$to);
                    $isSO = ($tSU===$to);
                    $cls  = '';
                    if($isCO&&$isSO)     $cls='tc';
                    elseif($isSO&&!$isCO)$cls='tw';
                    elseif($isCO)        $cls='tnc';
                    $ic  = $to==='TRUE'?'fa-check':'fa-xmark';
                    $icBg= ($isCO&&$isSO)?'#10B981':($isSO&&!$isCO?'#EF4444':($isCO?'#10B981':'#CBD5E1'));
                @endphp
                <div class="tfc {{ $cls }}">
                    <div class="flex items-center gap-2.5 mb-2">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white flex-shrink-0"
                             style="background:{{ $icBg }};box-shadow:{{ ($isCO||$isSO)?'0 4px 10px '.$icBg.'55':'' }}">
                            <i class="fa-solid {{ $ic }} text-sm"></i>
                        </div>
                        <span class="text-sm font-black {{ ($isCO&&$isSO)?'text-emerald-700':($isSO&&!$isCO?'text-red-700':'text-slate-800') }}">{{ $to }}</span>
                    </div>
                    @if($isCO && $isSO)
                        <span class="text-[9px] font-black text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded-lg">✓ Student's correct pick</span>
                    @elseif($isSO && !$isCO)
                        <span class="text-[9px] font-black text-red-700 bg-red-100 px-2 py-0.5 rounded-lg">✗ Student chose this</span>
                    @elseif($isCO)
                        <span class="text-[9px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Correct answer</span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif

            {{-- ── ESSAY ── --}}
            @if($isEss)
            @php
                $eText = $ans->answer_text ?? null;
                $hasPDF= !empty($submission->submission_file);
            @endphp

            @if(!empty($question->essay_rubric))
            <div class="flex gap-2.5 p-3.5 rounded-xl mb-3" style="background:#F5F3FF;border:1px solid #DDD6FE">
                <i class="fa-solid fa-clipboard-list text-purple-600 flex-shrink-0 mt-0.5"></i>
                <div>
                    <p class="text-[9px] font-black text-purple-900 uppercase tracking-widest mb-0.5">Grading Rubric</p>
                    <p class="text-[11px] font-medium text-purple-800 leading-relaxed">{{ $question->essay_rubric }}</p>
                </div>
            </div>
            @endif

            @if($eText)
            <div class="rounded-xl overflow-hidden border border-slate-200 mb-3">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-slate-200" style="background:#F8FAFC">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-user-pen text-purple-500"></i>
                        <span class="text-[10px] font-black text-slate-600 uppercase tracking-widest">Student's Answer</span>
                    </div>
                    <span class="text-[9px] font-bold text-slate-400">{{ str_word_count($eText) }} words</span>
                </div>
                <div class="p-4 max-h-52 overflow-y-auto" style="background:#FDFCFF">
                    <p class="text-[13px] text-slate-700 leading-loose font-medium italic">{{ $eText }}</p>
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-8 rounded-xl border-2 border-dashed border-slate-200 mb-3">
                <i class="fa-regular fa-file-lines text-3xl text-slate-300 mb-2"></i>
                <p class="text-xs font-bold text-slate-400">No written answer submitted</p>
            </div>
            @endif

            @if($hasPDF)
            <div class="rounded-xl overflow-hidden border border-slate-200">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-slate-200" style="background:#F8FAFC">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-file-pdf text-red-500 text-base"></i>
                        <span class="text-[11px] font-bold text-slate-800">{{ basename($submission->submission_file) }}</span>
                    </div>
                    <div class="flex gap-1.5">
                        <button type="button" onclick="openM()"
                                class="flex items-center gap-1 text-[10px] font-bold px-2.5 py-1.5 rounded-lg"
                                style="background:#fff;border:1px solid #E2E8F0;color:#475569">
                            <i class="fa-solid fa-expand text-[9px]"></i> Fullscreen
                        </button>
                        <a href="{{ route('teacher.submissions.download',['filename'=>$submission->submission_file]) }}" target="_blank"
                           class="flex items-center gap-1 text-[10px] font-bold px-2.5 py-1.5 rounded-lg"
                           style="background:#EFF6FF;border:1px solid #BFDBFE;color:#2563EB">
                            <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i> Open
                        </a>
                    </div>
                </div>
                <div class="h-52 flex flex-col items-center justify-center gap-3" style="background:linear-gradient(135deg,#F8FAFC,#F1F5F9)">
                    <div class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-md" style="background:linear-gradient(135deg,#EF4444,#DC2626)">
                        <i class="fa-solid fa-file-pdf text-white text-2xl"></i>
                    </div>
                    <p class="text-xs font-black text-slate-800">{{ basename($submission->submission_file) }}</p>
                    <a href="{{ route('teacher.submissions.download',['filename'=>$submission->submission_file]) }}" target="_blank"
                       class="flex items-center gap-1.5 text-[11px] font-black text-white px-4 py-2 rounded-xl shadow-md"
                       style="background:linear-gradient(135deg,#4F46E5,#2563EB)">
                        <i class="fa-solid fa-arrow-up-right-from-square text-[10px]"></i> View Document
                    </a>
                </div>
            </div>
            @endif
            @endif

        </div>
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-slate-200 fu">
        <i class="fa-solid fa-circle-exclamation text-3xl text-slate-300 mb-3"></i>
        <p class="text-sm font-bold text-slate-700">No questions found</p>
    </div>
    @endforelse

    </div>
    </div>

    {{-- Footer --}}
    <div class="h-12 bg-white border-t border-slate-200 flex items-center justify-between px-5 flex-shrink-0">
        <a href="{{ route('teacher.grading.queue') }}" class="flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fa-solid fa-arrow-left text-xs"></i> Back to Queue
        </a>
        <span class="flex items-center gap-1.5 text-[10px] font-semibold text-slate-400">
            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span>
            <span id="sl">Changes saved automatically</span>
        </span>
    </div>
</div>

{{-- ═══════════════════════ RIGHT PANEL ═══════════════════════ --}}
<div class="w-72 flex flex-col h-screen flex-shrink-0 overflow-hidden shadow-xl"
     style="background:#fff;border-left:1px solid #E2E8F0">

    <div class="px-5 py-4 flex-shrink-0"
         style="background:linear-gradient(135deg,#0F172A 0%,#1E3A5F 100%)">
        <div class="flex items-center gap-2.5 mb-3">
            <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="background:rgba(255,255,255,.12)">
                <i class="fa-solid fa-clipboard-check text-white text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-black text-white">Score Sheet</h2>
                <p class="text-[10px] text-white/45">Live-updating as you adjust</p>
            </div>
            <div class="ml-auto flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span>
                <span class="text-[9px] font-black text-white/40 uppercase tracking-widest">Live</span>
            </div>
        </div>
        <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl"
             style="background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.1)">
            <div class="w-7 h-7 rounded-full flex items-center justify-center text-white text-[10px] font-black flex-shrink-0"
                 style="background:linear-gradient(135deg,#4F46E5,#7C3AED)">
                {{ strtoupper(substr($submission->student->full_name ?? 'S', 0, 1)) }}
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-black text-white truncate">{{ $submission->student->full_name ?? 'Student' }}</p>
                <p class="text-[9px] text-white/45 font-mono">{{ $submission->student->institutional_id ?? '' }}</p>
            </div>
        </div>
    </div>

    <div class="flex-1 overflow-y-auto p-4 space-y-3.5">

        {{-- Score Ring --}}
        <div class="rounded-2xl p-5 text-center" style="background:linear-gradient(135deg,#F8FAFC,#EEF2FF);border:1.5px solid #C7D2FE">
            <p class="text-[9px] font-black text-indigo-500 uppercase tracking-widest mb-3">Grand Total Score</p>
            <div class="relative inline-block">
                <svg width="100" height="100" viewBox="0 0 100 100">
                    <circle class="rk" cx="50" cy="50" r="38"/>
                    <circle class="rf" cx="50" cy="50" r="38"
                            :stroke="totalScore() >= {{ $passThresh }} ? '#10B981' : '#EF4444'"
                            stroke-dasharray="238.8"
                            :stroke-dashoffset="238.8 - (238.8 * Math.min(totalScore(),{{ $totalPts }}) / {{ $totalPts }})"/>
                </svg>
                <div class="absolute inset-0 flex flex-col items-center justify-center">
                    <span class="text-2xl font-black text-slate-900 tabular-nums" x-text="totalScore()"></span>
                    <span class="text-[10px] font-bold text-slate-400">/ {{ $totalPts }}</span>
                </div>
            </div>
            <div class="mt-3 inline-flex items-center gap-1.5 text-[10px] font-black px-3 py-1.5 rounded-full"
                 :class="totalScore() >= {{ $passThresh }} ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                 :style="totalScore() >= {{ $passThresh }} ? 'border:1px solid #A7F3D0' : 'border:1px solid #FECACA'">
                <span x-text="totalScore() >= {{ $passThresh }} ? '✓ PASS' : '✗ FAIL'"></span>
                &nbsp;·&nbsp;
                <span x-text="Math.round(totalScore() / {{ $totalPts }} * 100) + '%'"></span>
            </div>
        </div>

        {{-- Auto-graded block --}}
        <div class="rounded-xl overflow-hidden" style="border:1.5px solid #E2E8F0">
            <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100" style="background:#F8FAFC">
                <i class="fa-solid fa-robot text-emerald-500"></i>
                <span class="text-[10px] font-black text-slate-800 uppercase tracking-wider">Auto-Graded</span>
                <span class="ml-auto text-sm font-black text-emerald-600 tabular-nums"
                      x-text="autoScore + ' / ' + autoPts"></span>
            </div>
            <div class="px-4 py-3 bg-white">
                <div class="h-2.5 rounded-full overflow-hidden" style="background:#F1F5F9">
                    <div class="h-full rounded-full transition-all duration-500"
                         style="background:linear-gradient(90deg,#10B981,#34D399)"
                         :style="'width:' + (autoPts > 0 ? Math.round(autoScore/autoPts*100) : 0) + '%'"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-[9px] font-semibold text-slate-400">MCQ + True/False</span>
                    <span class="text-[9px] font-bold text-slate-500"
                          x-text="(autoPts > 0 ? Math.round(autoScore/autoPts*100) : 0) + '%'"></span>
                </div>
            </div>
        </div>

        {{-- Essay rubric (only if exam has essay questions) --}}
        @if($hasEssay)
        <div class="rounded-xl overflow-hidden" style="border:1.5px solid #C4B5FD">
            <div class="flex items-center gap-2 px-4 py-3 border-b" style="background:linear-gradient(135deg,#F5F3FF,#EFF6FF);border-color:#C4B5FD">
                <i class="fa-solid fa-pen-nib text-purple-600"></i>
                <span class="text-[10px] font-black text-purple-900 uppercase tracking-wider">Manual Rubrics</span>
                <span class="ml-auto text-[11px] font-black text-purple-700"
                      x-text="essayTotal() + ' / {{ $essayMaxPts }} pts'"></span>
            </div>
            <div class="p-4 space-y-4 bg-white">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-600">📐 Accuracy</span>
                        <div class="text-[11px] font-black tabular-nums px-2 py-0.5 rounded-lg" style="background:#EEF2FF;color:#4338CA">
                            <span x-text="accuracy"></span>/10
                        </div>
                    </div>
                    <input type="range" name="accuracy" min="0" max="10" x-model.number="accuracy"
                           @input="upSlider($event.target)" class="w-full slider"
                           style="background:linear-gradient(to right,#4F46E5 0%,#4F46E5 0%,#E2E8F0 0%,#E2E8F0 100%)">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-600">🔭 Depth</span>
                        <div class="text-[11px] font-black tabular-nums px-2 py-0.5 rounded-lg" style="background:#EEF2FF;color:#4338CA">
                            <span x-text="depth"></span>/10
                        </div>
                    </div>
                    <input type="range" name="depth" min="0" max="10" x-model.number="depth"
                           @input="upSlider($event.target)" class="w-full slider"
                           style="background:linear-gradient(to right,#4F46E5 0%,#4F46E5 0%,#E2E8F0 0%,#E2E8F0 100%)">
                </div>
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-[11px] font-bold text-slate-600">✨ Clarity</span>
                        <div class="text-[11px] font-black tabular-nums px-2 py-0.5 rounded-lg" style="background:#EEF2FF;color:#4338CA">
                            <span x-text="clarity"></span>/5
                        </div>
                    </div>
                    <input type="range" name="clarity" min="0" max="5" x-model.number="clarity"
                           @input="upSlider($event.target)" class="w-full slider"
                           style="background:linear-gradient(to right,#4F46E5 0%,#4F46E5 0%,#E2E8F0 0%,#E2E8F0 100%)">
                </div>
                <div class="pt-3" style="border-top:1px solid #DDD6FE">
                    <div class="flex justify-between mb-1.5">
                        <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Essay sub-total</span>
                        <span class="text-[10px] font-black text-slate-800" x-text="essayTotal()+' / {{ $essayMaxPts }}'"></span>
                    </div>
                    <div class="h-2 rounded-full overflow-hidden" style="background:#EDE9FE">
                        <div class="h-full rounded-full transition-all duration-300"
                             style="background:linear-gradient(90deg,#7C3AED,#4F46E5)"
                             :style="`width: ${essayMax > 0 ? Math.round(essayTotal() / essayMax * 100) : 0}%`"></div>
                    </div>
                </div>
            </div>
        </div>
        @else
        <div class="rounded-xl p-4" style="background:#ECFDF5;border:1.5px solid #A7F3D0">
            <div class="flex items-start gap-3">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white flex-shrink-0" style="background:#10B981">
                    <i class="fa-solid fa-robot text-sm"></i>
                </div>
                <div>
                    <p class="text-[11px] font-black text-emerald-800 mb-1">Fully Auto-Graded</p>
                    <p class="text-[10px] text-emerald-700 leading-relaxed">No essay questions. All answers scored automatically.</p>
                </div>
            </div>
        </div>
        @endif

        {{-- Feedback --}}
        <div>
            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5 flex items-center gap-1.5">
                <i class="fa-regular fa-comment-dots text-slate-300"></i> Instructor Feedback
            </label>
            <textarea name="feedback" rows="3"
                      placeholder="Add feedback for the student…"
                      class="w-full rounded-xl p-3 text-xs font-medium placeholder-slate-300 resize-none focus:outline-none transition-all leading-relaxed"
                      style="background:#F8FAFC;border:1.5px solid #E2E8F0;color:#1E293B"
                      onfocus="this.style.borderColor='#4F46E5'" onblur="this.style.borderColor='#E2E8F0'">{{ $submission->teacher_feedback ?? '' }}</textarea>
        </div>

    </div>

    <div class="p-4 space-y-2 flex-shrink-0" style="border-top:1px solid #F1F5F9">
        <button type="submit"
                @click="document.getElementById('FA').value='save'; toast('Assessment saved!','success')"
                class="w-full flex items-center justify-center gap-2 font-black text-xs py-3 rounded-xl transition-all"
                style="background:linear-gradient(135deg,#4F46E5,#2563EB);color:#fff;box-shadow:0 4px 16px rgba(79,70,229,.35)">
            <i class="fa-solid fa-floppy-disk"></i> Save Assessment
        </button>
        <button type="submit"
                @click="document.getElementById('FA').value='save_next'"
                class="w-full flex items-center justify-center gap-2 font-bold text-xs py-2.5 rounded-xl transition-all"
                style="background:#F8FAFC;border:1.5px solid #E2E8F0;color:#475569">
            Save & Grade Next <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </button>
    </div>
</div>

</div>
</form>

{{-- Fullscreen modal --}}
<div id="M" class="hidden fixed inset-0 z-50 flex items-center justify-center p-6"
     style="background:rgba(15,23,42,.8);backdrop-filter:blur(4px)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-5xl h-[88vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 flex-shrink-0" style="background:#0F172A">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-file-pdf text-red-400 text-lg"></i>
                <p class="text-sm font-bold text-white">Student Submission — Fullscreen</p>
            </div>
            <button onclick="closeM()" class="w-8 h-8 rounded-xl flex items-center justify-center" style="background:rgba(255,255,255,.1)">
                <i class="fa-solid fa-xmark text-white text-sm"></i>
            </button>
        </div>
        <div class="flex-1 overflow-y-auto p-6 flex justify-center" style="background:#334155">
            <div class="bg-white w-full max-w-[720px] min-h-[600px] shadow-2xl p-12 rounded-sm text-slate-700 text-sm leading-relaxed">
                <p class="text-slate-400 italic text-center mt-16">Essay content displays here.</p>
            </div>
        </div>
    </div>
</div>

<div id="tb"></div>

<script>
// Clock
(function tick(){ const el=document.getElementById('lc'); if(el) el.textContent=new Date().toLocaleTimeString('en-US',{hour12:false}); setTimeout(tick,1000); })();

// Toast
function toast(m,t='info'){
    const c={info:'#4F46E5',success:'#10B981',warning:'#F59E0B'};
    const i={info:'fa-circle-info',success:'fa-circle-check',warning:'fa-triangle-exclamation'};
    const b=document.getElementById('tb'),el=document.createElement('div');
    el.className='toast';el.style.background=c[t]||c.info;
    el.innerHTML=`<i class="fa-solid ${i[t]||i.info}"></i>${m}`;
    b.appendChild(el);
    setTimeout(()=>{el.style.transition='all .3s';el.style.opacity='0';el.style.transform='translateY(8px)';setTimeout(()=>el.remove(),300)},3200);
}

// Modal
function openM(){document.getElementById('M').classList.remove('hidden')}
function closeM(){document.getElementById('M').classList.add('hidden')}
document.getElementById('M').addEventListener('click',function(e){if(e.target===this)closeM()});

// Alpine — autoPts now passed as 8th argument
function GWS(acc,dep,cla,auto,hasE,eMax,tMax,aPts){
    return{
        accuracy:acc, depth:dep, clarity:cla,
        autoScore:auto, hasEssay:hasE, essayMax:eMax, totalMax:tMax,
        autoPts:aPts,
        essayTotal(){ return +this.accuracy + +this.depth + +this.clarity; },
        totalScore(){ return this.autoScore + (this.hasEssay ? this.essayTotal() : 0); },
        upSlider(s){
            const p=(s.value/s.max)*100;
            s.style.background=`linear-gradient(to right,#4F46E5 0%,#4F46E5 ${p}%,#E2E8F0 ${p}%,#E2E8F0 100%)`;
            const el=document.getElementById('ss'); if(el) el.textContent='Unsaved…';
            const sl=document.getElementById('sl'); if(sl) sl.textContent='Unsaved changes';
        },
        init(){
            this.$nextTick(()=>{
                document.querySelectorAll('.slider').forEach(s=>this.upSlider(s));
            });
        }
    }
}
</script>
</body>
</html>