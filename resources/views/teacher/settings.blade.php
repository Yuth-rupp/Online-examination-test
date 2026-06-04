<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Account Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        /* 🌓 GLOBAL HIGH CONTRAST STRUCTURAL RULES */
        .high-contrast-mode {
            background-color: #030712 !important;
            color: #F9FAFB !important;
        }
        /* Forces Sidebars, Section Containers, and Headers into deep dark mode */
        .high-contrast-mode aside, 
        .high-contrast-mode section, 
        .high-contrast-mode header,
        .high-contrast-mode .bg-white {
            background-color: #111827 !important;
            border-color: #374151 !important;
            color: #F9FAFB !important;
        }
        /* High-contrast nav text link balances */
        .high-contrast-mode nav a:not([class*="bg-"]) {
            color: #9CA3AF !important;
        }
        .high-contrast-mode nav a:not([class*="bg-"]):hover {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
        }
        /* High visibility input layout maps */
        .high-contrast-mode input, 
        .high-contrast-mode select, 
        .high-contrast-mode textarea {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
            border: 2px solid #4B5563 !important;
        }
        .high-contrast-mode input::placeholder {
            color: #9CA3AF !important;
        }
        .high-contrast-mode .bg-gray-50 {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
        }
        .high-contrast-mode .text-gray-700,
        .high-contrast-mode .text-gray-600 {
            color: #E5E7EB !important;
        }
        
        /* Smooth transitions for the new professional modal */
        #legalModalWrapper {
            transition: all 0.25s ease-in-out;
        }
    </style>
    <script>
        // Execute immediately before document rendering to prevent bright screen flashing
        if (localStorage.getItem('high-contrast-enabled') === 'true') {
            document.documentElement.classList.add('high-contrast-mode');
        }
    </script>
</head>
<body id="appBody" class="bg-[#f8fafc] h-screen font-sans overflow-hidden flex flex-col transition-all">

    <header class="bg-white h-16 border-b border-gray-100 flex items-center justify-between px-8 z-20 flex-shrink-0">
        <div class="flex items-center gap-8">
            <div class="flex items-center gap-3 text-blue-600 font-bold text-base tracking-tight">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                </svg>
                <span class="font-bold tracking-tight text-gray-900">ExamSystem</span>
            </div>
            <nav class="flex gap-6 text-xs font-semibold text-gray-400">
                <a href="#" class="text-blue-600 border-b-2 border-blue-600 pb-5 pt-5 px-1">Account Settings</a>
                <a href="{{ route('teacher.dashboard') }}" class="hover:text-gray-600 transition-all pb-5 pt-5">Dashboard</a>
                <a href="#" class="hover:text-gray-600 transition-all pb-5 pt-5">Examinations</a>
            </nav>
        </div>

        <div class="flex items-center gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full overflow-hidden border border-gray-200 bg-gray-50">
                    <img id="navAvatar" src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
                </div>
                <span class="text-xs font-bold text-gray-700">
                    {{ Auth::user()->full_name ?? 'Yun Dalin' }}
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
        <aside class="w-64 bg-white h-full border-r border-gray-100 p-6 flex flex-col justify-between flex-shrink-0 select-none">
            <div class="space-y-1">
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <a href="{{ route('teacher.question-bank') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Question Bank</span>
                </a>

                <a href="#" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Monitoring</span>
                </a>

                <a href="{{ route('teacher.grading.show', 1) }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Grading</span>
                </a>

                <a href="{{ route('teacher.analytics') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    <span>Analytics</span>
                </a>

                <div class="flex items-center gap-3.5 text-white bg-blue-600 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight shadow-sm shadow-blue-600/10">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </div>
            </div>

            <div class="flex items-center gap-3 border-t border-gray-100 pt-4 pb-2">
                <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 bg-gray-50 flex-shrink-0">
                    <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
                </div>
                <div>
                    <p class="text-xs font-bold text-gray-800 leading-none">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                    <p class="text-[10px] text-gray-400 font-medium mt-1.5">Senior Faculty</p>
                </div>
            </div>
        </aside>

        <main class="flex-1 p-8 overflow-y-auto flex gap-6">
            <div class="w-2/3 space-y-6">
                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340] mb-6">
                        <i class="fa-regular fa-address-card text-[#1e5fa7] text-base"></i> Personalization
                    </div>

                    <form action="{{ route('teacher.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        
                        <div class="flex items-center gap-6 mb-8">
                            <div class="relative">
                                <div class="w-24 h-24 rounded-full overflow-hidden border-2 border-gray-100 shadow-inner bg-gray-50">
                                    <img id="profileImageCanvas" src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
                                </div>
                                
                                <input type="file" id="hiddenAvatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewUploadedAvatar(this)">
                                <input type="hidden" id="removeAvatarFlag" name="remove_avatar" value="0">
                                
                                <button type="button" onclick="triggerAvatarUpload()" class="absolute bottom-0 right-0 bg-[#0a2569] text-white w-6 h-6 rounded-full flex items-center justify-center text-[10px] shadow-md hover:bg-blue-800 transition-all cursor-pointer">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </div>
                            <div class="flex gap-2.5">
                                <button type="button" onclick="triggerAvatarUpload()" class="bg-[#0a2569] hover:bg-[#061743] text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm transition-all cursor-pointer">Change Image</button>
                                <button type="button" onclick="removeProfileImage()" class="bg-[#e2e8f0] hover:bg-gray-300 text-gray-600 text-xs font-bold px-4 py-2 rounded-xl transition-all cursor-pointer">Remove</button>
                            </div>
                            <p class="text-[10px] font-semibold text-gray-400 mt-auto ml-2">JPG, GIF or PNG. Max size of 800K.</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-2">Full Name</label>
                                <input type="text" name="full_name" value="{{ Auth::user()->full_name ?? 'Yun Dalin' }}" 
                                    class="w-full px-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500 transition-all" required>
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-2">University Email</label>
                                <input type="email" name="email" value="{{ Auth::user()->email ?? 'dalin12345@gmail.com' }}" 
                                    class="w-full px-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-xs font-semibold text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500 transition-all" required>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="pr-2">
                                <label class="block text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-2">University ID</label>
                                <input type="text" value="#UNI-8842-1092" disabled 
                                    class="w-full px-4 py-3 bg-[#f8fafc] border border-gray-100 text-gray-300 rounded-xl text-xs font-medium cursor-not-allowed select-none">
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-50">
                            <button type="button" onclick="window.location.reload();" class="text-xs font-bold text-gray-500 hover:text-gray-700 transition-all px-4 py-2 cursor-pointer">Cancel</button>
                            <button type="submit" class="bg-[#173154] hover:bg-[#0f2139] text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow-md transition-all cursor-pointer">Save Changes</button>
                        </div>
                    </form>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                    <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340] mb-6">
                        <i class="fa-solid fa-eye text-[#1e5fa7] text-base"></i> Accessibility Options
                    </div>
                    <div class="flex justify-between items-center">
                        <div>
                            <span class="text-xs font-bold text-gray-700 mb-0.5 block">High Contrast Mode</span>
                            <p class="text-[11px] text-gray-400 font-medium">Enhance UI visibility for visual impairments.</p>
                        </div>
                        <button onclick="toggleHighContrastTheme()" id="contrastToggleSwitch" class="w-10 h-5 bg-gray-200 rounded-full p-0.5 transition-all relative flex items-center cursor-pointer">
                            <span id="switchNodeCircle" class="w-4 h-4 bg-white rounded-full shadow-sm transition-all transform translate-x-0"></span>
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

                    <div class="pt-2 space-y-2 text-[11px] font-bold text-gray-600 flex flex-col">
                        <button onclick="displayModalPopup('Privacy Policy (GDPR)', 'Your dynamic standard system data details are completely covered securely in balance with global technical parameters. All records are guarded with end-to-end database hashing filters.', 'fa-shield-halved')" class="flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-all cursor-pointer w-full text-left px-4 py-3 rounded-xl border border-gray-100/50">
                            <span class="flex items-center gap-2"><i class="fa-regular fa-file-lines text-blue-600 text-sm"></i> View Privacy Policy (GDPR)</span> 
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                        <button onclick="displayModalPopup('Data Usage Agreement', 'By utilizing this specialized evaluation terminal network platform, systemic metrics are computed exclusively inside the private academic institution layer storage infrastructure pool. No client analytics tracking metrics leave the server boundaries.', 'fa-handshake')" class="flex justify-between items-center bg-gray-50 hover:bg-gray-100 transition-all cursor-pointer w-full text-left px-4 py-3 rounded-xl border border-gray-100/50">
                            <span class="flex items-center gap-2"><i class="fa-regular fa-handshake text-blue-600 text-sm"></i> Data Usage Agreement</span> 
                            <i class="fa-solid fa-chevron-right text-[10px] text-gray-400"></i>
                        </button>
                    </div>
                </section>

                <section class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-sm font-bold text-[#0c2340] mb-4">
                            <i class="fa-regular fa-comments text-[#1e5fa7] text-base"></i> Support & Notifications
                        </div>
                        
                        <button type="button" onclick="triggerTechnicalSupportAction()" class="w-full py-3 bg-[#173154] hover:bg-[#0f2139] text-white font-medium text-xs rounded-xl shadow-sm mb-3 flex items-center justify-center gap-2 transition-all cursor-pointer">
                            <i class="fa-regular fa-circle-question text-sm"></i> Technical Support
                        </button>

                        <div class="grid grid-cols-2 gap-3 mb-6">
                            <button type="button" onclick="displayModalPopup('Help Center Gateway', 'Welcome to the Help Center. Search the documentation bank logs, explore platform interface step-by-step components, or forward tickets to administrative workflows.', 'fa-circle-info')" class="py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-100/70 text-gray-600 font-bold text-[10px] rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-blue-600"></i> Help Center
                            </button>
                            <button type="button" onclick="displayModalPopup('User Guides Deck', 'Review step-by-step master operational setup manuals for managing question items, grading exam session structures, and parsing custom multi-format CSV exports.', 'fa-book')" class="py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-100/70 text-gray-600 font-bold text-[10px] rounded-xl transition-all cursor-pointer flex items-center justify-center gap-1.5">
                                <i class="fa-solid fa-book text-blue-600"></i> User Guides
                            </button>
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

    <div id="legalModalWrapper" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center hidden edit-modal z-50 transition-opacity opacity-0">
        <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl border border-gray-100 transform scale-95 transition-all relative">
            <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-4">
                <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg shadow-sm" id="modalHeaderIconContainer">
                    <i class="fa-solid fa-shield-halved" id="modalHeaderIcon"></i>
                </div>
                <div>
                    <h3 id="modalHeaderTitle" class="text-base font-bold text-slate-800 tracking-tight">Policy Title</h3>
                    <p class="text-[10px] uppercase font-bold tracking-wider text-blue-600/80">ExamSystem Document Integration</p>
                </div>
                <button type="button" onclick="dismissModalWindow()" class="absolute top-5 right-5 text-gray-400 hover:text-gray-600 transition-colors cursor-pointer text-sm p-1 hover:bg-gray-50 rounded-lg">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            
            <div class="bg-slate-50/50 rounded-xl p-4 border border-slate-100 max-h-[320px] overflow-y-auto">
                <p id="modalBodyText" class="text-xs text-slate-600 leading-relaxed font-medium">Policy context message details placeholder...</p>
            </div>
            
            <div class="flex justify-end gap-2 mt-5 pt-3 border-t border-gray-100">
                <button type="button" onclick="dismissModalWindow()" class="px-5 py-2.5 bg-slate-800 text-white font-bold text-xs rounded-xl hover:bg-slate-950 shadow-md shadow-slate-900/10 transition-all cursor-pointer">Acknowledge Account Policy</button>
            </div>
        </div>
    </div>

    <script>
        const FALLBACK_DEFAULT_IMAGE = "https://api.dicebear.com/7.x/bottts/svg?seed=Alex";

        document.addEventListener("DOMContentLoaded", function() {
            const isContrastActive = localStorage.getItem('high-contrast-enabled') === 'true';
            const switchToggleBtn = document.getElementById('contrastToggleSwitch');
            const toggleCircle = document.getElementById('switchNodeCircle');
            
            if (isContrastActive && switchToggleBtn && toggleCircle) {
                switchToggleBtn.classList.replace('bg-gray-200', 'bg-blue-600');
                toggleCircle.classList.replace('translate-x-0', 'translate-x-5');
            }
        });

        function triggerAvatarUpload() {
            document.getElementById('hiddenAvatarInput').click();
        }

        function previewUploadedAvatar(inputElement) {
            if (inputElement.files && inputElement.files[0]) {
                const fileReaderInstance = new FileReader();
                fileReaderInstance.onload = function(eventResult) {
                    const base64DataString = eventResult.target.result;
                    document.getElementById('profileImageCanvas').src = base64DataString;
                    document.getElementById('navAvatar').src = base64DataString;
                    document.getElementById('removeAvatarFlag').value = "0";
                };
                fileReaderInstance.readAsDataURL(inputElement.files[0]);
            }
        }

        function removeProfileImage() {
            if(confirm("Are you sure you want to clear your uploaded profile illustration avatar?")) {
                document.getElementById('profileImageCanvas').src = FALLBACK_DEFAULT_IMAGE;
                document.getElementById('navAvatar').src = FALLBACK_DEFAULT_IMAGE;
                document.getElementById('hiddenAvatarInput').value = ""; 
                document.getElementById('removeAvatarFlag').value = "1";
            }
        }

        function displayModalPopup(titleString, parameterDescriptionText, iconClassName = 'fa-shield-halved') {
            document.getElementById('modalHeaderTitle').innerText = titleString;
            document.getElementById('modalBodyText').innerText = parameterDescriptionText;
            
            const iconElement = document.getElementById('modalHeaderIcon');
            if (iconElement) {
                iconElement.className = `fa-solid ${iconClassName}`;
            }
            
            const targetModal = document.getElementById('legalModalWrapper');
            targetModal.classList.remove('hidden');
            
            setTimeout(() => {
                targetModal.classList.remove('opacity-0');
                targetModal.children[0].classList.remove('scale-95');
            }, 20);
        }

        function dismissModalWindow() {
            const targetModal = document.getElementById('legalModalWrapper');
            targetModal.classList.add('opacity-0');
            targetModal.children[0].classList.add('scale-95');
            
            setTimeout(() => {
                targetModal.classList.add('hidden');
            }, 220);
        }

        function toggleHighContrastTheme() {
            const rootElement = document.documentElement;
            const switchToggleBtn = document.getElementById('contrastToggleSwitch');
            const toggleCircle = document.getElementById('switchNodeCircle');

            if (rootElement.classList.contains('high-contrast-mode')) {
                rootElement.classList.remove('high-contrast-mode');
                localStorage.setItem('high-contrast-enabled', 'false');
                
                if(switchToggleBtn) switchToggleBtn.classList.replace('bg-blue-600', 'bg-gray-200');
                if(toggleCircle) toggleCircle.classList.replace('translate-x-5', 'translate-x-0');
            } else {
                rootElement.classList.add('high-contrast-mode');
                localStorage.setItem('high-contrast-enabled', 'true');
                
                if(switchToggleBtn) switchToggleBtn.classList.replace('bg-gray-200', 'bg-blue-600');
                if(toggleCircle) toggleCircle.classList.replace('translate-x-0', 'translate-x-5');
            }
        }

        function triggerTechnicalSupportAction() {
            const userResponseSelection = prompt("Describe the layout bottleneck or terminal issue encountered below:");
            if (userResponseSelection !== null && userResponseSelection.trim() !== "") {
                alert("Ticket safely assigned to your internal evaluation help desk platform group under queue token identifier code ID #" + Math.floor(1000 + Math.random() * 9000));
            }
        }
    </script>
</body>
</html>