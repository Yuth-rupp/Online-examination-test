{{-- ============================================================
     FILE: resources/views/partials/teacher-sidebar.blade.php
     ROLE: Single source of truth for the teacher left sidebar.
           Included by every teacher page so the size, spacing,
           and badges are always identical — and the "Grading"
           badge is always a REAL, live count (starts at 0 for a
           freshly registered account), never a hardcoded number.
     ============================================================ --}}
<style>
    /* Scoped so it can never be overridden by a page's own CSS */
    #ts-sidebar { width: 260px; }
    #ts-sidebar .nav-link {
        display: flex; align-items: center; gap: 11px;
        padding: 9px 12px; border-radius: 12px; text-decoration: none;
        font-size: 13.5px; font-weight: 500; color: #64748B; transition: all .2s;
    }
    #ts-sidebar .nav-link:hover { background: #F8FAFC; color: #1E293B; }
    #ts-sidebar .nav-link.active { background: #EFF6FF; color: #1D4ED8; font-weight: 700; }
    #ts-sidebar .nav-icon {
        width: 34px; height: 34px; border-radius: 9px;
        display: flex; align-items: center; justify-content: center;
        font-size: 14px; flex-shrink: 0; transition: all .2s;
    }
    #ts-sidebar .nav-link:hover .nav-icon { background: #F1F5F9; }
    #ts-sidebar .nav-link.active .nav-icon { background: #1D4ED8; color: #fff; }
</style>

<aside id="ts-sidebar" class="w-[260px] bg-white border-r border-[#E2E8F0] flex flex-col flex-shrink-0 sticky top-0 h-screen z-20">

    <!-- Logo -->
    <a href="{{ route('teacher.dashboard') }}"
       class="h-[72px] flex items-center px-5 gap-3 border-b border-[#E2E8F0] hover:opacity-90 transition-opacity flex-shrink-0">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white flex-shrink-0"
             style="background:linear-gradient(135deg,#2563EB 0%,#1E40AF 100%);box-shadow:0 4px 12px rgba(37,99,235,.35);">
            <i class="fa-solid fa-graduation-cap text-base"></i>
        </div>
        <span class="font-black text-[18px] text-[#0F172A] tracking-tight">ExamSystem</span>
    </a>

    {{-- Department badge: shows every department this teacher is assigned to teach in
         (home department_id + any extra department_teacher pivot rows), so it's just as
         clear for a teacher as it already is for a department admin. --}}
    @php
        $__tUser = Auth::user();
        $__tDepts = collect();
        if ($__tUser) {
            $__tDepts = $__tUser->departments()->pluck('name');
            if ($__tUser->department_id && $__tUser->department && !$__tDepts->contains($__tUser->department->name)) {
                $__tDepts->push($__tUser->department->name);
            }
        }
    @endphp
    <div class="px-5 pt-3 pb-1">
        @if($__tDepts->isNotEmpty())
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200"
                  title="Teaching in: {{ $__tDepts->implode(', ') }}">
                <i class="fa-solid fa-building text-[10px]"></i>
                {{ $__tDepts->count() > 1 ? $__tDepts->first().' +'.($__tDepts->count() - 1) : $__tDepts->first() }}
            </span>
        @else
            <span class="inline-flex items-center gap-1.5 text-[11px] font-bold px-2.5 py-1.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200"
                  title="No department has been assigned to you yet — ask your admin to add you">
                <i class="fa-solid fa-circle-question text-[10px]"></i>
                No Department
            </span>
        @endif
    </div>

    <!-- Nav -->
    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-1 pb-2">Main Menu</p>

        <a href="{{ route('teacher.dashboard') }}" class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span>
            <span>Dashboard</span>
        </a>

        <a href="{{ route('teacher.question-bank') }}" class="nav-link {{ request()->routeIs('teacher.question-bank') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-database"></i></span>
            <span>Question Bank</span>
        </a>

        <a href="{{ route('teacher.monitoring.show') }}" class="nav-link {{ request()->routeIs('teacher.monitoring.show') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-display"></i></span>
            <span>Monitoring</span>
        </a>

        <a href="{{ route('teacher.grading.queue') }}" class="nav-link {{ request()->routeIs('teacher.grading.queue') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-pen-to-square"></i></span>
            <span>Grading</span>
            @if(($pendingGradingCount ?? 0) > 0)
                <span class="ml-auto text-[10px] font-bold bg-red-500 text-white rounded-full px-2 py-0.5">{{ $pendingGradingCount }}</span>
            @endif
        </a>

        <a href="{{ route('teacher.analytics') }}" class="nav-link {{ request()->routeIs('teacher.analytics') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span>
            <span>Analytics</span>
        </a>

        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-4 pb-2">Account</p>

        <a href="{{ route('teacher.settings') }}" class="nav-link {{ request()->routeIs('teacher.settings') ? 'active' : '' }}">
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span>
            <span>Settings</span>
        </a>
    </nav>

    <!-- User -->
    <div class="p-3 border-t border-[#E2E8F0] flex-shrink-0">
        <a href="{{ route('teacher.settings') }}"
           class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8FAFC] transition-colors">
            <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-[#E2E8F0] flex-shrink-0">
                <img id="sidebarAvatar" src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'Alex') }}"
                     class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-[#0F172A] truncate">{{ Auth::user()->full_name ?? 'Teacher' }}</p>
                <p class="text-xs text-[#94A3B8] font-medium">Senior Faculty</p>
            </div>
            <i class="fa-solid fa-ellipsis-vertical text-[#94A3B8] text-sm"></i>
        </a>
    </div>
</aside>