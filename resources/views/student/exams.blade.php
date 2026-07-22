<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - My Exams</title>
  <meta name="description" content="View your upcoming, live, and completed exams on ExamSystem Student Portal.">
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
    .card { transition: box-shadow 0.2s ease, transform 0.2s ease; }
    .card:hover { transform: translateY(-2px); }

    /* Status live pulse */
    .status-live { animation: pulse-dot 1.5s infinite; }
    @keyframes pulse-dot { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

    /* Modal entrance */
    .modal-box { animation: modalIn 0.22s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }

    /* Token input */
    input.token-input::placeholder { color: rgba(255,255,255,0.45); }

    /* Tab active transition */
    .tab-pill { transition: all 0.2s cubic-bezier(0.4,0,0.2,1); }
  </style>
  @include('partials.notification-styles')
</head>

<body class="min-h-screen flex antialiased transition-colors duration-300"
      :class="darkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'"
      x-data="examsApp">

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
          <h1 class="font-black text-sm leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">ExamSystem</h1>
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
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold nav-active">
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
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
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

  <!-- ═══════════════════════════════════════
       MAIN CONTENT
  ════════════════════════════════════════ -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">

    <!-- TOPBAR -->
    <header class="border-b px-6 py-3.5 flex items-center justify-between sticky top-0 z-20 transition-colors duration-300"
            :class="darkMode ? 'bg-slate-900/95 border-slate-800 backdrop-blur-xl' : 'bg-white/95 border-slate-100 backdrop-blur-xl'">

      <div class="relative w-full max-w-sm">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        <input type="text"
               x-model="searchQuery"
               placeholder="Search by exam title, course name, or course ID…"
               class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm font-medium border-none focus:outline-none focus:ring-2 focus:ring-indigo-500/25 transition-all"
               :class="darkMode ? 'bg-slate-800 text-white placeholder-slate-500' : 'bg-slate-100 text-slate-800 placeholder-slate-400'">
      </div>

      <div class="flex items-center gap-2 ml-4">
        <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode);"
                class="p-2.5 rounded-xl transition-colors cursor-pointer"
                :class="darkMode ? 'bg-slate-800 text-amber-400 hover:bg-slate-700' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
          <i data-lucide="sun" class="w-4 h-4" x-show="darkMode"></i>
          <i data-lucide="moon" class="w-4 h-4" x-show="!darkMode"></i>
        </button>
        @include('partials.notification-bell')
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
    <div class="p-6 lg:p-8 space-y-7 flex-1 max-w-[1440px] w-full mx-auto">

      <!-- PAGE HEADER -->
      <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-md shadow-indigo-200 dark:shadow-indigo-900/30">
              <i data-lucide="book-open" class="w-4 h-4 text-white"></i>
            </div>
            <h2 class="text-2xl font-black tracking-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">My Exams</h2>
          </div>
          <p class="text-sm text-slate-400 font-medium ml-12">All your scheduled assessments — track status in real time.</p>
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

      <!-- STAT SUMMARY BAR -->
      <div class="grid grid-cols-3 gap-4">
        <div class="rounded-2xl p-4 border flex items-center gap-3"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center flex-shrink-0">
            <i data-lucide="calendar-clock" class="w-4 h-4 text-amber-500"></i>
          </div>
          <div>
            <p class="text-xl font-black" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="exams.filter(e => e.status === 'upcoming').length"></p>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Upcoming</p>
          </div>
        </div>
        <div class="rounded-2xl p-4 border flex items-center gap-3"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center flex-shrink-0">
            <i data-lucide="radio" class="w-4 h-4 text-emerald-500"></i>
          </div>
          <div>
            <p class="text-xl font-black text-emerald-500" x-text="exams.filter(e => e.status === 'ongoing').length"></p>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Live Now</p>
          </div>
        </div>
        <div class="rounded-2xl p-4 border flex items-center gap-3"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="w-9 h-9 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center flex-shrink-0">
            <i data-lucide="check-circle-2" class="w-4 h-4 text-indigo-500"></i>
          </div>
          <div>
            <p class="text-xl font-black text-indigo-500" x-text="exams.filter(e => e.status === 'completed').length"></p>
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide">Completed</p>
          </div>
        </div>
      </div>

      <!-- TAB SWITCHER -->
      <div class="flex items-center gap-1.5 p-1 rounded-2xl border w-fit"
           :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-slate-100 border-transparent'">
        <!-- Upcoming Tab -->
        <button @click="activeTab = 'upcoming'"
                class="tab-pill flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold"
                :class="activeTab === 'upcoming'
                  ? 'bg-white dark:bg-slate-700 text-amber-600 dark:text-amber-400 shadow-sm'
                  : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
          <span class="w-1.5 h-1.5 rounded-full bg-amber-400 flex-shrink-0"></span>
          📅 Upcoming
          <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px] font-black"
                :class="activeTab === 'upcoming' ? 'bg-amber-100 dark:bg-amber-500/20 text-amber-600' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'"
                x-text="exams.filter(e => e.status === 'upcoming').length"></span>
        </button>

        <!-- Ongoing Tab -->
        <button @click="activeTab = 'ongoing'"
                class="tab-pill flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold"
                :class="activeTab === 'ongoing'
                  ? 'bg-white dark:bg-slate-700 text-emerald-600 dark:text-emerald-400 shadow-sm'
                  : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0 status-live"></span>
          🟢 Live Now
          <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px] font-black"
                :class="activeTab === 'ongoing' ? 'bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'"
                x-text="exams.filter(e => e.status === 'ongoing').length"></span>
        </button>

        <!-- Completed Tab -->
        <button @click="activeTab = 'completed'"
                class="tab-pill flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs font-bold"
                :class="activeTab === 'completed'
                  ? 'bg-white dark:bg-slate-700 text-indigo-600 dark:text-indigo-400 shadow-sm'
                  : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'">
          <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 flex-shrink-0"></span>
          ✓ Completed
          <span class="ml-1 px-1.5 py-0.5 rounded-md text-[10px] font-black"
                :class="activeTab === 'completed' ? 'bg-indigo-100 dark:bg-indigo-500/20 text-indigo-600' : 'bg-slate-200 dark:bg-slate-700 text-slate-500'"
                x-text="exams.filter(e => e.status === 'completed').length"></span>
        </button>
      </div>

      <!-- EXAM CARDS GRID -->
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">

        <template x-for="exam in filteredExams" :key="exam.id">
          <div class="card border rounded-2xl overflow-hidden flex flex-col relative group"
               :class="{
                 'border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-900': exam.status === 'ongoing',
                 'border-indigo-100 dark:border-slate-800 bg-white dark:bg-slate-900': exam.status === 'completed',
                 'bg-white dark:bg-slate-900 border-slate-100 dark:border-slate-800': exam.status === 'upcoming'
               }">

            <!-- Color accent top strip -->
            <div class="h-1 w-full flex-shrink-0"
                 :class="{
                   'bg-gradient-to-r from-amber-400 to-orange-400': exam.status === 'upcoming',
                   'bg-gradient-to-r from-emerald-400 to-teal-400': exam.status === 'ongoing',
                   'bg-gradient-to-r from-indigo-400 to-violet-400': exam.status === 'completed'
                 }"></div>

            <div class="p-5 flex flex-col flex-1 gap-4">

              <!-- Header Row -->
              <div class="flex items-start justify-between gap-2">
                <span class="px-2.5 py-1 text-[11px] font-black tracking-wider rounded-lg"
                      :class="{
                        'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400': exam.status === 'upcoming',
                        'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': exam.status === 'ongoing',
                        'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400': exam.status === 'completed'
                      }"
                      x-text="exam.code"></span>

                <!-- Status Badge -->
                <span class="flex items-center gap-1.5 text-[11px] font-bold flex-shrink-0"
                      :class="{
                        'text-amber-500': exam.status === 'upcoming',
                        'text-emerald-500': exam.status === 'ongoing',
                        'text-indigo-500': exam.status === 'completed'
                      }">
                  <span class="w-1.5 h-1.5 rounded-full inline-block"
                        :class="{
                          'bg-amber-400': exam.status === 'upcoming',
                          'bg-emerald-400 status-live': exam.status === 'ongoing',
                          'bg-indigo-400': exam.status === 'completed'
                        }"></span>
                  <span x-text="exam.status === 'ongoing' ? 'Live' : (exam.status === 'completed' ? 'Done' : 'Upcoming')"></span>
                </span>
              </div>

              <!-- Title + Dept -->
              <div>
                <h3 class="text-base font-bold leading-snug" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="exam.title"></h3>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5" x-text="exam.dept"></p>
              </div>

              <!-- Info Rows -->
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

              <!-- Countdown / Progress indicator for upcoming -->
              <template x-if="exam.status === 'upcoming' && exam.countdown">
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     :class="darkMode ? 'bg-amber-500/10' : 'bg-amber-50'">
                  <i data-lucide="timer" class="w-3.5 h-3.5 text-amber-500 flex-shrink-0"></i>
                  <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400" x-text="'Starts in ' + exam.countdown"></span>
                </div>
              </template>

              <!-- Ongoing live progress bar -->
              <template x-if="exam.status === 'ongoing'">
                <div>
                  <div class="flex items-center justify-between mb-1">
                    <span class="text-[10px] font-bold text-emerald-500 flex items-center gap-1">
                      <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full status-live inline-block"></span>
                      Exam in progress
                    </span>
                    <span class="text-[10px] text-slate-400 font-medium" x-text="exam.progressPct + '%'"></span>
                  </div>
                  <div class="w-full h-1.5 rounded-full overflow-hidden"
                       :class="darkMode ? 'bg-slate-800' : 'bg-slate-100'">
                    <div class="h-full bg-gradient-to-r from-emerald-400 to-teal-400 rounded-full transition-all duration-1000"
                         :style="'width: ' + exam.progressPct + '%'"></div>
                  </div>
                </div>
              </template>

              <!-- Completed score badge -->
              <template x-if="exam.status === 'completed' && exam.score !== undefined">
                <div class="flex items-center gap-2 px-3 py-2 rounded-xl"
                     :class="darkMode ? 'bg-indigo-500/10' : 'bg-indigo-50'">
                  <i data-lucide="star" class="w-3.5 h-3.5 text-indigo-500 flex-shrink-0"></i>
                  <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400" x-text="'Score: ' + exam.score + '%'"></span>
                </div>
              </template>

              <!-- Action Row -->
              <div class="flex items-center gap-2 pt-2 border-t mt-auto"
                   :class="darkMode ? 'border-slate-800' : 'border-slate-100'">

                <!-- ONGOING: Enter Exam -->
                <template x-if="exam.status === 'ongoing'">
                  <a :href="'/student/exams/' + exam.id + '/enter'"
                     class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white text-xs font-black rounded-xl transition-all shadow-sm shadow-emerald-200 dark:shadow-emerald-900/20">
                    <i data-lucide="play-circle" class="w-3.5 h-3.5"></i>
                    Enter Exam Now
                  </a>
                </template>

                <!-- UPCOMING: View Details -->
                <template x-if="exam.status === 'upcoming'">
                  <button @click="showExamDetails(exam)"
                          class="flex-1 flex items-center justify-center gap-2 py-2.5 text-xs font-bold rounded-xl transition-all cursor-pointer"
                          :class="darkMode ? 'bg-slate-800 text-slate-300 hover:bg-slate-700' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'">
                    <i data-lucide="info" class="w-3.5 h-3.5"></i>
                    View Details
                  </button>
                </template>

                <!-- COMPLETED: Review Score + Remove -->
                <template x-if="exam.status === 'completed'">
                  <a href="{{ route('student.history') }}"
                     class="flex-1 flex items-center justify-center gap-2 py-2.5 bg-gradient-to-r from-indigo-500 to-violet-500 hover:from-indigo-600 hover:to-violet-600 text-white text-xs font-black rounded-xl transition-all shadow-sm shadow-indigo-200 dark:shadow-indigo-900/20">
                    <i data-lucide="bar-chart-2" class="w-3.5 h-3.5"></i>
                    Review Score
                  </a>
                </template>
                <template x-if="exam.status === 'completed'">
                  <button @click="deleteExam(exam)"
                          title="Remove from list"
                          class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-xl text-xs font-bold transition-all cursor-pointer"
                          :class="darkMode ? 'bg-slate-800 text-slate-400 hover:bg-red-500/10 hover:text-red-400' : 'bg-slate-100 text-slate-500 hover:bg-red-50 hover:text-red-500'">
                    <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                  </button>
                </template>
              </div>

            </div>
          </div>
        </template>

        <!-- EMPTY STATE per tab -->
        <template x-if="filteredExams.length === 0">
          <div class="col-span-full border-2 border-dashed rounded-2xl p-14 flex flex-col items-center justify-center text-center"
               :class="darkMode ? 'border-slate-800 bg-slate-900/40' : 'border-slate-200 bg-white'">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
              <template x-if="activeTab === 'upcoming'">
                <i data-lucide="calendar-x" class="w-7 h-7 text-slate-400"></i>
              </template>
              <template x-if="activeTab === 'ongoing'">
                <i data-lucide="radio" class="w-7 h-7 text-slate-400"></i>
              </template>
              <template x-if="activeTab === 'completed'">
                <i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i>
              </template>
            </div>
            <h4 class="text-sm font-bold mb-1" :class="darkMode ? 'text-slate-300' : 'text-slate-700'"
                x-text="activeTab === 'ongoing' ? 'No Live Exams Right Now' : (activeTab === 'upcoming' ? 'No Upcoming Exams' : 'No Completed Exams Yet')"></h4>
            <p class="text-xs text-slate-400 max-w-[220px] leading-relaxed"
               x-text="activeTab === 'ongoing' ? 'There are currently no active exam sessions. Check the Upcoming tab for your next scheduled exam.' : (activeTab === 'upcoming' ? 'You\'re all clear! No new exams have been assigned yet.' : 'You haven\'t completed any exams yet. Completed exams will appear here.')"></p>
          </div>
        </template>

      </div>
    </div>
  </main>

  <!-- ═══════════════════════════════════════
       EXAM DETAILS MODAL
  ════════════════════════════════════════ -->
  <div x-show="modalOpen" x-cloak
       class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
       @click.self="modalOpen = false">
    <div class="modal-box rounded-3xl shadow-2xl border max-w-sm w-full overflow-hidden"
         :class="darkMode ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-100'">

      <!-- Modal Header -->
      <div class="px-6 py-4 border-b flex items-center gap-3"
           :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
        <div class="w-8 h-8 brand-gradient rounded-xl flex items-center justify-center flex-shrink-0">
          <i data-lucide="book-open" class="w-3.5 h-3.5 text-white"></i>
        </div>
        <h3 class="text-sm font-black flex-1" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="modalTitle"></h3>
        <button @click="modalOpen = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <!-- Modal Body -->
      <div class="px-6 py-5 space-y-3" x-html="modalBody"></div>

      <!-- Modal Footer -->
      <div class="px-6 py-4 border-t flex justify-end gap-2"
           :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
        <button @click="modalOpen = false"
                class="px-5 py-2 brand-gradient text-white text-xs font-bold rounded-xl shadow-sm hover:opacity-90 transition-opacity cursor-pointer">
          Got it
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════
       ALPINE.JS LOGIC (all logic preserved)
  ════════════════════════════════════════ -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('examsApp', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',
        activeTab: 'upcoming',
        searchQuery: '',
        modalOpen: false,
        modalTitle: '',
        modalBody: '',
        liveTime: '',
        liveDate: '',

        exams: [
          @if(isset($exams) && count($exams) > 0)
            @foreach($exams as $exam)
            @php
              $hasSubmitted = isset($submissions) ? $submissions->contains('exam_id', $exam->exam_id) : false;
            @endphp
            {
              id: '{{ $exam->exam_id }}',
              title: '{{ addslashes($exam->title) }}',
              courseId: '{{ $exam->course->id ?? $exam->course_id }}',
              code: '{{ addslashes($exam->course->code ?? "DAT-464") }}',
              dept: '{{ addslashes($exam->course->name ?? "Database Department") }}',
              date: '{{ \Carbon\Carbon::parse($exam->start_time)->format("M d, Y") }}',
              time: '{{ \Carbon\Carbon::parse($exam->start_time)->format("h:i A") }}',
              duration: '{{ $exam->duration ?? 100 }} mins',
              durationMins: {{ (int)($exam->duration ?? 100) }},
              startTimeRaw: '{{ $exam->start_time }}',
              endTimeRaw: '{{ $exam->end_time }}',
              isSubmittedByStudent: {{ $hasSubmitted ? 'true' : 'false' }},
              status: '{{ $hasSubmitted ? "completed" : (\Carbon\Carbon::parse($exam->start_time)->isPast() && \Carbon\Carbon::parse($exam->end_time)->isFuture() ? "ongoing" : "upcoming") }}',
              countdown: '',
              progressPct: 0,
              score: undefined
            },
            @endforeach
          @endif
        ],

        get filteredExams() {
          return this.exams.filter(e => {
            const matchStatus = e.status === this.activeTab;
            const q = this.searchQuery.toLowerCase().trim();
            const matchSearch = q === '' ||
              e.title.toLowerCase().includes(q) ||
              e.code.toLowerCase().includes(q) ||
              e.dept.toLowerCase().includes(q) ||
              String(e.courseId).toLowerCase() === q ||
              String(e.courseId).toLowerCase().includes(q);
            return matchStatus && matchSearch;
          });
        },

        async deleteExam(exam) {
          if (!confirm(`Remove "${exam.title}" from your exam list? This won't affect your grade or submission — it just clears it from this page.`)) {
            return;
          }

          try {
            const res = await fetch(`/student/exams/${exam.id}`, {
              method: 'DELETE',
              headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              }
            });

            const data = await res.json().catch(() => ({}));

            if (!res.ok) {
              alert(data.message || 'Could not remove this exam. Please try again.');
              return;
            }

            this.exams = this.exams.filter(e => e.id !== exam.id);
          } catch (e) {
            console.warn('Failed to delete exam', e);
            alert('Could not remove this exam. Please check your connection and try again.');
          }
        },

        showExamDetails(exam) {
          this.modalTitle = exam.title;
          this.modalBody = `
            <div class="space-y-2 text-xs">
              <div class="flex items-center justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Course Code</span>
                <span class="font-black ${this.darkMode ? 'text-white' : 'text-slate-900'}">${exam.code}</span>
              </div>
              <div class="flex items-center justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Department</span>
                <span class="font-bold ${this.darkMode ? 'text-white' : 'text-slate-800'}">${exam.dept}</span>
              </div>
              <div class="flex items-center justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Scheduled Date</span>
                <span class="font-bold ${this.darkMode ? 'text-white' : 'text-slate-800'}">${exam.date}</span>
              </div>
              <div class="flex items-center justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Start Time</span>
                <span class="font-bold ${this.darkMode ? 'text-white' : 'text-slate-800'}">${exam.time}</span>
              </div>
              <div class="flex items-center justify-between py-2">
                <span class="text-slate-400 font-semibold">Duration</span>
                <span class="font-bold ${this.darkMode ? 'text-white' : 'text-slate-800'}">${exam.duration}</span>
              </div>
            </div>
            <div class="mt-4 px-3 py-2.5 rounded-xl ${this.darkMode ? 'bg-amber-500/10' : 'bg-amber-50'}">
              <p class="text-[11px] text-amber-600 dark:text-amber-400 font-semibold leading-relaxed">⏰ ${exam.countdown ? 'Starts in ' + exam.countdown : 'Check back closer to the exam date for real-time countdown.'}</p>
            </div>
          `;
          this.modalOpen = true;
        },

        updateClock() {
          const now = new Date();
          this.liveTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          this.liveDate = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        },

        formatCountdown(diffMs) {
          if (diffMs <= 0) return null;
          const totalSecs = Math.floor(diffMs / 1000);
          const d = Math.floor(totalSecs / 86400);
          const h = Math.floor((totalSecs % 86400) / 3600);
          const m = Math.floor((totalSecs % 3600) / 60);
          const s = totalSecs % 60;
          if (d > 0) return `${d}d ${h}h ${m}m`;
          if (h > 0) return `${h}h ${m}m ${s}s`;
          return `${m}m ${s}s`;
        },

        // ── Live sync: pulls fresh exams + submissions from the server and
        //    merges them into the reactive list.
        async syncExamsFromServer() {
          try {
            const res = await fetch('{{ route('student.exams') }}', {
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            const serverExams = data.exams || [];
            const submissions = data.submissions || [];

            serverExams.forEach(se => {
              const submission = submissions.find(s => String(s.exam_id) === String(se.exam_id));
              const existing = this.exams.find(e => String(e.id) === String(se.exam_id));

              if (existing) {
                if (submission) {
                  existing.isSubmittedByStudent = true;
                  existing.score = submission.percentage !== null && submission.percentage !== undefined
                    ? Number(submission.percentage)
                    : existing.score;
                  existing.status = 'completed';
                }
              } else {
                const start = new Date(se.start_time);
                const end = new Date(se.end_time);
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
                  dept: (se.course && se.course.name) || 'Database Department',
                  date: start.toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' }),
                  time: start.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                  duration: (se.duration || 100) + ' mins',
                  durationMins: se.duration || 100,
                  startTimeRaw: se.start_time,
                  endTimeRaw: se.end_time,
                  isSubmittedByStudent: !!submission,
                  status: status,
                  countdown: '',
                  progressPct: 0,
                  score: submission && submission.percentage !== null && submission.percentage !== undefined
                    ? Number(submission.percentage)
                    : undefined
                });
              }
            });
          } catch (e) {
            console.warn('Failed to sync exams from server', e);
          }
        },

        init() {
          this.$watch('darkMode', val => localStorage.setItem('darkMode', val));

          this.updateClock();
          setInterval(() => this.updateClock(), 1000);

          setInterval(() => this.syncExamsFromServer(), 15000);

          setInterval(() => {
            const now = new Date();
            this.exams.forEach(exam => {
              if (exam.isSubmittedByStudent === true) {
                exam.status = 'completed';
                return;
              }
              const start = new Date(exam.startTimeRaw);
              const end = new Date(exam.endTimeRaw);

              if (now >= start && now <= end) {
                exam.status = 'ongoing';
                const total = end - start;
                const elapsed = now - start;
                exam.progressPct = Math.min(100, Math.round((elapsed / total) * 100));
                exam.countdown = '';
              } else if (now > end) {
                exam.status = 'completed';
                exam.countdown = '';
              } else {
                exam.status = 'upcoming';
                exam.countdown = this.formatCountdown(start - now);
                exam.progressPct = 0;
              }
            });
          }, 1000);

          lucide.createIcons();
        }
      }));
    });
  </script>
  @include('partials.notification-realtime')
</body>
</html>