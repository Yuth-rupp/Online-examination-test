<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Backup Panel</title>
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
        <a href="{{ route('admin.backup') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl bg-[#1D4ED8] text-white font-medium text-sm transition-all"><i data-lucide="database" class="w-5 h-5"></i> Backup</a>
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
      <span class="text-sm font-medium text-slate-500">Infrastructure Infrastructure / System Backup Panel</span>
      <div class="w-8 h-8 rounded-full bg-orange-500 flex items-center justify-center text-white font-semibold text-xs">AU</div>
    </header>

    <div class="p-6 space-y-6 flex-1 max-w-[1400px] w-full mx-auto">
      
      @if(session('success'))
        <div class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-xl text-xs font-semibold shadow-sm flex items-center gap-2">
          <i data-lucide="check-circle" class="w-4 h-4 text-emerald-600"></i> {{ session('success') }}
        </div>
      @endif

      <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm space-y-4 md:col-span-2">
          <h3 class="text-xs font-bold text-[#0F172A] border-b pb-2 uppercase tracking-wider text-slate-400">Automated CRON Strategy</h3>
          <form class="space-y-4 text-xs">
            <div class="flex items-center gap-3">
              <input type="checkbox" id="auto-backup" @checked($autoBackupEnabled) class="w-4 h-4 text-[#1D4ED8] rounded cursor-pointer">
              <label for="auto-backup" class="font-medium text-slate-700 cursor-pointer">Enable Automated Rolling SQL Snapshot Backups</label>
            </div>
            <div class="flex items-center gap-4">
              <span class="font-semibold text-slate-500 uppercase">Snapshot Interval:</span>
              <select class="px-3 py-1.5 bg-[#F1F5F9] border rounded-xl text-xs font-medium cursor-pointer text-slate-700">
                <option value="daily" @selected($backupFrequency == 'daily')>Daily Execution (Midnight server time)</option>
                <option value="weekly" @selected($backupFrequency == 'weekly')>Weekly Interval Rotation</option>
              </select>
            </div>
            <button type="button" class="px-4 py-2 bg-slate-700 hover:bg-slate-800 text-white font-semibold rounded-xl transition-all">Save Routine Configuration</button>
          </form>
        </div>

        <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm flex flex-col justify-between">
          <div class="space-y-1">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Manual Core Backup</h3>
            <p class="text-xs text-slate-500 leading-relaxed">Instantly force a full database state dump and export compressed files to local storage arrays.</p>
          </div>
          <form action="{{ route('admin.backup.generate') }}" method="POST" class="pt-4">
            @csrf
            <button type="submit" class="w-full py-2.5 bg-[#1D4ED8] hover:bg-blue-700 text-white font-semibold text-xs rounded-xl shadow-md transition-all flex items-center justify-center gap-1.5 transform active:scale-98">
              <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i> Take Database Snapshot Now
            </button>
          </form>
        </div>

      </section>

      <section class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
        <div class="p-5 border-b border-[#E2E8F0]"><h3 class="text-sm font-bold text-[#0F172A]">Storage File System Manifest</h3></div>
        <table class="w-full text-left border-collapse text-xs">
          <thead>
            <tr class="bg-[#F8FAFC] border-b border-[#E2E8F0] font-semibold text-slate-400">
              <th class="px-6 py-4">Generation Date</th>
              <th class="px-6 py-4">File Name Identifier</th>
              <th class="px-6 py-4">Disk Capacity Size</th>
              <th class="px-6 py-4">Status</th>
              <th class="px-6 py-4 text-center">Server Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-[#E2E8F0] font-medium text-[#334155]">
            @forelse($backups as $b)
            <tr class="hover:bg-slate-50/50 transition-colors">
              <td class="px-6 py-4 text-slate-500">{{ $b['date'] }}</td>
              <td class="px-6 py-4 font-mono text-[#1D4ED8] font-bold">{{ $b['file'] }}</td>
              <td class="px-6 py-4 text-slate-500">{{ $b['size'] }}</td>
              <td class="px-6 py-4 font-bold text-emerald-600">Active Storage</td>
              <td class="px-6 py-4 text-center flex items-center justify-center gap-1">
                <button class="px-3 py-1 bg-slate-100 border text-slate-700 text-[11px] font-bold rounded-lg hover:bg-slate-200">Download</button>
                <button class="px-3 py-1 bg-red-50 text-red-600 border border-red-100 text-[11px] font-bold rounded-lg hover:bg-red-100">Wipe</button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="5" class="p-8 text-center text-slate-400 font-medium">
                No physical zip backup packages generated inside local filesystem arrays. Hit the blue button above to force snapshot creation!
              </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </section>

    </div>
  </main>

  <script>lucide.createIcons();</script>
</body>
</html>