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
        .high-contrast-mode .text-[#0F172A],
        .high-contrast-mode .text-[#1E293B] {
            color: #F9FAFB !important;
        }
        .high-contrast-mode .text-[#64748B],
        .high-contrast-mode .text-[#475569] {
            color: #9CA3AF !important;
        }
        .high-contrast-mode .bg-[#F8FAFC],
        .high-contrast-mode .bg-[#FAFCFF] {
            background-color: #030712 !important;
        }
    </style>
    <script>
        // Reads local storage before browser starts painting to prevent flashing white frames
        if (localStorage.getItem('high-contrast-enabled') === 'true') {
            document.documentElement.classList.add('high-contrast-mode');
        }
    </script>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex selection:bg-blue-500/20">

    <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between flex-shrink-0 z-20">
        <div>
            <div class="h-20 flex items-center px-6 gap-2.5">
                <div class="w-9 h-9 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span class="font-bold text-xl text-[#0F172A] tracking-tight">ExamSystem</span>
            </div>

            <nav class="px-4 py-2 space-y-1">
                <a href="{{ route('teacher.dashboard') }}" class="group flex items-center gap-3 px-4 py-3 bg-[#1D4ED8] text-white font-semibold rounded-xl shadow-md shadow-blue-500/10 transition-all">
                    <i class="fa-solid fa-table-columns w-5 text-center text-lg text-white"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('teacher.question-bank') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-database w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Question Bank</span>
                </a>
                
                <a href="#" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-desktop w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Monitoring</span>
                </a>
                
                <a href="{{ route('teacher.grading.show', ['student_id' => 1]) }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-file-signature w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Grading</span>
                </a>
                
                <a href="{{ route('teacher.analytics') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-chart-line w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Analytics</span>
                </a>
                <a href="{{ route('teacher.settings') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-gear w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-[#E2E8F0] flex items-center gap-3 bg-[#F8FAFC] m-4 rounded-xl">
            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 bg-white">
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
                <button class="p-2.5 hover:bg-[#F1F5F9] rounded-xl relative border border-[#E2E8F0] bg-white text-[#64748B] transition-colors">
                    <i class="fa-regular fa-bell text-lg"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <div class="flex items-center gap-3 border-l pl-6 border-[#E2E8F0]">
                    <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 bg-white">
                        <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                    <span class="text-sm font-semibold text-[#475569]">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</span>
                </div>
            </div>
        </header>

        <div class="p-8 flex-1 space-y-8 overflow-y-auto max-w-[1400px] w-full mx-auto">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#EFF6FF] text-[#1D4ED8] rounded-xl"><i class="fa-regular fa-file-lines text-xl"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#DCFCE7] text-[#15803D] rounded-full">+2 this week</span>
                    </div>
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider">Total Exams</p>
                    <h3 class="text-3xl font-extrabold text-[#0F172A] mt-1">8</h3>
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

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2 bg-white border border-[#E2E8F0] rounded-2xl shadow-sm flex flex-col justify-between overflow-hidden">
                    <div class="p-6 border-b border-[#E2E8F0] flex justify-between items-center">
                        <div>
                            <h3 class="font-bold text-[#0F172A] text-lg">Active Exam Sessions</h3>
                            <p class="text-xs text-[#64748B] mt-0.5">Real-time supervision system data</p>
                        </div>
                        <button class="text-xs font-semibold text-[#1D4ED8] hover:underline">View All</button>
                    </div>
                    <div class="divide-y divide-[#F1F5F9] overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead>
                                <tr class="bg-[#FAFCFF] text-[#94A3B8] font-bold text-xs uppercase tracking-wider">
                                    <th class="px-6 py-3.5">Exam Details</th>
                                    <th class="px-6 py-3.5">Participants</th>
                                    <th class="px-6 py-3.5">Time Left</th>
                                    <th class="px-6 py-3.5">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium text-[#475569]">
                                <tr>
                                    <td class="px-6 py-4.5">
                                        <div class="font-bold text-[#1E293B]">Advanced Calculus - Midterm</div>
                                        <div class="text-xs text-[#94A3B8] mt-0.5">Group A • Mathematics</div>
                                    </td>
                                    <td class="px-6 py-4.5"><span class="bg-[#EFF6FF] text-[#1D4ED8] text-xs font-bold px-2 py-1 rounded-md">+22</span></td>
                                    <td class="px-6 py-4.5 text-[#EA580C] font-semibold">42:15</td>
                                    <td class="px-6 py-4.5"><button class="bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5"><i class="fa-solid fa-tower-broadcast"></i> Live View</button></td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4.5">
                                        <div class="font-bold text-[#1E293B]">Introduction to Quantum Physics</div>
                                        <div class="text-xs text-[#94A3B8] mt-0.5">Physics 101 • Science Dept</div>
                                    </td>
                                    <td class="px-6 py-4.5"><span class="bg-[#EFF6FF] text-[#1D4ED8] text-xs font-bold px-2 py-1 rounded-md">+18</span></td>
                                    <td class="px-6 py-4.5">01:15:20</td>
                                    <td class="px-6 py-4.5"><button class="bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold px-3 py-1.5 rounded-lg transition-colors flex items-center gap-1.5"><i class="fa-solid fa-tower-broadcast"></i> Live View</button></td>
                                </tr>
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
                    <button class="w-full mt-6 py-2.5 bg-slate-50 hover:bg-slate-100 text-[#475569] border border-[#E2E8F0] font-bold text-xs rounded-xl transition-colors">View Detailed Activity</button>
                </div>
            </div>
        </div>
    </main>
</body>
</html>