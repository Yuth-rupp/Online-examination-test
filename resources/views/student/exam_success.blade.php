<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Exam Submitted — {{ $platformName }}</title>
  <meta name="description" content="Your exam has been submitted successfully on {{ $platformName }}.">

  <!-- Anti-flash dark mode -->
  <script>
    (function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
      }
    })();
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }

    /* ── Brand ───────────────────────────────────────── */
    .brand-grad { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); }

    /* ── Confetti ────────────────────────────────────── */
    @keyframes confettiFall {
      0%   { transform: translateY(-60px) rotate(0deg);   opacity: 1; }
      80%  { opacity: 1; }
      100% { transform: translateY(105vh) rotate(680deg); opacity: 0; }
    }
    .confetti {
      position: fixed;
      border-radius: 2px;
      animation: confettiFall linear forwards;
      pointer-events: none;
      z-index: 0;
    }

    /* ── Success icon ────────────────────────────────── */
    @keyframes scaleIn {
      from { transform: scale(0) rotate(-15deg); opacity: 0; }
      to   { transform: scale(1) rotate(0deg);   opacity: 1; }
    }
    @keyframes circleDraw {
      from { stroke-dashoffset: 200; }
      to   { stroke-dashoffset: 0;   }
    }
    @keyframes checkDraw {
      0%   { stroke-dashoffset: 60; opacity: 0; }
      40%  { opacity: 1; }
      100% { stroke-dashoffset: 0; }
    }
    @keyframes outerPulse {
      0%,100% { transform: scale(1);   opacity: .5; }
      50%      { transform: scale(1.18); opacity: 0; }
    }

    .icon-wrap { animation: scaleIn 0.55s cubic-bezier(.34,1.56,.64,1) 0.1s both; }
    .svg-ring  { stroke-dasharray: 200; stroke-dashoffset: 200; animation: circleDraw 0.7s ease 0.5s both; }
    .svg-tick  { stroke-dasharray: 60;  stroke-dashoffset: 60;  animation: checkDraw 0.4s ease 1.1s both; }
    .pulse-ring { animation: outerPulse 2.2s ease-out 1.5s infinite; }

    /* ── Fade-up stagger ─────────────────────────────── */
    @keyframes fadeUp {
      from { opacity: 0; transform: translateY(20px); }
      to   { opacity: 1; transform: translateY(0);    }
    }
    .fu   { animation: fadeUp 0.5s cubic-bezier(.4,0,.2,1) both; }
    .d1   { animation-delay: .20s; }
    .d2   { animation-delay: .34s; }
    .d3   { animation-delay: .46s; }
    .d4   { animation-delay: .58s; }
    .d5   { animation-delay: .70s; }

    /* ── Step line ───────────────────────────────────── */
    .step-done   { background: linear-gradient(135deg,#10B981,#059669); }
    .step-active { background: linear-gradient(135deg,#4F6EF7,#7C3AED); }
    .step-soon   { background: #E2E8F0; }
    .dark .step-soon { background: #334155; }

    /* ── Toast slide ─────────────────────────────────── */
    @keyframes toastIn {
      from { opacity:0; transform:translateY(-12px) scale(.97); }
      to   { opacity:1; transform:translateY(0) scale(1); }
    }
    .toast-in { animation: toastIn .3s cubic-bezier(.4,0,.2,1) both; }

    /* ── Print ───────────────────────────────────────── */
    @media print {
      body { background: white !important; }
      .no-print { display: none !important; }
      .print-card {
        border: 2px dashed #0f172a !important;
        box-shadow: none !important;
        border-radius: 0 !important;
        background: white !important;
        max-width: 100% !important;
      }
    }
  </style>
</head>

<body class="min-h-full flex items-center justify-center p-4 sm:p-8 relative overflow-x-hidden transition-colors duration-200"
      :class="darkMode ? 'bg-slate-900 text-slate-100' : 'bg-gradient-to-br from-indigo-50 via-white to-purple-50 text-slate-800'"
      x-data="{
        darkMode: document.documentElement.classList.contains('dark'),
        toastOpen: true,
        copied: false,

        toggleDark() {
          this.darkMode = !this.darkMode;
          document.documentElement.classList.toggle('dark', this.darkMode);
          localStorage.setItem('darkMode', this.darkMode);
        },

        copyRef() {
          const txt = document.getElementById('refId').textContent.trim();
          navigator.clipboard.writeText(txt).then(() => {
            this.copied = true;
            setTimeout(() => this.copied = false, 2200);
          });
        },

        init() {
          if (localStorage.getItem('darkMode') === 'true') this.darkMode = true;
          setTimeout(() => this.toastOpen = false, 6000);
          lucide.createIcons();
        }
      }">

  <!-- ════ CONFETTI (generated by JS below) ════ -->
  <div id="confettiWrap" class="no-print" aria-hidden="true"></div>

  <!-- ════ TOAST ════ -->
  <div x-show="toastOpen" x-cloak
       class="no-print toast-in fixed top-5 right-5 z-50 max-w-[340px] w-full rounded-2xl border shadow-2xl p-4 flex items-start gap-3"
       :class="darkMode
         ? 'bg-slate-800 border-slate-700 shadow-slate-950/50'
         : 'bg-white border-slate-100 shadow-indigo-100/60'">
    <div class="w-9 h-9 brand-grad rounded-xl flex items-center justify-center flex-shrink-0 shadow-md shadow-indigo-200">
      <i data-lucide="mail" class="w-4 h-4 text-white"></i>
    </div>
    <div class="flex-1 min-w-0">
      <p class="text-xs font-black" :class="darkMode?'text-slate-100':'text-slate-900'">Receipt Confirmed</p>
      <p class="text-[11px] mt-0.5 leading-relaxed" :class="darkMode?'text-slate-400':'text-slate-500'">
        Your submission reference has been recorded in the institutional registry.
      </p>
    </div>
    <button @click="toastOpen = false"
            class="p-1 rounded-lg cursor-pointer transition-colors"
            :class="darkMode?'text-slate-500 hover:text-slate-300':'text-slate-400 hover:text-slate-600'">
      <i data-lucide="x" class="w-3.5 h-3.5"></i>
    </button>
  </div>

  <!-- ════ DARK MODE TOGGLE ════ -->
  <button @click="toggleDark()"
          class="no-print fixed top-5 left-5 z-50 w-9 h-9 rounded-xl border cursor-pointer transition-all flex items-center justify-center"
          :class="darkMode
            ? 'bg-slate-800 border-slate-700 text-amber-400 hover:bg-slate-700'
            : 'bg-white border-slate-200 text-slate-500 hover:bg-slate-50 shadow-sm'">
    <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-4 h-4"></i>
  </button>

  <!-- ════ MAIN CARD ════ -->
  <div class="print-card relative z-10 w-full max-w-lg rounded-3xl border shadow-2xl overflow-hidden transition-colors duration-200"
       :class="darkMode
         ? 'bg-slate-800 border-slate-700 shadow-slate-950/50'
         : 'bg-white border-slate-100 shadow-indigo-100/60'">

    <!-- Top accent bar -->
    <div class="brand-grad h-1.5 w-full"></div>

    <div class="px-8 pt-8 pb-7 space-y-7">

      <!-- ── Success icon ── -->
      <div class="flex flex-col items-center text-center gap-4 fu">
        <div class="relative icon-wrap">
          <!-- Outer pulse ring -->
          <div class="pulse-ring absolute inset-0 rounded-full brand-grad opacity-30"></div>

          <!-- SVG circle + check -->
          <div class="relative w-20 h-20">
            <svg viewBox="0 0 80 80" fill="none" class="w-20 h-20 -rotate-90">
              <!-- Background circle -->
              <circle cx="40" cy="40" r="36" stroke="#E0E7FF" stroke-width="3" fill="none"/>
              <!-- Animated ring -->
              <circle class="svg-ring" cx="40" cy="40" r="36"
                      stroke="url(#gr)" stroke-width="3.5" fill="none"
                      stroke-linecap="round"/>
              <defs>
                <linearGradient id="gr" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#4F6EF7"/>
                  <stop offset="100%" stop-color="#7C3AED"/>
                </linearGradient>
              </defs>
            </svg>
            <!-- Check -->
            <svg viewBox="0 0 80 80" fill="none" class="w-20 h-20 absolute inset-0">
              <path class="svg-tick" d="M24 41l11 11 21-21"
                    stroke="url(#grc)" stroke-width="4.5"
                    stroke-linecap="round" stroke-linejoin="round"/>
              <defs>
                <linearGradient id="grc" x1="0%" y1="0%" x2="100%" y2="100%">
                  <stop offset="0%"   stop-color="#4F6EF7"/>
                  <stop offset="100%" stop-color="#7C3AED"/>
                </linearGradient>
              </defs>
            </svg>
          </div>
        </div>

        <div class="fu d1">
          <h1 class="text-2xl sm:text-3xl font-black tracking-tight leading-tight"
              :class="darkMode?'text-white':'text-slate-900'">
            Exam Submitted!
          </h1>
          <p class="text-sm mt-1.5 max-w-[280px] mx-auto leading-relaxed font-medium"
             :class="darkMode?'text-slate-400':'text-slate-500'">
            Your answers are securely packaged and sent to the grading server.
          </p>
        </div>
      </div>

      <!-- ── Reference card ── -->
      <div class="rounded-2xl border p-5 space-y-4 fu d2 transition-colors duration-200"
           :class="darkMode?'bg-slate-900/60 border-slate-700':'bg-slate-50/80 border-slate-100'">

        <!-- Reference ID -->
        <div class="text-center space-y-2">
          <p class="text-[9px] font-black uppercase tracking-widest"
             :class="darkMode?'text-slate-500':'text-slate-400'">Submission Reference</p>
          <div class="flex items-center justify-center gap-2.5 flex-wrap">
            <span id="refId"
                  class="font-mono text-xs sm:text-sm font-black px-4 py-2 rounded-xl border select-all"
                  :class="darkMode?'bg-indigo-900/30 border-indigo-700 text-indigo-300':'bg-indigo-50 border-indigo-100 text-indigo-700'">
              #{{ $submission->exam_id ?? ($exam->exam_id ?? 'N/A') }}
            </span>
            <button @click="copyRef()"
                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl border text-[11px] font-black cursor-pointer transition-all"
                    :class="copied
                      ? (darkMode?'bg-emerald-900/30 border-emerald-700 text-emerald-400':'bg-emerald-50 border-emerald-200 text-emerald-600')
                      : (darkMode?'border-slate-600 text-slate-400 hover:bg-slate-700':'border-slate-200 text-slate-500 hover:bg-slate-100')">
              <i :data-lucide="copied?'check':'copy'" class="w-3.5 h-3.5"></i>
              <span x-text="copied?'Copied!':'Copy'"></span>
            </button>
          </div>
        </div>

        <div class="h-px" :class="darkMode?'bg-slate-700':'bg-slate-200'"></div>

        <!-- Stats row -->
        <div class="grid grid-cols-2 gap-4 text-xs">
          <div class="space-y-1">
            <p class="text-[9px] font-black uppercase tracking-widest"
               :class="darkMode?'text-slate-500':'text-slate-400'">Submitted</p>
            <p class="font-black" :class="darkMode?'text-slate-200':'text-slate-800'">
              {{ isset($submission->created_at)
                ? \Carbon\Carbon::parse($submission->created_at)->timezone('Asia/Phnom_Penh')->format('M d, Y')
                : \Carbon\Carbon::now('Asia/Phnom_Penh')->format('M d, Y') }}
            </p>
            <p class="font-bold" :class="darkMode?'text-slate-400':'text-slate-500'">
              {{ isset($submission->created_at)
                ? \Carbon\Carbon::parse($submission->created_at)->timezone('Asia/Phnom_Penh')->format('h:i A')
                : \Carbon\Carbon::now('Asia/Phnom_Penh')->format('h:i A') }}
            </p>
          </div>
          <div class="space-y-1 text-right">
            <p class="text-[9px] font-black uppercase tracking-widest"
               :class="darkMode?'text-slate-500':'text-slate-400'">Grading Status</p>
            <div class="inline-flex items-center gap-1.5 text-[11px] font-black px-2.5 py-1 rounded-xl border"
                 :class="darkMode?'bg-amber-900/20 border-amber-700 text-amber-400':'bg-amber-50 border-amber-100 text-amber-600'">
              <span class="w-1.5 h-1.5 rounded-full bg-amber-400 animate-pulse no-print"></span>
              Pending Grading
            </div>
            <p class="text-[10px] font-medium" :class="darkMode?'text-slate-500':'text-slate-400'">
              MCQ auto-graded · Essays manual
            </p>
          </div>
        </div>
      </div>

      <!-- ── Next steps ── -->
      <div class="rounded-2xl border px-5 py-4 fu d3 transition-colors duration-200"
           :class="darkMode?'bg-slate-900/40 border-slate-700':'bg-white border-slate-100'">
        <p class="text-[9px] font-black uppercase tracking-widest mb-4"
           :class="darkMode?'text-slate-500':'text-slate-400'">What Happens Next</p>

        <!-- Step 1 -->
        <div class="flex items-center gap-3 mb-2">
          <div class="step-done w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-emerald-200">
            <i data-lucide="check" class="w-4 h-4 text-white"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-black" :class="darkMode?'text-slate-200':'text-slate-900'">Exam Submitted</p>
            <p class="text-[10px] font-medium" :class="darkMode?'text-slate-500':'text-slate-400'">All responses securely recorded</p>
          </div>
          <span class="text-[10px] font-black px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-600 border border-emerald-100">Done</span>
        </div>

        <!-- Connector -->
        <div class="ml-4 mb-2 w-px h-4" :class="darkMode?'bg-slate-700':'bg-slate-200'"></div>

        <!-- Step 2 -->
        <div class="flex items-center gap-3 mb-2">
          <div class="step-active w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 shadow-sm shadow-indigo-200">
            <i data-lucide="loader" class="w-4 h-4 text-white animate-spin"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-black" :class="darkMode?'text-slate-200':'text-slate-900'">Grading in Progress</p>
            <p class="text-[10px] font-medium" :class="darkMode?'text-slate-500':'text-slate-400'">MCQ auto-graded · Essays reviewed by instructor</p>
          </div>
          <span class="text-[10px] font-black px-2 py-0.5 rounded-lg bg-indigo-50 text-indigo-600 border border-indigo-100">Active</span>
        </div>

        <!-- Connector -->
        <div class="ml-4 mb-2 w-px h-4" :class="darkMode?'bg-slate-700':'bg-slate-200'"></div>

        <!-- Step 3 -->
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0"
               :class="darkMode?'bg-slate-700':'bg-slate-100'">
            <i data-lucide="bar-chart-2" class="w-4 h-4" :class="darkMode?'text-slate-400':'text-slate-400'"></i>
          </div>
          <div class="flex-1">
            <p class="text-xs font-black" :class="darkMode?'text-slate-400':'text-slate-500'">Results Published</p>
            <p class="text-[10px] font-medium" :class="darkMode?'text-slate-500':'text-slate-400'">View score in Results & History</p>
          </div>
          <span class="text-[10px] font-black px-2 py-0.5 rounded-lg"
                :class="darkMode?'bg-slate-700 text-slate-400':'bg-slate-100 text-slate-400'">Soon</span>
        </div>
      </div>

      <!-- ── CTA buttons ── -->
      <div class="flex flex-col gap-2.5 no-print fu d4">
        <button onclick="window.print()"
                class="w-full flex items-center justify-center gap-2 py-3.5 brand-grad text-white font-black text-sm rounded-2xl shadow-lg shadow-indigo-200 hover:opacity-90 cursor-pointer transition-opacity">
          <i data-lucide="printer" class="w-4 h-4"></i>
          Print Scorecard Receipt
        </button>
        <a href="{{ route('student.history') }}"
           class="w-full flex items-center justify-center gap-2 py-3.5 border font-black text-sm rounded-2xl cursor-pointer transition-all"
           :class="darkMode
             ? 'bg-slate-700 border-slate-600 text-slate-200 hover:bg-slate-600'
             : 'bg-white border-slate-200 text-slate-700 hover:bg-slate-50 hover:shadow-sm'">
          <i data-lucide="eye" class="w-4 h-4"></i>
          View Results & History
        </a>
        <a href="{{ route('student.dashboard') }}"
           class="w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold cursor-pointer transition-colors"
           :class="darkMode?'text-slate-500 hover:text-slate-300':'text-slate-400 hover:text-slate-600'">
          <i data-lucide="home" class="w-3.5 h-3.5"></i>
          Back to Dashboard
        </a>
      </div>

      <!-- ── Footer sig ── -->
      <div class="text-center flex items-center justify-center gap-1.5 text-[10px] font-mono uppercase tracking-widest fu d5 pt-1 border-t"
           :class="darkMode?'text-slate-600 border-slate-700':'text-slate-400 border-slate-100'">
        <i data-lucide="lock" class="w-3 h-3"></i>
        SIG: {{ substr(md5($submission->exam_id ?? 'proctor'), 0, 8) }}…C91E
      </div>

    </div>
  </div>

  <script>
    // ── Confetti burst ─────────────────────────────────────────────────────
    (function () {
      const colors = [
        '#4F6EF7','#7C3AED','#10B981','#F59E0B',
        '#EF4444','#EC4899','#06B6D4','#84CC16'
      ];
      const wrap = document.getElementById('confettiWrap');
      for (let i = 0; i < 55; i++) {
        const el = document.createElement('div');
        el.className = 'confetti';
        const size = Math.random() * 9 + 5;
        Object.assign(el.style, {
          left:              (Math.random() * 100) + 'vw',
          top:               0,
          width:             size + 'px',
          height:            size + 'px',
          borderRadius:      Math.random() > .5 ? '50%' : '2px',
          background:        colors[Math.floor(Math.random() * colors.length)],
          animationDuration: (Math.random() * 2.5 + 2) + 's',
          animationDelay:    (Math.random() * 1.8) + 's',
        });
        wrap.appendChild(el);
      }
    })();

    // ── Lucide init ────────────────────────────────────────────────────────
    document.addEventListener('DOMContentLoaded', () => lucide.createIcons());

    // Re-render Lucide after Alpine mutations (dark-mode icon swap, copy icon)
    document.addEventListener('alpine:init', () => {
      document.addEventListener('alpine:initialized', () => lucide.createIcons());
    });
  </script>
</body>
</html>