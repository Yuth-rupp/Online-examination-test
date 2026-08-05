<div class="flex min-h-screen bg-[#F8FAFC] text-[#1E293B] w-full" wire:poll.1s x-data="{ showNotifications: false, showTheme: false }">

  <!-- Left Sidebar Navigation -->
  <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between fixed h-full z-10 hidden md:flex">
    <div>
      <div class="p-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-md shadow-blue-100">
          <i data-lucide="graduation-cap" class="w-6 h-6"></i>
        </div>
        <div>
          <h1 class="font-bold text-base text-[#0F172A] leading-tight">{{ $platformName }}</h1>
          <p class="text-xs text-[#64748B]">Student Portal</p>
        </div>
      </div>

      <nav class="px-4 space-y-1">
        <a href="{{ route('student.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('student.dashboard') ? 'bg-[#1D4ED8] text-white font-semibold' : 'text-[#64748B] hover:bg-[#F1F5F9]' }} text-sm transition-all">
          <i data-lucide="layout-dashboard" class="w-5 h-5"></i>
          Dashboard
        </a>
        <a href="{{ route('student.exams') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('student.exams') ? 'bg-[#1D4ED8] text-white font-semibold' : 'text-[#64748B] hover:bg-[#F1F5F9]' }} text-sm transition-all">
          <i data-lucide="book-open" class="w-5 h-5"></i>
          Exams
        </a>
        <a href="{{ route('student.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('student.history') ? 'bg-[#1D4ED8] text-white font-semibold' : 'text-[#64748B] hover:bg-[#F1F5F9]' }} text-sm transition-all">
          <i data-lucide="history" class="w-5 h-5"></i>
          History
        </a>
        <a href="{{ route('student.support') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('student.support') ? 'bg-[#1D4ED8] text-white font-semibold' : 'text-[#64748B] hover:bg-[#F1F5F9]' }} text-sm transition-all">
          <i data-lucide="help-circle" class="w-5 h-5"></i>
          Support
        </a>
        <a href="{{ route('student.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('student.settings') ? 'bg-[#1D4ED8] text-white font-semibold' : 'text-[#64748B] hover:bg-[#F1F5F9]' }} text-sm transition-all">
          <i data-lucide="settings" class="w-5 h-5"></i>
          Settings
        </a>
      </nav>
    </div>

    <div class="p-4 border-t border-[#E2E8F0] flex flex-col gap-3 bg-slate-50/50">
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-full bg-amber-400 overflow-hidden flex items-center justify-center border border-slate-200 shadow-sm">
            <span class="text-sm font-bold text-amber-900">
              {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'ST' }}
            </span>
          </div>
          <div class="overflow-hidden">
            <h4 class="text-sm font-semibold text-[#0F172A] truncate max-w-[120px]">
              {{ Auth::user()->full_name ?? 'Student' }}
            </h4>
            <p class="text-xs text-[#64748B] truncate">ID: {{ Auth::user()->user_id ?? Auth::user()->institutional_id ?? '2026-0000' }}</p>
          </div>
        </div>
        
        <form action="{{ route('logout') }}" method="POST" class="inline m-0">
          @csrf
          <button type="submit" class="p-1.5 text-slate-400 hover:text-red-500 rounded-lg hover:bg-red-50 transition-colors cursor-pointer" title="Logout">
            <i data-lucide="log-out" class="w-4 h-4"></i>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- Main Workstation Area -->
  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-[#E2E8F0] px-6 py-4 flex items-center justify-between sticky top-0 z-20">
      <div class="relative w-full max-w-md">
        <i data-lucide="search" class="w-5 h-5 text-[#94A3B8] absolute left-3 top-1/2 -translate-y-1/2"></i>
        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search exams, courses, or guides..." class="w-full bg-[#F1F5F9] pl-11 pr-4 py-2 rounded-xl text-sm border-none focus:outline-none focus:ring-2 focus:ring-[#1D4ED8]/20 text-[#1E293B] placeholder-[#94A3B8]">
      </div>

      <div class="flex items-center gap-4 relative">
        <div class="relative">
          <button @click="showNotifications = !showNotifications; showTheme = false" class="p-2 text-[#64748B] hover:bg-[#F1F5F9] rounded-xl relative cursor-pointer">
            <i data-lucide="bell" class="w-5 h-5"></i>
            <span class="absolute top-2 right-2 w-1.5 h-1.5 bg-orange-500 rounded-full"></span>
          </button>
          
          <div x-show="showNotifications" @click.outside="showNotifications = false" style="display: none;" class="absolute right-0 mt-2 w-80 bg-white border border-[#E2E8F0] rounded-2xl shadow-xl p-4 z-30">
            <div class="flex items-center justify-between border-b pb-2 mb-2 border-slate-100">
              <h4 class="text-xs font-bold uppercase text-[#0F172A] tracking-wider">Live Logs</h4>
              <span class="text-[10px] bg-blue-50 text-blue-600 font-bold px-2 py-0.5 rounded-full">Active</span>
            </div>
            <div class="text-xs text-slate-600 space-y-2">
              <div class="p-2 hover:bg-slate-50 rounded-lg border border-transparent hover:border-slate-100 transition-all">
                <p class="font-semibold text-slate-800">Advanced Algorithms Open</p>
                <p class="text-slate-400 mt-0.5">The synchronized live examination container is ready.</p>
              </div>
            </div>
          </div>
        </div>

        <div class="relative">
          <button @click="showTheme = !showTheme; showNotifications = false" class="p-2 text-[#64748B] hover:bg-[#F1F5F9] rounded-xl cursor-pointer">
            <i data-lucide="contrast" class="w-5 h-5"></i>
          </button>

          <div x-show="showTheme" @click.outside="showTheme = false" style="display: none;" class="absolute right-0 mt-2 w-40 bg-white border border-[#E2E8F0] rounded-xl shadow-xl p-2 z-30 text-xs">
            <button class="w-full text-left p-2 hover:bg-slate-50 rounded-lg text-slate-700 font-medium flex items-center gap-2"><i data-lucide="sun" class="w-4 h-4"></i> Light Theme</button>
            <button class="w-full text-left p-2 hover:bg-blue-50 rounded-lg text-blue-600 font-bold flex items-center gap-2"><i data-lucide="moon" class="w-4 h-4"></i> Dark Workspace</button>
          </div>
        </div>

        <div class="flex items-center gap-2 pl-2 border-l border-[#E2E8F0]">
          <div class="w-8 h-8 rounded-full bg-amber-400 overflow-hidden flex items-center justify-center text-xs font-bold text-amber-900 shadow-sm">
            {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'ST' }}
          </div>
          <span class="text-sm font-medium text-[#0F172A] hidden sm:inline">
            {{ Auth::user()->full_name ?? 'Student' }}
          </span>
        </div>
      </div>
    </header>

    <div class="p-6 space-y-8 flex-1 max-w-[1400px] w-full mx-auto">
      
      <!-- ASSESSMENT OVERVIEW TRACK -->
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 flex items-center gap-5 shadow-sm">
          <div class="w-14 h-14 bg-blue-50 rounded-2xl flex items-center justify-center text-[#1D4ED8] border border-blue-100 shadow-sm">
            <i data-lucide="clipboard-list" class="w-7 h-7"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-[#64748B] tracking-wide uppercase mb-0.5">Total Exams</p>
            <h3 class="text-3xl font-black text-[#0F172A] tracking-tight">{{ $totalExamsCount }}</h3>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 flex items-center gap-5 shadow-sm">
          <div class="w-14 h-14 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600 border border-emerald-100 shadow-sm">
            <i data-lucide="check-circle-2" class="w-7 h-7"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-[#64748B] tracking-wide uppercase mb-0.5">Completed Track</p>
            <h3 class="text-3xl font-black text-emerald-600 tracking-tight">{{ $completedExamsCount }}</h3>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 flex items-center gap-5 shadow-sm">
          <div class="w-14 h-14 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-500 border border-amber-100 shadow-sm">
            <i data-lucide="star" class="w-7 h-7"></i>
          </div>
          <div>
            <p class="text-xs font-bold text-[#64748B] tracking-wide uppercase mb-0.5">Average Grade</p>
            <h3 class="text-3xl font-black text-[#1D4ED8] tracking-tight">{{ round($averageScorePercent, 1) }}%</h3>
          </div>
        </div>
      </section>

      <!-- SECTIONS CARDS LIST -->
      <section class="space-y-4">
        <div class="flex items-center justify-between">
          <h2 class="text-lg font-black text-[#0F172A] tracking-tight">Active Folders Layout</h2>
          <div class="bg-slate-200/70 p-1 rounded-xl flex items-center gap-1 text-xs">
            <button wire:click="changeTab('upcoming')" class="px-4 py-2 font-bold rounded-lg transition {{ $activeTab === 'upcoming' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Upcoming</button>
            <button wire:click="changeTab('ongoing')" class="px-4 py-2 font-bold rounded-lg transition {{ $activeTab === 'ongoing' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Ongoing</button>
            <button wire:click="changeTab('completed')" class="px-4 py-2 font-bold rounded-lg transition {{ $activeTab === 'completed' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">Completed</button>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          
          @forelse($exams as $item)
            @if($activeTab === 'completed')
              <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm flex flex-col justify-between min-h-[250px] p-6 hover:shadow-md transition-all">
                <div>
                  <div class="flex items-center justify-between mb-4">
                    <span class="px-2.5 py-1 bg-slate-100 text-xs font-bold text-slate-600 rounded-md">COMPLETED</span>
                  </div>
                  <h3 class="text-lg font-bold text-[#0F172A] line-clamp-1">{{ $item->exam->title ?? 'N/A' }}</h3>
                  <p class="text-xs font-medium text-slate-400 mt-0.5">{{ $item->exam->course->course_name ?? 'General Assignment' }}</p>
                </div>
                <div class="border-t border-slate-50 pt-4 flex items-center justify-between mt-auto">
                  <div class="text-xs text-slate-400">
                    <i data-lucide="calendar" class="w-4 h-4 inline mr-1 text-slate-300"></i> {{ \Carbon\Carbon::parse($item->created_at)->format('M d, Y') }}
                  </div>
                  <div class="text-right">
                    <span class="block text-[9px] font-bold text-slate-400 uppercase tracking-widest">Final Score</span>
                    <span class="text-xl font-black text-slate-800">{{ round($item->percentage) }}<span class="text-xs text-slate-400 font-normal">/100</span></span>
                  </div>
                </div>
              </div>
            @else
              @php $isOngoing = \Carbon\Carbon::parse($item->start_time)->isPast() && \Carbon\Carbon::parse($item->end_time)->isFuture(); @endphp
              <div class="bg-white border {{ $isOngoing ? 'border-emerald-200 ring-1 ring-emerald-100' : 'border-[#E2E8F0]' }} rounded-2xl overflow-hidden shadow-sm relative flex flex-col justify-between min-h-[250px] p-6 hover:shadow-md transition-all">
                @if($isOngoing) <div class="absolute top-0 left-0 right-0 h-1.5 bg-emerald-500"></div> @endif
                <div class="space-y-4">
                  <div class="flex items-center justify-between">
                    <span class="px-2.5 py-1 {{ $isOngoing ? 'bg-emerald-50 text-emerald-700' : 'bg-blue-50 text-[#2563EB]' }} text-xs font-bold rounded-md tracking-wider">
                      {{ $item->course->code ?? 'GEN' }}
                    </span>
                    @if($isOngoing)
                      <span class="text-xs font-bold text-emerald-600 flex items-center gap-1 animate-pulse"><span class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></span> LIVE</span>
                    @else
                      <span class="text-xs text-[#94A3B8] truncate max-w-[120px]">{{ $item->course->title ?? 'Department' }}</span>
                    @endif
                  </div>
                  <h3 class="text-lg font-bold text-[#0F172A] line-clamp-1">{{ $item->title }}</h3>
                  <div class="space-y-2 text-sm text-[#64748B]">
                    <div class="flex items-center gap-2"><i data-lucide="calendar" class="w-4 h-4 text-[#94A3B8]"></i><span>{{ \Carbon\Carbon::parse($item->start_time)->format('M d, Y') }}</span></div>
                    <div class="flex items-center gap-2"><i data-lucide="clock" class="w-4 h-4 text-[#94A3B8]"></i><span>{{ \Carbon\Carbon::parse($item->start_time)->format('h:i A') }} ({{ $item->duration }} mins)</span></div>
                  </div>
                </div>
                <div class="pt-4 border-t border-slate-50 flex items-center justify-between mt-auto">
                  <span class="text-xs font-bold {{ $isOngoing ? 'text-emerald-600' : 'text-orange-500' }}">{{ $isOngoing ? 'Active Now' : 'Upcoming' }}</span>
                  @if($isOngoing)
                    <a href="{{ route('exams.enter', $item->exam_id) }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl shadow-md transition-all">Enter Exam</a>
                  @else
                    <button wire:click="viewExamDetails({{ $item->exam_id }})" class="px-4 py-2 bg-[#F1F5F9] text-[#1E293B] hover:bg-[#E2E8F0] text-sm font-medium rounded-xl transition-all cursor-pointer">Details</button>
                  @endif
                </div>
              </div>
            @endif
          @empty
            <div class="col-span-1 md:col-span-2 lg:col-span-3 bg-white border border-dashed border-slate-200 rounded-2xl overflow-hidden shadow-sm flex flex-col items-center justify-center min-h-[250px] p-6 text-center">
              <div class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 mb-2"><i data-lucide="inbox" class="w-5 h-5"></i></div>
              <h4 class="text-sm font-bold text-slate-700">Folder Empty</h4>
              <p class="text-xs text-slate-400 max-w-[180px] mt-0.5">No examinations found matching this folder filter.</p>
            </div>
          @endforelse

          <div class="bg-gradient-to-br from-[#1D4ED8] to-[#2563EB] text-white rounded-2xl p-6 shadow-md flex flex-col justify-between min-h-[250px]">
            <div class="space-y-4">
              <div class="flex items-center justify-between">
                <span class="px-2.5 py-1 bg-white/20 text-xs font-bold text-white rounded-md tracking-wider">SECURE CHANNELS</span>
                <span class="text-xs text-white/70 font-mono">ID LOCK</span>
              </div>
              <h3 class="text-xl font-bold">Class Token Entrance</h3>
              <p class="text-xs text-white/80 leading-relaxed">Provide the verification classroom code token shared by your lecturer framework to bypass deployment proctor checkpoints.</p>
            </div>
            
            <form action="{{ route('student.verifyCode') }}" method="POST" class="mt-4 space-y-2">
              @csrf
              <div class="flex gap-2">
                <input type="text" name="access_code" placeholder="e.g., DBMS-4821" required class="flex-1 bg-white/10 border border-white/20 px-3 py-2 rounded-xl text-sm font-mono text-white placeholder-white/40 focus:outline-none focus:ring-2 focus:ring-white uppercase">
                <button type="submit" class="px-4 py-2 bg-white text-[#1D4ED8] hover:bg-slate-50 text-sm font-bold rounded-xl transition-all shadow-sm cursor-pointer">Verify</button>
              </div>
            </form>
          </div>

        </div>
      </section>

      <!-- Bottom Layout Section -->
      <section class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm lg:col-span-2 flex flex-col justify-between">
          <h3 class="text-base font-bold text-[#0F172A] mb-4">Performance Architecture Insights</h3>
          <div class="w-full grid grid-cols-5 items-end gap-2 px-2 h-32 relative">
            <div class="absolute inset-x-0 bottom-0 top-0 flex flex-col justify-between pointer-events-none"><div class="w-full h-1/2 bg-blue-50/20 border-b border-dashed border-blue-100"></div></div>
            <div class="relative flex flex-col items-center group z-10 w-full"><div class="w-full bg-blue-500/40 h-24 rounded-t-md"></div><span class="text-[9px] font-bold text-[#94A3B8] mt-2">MON</span></div>
            <div class="relative flex flex-col items-center group z-10 w-full"><div class="w-full bg-blue-500/30 h-16 rounded-t-md"></div><span class="text-[9px] font-bold text-[#94A3B8] mt-2">TUE</span></div>
            <div class="relative flex flex-col items-center group z-10 w-full"><div class="w-full bg-[#1D4ED8] h-28 rounded-t-md"></div><span class="text-[9px] font-bold text-[#94A3B8] mt-2">WED</span></div>
            <div class="relative flex flex-col items-center group z-10 w-full"><div class="w-full bg-blue-500/20 h-20 rounded-t-md"></div><span class="text-[9px] font-bold text-[#94A3B8] mt-2">THU</span></div>
            <div class="relative flex flex-col items-center group z-10 w-full"><div class="w-full bg-blue-500/50 h-24 rounded-t-md"></div><span class="text-[9px] font-bold text-[#94A3B8] mt-2">FRI</span></div>
          </div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex flex-col">
          <h3 class="text-base font-bold text-[#0F172A] mb-4">System Verification Anchors</h3>
          <div class="space-y-3 flex-1 flex flex-col justify-center">
            <a href="{{ route('student.printTicket') }}" target="_blank" class="w-full flex items-center justify-between p-3.5 border border-[#F1F5F9] hover:border-[#E2E8F0] rounded-xl hover:bg-[#F8FAFC] transition-all group text-left shadow-sm">
              <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-all"><i data-lucide="file-text" class="w-4 h-4"></i></div>
                <div>
                  <span class="text-xs font-bold text-[#334155] group-hover:text-[#0F172A] block">Hall Entry Voucher</span>
                  <span class="text-[10px] text-slate-400 block mt-0.5">Generate verified print document</span>
                </div>
              </div>
              <i data-lucide="chevron-right" class="w-4 h-4 text-[#94A3B8]"></i>
            </a>
          </div>
        </div>
      </section>
    </div>
  </main>

  <!-- POPUP DETAILS MODAL -->
  @if($showModal && $selectedExamDetails)
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
      <div class="bg-white rounded-2xl shadow-xl border border-slate-100 max-w-md w-full overflow-hidden transform transition-all">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-base font-bold text-[#0F172A]">Assessment Summary</h3>
          <button wire:click="closeModal()" class="p-1.5 rounded-lg hover:bg-slate-100 text-slate-400 hover:text-slate-600 cursor-pointer"><i data-lucide="x" class="w-5 h-5"></i></button>
        </div>
        <div class="p-6 space-y-4 text-xs font-medium text-slate-600">
          <div class="bg-slate-50 p-4 rounded-xl space-y-2 border border-slate-100">
            <p class="text-sm font-bold text-slate-900 mb-1">{{ $selectedExamDetails->title }}</p>
            <p><strong>Course Target:</strong> {{ $selectedExamDetails->course->course_name ?? 'N/A' }}</p>
            <p><strong>System Token:</strong> <span class="font-mono bg-white border px-1.5 py-0.5 rounded text-blue-600 uppercase font-bold">{{ $selectedExamDetails->access_code ?? 'None' }}</span></p>
            <p><strong>Deployment Clock:</strong> {{ \Carbon\Carbon::parse($selectedExamDetails->start_time)->format('M d, Y @ h:i A') }}</p>
            <p><strong>Allotted Frame:</strong> {{ $selectedExamDetails->duration }} Minutes</p>
          </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 text-right"><button wire:click="closeModal()" class="px-4 py-2 bg-[#1D4ED8] hover:bg-[#1e40af] text-white text-xs font-bold rounded-xl shadow-sm transition cursor-pointer">Dismiss</button></div>
      </div>
    </div>
  @endif

</div>