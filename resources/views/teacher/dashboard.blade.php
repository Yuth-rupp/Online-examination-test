<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* 🌓 SYSTEM-WIDE HIGH CONTRAST CORES */
        .high-contrast-mode {
            background-color: #030712 !important;
            color: #F9FAFB !important;
        }
        .high-contrast-mode aside, 
        .high-contrast-mode section, 
        .high-contrast-mode header,
        .high-contrast-mode .bg-white {
            background-color: #111827 !important;
            border-color: #374151 !important;
            color: #F9FAFB !important;
        }
        .high-contrast-mode nav a:not([class*="bg-"]) {
            color: #9CA3AF !important;
        }
        .high-contrast-mode nav a:not([class*="bg-"]):hover {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
        }
        .high-contrast-mode td,
        .high-contrast-mode th {
            color: #E5E7EB !important;
            border-color: #374151 !important;
        }
        .high-contrast-mode tr:hover {
            background-color: #1F2937 !important;
        }
        .high-contrast-mode text-[#0F172A],
        .high-contrast-mode text-[#1E293B] {
            color: #F9FAFB !important;
        }
        .high-contrast-mode text-[#64748B],
        .high-contrast-mode text-[#475569] {
            color: #9CA3AF !important;
        }
        .high-contrast-mode .bg-[#F8FAFC],
        .high-contrast-mode .bg-[#FAFCFF] {
            background-color: #030712 !important;
        }
    </style>
    <script>
        if (localStorage.getItem('high-contrast-enabled') === 'true') {
            document.documentElement.classList.add('high-contrast-mode');
        }
    </script>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex selection:bg-blue-500/20 relative overflow-x-hidden flex flex-row">

    <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between flex-shrink-0 z-20">
        <div>
            <div class="h-20 flex items-center px-6 gap-2.5">
                <div class="w-9 h-9 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span class="font-bold text-xl text-[#0F172A] tracking-tight">ExamSystem</span>
            </div>

            <nav class="px-4 py-2 space-y-1">
                <a href="{{ route('teacher.dashboard') }}" 
                   class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.dashboard') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} rounded-xl transition-all">
                     <i class="fa-solid fa-table-columns w-5 text-center text-lg {{ request()->routeIs('teacher.dashboard') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                     <span>Dashboard</span>
                </a>
                
                <a href="{{ route('teacher.question-bank') }}" 
                   class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.question-bank') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                     <i class="fa-solid fa-database w-5 text-center text-lg {{ request()->routeIs('teacher.question-bank') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                     <span>Question Bank</span>
                </a>
                
                <a href="{{ route('teacher.monitoring.show') }}" 
                   class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.monitoring.show') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                     <i class="fa-solid fa-desktop w-5 text-center text-lg {{ request()->routeIs('teacher.monitoring.show') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                     <span>Monitoring</span>
                </a>
                
                <a href="{{ route('teacher.grading.show', ['student_id' => 1]) }}" 
                   class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.grading.*') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                     <i class="fa-solid fa-file-signature w-5 text-center text-lg {{ request()->routeIs('teacher.grading.*') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                     <span>Grading</span>
                </a>
                
                <a href="{{ route('teacher.analytics') }}" 
                   class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.analytics') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                     <i class="fa-solid fa-chart-line w-5 text-center text-lg {{ request()->routeIs('teacher.analytics') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                     <span>Analytics</span>
                </a>

                <a href="{{ route('teacher.settings') }}" 
                   class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.settings') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                     <i class="fa-solid fa-gear w-5 text-center text-lg {{ request()->routeIs('teacher.settings') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                     <span>Settings</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-[#E2E8F0] flex items-center gap-3 bg-[#F8FAFC] m-4 rounded-xl cursor-pointer hover:bg-slate-100/80 transition-colors" onclick="window.location.href='{{ route('teacher.settings') }}'">
            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 bg-white shadow-inner">
                <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0F172A] leading-tight">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</h4>
                <p class="text-xs text-[#94A3B8] font-medium mt-0.5">Senior Faculty</p>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0">
        
        <header class="h-20 bg-white border-b border-[#E2E8F0] flex items-center justify-between px-8 z-10 flex-shrink-0">
            <h1 class="text-2xl font-bold text-[#0F172A]">Welcome back, {{ Str::before(Auth::user()->full_name ?? 'Yun', ' ') }}</h1>
            
            <div class="flex items-center gap-6">
                <button onclick="toggleNotificationDrawer()" class="p-2.5 hover:bg-[#F1F5F9] rounded-xl relative border border-[#E2E8F0] bg-white text-[#64748B] transition-all hover:scale-105 active:scale-95">
                    <i class="fa-regular fa-bell text-lg"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <div class="flex items-center gap-3 border-l pl-6 border-[#E2E8F0] cursor-pointer" onclick="window.location.href='{{ route('teacher.settings') }}'">
                    <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 bg-white">
                        <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <span class="text-sm font-semibold text-[#475569]">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</span>
                </div>
            </div>
        </header>

        <div class="p-8 flex-1 space-y-8 overflow-y-auto max-w-[1400px] w-full mx-auto">
            
            @if(session('success'))
                <div class="bg-emerald-50 border-2 border-emerald-500/30 p-5 rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 shadow-md shadow-emerald-500/5 animate-fadeIn mb-4">
                    <div class="flex items-start gap-3.5">
                        <div class="p-3 bg-emerald-500 text-white rounded-xl shadow-sm mt-0.5">
                            <i class="fa-solid fa-key text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-[#0F172A] text-base leading-tight">Exam Session Deployed Successfully!</h3>
                            <p class="text-xs text-emerald-700/90 font-medium mt-1">
                                {{ session('success') }}
                            </p>
                        </div>
                    </div>
                    
                    @if(Str::contains(session('success'), ': '))
                        <div class="flex items-center gap-2 bg-white border border-emerald-200 shadow-xs px-4 py-2.5 rounded-xl self-start sm:self-center">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Access Code:</span>
                            <span class="text-base font-extrabold text-emerald-600 font-mono tracking-wider">
                                {{ Str::afterLast(session('success'), ': ') }}
                            </span>
                            <button onclick="navigator.clipboard.writeText('{{ Str::afterLast(session('success'), ': ') }}'); alert('Token copied!')" 
                                    class="ml-2 text-slate-400 hover:text-slate-600 p-1 hover:bg-slate-50 rounded-md transition-all">
                                <i class="fa-regular fa-copy text-sm"></i>
                            </button>
                        </div>
                    @endif
                </div>
            @endif

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#EFF6FF] text-[#1D4ED8] rounded-xl"><i class="fa-regular fa-file-lines text-xl"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#DCFCE7] text-[#15803D] rounded-full">+2 this week</span>
                    </div>
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Total Exams</p>
                    <h3 class="text-3xl font-extrabold text-[#0F172A] mt-1">{{ $totalExams ?? 0 }}</h3>
                </div>

                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#FFF7ED] text-[#EA580C] rounded-xl"><i class="fa-solid fa-satellite-dish text-xl"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#FFEDD5] text-[#C2410C] rounded-full animate-pulse">Live Now</span>
                    </div>
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Active Sessions</p>
                    <h3 class="text-3xl font-extrabold text-[#0F172A] mt-1">2</h3>
                </div>

                <a href="{{ route('teacher.grading.show', ['student_id' => 1]) }}" class="block bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm relative hover:border-blue-400/70 transition-all group">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#FDF2F8] text-[#DB2777] group-hover:bg-pink-50 transition-colors rounded-xl"><i class="fa-solid fa-signature text-xl"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#FCE7F3] text-[#9D174D] rounded-full">Urgent</span>
                    </div>
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Pending Grading</p>
                    <div class="flex items-baseline justify-between">
                        <h3 class="text-3xl font-extrabold text-[#0F172A] mt-1">45</h3>
                        <span class="text-xs font-bold text-blue-600 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">Open Panel &rarr;</span>
                    </div>
                </a>
            </div>

            <section class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="p-6 border-b border-[#E2E8F0] flex items-center gap-3 bg-slate-50/50">
                    <div class="w-8 h-8 bg-indigo-50 rounded-lg flex items-center justify-center text-indigo-600"><i class="fa-solid fa-plus text-sm"></i></div>
                    <div>
                        <h3 class="font-bold text-[#0F172A] text-lg">Deploy New Examination Session</h3>
                        <p class="text-xs text-[#64748B] mt-0.5">Generate standalone metrics partitions and single-use classroom access keys instantly.</p>
                    </div>
                </div>
                <form action="{{ route('exams.store') }}" method="POST" class="p-6 grid grid-cols-1 md:grid-cols-4 gap-5 items-end">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Examination Title</label>
                        <input type="text" name="title" placeholder="e.g., DBMS Quiz 1" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium">
                    </div>
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center mb-0.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Target Assigned Curriculum Course</label>
                            <a href="{{ route('teacher.courses.create') }}" class="text-[11px] font-bold text-blue-600 hover:text-blue-800 transition-colors flex items-center gap-1"><i class="fa-solid fa-plus text-[9px]"></i> Add New Course</a>
                        </div>
                        <div class="relative">
                            <select name="course_id" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-700 transition-all font-medium appearance-none">
                                @forelse($courses as $courseItem)
                                    <option value="{{ $courseItem->id }}">{{ $courseItem->name }}</option>
                                @empty
                                    <option value="" disabled selected>No courses assigned to your account</option>
                                @endforelse
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-slate-400">
                                <i class="fa-solid fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Duration (Min)</label>
                            <input type="number" name="duration" placeholder="60" required min="1" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Pass Mark (%)</label>
                            <input type="number" name="pass_mark" placeholder="50" required min="0" max="100" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium">
                        </div>
                    </div>
                    <div>
                        <button type="submit" class="w-full bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-md shadow-blue-500/10 flex items-center justify-center gap-2 transform active:scale-[0.98]">
                            <i class="fa-solid fa-bolt"></i> Generate Access Token
                        </button>
                    </div>
                </form>

                <div class="m-6 p-4 bg-slate-50 border border-slate-100 rounded-xl">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2.5">Your Active Curriculum Subjects Manager</h4>
                    <div class="flex flex-wrap gap-2">
                        @foreach($courses as $courseItem)
                            <div class="flex items-center gap-2 bg-white px-3 py-1.5 border border-slate-200 rounded-xl text-xs font-medium shadow-xs">
                                <div>
                                    <span class="text-slate-800 font-bold">{{ $courseItem->name }}</span>
                                    <span class="text-slate-400 font-mono text-[10px] ml-1">({{ $courseItem->code }})</span>
                                </div>
                                <form action="{{ route('teacher.courses.destroy', $courseItem->id) }}" method="POST" onsubmit="return confirm('Purge this entire course partition from database structure logs?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-red-500 transition-colors pl-1 border-l border-slate-100">
                                        <i class="fa-regular fa-trash-can text-[11px]"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 bg-white border border-[#E2E8F0] rounded-2xl shadow-sm flex flex-col justify-between overflow-hidden">
                    <div class="p-6 border-b border-[#E2E8F0] flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-[#0F172A] text-lg">Active Exam Sessions</h3>
                            <p class="text-xs text-[#64748B] mt-0.5">Real-time supervision system tokens</p>
                        </div>
                        <button onclick="window.location.href='{{ route('teacher.monitoring.show') }}'" class="text-xs font-semibold text-[#1D4ED8] hover:underline">View All</button>
                    </div>

                    <div class="divide-y divide-[#F1F5F9] overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-[#FAFCFF] text-[#94A3B8] font-bold text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3.5">Exam Details</th>
                                    <th class="px-6 py-3.5">Active Token</th>
                                    <th class="px-6 py-3.5">Duration</th>
                                    <th class="px-6 py-3.5 text-center">Action Parameters</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-[#475569]">
                                @forelse($activeExams as $activeSession)
                                    <tr class="hover:bg-slate-50/50 transition-colors">
                                        <td class="px-6 py-4.5">
                                            <div class="font-bold text-[#1E293B]">{{ $activeSession->title }}</div>
                                            <div class="text-xs text-[#94A3B8] mt-0.5">
                                                {{ $activeSession->course->name ?? 'General Curriculum' }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4.5">
                                            @if($activeSession->access_code)
                                                <div class="inline-flex items-center gap-1.5 bg-slate-100 border border-slate-200 text-slate-700 font-mono text-xs px-2.5 py-1 rounded-lg shadow-sm">
                                                    <span>{{ $activeSession->access_code }}</span>
                                                    <button type="button" onclick="navigator.clipboard.writeText('{{ $activeSession->access_code }}'); alert('Token copied!')" class="text-slate-400 hover:text-slate-600 transition-colors">
                                                        <i class="fa-regular fa-copy text-[10px]"></i>
                                                    </button>
                                                </div>
                                            @else
                                                <span class="text-xs text-slate-400 font-sans italic">No token active</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4.5 font-semibold text-slate-700">
                                            {{ $activeSession->duration }} mins
                                        </td>
                                        <td class="px-6 py-4.5 flex items-center justify-center gap-2">
                                            <a href="{{ route('teacher.monitoring.show') }}" class="inline-flex bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors items-center gap-1.5 shadow-sm">
                                                <i class="fa-solid fa-tower-broadcast"></i> Live View
                                            </a>
                                            <a href="{{ route('teacher.exams.preview', ['id' => $activeSession->id ?? $activeSession->exam_id]) }}" class="inline-flex bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold px-3 py-1.5 rounded-lg transition-colors items-center gap-1.5 border border-slate-200 shadow-xs">
                                                <i class="fa-regular fa-eye"></i> View Questions Page
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr class="bg-slate-50/30">
                                        <td colspan="4" class="px-6 py-10 text-center text-xs text-slate-400 italic">
                                            No active examination modules currently initialized on system.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-4 bg-[#FAFCFF] border-t border-[#E2E8F0]"></div>
                </div>

                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm p-6 flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-[#0F172A] text-lg mb-5">Recent Activity</h3>
                        <div class="space-y-5 pl-4 border-l-2 border-[#F1F5F9] relative">
                            <div class="relative text-xs font-medium">
                                <span class="absolute -left-[23px] top-0.5 bg-emerald-500 w-2.5 h-2.5 rounded-full border-2 border-white ring-4 ring-emerald-50"></span>
                                <div class="font-bold text-[#1E293B]">Sarah Jenkins submitted Calculus Midterm</div>
                                <div class="text-[10px] text-[#94A3B8] mt-0.5">2 minutes ago</div>
                            </div>
                            <div class="relative text-xs font-medium">
                                <span class="absolute -left-[23px] top-0.5 bg-blue-500 w-2.5 h-2.5 rounded-full border-2 border-white ring-4 ring-blue-50"></span>
                                <div class="font-bold text-[#1E293B]">David Miller started Physics 101 Exam</div>
                                <div class="text-[10px] text-[#94A3B8] mt-0.5">15 minutes ago</div>
                            </div>
                            <div class="relative text-xs font-medium">
                                <span class="absolute -left-[23px] top-0.5 bg-rose-500 w-2.5 h-2.5 rounded-full border-2 border-white ring-4 ring-rose-50"></span>
                                <div class="font-bold text-rose-600">Alert: Suspicious activity detected</div>
                                <div class="text-[10px] text-rose-400 mt-0.5">Session: Calculus • Stud: J. Doe</div>
                            </div>
                        </div>
                    </div>
                    <button onclick="window.location.href='{{ route('teacher.monitoring.show') }}'" class="w-full mt-6 py-2.5 bg-slate-50 hover:bg-slate-100 text-[#475569] border border-[#E2E8F0] font-bold text-xs rounded-xl transition-colors shadow-sm">View Detailed Activity</button>
                </div>

            </div>
        </div>
    </main>

    <div id="notificationDrawer" class="fixed inset-0 z-50 overflow-hidden hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 overflow-hidden">
            <div onclick="toggleNotificationDrawer()" class="absolute inset-0 bg-slate-950/40 backdrop-blur-xs transition-opacity" aria-hidden="true"></div>
            
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div class="pointer-events-auto w-screen max-w-md transform transition-all duration-300 translate-x-full" id="drawerBody">
                    <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-2xl border-l border-slate-200">
                        <div class="px-6 py-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-bell text-indigo-600 text-lg"></i>
                                <h2 class="text-base font-bold text-slate-900" id="slide-over-title">Live System Notifications</h2>
                            </div>
                            <button onclick="toggleNotificationDrawer()" class="p-1 rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                <i class="fa-solid fa-xmark text-lg"></i>
                            </button>
                        </div>
                        <div class="relative flex-1 p-6 space-y-4">
                            <div class="p-4 bg-rose-50/60 border border-rose-100 rounded-xl space-y-2">
                                <div class="flex justify-between items-start">
                                    <span class="px-2 py-0.5 bg-rose-100 text-rose-700 text-[10px] font-extrabold uppercase rounded-md tracking-wider">High Alert</span>
                                    <span class="text-[10px] text-rose-400 font-medium">Just Now</span>
                                </div>
                                <p class="text-xs font-bold text-rose-900">Suspicious tab navigation detected on candidate frame J. Doe.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const drawer = document.getElementById('notificationDrawer');
        const drawerBody = document.getElementById('drawerBody');

        function toggleNotificationDrawer() {
            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => {
                    drawerBody.classList.remove('translate-x-full');
                    drawerBody.classList.add('translate-x-0');
                }, 10);
            } else {
                drawerBody.classList.remove('translate-x-0');
                drawerBody.classList.add('translate-x-full');
                setTimeout(() => {
                    drawer.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>