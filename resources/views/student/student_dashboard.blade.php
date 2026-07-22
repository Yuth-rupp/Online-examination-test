<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Student Dashboard</title>
  <meta name="description" content="ExamSystem student portal — view your exams, track performance, and access assessment tools.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Anti-Flash Dark Mode Script -->
  <script>
    (function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
      }
    })();
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          fontFamily: { inter: ['Inter', 'sans-serif'] },
        }
      }
    }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }

    /* Custom scrollbar */
    ::-webkit-scrollbar { width: 5px; height: 5px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
    .dark ::-webkit-scrollbar-thumb { background: #334155; }

    /* Smooth transitions */
    .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }
    .card { transition: box-shadow 0.2s ease, transform 0.2s ease; }
    .card:hover { transform: translateY(-2px); }

    /* Animated gradient for brand accent */
    .brand-gradient { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); }

    /* Glow on metric cards */
    .metric-glow-blue { box-shadow: 0 0 0 1px #E0E7FF, 0 4px 24px 0 rgba(79,110,247,0.07); }
    .metric-glow-green { box-shadow: 0 0 0 1px #D1FAE5, 0 4px 24px 0 rgba(16,185,129,0.07); }
    .metric-glow-purple { box-shadow: 0 0 0 1px #EDE9FE, 0 4px 24px 0 rgba(124,58,237,0.07); }
    .dark .metric-glow-blue, .dark .metric-glow-green, .dark .metric-glow-purple { box-shadow: none; }

    /* Bar chart bar */
    .bar-fill { transition: height 0.7s cubic-bezier(0.4,0,0.2,1); }

    /* Sidebar active pill */
    .nav-active { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); color: white; box-shadow: 0 4px 14px rgba(79,110,247,0.35); }

    /* Status live pulse */
    .status-live { animation: pulse-dot 1.5s infinite; }
    @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    /* Token input placeholder */
    input.token-input::placeholder { color: rgba(255,255,255,0.45); }

    /* Modal entrance animation */
    .modal-box { animation: modalIn 0.22s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }
  </style>
  @include('partials.notification-styles')
</head>

<body class="min-h-screen flex antialiased transition-colors duration-300"
      :class="darkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'"
      x-data="dashboardApp">

  <!-- ═══════════════════════════════════════════════════
       SIDEBAR
  ════════════════════════════════════════════════════ -->
  <aside class="w-64 flex flex-col fixed h-full z-30 hidden md:flex border-r transition-colors duration-300"
         :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">

    <!-- Logo -->
    <div class="px-5 pt-6 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 brand-gradient rounded-2xl flex items-center justify-center shadow-lg shadow-indigo-200 dark:shadow-indigo-900/40 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div>
          <h1 class="font-black text-sm leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">ExamSystem</h1>
          <p class="text-[11px] font-medium text-slate-400">Student Portal</p>
        </div>
      </div>
    </div>

    <!-- Section Label -->
    <p class="px-5 pt-4 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Main Menu</p>

    <!-- Nav Links (resized to match teacher sidebar) -->
    <nav class="px-3 space-y-1.5 flex-1">
      <a href="{{ route('student.dashboard') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold {{ request()->routeIs('student.dashboard') ? 'nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i>
        Dashboard
      </a>
      <a href="{{ route('student.exams') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold {{ request()->routeIs('student.exams') ? 'nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="book-open" class="w-5 h-5 flex-shrink-0"></i>
        My Exams
      </a>
      <a href="{{ route('student.history') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold {{ request()->routeIs('student.history') ? 'nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="history" class="w-5 h-5 flex-shrink-0"></i>
        History
      </a>

      <p class="px-2 pt-5 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Resources</p>

      <a href="{{ route('student.support') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold {{ request()->routeIs('student.support') ? 'nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="headphones" class="w-5 h-5 flex-shrink-0"></i>
        Support
      </a>
      <a href="{{ route('student.settings') }}"
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold {{ request()->routeIs('student.settings') ? 'nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="settings-2" class="w-5 h-5 flex-shrink-0"></i>
        Settings
      </a>
    </nav>

    <!-- User Profile Footer -->
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
            {{ Auth::user()->institutional_id ?? 'STU-1122-3344' }}
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

  <!-- ═══════════════════════════════════════════════════
       MAIN CONTENT
  ════════════════════════════════════════════════════ -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">

    <!-- TOPBAR -->
    <header class="border-b px-6 py-3.5 flex items-center justify-between sticky top-0 z-20 transition-colors duration-300"
            :class="darkMode ? 'bg-slate-900/95 border-slate-800 backdrop-blur-xl' : 'bg-white/95 border-slate-100 backdrop-blur-xl'">

      <!-- Search -->
      <div class="relative w-full max-w-sm">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        <input type="text"
               x-model="searchQuery"
               placeholder="Search exams, courses…"
               class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm font-medium border-none focus:outline-none focus:ring-2 focus:ring-indigo-500/25 transition-all"
               :class="darkMode ? 'bg-slate-800 text-white placeholder-slate-500' : 'bg-slate-100 text-slate-800 placeholder-slate-400'">
      </div>

      <!-- Right Actions -->
      <div class="flex items-center gap-2 ml-4">
        <!-- Dark Mode Toggle -->
        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode);"
                class="p-2.5 rounded-xl transition-colors cursor-pointer"
                :class="darkMode ? 'bg-slate-800 text-amber-400 hover:bg-slate-700' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
          <i data-lucide="sun" class="w-4 h-4" x-show="darkMode"></i>
          <i data-lucide="moon" class="w-4 h-4" x-show="!darkMode"></i>
        </button>

        @include('partials.notification-bell')

        <!-- Divider -->
        <div class="w-px h-6 mx-1" :class="darkMode ? 'bg-slate-700' : 'bg-slate-200'"></div>

        <!-- Avatar + Name -->
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
    <div class="p-6 lg:p-8 space-y-8 flex-1 max-w-[1440px] w-full mx-auto">

      <!-- ══════════════════════════════════
           WELCOME BANNER
      ═══════════════════════════════════ -->
      <div class="rounded-3xl p-6 md:p-8 relative overflow-hidden brand-gradient text-white shadow-xl shadow-indigo-200 dark:shadow-indigo-900/30">
        <div class="absolute -right-8 -top-8 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute -right-2 top-10 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>
        <div class="absolute right-32 -bottom-6 w-20 h-20 bg-white/5 rounded-full pointer-events-none"></div>

        <div class="relative flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
          <div>
            <p class="text-indigo-200 text-sm font-semibold mb-1">👋 Welcome back,</p>
            <h2 class="text-2xl md:text-3xl font-black">{{ Auth::user()->full_name ?? 'You Phatyuth' }}!</h2>
            <p class="text-indigo-200 text-sm mt-1.5 font-medium">Here's what's happening with your exams today.</p>
          </div>
          <div class="flex-shrink-0 flex items-center gap-3">
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl px-4 py-3 text-center border border-white/20">
              <p class="text-2xl font-black" x-text="metrics.totalExams"></p>
              <p class="text-[11px] text-indigo-200 font-semibold mt-0.5">Assigned</p>
            </div>
            <div class="bg-white/15 backdrop-blur-sm rounded-2xl px-4 py-3 text-center border border-white/20">
              <p class="text-2xl font-black" x-text="metrics.averageScore + '%'"></p>
              <p class="text-[11px] text-indigo-200 font-semibold mt-0.5">Avg Score</p>
            </div>
          </div>
        </div>
      </div>

      <!-- ══════════════════════════════════
           METRIC CARDS
      ═══════════════════════════════════ -->
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-5">

        <div class="card rounded-2xl p-5 border metric-glow-blue"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-transparent'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-indigo-50 dark:bg-indigo-500/10 rounded-xl flex items-center justify-center">
              <i data-lucide="clipboard-list" class="w-5 h-5 text-indigo-500"></i>
            </div>
            <span class="text-xs font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-2.5 py-1 rounded-lg">All Time</span>
          </div>
          <p class="text-3xl font-black" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="metrics.totalExams"></p>
          <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Total Exams Assigned</p>
        </div>

        <div class="card rounded-2xl p-5 border metric-glow-green"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-transparent'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center">
              <i data-lucide="check-circle-2" class="w-5 h-5 text-emerald-500"></i>
            </div>
            <span class="text-xs font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-2.5 py-1 rounded-lg">Done</span>
          </div>
          <p class="text-3xl font-black text-emerald-500" x-text="metrics.completedExams"></p>
          <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Exams Completed</p>
        </div>

        <div class="card rounded-2xl p-5 border metric-glow-purple"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-transparent'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-11 h-11 bg-violet-50 dark:bg-violet-500/10 rounded-xl flex items-center justify-center">
              <i data-lucide="trending-up" class="w-5 h-5 text-violet-500"></i>
            </div>
            <span class="text-xs font-bold text-violet-500 bg-violet-50 dark:bg-violet-500/10 px-2.5 py-1 rounded-lg">Score</span>
          </div>
          <p class="text-3xl font-black text-violet-500" x-text="metrics.averageScore + '%'"></p>
          <p class="text-xs font-semibold text-slate-400 mt-1 uppercase tracking-wider">Average Score</p>
        </div>

      </section>

      <!-- ══════════════════════════════════
           ASSESSMENT FOLDERS
      ═══════════════════════════════════ -->
      <section class="space-y-5">
        <div class="flex items-center justify-between">
          <div>
            <h2 class="text-lg font-black" :class="darkMode ? 'text-white' : 'text-slate-900'">Assessment Folders</h2>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Your assigned exams, sorted by status</p>
          </div>
          <div class="flex items-center gap-1 p-1 rounded-xl border"
               :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-100 border-transparent'">
            <button @click="activeFolderTab = 'upcoming'"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all"
                    :class="activeFolderTab === 'upcoming' ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
              📅 Upcoming
            </button>
            <button @click="activeFolderTab = 'ongoing'"
                    class="px-4 py-2 text-xs font-bold rounded-lg transition-all"
                    :class="activeFolderTab === 'ongoing' ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
              🟢 Live Now
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

          <!-- EXAM CARD LOOP -->
          <template x-for="exam in filteredExams" :key="exam.id">
            <div class="card border rounded-2xl overflow-hidden flex flex-col relative"
                 :class="exam.status === 'ongoing'
                   ? 'border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-900'
                   : (darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100')">
              <!-- Live indicator strip -->
              <div x-show="exam.status === 'ongoing'" class="h-1 w-full bg-gradient-to-r from-emerald-400 to-teal-400"></div>

              <div class="p-5 flex flex-col flex-1 gap-4">
                <div class="flex items-start justify-between gap-2">
                  <span class="px-2.5 py-1 text-[11px] font-black tracking-wider bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 rounded-lg" x-text="exam.code"></span>
                  <span class="flex items-center gap-1.5 text-[11px] font-bold"
                        :class="exam.status === 'ongoing' ? 'text-emerald-500' : 'text-amber-500'">
                    <span class="w-1.5 h-1.5 rounded-full inline-block"
                          :class="exam.status === 'ongoing' ? 'bg-emerald-400 status-live' : 'bg-amber-400'"></span>
                    <span x-text="exam.status === 'ongoing' ? 'Live' : 'Upcoming'"></span>
                  </span>
                </div>

                <h3 class="text-sm font-bold leading-snug flex-1" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="exam.title"></h3>

                <div class="space-y-1.5">
                  <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                    <i data-lucide="calendar" class="w-3.5 h-3.5 flex-shrink-0"></i>
                    <span x-text="exam.date"></span>
                  </div>
                  <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
                    <i data-lucide="clock" class="w-3.5 h-3.5 flex-shrink-0"></i>
                    <span x-text="exam.time + ' · ' + exam.duration"></span>
                  </div>
                </div>

                <div class="flex items-center gap-2 pt-2 border-t" :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
                  <template x-if="exam.status === 'ongoing'">
                    <a :href="'/student/exams/' + exam.id + '/enter'"
                       class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-black rounded-xl transition-all shadow-sm shadow-emerald-200 dark:shadow-emerald-900/30">
                      <i data-lucide="play-circle" class="w-3.5 h-3.5"></i>
                      Enter Exam
                    </a>
                  </template>
                  <button @click="modalTitle = exam.title; modalBody = '<div class=\'space-y-2\'><div class=\'flex justify-between py-2 border-b border-slate-100 dark:border-slate-700\'><span class=\'text-slate-400 text-xs\'>Course</span><span class=\'text-xs font-bold\'>' + exam.code + '</span></div><div class=\'flex justify-between py-2 border-b border-slate-100 dark:border-slate-700\'><span class=\'text-slate-400 text-xs\'>Department</span><span class=\'text-xs font-bold\'>' + exam.dept + '</span></div><div class=\'flex justify-between py-2\'><span class=\'text-slate-400 text-xs\'>Duration</span><span class=\'text-xs font-bold\'>' + exam.duration + '</span></div></div>'; modalOpen = true"
                          class="flex-1 flex items-center justify-center gap-2 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer"
                          :class="darkMode ? 'bg-slate-800 text-slate-300 hover:bg-slate-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                    Details
                  </button>
                </div>
              </div>
            </div>
          </template>

          <!-- EMPTY STATE -->
          <template x-if="filteredExams.length === 0">
            <div class="border-2 border-dashed rounded-2xl p-8 flex flex-col items-center justify-center text-center"
                 :class="darkMode ? 'border-slate-800 bg-slate-900/50' : 'border-slate-200 bg-white'">
              <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                <i data-lucide="calendar-x" class="w-6 h-6 text-slate-400"></i>
              </div>
              <h4 class="text-sm font-bold" :class="darkMode ? 'text-slate-300' : 'text-slate-700'">Nothing here yet</h4>
              <p class="text-xs text-slate-400 mt-1.5 max-w-[180px] leading-relaxed">No exams match this filter. Try switching tabs or check back later.</p>
            </div>
          </template>

          <!-- TOKEN ENTRY CARD -->
          <div class="brand-gradient rounded-2xl p-5 flex flex-col justify-between text-white relative overflow-hidden shadow-lg shadow-indigo-200 dark:shadow-indigo-900/30">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full pointer-events-none"></div>
            <div class="absolute right-10 -top-4 w-16 h-16 bg-white/5 rounded-full pointer-events-none"></div>
            <div>
              <div class="w-10 h-10 bg-white/15 rounded-xl flex items-center justify-center mb-4 border border-white/20">
                <i data-lucide="key-round" class="w-5 h-5 text-white"></i>
              </div>
              <span class="text-[10px] font-black tracking-widest text-indigo-200 uppercase">Secure Entry</span>
              <h3 class="text-base font-black mt-1 mb-2">Class Token Access</h3>
              <p class="text-xs text-indigo-200 leading-relaxed">Enter the token code provided by your lecturer to access a specific exam session.</p>
            </div>
            <form action="{{ route('student.verifyCode') }}" method="POST" class="mt-5 relative z-10">
              @csrf
              <div class="flex gap-2">
                <input type="text"
                       name="access_code"
                       placeholder="e.g., DBMS-4821"
                       required
                       class="token-input flex-1 min-w-0 bg-white/10 border border-white/20 px-3 py-2.5 rounded-xl text-xs font-mono text-white focus:outline-none focus:ring-2 focus:ring-white/30 uppercase tracking-widest">
                <button type="submit"
                        class="px-4 py-2.5 bg-white text-indigo-600 hover:bg-indigo-50 text-xs font-black rounded-xl transition-all cursor-pointer shadow-sm flex-shrink-0">
                  Verify
                </button>
              </div>
            </form>
          </div>

        </div>
      </section>

      <!-- ══════════════════════════════════
           BOTTOM ROW: Chart + Quick Actions
      ═══════════════════════════════════ -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        <!-- PERFORMANCE CHART -->
        <div class="card border rounded-2xl p-6 lg:col-span-2"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-6">
            <div>
              <h3 class="text-base font-black" :class="darkMode ? 'text-white' : 'text-slate-900'">Performance Insights</h3>
              <p class="text-xs text-slate-400 font-medium mt-0.5">Your recent exam scores</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1.5 rounded-lg">
              <i data-lucide="bar-chart-3" class="w-3.5 h-3.5"></i>
              Last 5 Exams
            </div>
          </div>

          <!-- Chart with data -->
          <div x-show="weeklyData.length > 0" class="flex gap-4">
            <!-- Y-axis labels -->
            <div class="flex flex-col justify-between text-[10px] text-slate-400 text-right pb-7 flex-shrink-0 select-none">
              <span>100%</span>
              <span>75%</span>
              <span>50%</span>
              <span>25%</span>
              <span>0%</span>
            </div>
            <!-- Bars -->
            <div class="flex items-end gap-3 flex-1 h-44">
              <template x-for="(data, idx) in weeklyData" :key="idx">
                <div class="relative flex-1 flex flex-col items-center gap-2 group cursor-pointer"
                     @click="modalTitle = data.title + ' · Score'; modalBody = '<div class=\'text-center py-4\'><p class=\'text-5xl font-black text-indigo-500\'>' + data.score + '%</p><p class=\'text-xs text-slate-400 mt-2\'>' + data.date + '</p></div>'; modalOpen = true">
                  <!-- Tooltip -->
                  <span class="absolute -top-7 left-1/2 -translate-x-1/2 bg-slate-900 dark:bg-slate-700 text-white text-[10px] font-bold px-2 py-0.5 rounded-md opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none whitespace-nowrap z-10" x-text="data.score + '%'"></span>
                  <!-- Bar container -->
                  <div class="w-full rounded-xl overflow-hidden h-32 relative"
                       :class="darkMode ? 'bg-slate-800' : 'bg-slate-100'">
                    <div class="absolute bottom-0 left-0 right-0 rounded-t-xl bar-fill group-hover:opacity-80 transition-opacity"
                         style="background: linear-gradient(to top, #4F6EF7, #7C3AED)"
                         :style="'height: ' + data.score + '%'"></div>
                  </div>
                  <!-- Label -->
                  <span class="text-[10px] font-bold text-slate-400 truncate w-full text-center" x-text="data.title"></span>
                </div>
              </template>
            </div>
          </div>

          <!-- Empty chart state -->
          <div x-show="weeklyData.length === 0" class="h-44 flex items-center justify-center">
            <div class="text-center">
              <i data-lucide="bar-chart-3" class="w-10 h-10 text-slate-200 dark:text-slate-700 mx-auto mb-2"></i>
              <p class="text-xs text-slate-400 font-medium">No submission data yet.<br>Complete an exam to see your performance here.</p>
            </div>
          </div>
        </div>

        <!-- QUICK ACTIONS PANEL -->
        <div class="card border rounded-2xl p-6 flex flex-col"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="mb-5">
            <h3 class="text-base font-black" :class="darkMode ? 'text-white' : 'text-slate-900'">Quick Actions</h3>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Useful tools and documents</p>
          </div>
          <div class="space-y-2 flex-1">

            <!-- Hall Entry Voucher -->
            <a href="{{ route('student.printTicket') }}" target="_blank"
               class="group flex items-center gap-3 p-3.5 rounded-xl border transition-all"
               :class="darkMode ? 'border-slate-800 hover:border-indigo-500/40 hover:bg-indigo-500/5' : 'border-slate-100 hover:border-indigo-200 hover:bg-indigo-50/50'">
              <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="printer" class="w-4 h-4 text-indigo-500"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-bold" :class="darkMode ? 'text-white' : 'text-slate-800'">Hall Entry Voucher</p>
                <p class="text-[11px] text-slate-400">Print your exam ticket</p>
              </div>
              <i data-lucide="arrow-up-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-indigo-500 transition-colors flex-shrink-0"></i>
            </a>

            <!-- Exam Guidelines -->
            <button @click="modalTitle = '📋 Examination Guidelines'; modalBody = '<div class=\'space-y-3 text-xs leading-relaxed\'><p class=\'text-slate-500\'>Please read carefully before starting your exam.</p><ul class=\'space-y-2 list-disc list-inside text-slate-600 dark:text-slate-400\'><li>Keep browser instances isolated at all times</li><li>Do not open additional tabs or windows</li><li>Ensure stable internet connection before starting</li><li>Report technical issues to support immediately</li></ul></div>'; modalOpen = true"
                    class="group w-full flex items-center gap-3 p-3.5 rounded-xl border transition-all cursor-pointer"
                    :class="darkMode ? 'border-slate-800 hover:border-amber-500/40 hover:bg-amber-500/5' : 'border-slate-100 hover:border-amber-200 hover:bg-amber-50/50'">
              <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="shield-check" class="w-4 h-4 text-amber-500"></i>
              </div>
              <div class="flex-1 min-w-0 text-left">
                <p class="text-xs font-bold" :class="darkMode ? 'text-white' : 'text-slate-800'">Exam Guidelines</p>
                <p class="text-[11px] text-slate-400">Rules and instructions</p>
              </div>
              <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-amber-500 transition-colors flex-shrink-0"></i>
            </button>

            <!-- Proctoring FAQ -->
            <button @click="modalTitle = '💬 Proctoring FAQ'; modalBody = '<div class=\'space-y-4 text-xs\'><div><p class=\'font-bold text-slate-700 dark:text-slate-200 mb-1\'>Q: What if my internet drops?</p><p class=\'text-slate-500 leading-relaxed\'>Offline mode saves your progress automatically. Reconnect and resume where you left off.</p></div><div><p class=\'font-bold text-slate-700 dark:text-slate-200 mb-1\'>Q: Can I go back to previous questions?</p><p class=\'text-slate-500 leading-relaxed\'>Yes, unless the exam is configured as one-way by your lecturer.</p></div></div>'; modalOpen = true"
                    class="group w-full flex items-center gap-3 p-3.5 rounded-xl border transition-all cursor-pointer"
                    :class="darkMode ? 'border-slate-800 hover:border-teal-500/40 hover:bg-teal-500/5' : 'border-slate-100 hover:border-teal-200 hover:bg-teal-50/50'">
              <div class="w-9 h-9 rounded-xl bg-teal-50 dark:bg-teal-500/10 flex items-center justify-center flex-shrink-0 group-hover:scale-105 transition-transform">
                <i data-lucide="help-circle" class="w-4 h-4 text-teal-500"></i>
              </div>
              <div class="flex-1 min-w-0 text-left">
                <p class="text-xs font-bold" :class="darkMode ? 'text-white' : 'text-slate-800'">Proctoring FAQ</p>
                <p class="text-[11px] text-slate-400">Common questions answered</p>
              </div>
              <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-slate-400 group-hover:text-teal-500 transition-colors flex-shrink-0"></i>
            </button>

          </div>

          <!-- Support CTA -->
          <div class="mt-4 p-3.5 rounded-xl border"
               :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
            <p class="text-xs font-bold" :class="darkMode ? 'text-white' : 'text-slate-800'">Need help? 🙋</p>
            <p class="text-[11px] text-slate-400 mt-0.5 mb-2.5">Our support team is available 24/7.</p>
            <a href="{{ route('student.support') }}"
               class="inline-flex items-center gap-1.5 text-[11px] font-black text-indigo-500 hover:text-indigo-600 transition-colors">
              Contact Support <i data-lucide="arrow-right" class="w-3 h-3"></i>
            </a>
          </div>
        </div>

      </section>
    </div>
  </main>

  <!-- ═══════════════════════════════════════════════════
       MODAL
  ════════════════════════════════════════════════════ -->
  <div x-show="modalOpen" x-cloak
       class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
       @click.self="modalOpen = false">
    <div class="modal-box rounded-3xl shadow-2xl border max-w-sm w-full overflow-hidden"
         :class="darkMode ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-100'">

      <div class="px-6 py-4 border-b flex items-center justify-between"
           :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
        <h3 class="text-sm font-black" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="modalTitle"></h3>
        <button @click="modalOpen = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div class="px-6 py-5">
        <div class="text-xs leading-relaxed" :class="darkMode ? 'text-slate-300' : 'text-slate-600'" x-html="modalBody"></div>
      </div>

      <div class="px-6 py-4 border-t flex justify-end"
           :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
        <button @click="modalOpen = false"
                class="px-5 py-2 brand-gradient text-white text-xs font-bold rounded-xl shadow-sm hover:opacity-90 transition-opacity cursor-pointer">
          Got it
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════════════════
       ALPINE.JS APP DATA (All logic preserved)
  ════════════════════════════════════════════════════ -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('dashboardApp', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',
        activeFolderTab: 'upcoming',
        searchQuery: '',
        modalOpen: false,
        modalTitle: '',
        modalBody: '',

        metrics: {
          totalExams: {{ (int)($totalExams ?? 0) }},
          completedExams: {{ (int)($completedExams ?? 0) }},
          averageScore: {{ round($averageScore ?? 0) }}
        },

        weeklyData: [
          @if(isset($submissions) && count($submissions) > 0)
            @foreach($submissions->take(5) as $sub)
              {
                title: '{{ addslashes($sub->exam->title ?? "Exam") }}',
                score: {{ (int)($sub->percentage ?? 0) }},
                date: '{{ \Carbon\Carbon::parse($sub->created_at)->format("M d, Y") }}'
              },
            @endforeach
          @endif
        ],

        exams: [
          @if(isset($upcomingExams) && count($upcomingExams) > 0)
            @foreach($upcomingExams as $exam)
            @php
              $hasSubmitted = isset($submissions) ? $submissions->contains('exam_id', $exam->exam_id) : false;
            @endphp
            {
              id: '{{ $exam->exam_id }}',
              title: '{{ addslashes($exam->title) }}',
              courseId: '{{ $exam->course->id ?? $exam->course_id }}',
              code: '{{ addslashes($exam->course->code ?? "DAT-464") }}',
              dept: '{{ addslashes($exam->course->name ?? "Database Dept") }}',
              date: '{{ \Carbon\Carbon::parse($exam->start_time)->format("M d, Y") }}',
              time: '{{ \Carbon\Carbon::parse($exam->start_time)->format("h:i A") }}',
              duration: '{{ $exam->duration ?? 100 }} mins',
              startTimeRaw: '{{ $exam->start_time }}',
              endTimeRaw: '{{ $exam->end_time ?? \Carbon\Carbon::parse($exam->start_time)->addMinutes($exam->duration ?? 100)->toDateTimeString() }}',
              isSubmitted: {{ $hasSubmitted ? 'true' : 'false' }},
              status: '{{ $hasSubmitted ? "completed" : (\Carbon\Carbon::parse($exam->start_time)->isPast() && \Carbon\Carbon::parse($exam->end_time)->isFuture() ? "ongoing" : "upcoming") }}'
            },
            @endforeach
          @endif
        ],

        get filteredExams() {
          return this.exams.filter(e => {
            const matchStatus = e.status === this.activeFolderTab;
            const q = this.searchQuery.toLowerCase().trim();
            const matchSearch = q === '' ||
              e.title.toLowerCase().includes(q) ||
              e.code.toLowerCase().includes(q) ||
              e.dept.toLowerCase().includes(q) ||
              String(e.courseId).toLowerCase().includes(q);
            return matchStatus && matchSearch;
          });
        },

        // ── Live sync: pulls fresh metrics, exams, and submissions from the
        //    server and merges them into the reactive state. This is what
        //    makes a newly published exam show up under "Upcoming", and a
        //    freshly graded score flip an exam to "Completed", without a
        //    manual page refresh — same polling pattern used on the Exams
        //    and History pages.
        async syncDashboardFromServer() {
          try {
            const res = await fetch('{{ route('student.dashboard') }}', {
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();

            this.metrics.totalExams = data.totalExams ?? this.metrics.totalExams;
            this.metrics.completedExams = data.completedExams ?? this.metrics.completedExams;
            this.metrics.averageScore = Math.round(data.averageScore ?? this.metrics.averageScore);

            const submissions = data.submissions || [];
            const serverExams = data.upcomingExams || [];

            serverExams.forEach(se => {
              const submission = submissions.find(s => String(s.exam_id) === String(se.exam_id));
              const existing = this.exams.find(e => String(e.id) === String(se.exam_id));

              if (existing) {
                if (submission) {
                  existing.isSubmitted = true;
                  existing.status = 'completed';
                }
              } else {
                // Brand new exam the teacher just published — add it.
                const start = new Date(se.start_time);
                const end = new Date(se.end_time || start);
                const now = new Date();
                let status = 'upcoming';
                if (submission) status = 'completed';
                else if (now >= start && now <= end) status = 'ongoing';
                else if (now > end) status = 'completed';

                this.exams.push({
                  id: String(se.exam_id),
                  title: se.title,
                  courseId: String((se.course && se.course.id) || se.course_id || ''),
                  code: (se.course && se.course.code) || 'DAT-464',
                  dept: (se.course && se.course.name) || 'Database Dept',
                  date: start.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                  time: start.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                  duration: (se.duration || 100) + ' mins',
                  startTimeRaw: se.start_time,
                  endTimeRaw: se.end_time,
                  isSubmitted: !!submission,
                  status: status
                });
              }
            });

            // Refresh the trend chart with the latest graded submissions.
            this.weeklyData = submissions.slice(0, 5).map(s => ({
              title: (s.exam && s.exam.title) || 'Exam',
              score: Math.round(s.percentage || 0),
              date: new Date(s.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
            }));

            this.$nextTick(() => lucide.createIcons());
          } catch (e) {
            console.warn('Failed to sync dashboard from server', e);
          }
        },

        init() {
          setInterval(() => {
            const now = new Date();
            this.exams.forEach(exam => {
              if (exam.isSubmitted) { exam.status = 'completed'; return; }
              const start = new Date(exam.startTimeRaw);
              const end = new Date(exam.endTimeRaw);
              if (now >= start && now <= end) exam.status = 'ongoing';
              else if (now > end) exam.status = 'completed';
              else exam.status = 'upcoming';
            });
          }, 1000);

          // Poll the server every 15s for newly published exams and
          // freshly graded scores.
          setInterval(() => this.syncDashboardFromServer(), 15000);

          lucide.createIcons();
        }
      }));
    });
  </script>
  @include('partials.notification-realtime')
</body>
</html>
