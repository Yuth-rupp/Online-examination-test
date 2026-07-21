<!-- ═══════════════════════════════════════════════════
       ADMIN SIDEBAR
       Categorized like the student portal (section labels +
       grouped links) but in the admin's professional blue palette.
  ════════════════════════════════════════════════════ -->
  <aside class="w-64 flex flex-col fixed h-full z-30 hidden md:flex border-r transition-colors duration-300"
         :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'">

    <!-- Logo -->
    <div class="px-5 pt-6 pb-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 admin-brand-gradient rounded-2xl flex items-center justify-center shadow-lg shadow-blue-200 dark:shadow-blue-900/40 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-5 h-5 text-white"></i>
        </div>
        <div>
          <h1 class="font-black text-sm leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">ExamSystem</h1>
          <p class="text-[11px] font-medium text-slate-400">Admin Console</p>
        </div>
      </div>
    </div>

    <nav class="px-3 space-y-0.5 flex-1 overflow-y-auto">

      <!-- Overview -->
      <p class="px-2 pt-2 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Overview</p>
      <a href="{{ route('admin.dashboard') }}"
         class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.dashboard') ? 'admin-nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="layout-dashboard" class="w-4 h-4 flex-shrink-0"></i>
        Dashboard
      </a>

      <!-- Management -->
      <p class="px-2 pt-5 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Management</p>
      <a href="{{ route('admin.users') }}"
         class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.users') ? 'admin-nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="users-round" class="w-4 h-4 flex-shrink-0"></i>
        User Management
      </a>
      <a href="{{ route('admin.exams') }}"
         class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.exams') ? 'admin-nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="book-open" class="w-4 h-4 flex-shrink-0"></i>
        Exams
      </a>

      <!-- Support & Security -->
      <p class="px-2 pt-5 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">Support &amp; Security</p>
      <a href="{{ route('admin.support') }}"
         class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.support*') ? 'admin-nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="headphones" class="w-4 h-4 flex-shrink-0"></i>
        <span class="flex-1">Support Desk</span>
        @if(($openTickets ?? 0) > 0)
        <span class="text-[10px] font-black bg-rose-100 text-rose-600 dark:bg-rose-500/15 dark:text-rose-400 px-2 py-0.5 rounded-full">{{ $openTickets }}</span>
        @endif
      </a>
      <a href="{{ route('admin.security') }}"
         class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.security') ? 'admin-nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="shield-check" class="w-4 h-4 flex-shrink-0"></i>
        Security Logs
      </a>

      <!-- System -->
      <p class="px-2 pt-5 pb-2 text-[10px] font-black tracking-[0.12em] uppercase text-slate-400">System</p>
      <a href="{{ route('admin.settings') }}"
         class="nav-link flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold {{ request()->routeIs('admin.settings*') ? 'admin-nav-active' : 'text-slate-500 hover:text-slate-900 dark:hover:text-white hover:bg-slate-50 dark:hover:bg-slate-800' }}">
        <i data-lucide="settings-2" class="w-4 h-4 flex-shrink-0"></i>
        Settings
      </a>
    </nav>

    <!-- User Profile Footer -->
    <div class="p-3 m-3 rounded-2xl border transition-colors"
         :class="darkMode ? 'bg-slate-800 border-slate-700' : 'bg-slate-50 border-slate-100'">
      <div class="flex items-center gap-3">
        <div class="relative flex-shrink-0">
          @if(Auth::user()->avatar_url)
            <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->full_name }}"
                 class="w-9 h-9 rounded-xl object-cover shadow-sm">
          @else
            <div class="w-9 h-9 rounded-xl admin-brand-gradient flex items-center justify-center shadow-sm">
              <span class="text-xs font-black text-white uppercase">
                {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'AD' }}
              </span>
            </div>
          @endif
          <div class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-emerald-400 rounded-full border-2"
               :class="darkMode ? 'border-slate-800' : 'border-slate-50'"></div>
        </div>
        <div class="flex-1 overflow-hidden">
          <h4 class="text-xs font-bold truncate" :class="darkMode ? 'text-white' : 'text-slate-900'">
            {{ Auth::user()->full_name ?? 'Admin User' }}
          </h4>
          <p class="text-[11px] text-slate-400 font-medium truncate">
            {{ Auth::user()->institutional_id ?? 'ADM-0000' }}
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