<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Pre-Exam Verification</title>
  <meta name="description" content="Complete identity and environment verification before entering your exam.">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }

    .brand-gradient { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); }

    .btn-pulse::after {
      content: '';
      position: absolute;
      inset: -4px;
      border-radius: 18px;
      background: linear-gradient(135deg, #4F6EF7, #7C3AED);
      opacity: 0;
      z-index: -1;
      animation: btnPulse 2s ease infinite;
    }
    @keyframes btnPulse {
      0%   { transform: scale(1);    opacity: 0.45; }
      70%  { transform: scale(1.05); opacity: 0; }
      100% { transform: scale(1.05); opacity: 0; }
    }

    .check-in { animation: checkIn 0.4s ease both; }
    @keyframes checkIn {
      from { opacity:0; transform: translateX(-10px); }
      to   { opacity:1; transform: translateX(0); }
    }

    .live-dot { animation: livePulse 1.5s ease infinite; }
    @keyframes livePulse {
      0%,100% { transform:scale(1);   opacity:1; }
      50%      { transform:scale(1.6); opacity:0.4; }
    }

    .spin { animation: spin 0.8s linear infinite; }
    @keyframes spin { to { transform: rotate(360deg); } }

    .cam-shimmer {
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.04), transparent);
      background-size: 200% 100%;
      animation: shimmer 2.5s ease infinite;
    }
    @keyframes shimmer { 0%{background-position:200%} 100%{background-position:-200%} }

    @keyframes fadeUp { from{opacity:0;transform:translateY(14px)} to{opacity:1;transform:translateY(0)} }
    .fade-up    { animation: fadeUp 0.5s ease both; }
    .fade-up-d1 { animation: fadeUp 0.5s 0.1s ease both; }
    .fade-up-d2 { animation: fadeUp 0.5s 0.2s ease both; }

    .gradient-text {
      background: linear-gradient(135deg, #4F6EF7, #7C3AED);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }
  </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col"
      x-data="verificationApp">

  <!-- ════ HEADER ════ -->
  <header class="bg-white border-b border-slate-100 px-6 py-3.5 sticky top-0 z-30 shadow-sm">
    <div class="max-w-6xl mx-auto flex items-center justify-between gap-4">

      <div class="flex items-center gap-3 min-w-0">
        <div class="w-10 h-10 brand-gradient rounded-xl flex items-center justify-center shadow-md shadow-indigo-200 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div class="min-w-0">
          <h1 class="font-black text-sm text-slate-900 leading-tight truncate">{{ $exam->title ?? 'Assessment Gateway' }}</h1>
          <div class="flex items-center gap-2 mt-0.5">
            <span class="flex items-center gap-1 text-[10px] font-black text-emerald-600 uppercase tracking-wider">
              <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 live-dot inline-block"></span>
              Live Session
            </span>
            <span class="text-slate-300 text-xs">•</span>
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ $exam->duration_minutes ?? 45 }} min</span>
          </div>
        </div>
      </div>

      <!-- Countdown -->
      <div class="hidden sm:flex flex-col items-center px-5 py-2 rounded-2xl border border-slate-100 bg-slate-50">
        <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-0.5">Session in</p>
        <p class="text-base font-black text-slate-900 tabular-nums" x-text="countdown"></p>
      </div>

      <div class="flex items-center gap-3 flex-shrink-0">
        <div class="text-right hidden sm:block">
          <p class="text-sm font-black text-slate-800 leading-none">{{ Auth::user()->full_name ?? 'Student' }}</p>
          <p class="text-[11px] font-mono text-slate-400 mt-0.5">ID: {{ Auth::user()->user_id ?? 'N/A' }}</p>
        </div>
        <div class="w-9 h-9 rounded-xl overflow-hidden bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-[11px] font-black text-amber-900 flex-shrink-0">
          @if(Auth::user() && Auth::user()->avatar_url)
            <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ Auth::user()->full_name }}">
          @else
            {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'YP' }}
          @endif
        </div>
      </div>
    </div>
  </header>

  <!-- ════ MAIN ════ -->
  <main class="flex-1 max-w-6xl w-full mx-auto px-4 py-10 flex flex-col items-center">

    <!-- Hero -->
    <div class="text-center mb-8 fade-up">
      <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-indigo-50 rounded-full mb-4">
        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 live-dot"></span>
        <span class="text-[11px] font-black text-indigo-600 uppercase tracking-wider">Pre-Exam Verification</span>
      </div>
      <h2 class="text-4xl font-black tracking-tight text-slate-900">Enter <span class="gradient-text">Exam Room</span></h2>
      <p class="text-slate-400 text-sm mt-2 max-w-md mx-auto leading-relaxed">Identity and environment checks must be completed before you can begin.</p>
    </div>

    <!-- Detail strip -->
    <div class="flex flex-wrap items-center justify-center gap-3 mb-8 fade-up-d1">
      <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-100 shadow-sm text-xs font-bold text-slate-600">
        <i data-lucide="clock" class="w-3.5 h-3.5 text-indigo-500"></i>
        {{ $exam->duration_minutes ?? 45 }} Minutes
      </div>
      <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-100 shadow-sm text-xs font-bold text-slate-600">
        <i data-lucide="help-circle" class="w-3.5 h-3.5 text-indigo-500"></i>
        {{ $exam->questions_count ?? '—' }} Questions
      </div>
      <div class="flex items-center gap-2 px-4 py-2 bg-white rounded-xl border border-slate-100 shadow-sm text-xs font-bold text-slate-600">
        <i data-lucide="key" class="w-3.5 h-3.5 text-indigo-500"></i>
        <span class="font-mono text-slate-500">{{ $exam->access_code ?? 'TOKEN' }}</span>
        <button onclick="copyToken()" class="text-indigo-400 hover:text-indigo-600 cursor-pointer ml-1 transition-colors" title="Copy token">
          <i data-lucide="copy" class="w-3 h-3" id="copy-icon"></i>
        </button>
      </div>
    </div>

    <!-- CARD -->
    <div class="w-full max-w-5xl bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden fade-up-d2">
      <div class="grid grid-cols-1 md:grid-cols-2">

        <!-- LEFT: Guidelines -->
        <div class="p-8 border-b md:border-b-0 md:border-r border-slate-100">
          <div class="flex items-center gap-2 mb-6">
            <div class="w-7 h-7 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
              <i data-lucide="list-checks" class="w-3.5 h-3.5 text-white"></i>
            </div>
            <h3 class="text-sm font-black text-slate-900">Exam Guidelines</h3>
          </div>

          <div class="space-y-3 mb-6">
            <div class="flex items-start gap-3.5 p-4 bg-amber-50 border border-amber-100 rounded-2xl check-in" style="animation-delay:0s">
              <div class="w-9 h-9 bg-amber-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="monitor-off" class="w-4 h-4 text-amber-600"></i>
              </div>
              <div>
                <h4 class="font-black text-sm text-slate-800">No tab switching</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">System auto-detects window focus changes. Three violations = disqualification.</p>
              </div>
            </div>

            <div class="flex items-start gap-3.5 p-4 bg-blue-50 border border-blue-100 rounded-2xl check-in" style="animation-delay:0.1s">
              <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="video" class="w-4 h-4 text-blue-600"></i>
              </div>
              <div>
                <h4 class="font-black text-sm text-slate-800">Keep camera on</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Webcam stream must remain active and visible throughout the entire session.</p>
              </div>
            </div>

            <div class="flex items-start gap-3.5 p-4 bg-indigo-50 border border-indigo-100 rounded-2xl check-in" style="animation-delay:0.2s">
              <div class="w-9 h-9 bg-indigo-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="shield-check" class="w-4 h-4 text-indigo-600"></i>
              </div>
              <div>
                <h4 class="font-black text-sm text-slate-800">Secure environment</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Browser is locked to this testing session. No extensions or other apps.</p>
              </div>
            </div>

            <div class="flex items-start gap-3.5 p-4 bg-rose-50 border border-rose-100 rounded-2xl check-in" style="animation-delay:0.3s">
              <div class="w-9 h-9 bg-rose-100 rounded-xl flex items-center justify-center flex-shrink-0">
                <i data-lucide="clock" class="w-4 h-4 text-rose-600"></i>
              </div>
              <div>
                <h4 class="font-black text-sm text-slate-800">Timer enforced</h4>
                <p class="text-xs text-slate-500 mt-0.5 leading-relaxed">Timer starts the moment you enter. Exam auto-submits at zero.</p>
              </div>
            </div>
          </div>

          <!-- Agree checkbox -->
          <label class="flex items-start gap-3 p-4 rounded-2xl border-2 cursor-pointer select-none transition-all duration-200"
                 :class="agreed ? 'bg-indigo-50 border-indigo-300' : 'bg-slate-50 border-slate-100 hover:border-slate-200'">
            <div class="relative flex-shrink-0 mt-0.5">
              <input type="checkbox" x-model="agreed" class="sr-only">
              <div class="w-5 h-5 rounded-md border-2 flex items-center justify-center transition-all"
                   :class="agreed ? 'brand-gradient border-transparent' : 'border-slate-300 bg-white'">
                <i data-lucide="check" class="w-3 h-3 text-white" x-show="agreed"></i>
              </div>
            </div>
            <span class="text-xs text-slate-600 leading-relaxed font-medium">
              I have read and agree to the <a href="#" class="text-indigo-600 font-bold underline hover:text-indigo-700">exam rules & academic integrity policy</a>. I understand violations lead to immediate disqualification.
            </span>
          </label>
        </div>

        <!-- RIGHT: System Verification -->
        <div class="p-8 flex flex-col gap-5">
          <div class="flex items-center gap-2">
            <div class="w-7 h-7 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
              <i data-lucide="monitor-check" class="w-3.5 h-3.5 text-white"></i>
            </div>
            <h3 class="text-sm font-black text-slate-900">System Verification</h3>
          </div>

          <!-- Camera feed -->
          <div class="relative aspect-video w-full rounded-2xl overflow-hidden bg-slate-900 border border-slate-700 shadow-inner">
            <video id="webcam-feed" autoplay playsinline muted class="absolute inset-0 w-full h-full object-cover" style="display:none"></video>

            <!-- Placeholder: shown when camera not yet granted -->
            <div id="cam-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-white text-center px-6 gap-4">
              <!-- Icon -->
              <div class="w-14 h-14 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center border border-white/20" id="cam-ph-icon-wrap">
                <i data-lucide="camera" class="w-6 h-6 text-white/60" id="cam-ph-icon"></i>
              </div>

              <!-- Status text -->
              <div>
                <p class="text-xs font-black text-white/90 uppercase tracking-wider" id="cam-ph-text">Camera not enabled</p>
                <p class="text-[11px] text-white/50 mt-1" id="cam-ph-sub">Click the button below to allow access</p>
              </div>

              <!-- Enable button: always visible so user can retry -->
              <button id="enable-cam-btn"
                      onclick="requestCamera()"
                      class="flex items-center gap-2 px-5 py-2.5 bg-white/15 hover:bg-white/25 border border-white/25 text-white text-xs font-black rounded-xl transition-all cursor-pointer backdrop-blur-sm">
                <i data-lucide="video" class="w-4 h-4"></i>
                Enable Camera &amp; Microphone
              </button>
            </div>

            <!-- LIVE badge -->
            <div id="cam-live-badge" class="hidden absolute top-3 left-3 flex items-center gap-1.5 bg-black/50 backdrop-blur-sm px-2.5 py-1 rounded-full border border-white/10">
              <span class="w-1.5 h-1.5 rounded-full bg-red-500 live-dot"></span>
              <span class="text-[10px] font-black text-white uppercase tracking-wider">Live</span>
            </div>

            <!-- Scan line effect -->
            <div class="cam-shimmer absolute inset-0 pointer-events-none"></div>
          </div>

          <!-- System checks -->
          <div class="space-y-2">
            <template x-for="check in systemChecks" :key="check.key">
              <div class="flex items-center justify-between px-4 py-3 rounded-xl border transition-all duration-300"
                   :class="checks[check.key]==='ok'?'bg-emerald-50 border-emerald-100':checks[check.key]==='fail'?'bg-red-50 border-red-100':'bg-slate-50 border-slate-100'">
                <div class="flex items-center gap-2.5">
                  <!-- Spinning -->
                  <template x-if="checks[check.key]==='checking'">
                    <svg class="w-4 h-4 spin text-slate-300" viewBox="0 0 24 24" fill="none">
                      <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" stroke-dasharray="40" stroke-dashoffset="15"/>
                    </svg>
                  </template>
                  <!-- OK -->
                  <template x-if="checks[check.key]==='ok'">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-500"></i>
                  </template>
                  <!-- Fail -->
                  <template x-if="checks[check.key]==='fail'">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-500"></i>
                  </template>

                  <i :data-lucide="check.icon" class="w-3.5 h-3.5"
                     :class="checks[check.key]==='ok'?'text-emerald-600':checks[check.key]==='fail'?'text-red-500':'text-slate-400'"></i>
                  <span class="text-xs font-black uppercase tracking-wider"
                        :class="checks[check.key]==='ok'?'text-emerald-700':checks[check.key]==='fail'?'text-red-600':'text-slate-500'"
                        x-text="check.label"></span>
                </div>
                <div class="flex items-center gap-2">
                  <span x-show="check.key==='net' && checks.net==='ok'" class="text-[10px] text-slate-400 font-mono" x-text="pingMs+'ms'"></span>
                  <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-0.5 rounded-md"
                        :class="checks[check.key]==='ok'?'bg-emerald-100 text-emerald-600':checks[check.key]==='fail'?'bg-red-100 text-red-600':'bg-slate-200 text-slate-400'"
                        x-text="checks[check.key]==='ok'?check.okLabel:checks[check.key]==='fail'?'Denied':'Checking…'"></span>
                </div>
              </div>
            </template>
          </div>

          <!-- Camera denied: step-by-step fix instructions -->
          <div x-show="checks.camera === 'fail'" x-cloak
               class="rounded-2xl border border-red-100 bg-red-50 overflow-hidden">
            <div class="px-4 py-3 bg-red-100 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4 text-red-600"></i>
                <p class="text-xs font-black text-red-700">Camera Access Denied — How to Fix</p>
              </div>
              <button onclick="requestCamera()"
                      class="flex items-center gap-1.5 px-3 py-1.5 bg-red-600 text-white text-[11px] font-black rounded-lg cursor-pointer hover:bg-red-700 transition-colors">
                <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                Retry
              </button>
            </div>
            <div class="px-4 py-3 space-y-2.5">
              <!-- Chrome -->
              <div class="flex items-start gap-2.5">
                <div class="w-5 h-5 rounded-md bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span class="text-[10px] font-black text-red-600">1</span>
                </div>
                <p class="text-xs text-red-700 font-medium leading-relaxed">
                  Click the <strong>🔒 lock icon</strong> (or camera icon) in your browser's address bar.
                </p>
              </div>
              <div class="flex items-start gap-2.5">
                <div class="w-5 h-5 rounded-md bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span class="text-[10px] font-black text-red-600">2</span>
                </div>
                <p class="text-xs text-red-700 font-medium leading-relaxed">
                  Set <strong>Camera</strong> and <strong>Microphone</strong> to <span class="font-black">Allow</span>.
                </p>
              </div>
              <div class="flex items-start gap-2.5">
                <div class="w-5 h-5 rounded-md bg-red-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                  <span class="text-[10px] font-black text-red-600">3</span>
                </div>
                <p class="text-xs text-red-700 font-medium leading-relaxed">
                  Click <strong>Retry</strong> above — or reload this page.
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- ── START BUTTON SECTION ── -->
      <div class="px-8 py-6 border-t border-slate-100 bg-gradient-to-b from-slate-50 to-white flex flex-col items-center gap-3">

        <!-- Mini status row -->
        <div class="flex items-center gap-4 mb-1">
          <template x-for="check in systemChecks" :key="check.key">
            <div class="flex items-center gap-1.5">
              <span class="w-2 h-2 rounded-full transition-colors duration-500"
                    :class="checks[check.key]==='ok'?'bg-emerald-500':checks[check.key]==='fail'?'bg-red-500':'bg-slate-300 animate-pulse'"></span>
              <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400" x-text="check.shortLabel"></span>
            </div>
          </template>
          <span class="h-3 w-px bg-slate-200"></span>
          <div class="flex items-center gap-1.5">
            <span class="w-2 h-2 rounded-full transition-colors duration-500" :class="agreed?'bg-emerald-500':'bg-slate-300'"></span>
            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Rules</span>
          </div>
        </div>

        <!-- Button -->
        <div class="relative w-full max-w-md">
          <a x-show="agreed && allChecksOk"
             x-cloak
             href="{{ route('exams.start', $exam->exam_id ?? 0) }}"
             class="btn-pulse relative w-full flex items-center justify-center gap-2.5 py-4 brand-gradient text-white font-black text-sm rounded-2xl shadow-lg shadow-indigo-200 hover:opacity-95 transition-all cursor-pointer">
            <i data-lucide="play-circle" class="w-5 h-5"></i>
            START EXAM NOW
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
          <button x-show="!(agreed && allChecksOk)"
                  disabled
                  class="w-full flex items-center justify-center gap-2.5 py-4 bg-slate-200 text-slate-400 font-black text-sm rounded-2xl cursor-not-allowed select-none">
            <i data-lucide="lock" class="w-4 h-4"></i>
            <span x-text="!agreed ? 'Agree to rules first' : 'Completing system checks…'"></span>
          </button>
        </div>

        <p class="text-[10px] text-slate-400 font-mono">
          Token: <span class="text-slate-600 font-black">{{ $exam->access_code ?? 'UNINITIALIZED' }}</span>
        </p>
      </div>
    </div>
  </main>

  <!-- ════ FOOTER ════ -->
  <footer class="border-t border-slate-100 bg-white px-6 py-4">
    <div class="max-w-6xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-3">
      <button onclick="window.history.back()"
              class="flex items-center gap-2 text-xs font-bold text-slate-400 hover:text-slate-600 cursor-pointer transition-colors">
        <i data-lucide="chevron-left" class="w-3.5 h-3.5"></i>
        Cancel & Go Back
      </button>
      <div class="flex items-center gap-5 text-[10px] font-bold uppercase tracking-widest text-slate-400">
        <span class="flex items-center gap-1.5"><i data-lucide="lock" class="w-3 h-3 text-slate-300"></i> 256-bit Encrypted</span>
        <span class="flex items-center gap-1.5"><i data-lucide="headphones" class="w-3 h-3 text-slate-300"></i> 24/7 Support</span>
        <span class="flex items-center gap-1.5"><i data-lucide="shield" class="w-3 h-3 text-slate-300"></i> AI Monitored</span>
      </div>
    </div>
  </footer>

  <script>
    // ── Global camera request (called by button AND by init) ──────────────
    let alpineApp = null; // reference to the Alpine component

    async function requestCamera() {
      const video  = document.getElementById('webcam-feed');
      const ph     = document.getElementById('cam-placeholder');
      const phText = document.getElementById('cam-ph-text');
      const phSub  = document.getElementById('cam-ph-sub');
      const badge  = document.getElementById('cam-live-badge');
      const btn    = document.getElementById('enable-cam-btn');

      // Show "requesting…" state
      if (phText) phText.textContent = 'Requesting access…';
      if (phSub)  phSub.textContent  = 'Please click Allow in the browser popup';
      if (btn)    btn.disabled = true;

      if (alpineApp) {
        alpineApp.checks.camera = 'checking';
        alpineApp.checks.mic    = 'checking';
      }

      try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true, audio: true });

        // Show live webcam feed
        video.srcObject = stream;
        video.style.display = 'block';
        if (ph)    ph.style.display = 'none';
        if (badge) badge.classList.remove('hidden');

        if (alpineApp) {
          alpineApp.checks.camera = 'ok';
          alpineApp.checks.mic    = 'ok';
        }
      } catch (err) {
        console.warn('Camera denied:', err);

        // Reset placeholder to show deny state
        if (video) video.style.display = 'none';
        if (ph)    ph.style.display = 'flex';
        if (badge) badge.classList.add('hidden');
        if (phText) phText.textContent = 'Camera Access Denied';
        if (phSub)  phSub.textContent  = 'Click Allow in your browser, then press Retry below';
        if (btn) {
          btn.disabled = false;
          btn.innerHTML = `<i data-lucide="refresh-cw" class="w-4 h-4"></i> Retry Camera Access`;
        }

        if (alpineApp) {
          alpineApp.checks.camera = 'fail';
          alpineApp.checks.mic    = 'fail';
        }
      }

      lucide.createIcons();
    }

    // ── Alpine app ─────────────────────────────────────────────────────────
    document.addEventListener('alpine:init', () => {
      Alpine.data('verificationApp', () => ({
        agreed: false,
        countdown: '--:--:--',
        pingMs: 0,

        checks: { camera: 'checking', mic: 'checking', net: 'checking' },

        systemChecks: [
          { key: 'camera', icon: 'video', label: 'Camera',     shortLabel: 'Cam', okLabel: 'Ready'  },
          { key: 'mic',    icon: 'mic',   label: 'Microphone', shortLabel: 'Mic', okLabel: 'Ready'  },
          { key: 'net',    icon: 'wifi',  label: 'Connection', shortLabel: 'Net', okLabel: 'Strong' },
        ],

        get allChecksOk() {
          return this.checks.camera === 'ok' && this.checks.mic === 'ok' && this.checks.net === 'ok';
        },

        startCountdown() {
          let secs = 15 * 60;
          const tick = () => {
            const h = String(Math.floor(secs / 3600)).padStart(2, '0');
            const m = String(Math.floor((secs % 3600) / 60)).padStart(2, '0');
            const s = String(secs % 60).padStart(2, '0');
            this.countdown = `${h}:${m}:${s}`;
            if (secs > 0) { secs--; setTimeout(tick, 1000); }
            else { this.countdown = 'NOW ✓'; }
          };
          tick();
        },

        async checkNetwork() {
          try {
            const t = performance.now();
            await fetch('/favicon.ico?_=' + Date.now(), { mode: 'no-cors', cache: 'no-store' });
            this.pingMs = Math.round(performance.now() - t);
            this.checks.net = 'ok';
          } catch {
            this.checks.net = 'fail';
          }
          lucide.createIcons();
        },

        init() {
          alpineApp = this; // expose to global requestCamera()
          lucide.createIcons();
          this.startCountdown();

          // Auto-request camera on load (user can also click button)
          setTimeout(() => requestCamera(), 300);
          setTimeout(() => this.checkNetwork(), 700);
          setInterval(() => this.checkNetwork(), 10000);
        }
      }));
    });

    function copyToken() {
      const token = '{{ $exam->access_code ?? "" }}';
      if (!token) return;
      navigator.clipboard.writeText(token).then(() => {
        const icon = document.getElementById('copy-icon');
        icon.setAttribute('data-lucide', 'check');
        lucide.createIcons();
        setTimeout(() => { icon.setAttribute('data-lucide', 'copy'); lucide.createIcons(); }, 2000);
      });
    }
  </script>
</body>
</html>