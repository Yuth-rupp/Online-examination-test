<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Teacher Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f8fafc] h-screen font-sans overflow-hidden flex">

    <aside class="w-64 bg-white h-full border-r border-gray-100 flex flex-col justify-between flex-shrink-0">
        <div>
            <div class="p-6 flex items-center gap-3 text-[#1e5fa7] font-bold text-lg border-b border-gray-50">
                <i class="fa-solid fa-graduation-cap text-xl"></i>
                <span>ExamSystem</span>
            </div>
            <nav class="p-4 space-y-1">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-4 px-4 py-3 bg-[#1e7be6] text-white rounded-xl text-sm font-medium shadow-sm shadow-blue-200">
                    <i class="fa-solid fa-table-columns text-base w-5"></i> Dashboard
                </a>
                <a href="{{ route('questions.create') }}" class="flex items-center gap-4 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-medium transition-all">
                    <i class="fa-solid fa-folder-open text-base w-5"></i> Question Bank
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-medium transition-all">
                    <i class="fa-solid fa-eye text-base w-5"></i> Monitoring
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-medium transition-all">
                    <i class="fa-solid fa-file-signature text-base w-5"></i> Grading
                </a>
                <a href="#" class="flex items-center gap-4 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-medium transition-all">
                    <i class="fa-solid fa-chart-simple text-base w-5"></i> Analytics
                </a>
                <a href="{{ route('teacher.settings') }}" class="flex items-center gap-4 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-medium transition-all">
                    <i class="fa-solid fa-gear text-base w-5"></i> Settings
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-gray-50 bg-gray-50/50 flex items-center gap-3">
            <div class="w-10 h-10 rounded-full overflow-hidden border border-gray-200">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Alex" class="bg-red-100" alt="Avatar">
            </div>
            <div class="flex flex-col">
                <span class="text-sm font-bold text-gray-700 leading-tight">{{ Auth::user()->full_name ?? 'Teacher Alex' }}</span>
                <span class="text-[11px] text-gray-400 font-medium">Senior Faculty</span>
            </div>
        </div>
    </aside>

    <main class="flex-1 h-full flex flex-col overflow-hidden">
        <header class="bg-white h-16 border-b border-gray-100 flex items-center justify-between px-8 z-10 flex-shrink-0">
            <div class="relative w-96">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-gray-400 text-xs"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" class="w-full pl-9 pr-4 py-2 bg-gray-100/80 rounded-xl text-xs text-gray-600 focus:outline-none focus:bg-white focus:border-gray-200 transition-all" placeholder="Search exams, students, or reports...">
            </div>
            <div class="flex items-center gap-4 text-gray-400 text-sm">
                <button class="hover:text-gray-600 relative p-1"><i class="fa-regular fa-bell"></i><span class="absolute top-1 right-1 w-1.5 h-1.5 bg-blue-500 rounded-full"></span></button>
                <button class="hover:text-gray-600 p-1"><i class="fa-solid fa-sliders"></i></button>
            </div>
        </header>

        <div class="flex-1 p-8 overflow-y-auto flex gap-6">
            <div class="w-2/3 space-y-6">
                <div>
                    <h2 class="text-2xl font-extrabold text-gray-800">Welcome back, {{ explode(' ', Auth::user()->full_name ?? 'Alex')[0] }}</h2>
                    <p class="text-xs text-gray-400 font-medium mt-1">Here's what's happening in your classes today.</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
                        <div class="flex justify-between items-start">
                            <div class="p-2.5 bg-blue-50 rounded-xl text-blue-600 text-sm"><i class="fa-regular fa-clipboard"></i></div>
                            <span class="bg-emerald-50 text-emerald-600 text-[10px] font-bold px-2 py-0.5 rounded-full">+2 this week</span>
                        </div>
                        <div><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Total Exams</p><h3 class="text-3xl font-extrabold text-gray-800 mt-1">8</h3></div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
                        <div class="flex justify-between items-start">
                            <div class="p-2.5 bg-amber-50 rounded-xl text-amber-600 text-sm"><i class="fa-solid fa-tower-broadcast"></i></div>
                            <span class="bg-amber-50 text-amber-600 text-[10px] font-bold px-2 py-0.5 rounded-full">Live Now</span>
                        </div>
                        <div><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Active Sessions</p><h3 class="text-3xl font-extrabold text-gray-800 mt-1">2</h3></div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm flex flex-col justify-between h-32">
                        <div class="flex justify-between items-start">
                            <div class="p-2.5 bg-purple-50 rounded-xl text-purple-600 text-sm"><i class="fa-regular fa-folder-open"></i></div>
                            <span class="bg-rose-50 text-rose-600 text-[10px] font-bold px-2 py-0.5 rounded-full">Urgent</span>
                        </div>
                        <div><p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">Pending Grading</p><h3 class="text-3xl font-extrabold text-gray-800 mt-1">45</h3></div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                    <div class="p-5 flex justify-between items-center border-b border-gray-50">
                        <h4 class="font-bold text-gray-800 text-sm">Active Exam Sessions</h4>
                        <a href="#" class="text-xs font-bold text-[#1e7be6] hover:underline">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/70 border-b border-gray-50 text-[10px] font-bold text-gray-400 uppercase tracking-wider">
                                    <th class="py-3 px-6">Exam Details</th>
                                    <th class="py-3 px-4">Participants</th>
                                    <th class="py-3 px-4">Time Left</th>
                                    <th class="py-3 px-6 text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 text-xs text-gray-600 font-medium">
                                <tr>
                                    <td class="py-4 px-6"><div class="font-bold text-gray-800 text-sm">Advanced Calculus - Midterm</div><div class="text-[10px] text-gray-400 font-semibold mt-0.5">Group A • Mathematics</div></td>
                                    <td class="py-4 px-4"><span class="bg-blue-50 text-[#1e7be6] font-bold px-2 py-1 rounded-lg text-[11px]">+22</span></td>
                                    <td class="py-4 px-4 font-bold text-amber-500">42:15</td>
                                    <td class="py-4 px-6 text-center"><button class="bg-[#1e7be6] text-white text-[11px] font-bold px-4 py-2 rounded-xl flex items-center gap-2 mx-auto"><i class="fa-solid fa-tower-broadcast"></i> Live View</button></td>
                                </tr>
                                <tr>
                                    <td class="py-4 px-6"><div class="font-bold text-gray-800 text-sm">Introduction to Quantum Physics</div><div class="text-[10px] text-gray-400 font-semibold mt-0.5">Physics 101 • Science Dept</div></td>
                                    <td class="py-4 px-4"><span class="bg-blue-50 text-[#1e7be6] font-bold px-2 py-1 rounded-lg text-[11px]">+18</span></td>
                                    <td class="py-4 px-4 font-bold text-amber-500">01:15:20</td>
                                    <td class="py-4 px-6 text-center"><button class="bg-[#1e7be6] text-white text-[11px] font-bold px-4 py-2 rounded-xl flex items-center gap-2 mx-auto"><i class="fa-solid fa-tower-broadcast"></i> Live View</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="w-1/3 bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between">
                <div>
                    <h4 class="font-bold text-gray-800 text-sm mb-5">Recent Activity</h4>
                    <div class="space-y-5 relative pl-4 border-l border-gray-100">
                        <div class="relative"><span class="absolute -left-[21px] top-1 bg-emerald-50 text-emerald-600 w-3.5 h-3.5 rounded-full flex items-center justify-center text-[7px] border border-white shadow-sm"><i class="fa-solid fa-check"></i></span><div class="text-xs font-bold text-gray-800">Sarah Jenkins submitted Calculus Midterm</div><div class="text-[10px] text-gray-400 font-medium mt-0.5">2 minutes ago</div></div>
                        <div class="relative"><span class="absolute -left-[21px] top-1 bg-blue-50 text-[#1e7be6] w-3.5 h-3.5 rounded-full flex items-center justify-center text-[7px] border border-white shadow-sm"><i class="fa-solid fa-user text-[6px]"></i></span><div class="text-xs font-bold text-gray-800">David Miller started Physics 101 Exam</div><div class="text-[10px] text-gray-400 font-medium mt-0.5">15 minutes ago</div></div>
                        <div class="relative"><span class="absolute -left-[21px] top-1 bg-rose-50 text-rose-600 w-3.5 h-3.5 rounded-full flex items-center justify-center text-[7px] border border-white shadow-sm"><i class="fa-solid fa-triangle-exclamation text-[6px]"></i></span><div class="text-xs font-bold text-gray-800 text-rose-700">Alert: Suspicious activity detected</div><div class="text-[10px] text-rose-400 font-medium mt-0.5">Session: Calculus • Stud: J. Doe</div></div>
                    </div>
                </div>
                <button class="w-full py-2.5 border border-gray-100 text-gray-500 font-bold text-xs rounded-xl hover:bg-gray-50 transition-all">View Detailed Activity</button>
            </div>
        </div>
    </main>
</body>
</html>