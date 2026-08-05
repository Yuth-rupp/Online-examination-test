<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $platformName }} - Results & History</title>
  <meta name="description" content="View your full exam performance history, scores, and grade trends on {{ $platformName }}.">
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

    /* Modal entrance */
    .modal-box { animation: modalIn 0.22s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }

    /* Chart tooltip */
    .chart-tooltip { pointer-events: none; transition: opacity 0.15s; }

    /* Score bar animation */
    .score-bar { transition: width 0.8s cubic-bezier(0.4,0,0.2,1); }

    /* Table row hover */
    .table-row { transition: background-color 0.15s ease; }
  </style>
  @include('partials.notification-styles')
</head>

<body class="min-h-screen flex antialiased transition-colors duration-300"
      :class="darkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'"
      x-data="historyApp">

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
         class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold nav-active">
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

      <!-- Breadcrumb -->
      <div class="flex items-center gap-2 text-sm font-semibold text-slate-400">
        <i data-lucide="trending-up" class="w-4 h-4 text-indigo-500"></i>
        <span>Results & History</span>
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
    <div class="p-6 lg:p-8 space-y-8 flex-1 max-w-[1440px] w-full mx-auto">

      <!-- PAGE HEADER -->
      <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-5">
        <div>
          <div class="flex items-center gap-3 mb-1">
            <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-md shadow-indigo-200 dark:shadow-indigo-900/30">
              <i data-lucide="bar-chart-2" class="w-4 h-4 text-white"></i>
            </div>
            <h2 class="text-2xl font-black tracking-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">Performance History</h2>
          </div>
          <p class="text-sm text-slate-400 font-medium ml-12">Track your academic progress and score trends over time.</p>
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

      <!-- ══════════════════════════════════
           STAT SUMMARY CARDS
      ═══════════════════════════════════ -->
      <section class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Exams Taken -->
        <div class="card rounded-2xl p-5 border"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
              <i data-lucide="clipboard-check" class="w-4 h-4 text-indigo-500"></i>
            </div>
            <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-md">Total</span>
          </div>
          <p class="text-3xl font-black" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="submissions.length"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-0.5">Exams Taken</p>
        </div>

        <!-- Average Score -->
        <div class="card rounded-2xl p-5 border"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                 :class="avgClass.bg">
              <i data-lucide="trending-up" class="w-4 h-4" :class="avgClass.icon"></i>
            </div>
            <span class="text-[10px] font-black px-2 py-0.5 rounded-md"
                  :class="avgClass.badge" x-text="avgClass.label"></span>
          </div>
          <p class="text-3xl font-black" :class="[darkMode ? 'text-white' : 'text-slate-900']" x-text="averagePerformance"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-0.5">Average Score</p>
        </div>

        <!-- Best Score -->
        <div class="card rounded-2xl p-5 border"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
              <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i>
            </div>
            <span class="text-[10px] font-black text-amber-500 bg-amber-50 dark:bg-amber-500/10 px-2 py-0.5 rounded-md">Best</span>
          </div>
          <p class="text-3xl font-black text-amber-500" x-text="bestScore + '%'"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-0.5">Highest Score</p>
        </div>

        <!-- Grade Distribution -->
        <div class="card rounded-2xl p-5 border"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
              <i data-lucide="pie-chart" class="w-4 h-4 text-violet-500"></i>
            </div>
            <span class="text-[10px] font-black text-violet-500 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded-md">Grades</span>
          </div>
          <div class="flex items-center gap-1.5">
            <span class="text-sm font-black text-emerald-500" x-text="gradeCounts.A + ' A'"></span>
            <span class="text-slate-300 dark:text-slate-700">·</span>
            <span class="text-sm font-black text-blue-500" x-text="gradeCounts.Bplus + ' B+'"></span>
            <span class="text-slate-300 dark:text-slate-700">·</span>
            <span class="text-sm font-black text-amber-500" x-text="gradeCounts.C + ' C'"></span>
          </div>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Grade Mix</p>
        </div>

      </section>

      <!-- ══════════════════════════════════
           PERFORMANCE CHART
      ═══════════════════════════════════ -->
      <div class="rounded-2xl border p-6"
           :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">

        <div class="flex items-center justify-between mb-6">
          <div>
            <h3 class="text-base font-black" :class="darkMode ? 'text-white' : 'text-slate-900'">Score Trend</h3>
            <p class="text-xs text-slate-400 font-medium mt-0.5">Your performance across all completed exams</p>
          </div>
          <div class="flex items-center gap-2">
            <span class="flex items-center gap-1.5 text-xs font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1.5 rounded-lg">
              <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse inline-block"></span>
              Live Chart
            </span>
            <span class="text-xs font-bold px-3 py-1.5 rounded-lg"
                  :class="darkMode ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-500'"
                  x-text="'Avg: ' + averagePerformance"></span>
          </div>
        </div>

        <!-- Chart or empty state -->
        <template x-if="gradedSubmissions.length > 0">
          <div class="relative">
            <div class="flex gap-4">
              <!-- Y-axis labels -->
              <div class="flex flex-col justify-between text-[10px] text-slate-400 font-bold text-right pb-8 flex-shrink-0 w-8 select-none">
                <span>100%</span>
                <span>75%</span>
                <span>50%</span>
                <span>25%</span>
                <span>0%</span>
              </div>

              <!-- SVG Chart -->
              <div class="flex-1 relative">
                <svg viewBox="0 0 1000 260" class="w-full h-auto overflow-visible" style="height: 180px;">
                  <defs>
                    <linearGradient id="areaGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#4F6EF7" stop-opacity="0.18"/>
                      <stop offset="100%" stop-color="#4F6EF7" stop-opacity="0.00"/>
                    </linearGradient>
                  </defs>

                  <!-- Grid lines -->
                  <line x1="0" y1="20" x2="1000" y2="20" stroke-width="1" stroke-dasharray="5 5" :stroke="darkMode ? '#1E293B' : '#F1F5F9'"/>
                  <line x1="0" y1="80" x2="1000" y2="80" stroke-width="1" stroke-dasharray="5 5" :stroke="darkMode ? '#1E293B' : '#F1F5F9'"/>
                  <line x1="0" y1="140" x2="1000" y2="140" stroke-width="1" stroke-dasharray="5 5" :stroke="darkMode ? '#1E293B' : '#F1F5F9'"/>
                  <line x1="0" y1="200" x2="1000" y2="200" stroke-width="1" stroke-dasharray="5 5" :stroke="darkMode ? '#1E293B' : '#F1F5F9'"/>

                  <!-- Average reference line -->
                  <line x1="0" :y1="220 - (avgRaw * 1.8)" x2="1000" :y2="220 - (avgRaw * 1.8)"
                        stroke="#A78BFA" stroke-width="1.5" stroke-dasharray="8 4" opacity="0.6"/>

                  <!-- Area fill -->
                  <polygon :points="svgGradientPoints" fill="url(#areaGrad)"/>

                  <!-- Line -->
                  <path :d="'M ' + svgPoints" fill="none" stroke="#4F6EF7" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>

                  <!-- Data points -->
                  <template x-for="(sub, idx) in gradedSubmissions" :key="sub.id">
                    <g class="cursor-pointer"
                       @click="modalTitle = '📊 ' + sub.title; modalBody = buildScoreModal(sub); modalOpen = true">
                      <!-- Outer ring -->
                      <circle :cx="getX(idx)" :cy="getY(sub.percentage)" r="9"
                              :fill="darkMode ? '#0f172a' : '#ffffff'" opacity="0.8"/>
                      <!-- Colored dot -->
                      <circle :cx="getX(idx)" :cy="getY(sub.percentage)" r="6"
                              :fill="gradeColor(sub.grade)" class="hover:r-8 transition-all"/>
                      <!-- Inner dot -->
                      <circle :cx="getX(idx)" :cy="getY(sub.percentage)" r="2.5" fill="white"/>
                    </g>
                  </template>
                </svg>

                <!-- X-axis labels -->
                <div class="flex justify-between text-[10px] font-bold text-slate-400 pt-2 px-0">
                  <template x-for="sub in gradedSubmissions" :key="sub.id">
                    <span class="truncate text-center flex-1 max-w-[100px]" x-text="sub.title"></span>
                  </template>
                </div>
              </div>
            </div>

            <!-- Legend -->
            <div class="flex items-center gap-5 mt-4 pt-4 border-t" :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                <div class="w-3 h-0.5 bg-indigo-500 rounded"></div>
                Score Line
              </div>
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-slate-400">
                <div class="w-3 h-0.5 bg-violet-400 rounded" style="border-top: 1.5px dashed #A78BFA; background: none;"></div>
                Average
              </div>
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-500">
                <div class="w-2.5 h-2.5 rounded-full bg-emerald-500"></div> Grade A (≥85%)
              </div>
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-blue-500">
                <div class="w-2.5 h-2.5 rounded-full bg-blue-500"></div> Grade B+ (≥70%)
              </div>
              <div class="flex items-center gap-1.5 text-[11px] font-semibold text-amber-500">
                <div class="w-2.5 h-2.5 rounded-full bg-amber-500"></div> Grade C (&lt;70%)
              </div>
            </div>
          </div>
        </template>

        <!-- Chart empty state -->
        <template x-if="gradedSubmissions.length === 0">
          <div class="h-44 flex items-center justify-center">
            <div class="text-center">
              <i data-lucide="bar-chart-2" class="w-10 h-10 text-slate-200 dark:text-slate-800 mx-auto mb-2"></i>
              <p class="text-xs text-slate-400 font-medium">No exam results yet.<br>Complete an exam to see your score trend here.</p>
            </div>
          </div>
        </template>
      </div>

      <!-- ══════════════════════════════════
           DETAILED RESULTS TABLE
      ═══════════════════════════════════ -->
      <div class="space-y-4">
        <!-- Controls Row -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-black" :class="darkMode ? 'text-white' : 'text-slate-900'">Detailed Results</h3>
            <p class="text-xs text-slate-400 font-medium mt-0.5" x-text="filteredSubmissions.length + ' result(s) shown'"></p>
          </div>

          <div class="flex flex-wrap items-center gap-2">
            <!-- Search -->
            <div class="relative">
              <i data-lucide="search" class="w-3.5 h-3.5 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none"></i>
              <input x-model="searchQuery" type="text" placeholder="Search exams…"
                     class="pl-9 pr-3 py-2 rounded-xl text-xs font-medium border focus:outline-none focus:ring-2 focus:ring-indigo-500/20 w-44 transition-all"
                     :class="darkMode ? 'bg-slate-800 border-slate-700 text-white placeholder-slate-500' : 'bg-white border-slate-200 text-slate-800 placeholder-slate-400'">
            </div>

            <!-- Grade Filter -->
            <select x-model="gradeFilter"
                    class="px-3 py-2 rounded-xl text-xs font-bold border focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all"
                    :class="darkMode ? 'bg-slate-800 border-slate-700 text-white' : 'bg-white border-slate-200 text-slate-700'">
              <option value="All">All Grades</option>
              <option value="A">Grade A (≥85%)</option>
              <option value="B+">Grade B+ (≥70%)</option>
              <option value="C">Grade C (&lt;70%)</option>
              <option value="Pending">Pending Grading</option>
            </select>

            <!-- Sort -->
            <select x-model="sortOrder"
                    class="px-3 py-2 rounded-xl text-xs font-bold border focus:outline-none focus:ring-2 focus:ring-indigo-500/20 transition-all"
                    :class="darkMode ? 'bg-slate-800 border-slate-700 text-white' : 'bg-white border-slate-200 text-slate-700'">
              <option value="desc">↓ Highest First</option>
              <option value="asc">↑ Lowest First</option>
            </select>

            <!-- Export CSV -->
            <button @click="downloadCSVBoard()"
                    class="flex items-center gap-2 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white px-4 py-2 rounded-xl text-xs font-black cursor-pointer shadow-sm shadow-emerald-200 dark:shadow-emerald-900/20 transition-all">
              <i data-lucide="download" class="w-3.5 h-3.5"></i>
              Export Report
            </button>
          </div>
        </div>

        <!-- Table -->
        <div class="rounded-2xl border overflow-hidden"
             :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
          <div class="overflow-x-auto">
            <table class="w-full text-left">
              <thead>
                <tr class="border-b text-[11px] font-black uppercase tracking-wider text-slate-400"
                    :class="darkMode ? 'border-slate-800 bg-slate-900' : 'border-slate-100 bg-slate-50'">
                  <th class="px-5 py-3.5 w-[35%]">Exam</th>
                  <th class="px-5 py-3.5">Score</th>
                  <th class="px-5 py-3.5">Grade</th>
                  <th class="px-5 py-3.5 hidden md:table-cell">Performance</th>
                  <th class="px-5 py-3.5 hidden lg:table-cell">Percentile</th>
                  <th class="px-5 py-3.5 text-right">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y" :class="darkMode ? 'divide-slate-800' : 'divide-slate-50'">

                <template x-for="sub in filteredSubmissions" :key="sub.id">
                  <tr class="table-row group/row" :class="darkMode ? 'hover:bg-slate-800/50' : 'hover:bg-slate-50/80'">

                    <!-- Exam Name -->
                    <td class="px-5 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0"
                             :class="getIconBg(sub.title)">
                          <i :data-lucide="getIconName(sub.title)" class="w-4 h-4"></i>
                        </div>
                        <div>
                          <p class="text-sm font-bold" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="sub.title"></p>
                          <p class="text-[11px] text-slate-400 font-medium" x-text="sub.code || 'Assessment'"></p>
                        </div>
                      </div>
                    </td>

                    <!-- Score -->
                    <td class="px-5 py-4">
                      <template x-if="!sub.isPending">
                        <div>
                          <p class="text-sm font-black" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="sub.score + '/' + sub.maxScore"></p>
                          <p class="text-[11px] text-slate-400 font-medium" x-text="sub.percentage + '%'"></p>
                        </div>
                      </template>
                      <template x-if="sub.isPending">
                        <p class="text-[11px] text-slate-400 font-semibold italic">Not yet graded</p>
                      </template>
                    </td>

                    <!-- Grade Badge -->
                    <td class="px-5 py-4">
                      <template x-if="!sub.isPending">
                        <span class="inline-flex items-center justify-center w-12 py-1 rounded-lg text-xs font-black"
                              :class="{
                                'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400': sub.grade === 'A',
                                'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400': sub.grade === 'B+',
                                'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400': sub.grade === 'C'
                              }"
                              x-text="sub.grade"></span>
                      </template>
                      <template x-if="sub.isPending">
                        <span class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] font-black uppercase tracking-wide bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400">
                          <i data-lucide="clock" class="w-3 h-3"></i>
                          Pending
                        </span>
                      </template>
                    </td>

                    <!-- Score Progress Bar -->
                    <td class="px-5 py-4 hidden md:table-cell">
                      <template x-if="!sub.isPending">
                        <div class="flex items-center gap-2 min-w-[120px]">
                          <div class="flex-1 h-2 rounded-full overflow-hidden"
                               :class="darkMode ? 'bg-slate-800' : 'bg-slate-100'">
                            <div class="h-full rounded-full score-bar"
                                 :class="{
                                   'bg-gradient-to-r from-emerald-400 to-teal-400': sub.grade === 'A',
                                   'bg-gradient-to-r from-blue-400 to-indigo-400': sub.grade === 'B+',
                                   'bg-gradient-to-r from-amber-400 to-orange-400': sub.grade === 'C'
                                 }"
                                 :style="'width: ' + sub.percentage + '%'"></div>
                          </div>
                          <span class="text-[11px] font-black text-slate-400 w-8 text-right" x-text="sub.percentage + '%'"></span>
                        </div>
                      </template>
                      <template x-if="sub.isPending">
                        <span class="text-[11px] text-slate-400 font-medium">Awaiting teacher review</span>
                      </template>
                    </td>

                    <!-- Percentile -->
                    <td class="px-5 py-4 hidden lg:table-cell">
                      <span class="text-xs font-bold text-slate-400" x-text="sub.isPending ? '—' : sub.percentile"></span>
                    </td>

                    <!-- Action -->
                    <td class="px-5 py-4 text-right">
                      <div class="flex items-center justify-end gap-2" x-show="!sub.isPending">
                        <button @click="modalTitle = '📊 ' + sub.title; modalBody = buildScoreModal(sub); modalOpen = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer"
                                :class="darkMode ? 'bg-slate-800 border border-slate-700 text-slate-300 hover:bg-indigo-500/10 hover:text-indigo-400 hover:border-indigo-500/30' : 'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200'">
                          <i data-lucide="eye" class="w-3 h-3"></i>
                          Details
                        </button>
                        <button @click="modalTitle = '📋 ' + sub.title + ' — Full Review'; modalBody = buildScoreModal(sub); modalOpen = true"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold brand-gradient text-white hover:opacity-90 transition-all shadow-sm cursor-pointer">
                          <i data-lucide="clipboard-list" class="w-3 h-3"></i>
                          Review
                        </button>
                      </div>
                      <span class="text-[11px] text-slate-400 font-semibold italic" x-show="sub.isPending">Awaiting grade</span>
                    </td>
                  </tr>
                </template>

              </tbody>
            </table>
          </div>

          <!-- Table Empty State -->
          <template x-if="filteredSubmissions.length === 0">
            <div class="py-14 flex flex-col items-center justify-center text-center border-t"
                 :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
              <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                <i data-lucide="search-x" class="w-5 h-5 text-slate-400"></i>
              </div>
              <h4 class="text-sm font-bold mb-1" :class="darkMode ? 'text-slate-300' : 'text-slate-700'">No results found</h4>
              <p class="text-xs text-slate-400 max-w-[200px] leading-relaxed">
                Try adjusting your search or filter to find what you're looking for.
              </p>
            </div>
          </template>
        </div>
      </div>

    </div>
  </main>

  <!-- ═══════════════════════════════════════
       DETAILS MODAL
  ════════════════════════════════════════ -->
  <div x-show="modalOpen" x-cloak
       class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
       @click.self="modalOpen = false">
    <div class="modal-box rounded-3xl shadow-2xl border max-w-sm w-full overflow-hidden"
         :class="darkMode ? 'bg-slate-900 border-slate-700' : 'bg-white border-slate-100'">

      <div class="px-6 py-4 border-b flex items-center gap-3"
           :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
        <div class="w-8 h-8 brand-gradient rounded-xl flex items-center justify-center flex-shrink-0">
          <i data-lucide="bar-chart-2" class="w-3.5 h-3.5 text-white"></i>
        </div>
        <h3 class="text-sm font-black flex-1 truncate" :class="darkMode ? 'text-white' : 'text-slate-900'" x-text="modalTitle"></h3>
        <button @click="modalOpen = false"
                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div class="px-6 py-5" x-html="modalBody"></div>

      <div class="px-6 py-4 border-t flex justify-end"
           :class="darkMode ? 'border-slate-800' : 'border-slate-100'">
        <button @click="modalOpen = false"
                class="px-5 py-2 brand-gradient text-white text-xs font-bold rounded-xl shadow-sm hover:opacity-90 transition-opacity cursor-pointer">
          Got it
        </button>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════════════════════
       ALPINE.JS LOGIC
  ════════════════════════════════════════ -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('historyApp', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',
        searchQuery: '',
        gradeFilter: 'All',
        sortOrder: 'desc',
        modalOpen: false,
        modalTitle: '',
        modalBody: '',
        liveTime: '',
        liveDate: '',

        submissions: [
          @if(isset($submissions) && $submissions->count() > 0)
            @foreach($submissions as $sub)
              @if($sub->exam)
              {
                id: '{{ $sub->id }}',
                title: '{{ addslashes($sub->exam->title) }}',
                code: '{{ addslashes($sub->exam->course->code ?? "") }}',
                status: '{{ $sub->status }}',
                isPending: {{ $sub->status === 'pending_grading' ? 'true' : 'false' }},
                score: {{ $sub->total_score ?? 0 }},
                maxScore: {{ $sub->max_score ?? 100 }},
                percentage: {{ $sub->status === 'pending_grading' ? 0 : round($sub->percentage ?? 0) }},
                grade: {{ $sub->status === 'pending_grading' ? 'null' : "'" . ($sub->percentage >= 85 ? 'A' : ($sub->percentage >= 70 ? 'B+' : 'C')) . "'" }},
                percentile: '{{ $sub->percentage >= 85 ? "Top 5%" : ($sub->percentage >= 70 ? "Top 12%" : "Top 26%") }}',
                submittedAt: '{{ \Carbon\Carbon::parse($sub->created_at)->format("M d, Y") }}'
              },
              @endif
            @endforeach
          @endif
        ],

        // ─── COMPUTED PROPERTIES ───────────────────────

        // Submissions still awaiting teacher grading have no real score yet
        // (the essay portion hasn't been marked) — they must never feed into
        // averages, best score, the trend chart, or grade counts, or a
        // student sees a "final" percentage before the teacher has actually
        // finished grading their paper.
        get gradedSubmissions() {
          return this.submissions.filter(s => !s.isPending);
        },

        get averagePerformance() {
          const graded = this.gradedSubmissions;
          if (graded.length === 0) return '0%';
          const total = graded.reduce((s, x) => s + x.percentage, 0);
          return (total / graded.length).toFixed(1) + '%';
        },

        get avgRaw() {
          const graded = this.gradedSubmissions;
          if (graded.length === 0) return 0;
          return graded.reduce((s, x) => s + x.percentage, 0) / graded.length;
        },

        get bestScore() {
          const graded = this.gradedSubmissions;
          if (graded.length === 0) return 0;
          return Math.max(...graded.map(s => s.percentage));
        },

        get gradeCounts() {
          return {
            A: this.gradedSubmissions.filter(s => s.grade === 'A').length,
            Bplus: this.gradedSubmissions.filter(s => s.grade === 'B+').length,
            C: this.gradedSubmissions.filter(s => s.grade === 'C').length,
          };
        },

        get avgClass() {
          const avg = this.avgRaw;
          if (avg >= 85) return { bg: 'bg-emerald-50 dark:bg-emerald-500/10', icon: 'text-emerald-500', badge: 'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10', label: 'Grade A' };
          if (avg >= 70) return { bg: 'bg-blue-50 dark:bg-blue-500/10', icon: 'text-blue-500', badge: 'text-blue-500 bg-blue-50 dark:bg-blue-500/10', label: 'Grade B+' };
          return { bg: 'bg-amber-50 dark:bg-amber-500/10', icon: 'text-amber-500', badge: 'text-amber-500 bg-amber-50 dark:bg-amber-500/10', label: 'Grade C' };
        },

        get filteredSubmissions() {
          let result = this.submissions.filter(sub => {
            const matchSearch = sub.title.toLowerCase().includes(this.searchQuery.toLowerCase()) ||
                                (sub.code || '').toLowerCase().includes(this.searchQuery.toLowerCase());
            const matchGrade = this.gradeFilter === 'All'
              || (this.gradeFilter === 'Pending' && sub.isPending)
              || sub.grade === this.gradeFilter;
            return matchSearch && matchGrade;
          });
          this.$nextTick(() => lucide.createIcons());
          return result.sort((a, b) =>
            this.sortOrder === 'desc' ? b.percentage - a.percentage : a.percentage - b.percentage
          );
        },

        // ─── SVG CHART HELPERS ────────────────────────

        getX(idx) {
          const n = this.gradedSubmissions.length;
          if (n <= 1) return 500;
          return 30 + (idx * 940 / (n - 1));
        },

        getY(pct) {
          return 220 - (pct * 1.9);
        },

        get svgPoints() {
          const graded = this.gradedSubmissions;
          if (graded.length === 0) return '30,220';
          return graded.map((s, i) => `${this.getX(i)},${this.getY(s.percentage)}`).join(' ');
        },

        get svgGradientPoints() {
          const graded = this.gradedSubmissions;
          if (graded.length === 0) return '30,220 30,230';
          const lastX = this.getX(graded.length - 1);
          return `30,230 ${this.svgPoints} ${lastX},230`;
        },

        gradeColor(grade) {
          if (grade === 'A') return '#10B981';
          if (grade === 'B+') return '#3B82F6';
          return '#F59E0B';
        },

        // ─── ICON HELPERS ────────────────────────────

        getIconName(title) {
          const t = title.toLowerCase();
          if (t.includes('database') || t.includes('sql')) return 'database';
          if (t.includes('math') || t.includes('calculus') || t.includes('linear') || t.includes('algebra')) return 'calculator';
          if (t.includes('physics') || t.includes('mechanics')) return 'atom';
          if (t.includes('final') || t.includes('midterm')) return 'file-check-2';
          if (t.includes('network') || t.includes('cisco')) return 'network';
          if (t.includes('program') || t.includes('code') || t.includes('java') || t.includes('python')) return 'code-2';
          return 'book-open';
        },

        getIconBg(title) {
          const t = title.toLowerCase();
          if (t.includes('database') || t.includes('sql')) return this.darkMode ? 'bg-blue-500/10 text-blue-400' : 'bg-blue-50 text-blue-600';
          if (t.includes('math') || t.includes('calculus')) return this.darkMode ? 'bg-violet-500/10 text-violet-400' : 'bg-violet-50 text-violet-600';
          if (t.includes('physics')) return this.darkMode ? 'bg-amber-500/10 text-amber-400' : 'bg-amber-50 text-amber-600';
          return this.darkMode ? 'bg-slate-800 text-slate-400' : 'bg-slate-100 text-slate-500';
        },

        // ─── MODAL BUILDER ───────────────────────────

        buildScoreModal(sub) {
          const barColor = sub.grade === 'A' ? '#10B981' : (sub.grade === 'B+' ? '#3B82F6' : '#F59E0B');
          const darkBg = this.darkMode ? '#1e293b' : '#f8fafc';
          return `
            <div class="space-y-3 text-xs">
              <div class="flex justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Course Code</span>
                <span class="font-black ${this.darkMode ? 'text-white' : 'text-slate-900'}">${sub.code || '—'}</span>
              </div>
              <div class="flex justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Score</span>
                <span class="font-black ${this.darkMode ? 'text-white' : 'text-slate-900'}">${sub.score} / ${sub.maxScore}</span>
              </div>
              <div class="flex justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Grade</span>
                <span class="font-black" style="color:${barColor}">${sub.grade}</span>
              </div>
              <div class="flex justify-between py-2 border-b ${this.darkMode ? 'border-slate-800' : 'border-slate-100'}">
                <span class="text-slate-400 font-semibold">Percentile</span>
                <span class="font-bold ${this.darkMode ? 'text-white' : 'text-slate-800'}">${sub.percentile}</span>
              </div>
              <div class="flex justify-between py-2">
                <span class="text-slate-400 font-semibold">Submitted</span>
                <span class="font-bold ${this.darkMode ? 'text-white' : 'text-slate-800'}">${sub.submittedAt || '—'}</span>
              </div>
              <div class="mt-1 pt-3">
                <div class="flex justify-between mb-1">
                  <span class="text-slate-400 text-[10px] font-semibold">Performance</span>
                  <span class="text-[10px] font-black" style="color:${barColor}">${sub.percentage}%</span>
                </div>
                <div class="w-full h-2 rounded-full overflow-hidden" style="background:${this.darkMode ? '#1e293b' : '#f1f5f9'}">
                  <div class="h-full rounded-full" style="width:${sub.percentage}%; background:${barColor}; transition: width 0.6s ease"></div>
                </div>
              </div>
            </div>
          `;
        },

        // ─── CSV EXPORT ──────────────────────────────

        downloadCSVBoard() {
          const studentName = '{{ Auth::user()->full_name ?? "Student" }}';
          const studentId = '{{ Auth::user()->institutional_id ?? Auth::user()->user_id ?? "N/A" }}';
          const generatedDate = new Date().toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' });
          const generatedTime = new Date().toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit' });

          const gradeLabel = { A: 'Excellent (A)', 'B+': 'Good (B+)', C: 'Average (C)' };

          let csv = '\uFEFF'; // UTF-8 BOM for Excel compatibility

          csv += `EXAMSYSTEM - ACADEMIC PERFORMANCE REPORT\n`;
          csv += `${'─'.repeat(55)}\n`;
          csv += `Student Name       : ${studentName}\n`;
          csv += `Student ID         : ${studentId}\n`;
          csv += `Report Generated   : ${generatedDate} at ${generatedTime}\n`;
          csv += `Total Exams Taken  : ${this.submissions.length}\n`;
          csv += `Overall Average    : ${this.averagePerformance}\n`;
          csv += `Best Score         : ${this.bestScore}%\n`;
          csv += `\n`;

          csv += `${'─'.repeat(55)}\n`;
          csv += `No.,Exam Title,Course Code,Score,Max Score,Percentage,Grade,Performance Level,Percentile Rank,Submitted On\n`;
          csv += `${'─'.repeat(55)}\n`;

          this.submissions.forEach((sub, i) => {
            if (sub.isPending) {
              csv += `${i + 1},"${sub.title}","${sub.code || '—'}","Pending","Pending","Pending","Pending Grading","Awaiting teacher review","—","${sub.submittedAt || '—'}"\n`;
              return;
            }
            const perfLevel = gradeLabel[sub.grade] || sub.grade;
            csv += `${i + 1},"${sub.title}","${sub.code || '—'}",${sub.score},${sub.maxScore},${sub.percentage}%,"${sub.grade}","${perfLevel}","${sub.percentile}","${sub.submittedAt || '—'}"\n`;
          });

          csv += `\n`;
          csv += `${'─'.repeat(55)}\n`;
          csv += `GRADE SUMMARY\n`;
          csv += `Grade A (Excellent ≥85%)  : ${this.gradeCounts.A} exam(s)\n`;
          csv += `Grade B+ (Good ≥70%)      : ${this.gradeCounts.Bplus} exam(s)\n`;
          csv += `Grade C (Average <70%)    : ${this.gradeCounts.C} exam(s)\n`;
          csv += `\n`;
          csv += `This report was automatically generated by {{ $platformName }} Student Portal.\n`;
          csv += `For inquiries, please contact your academic administrator.\n`;

          const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
          const url = URL.createObjectURL(blob);
          const link = document.createElement('a');
          link.href = url;
          link.download = `{{ $platformNameSlug }}_Report_${studentName.replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.csv`;
          document.body.appendChild(link);
          link.click();
          document.body.removeChild(link);
          URL.revokeObjectURL(url);
        },

        // ─── LIVE SYNC ─────────────────────────────────
        async syncSubmissionsFromServer() {
          try {
            const res = await fetch('{{ route('student.history') }}', {
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();
            this.submissions = (data || [])
              .filter(s => s.exam)
              .map(s => {
                const isPending = s.status === 'pending_grading';
                const pct = isPending ? 0 : Math.round(s.percentage || 0);
                return {
                  id: String(s.id),
                  title: s.exam.title,
                  code: (s.exam.course && s.exam.course.code) || '',
                  status: s.status,
                  isPending: isPending,
                  score: s.total_score || 0,
                  maxScore: s.max_score || 100,
                  percentage: pct,
                  grade: isPending ? null : (pct >= 85 ? 'A' : (pct >= 70 ? 'B+' : 'C')),
                  percentile: pct >= 85 ? 'Top 5%' : (pct >= 70 ? 'Top 12%' : 'Top 26%'),
                  submittedAt: new Date(s.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit', year: 'numeric' })
                };
              });
            this.$nextTick(() => lucide.createIcons());
          } catch (e) {
            console.warn('Failed to sync exam history from server', e);
          }
        },

        // ─── CLOCK ───────────────────────────────────

        updateClock() {
          const now = new Date();
          this.liveTime = now.toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
          this.liveDate = now.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric' });
        },

        // ─── INIT ────────────────────────────────────

        init() {
          this.$watch('darkMode', val => localStorage.setItem('darkMode', val));

          this.updateClock();
          setInterval(() => this.updateClock(), 1000);

          // Poll for newly graded submissions every 15s
          setInterval(() => this.syncSubmissionsFromServer(), 15000);

          lucide.createIcons();
        }
      }));
    });
  </script>
  @include('partials.notification-realtime')
</body>
</html>