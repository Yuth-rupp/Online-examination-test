<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Settings & Profile</title>
  <meta name="description" content="Manage your profile and view academic performance on ExamSystem.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script>
    (function () {
      if (localStorage.getItem('darkMode') === 'true') document.documentElement.classList.add('dark');
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
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
    .dark ::-webkit-scrollbar-thumb { background: #334155; }

    .brand-gradient { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); }
    .nav-active { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); color: white; box-shadow: 0 4px 14px rgba(79,110,247,0.35); }
    .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }
    .card-hover { transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .card-hover:hover { transform: translateY(-2px); }

    .modal-box { animation: modalIn 0.22s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.96) translateY(8px); } to { opacity:1; transform:scale(1) translateY(0); } }

    .notif-dropdown { animation: dropIn 0.18s ease; transform-origin: top right; }
    @keyframes dropIn { from { opacity:0; transform:translateY(-6px) scale(0.97); } to { opacity:1; transform:translateY(0) scale(1); } }

    .form-input { transition: all 0.2s ease; }
    .form-input:focus { outline: none; border-color: #4F6EF7; box-shadow: 0 0 0 3px rgba(79,110,247,0.12); }

    /* Profile hero */
    .profile-hero {
      background: linear-gradient(135deg, #4F46E5 0%, #7C3AED 55%, #4F6EF7 100%);
      position: relative;
      overflow: hidden;
      height: 100px;
    }
    .profile-hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background:
        radial-gradient(ellipse at 20% 50%, rgba(255,255,255,0.15) 0%, transparent 60%),
        radial-gradient(ellipse at 85% 30%, rgba(255,255,255,0.08) 0%, transparent 50%);
    }

    /* Avatar camera overlay */
    .avatar-overlay { opacity: 0; transition: opacity 0.2s; }
    .avatar-wrap:hover .avatar-overlay { opacity: 1; }

    .pulse-dot { animation: pulseDot 2s infinite; }
    @keyframes pulseDot { 0%,100%{opacity:1;} 50%{opacity:0.35;} }

    @keyframes bellShake {
      0%,100%{transform:rotate(0);} 15%{transform:rotate(-16deg);} 30%{transform:rotate(16deg);}
      50%{transform:rotate(-10deg);} 70%{transform:rotate(6deg);}
    }
    .bell-ring { animation: bellShake 0.45s ease; }

    @media print {
      .no-print { display: none !important; }
      aside { display: none !important; }
      main { padding-left: 0 !important; }
    }
  </style>
  @include('partials.notification-styles')
</head>

<body class="min-h-screen flex antialiased transition-colors duration-300"
      :class="darkMode ? 'bg-slate-950 text-slate-100' : 'bg-slate-50 text-slate-800'"
      x-data="settingsApp">

  <!-- ════════════ SIDEBAR (resized to match teacher) ════════════ -->
  <aside class="w-64 flex flex-col fixed h-full z-30 hidden md:flex border-r transition-colors duration-300 no-print"
         :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">
    <div class="px-5 pt-6 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 brand-gradient rounded-2xl flex items-center justify-center shadow-lg flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div>
          <h1 class="font-black text-sm" :class="darkMode?'text-white':'text-slate-900'">ExamSystem</h1>
          <p class="text-[11px] font-medium text-slate-400">Student Portal</p>
        </div>
      </div>
    </div>

    <p class="px-5 pt-4 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Main Menu</p>

    <!-- Nav Links (resized to match teacher sidebar) -->
    <nav class="px-3 space-y-1.5 flex-1">
      <a href="{{ route('student.dashboard') }}" class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="layout-dashboard" class="w-5 h-5 flex-shrink-0"></i> Dashboard
      </a>
      <a href="{{ route('student.exams') }}" class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="book-open" class="w-5 h-5 flex-shrink-0"></i> My Exams
      </a>
      <a href="{{ route('student.history') }}" class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="history" class="w-5 h-5 flex-shrink-0"></i> History
      </a>

      <p class="px-2 pt-5 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Resources</p>

      <a href="{{ route('student.support') }}" class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800">
        <i data-lucide="headphones" class="w-5 h-5 flex-shrink-0"></i> Support
      </a>
      <a href="{{ route('student.settings') }}" class="nav-link flex items-center gap-3.5 px-4 py-3 rounded-xl text-[15px] font-semibold nav-active">
        <i data-lucide="settings-2" class="w-5 h-5 flex-shrink-0"></i> Settings
      </a>
    </nav>

    <div class="p-3 m-3 rounded-2xl border" :class="darkMode?'bg-slate-800 border-slate-700':'bg-slate-50 border-slate-100'">
      <div class="flex items-center gap-3">
        <div class="relative flex-shrink-0">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center overflow-hidden shadow-sm">
            @if($user->profile_image)
              <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
            @else
              <span class="text-xs font-black text-amber-900 uppercase">{{ strtoupper(substr($user->full_name,0,2)) }}</span>
            @endif
          </div>
          <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2" :class="darkMode?'border-slate-800':'border-slate-50'"></div>
        </div>
        <div class="flex-1 min-w-0">
          <h4 class="text-xs font-bold truncate" :class="darkMode?'text-white':'text-slate-900'">{{ $user->full_name }}</h4>
          <p class="text-[11px] text-slate-400 truncate">{{ $user->institutional_id ?? $user->user_id }}</p>
        </div>
        <form action="{{ route('logout') }}" method="POST" class="flex-shrink-0">@csrf
          <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 cursor-pointer transition-colors">
            <i data-lucide="log-out" class="w-3.5 h-3.5"></i>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- ════════════ MAIN ════════════ -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">

    <!-- TOPBAR -->
    <header class="border-b px-6 py-3.5 flex items-center justify-between sticky top-0 z-20 no-print transition-colors"
            :class="darkMode?'bg-slate-900/95 border-slate-800 backdrop-blur-xl':'bg-white/95 border-slate-100 backdrop-blur-xl'">

      <div class="relative w-full max-w-sm">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
        <input type="text" placeholder="Search exams, courses…"
               class="w-full pl-10 pr-4 py-2.5 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-indigo-500/25 transition-all"
               :class="darkMode?'bg-slate-800 text-white placeholder-slate-500':'bg-slate-100 text-slate-800 placeholder-slate-400'">
      </div>

      <div class="flex items-center gap-2 ml-4">
        <!-- Dark mode -->
        <button @click="darkMode=!darkMode; localStorage.setItem('darkMode',darkMode)"
                class="p-2.5 rounded-xl cursor-pointer transition-colors"
                :class="darkMode?'bg-slate-800 text-amber-400 hover:bg-slate-700':'bg-slate-100 text-slate-500 hover:bg-slate-200'">
          <i data-lucide="sun" class="w-4 h-4" x-show="darkMode"></i>
          <i data-lucide="moon" class="w-4 h-4" x-show="!darkMode"></i>
        </button>

        @include('partials.notification-bell')
        <!-- ── End Bell ── -->

        <div class="w-px h-6 mx-1" :class="darkMode?'bg-slate-700':'bg-slate-200'"></div>

        <div class="flex items-center gap-2.5 pl-1">
          <div class="w-8 h-8 rounded-xl overflow-hidden bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-[11px] font-black text-amber-900 shadow-sm">
            @if($user->profile_image)
              <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
            @else
              {{ strtoupper(substr($user->full_name,0,2)) }}
            @endif
          </div>
          <div class="hidden sm:block">
            <p class="text-sm font-bold leading-none" :class="darkMode?'text-white':'text-slate-800'">{{ $user->full_name }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Student</p>
          </div>
        </div>
      </div>
    </header>

    <!-- PAGE BODY -->
    <div class="p-6 lg:p-8 space-y-6 max-w-[1440px] w-full mx-auto">

      <!-- ── PROFILE CARD ── -->
      <div class="rounded-3xl overflow-hidden border shadow-sm"
           :class="darkMode?'border-slate-800':'border-slate-100'">

        <!-- Banner with clock -->
        <div class="profile-hero relative flex-shrink-0">
          <div class="absolute top-5 right-6 flex items-center gap-2 bg-white/10 border border-white/20 px-3 py-1.5 rounded-xl no-print">
            <i data-lucide="clock" class="w-3 h-3 text-white/70"></i>
            <span class="text-xs font-black text-white tabular-nums" x-text="liveTime"></span>
            <span class="text-[10px] text-white/60" x-text="liveDate"></span>
          </div>
          <!-- Decorative bubbles -->
          <div class="absolute top-4 right-40 w-16 h-16 rounded-full border border-white/10 bg-white/5"></div>
          <div class="absolute bottom-2 right-20 w-8 h-8 rounded-full border border-white/10 bg-white/5"></div>
        </div>

        <!-- Content below banner -->
        <div :class="darkMode?'bg-slate-900':'bg-white'" class="px-7 pb-7">

          <!-- Avatar + Name row -->
          <div class="flex flex-col sm:flex-row gap-5 items-start sm:items-end -mt-12 mb-6">

            <!-- Avatar upload -->
            <form action="{{ route('student.profile.photo') }}" method="POST" enctype="multipart/form-data" id="avatar-form" class="flex-shrink-0">
              @csrf
              <div class="avatar-wrap relative cursor-pointer rounded-2xl border-4 shadow-xl overflow-hidden w-24 h-24"
                   :class="darkMode?'border-slate-900':'border-white'"
                   onclick="document.getElementById('img-uploader').click()">
                @if($user->profile_image)
                  <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
                @else
                  <div class="w-full h-full bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center">
                    <span class="text-2xl font-black text-amber-900 uppercase">{{ strtoupper(substr($user->full_name,0,2)) }}</span>
                  </div>
                @endif
                <div class="avatar-overlay absolute inset-0 bg-slate-900/60 flex flex-col items-center justify-center gap-1">
                  <i data-lucide="camera" class="w-5 h-5 text-white"></i>
                  <span class="text-[10px] font-bold text-white">Change</span>
                </div>
                <input type="file" id="img-uploader" name="profile_photo" class="hidden" onchange="document.getElementById('avatar-form').submit()">
              </div>
            </form>

            <!-- Name + email -->
            <div class="flex-1 min-w-0 pt-14 sm:pt-0 sm:pb-1">
              <div class="flex flex-wrap items-center gap-2 mb-0.5">
                <h2 class="text-xl font-black leading-tight" :class="darkMode?'text-white':'text-slate-900'">{{ $user->full_name }}</h2>
                <span class="px-2.5 py-0.5 text-[10px] font-black rounded-lg brand-gradient text-white flex-shrink-0">Student</span>
              </div>
              <p class="text-sm text-slate-400 font-medium">{{ $user->email }}</p>
            </div>
          </div>

          <!-- Info chips -->
          <div class="flex flex-wrap gap-2 mb-5">
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border"
                  :class="darkMode?'bg-slate-800 border-slate-700 text-slate-300':'bg-slate-50 border-slate-100 text-slate-600'">
              <i data-lucide="hash" class="w-3 h-3 text-indigo-500"></i>
              {{ $user->institutional_id ?? $user->user_id ?? 'STU-2026' }}
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border"
                  :class="darkMode?'bg-slate-800 border-slate-700 text-slate-300':'bg-slate-50 border-slate-100 text-slate-600'">
              <i data-lucide="book" class="w-3 h-3 text-indigo-500"></i>
              Computer Science
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border"
                  :class="darkMode?'bg-slate-800 border-slate-700 text-slate-300':'bg-slate-50 border-slate-100 text-slate-600'">
              <i data-lucide="map-pin" class="w-3 h-3 text-indigo-500"></i>
              Main Campus, Hall B
            </span>
            <span class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold border"
                  :class="darkMode?'bg-slate-800 border-slate-700 text-slate-300':'bg-slate-50 border-slate-100 text-slate-600'">
              <i data-lucide="calendar" class="w-3 h-3 text-indigo-500"></i>
              Enrolled {{ now()->format('Y') }}
            </span>
          </div>

          <!-- Buttons -->
          <div class="flex flex-wrap gap-2 no-print">
            <button @click="modalOpen=true"
                    class="flex items-center gap-2 px-5 py-2.5 brand-gradient text-white text-xs font-black rounded-xl shadow-md shadow-indigo-200 dark:shadow-indigo-900/30 hover:opacity-90 cursor-pointer transition-all">
              <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Profile
            </button>
            <button onclick="window.print()"
                    class="flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl border cursor-pointer transition-all"
                    :class="darkMode?'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700':'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
              <i data-lucide="file-down" class="w-3.5 h-3.5"></i> Export PDF
            </button>
            <a href="{{ route('student.history') }}"
               class="flex items-center gap-2 px-5 py-2.5 text-xs font-bold rounded-xl border cursor-pointer transition-all"
               :class="darkMode?'bg-slate-800 border-slate-700 text-slate-300 hover:bg-slate-700':'bg-slate-50 border-slate-200 text-slate-600 hover:bg-slate-100'">
              <i data-lucide="bar-chart-2" class="w-3.5 h-3.5"></i> Full Results
            </a>
          </div>
        </div>
      </div>

      <!-- ── STAT CARDS ── -->
      <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card-hover rounded-2xl p-5 border" :class="darkMode?'bg-slate-900 border-slate-800':'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 flex items-center justify-center">
              <i data-lucide="clipboard-check" class="w-4 h-4 text-indigo-500"></i>
            </div>
            <span class="text-[10px] font-black text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-2 py-0.5 rounded-md">Total</span>
          </div>
          <p class="text-3xl font-black" :class="darkMode?'text-white':'text-slate-900'" x-text="chartBars.length"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Exams Taken</p>
        </div>

        <div class="card-hover rounded-2xl p-5 border" :class="darkMode?'bg-slate-900 border-slate-800':'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl flex items-center justify-center"
                 :class="metrics.averageScore>=80?'bg-emerald-50 dark:bg-emerald-500/10':metrics.averageScore>=50?'bg-blue-50 dark:bg-blue-500/10':'bg-amber-50 dark:bg-amber-500/10'">
              <i data-lucide="trending-up" class="w-4 h-4"
                 :class="metrics.averageScore>=80?'text-emerald-500':metrics.averageScore>=50?'text-blue-500':'text-amber-500'"></i>
            </div>
            <span class="text-[10px] font-black px-2 py-0.5 rounded-md"
                  :class="metrics.averageScore>=80?'text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10':metrics.averageScore>=50?'text-blue-500 bg-blue-50 dark:bg-blue-500/10':'text-amber-500 bg-amber-50 dark:bg-amber-500/10'"
                  x-text="metrics.averageScore>=80?'Grade A':metrics.averageScore>=70?'Grade B+':'Grade C'"></span>
          </div>
          <p class="text-3xl font-black"
             :class="metrics.averageScore>=80?'text-emerald-500':metrics.averageScore>=50?'text-blue-500':'text-amber-500'"
             x-text="metrics.averageScore+'%'"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Avg Score</p>
        </div>

        <div class="card-hover rounded-2xl p-5 border" :class="darkMode?'bg-slate-900 border-slate-800':'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-500/10 flex items-center justify-center">
              <i data-lucide="trophy" class="w-4 h-4 text-amber-500"></i>
            </div>
            <span class="text-[10px] font-black text-amber-500 bg-amber-50 dark:bg-amber-500/10 px-2 py-0.5 rounded-md">Best</span>
          </div>
          <p class="text-3xl font-black text-amber-500" x-text="bestScore+'%'"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Highest Score</p>
        </div>

        <div class="card-hover rounded-2xl p-5 border" :class="darkMode?'bg-slate-900 border-slate-800':'bg-white border-slate-100'">
          <div class="flex items-center justify-between mb-4">
            <div class="w-10 h-10 rounded-xl bg-violet-50 dark:bg-violet-500/10 flex items-center justify-center">
              <i data-lucide="medal" class="w-4 h-4 text-violet-500"></i>
            </div>
            <span class="text-[10px] font-black text-violet-500 bg-violet-50 dark:bg-violet-500/10 px-2 py-0.5 rounded-md">Rank</span>
          </div>
          <p class="text-lg font-black text-violet-500 leading-tight"
             x-text="metrics.averageScore>=85?'Top 5%':metrics.averageScore>=70?'Top 15%':metrics.averageScore>=50?'Top 30%':'Keep Going 💪'"></p>
          <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wide mt-1">Class Rank</p>
        </div>
      </div>

      <!-- ── CHART ── -->
      <div class="rounded-2xl border" :class="darkMode?'bg-slate-900 border-slate-800':'bg-white border-slate-100'">
        <div class="px-6 py-5 border-b flex items-center justify-between" :class="darkMode?'border-slate-800':'border-slate-100'">
          <div>
            <h3 class="text-sm font-black" :class="darkMode?'text-white':'text-slate-900'">Performance Trend</h3>
            <p class="text-xs text-slate-400 font-medium mt-0.5">
              Your last <span class="font-black text-indigo-500" x-text="chartBars.length"></span> exam scores
            </p>
          </div>
          <span class="flex items-center gap-1.5 text-xs font-bold text-indigo-500 bg-indigo-50 dark:bg-indigo-500/10 px-3 py-1.5 rounded-lg">
            <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse inline-block"></span>
            Live
          </span>
        </div>

        <div class="p-6">
          <template x-if="chartBars.length > 0">
            <div class="flex gap-4">
              <div class="flex flex-col justify-between text-[10px] text-slate-400 font-bold text-right pb-7 w-8 flex-shrink-0 select-none">
                <span>100%</span><span>75%</span><span>50%</span><span>25%</span><span>0%</span>
              </div>
              <div class="flex-1">
                <svg viewBox="0 0 1000 200" class="w-full overflow-visible" style="height:130px">
                  <defs>
                    <linearGradient id="barGrad" x1="0" y1="0" x2="0" y2="1">
                      <stop offset="0%" stop-color="#4F6EF7" stop-opacity="0.95"/>
                      <stop offset="100%" stop-color="#7C3AED" stop-opacity="0.8"/>
                    </linearGradient>
                  </defs>
                  <line x1="0" y1="0"   x2="1000" y2="0"   stroke-dasharray="5 5" stroke-width="1" :stroke="darkMode?'#1e293b':'#f1f5f9'"/>
                  <line x1="0" y1="50"  x2="1000" y2="50"  stroke-dasharray="5 5" stroke-width="1" :stroke="darkMode?'#1e293b':'#f1f5f9'"/>
                  <line x1="0" y1="100" x2="1000" y2="100" stroke-dasharray="5 5" stroke-width="1" :stroke="darkMode?'#1e293b':'#f1f5f9'"/>
                  <line x1="0" y1="150" x2="1000" y2="150" stroke-dasharray="5 5" stroke-width="1" :stroke="darkMode?'#1e293b':'#f1f5f9'"/>

                  <template x-for="(bar, i) in chartBars" :key="i">
                    <g class="cursor-pointer"
                       @click="modalContent = bar.label + ' — Score: ' + bar.score + '%'; modalOpen2=true">
                      <rect
                        :x="(i * (1000/chartBars.length)) + 16"
                        :y="200 - (bar.score * 1.9)"
                        :width="(1000/chartBars.length) - 32"
                        :height="bar.score * 1.9"
                        rx="8"
                        :fill="i === chartBars.length-1 ? 'url(#barGrad)' : (darkMode ? '#334155' : '#e2e8f0')"
                        class="hover:opacity-75 transition-opacity duration-200"/>
                      <text
                        :x="(i * (1000/chartBars.length)) + (1000/chartBars.length)/2"
                        :y="200 - (bar.score * 1.9) - 8"
                        text-anchor="middle"
                        font-size="24"
                        font-weight="800"
                        :fill="darkMode?'#94a3b8':'#64748b'"
                        x-text="bar.score+'%'"/>
                    </g>
                  </template>
                </svg>
                <div class="flex pt-2">
                  <template x-for="bar in chartBars" :key="bar.label">
                    <span class="text-[10px] font-bold text-slate-400 text-center flex-1 truncate" x-text="bar.label"></span>
                  </template>
                </div>
              </div>
            </div>
          </template>

          <template x-if="chartBars.length === 0">
            <div class="h-36 flex flex-col items-center justify-center">
              <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                <i data-lucide="bar-chart-2" class="w-6 h-6 text-slate-400"></i>
              </div>
              <p class="text-sm font-bold mb-1" :class="darkMode?'text-slate-300':'text-slate-700'">No data yet</p>
              <p class="text-xs text-slate-400 max-w-[200px] text-center leading-relaxed">Complete your first exam to see your performance trend here.</p>
            </div>
          </template>
        </div>
      </div>

      <!-- ── EXAM HISTORY TABLE ── -->
      <div class="rounded-2xl border overflow-hidden" :class="darkMode?'bg-slate-900 border-slate-800':'bg-white border-slate-100'">
        <div class="px-6 py-5 border-b flex items-center justify-between" :class="darkMode?'border-slate-800':'border-slate-100'">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-sm">
              <i data-lucide="scroll-text" class="w-4 h-4 text-white"></i>
            </div>
            <div>
              <h3 class="text-sm font-black" :class="darkMode?'text-white':'text-slate-900'">Exam History</h3>
              <p class="text-[11px] text-slate-400">All your completed assessments</p>
            </div>
          </div>
          <button onclick="window.print()"
                  class="no-print flex items-center gap-2 px-4 py-2 text-xs font-bold rounded-xl border cursor-pointer transition-all"
                  :class="darkMode?'bg-slate-800 border-slate-700 text-emerald-400 hover:bg-slate-700':'bg-emerald-50 border-emerald-100 text-emerald-700 hover:bg-emerald-100'">
            <i data-lucide="file-down" class="w-3.5 h-3.5"></i> Export PDF
          </button>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left">
            <thead>
              <tr class="text-[11px] font-black uppercase tracking-wider text-slate-400 border-b"
                  :class="darkMode?'border-slate-800 bg-slate-900':'border-slate-100 bg-slate-50'">
                <th class="px-6 py-3.5 w-[36%]">Exam</th>
                <th class="px-6 py-3.5">Score</th>
                <th class="px-6 py-3.5">Status</th>
                <th class="px-6 py-3.5 hidden md:table-cell">Performance</th>
                <th class="px-6 py-3.5 hidden sm:table-cell">Date</th>
                <th class="px-6 py-3.5 text-right no-print">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y" :class="darkMode?'divide-slate-800':'divide-slate-50'">
              @if(isset($submissions) && count($submissions) > 0)
                @foreach($submissions as $sub)
                  <tr class="transition-colors" :class="darkMode?'hover:bg-slate-800/50':'hover:bg-slate-50/80'">
                    <td class="px-6 py-4">
                      <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex-shrink-0 flex items-center justify-center"
                             :class="darkMode?'bg-indigo-500/10 text-indigo-400':'bg-indigo-50 text-indigo-600'">
                          <i data-lucide="{{ Str::contains(strtolower($sub->exam->title ?? ''), 'database') ? 'database' : (Str::contains(strtolower($sub->exam->title ?? ''), 'math') ? 'calculator' : (Str::contains(strtolower($sub->exam->title ?? ''), 'physics') ? 'atom' : 'book-open')) }}" class="w-4 h-4"></i>
                        </div>
                        <div>
                          <p class="text-sm font-bold" :class="darkMode?'text-white':'text-slate-900'">{{ $sub->exam->title ?? 'Assessment' }}</p>
                          <p class="text-[11px] text-slate-400">{{ $sub->exam->course->code ?? 'GEN-101' }}</p>
                        </div>
                      </div>
                    </td>
                    <td class="px-6 py-4">
                      <p class="text-sm font-black" :class="darkMode?'text-white':'text-slate-900'">
                        {{ round($sub->percentage) }}<span class="text-slate-400 font-semibold text-xs">/100</span>
                      </p>
                    </td>
                    <td class="px-6 py-4">
                      @if($sub->percentage >= 50)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-black rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                          <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>Passed
                        </span>
                      @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-black rounded-lg bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400">
                          <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Failed
                        </span>
                      @endif
                    </td>
                    <td class="px-6 py-4 hidden md:table-cell">
                      <div class="flex items-center gap-2 min-w-[120px]">
                        <div class="flex-1 h-2 rounded-full overflow-hidden" :class="darkMode?'bg-slate-800':'bg-slate-100'">
                          <div class="h-full rounded-full {{ $sub->percentage >= 80 ? 'bg-gradient-to-r from-emerald-400 to-teal-400' : ($sub->percentage >= 50 ? 'bg-gradient-to-r from-blue-400 to-indigo-400' : 'bg-gradient-to-r from-amber-400 to-red-400') }}"
                               style="width:{{ $sub->percentage }}%"></div>
                        </div>
                        <span class="text-[11px] font-black text-slate-400 w-8 text-right">{{ round($sub->percentage) }}%</span>
                      </div>
                    </td>
                    <td class="px-6 py-4 hidden sm:table-cell">
                      <p class="text-xs font-bold text-slate-400">{{ \Carbon\Carbon::parse($sub->created_at)->format('M d, Y') }}</p>
                      <p class="text-[10px] text-slate-500 mt-0.5">{{ \Carbon\Carbon::parse($sub->created_at)->format('h:i A') }}</p>
                    </td>
                    <td class="px-6 py-4 text-right no-print">
                      <a href="{{ route('exams.feedback', ['id' => $sub->id]) }}"
                         class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold transition-all"
                         :class="darkMode?'bg-slate-800 border border-slate-700 text-slate-300 hover:bg-indigo-500/10 hover:text-indigo-400 hover:border-indigo-500/30':'bg-slate-50 border border-slate-200 text-slate-600 hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200'">
                        <i data-lucide="eye" class="w-3 h-3"></i> View
                      </a>
                    </td>
                  </tr>
                @endforeach
              @else
                <tr><td colspan="6">
                  <div class="py-14 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                      <i data-lucide="inbox" class="w-6 h-6 text-slate-400"></i>
                    </div>
                    <h4 class="text-sm font-bold mb-1" :class="darkMode?'text-slate-300':'text-slate-700'">No exam history yet</h4>
                    <p class="text-xs text-slate-400 leading-relaxed max-w-[200px]">Complete your first exam to see results here.</p>
                    <a href="{{ route('student.exams') }}"
                       class="mt-4 inline-flex items-center gap-2 px-4 py-2 brand-gradient text-white text-xs font-black rounded-xl shadow-sm hover:opacity-90 transition-opacity">
                      <i data-lucide="book-open" class="w-3.5 h-3.5"></i> View My Exams
                    </a>
                  </div>
                </td></tr>
              @endif
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </main>

  <!-- ════ EDIT PROFILE MODAL ════ -->
  <div x-show="modalOpen" x-cloak
       class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4 no-print"
       @click.self="modalOpen=false">
    <div class="modal-box rounded-3xl shadow-2xl border max-w-sm w-full overflow-hidden"
         :class="darkMode?'bg-slate-900 border-slate-700':'bg-white border-slate-100'">
      <div class="px-6 py-4 border-b flex items-center gap-3" :class="darkMode?'border-slate-800':'border-slate-100'">
        <div class="w-8 h-8 brand-gradient rounded-xl flex items-center justify-center">
          <i data-lucide="edit-3" class="w-3.5 h-3.5 text-white"></i>
        </div>
        <div class="flex-1">
          <h3 class="text-sm font-black" :class="darkMode?'text-white':'text-slate-900'">Edit Profile</h3>
          <p class="text-[11px] text-slate-400">Update your display name</p>
        </div>
        <button @click="modalOpen=false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer transition-colors">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <form action="{{ route('student.profile.update') }}" method="POST">@csrf
        <div class="px-6 py-5 space-y-4">
          <div>
            <label class="block text-xs font-black text-slate-400 uppercase tracking-widest mb-2">Full Name <span class="text-red-400">*</span></label>
            <div class="relative">
              <i data-lucide="user" class="w-4 h-4 text-slate-400 absolute left-3.5 top-1/2 -translate-y-1/2 pointer-events-none"></i>
              <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                     class="form-input w-full pl-10 pr-4 py-3 rounded-xl text-sm font-medium border transition-all"
                     :class="darkMode?'bg-slate-800 border-slate-700 text-white placeholder-slate-500':'bg-slate-50 border-slate-200 text-slate-900 focus:bg-white'">
            </div>
          </div>
          <div class="flex items-start gap-2.5 p-3.5 rounded-xl border"
               :class="darkMode?'bg-slate-800 border-slate-700':'bg-amber-50 border-amber-100'">
            <i data-lucide="info" class="w-3.5 h-3.5 text-amber-500 mt-0.5 flex-shrink-0"></i>
            <p class="text-[11px] text-amber-700 dark:text-amber-400 leading-relaxed font-medium">
              Student ID and email cannot be changed here. Contact your administrator for those changes.
            </p>
          </div>
        </div>
        <div class="px-6 py-4 border-t flex justify-end gap-2" :class="darkMode?'border-slate-800':'border-slate-100'">
          <button type="button" @click="modalOpen=false"
                  class="px-4 py-2 rounded-xl text-xs font-bold border cursor-pointer transition-all"
                  :class="darkMode?'border-slate-700 text-slate-300 hover:bg-slate-800':'border-slate-200 text-slate-600 hover:bg-slate-50'">
            Cancel
          </button>
          <button type="submit" class="px-5 py-2 brand-gradient text-white text-xs font-black rounded-xl shadow-sm hover:opacity-90 cursor-pointer transition-opacity">
            Save Changes
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- ════ SCORE DETAIL MINI-MODAL ════ -->
  <div x-show="modalOpen2" x-cloak
       class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
       @click.self="modalOpen2=false">
    <div class="modal-box rounded-2xl shadow-2xl border max-w-xs w-full overflow-hidden"
         :class="darkMode?'bg-slate-900 border-slate-700':'bg-white border-slate-100'">
      <div class="px-5 py-4 flex items-center justify-between border-b" :class="darkMode?'border-slate-800':'border-slate-100'">
        <h3 class="text-sm font-black" :class="darkMode?'text-white':'text-slate-900'">Score Details</h3>
        <button @click="modalOpen2=false" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 cursor-pointer">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>
      <div class="px-5 py-5 text-sm font-bold" :class="darkMode?'text-slate-200':'text-slate-700'" x-text="modalContent"></div>
      <div class="px-5 pb-4 flex justify-end">
        <button @click="modalOpen2=false" class="px-4 py-2 brand-gradient text-white text-xs font-black rounded-xl cursor-pointer hover:opacity-90">Close</button>
      </div>
    </div>
  </div>

  <!-- ════ ALPINE + REAL-TIME NOTIFICATION LOGIC ════ -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('settingsApp', () => ({
        darkMode: localStorage.getItem('darkMode') === 'true',
        modalOpen: false,
        modalOpen2: false,
        modalContent: '',
        liveTime: '',
        liveDate: '',

        metrics: {
          averageScore: {{ round($averageScore ?? 0) }}
        },

        get bestScore() {
          if (!this.chartBars.length) return 0;
          return Math.max(...this.chartBars.map(b => b.score));
        },

        chartBars: [
          @if(isset($submissions) && count($submissions) > 0)
            @foreach($submissions->take(7)->reverse() as $sub)
              { score: {{ round($sub->percentage ?? 0) }}, label: '{{ \Carbon\Carbon::parse($sub->created_at)->format("M d") }}' },
            @endforeach
          @endif
        ],

        updateClock() {
          const now = new Date();
          this.liveTime = now.toLocaleTimeString('en-US', { hour:'2-digit', minute:'2-digit', second:'2-digit' });
          this.liveDate = now.toLocaleDateString('en-US', { weekday:'short', month:'short', day:'numeric' });
        },

        // ── Live sync: refreshes the average score and performance chart
        //    the moment a submission is newly graded, without a manual
        //    page refresh — same polling pattern used on the Dashboard,
        //    Exams, and History pages.
        async syncSettingsFromServer() {
          try {
            const res = await fetch('{{ route('student.settings') }}', {
              headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const data = await res.json();

            this.metrics.averageScore = Math.round(data.averageScore ?? this.metrics.averageScore);

            const submissions = data.submissions || [];
            this.chartBars = submissions.slice(0, 7).reverse().map(s => ({
              score: Math.round(s.percentage || 0),
              label: new Date(s.created_at).toLocaleDateString('en-US', { month: 'short', day: '2-digit' })
            }));
          } catch (e) {
            console.warn('Failed to sync settings from server', e);
          }
        },

        init() {
          this.$watch('darkMode', val => localStorage.setItem('darkMode', val));
          this.updateClock();
          setInterval(() => this.updateClock(), 1000);

          setInterval(() => this.syncSettingsFromServer(), 15000);

          lucide.createIcons();
        }
      }));
    });
  </script>
  @include('partials.notification-realtime')
</body>
</html>