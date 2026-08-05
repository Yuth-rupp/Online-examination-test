<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $platformName }} - Support & Help</title>
  <meta name="description" content="Get help and submit support tickets on {{ $platformName }} Student Portal.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Anti-Flash Dark Mode -->
  <script>
    (function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
      }
    })();
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }

    /* Scrollbar */
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
    .dark ::-webkit-scrollbar-thumb { background: #334155; }

    /* Shared Design Tokens */
    .brand-gradient { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); }
    .nav-active { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); color: white; box-shadow: 0 4px 14px rgba(79,110,247,0.35); }
    .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }

    /* Input focus ring */
    .form-input { transition: all 0.2s ease; }
    .form-input:focus { outline: none; border-color: #4F6EF7; box-shadow: 0 0 0 3px rgba(79,110,247,0.12); }

    /* Drag-drop zone states */
    .drop-zone-default { border: 2px dashed #E2E8F0; }
    .drop-zone-hover { border: 2px dashed #4F6EF7; background: rgba(79,110,247,0.04); }
    .drop-zone-success { border: 2px solid #10B981; background: rgba(16,185,129,0.04); }

    /* Drawer slide animation */
    .drawer-enter { transform: translateX(100%); }
    .drawer-open { transform: translateX(0); transition: transform 0.3s cubic-bezier(0.4,0,0.2,1); }

    /* Toast */
    .toast { animation: toastIn 0.3s ease, toastOut 0.3s ease 3.7s forwards; }
    @keyframes toastIn { from { opacity:0; transform: translateY(16px); } to { opacity:1; transform: translateY(0); } }
    @keyframes toastOut { from { opacity:1; } to { opacity:0; } }

    /* Modal entrance */
    .modal-box { animation: modalIn 0.22s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }

    /* Bell shake on new notification */
    @keyframes bellShake {
      0%, 100% { transform: rotate(0deg); }
      20% { transform: rotate(-15deg); }
      40% { transform: rotate(15deg); }
      60% { transform: rotate(-10deg); }
      80% { transform: rotate(8deg); }
    }
    .bell-shake { animation: bellShake 0.5s ease; }

    /* Status dot pulse */
    .pulse-dot { animation: pulseDot 2s infinite; }
    @keyframes pulseDot { 0%, 100% { opacity:1; } 50% { opacity:0.4; } }
  </style>
  @include('partials.notification-styles')
</head>

<body class="min-h-screen flex antialiased transition-colors duration-300"
      :class="darkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'"
      x-data="supportApp">

  <!-- ═══════════════════════════════════════
       SIDEBAR (resized to match teacher)
  ════════════════════════════════════════ -->
  <aside class="w-64 flex flex-col fixed h-full z-30 hidden md:flex border-r transition-colors duration-300"
         :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">

    <!-- Logo -->
    <div class="px-5 pt-6 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 brand-gradient rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div>
          <h1 class="font-black text-sm leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ $platformName }}</h1>
          <p class="text-[11px] font-medium text-slate-400">Student Portal</p>
        </div>
      </div>
    </div>

    <p class="px-5 pt-4 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Main Menu</p>

    <!-- Nav Links (resized to match teacher sidebar) -->
    <nav class="px-3 space-y-1.5 flex-1">
      <a href="{{ route('student.dashboard') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
        Dashboard
      </a>
      <a href="{{ route('student.exams') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="book-open" class="w-5 h-5 flex-shrink-0"></i>
        My Exams
      </a>
      <a href="{{ route('student.history') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="history" class="w-5 h-5 flex-shrink-0"></i>
        History
      </a>

      <p class="px-2 pt-5 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Resources</p>

      <a href="{{ route('student.support') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold nav-active">
        <i data-lucide="headphones" class="w-5 h-5 flex-shrink-0"></i>
        Support
      </a>
      <a href="{{ route('student.settings') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="settings-2" class="w-5 h-5 flex-shrink-0"></i>
        Settings
      </a>
    </nav>

    <!-- User Footer -->
    <div class="p-3 m-3 rounded-2xl border transition-colors"
         :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
      <div class="flex items-center gap-3">
        <div class="relative flex-shrink-0">
          <div class="w-9 h-9 rounded-xl overflow-hidden bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center shadow-sm">
            @if(Auth::user() && Auth::user()->avatar_url)
              <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ Auth::user()->full_name }}">
            @else
              <span class="text-xs font-black text-amber-900 uppercase">
                {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'YP' }}
              </span>
            @endif
          </div>
          <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2"
               :class="darkMode ? 'border-slate-800' : 'border-slate-50'"></div>
        </div>
        <div class="flex-1 overflow-hidden">
          <h4 class="text-xs font-bold truncate" :class="darkMode ? 'text-white' : 'text-slate-900'">
            {{ Auth::user()->full_name ?? 'You Phatyuth' }}
          </h4>
          <p class="text-[11px] text-slate-400 font-medium truncate">
            {{ Auth::user()->institutional_id ?? Auth::user()->user_id ?? 'STU-1122-3344' }}
          </p>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">
          @csrf
          <button type="submit" title="Sign out"
                  class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors cursor-pointer">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- ═══════════════════════════════════════
       MAIN CONTENT
  ════════════════════════════════════════ -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">

    <!-- TOPBAR -->
    <header class="border-b px-6 py-3.5 flex items-center justify-between sticky top-0 z-20 transition-colors duration-300"
            :class="darkMode ? 'bg-slate-900/95 border-slate-800 backdrop-blur-xl' : 'bg-white/95 border-slate-100 backdrop-blur-xl'">
      <div class="flex items-center gap-2 text-sm font-semibold text-slate-400">
        <i data-lucide="headphones" class="w-4 h-4 text-indigo-500"></i>
        <span>Support & Help</span>
      </div>

      <div class="flex items-center gap-2 ml-4">
        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode);"
                class="p-2.5 rounded-xl transition-colors cursor-pointer"
                :class="darkMode ? 'bg-slate-800 text-amber-400 hover:bg-slate-700' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
          <i data-lucide="sun" class="w-4 h-4" x-show="darkMode"></i>
          <i data-lucide="moon" class="w-4 h-4" x-show="!darkMode"></i>
        </button>

        @include('partials.notification-bell')

        <!-- Support Ticket Status Bell -->
        <button id="ticket-bell-btn" @click="openDrawer = true" title="Support ticket updates"
                class="relative p-2.5 rounded-xl transition-colors cursor-pointer"
                :class="darkMode ? 'bg-slate-800 text-slate-400 hover:bg-slate-700' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
          <i data-lucide="life-buoy" class="w-4 h-4" id="ticket-bell-icon"></i>
          <span id="ticket-bell-counter"
                class="absolute -top-1 -right-1 bg-red-500 text-white font-black text-[9px] w-4 h-4 rounded-full flex items-center justify-center border border-white dark:border-slate-800 hidden">0</span>
        </button>

        <div class="w-px h-6 mx-1" :class="darkMode ? 'bg-slate-700' : 'bg-slate-200'"></div>

        <div class="flex items-center gap-2.5 pl-1">
          <div class="w-8 h-8 rounded-xl overflow-hidden bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-[11px] font-black text-amber-900 shadow-sm">
            @if(Auth::user() && Auth::user()->avatar_url)
              <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ Auth::user()->full_name }}">
            @else
              {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'YP' }}
            @endif
          </div>
          <div class="hidden sm:block">
            <p class="text-sm font-bold leading-none" :class="darkMode ? 'text-white' : 'text-slate-800'">
              {{ Auth::user()->full_name ?? 'You Phatyuth' }}
            </p>
            <p class="text-[11px] text-slate-400 mt-0.5">Student</p>
          </div>
        </div>
      </div>
    </header>

    <!-- PAGE BODY -->
    <div class="p-6 lg:p-8 flex-1 max-w-[1440px] w-full mx-auto space-y-7">

      <!-- PAGE HEADER -->
      <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-md shadow-indigo-200 dark:shadow-indigo-900/30">
              <i data-lucide="headphones" class="w-4 h-4 text-white"></i>
            </div>
            <h2 class="text-2xl font-black tracking-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">Support & Help</h2>
          </div>
          <p class="text-sm text-slate-400 font-medium ml-12">Submit a ticket and track your support requests in real time.</p>
        </div>

        <!-- Live Clock -->
        <div class="flex items-center gap-2.5 px-4 py-2.5 rounded-2xl border flex-shrink-0"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="w-7 h-7 brand-gradient rounded-lg flex items-center justify-center flex-shrink-0">
            <i data-lucide="clock" class="w-3.5 h-3.5 text-white"></i>
          </div>
          <div>
            <p class="text-xs font-black tabular-nums" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="liveTime"></p>
            <p class="text-[10px] text-slate-400 font-medium" x-text="liveDate"></p>
          </div>
        </div>
      </div>

      <!-- MAIN GRID -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

        <!-- ── LEFT: TICKET FORM ───────────────────── -->
        <div class="lg:col-span-2 space-y-5">

          <!-- Form Card -->
          <div class="rounded-2xl border"
               :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">

            <!-- Form Header -->
            <div class="px-6 py-5 border-b flex items-center gap-3"
                 :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
              <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-sm">
                <i data-lucide="send" class="w-4 h-4 text-white"></i>
              </div>
              <div>
                <h3 class="text-sm font-black" :class="darkMode ? 'text-white' : 'text-slate-900'">Submit a Support Request</h3>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Our team usually responds within 2–4 hours.</p>
              </div>
            </div>

            <form method="POST" action="{{ route('student.support.store') }}" enctype="multipart/form-data"
                  class="px-6 py-6 space-y-5" id="support-form">
              @csrf

              <!-- Subject -->
              <div>
                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                  Issue Subject <span class="text-red-400">*</span>
                </label>
                <input type="text" name="subject"
                       placeholder="e.g. Cannot open webcam during exam"
                       required
                       class="form-input w-full px-4 py-3 rounded-xl text-sm font-medium border"
                       :class="darkMode ? 'bg-slate-800 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-slate-50 border-slate-200 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white'">
              </div>

              <!-- Category -->
              <div>
                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                  Issue Category
                </label>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                  <template x-for="cat in categories" :key="cat.value">
                    <label class="cursor-pointer">
                      <input type="radio" name="category" :value="cat.value" x-model="selectedCategory" class="hidden peer">
                      <div class="flex flex-col items-center gap-1.5 p-3 rounded-xl border text-center transition-all peer-checked:ring-2 peer-checked:ring-indigo-500 peer-checked:border-indigo-400"
                           :class="darkMode ? 'border-slate-700 bg-slate-800 hover:border-slate-600 peer-checked:bg-indigo-500/10' : 'border-slate-200 bg-slate-50 hover:border-slate-300 peer-checked:bg-indigo-50'">
                        <i :data-lucide="cat.icon" class="w-4 h-4 peer-checked:text-indigo-500"
                           :class="selectedCategory === cat.value ? 'text-indigo-500' : 'text-slate-400'"></i>
                        <span class="text-[11px] font-bold"
                              :class="selectedCategory === cat.value ? 'text-indigo-500' : (darkMode ? 'text-slate-300' : 'text-slate-600')"
                              x-text="cat.label"></span>
                      </div>
                    </label>
                  </template>
                </div>
              </div>

              <!-- Description -->
              <div>
                <div class="flex items-center justify-between mb-2">
                  <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                    Description <span class="text-red-400">*</span>
                  </label>
                  <span class="text-[11px] font-semibold text-slate-400" x-text="descCount + '/500'"></span>
                </div>
                <textarea name="description" rows="5"
                          placeholder="Describe what happened in detail — include any error messages, steps to reproduce, and what you were doing when the issue occurred."
                          required maxlength="500"
                          @input="descCount = $event.target.value.length"
                          class="form-input w-full px-4 py-3 rounded-xl text-sm font-medium border resize-none leading-relaxed"
                          :class="darkMode ? 'bg-slate-800 border-slate-700 text-white placeholder-slate-500 focus:border-indigo-500' : 'bg-slate-50 border-slate-200 text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:bg-white'"></textarea>
              </div>

              <!-- File Upload -->
              <div>
                <label class="block text-xs font-black text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-2">
                  Screenshot Attachment <span class="text-slate-400 font-medium normal-case tracking-normal">(optional)</span>
                </label>
                <div id="drop-zone"
                     class="drop-zone-default relative rounded-2xl p-8 text-center cursor-pointer transition-all duration-200"
                     :class="darkMode ? 'bg-slate-800' : 'bg-slate-50'">
                  <input type="file" name="screenshot" id="file-input"
                         accept="image/png,image/jpg,image/jpeg"
                         class="absolute inset-0 opacity-0 cursor-pointer w-full h-full z-10">

                  <!-- Default state -->
                  <div id="upload-prompt-view" class="space-y-2 pointer-events-none">
                    <div class="w-12 h-12 rounded-2xl mx-auto flex items-center justify-center mb-3 border"
                         :class="darkMode ? 'bg-slate-700 border-slate-600 text-slate-400' : 'bg-white border-slate-200 text-slate-400'">
                      <i data-lucide="upload-cloud" class="w-5 h-5"></i>
                    </div>
                    <p class="text-sm font-bold" :class="darkMode ? 'text-slate-300' : 'text-slate-700'">
                      Drag & drop your screenshot here
                    </p>
                    <p class="text-xs text-slate-400 font-medium">or <span class="text-indigo-500 font-bold">browse files</span> · PNG, JPG up to 5MB</p>
                  </div>

                  <!-- Selected state -->
                  <div id="upload-selected-view" class="hidden space-y-2 pointer-events-none">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 mx-auto flex items-center justify-center mb-3">
                      <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
                    </div>
                    <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400" id="selected-file-name">filename.jpg</p>
                    <p class="text-xs text-emerald-500 font-semibold">✓ Ready to attach</p>
                  </div>
                </div>
              </div>

              <!-- Submit -->
              <div class="flex items-center justify-between pt-2">
                <p class="text-xs text-slate-400 font-medium flex items-center gap-1.5">
                  <i data-lucide="shield-check" class="w-3.5 h-3.5 text-emerald-500"></i>
                  Your request is encrypted and secure
                </p>
                <button type="submit" id="submit-btn"
                        class="flex items-center gap-2 px-6 py-3 brand-gradient text-white text-sm font-black rounded-xl shadow-md shadow-indigo-200 dark:shadow-indigo-900/30 hover:opacity-90 transition-all cursor-pointer">
                  <i data-lucide="send" class="w-4 h-4"></i>
                  Send Request
                </button>
              </div>
            </form>
          </div>

          <!-- Success / Error Flash -->
          @if(session('success'))
          <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30">
            <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500 flex-shrink-0"></i>
            <p class="text-sm font-bold text-emerald-700 dark:text-emerald-400">{{ session('success') }}</p>
          </div>
          @endif
          @if(session('error'))
          <div class="flex items-center gap-3 px-5 py-4 rounded-2xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/30">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 flex-shrink-0"></i>
            <p class="text-sm font-bold text-red-700 dark:text-red-400">{{ session('error') }}</p>
          </div>
          @endif

        </div>

        <!-- ── RIGHT: QUEUE + FAQ ──────────────────── -->
        <div class="space-y-5">

          <!-- Active Queue -->
          <div class="rounded-2xl border"
               :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
            <div class="px-5 py-4 border-b flex items-center justify-between"
                 :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
              <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-400 pulse-dot inline-block"></span>
                <h3 class="text-xs font-black uppercase tracking-wider" :class="darkMode ? 'text-white' : 'text-slate-900'">Active Queue</h3>
              </div>
              <span class="text-[10px] font-black px-2.5 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400"
                    id="active-queue-count">
                {{ $tickets->count() }} ticket(s)
              </span>
            </div>

            <div class="p-3 space-y-3 max-h-96 overflow-y-auto" id="active-tickets-wrapper">
              @forelse($tickets as $ticket)
                <div class="p-4 rounded-xl border transition-all"
                     id="ticket-card-{{ $ticket['ticket_id'] }}"
                     :class="darkMode ? 'bg-slate-800 border-slate-700 hover:border-slate-600' : 'bg-slate-50 border-slate-100 hover:border-slate-200'">
                  <div class="flex items-start justify-between gap-2 mb-2">
                    <div class="flex-1 min-w-0">
                      <span class="text-[10px] font-mono font-bold text-slate-400 block">{{ $ticket['ticket_no'] }}</span>
                      <h4 class="text-xs font-bold truncate mt-0.5" :class="darkMode ? 'text-white' : 'text-slate-900'">
                        {{ $ticket['subject'] }}
                      </h4>
                    </div>
                    @if($ticket['status'] === 'INVESTIGATING')
                      <span class="flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 pulse-dot"></span>
                        In Review
                      </span>
                    @else
                      <span class="flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 flex-shrink-0">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                        Pending
                      </span>
                    @endif
                  </div>
                  <p class="text-[11px] text-slate-400 leading-relaxed line-clamp-2">{{ $ticket['description'] }}</p>
                  <div class="flex items-center gap-1.5 mt-2.5 text-[10px] text-slate-400 font-medium">
                    <i data-lucide="clock" class="w-3 h-3"></i>
                    <span>{{ $ticket['updated_at'] }}</span>
                  </div>
                </div>
              @empty
                <div id="empty-queue-placeholder"
                     class="py-10 flex flex-col items-center justify-center text-center">
                  <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                    <i data-lucide="inbox" class="w-5 h-5 text-slate-400"></i>
                  </div>
                  <h4 class="text-xs font-bold" :class="darkMode ? 'text-slate-300' : 'text-slate-700'">All clear!</h4>
                  <p class="text-[11px] text-slate-400 mt-1 max-w-[160px] leading-relaxed">No active tickets. Submit a request if you need help.</p>
                </div>
              @endforelse
            </div>

            <!-- View resolved button -->
            <div class="px-4 py-3 border-t" :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
              <button @click="openDrawer = true"
                      class="w-full flex items-center justify-center gap-2 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer"
                      :class="darkMode ? 'bg-slate-800 text-slate-300 hover:bg-slate-700' : 'bg-slate-50 text-slate-600 hover:bg-slate-100'">
                <i data-lucide="check-circle-2" class="w-3.5 h-3.5 text-emerald-500"></i>
                View Resolved History
              </button>
            </div>
          </div>

          <!-- Quick Help FAQ -->
          <div class="rounded-2xl border"
               :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
            <div class="px-5 py-4 border-b flex items-center gap-2"
                 :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
              <i data-lucide="help-circle" class="w-4 h-4 text-indigo-500"></i>
              <h3 class="text-xs font-black uppercase tracking-wider" :class="darkMode ? 'text-white' : 'text-slate-900'">Quick Help</h3>
            </div>
            <div class="p-3 space-y-1.5">
              <template x-for="faq in faqs" :key="faq.q">
                <div class="rounded-xl border overflow-hidden"
                     :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
                  <button @click="faq.open = !faq.open"
                          class="w-full flex items-center justify-between px-4 py-3 text-left transition-colors cursor-pointer"
                          :class="darkMode ? 'hover:bg-slate-800' : 'hover:bg-slate-50'">
                    <span class="text-xs font-bold" :class="darkMode ? 'text-slate-200' : 'text-slate-800'" x-text="faq.q"></span>
                    <i :data-lucide="faq.open ? 'chevron-up' : 'chevron-down'" class="w-3.5 h-3.5 text-slate-400 flex-shrink-0 ml-2"></i>
                  </button>
                  <div x-show="faq.open" x-collapse class="px-4 pb-3">
                    <p class="text-[11px] text-slate-400 leading-relaxed" x-text="faq.a"></p>
                  </div>
                </div>
              </template>
            </div>
          </div>

          <!-- Contact info block -->
          <div class="rounded-2xl p-5 brand-gradient text-white relative overflow-hidden">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/5 rounded-full pointer-events-none"></div>
            <div class="relative">
              <div class="w-9 h-9 bg-white/15 border border-white/20 rounded-xl flex items-center justify-center mb-3">
                <i data-lucide="mail" class="w-4 h-4 text-white"></i>
              </div>
              <h4 class="text-sm font-black">Still need help?</h4>
              <p class="text-xs text-indigo-200 mt-1 leading-relaxed">Our admin team is online Mon–Fri, 8AM–5PM. Average response time is under 2 hours.</p>
              <a href="mailto:support@examsystem.edu"
                 class="inline-flex items-center gap-1.5 mt-3 text-xs font-black text-white bg-white/15 border border-white/20 px-3 py-1.5 rounded-lg hover:bg-white/25 transition-colors">
                <i data-lucide="mail" class="w-3 h-3"></i>
                Email Support
              </a>
            </div>
          </div>

        </div>
      </div>
    </div>
  </main>

  <!-- ═══════════════════════════════════════
       RESOLVED HISTORY DRAWER
  ════════════════════════════════════════ -->
  <div x-show="openDrawer" x-cloak class="fixed inset-0 z-50 overflow-hidden">
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-slate-950/50 backdrop-blur-sm"
         @click="openDrawer = false"></div>

    <!-- Drawer panel -->
    <div class="absolute inset-y-0 right-0 w-full max-w-md flex flex-col"
         :class="darkMode ? 'bg-slate-900 border-l border-slate-800' : 'bg-white border-l border-slate-100'"
         style="box-shadow: -8px 0 32px rgba(0,0,0,0.12)">

      <!-- Drawer Header -->
      <div class="px-6 py-5 border-b flex items-center justify-between brand-gradient text-white"
           style="border-bottom: none;">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 bg-white/15 border border-white/20 rounded-xl flex items-center justify-center">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-white"></i>
          </div>
          <div>
            <h2 class="text-sm font-black">Resolved History</h2>
            <p class="text-[11px] text-indigo-200 mt-0.5">Tickets closed by support team</p>
          </div>
        </div>
        <button @click="openDrawer = false"
                class="p-2 rounded-xl bg-white/10 hover:bg-white/20 transition-colors cursor-pointer">
          <i data-lucide="x" class="w-4 h-4 text-white"></i>
        </button>
      </div>

      <!-- Drawer Body -->
      <div class="flex-1 overflow-y-auto p-5 space-y-3" id="resolved-drawer-body">
        <!-- Syncing placeholder (replaced by JS) -->
        <div class="py-16 flex flex-col items-center justify-center text-center" id="drawer-loading">
          <div class="w-10 h-10 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
            <i data-lucide="loader-2" class="w-5 h-5 text-slate-400 animate-spin"></i>
          </div>
          <p class="text-xs text-slate-400 font-medium">Loading resolved tickets…</p>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════
       TOAST CONTAINER
  ════════════════════════════════════════ -->
  <div id="toast-container" class="fixed bottom-6 right-6 z-50 space-y-2 pointer-events-none"></div>

  <!-- ═══════════════════════════════════════
       ALPINE.JS + VANILLA JS
  ════════════════════════════════════════ -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('supportApp', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',
        openDrawer: false,
        liveTime: '',
        liveDate: '',
        descCount: 0,
        selectedCategory: 'technical',

        categories: [
          { value: 'technical', label: 'Technical', icon: 'monitor' },
          { value: 'exam', label: 'Exam Issue', icon: 'book-open' },
          { value: 'account', label: 'Account', icon: 'user' },
          { value: 'other', label: 'Other', icon: 'help-circle' },
        ],

        faqs: [
          {
            q: "My exam won't load — what do I do?",
            a: "Try refreshing the page and clearing browser cache. Make sure you're using Chrome or Firefox. If the issue persists, submit a support ticket immediately.",
            open: false
          },
          {
            q: "My webcam isn't detected by the proctor",
            a: "Check your browser permissions — go to Settings → Privacy → Camera and allow access. Restart the browser and try again.",
            open: false
          },
          {
            q: "I accidentally closed my exam tab",
            a: "Navigate back to the exam URL. Your progress is auto-saved every 30 seconds. If you're locked out, contact support immediately with your exam ID.",
            open: false
          },
        ],

        updateClock() {
          const now = new Date();
          this.liveTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          this.liveDate = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        },

        init() {
          this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
          this.$watch('openDrawer', val => { if (val) runResolutionScan(); });
          this.updateClock();
          setInterval(() => this.updateClock(), 1000);
          lucide.createIcons();
        }
      }));
    });

    // ── File Upload Logic ────────────────────────────────
    const fileInput  = document.getElementById('file-input');
    const dropZone   = document.getElementById('drop-zone');
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
        lucide.createIcons();
      }
    });

    ['dragenter', 'dragover'].forEach(evt => {
      dropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropZone.classList.add('drop-zone-hover');
      });
    });

    ['dragleave', 'drop'].forEach(evt => {
      dropZone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropZone.classList.remove('drop-zone-hover');
      });
    });

    // ── Toast Helper ─────────────────────────────────────
    function showToast(message, type = 'success') {
      const colors = type === 'success'
        ? 'bg-emerald-600 text-white'
        : 'bg-red-600 text-white';
      const icon = type === 'success' ? 'check-circle-2' : 'alert-circle';

      const toast = document.createElement('div');
      toast.className = `toast pointer-events-auto flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold ${colors}`;
      toast.innerHTML = `<i data-lucide="${icon}" class="w-4 h-4 flex-shrink-0"></i> ${message}`;
      document.getElementById('toast-container').appendChild(toast);
      lucide.createIcons();

      setTimeout(() => toast.remove(), 4200);
    }

    // ── Bell Notification ─────────────────────────────────
    let cachedResolvedCount = null;

    function shakeBell() {
      const bellIcon = document.getElementById('ticket-bell-icon');
      if (bellIcon) {
        bellIcon.classList.add('bell-shake');
        setTimeout(() => bellIcon.classList.remove('bell-shake'), 600);
      }
    }

    function setBellCounter(count) {
      const counter = document.getElementById('ticket-bell-counter');
      if (!counter) return;
      if (count > 0) {
        counter.innerText = count;
        counter.classList.remove('hidden');
      } else {
        counter.classList.add('hidden');
      }
    }

    // ── Resolved Drawer Render ────────────────────────────
    function renderResolvedTicket(item) {
      return `
        <div class="rounded-2xl border overflow-hidden" style="${document.documentElement.classList.contains('dark') ? 'background:#1e293b; border-color:#334155;' : 'background:#f8fafc; border-color:#e2e8f0;'}">
          <div class="px-5 py-4 border-b" style="border-color:${document.documentElement.classList.contains('dark') ? '#334155' : '#f1f5f9'}">
            <div class="flex items-start justify-between gap-2">
              <div class="flex-1 min-w-0">
                <span class="text-[10px] font-mono font-bold text-slate-400 block">${item.ticket_no}</span>
                <h5 class="text-xs font-bold mt-0.5 truncate" style="color:${document.documentElement.classList.contains('dark') ? '#f1f5f9' : '#1e293b'}">${item.issue_category || item.subject}</h5>
              </div>
              <span class="flex items-center gap-1 text-[10px] font-black px-2 py-0.5 rounded-lg flex-shrink-0" style="background:#ecfdf5; color:#059669;">
                <span style="width:6px;height:6px;background:#10b981;border-radius:99px;display:inline-block;"></span>
                Resolved
              </span>
            </div>
          </div>
          <div class="px-5 py-4 space-y-3">
            <div class="text-[11px] leading-relaxed text-slate-400 bg-white dark:bg-slate-900 border border-slate-100 dark:border-slate-800 rounded-xl p-3">
              <span class="font-bold text-slate-500 dark:text-slate-400">Your report:</span> ${item.description}
            </div>
            ${item.admin_comment ? `
            <div class="text-[11px] leading-relaxed text-indigo-700 dark:text-indigo-300 bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20 rounded-xl p-3">
              <span class="font-bold flex items-center gap-1.5 mb-1 text-[10px] uppercase tracking-wider">🎧 Support Response:</span>
              ${item.admin_comment}
            </div>` : ''}
          </div>
        </div>
      `;
    }

    // ── Real-Time Polling ─────────────────────────────────
    function runResolutionScan() {
      fetch('/student/support/notifications')
        .then(res => res.json())
        .then(data => {
          const drawerBody = document.getElementById('resolved-drawer-body');
          const loading = document.getElementById('drawer-loading');
          if (loading) loading.remove();

          // Clear and re-render drawer
          drawerBody.innerHTML = '';
          if (!data.resolved_items || data.resolved_items.length === 0) {
            drawerBody.innerHTML = `
              <div style="text-align:center; padding: 4rem 1rem;">
                <div style="width:48px;height:48px;background:#f1f5f9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                  <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                </div>
                <p style="font-size:12px;font-weight:700;color:#475569;">No resolved tickets yet</p>
                <p style="font-size:11px;color:#94a3b8;margin-top:4px;">Resolved tickets will appear here once support closes them.</p>
              </div>`;
            cachedResolvedCount = 0;
            return;
          }

          data.resolved_items.forEach(item => {
            // Remove from active queue if present
            const activeCard = document.getElementById(`ticket-card-${item.ticket_id}`);
            if (activeCard) activeCard.remove();

            // Render in drawer
            drawerBody.innerHTML += renderResolvedTicket(item);
          });

          // Update queue count
          const remainingCards = document.querySelectorAll('#active-tickets-wrapper [id^="ticket-card-"]').length;
          const queueCount = document.getElementById('active-queue-count');
          if (queueCount) queueCount.innerText = remainingCards + ' ticket(s)';

          // Show empty state if no active tickets remain
          if (remainingCards === 0) {
            const wrapper = document.getElementById('active-tickets-wrapper');
            if (wrapper && !document.getElementById('empty-queue-placeholder')) {
              wrapper.innerHTML = `
                <div id="empty-queue-placeholder" style="padding:2.5rem 1rem;text-align:center;">
                  <div style="width:48px;height:48px;background:#f1f5f9;border-radius:16px;display:flex;align-items:center;justify-content:center;margin:0 auto 12px;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#94a3b8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                  </div>
                  <p style="font-size:12px;font-weight:700;color:#475569;">All clear!</p>
                  <p style="font-size:11px;color:#94a3b8;margin-top:4px;">No active tickets right now.</p>
                </div>`;
            }
          }

          // Bell badge for new resolutions
          const newCount = data.count;
          if (cachedResolvedCount !== null && newCount > cachedResolvedCount) {
            const diff = newCount - cachedResolvedCount;
            setBellCounter(diff);
            shakeBell();
            showToast(`🎉 ${diff} ticket(s) resolved by support!`, 'success');

            // Optional audio
            try {
              const audio = new Audio('https://assets.mixkit.co/active_storage/sfx/2869/2869-500.wav');
              audio.volume = 0.35;
              audio.play().catch(() => {});
            } catch(e) {}
          }

          cachedResolvedCount = newCount;
          lucide.createIcons();
        })
        .catch(err => console.warn('Support notification poll failed:', err));
    }

    // Start polling
    runResolutionScan();
    setInterval(runResolutionScan, 3000);

    // Clear bell counter when drawer opens
    document.addEventListener('alpine:initialized', () => {
      const app = document.querySelector('[x-data]').__x;
    });

    // Reset bell counter when drawer is opened
    document.addEventListener('click', (e) => {
      if (e.target.closest('#ticket-bell-btn') || e.target.closest('[\\@click*="openDrawer = true"]')) {
        setBellCounter(0);
      }
    });
  </script>
  @include('partials.notification-realtime')
</body>
</html>