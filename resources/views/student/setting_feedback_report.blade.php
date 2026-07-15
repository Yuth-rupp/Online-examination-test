<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Feedback Report</title>
  
  <!-- Anti-Flash Script -->
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
      darkMode: 'class'
    }
  </script>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  
  <style>
    body { font-family: 'Inter', sans-serif; transition: background-color 0.25s, color 0.25s; }
    [x-cloak] { display: none !important; }
    @media print {
      .no-print { display: none !important; }
      .print-area { padding: 0 !important; margin: 0 !important; border: none !important; width: 100% !important; }
      body { background: white !important; color: black !important; }
    }
  </style>
</head>
<body class="min-h-screen flex transition-colors duration-200"
      :class="darkMode ? 'bg-slate-900 text-slate-100' : 'bg-[#F8FAFC] text-[#1E293B]'"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }">

  <!-- Left Sidebar Navigation Layout Frame -->
  <aside class="w-64 border-r flex flex-col justify-between fixed h-full z-10 hidden md:flex transition-colors duration-200 no-print"
         :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-[#E2E8F0]'">
    <div>
      <div class="p-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-100">
          <i data-lucide="graduation-cap" class="w-6 h-6"></i>
        </div>
        <div>
          <h1 class="font-bold text-base leading-tight" :class="darkMode ? 'text-white' : 'text-[#0F172A]'">ExamSystem</h1>
          <p class="text-xs text-[#64748B]">Student Portal</p>
        </div>
      </div>

      <nav class="px-4 space-y-1">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('student.dashboard') ? 'bg-[#1D4ED8] text-white font-semibold shadow-sm' : 'text-[#64748B] hover:bg-[#F1F5F9]' }}">
          <i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard
        </a>
        <a href="{{ route('student.exams') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('student.exams') ? 'bg-[#1D4ED8] text-white font-semibold shadow-sm' : 'text-[#64748B] hover:bg-[#F1F5F9]' }}">
          <i data-lucide="book-open" class="w-5 h-5"></i> Exams
        </a>
        <a href="{{ route('student.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('student.history') ? 'bg-[#1D4ED8] text-white font-semibold shadow-sm' : 'text-[#64748B] hover:bg-[#F1F5F9]' }}">
          <i data-lucide="history" class="w-5 h-5"></i> History
        </a>
        <a href="{{ route('student.support') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('student.support') ? 'bg-[#1D4ED8] text-white font-semibold shadow-sm' : 'text-[#64748B] hover:bg-[#F1F5F9]' }}">
          <i data-lucide="help-circle" class="w-5 h-5"></i> Support
        </a>
        <a href="{{ route('student.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-sm transition-all {{ request()->routeIs('student.settings') ? 'bg-[#1D4ED8] text-white font-semibold shadow-sm' : 'text-[#64748B] hover:bg-[#F1F5F9]' }}">
          <i data-lucide="settings" class="w-5 h-5"></i> Settings
        </a>
      </nav>
    </div>

    <div class="p-4 border-t flex flex-col gap-3 transition-colors duration-200" :class="darkMode ? 'border-slate-700 bg-slate-800/50' : 'border-[#E2E8F0] bg-slate-50/50'">
      <form action="{{ route('logout') }}" method="POST" class="w-full m-0">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 rounded-xl transition-all cursor-pointer">
          <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
        </button>
      </form>
    </div>
  </aside>

  <!-- Main Display Portal View -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col print-area">
    
    <!-- Header Area -->
    <header class="border-b px-6 py-4 flex items-center justify-between sticky top-0 z-20 transition-colors duration-200 shadow-sm no-print"
            :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-[#E2E8F0]'">
      <div class="flex items-center gap-4">
        <a href="{{ route('student.settings') }}" class="p-2 text-[#64748B] hover:bg-[#F1F5F9] dark:hover:bg-slate-700 rounded-xl transition-all">
          <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <h2 class="text-base font-black tracking-tight" :class="darkMode ? 'text-white' : 'text-[#0F172A]'">Feedback Report Overview</h2>
      </div>

      <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode);" class="p-2 text-[#64748B] rounded-xl cursor-pointer" :class="darkMode ? 'hover:bg-slate-700 text-amber-400' : 'hover:bg-[#F1F5F9]'">
        <i data-lucide="sun" class="w-5 h-5" x-show="darkMode"></i>
        <i data-lucide="moon" class="w-5 h-5" x-show="!darkMode"></i>
      </button>
    </header>

    <!-- Content Workspace -->
    <div class="p-6 space-y-6 flex-1 max-w-[900px] w-full mx-auto">
      
      <!-- Metrics Card Block Layout -->
      <div class="border rounded-3xl p-6 shadow-sm space-y-6" :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-white border-[#E2E8F0]'">
        <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4 border-b pb-4" :class="darkMode ? 'border-slate-700' : 'border-slate-100'">
          <div>
            <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider text-blue-600 bg-blue-50 dark:bg-blue-950/40 dark:text-blue-400 rounded-md">
              {{ $submission->exam->course->code ?? 'COURSE' }}
            </span>
            <h2 class="text-2xl font-black mt-2" :class="darkMode ? 'text-white' : 'text-[#0F172A]'">{{ $submission->exam->title ?? 'Exam Title' }}</h2>
          </div>
          
          <button onclick="window.print()" class="text-xs font-bold bg-[#1D4ED8] hover:bg-blue-700 text-white px-4 py-2 rounded-xl shadow-sm transition no-print self-start sm:self-auto cursor-pointer">
            Print Results
          </button>
        </div>

        <!-- Evaluation Results Breakdown Row Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
          <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-transparent">
            <p class="text-[10px] font-bold uppercase text-slate-400">Total Scored Points</p>
            <h4 class="text-2xl font-black mt-1 text-blue-600 dark:text-blue-400">{{ $submission->total_score }}</h4>
          </div>

          <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-transparent">
            <p class="text-[10px] font-bold uppercase text-slate-400">Percentage Weight Achievement</p>
            <h4 class="text-2xl font-black mt-1" :class="darkMode ? 'text-white' : 'text-[#0F172A]'">{{ round($submission->percentage) }}%</h4>
          </div>

          <div class="bg-slate-50 dark:bg-slate-900/40 p-4 rounded-2xl border border-slate-100 dark:border-transparent">
            <p class="text-[10px] font-bold uppercase text-slate-400">Proctor Status Verdict</p>
            <div class="mt-1">
              @if($submission->percentage >= 50)
                <span class="text-emerald-600 dark:text-emerald-400 text-lg font-black uppercase tracking-wider">PASSED</span>
              @else
                <span class="text-rose-600 dark:text-rose-400 text-lg font-black uppercase tracking-wider">FAILED</span>
              @endif
            </div>
          </div>
        </div>

        <div class="p-4 bg-slate-50 dark:bg-slate-900/50 rounded-2xl text-xs font-medium text-slate-400 leading-relaxed border border-slate-100 dark:border-transparent">
          <span class="font-bold text-slate-500 dark:text-slate-300">System Handshake Trace Note:</span> This assessment packet environment summary records were generated securely and processed instantly by the automated online assessment proctor compilation logic.
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      lucide.createIcons();
    });
  </script>
</body>
</html>