<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exam Submitted — ExamSystem</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }

    /* ── Brand gradient ───────────────────────────────── */
    .brand-grad { background: linear-gradient(135deg,#4F6EF7,#7C3AED); }
    .brand-text  { background: linear-gradient(135deg,#4F6EF7,#7C3AED);
                   -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text; }

    /* ── Animated success circle ─────────────────────── */
    @keyframes circleIn {
      from { stroke-dashoffset: 166; }
      to   { stroke-dashoffset: 0;   }
    }
    @keyframes checkIn {
      0%   { stroke-dashoffset: 50; opacity: 0; }
      50%  { opacity: 1; }
      100% { stroke-dashoffset: 0; }
    }
    @keyframes scaleIn {
      from { transform: scale(0); opacity: 0; }
      to   { transform: scale(1); opacity: 1; }
    }
    .svg-circle { animation: circleIn 0.7s cubic-bezier(.4,0,.2,1) 0.2s both; stroke-dasharray: 166; stroke-dashoffset: 166; }
    .svg-check  { animation: checkIn  0.4s cubic-bezier(.4,0,.2,1) 0.8s both; stroke-dasharray: 50;  stroke-dashoffset: 50;  }
    .icon-wrap  { animation: scaleIn  0.5s cubic-bezier(.4,0,.2,1) both; }

    /* ── Page entrance ───────────────────────────────── */
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(24px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .fade-up  { animation: fadeUp 0.5s cubic-bezier(.4,0,.2,1) both; }
    .delay-1  { animation-delay: 0.15s; }
    .delay-2  { animation-delay: 0.28s; }
    .delay-3  { animation-delay: 0.40s; }
    .delay-4  { animation-delay: 0.52s; }

    /* ── Confetti particles ───────────────────────────── */
    @keyframes confettiFall {
      0%   { transform: translateY(-80px) rotate(0deg);  opacity: 1; }
      100% { transform: translateY(120vh) rotate(720deg); opacity: 0; }
    }
    .confetti-piece {
      position: fixed;
      width: 9px;
      height: 9px;
      border-radius: 2px;
      animation: confettiFall linear both;
      pointer-events: none;
      z-index: 999;
    }

    /* ── Pulse ring ──────────────────────────────────── */
    @keyframes pulseRing {
      0%   { transform: scale(.9); box-shadow: 0 0 0 0 rgba(99,102,241,.4); }
      70%  { transform: scale(1);  box-shadow: 0 0 0 20px rgba(99,102,241,0); }
      100% { transform: scale(.9); box-shadow: 0 0 0 0 rgba(99,102,241,0); }
    }
    .pulse-ring { animation: pulseRing 2.5s ease-out 1s infinite; }

    /* ── Step timeline ───────────────────────────────── */
    .step-done  { background: linear-gradient(135deg,#10B981,#059669); color: white; }
    .step-now   { background: linear-gradient(135deg,#4F6EF7,#7C3AED); color: white; }
    .step-later { background: #F1F5F9; color: #94A3B8; }
  </style>
</head>

<body class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50 flex flex-col items-center justify-center p-5">

  <!-- ════ CONFETTI ════ -->
  <div id="confetti-container"></div>

  <!-- ════ MAIN CARD ════ -->
  <div class="w-full max-w-lg fade-up">

    <!-- Success icon -->
    <div class="flex justify-center mb-6 icon-wrap">
      <div class="relative">
        <div class="w-24 h-24 brand-grad rounded-full flex items-center justify-center shadow-2xl shadow-indigo-300 pulse-ring">
          <svg viewBox="0 0 52 52" class="w-12 h-12" fill="none">
            <circle class="svg-circle" cx="26" cy="26" r="25" stroke="rgba(255,255,255,0.3)" stroke-width="2" fill="none"/>
            <path class="svg-check"  d="M14 27l8 8 16-16" stroke="white" stroke-width="3"
                  stroke-linecap="round" stroke-linejoin="round" fill="none"/>
          </svg>
        </div>
      </div>
    </div>

    <!-- Headline -->
    <div class="text-center mb-7 fade-up delay-1">
      <h1 class="text-3xl font-black text-slate-900 mb-2 leading-tight">Exam Submitted!</h1>
      <p class="text-sm text-slate-500 font-medium max-w-xs mx-auto leading-relaxed">
        Your answers have been securely packaged and delivered to the grading server.
      </p>
    </div>

    <!-- Reference + stats card -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-xl overflow-hidden mb-4 fade-up delay-2">

      <!-- Reference ID -->
      <div class="px-6 py-5 border-b border-slate-100 bg-gradient-to-r from-indigo-50/60 to-purple-50/40">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1.5">Submission Reference</p>
        <div class="flex items-center gap-3">
          <p class="text-sm font-black font-mono text-indigo-700 flex-1 truncate">
            #{{ $submission->id ?? $submission->submission_id ?? ($exam->exam_id ?? 'N/A') }}
          </p>
          <button id="copyBtn" onclick="copyRef()"
                  class="flex items-center gap-1.5 px-3 py-1.5 border border-indigo-200 text-indigo-600 text-[10px] font-black rounded-lg hover:bg-indigo-50 cursor-pointer transition-colors">
            <i data-lucide="copy" id="copyIcon" class="w-3 h-3"></i>
            <span id="copyLabel">Copy</span>
          </button>
        </div>
      </div>

      <!-- Stats row -->
      <div class="grid grid-cols-3 divide-x divide-slate-100">
        <div class="px-5 py-4 text-center">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Answered</p>
          <p class="text-xl font-black text-slate-900">
            {{ $submission->total_answered ?? count($answers ?? []) ?? '—' }}
            <span class="text-sm text-slate-400 font-bold">/ {{ $exam->total_questions ?? count($exam->questions ?? []) ?? '—' }}</span>
          </p>
        </div>
        <div class="px-5 py-4 text-center">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Submitted At</p>
          <p class="text-xs font-black text-slate-900 leading-tight">
            {{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('M d, Y') : now()->format('M d, Y') }}<br>
            <span class="text-slate-400 font-bold">{{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('H:i A') : now()->format('H:i A') }}</span>
          </p>
        </div>
        <div class="px-5 py-4 text-center">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Grading</p>
          <span class="inline-flex items-center gap-1.5 text-[10px] font-black px-2.5 py-1 rounded-lg
            {{ ($submission->status ?? 'pending') === 'graded' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
            <span class="w-1.5 h-1.5 rounded-full
              {{ ($submission->status ?? 'pending') === 'graded' ? 'bg-emerald-500' : 'bg-amber-400 animate-pulse' }}">
            </span>
            {{ ($submission->status ?? 'pending') === 'graded' ? 'Graded' : 'Pending' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Next steps timeline -->
    <div class="bg-white border border-slate-100 rounded-3xl shadow-sm px-6 py-5 mb-4 fade-up delay-3">
      <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-4">What Happens Next</p>
      <div class="space-y-4">
        <!-- Step 1: done -->
        <div class="flex items-center gap-3.5">
          <div class="step-done w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200">
            <i data-lucide="check" class="w-4 h-4"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-black text-slate-900">Exam Submitted</p>
            <p class="text-[10px] text-slate-400 font-medium">Answers securely packaged</p>
          </div>
          <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-lg">Done</span>
        </div>

        <!-- Connector -->
        <div class="ml-4 w-px h-3 bg-slate-200"></div>

        <!-- Step 2: in progress -->
        <div class="flex items-center gap-3.5">
          <div class="step-now w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-indigo-200">
            <i data-lucide="loader" class="w-4 h-4 animate-spin"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-black text-slate-900">Grading in Progress</p>
            <p class="text-[10px] text-slate-400 font-medium">Auto-graded MCQ • Manual for essays</p>
          </div>
          <span class="text-[10px] font-black text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded-lg">Active</span>
        </div>

        <!-- Connector -->
        <div class="ml-4 w-px h-3 bg-slate-200"></div>

        <!-- Step 3: pending -->
        <div class="flex items-center gap-3.5">
          <div class="step-later w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-black text-slate-500">Results Published</p>
            <p class="text-[10px] text-slate-400 font-medium">Check your results page</p>
          </div>
          <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-2 py-0.5 rounded-lg">Soon</span>
        </div>
      </div>
    </div>

    <!-- CTA buttons -->
    <div class="flex flex-col gap-2 fade-up delay-4">
      <a href="{{ route('student.exams.ticket', $exam->exam_id ?? 1) }}"
         class="w-full flex items-center justify-center gap-2 py-3.5 brand-grad text-white font-black text-sm rounded-2xl shadow-lg shadow-indigo-200 hover:opacity-90 transition-opacity cursor-pointer">
        <i data-lucide="printer" class="w-4 h-4"></i>
        Print Exam Ticket / Receipt
      </a>
      <a href="{{ route('student.results.index') }}"
         class="w-full flex items-center justify-center gap-2 py-3.5 bg-white border border-slate-200 text-slate-700 font-black text-sm rounded-2xl hover:bg-slate-50 hover:shadow-sm transition-all cursor-pointer">
        <i data-lucide="bar-chart-2" class="w-4 h-4"></i>
        View Results & History
      </a>
      <a href="{{ route('student.dashboard') }}"
         class="w-full flex items-center justify-center gap-2 py-2.5 text-slate-400 text-xs font-bold hover:text-slate-600 cursor-pointer transition-colors">
        <i data-lucide="home" class="w-3.5 h-3.5"></i>
        Back to Dashboard
      </a>
    </div>

    <!-- Footer sig -->
    <div class="text-center mt-6 text-[10px] font-mono text-slate-300 fade-up delay-4">
      <i data-lucide="lock" class="w-3 h-3 inline mr-1 text-slate-300"></i>
      SIG: {{ substr(md5(Auth::user()->email ?? 'verify'), 0, 24) }}…
    </div>
  </div>

  <script>
    // ── Confetti burst ──────────────────────────────────────────────────────
    const colors = ['#4F6EF7','#7C3AED','#10B981','#F59E0B','#EF4444','#EC4899','#06B6D4'];
    const container = document.getElementById('confetti-container');

    for (let i = 0; i < 40; i++) {
      const el = document.createElement('div');
      el.className = 'confetti-piece';
      el.style.left = Math.random() * 100 + 'vw';
      el.style.background = colors[Math.floor(Math.random() * colors.length)];
      el.style.width = (Math.random() * 8 + 6) + 'px';
      el.style.height = (Math.random() * 8 + 6) + 'px';
      el.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
      el.style.animationDuration = (Math.random() * 2 + 2) + 's';
      el.style.animationDelay = (Math.random() * 1.5) + 's';
      container.appendChild(el);
    }

    // ── Copy reference ID ───────────────────────────────────────────────────
    function copyRef() {
      const ref = '#{{ $submission->id ?? $submission->submission_id ?? ($exam->exam_id ?? "N/A") }}';
      navigator.clipboard.writeText(ref).then(() => {
        document.getElementById('copyIcon').setAttribute('data-lucide', 'check');
        document.getElementById('copyLabel').textContent = 'Copied!';
        lucide.createIcons();
        setTimeout(() => {
          document.getElementById('copyIcon').setAttribute('data-lucide', 'copy');
          document.getElementById('copyLabel').textContent = 'Copy';
          lucide.createIcons();
        }, 2000);
      });
    }

    window.addEventListener('DOMContentLoaded', () => lucide.createIcons());
  </script>
</body>
</html>