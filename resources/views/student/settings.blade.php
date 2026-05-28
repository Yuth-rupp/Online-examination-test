<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Profile & Settings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex">

  <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between fixed h-full z-10 hidden md:flex">
    <div>
      <div class="p-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white">
          <i data-lucide="graduation-cap" class="w-6 h-6"></i>
        </div>
        <div>
          <h1 class="font-bold text-base text-[#0F172A] leading-tight">ExamSystem</h1>
          <p class="text-xs text-[#64748B]">Student Portal</p>
        </div>
      </div>

      <nav class="px-4 space-y-1">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all">
          <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
          Dashboard
        </a>
        <a href="{{ route('student.exams') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all">
          <i data-lucide="book-open" class="w-5 h-5"></i>
          Exams
        </a>
        <a href="{{ route('student.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all">
          <i data-lucide="history" class="w-5 h-5"></i>
          History
        </a>
        <a href="{{ route('student.support') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all">
          <i data-lucide="help-circle" class="w-5 h-5"></i>
          Support
        </a>
        <a href="{{ route('student.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#1D4ED8] text-white font-medium text-sm transition-all">
          <i data-lucide="settings" class="w-5 h-5"></i>
          Settings
        </a>
      </nav>
    </div>

    <div class="p-4 border-t border-[#E2E8F0] flex flex-col gap-3">
      <form action="{{ route('logout') }}" method="POST" class="w-full">
        @csrf
        <button type="submit" class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-red-500 hover:text-red-700 transition-colors p-2 rounded-lg hover:bg-red-50 w-full text-left">
          <i data-lucide="log-out" class="w-4 h-4"></i> Sign Out
        </button>
      </form>
      
      <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
        <div class="w-10 h-10 rounded-full bg-amber-400 overflow-hidden flex items-center justify-center font-bold text-amber-900 border border-slate-200">
          AJ
        </div>
        <div>
          <h4 class="text-sm font-semibold text-[#0F172A]">{{ $user->full_name ?? 'Alex Johnson' }}</h4>
          <p class="text-xs text-[#64748B]">ID: {{ $user->institutional_id ?? '2024-0891' }}</p>
        </div>
      </div>
    </div>
  </aside>

  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">
    
    <header class="bg-white border-b border-[#E2E8F0] px-6 py-4 flex items-center justify-between sticky top-0 z-20">
      <div class="relative w-full max-w-md">
        <i data-lucide="search" class="w-5 h-5 text-[#94A3B8] absolute left-3 top-1/2 -translate-y-1/2"></i>
        <input type="text" placeholder="Search exams, courses, or guides..." class="w-full bg-[#F1F5F9] pl-11 pr-4 py-2 rounded-xl text-sm border-none focus:outline-none focus:ring-2 focus:ring-[#1D4ED8]/20 text-[#1E293B]">
      </div>

      <div class="flex items-center gap-4">
        <button class="p-2 text-[#64748B] hover:bg-[#F1F5F9] rounded-xl"><i data-lucide="bell" class="w-5 h-5"></i></button>
        <button class="p-2 text-[#64748B] hover:bg-[#F1F5F9] rounded-xl"><i data-lucide="contrast" class="w-5 h-5"></i></button>
        <div class="flex items-center gap-2 pl-2 border-l border-[#E2E8F0]">
          <div class="w-8 h-8 rounded-full bg-amber-400 flex items-center justify-center text-xs font-bold text-amber-900">AJ</div>
          <span class="text-sm font-medium text-[#0F172A] hidden sm:inline">Alex J.</span>
        </div>
      </div>
    </header>

    <div class="p-6 space-y-6 flex-1 max-w-[1400px] w-full mx-auto">
      
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm lg:col-span-2 flex items-center gap-6 relative">
          <div class="relative group">
            <div class="w-24 h-24 rounded-full bg-amber-400 border-4 border-white shadow-md overflow-hidden flex items-center justify-center text-3xl font-bold text-amber-900">
              AJ
            </div>
            <button class="absolute bottom-0 right-0 w-7 h-7 bg-white rounded-full border border-slate-200 shadow flex items-center justify-center text-slate-500 hover:text-[#1D4ED8]">
              <i data-lucide="camera" class="w-4 h-4"></i>
            </button>
          </div>

          <div class="space-y-2 flex-1">
            <span class="px-2 py-0.5 bg-blue-50 text-[10px] font-bold text-[#1D4ED8] rounded uppercase tracking-wider">Student Profile</span>
            <h2 class="text-2xl font-bold text-[#0F172A] capitalize">{{ strtolower($user->full_name ?? 'alex johnson') }}</h2>
            <p class="text-sm text-[#64748B]">{{ $user->email ?? 'alex.rivera@university.edu' }}</p>
            
            <div class="flex flex-wrap gap-4 text-xs font-medium text-slate-500 pt-1">
              <span class="flex items-center gap-1.5"><i data-lucide="code" class="w-4 h-4 text-slate-400"></i> Computer Science</span>
              <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-4 h-4 text-slate-400"></i> Main Campus, Hall B</span>
            </div>

            <button onclick="document.getElementById('edit-modal').classList.remove('hidden')" class="mt-2 px-4 py-1.5 bg-[#F1F5F9] hover:bg-[#E2E8F0] text-slate-700 text-xs font-semibold rounded-lg flex items-center gap-1.5 transition-colors">
              <i data-lucide="edit-3" class="w-3.5 h-3.5"></i> Edit Profile
            </button>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex flex-col justify-between">
          <div class="flex items-center justify-between">
            <h3 class="text-xs font-bold tracking-wider text-slate-400 uppercase">Academic Progress</h3>
            <span class="px-2 py-0.5 bg-emerald-50 text-xs font-bold text-emerald-600 rounded">+4.2%</span>
          </div>

          <div class="grid grid-cols-5 items-end gap-2 h-20 px-2 relative mt-4">
            <div class="w-full bg-[#E2E8F0]/50 h-14 rounded-md"></div>
            <div class="w-full bg-[#E2E8F0]/70 h-16 rounded-md"></div>
            <div class="relative w-full bg-[#1D4ED8] h-20 rounded-md flex justify-center">
              <span class="absolute -top-5 text-[10px] font-bold text-[#1D4ED8]">91</span>
            </div>
            <div class="w-full bg-[#E2E8F0]/70 h-16 rounded-md"></div>
            <div class="w-full bg-[#E2E8F0]/50 h-14 rounded-md"></div>
          </div>

          <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 mt-4">
            <div>
              <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Avg Score</p>
              <h4 class="text-lg font-bold text-slate-800">{{ $averageScore > 0 ? number_format($averageScore, 1) : '91.4' }}%</h4>
            </div>
            <div>
              <p class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-0.5">Rank</p>
              <h4 class="text-lg font-bold text-slate-800">Top 5%</h4>
            </div>
          </div>
        </div>

      </div>

      <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
        <div class="p-6 border-b border-[#E2E8F0] flex items-center justify-between">
          <h3 class="text-base font-bold text-[#0F172A] flex items-center gap-2">
            <span class="w-1.5 h-4 bg-[#1D4ED8] rounded-full"></span> Exam History
          </h3>
          <button class="text-xs font-semibold text-[#1D4ED8] hover:underline flex items-center gap-1">
            Export PDF <i data-lucide="download" class="w-3.5 h-3.5"></i>
          </button>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse">
            <thead>
              <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] text-[10px] font-bold uppercase tracking-wider text-slate-400">
                <th class="px-6 py-4">Exam Name</th>
                <th class="px-6 py-4">Score</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4">Date</th>
                <th class="px-6 py-4 text-center">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-[#E2E8F0] text-sm text-[#334155]">
              
              @forelse($submissions as $sub)
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4 font-semibold text-slate-800 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1D4ED8] flex items-center justify-center font-bold text-xs">Σ</div>
                  {{ $sub->exam->title ?? 'Course Examination' }}
                </td>
                <td class="px-6 py-4 font-bold text-slate-800">
                  <div class="flex items-center gap-3 max-w-[150px]">
                    <span>{{ round($sub->total_score) }}/100</span>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden">
                      <div class="bg-[#1D4ED8] h-full" style="width: {{ $sub->percentage }}%"></div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase rounded bg-emerald-50 text-emerald-600 border border-emerald-100">PASSED</span>
                </td>
                <td class="px-6 py-4 text-slate-500 text-xs">{{ \Carbon\Carbon::parse($sub->submitted_at)->format('M d, Y') }}</td>
                <td class="px-6 py-4 text-center text-slate-400 hover:text-slate-600 cursor-pointer font-bold">•••</td>
              </tr>
              @empty
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4 font-semibold text-slate-800 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1D4ED8] flex items-center justify-center font-bold text-xs">Σ</div>
                  Calculus I
                </td>
                <td class="px-6 py-4 font-bold text-slate-800">
                  <div class="flex items-center gap-3 max-w-[150px]">
                    <span>94/100</span>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden"><div class="bg-[#1D4ED8] h-full w-[94%]"></div></div>
                  </div>
                </td>
                <td class="px-6 py-4"><span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-emerald-50 text-emerald-600">PASSED</span></td>
                <td class="px-6 py-4 text-slate-500 text-xs">Oct 12, 2026</td>
                <td class="px-6 py-4 text-center text-slate-400 cursor-pointer font-bold">•••</td>
              </tr>
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4 font-semibold text-slate-800 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1D4ED8] flex items-center justify-center"><i data-lucide="layout-grid" class="w-4 h-4"></i></div>
                  Linear Algebra
                </td>
                <td class="px-6 py-4 font-bold text-slate-800">
                  <div class="flex items-center gap-3 max-w-[150px]">
                    <span>88/100</span>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden"><div class="bg-[#1D4ED8] h-full w-[88%]"></div></div>
                  </div>
                </td>
                <td class="px-6 py-4"><span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-emerald-50 text-emerald-600">PASSED</span></td>
                <td class="px-6 py-4 text-slate-500 text-xs">Sep 28, 2026</td>
                <td class="px-6 py-4 text-center text-slate-400 cursor-pointer font-bold">•••</td>
              </tr>
              <tr class="hover:bg-slate-50/80 transition-colors">
                <td class="px-6 py-4 font-semibold text-slate-800 flex items-center gap-3">
                  <div class="w-8 h-8 rounded-lg bg-blue-50 text-[#1D4ED8] flex items-center justify-center"><i data-lucide="monitor" class="w-4 h-4"></i></div>
                  Intro to CS
                </td>
                <td class="px-6 py-4 font-bold text-slate-800">
                  <div class="flex items-center gap-3 max-w-[150px]">
                    <span>91/100</span>
                    <div class="w-full bg-slate-100 h-1.5 rounded-full overflow-hidden"><div class="bg-[#1D4ED8] h-full w-[91%]"></div></div>
                  </div>
                </td>
                <td class="px-6 py-4"><span class="px-2 py-0.5 text-[10px] font-extrabold rounded bg-emerald-50 text-emerald-600">PASSED</span></td>
                <td class="px-6 py-4 text-slate-500 text-xs">Sep 15, 2026</td>
                <td class="px-6 py-4 text-center text-slate-400 cursor-pointer font-bold">•••</td>
              </tr>
              @endforelse

            </tbody>
          </table>
        </div>

        <div class="p-4 bg-[#F8FAFC] border-t border-[#E2E8F0] flex items-center justify-between text-xs font-medium text-slate-400">
          <span>Showing 4 of 12 records</span>
          <div class="flex items-center gap-1">
            <button class="px-3 py-1.5 border border-slate-200 bg-white text-slate-600 rounded-lg shadow-sm hover:bg-slate-50">Prev</button>
            <button class="px-3 py-1.5 border border-slate-200 bg-white text-slate-600 rounded-lg shadow-sm hover:bg-slate-50">Next</button>
          </div>
        </div>
      </div>

    </div>
  </main>

  <div id="edit-modal" class="fixed inset-0 bg-slate-900/40 backdrop-blur-sm z-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl border border-slate-100 shadow-xl max-w-md w-full p-6 space-y-4">
      <h3 class="text-lg font-bold text-[#0F172A]">Edit Profile Data</h3>
      <form action="{{ route('student.profile.update') }}" method="POST" class="space-y-4">
        @csrf
        <div>
          <label class="block text-xs font-semibold text-slate-500 mb-1.5">Full Name</label>
          <input type="text" name="full_name" value="{{ $user->full_name }}" required class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:border-[#1D4ED8]">
        </div>
        <div class="flex justify-end gap-2 pt-2">
          <button type="button" onclick="document.getElementById('edit-modal').classList.add('hidden')" class="px-4 py-2 bg-slate-100 text-slate-600 text-xs font-semibold rounded-xl">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-[#1D4ED8] text-white text-xs font-semibold rounded-xl shadow-md">Save Changes</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    lucide.createIcons();
  </script>
</body>
</html>