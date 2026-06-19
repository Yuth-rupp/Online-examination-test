<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Student Dashboard</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght=400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    body { font-family: 'Inter', sans-serif; }
  </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex">

  <!-- Left Sidebar Navigation -->
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
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#1D4ED8] text-white font-medium text-sm transition-all">
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
        <a href="{{ route('student.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all">
          <i data-lucide="settings" class="w-5 h-5"></i>
          Settings
        </a>
      </nav>
    </div>

    <div class="p-4 border-t border-[#E2E8F0] flex flex-col gap-3">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-amber-400 overflow-hidden flex items-center justify-center border border-slate-200">
            <span class="text-sm font-bold text-amber-900">
              {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'JD' }}
            </span>
          </div>
          <div>
            <h4 class="text-sm font-semibold text-[#0F172A] truncate max-w-[120px]">
              {{ Auth::user()->full_name ?? 'Jane Doe' }}
            </h4>
            <p class="text-xs text-[#64748B]">ID: {{ Auth::user()->institutional_id ?? '88291' }}</p>
          </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" class="inline">
          @csrf
          <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors" title="Logout">
            <i data-lucide="log-out" class="w-4 h-4"></i>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- Main View Execution Deck -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">
    
    <header class="bg-white border-b border-[#E2E8F0] px-6 py-4 flex items-center justify-between sticky top-0 z-20">
      <div class="relative w-full max-w-md">
        <i data-lucide="search" class="w-5 h-5 text-[#94A3B8] absolute left-3 top-1/2 -translate-y-1/2"></i>
        <input type="text" placeholder="Search exams, courses, or guides..." class="w-full bg-[#F1F5F9] pl-11 pr-4 py-2 rounded-xl text-sm border-none focus:outline-none focus:ring-2 focus:ring-[#1D4ED8]/20 text-[#1E293B] placeholder-[#94A3B8]">
      </div>

      <div class="flex items-center gap-4">
        <button class="p-2 text-[#64748B] hover:bg-[#F1F5F9] rounded-xl relative">
          <i data-lucide="bell" class="w-5 h-5"></i>
          <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
        </button>
        <button class="p-2 text-[#64748B] hover:bg-[#F1F5F9] rounded-xl">
          <i data-lucide="contrast" class="w-5 h-5"></i>
        </button>
        <div class="flex items-center gap-2 pl-2 border-l border-[#E2E8F0]">
          <div class="w-8 h-8 rounded-full bg-amber-400 overflow-hidden flex items-center justify-center text-xs font-bold text-amber-900">
            {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'JD' }}
          </div>
          <span class="text-sm font-medium text-[#0F172A] hidden sm:inline">
            {{ Auth::user()->full_name ?? 'Jane Doe' }}
          </span>
        </div>
      </div>
    </header>

    <div class="p-6 space-y-8 flex-1 max-w-[1400px] w-full mx-auto">
      
      <!-- Top Balanced Metrics Parameters Cards Grid -->
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 flex items-center gap-5 shadow-sm">
          <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-[#1D4ED8] border border-blue-100">
            <i data-lucide="clipboard-list" class="w-7 h-7"></i>
          </div>
          <div>
            <p class="text-xs font-medium text-[#64748B] tracking-wide uppercase mb-0.5">Total Exams</p>
            <h3 class="text-3xl font-bold text-[#0F172A]">{{ $totalExams ?? 0 }}</h3>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 flex items-center gap-5 shadow-sm">
          <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 border border-emerald-100">
            <i data-lucide="check-circle-2" class="w-7 h-7"></i>
          </div>
          <div>
            <p class="text-xs font-medium text-[#64748B] tracking-wide uppercase mb-0.5">Completed</p>
            <h3 class="text-3xl font-bold text-[#0F172A]">{{ $completedExams ?? 0 }}</h3>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 flex items-center gap-5 shadow-sm">
          <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-100">
            <i data-lucide="star" class="w-7 h-7"></i>
          </div>
          <div>
            <p class="text-xs font-medium text-[#64748B] tracking-wide uppercase mb-0.5">Average Score</p>
            <h3 class="text-3xl font-bold text-[#1D4ED8]">{{ number_format($averageScore ?? 0, 0) }}%</h3>
          </div>
        </div>
      </section>

      <!-- Upcoming Scheduled / Active Access Grid Frame -->
      <section class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-bold text-[#0F172A]">Assessment Overview</h2>
          <a href="{{ route('student.exams') }}" class="text-sm font-semibold text-[#1D4ED8] hover:underline flex items-center gap-1">
            View All <i data-lucide="arrow-right" class="w-4 h-4"></i>
          </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          <!-- Loop Handling Over Dynamic Upcoming Collections -->
          @forelse($upcomingExams as $upcoming)
            <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm relative flex flex-col justify-between min-h-[250px]">
              <div class="absolute top-0 left-0 right-0 h-1.5 bg-[#2563EB]"></div>
              <div class="p-6 space-y-4">
                <div class="flex items-center justify-between">
                  <span class="px-2.5 py-1 bg-blue-50 text-xs font-bold text-[#2563EB] rounded-md tracking-wider">
                    {{ $upcoming->course->code ?? 'COURSE' }}
                  </span>
                  <span class="text-xs text-[#94A3B8] max-w-[120px] truncate">{{ $upcoming->course->title ?? 'Department' }}</span>
                </div>
                <h3 class="text-lg font-bold text-[#0F172A] line-clamp-1">{{ $upcoming->title }}</h3>
                <div class="space-y-2 text-sm text-[#64748B]">
                  <div class="flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4 text-[#94A3B8]"></i>
                    <span>{{ \Carbon\Carbon::parse($upcoming->start_time)->format('M d, Y') }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <i data-lucide="clock" class="w-4 h-4 text-[#94A3B8]"></i>
                    <span>{{ \Carbon\Carbon::parse($upcoming->start_time)->format('h:i A') }} ({{ $upcoming->duration }} mins)</span>
                  </div>
                </div>
              </div>
              <div class="p-6 pt-0 border-t border-slate-50 flex items-center justify-between mt-auto">
                <div class="flex items-center gap-2">
                  <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                  <span class="text-sm font-medium text-orange-600">Upcoming</span>
                </div>
                <button onclick="openDetailsModal('{{ $upcoming->title }}', '{{ $upcoming->course->code ?? 'GEN' }}', '{{ \Carbon\Carbon::parse($upcoming->start_time)->format('M d, Y') }}', '{{ $upcoming->duration }} mins', 'Please remain prepared to authenticate proctor validations 15 minutes ahead of deployment.')" class="px-4 py-2 bg-[#F1F5F9] text-[#1E293B] hover:bg-[#E2E8F0] text-sm font-medium rounded-xl transition-all">Details</button>
              </div>
            </div>
          @empty
            <!-- Fallback Static Placeholders If No Scheduled Records Populated -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm relative flex flex-col justify-between min-h-[250px] opacity-60">
              <div class="absolute top-0 left-0 right-0 h-1.5 bg-slate-300"></div>
              <div class="p-6 space-y-4">
                <h3 class="text-sm font-medium text-slate-400">No further scheduled assessments pending.</h3>
              </div>
            </div>
          @endforelse

          <!-- SINGLE-USE CLASSROOM ACCESS SECURITY ROOM CODE ENTRANCE BOX -->
          <div class="bg-gradient-to-br from-[#1D4ED8] to-[#2563EB] text-white rounded-2xl p-6 shadow-md flex flex-col justify-between min-h-[250px]">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="px-2.5 py-1 bg-white/20 text-xs font-bold text-white rounded-md tracking-wider">SECURE ENTRY</span>
                @if($liveExam)
                  <span class="flex items-center gap-1 text-xs text-emerald-300 font-semibold animate-pulse">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span> Live Session Open
                  </span>
                @else
                  <span class="text-xs text-white/70">Class Validation Lock</span>
                @endif
              </div>
              <h3 class="text-xl font-bold">Enter Session Code</h3>
              <p class="text-xs text-white/80 leading-relaxed">
                @if($liveExam)
                  Active Assessment Found: <strong>{{ $liveExam->title }}</strong>. Please provide the unique class token issued by your instructor to unlock initialization parameters.
                @else
                  Provide the custom single-use access code shared by your lecturer (e.g. DBMS-3841) to pass security verification gates.
                @endif
              </p>
            </div>
            
            <form action="{{ route('student.verifyCode') }}" method="POST" class="mt-4 space-y-2">
              @csrf
              @if(session('error'))
                <p class="text-xs text-rose-300 font-semibold bg-rose-950/40 p-2 rounded-lg border border-rose-900/30">
                  {{ session('error') }}
                </p>
              @endif
              @if(session('success'))
                <p class="text-xs text-emerald-200 font-semibold bg-emerald-950/40 p-2 rounded-lg border border-emerald-900/30">
                  {{ session('success') }}
                </p>
              @endif
              <div class="flex gap-2">
                <input type="text" name="access_code" placeholder="e.g., DBMS-4821" required 
                       class="flex-1 bg-white/10 border border-white/20 px-3 py-2 rounded-xl text-sm font-mono text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-white uppercase">
                <button type="submit" class="px-4 py-2 bg-white text-[#1D4ED8] hover:bg-slate-50 text-sm font-bold rounded-xl transition-all shadow-sm">
                  Verify
                </button>
              </div>
            </form>
          </div>

        </div>
      </section>

      <!-- Insights Layout Columns Base Deck -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm lg:col-span-2 flex flex-col justify-between">
          <div>
            <h3 class="text-base font-bold text-[#0F172A] mb-6">Performance Insights</h3>
          </div>
          
          <div class="w-full grid grid-cols-5 items-end gap-1 px-2 pt-4 relative h-36">
            <div class="absolute inset-x-0 bottom-0 top-0 flex flex-col justify-between pointer-events-none">
              <div class="w-full h-[70px] bg-blue-50/40 rounded-t-md"></div>
              <div class="w-full h-[66px] bg-transparent"></div>
            </div>

            <div class="relative flex flex-col items-center group z-10">
              <div class="w-full bg-[#3B82F6]/50 h-24 rounded-t-sm transition-all group-hover:bg-[#1D4ED8]"></div>
              <span class="text-[10px] font-bold tracking-wider text-[#94A3B8] mt-3 uppercase">Mon</span>
            </div>
            <div class="relative flex flex-col items-center group z-10">
              <div class="w-full bg-[#3B82F6]/40 h-16 rounded-t-sm transition-all group-hover:bg-[#1D4ED8]"></div>
              <span class="text-[10px] font-bold tracking-wider text-[#94A3B8] mt-3 uppercase">Tue</span>
            </div>
            <div class="relative flex flex-col items-center group z-10">
              <div class="w-full bg-[#1D4ED8] h-32 rounded-t-sm transition-all"></div>
              <span class="text-[10px] font-bold tracking-wider text-[#94A3B8] mt-3 uppercase">Wed</span>
            </div>
            <div class="relative flex flex-col items-center group z-10">
              <div class="w-full bg-[#3B82F6]/30 h-20 rounded-t-sm transition-all group-hover:bg-[#1D4ED8]"></div>
              <span class="text-[10px] font-bold tracking-wider text-[#94A3B8] mt-3 uppercase">Thu</span>
            </div>
            <div class="relative flex flex-col items-center group z-10">
              <div class="w-full bg-[#3B82F6]/50 h-24 rounded-t-sm transition-all group-hover:bg-[#1D4ED8]"></div>
              <span class="text-[10px] font-bold tracking-wider text-[#94A3B8] mt-3 uppercase">Fri</span>
            </div>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex flex-col">
          <h3 class="text-base font-bold text-[#0F172A] mb-4">Quick Links</h3>
          <div class="space-y-3 flex-1 flex flex-col justify-center">
            
            <a href="{{ route('student.printTicket') }}" target="_blank" class="w-full flex items-center justify-between p-3.5 border border-[#F1F5F9] hover:border-[#E2E8F0] rounded-xl hover:bg-[#F8FAFC] transition-all group text-left">
              <div class="flex items-center gap-3">
                <i data-lucide="download" class="w-5 h-5 text-[#2563EB]"></i>
                <span class="text-sm font-medium text-[#334155] group-hover:text-[#0F172A]">Download Hall Ticket</span>
              </div>
              <i data-lucide="chevron-right" class="w-4 h-4 text-[#94A3B8] group-hover:text-[#64748B]"></i>
            </a>

            <button onclick="openInfoModal('Examination Guidelines', 'All active browser instances must remain contained within a single window frame. Tab navigation switching, side panel interactions, or secondary monitors will generate automated system flags.')" class="w-full flex items-center justify-between p-3.5 border border-[#F1F5F9] hover:border-[#E2E8F0] rounded-xl hover:bg-[#F8FAFC] transition-all group text-left">
              <div class="flex items-center gap-3">
                <i data-lucide="file-text" class="w-5 h-5 text-[#2563EB]"></i>
                <span class="text-sm font-medium text-[#334155] group-hover:text-[#0F172A]">Examination Guidelines</span>
              </div>
              <i data-lucide="chevron-right" class="w-4 h-4 text-[#94A3B8] group-hover:text-[#64748B]"></i>
            </button>

            <button onclick="openInfoModal('Proctoring FAQ', 'Q: What happens if my network disconnects?<br>A: The application context switches automatically to offline buffer mode. Reconnect within 180 seconds to protect session integrity.')" class="w-full flex items-center justify-between p-3.5 border border-[#F1F5F9] hover:border-[#E2E8F0] rounded-xl hover:bg-[#F8FAFC] transition-all group text-left">
              <div class="flex items-center gap-3">
                <i data-lucide="help-circle" class="w-5 h-5 text-[#2563EB]"></i>
                <span class="text-sm font-medium text-[#334155] group-hover:text-[#0F172A]">Proctoring FAQ</span>
              </div>
              <i data-lucide="chevron-right" class="w-4 h-4 text-[#94A3B8] group-hover:text-[#64748B]"></i>
            </button>

          </div>
        </div>

      </section>
    </div>
  </main>

  <!-- Global Modal Overlay -->
  <div id="modalOverlay" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full overflow-hidden transform scale-95 opacity-0 transition-all duration-200" id="modalCard">
      <div class="p-6 border-b border-slate-100 flex items-center justify-between">
        <h3 id="modalTitle" class="text-lg font-bold text-[#0F172A]">Notification</h3>
        <button onclick="closeModal()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600">
          <i data-lucide="x" class="w-5 h-5"></i>
        </button>
      </div>
      <div class="p-6 space-y-3 font-medium" id="modalContent"></div>
      <div class="p-4 bg-slate-50 border-t border-slate-100 text-right">
        <button onclick="closeModal()" class="px-4 py-2 bg-[#1D4ED8] hover:bg-[#1e40af] text-white text-sm font-semibold rounded-xl shadow-sm transition">Dismiss</button>
      </div>
    </div>
  </div>

  <script>
    lucide.createIcons();

    const overlay = document.getElementById('modalOverlay');
    const card = document.getElementById('modalCard');
    const mTitle = document.getElementById('modalTitle');
    const mContent = document.getElementById('modalContent');

    function openInfoModal(title, text) {
        mTitle.innerText = title;
        mContent.innerHTML = `<p class="text-sm text-[#64748B] leading-relaxed">${text}</p>`;
        showModal();
    }

    function openDetailsModal(title, code, date, time, extra) {
        mTitle.innerText = `${title} (${code})`;
        mContent.innerHTML = `
            <div class="space-y-2 text-sm text-[#64748B]">
                <p><strong>Scheduled Date:</strong> ${date}</p>
                <p><strong>Window Frame:</strong> ${time}</p>
                <hr class="my-2 border-slate-100" />
                <p class="text-xs bg-amber-50 text-amber-700 p-3 rounded-lg border border-amber-100">${extra}</p>
            </div>
        `;
        showModal();
    }

    function showModal() {
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        setTimeout(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal() {
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            overlay.classList.remove('flex');
            overlay.classList.add('hidden');
        }, 150);
    }
  </script>
</body>
</html>