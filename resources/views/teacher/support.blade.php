<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $platformName }} - Support & Help</title>
  <meta name="description" content="Get help and submit support tickets on {{ $platformName }} Teacher Portal.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }

    .brand-gradient { background: linear-gradient(135deg, #2563EB 0%, #1D4ED8 100%); }
    .navy-gradient { background: linear-gradient(135deg,#0B1836 0%,#152C5E 55%,#1E3A8A 100%); }

    .form-input { transition: all .2s ease; }
    .form-input:focus { outline: none; border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }

    .drop-zone-default { border: 2px dashed #E2E8F0; }
    .drop-zone-hover { border: 2px dashed #2563EB; background: rgba(37,99,235,.04); }
    .drop-zone-success { border: 2px solid #10B981; background: rgba(16,185,129,.04); }

    .toast { animation: toastIn .3s ease, toastOut .3s ease 3.7s forwards; }
    @keyframes toastIn { from { opacity:0; transform: translateY(16px);} to { opacity:1; transform: translateY(0);} }
    @keyframes toastOut { from { opacity:1; } to { opacity:0; } }

    @keyframes bellShake {
      0%,100% { transform: rotate(0deg); }
      20% { transform: rotate(-15deg); } 40% { transform: rotate(15deg); }
      60% { transform: rotate(-10deg); } 80% { transform: rotate(8deg); }
    }
    .bell-shake { animation: bellShake .5s ease; }

    .pulse-dot { animation: pulseDot 2s infinite; }
    @keyframes pulseDot { 0%,100% { opacity:1; } 50% { opacity:.4; } }
  </style>
</head>
<body class="bg-[#F8FAFC] text-slate-800 antialiased" x-data="supportApp">

<div class="flex min-h-screen">

  @include('partials.teacher-sidebar')

  <!-- ═══════ MAIN ═══════ -->
  <main class="flex-1 flex flex-col min-w-0">

    <!-- TOPBAR -->
    <header class="h-[72px] flex items-center justify-between px-7 sticky top-0 z-10 flex-shrink-0 navy-gradient">
      <div class="flex items-center gap-4">
        <div>
          <h1 class="text-xl font-black text-white tracking-tight">Support &amp; Help</h1>
          <p class="text-[11px] text-white/50 font-medium mt-0.5">Submit a ticket and track it in real time</p>
        </div>
        @if($tickets->count() > 0)
        <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold text-amber-200 px-3 py-1 rounded-full"
             style="background:rgba(251,191,36,.15);border:1px solid rgba(251,191,36,.3)">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-400 pulse-dot"></span>
          <span id="active-queue-pill">{{ $tickets->count() }} open ticket(s)</span>
        </div>
        @endif
      </div>

      <div class="flex items-center gap-3">
        <div class="hidden md:block text-xs font-bold text-white/70 px-3 py-2 rounded-lg font-mono tabular-nums"
             style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)" x-text="liveTime">--:--:--</div>

        <button id="ticket-bell-btn" @click="openDrawer = true; setBellCounter(0)" title="Support ticket updates"
                class="relative w-9 h-9 flex items-center justify-center rounded-xl text-white/70 hover:text-white transition-all"
                style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
          <i class="fa-solid fa-life-ring text-sm" id="ticket-bell-icon"></i>
          <span id="ticket-bell-counter"
                class="absolute -top-1.5 -right-1.5 bg-red-500 text-white font-black text-[9px] w-4 h-4 rounded-full flex items-center justify-center border-2 border-[#152C5E] hidden">0</span>
        </button>

        <div class="flex items-center gap-2.5 pl-3 cursor-pointer hover:opacity-80 transition-opacity"
             style="border-left:1px solid rgba(255,255,255,.15)"
             onclick="window.location.href='{{ route('teacher.settings') }}'">
          <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-white/20">
            <img src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'Instructor') }}"
                 class="w-full h-full object-cover" alt="Avatar">
          </div>
          <span class="text-sm font-semibold text-white/80 hidden sm:block">{{ Auth::user()->full_name ?? 'Instructor' }}</span>
        </div>
      </div>
    </header>

    <!-- PAGE BODY -->
    <div class="p-7 max-w-[1440px] w-full mx-auto space-y-6">

      @if(session('success'))
      <div class="flex items-center gap-3 px-5 py-3.5 rounded-2xl bg-emerald-50 border border-emerald-200">
        <i class="fa-solid fa-circle-check text-emerald-500"></i>
        <p class="text-sm font-bold text-emerald-700">{{ session('success') }}</p>
      </div>
      @endif

      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- ── LEFT: TICKET FORM ── -->
        <div class="lg:col-span-2 space-y-5">

          <div class="rounded-2xl border border-[#E2E8F0] bg-white">
            <div class="px-6 py-5 border-b border-[#E2E8F0] flex items-center gap-3">
              <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-sm">
                <i class="fa-solid fa-paper-plane text-white text-xs"></i>
              </div>
              <div>
                <h3 class="text-sm font-black text-[#0F172A]">Report a Problem</h3>
                <p class="text-[11px] text-[#94A3B8] font-medium mt-0.5">Admin usually responds within 2–4 hours.</p>
              </div>
            </div>

            <form method="POST" action="{{ route('teacher.support.store') }}" enctype="multipart/form-data"
                  class="px-6 py-6 space-y-5" id="support-form">
              @csrf

              <div>
                <label class="block text-xs font-black text-[#64748B] uppercase tracking-widest mb-2">
                  Issue Subject <span class="text-red-400">*</span>
                </label>
                <input type="text" name="subject" placeholder="e.g. Grading queue won't load submissions" required
                       class="form-input w-full px-4 py-3 rounded-xl text-sm font-medium border border-[#E2E8F0] bg-[#F8FAFC] focus:bg-white">
              </div>

              <div>
                <label class="block text-xs font-black text-[#64748B] uppercase tracking-widest mb-2">Issue Category</label>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                  <template x-for="cat in categories" :key="cat.value">
                    <label class="cursor-pointer">
                      <input type="radio" name="issue_category_ui" :value="cat.value" x-model="selectedCategory" class="hidden peer">
                      <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] text-center transition-all hover:border-[#CBD5E1]"
                           :class="selectedCategory === cat.value ? 'ring-2 ring-blue-500 border-blue-400 bg-blue-50' : ''">
                        <i :class="cat.icon" class="text-sm" :style="selectedCategory === cat.value ? 'color:#2563EB' : 'color:#94A3B8'"></i>
                        <span class="text-[11px] font-bold" :style="selectedCategory === cat.value ? 'color:#2563EB' : 'color:#64748B'" x-text="cat.label"></span>
                      </div>
                    </label>
                  </template>
                </div>
                <input type="hidden" name="category_label" :value="categories.find(c => c.value === selectedCategory)?.label">
              </div>

              <div>
                <label class="block text-xs font-black text-[#64748B] uppercase tracking-widest mb-2">Priority</label>
                <select name="priority" class="form-input w-full px-4 py-3 rounded-xl text-sm font-medium border border-[#E2E8F0] bg-[#F8FAFC]">
                  <option value="high" selected>High</option>
                  <option value="medium">Medium</option>
                  <option value="low">Low</option>
                </select>
              </div>

              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-black text-[#64748B] uppercase tracking-widest">
                    Description <span class="text-red-400">*</span>
                  </label>
                  <span class="text-[11px] font-semibold text-[#94A3B8]" x-text="descCount + '/500'"></span>
                </div>
                <textarea name="description" rows="5" maxlength="500" required
                          @input="descCount = $event.target.value.length"
                          placeholder="Describe what happened — include the exam or course name, and any error message you saw."
                          class="form-input w-full px-4 py-3 rounded-xl text-sm font-medium border border-[#E2E8F0] bg-[#F8FAFC] focus:bg-white resize-none leading-relaxed"></textarea>
              </div>

              <div>
                <label class="block text-xs font-black text-[#64748B] uppercase tracking-widest mb-2">
                  Screenshot <span class="text-[#94A3B8] font-medium normal-case tracking-normal">(optional)</span>
                </label>
                <div id="drop-zone" class="drop-zone-default relative rounded-2xl p-8 text-center cursor-pointer bg-[#F8FAFC] transition-all duration-200">
                  <input type="file" name="screenshot" id="file-input" accept="image/png,image/jpg,image/jpeg"
                         class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">
                  <div id="upload-prompt-view" class="space-y-2 pointer-events-none">
                    <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center mb-3 border border-[#E2E8F0] bg-white text-[#94A3B8]">
                      <i class="fa-solid fa-cloud-arrow-up"></i>
                    </div>
                    <p class="text-sm font-bold text-[#334155]">Drag &amp; drop a screenshot here</p>
                    <p class="text-xs text-[#94A3B8] font-medium">or <span class="text-blue-600 font-bold">browse files</span> · PNG, JPG up to 5MB</p>
                  </div>
                  <div id="upload-selected-view" class="hidden space-y-2 pointer-events-none">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 border border-emerald-200 mx-auto flex items-center justify-center mb-3">
                      <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    </div>
                    <p class="text-sm font-bold text-emerald-600" id="selected-file-name">filename.jpg</p>
                    <p class="text-xs text-emerald-500 font-semibold">Ready to attach</p>
                  </div>
                </div>
              </div>

              <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-[#94A3B8] font-medium flex items-center gap-1.5">
                  <i class="fa-solid fa-shield-halved text-emerald-500"></i> Your report goes straight to the admin team
                </p>
                <button type="submit"
                        class="flex items-center gap-2 px-6 py-3 brand-gradient text-white text-sm font-black rounded-xl shadow-md shadow-blue-200 hover:opacity-90 transition-all cursor-pointer">
                  <i class="fa-solid fa-paper-plane text-xs"></i> Send Report
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- ── RIGHT: QUEUE + FAQ + CONTACT ── -->
        <div class="space-y-5">

          <div class="rounded-2xl border border-[#E2E8F0] bg-white">
            <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-blue-400 pulse-dot inline-block"></span>
                <h3 class="text-xs font-black uppercase tracking-wider text-[#0F172A]">Active Queue</h3>
              </div>
              <span class="text-[10px] font-black px-2.5 py-1 rounded-lg bg-blue-50 text-blue-600" id="active-queue-count">
                {{ $tickets->count() }} ticket(s)
              </span>
            </div>

            <div class="p-3 space-y-3 max-h-96 overflow-y-auto" id="active-tickets-wrapper">
              @forelse($tickets as $ticket)
                <div class="p-4 rounded-xl border border-[#E2E8F0] bg-[#F8FAFC] hover:border-[#CBD5E1] transition-all"
                     id="ticket-card-{{ $ticket['ticket_id'] }}">
                  <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex-1 min-w-0">
                      <span class="text-[10px] font-mono font-bold text-[#94A3B8] block">{{ $ticket['ticket_no'] }}</span>
                      <h4 class="text-xs font-bold truncate mt-0.5 text-[#0F172A]">{{ $ticket['subject'] }}</h4>
                    </div>
                    @if($ticket['status'] === 'INVESTIGATING')
                      <span class="flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg bg-blue-50 text-blue-600 flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-400 pulse-dot"></span> In Review
                      </span>
                    @else
                      <span class="flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg bg-amber-50 text-amber-600 flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span> Pending
                      </span>
                    @endif
                  </div>
                  <p class="text-[11px] text-[#94A3B8] leading-relaxed line-clamp-2">{{ $ticket['description'] }}</p>
                  <div class="flex items-center gap-1.5 mt-2.5 text-[10px] text-[#94A3B8] font-medium">
                    <i class="fa-regular fa-clock"></i> <span>{{ $ticket['updated_at'] }}</span>
                  </div>
                </div>
              @empty
                <div id="empty-queue-placeholder" class="py-10 flex flex-col items-center justify-center text-center">
                  <div class="w-12 h-12 rounded-2xl bg-[#F1F5F9] flex items-center justify-center mb-3">
                    <i class="fa-solid fa-inbox text-[#94A3B8]"></i>
                  </div>
                  <h4 class="text-xs font-bold text-[#334155]">All clear!</h4>
                  <p class="text-[11px] text-[#94A3B8] mt-1 max-w-[160px] leading-relaxed">No active tickets. Submit a report if you need help.</p>
                </div>
              @endforelse
            </div>

            <div class="px-4 py-3 border-t border-[#E2E8F0]">
              <button @click="openDrawer = true; setBellCounter(0)"
                      class="w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold rounded-xl bg-[#F8FAFC] text-[#334155] hover:bg-[#F1F5F9] transition-all cursor-pointer">
                <i class="fa-solid fa-circle-check text-emerald-500"></i> View Resolved History
              </button>
            </div>
          </div>

          <div class="rounded-2xl border border-[#E2E8F0] bg-white">
            <div class="px-5 py-4 border-b border-[#E2E8F0] flex items-center gap-2">
              <i class="fa-regular fa-circle-question text-blue-600"></i>
              <h3 class="text-xs font-black uppercase tracking-wider text-[#0F172A]">Quick Help</h3>
            </div>
            <div class="p-3 space-y-1.5">
              <template x-for="faq in faqs" :key="faq.q">
                <div class="rounded-xl border border-[#E2E8F0] overflow-hidden">
                  <button @click="faq.open = !faq.open"
                          class="w-full flex items-center justify-between px-4 py-3 text-left hover:bg-[#F8FAFC] transition-colors cursor-pointer">
                    <span class="text-xs font-bold text-[#334155]" x-text="faq.q"></span>
                    <i class="fa-solid text-[10px] text-[#94A3B8] flex-shrink-0 ml-2" :class="faq.open ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                  </button>
                  <div x-show="faq.open" x-collapse class="px-4 pb-3">
                    <p class="text-[11px] text-[#94A3B8] leading-relaxed" x-text="faq.a"></p>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <div class="rounded-2xl p-5 brand-gradient text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>
            <div class="relative">
              <div class="w-9 h-9 bg-white/15 border border-white/20 rounded-xl flex items-center justify-center mb-3">
                <i class="fa-regular fa-envelope text-white"></i>
              </div>
              <h4 class="text-sm font-black">Still need help?</h4>
              <p class="text-xs text-blue-100 mt-1 leading-relaxed">Admin team is online Mon–Fri, 8AM–5PM. Average response under 2 hours.</p>
              <a href="mailto:support@examsystem.edu"
                 class="inline-flex items-center gap-1.5 mt-3 text-xs font-black text-white bg-white/15 border border-white/20 px-3 py-1.5 rounded-lg hover:bg-white/25 transition-colors">
                <i class="fa-regular fa-envelope text-[10px]"></i> Email Support
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>
</div>

<!-- RESOLVED HISTORY DRAWER -->
<div x-show="openDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden">
  <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm" @click="openDrawer = false"></div>
  <div class="absolute inset-y-0 right-0 w-full max-w-md flex flex-col bg-white border-l border-[#E2E8F0]"
       style="box-shadow:-8px 0 32px rgba(0,0,0,.12)">
    <div class="px-6 py-5 flex items-center justify-between brand-gradient text-white">
      <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-white/15 border border-white/20 rounded-xl flex items-center justify-center">
          <i class="fa-solid fa-circle-check"></i>
        </div>
        <div>
          <h2 class="text-sm font-black">Resolved History</h2>
          <p class="text-[11px] text-blue-100 mt-0.5">Tickets closed by admin</p>
        </div>
      </div>
      <button @click="openDrawer = false" class="p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="flex-1 overflow-y-auto p-5 space-y-3" id="resolved-drawer-body">
      <div class="py-16 flex flex-col items-center justify-center text-center" id="drawer-loading">
        <div class="w-10 h-10 rounded-2xl bg-[#F1F5F9] flex items-center justify-center mb-3">
          <i class="fa-solid fa-spinner fa-spin text-[#94A3B8]"></i>
        </div>
        <p class="text-xs text-[#94A3B8] font-medium">Loading resolved tickets…</p>
      </div>
    </div>
  </div>
</div>

<!-- TOASTS -->
<div id="toast-container" class="fixed bottom-6 right-6 z-50 space-y-2 pointer-events-none"></div>

<script>
  document.addEventListener('alpine:init', () => {
    Alpine.data('supportApp', () => ({
      openDrawer: false,
      liveTime: '',
      descCount: 0,
      selectedCategory: 'grading',

      categories: [
        { value: 'grading',    label: 'Grading',       icon: 'fa-solid fa-clipboard-check' },
        { value: 'monitoring', label: 'Monitoring',    icon: 'fa-solid fa-video' },
        { value: 'questions',  label: 'Question Bank', icon: 'fa-solid fa-database' },
        { value: 'roster',     label: 'Course/Roster', icon: 'fa-solid fa-users' },
        { value: 'account',    label: 'Account',       icon: 'fa-solid fa-user' },
        { value: 'other',      label: 'Other',         icon: 'fa-solid fa-circle-question' },
      ],

      faqs: [
        { q: "An exam won't publish — what do I do?", a: "Check that every question has a valid mark value and the start/end time is in the future. If it still fails, submit a ticket with the exam name and the exact error shown.", open: false },
        { q: "Grading queue isn't showing new submissions", a: "Refresh the page first — submissions sometimes take a few seconds to sync. If a specific student's submission is missing after a refresh, report it with the student and exam name.", open: false },
        { q: "Webcam monitoring feed is frozen or blank", a: "This is usually the student's connection, not your dashboard. Ask them to rejoin. If multiple students are affected at once, submit a ticket immediately — it may be a system-wide issue.", open: false },
      ],

      updateClock() {
        this.liveTime = new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      },

      setBellCounter(count) {
        const counter = document.getElementById('ticket-bell-counter');
        if (!counter) return;
        if (count > 0) { counter.innerText = count; counter.classList.remove('hidden'); }
        else { counter.classList.add('hidden'); }
      },

      init() {
        this.$watch('openDrawer', val => { if (val) runResolutionScan(); });
        this.updateClock();
        setInterval(() => this.updateClock(), 1000);
      }
    }));
  });

  document.getElementById('support-form').addEventListener('submit', function () {
    const subjectInput = this.querySelector('input[name="subject"]');
    const catLabelInput = this.querySelector('input[name="category_label"]');
    if (subjectInput && catLabelInput && catLabelInput.value && !subjectInput.value.includes(catLabelInput.value)) {
      subjectInput.value = `[${catLabelInput.value}] ${subjectInput.value}`;
    }
  });

  const fileInput = document.getElementById('file-input');
  const dropZone = document.getElementById('drop-zone');
  const promptView = document.getElementById('upload-prompt-view');
  const selectedView = document.getElementById('upload-selected-view');
  const fileNameText = document.getElementById('selected-file-name');

  fileInput.addEventListener('change', function () {
    if (this.files && this.files[0]) {
      fileNameText.innerText = this.files[0].name;
      promptView.classList.add('hidden');
      selectedView.classList.remove('hidden');
      dropZone.classList.remove('drop-zone-default');
      dropZone.classList.add('drop-zone-success');
    }
  });
  ['dragenter', 'dragover'].forEach(evt => dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.add('drop-zone-hover'); }));
  ['dragleave', 'drop'].forEach(evt => dropZone.addEventListener(evt, e => { e.preventDefault(); dropZone.classList.remove('drop-zone-hover'); }));

  function showToast(message, type = 'success') {
    const colors = type === 'success' ? 'bg-emerald-600 text-white' : 'bg-red-600 text-white';
    const icon = type === 'success' ? 'fa-circle-check' : 'fa-triangle-exclamation';
    const toast = document.createElement('div');
    toast.className = `toast pointer-events-auto flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold ${colors}`;
    toast.innerHTML = `<i class="fa-solid ${icon}"></i> ${message}`;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(() => toast.remove(), 4200);
  }

  function shakeBell() {
    const icon = document.getElementById('ticket-bell-icon');
    if (icon) { icon.classList.add('bell-shake'); setTimeout(() => icon.classList.remove('bell-shake'), 600); }
  }

  function renderResolvedTicket(item) {
    return `
      <div class="rounded-2xl border border-[#E2E8F0] overflow-hidden bg-[#F8FAFC]">
        <div class="px-5 py-4 border-b border-[#F1F5F9]">
          <div class="flex items-start justify-between gap-2">
            <div class="flex-1 min-w-0">
              <span class="text-[10px] font-mono font-bold text-[#94A3B8] block">${item.ticket_no}</span>
              <h5 class="text-xs font-bold mt-0.5 truncate text-[#1E293B]">${item.issue_category || item.subject}</h5>
            </div>
            <span class="flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg flex-shrink-0" style="background:#ecfdf5;color:#059669;">
              <span style="width:6px;height:6px;background:#10b981;border-radius:99px;display:inline-block;"></span> Resolved
            </span>
          </div>
        </div>
        <div class="px-5 py-4 space-y-3">
          <div class="text-[11px] leading-relaxed text-[#64748B] bg-white border border-[#F1F5F9] rounded-xl p-3">
            <span class="font-bold text-[#475569]">Your report:</span> ${item.description}
          </div>
          ${item.admin_comment ? `
          <div class="text-[11px] leading-relaxed text-blue-700 bg-blue-50 border border-blue-100 rounded-xl p-3">
            <span class="font-bold flex items-center gap-1.5 mb-1 text-[10px] uppercase tracking-wider">Admin Response:</span>
            ${item.admin_comment}
          </div>` : ''}
        </div>
      </div>`;
  }

  let cachedResolvedCount = null;

  function runResolutionScan() {
    fetch("{{ route('teacher.support.notifications') }}")
      .then(res => res.json())
      .then(data => {
        const drawerBody = document.getElementById('resolved-drawer-body');
        const loading = document.getElementById('drawer-loading');
        if (loading) loading.remove();

        drawerBody.innerHTML = '';
        if (!data.resolved_items || data.resolved_items.length === 0) {
          drawerBody.innerHTML = `
            <div style="text-align:center;padding:4rem 1rem;">
              <div style="width:48px;height:48px;background:#f1f5f9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                <i class="fa-solid fa-inbox" style="color:#94a3b8;"></i>
              </div>
              <p style="font-size:12px;font-weight:700;color:#475569;">No resolved tickets yet</p>
              <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Resolved tickets will appear here once admin closes them.</p>
            </div>`;
          cachedResolvedCount = 0;
          return;
        }

        data.resolved_items.forEach(item => {
          const activeCard = document.getElementById(`ticket-card-${item.ticket_id}`);
          if (activeCard) activeCard.remove();
          drawerBody.innerHTML += renderResolvedTicket(item);
        });

        const remainingCards = document.querySelectorAll('#active-tickets-wrapper [id^="ticket-card-"]').length;
        const queueCount = document.getElementById('active-queue-count');
        if (queueCount) queueCount.innerText = remainingCards + ' ticket(s)';
        const queuePill = document.getElementById('active-queue-pill');
        if (queuePill) queuePill.innerText = remainingCards + ' open ticket(s)';

        if (remainingCards === 0) {
          const wrapper = document.getElementById('active-tickets-wrapper');
          if (wrapper && !document.getElementById('empty-queue-placeholder')) {
            wrapper.innerHTML = `
              <div id="empty-queue-placeholder" style="padding:2.5rem 1rem;text-align:center;">
                <div style="width:48px;height:48px;background:#f1f5f9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                  <i class="fa-solid fa-inbox" style="color:#94a3b8;"></i>
                </div>
                <p style="font-size:12px;font-weight:700;color:#475569;">All clear!</p>
                <p style="font-size:11px;color:#94a3b8;margin-top:4px;">No active tickets right now.</p>
              </div>`;
          }
        }

        const newCount = data.count;
        if (cachedResolvedCount !== null && newCount > cachedResolvedCount) {
          const diff = newCount - cachedResolvedCount;
          const counter = document.getElementById('ticket-bell-counter');
          if (counter) { counter.innerText = diff; counter.classList.remove('hidden'); }
          shakeBell();
          showToast(`${diff} ticket(s) resolved by admin!`, 'success');
        }
        cachedResolvedCount = newCount;
      })
      .catch(err => console.warn('Support notification poll failed:', err));
  }

  runResolutionScan();
  setInterval(runResolutionScan, 5000);
</script>
</body>
</html>
