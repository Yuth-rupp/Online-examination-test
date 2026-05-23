<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Account Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-[#f8fafc] h-screen font-sans overflow-hidden flex flex-col">

    <header class="bg-white h-16 border-b border-gray-100 flex items-center justify-between px-8 z-20 flex-shrink-0">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3 text-[#1e5fa7] font-bold text-lg">
                <i class="fa-solid fa-graduation-cap text-xl"></i>
                <span>ExamSystem</span>
            </div>
            <nav class="flex gap-6 text-xs font-semibold text-gray-400">
                <a href="#" class="text-[#1e5fa7] border-b-2 border-[#1e5fa7] pb-5 pt-5 px-1">Account Settings</a>
                <a href="{{ route('teacher.dashboard') }}" class="hover:text-gray-600 transition-all pb-5 pt-5">Dashboard</a>
                <a href="#" class="hover:text-gray-600 transition-all pb-5 pt-5">Examinations</a>
            </nav>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200">
                    <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Alex" class="bg-red-100" alt="Avatar">
                </div>
                <span class="text-xs font-bold text-[#1e5fa7]">
                    {{ Auth::guard('web')->user()->full_name ?? 'Teacher Alex' }}
                </span>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="flex items-center gap-2 text-xs font-bold text-rose-500 hover:text-rose-700 transition-all border border-transparent hover:border-rose-100 px-3 py-1.5 rounded-lg">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i> Sign Out
                </button>
            </form>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        <aside class="w-64 bg-white h-full border-r border-gray-100 p-4 flex flex-col justify-between flex-shrink-0">
            <nav class="space-y-1">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-4 px-4 py-3 text-gray-500 hover:bg-gray-50 hover:text-gray-800 rounded-xl text-sm font-medium transition-all">
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
                <a href="#" class="flex items-center gap-4 px-4 py-3 bg-[#1e7be6] text-white rounded-xl text-sm font-medium shadow-sm shadow-blue-200 transition-all">
                    <i class="fa-solid fa-gear text-base w-5"></i> Settings
                </a>
            </nav>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto flex gap-6">
            
            <div class="w-2/3 space-y-6">
                
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340] mb-6">
                        <i class="fa-regular fa-address-card text-[#1e5fa7] text-base"></i> Personalization
                    </div>

                    <div class="flex items-center gap-6 mb-8">
                        <div class="relative">
                            <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-100 shadow-inner">
                                <img src="https://api.dicebear.com/7.x/bottts/svg?seed=Alex" class="bg-red-100 w-full h-full" alt="Avatar">
                            </div>
                            <button class="absolute bottom-0 right-0 bg-[#0a2569] text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] shadow-md hover:bg-blue-800 transition-all">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>
                        <div class="flex gap-2.5">
                            <button class="bg-[#0a2569] hover:bg-[#061743] text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition-all">Change Image</button>
                            <button class="bg-[#e2e8f0] hover:bg-gray-300 text-gray-600 text-xs font-bold px-4 py-2 rounded-xl transition-all">Remove</button>
                        </div>
                        <p class="text-[10px] font-semibold text-gray-400 mt-auto ml-2">JPG, GIF or PNG. Max size of 800K.</p>
                    </div>

                    <form action="#" method="POST" class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-2">Full Name</label>
                                <input type="text" value="{{ Auth::guard('web')->user()->full_name ?? 'Dr. Sem Vattanakpanha' }}" 
                                    class="w-full px-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-2">University Email</label>
                                <input type="email" value="{{ Auth::guard('web')->user()->email ?? 'vattanakpanha@university.edu' }}" 
                                    class="w-full px-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500 transition-all">
                            </div>
                        </div>

                        <div class="w-1/2 pr-2">
                            <label class="block text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-2">University ID</label>
                            <input type="text" value="#UNI-8842-1092" disabled 
                                class="w-full px-4 py-3 bg-[#f8fafc] border border-gray-100 text-gray-300 rounded-xl text-xs font-medium cursor-not-allowed select-none">
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-50">
                            <button type="button" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition-all px-4 py-2">Cancel</button>
                            <button type="submit" class="bg-[#173154] hover:bg-[#0f2139] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md transition-all">Save Changes</button>
                        </div>
                    </form>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340] mb-6">
                        <i class="fa-solid fa-wheelchair text-[#1e5fa7] text-base"></i> Accessibility Options
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <h4 class="text-xs font-bold text-gray-700 mb-0.5">High Contrast Mode</h4>
                            <p class="text-[11px] text-gray-400 font-medium">Enhance UI visibility for visual impairments.</p>
                        </div>
                        <button class="w-10 h-5 bg-gray-200 rounded-full p-0.5 transition-all relative flex items-center">
                            <span class="w-4 h-4 bg-white rounded-full shadow-sm"></span>
                        </button>
                    </div>
                </section>
            </div>

            <div class="w-1/3 space-y-6">
                
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 space-y-5">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340]">
                        <i class="fa-solid fa-shield-halved text-[#1e5fa7] text-base"></i> Security & Privacy
                    </div>
                    
                    <div class="space-y-2">
                        <div class="bg-gray-50 px-4 py-3 rounded-xl flex justify-between items-center text-[10px] font-bold">
                            <span class="text-gray-600 flex items-center gap-2"><i class="fa-brands fa-google text-gray-400"></i> Google SSO</span>
                            <span class="text-emerald-600 uppercase tracking-wider">Connected</span>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 rounded-xl flex justify-between items-center text-[10px] font-bold">
                            <span class="text-gray-600 flex items-center gap-2"><i class="fa-brands fa-microsoft text-gray-400"></i> Microsoft Azure</span>
                            <span class="text-emerald-600 uppercase tracking-wider">Connected</span>
                        </div>
                    </div>

                    <div class="pt-2 space-y-3 text-[11px] font-semibold text-gray-500">
                        <a href="#" class="flex justify-between items-center hover:text-[#1e5fa7] transition-all">
                            <span>View Privacy Policy (GDPR)</span> <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-gray-400"></i>
                        </a>
                        <a href="#" class="flex justify-between items-center hover:text-[#1e5fa7] transition-all">
                            <span>Data Usage Agreement</span> <i class="fa-solid fa-arrow-up-right-from-square text-[9px] text-gray-400"></i>
                        </a>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340] mb-4">
                            <i class="fa-regular fa-comments text-[#1e5fa7] text-base"></i> Support & Notifications
                        </div>
                        
                        <button class="w-full py-3 bg-[#173154] hover:bg-[#0f2139] text-white font-medium text-xs rounded-xl shadow-sm mb-2 flex items-center justify-center gap-2 transition-all">
                            <i class="fa-regular fa-circle-question text-sm"></i> Technical Support
                        </button>

                        <div class="grid grid-cols-2 gap-2 mb-6">
                            <button class="py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-[10px] rounded-xl transition-all">Help Center</button>
                            <button class="py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 font-bold text-[10px] rounded-xl transition-all">User Guides</button>
                        </div>

                        <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400 mb-3">System Feed</p>
                        <div class="space-y-4 pl-3.5 border-l border-gray-100 relative">
                            <div class="relative text-[11px]">
                                <span class="absolute -left-[19px] top-1 bg-red-500 w-2 h-2 rounded-full border-2 border-white shadow-sm"></span>
                                <div class="font-bold text-gray-700">Storage Quota Alert</div>
                                <div class="text-[9px] text-gray-400 font-medium">2 hours ago • Action required</div>
                            </div>
                            <div class="relative text-[11px]">
                                <span class="absolute -left-[19px] top-1 bg-emerald-500 w-2 h-2 rounded-full border-2 border-white shadow-sm"></span>
                                <div class="font-bold text-gray-700">Security Audit Passed</div>
                                <div class="text-[9px] text-gray-400 font-medium">Yesterday, 4:12 PM • 0 threats</div>
                            </div>
                            <div class="relative text-[11px]">
                                <span class="absolute -left-[19px] top-1 bg-blue-400 w-2 h-2 rounded-full border-2 border-white shadow-sm"></span>
                                <div class="font-bold text-gray-700">Profile Updated</div>
                                <div class="text-[9px] text-gray-400 font-medium">Oct 24, 2023 • System</div>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </main>
    </div>

</body>
</html>