<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Admin Settings</title>
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
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="layout-dashboard" class="w-5 h-5"></i> Dashboard</a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="users" class="w-5 h-5"></i> User Management</a>
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="shield" class="w-5 h-5"></i> Security</a>
        <a href="{{ route('admin.backup') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-[#64748B] hover:bg-[#F1F5F9] hover:text-[#1E293B] font-medium text-sm transition-all"><i data-lucide="database" class="w-5 h-5"></i> Backup</a>
        <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#1D4ED8] text-white font-medium text-sm transition-all"><i data-lucide="settings" class="w-5 h-5"></i> Settings</a>
      </nav>
    </div>

    <div class="p-4 border-t border-[#E2E8F0]">
      <form action="{{ route('logout') }}" method="POST" id="logout-form" class="mb-2">
        @csrf
        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2.5 rounded-xl text-red-600 hover:bg-red-50 font-medium text-sm transition-all">
          <i data-lucide="log-out" class="w-4 h-4"></i>
          Sign Out
        </button>
      </form>

      <div class="flex items-center gap-3 pt-2 border-t border-slate-100">
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
      
      <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex flex-col sm:flex-row items-center gap-5">
          <div class="w-16 h-16 rounded-2xl bg-blue-50 text-[#1D4ED8] flex items-center justify-center">
            <i data-lucide="settings" class="w-8 h-8"></i>
          </div>
          <div class="text-center sm:text-left">
            <h2 class="text-xl font-bold text-[#0F172A]">System Configurations</h2>
            <p class="text-xs text-[#64748B] mt-0.5">Manage parameters, automated tasks, and examination boundaries.</p>
          </div>
        </div>
      </div>

      <form action="#" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @csrf
        
        <div class="md:col-span-2 space-y-6">
          <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-[#E2E8F0] bg-slate-50/50 flex items-center gap-2">
              <i data-lucide="shield-alert" class="w-4 h-4 text-orange-500"></i>
              <h3 class="text-sm font-bold text-[#0F172A]">Live Proctoring & Integrity Rules</h3>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-[#64748B] mb-2">Max Allowed Tab Switches</label>
                  <input type="number" name="proctor_max_switches" class="w-full px-4 py-2.5 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] text-sm focus:outline-none focus:border-[#1D4ED8]" value="3">
                </div>
                <div>
                  <label class="block text-xs font-semibold text-[#64748B] mb-2">Auto-Lock Warning Threshold</label>
                  <input type="number" name="proctor_warn_threshold" class="w-full px-4 py-2.5 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] text-sm focus:outline-none focus:border-[#1D4ED8]" value="2">
                </div>
              </div>
              <div class="pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox" name="block_right_click" class="rounded border-[#E2E8F0] text-[#1D4ED8] w-4 h-4" checked>
                  <span class="text-xs font-medium text-[#334155]">Enforce Right-Click & Copy-Paste Prevention during exams</span>
                </label>
              </div>
            </div>
          </div>

          <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-[#E2E8F0] bg-slate-50/50 flex items-center gap-2">
              <i data-lucide="sliders" class="w-4 h-4 text-[#1D4ED8]"></i>
              <h3 class="text-sm font-bold text-[#0F172A]">Global Examination Parameters</h3>
            </div>
            <div class="p-6 space-y-4">
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                  <label class="block text-xs font-semibold text-[#64748B] mb-2">Browser Window Sync Interval</label>
                  <select name="sync_interval" class="w-full px-4 py-2.5 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] text-sm focus:outline-none focus:border-[#1D4ED8]">
                    <option value="5">Every 5 Seconds</option>
                    <option value="10" selected>Every 10 Seconds</option>
                    <option value="30">Every 30 Seconds</option>
                  </select>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-[#64748B] mb-2">Default Passing Score Cutoff (%)</label>
                  <input type="number" name="passing_rate" class="w-full px-4 py-2.5 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] text-sm focus:outline-none focus:border-[#1D4ED8]" value="50">
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
            <div class="p-5 border-b border-[#E2E8F0] bg-slate-50/50 flex items-center gap-2">
              <i data-lucide="hard-drive" class="w-4 h-4 text-slate-500"></i>
              <h3 class="text-sm font-bold text-[#0F172A]">Maintenance Actions</h3>
            </div>
            <div class="p-6 space-y-4">
              <div>
                <label class="block text-xs font-semibold text-[#64748B] mb-2">Audit Log Retention Policy</label>
                <select name="log_retention" class="w-full px-4 py-2.5 border border-[#E2E8F0] rounded-xl bg-[#F8FAFC] text-sm focus:outline-none focus:border-[#1D4ED8]">
                  <option value="30">Purge logs after 30 Days</option>
                  <option value="90" selected>Purge logs after 90 Days</option>
                  <option value="365">Purge logs after 1 Year</option>
                </select>
              </div>
              <div class="border-t border-[#E2E8F0] pt-4 flex flex-col gap-2">
                <button type="button" class="w-full px-4 py-2 text-xs font-semibold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition">Clear Database Cache</button>
                <button type="button" class="w-full px-4 py-2 text-xs font-semibold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition">Flush Proctoring Queue</button>
              </div>
            </div>
          </div>

          <div class="bg-white border border-[#E2E8F0] rounded-2xl p-4 shadow-sm flex flex-col gap-2">
            <button type="submit" class="w-full py-3 bg-[#1D4ED8] text-white font-bold text-sm rounded-xl shadow-sm hover:bg-blue-700 transition">Save System Rules</button>
            <a href="{{ route('admin.dashboard') }}" class="w-full py-3 bg-[#F1F5F9] text-[#1E293B] font-medium text-center text-sm rounded-xl transition hover:bg-[#E2E8F0]">Cancel Changes</a>
          </div>
        </div>
      </form>

    </div>
  </main>

  <script>lucide.createIcons();</script>
</body>
</html>