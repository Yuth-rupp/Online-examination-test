<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Admin Console</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex">

  <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between fixed h-full z-10 hidden md:flex">
    <div>
      <div class="p-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white"><i data-lucide="graduation-cap" class="w-6 h-6"></i></div>
        <div>
          <h1 class="font-bold text-base text-[#0F172A] leading-tight">ExamSystem</h1>
          <p class="text-xs text-[#64748B]">Admin Console</p>
        </div>
      </div>

      <nav class="px-4 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#1D4ED8] text-white font-medium text-sm transition-all"><i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard</a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="users" class="w-5 h-5"></i> User Management</a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="shield" class="w-5 h-5"></i> Security</a>
        <a href="{{ route('admin.backup') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="database" class="w-5 h-5"></i> Backup</a>
        <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="settings" class="w-5 h-5"></i> Settings</a>
      </nav>
    </div>

    <div class="p-4 border-t border-[#E2E8F0]">
      <div class="flex items-center gap-3 pt-2">
        <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-600 border border-orange-200"><i data-lucide="user" class="w-5 h-5"></i></div>
        <div>
          <h4 class="text-sm font-semibold text-[#0F172A]">Admin User</h4>
          <p class="text-xs text-[#64748B]">Super Administrator</p>
        </div>
      </div>
    </div>
  </aside>

  <main class="flex-1 md:pl-64 min-h-screen flex flex-col">
    <header class="bg-white border-b border-[#E2E8F0] px-6 py-4 flex items-center justify-between sticky top-0 z-20">
      <div class="flex items-center gap-2 text-sm font-medium">
        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
        <span class="text-slate-500">System Monitoring Status: <span class="text-emerald-600 font-semibold">Active</span></span>
      </div>
      <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-semibold text-xs">AU</div>
    </header>

    <div class="p-6 space-y-6 flex-1 max-w-[1400px] w-full mx-auto">
      
      <section class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-[#64748B]">Total Registered Accounts</p>
            <h3 class="text-3xl font-bold text-[#0F172A]">{{ $totalUsers }}</h3>
            <span class="text-xs text-slate-400">Database Active Records</span>
          </div>
          <div class="w-12 h-12 bg-blue-50 text-[#1D4ED8] rounded-xl flex items-center justify-center"><i data-lucide="user" class="w-5 h-5"></i></div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-[#64748B]">Concurrent Running Exams</p>
            <h3 class="text-3xl font-bold text-[#0F172A]">{{ $activeExams }}</h3>
            <span class="text-xs text-emerald-600 font-medium">Live examination halls active</span>
          </div>
          <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center"><i data-lucide="activity" class="w-5 h-5"></i></div>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex items-center justify-between">
          <div>
            <p class="text-xs font-semibold text-[#64748B]">Core Server CPU Load</p>
            <h3 class="text-3xl font-bold text-[#1D4ED8]">{{ $cpuUsage }}%</h3>
            <span class="text-xs text-blue-600 font-medium">Optimal AWS Node Performance</span>
          </div>
          <div class="w-12 h-12 bg-blue-50 text-[#1D4ED8] rounded-xl flex items-center justify-center"><i data-lucide="cpu" class="w-5 h-5"></i></div>
        </div>
      </section>

      <section class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden border-l-4 border-l-orange-500">
        <div class="p-5 border-b border-[#E2E8F0]">
          <h3 class="text-sm font-bold text-[#0F172A] flex items-center gap-2">
            <i data-lucide="shield-alert" class="w-4 h-4 text-orange-500"></i> Live Proctoring Flag Monitor
          </h3>
          <p class="text-xs text-slate-400">Detecting window/tab switching events instantly across active student test groups.</p>
        </div>
        <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-4">
          @forelse($proctorFlags as $flag)
            <div class="p-3 bg-orange-50/50 border border-orange-100 rounded-xl flex items-center justify-between">
              <div>
                <span class="text-xs font-bold text-slate-800">{{ $flag['student'] }}</span>
                <p class="text-[11px] text-slate-500">Exam: {{ $flag['exam'] }}</p>
              </div>
              <div class="text-right">
                <span class="px-2 py-0.5 bg-orange-500 text-white font-black text-[10px] rounded">{{ $flag['violations'] }} Tabs Switched</span>
                <p class="text-[10px] text-slate-400 mt-1">{{ $flag['time'] }}</p>
              </div>
            </div>
          @empty
            <div class="p-4 text-center text-xs text-slate-400 col-span-2">No active examination integrity violations detected.</div>
          @endif
        </div>
      </section>

      <section class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#E2E8F0]">
          <h3 class="text-sm font-bold text-[#0F172A]">Core System Audit Trail</h3>
        </div>
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] font-semibold text-slate-400">
              <th class="px-6 py-3.5">Action Executed</th>
              <th class="px-6 py-3.5">Executor Profile</th>
              <th class="px-6 py-3.5">IP Tracker Address</th>
              <th class="px-6 py-3.5">System Reference Class</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E2E8F0] font-medium text-[#334155]">
            @forelse($systemLogs as $log)
            <tr class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-4 font-mono text-[#1D4ED8] font-bold">{{ $log->action }}</td>
              <td class="px-6 py-4 font-semibold text-slate-700">{{ $log->full_name ?? 'Anonymous User' }}</td>
              <td class="px-6 py-4 font-mono text-slate-400">{{ $log->ip_address }}</td>
              <td class="px-6 py-4 text-slate-400 text-[11px]">{{ $log->model_type }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="p-6 text-center text-slate-400">No operations executed in audit log stream yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </section>

    </div>
  </main>

  <script>lucide.createIcons();</script>
</body>
</html>