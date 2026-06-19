<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Live Monitoring</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-slate-700 antialiased h-screen flex overflow-hidden">

    <aside class="w-64 bg-white border-r border-slate-200 flex flex-col justify-between flex-shrink-0 z-20">
        <div>
            <div class="h-20 flex items-center px-6 gap-2.5">
                <div class="w-9 h-9 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span class="font-bold text-xl text-[#0F172A] tracking-tight">ExamSystem</span>
            </div>

            <nav class="px-4 py-2 space-y-1">
                <a href="{{ route('teacher.dashboard') }}" class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.dashboard') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} rounded-xl transition-all">
                    <i class="fa-solid fa-table-columns w-5 text-center text-lg {{ request()->routeIs('teacher.dashboard') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('teacher.question-bank') }}" class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.question-bank') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                    <i class="fa-solid fa-database w-5 text-center text-lg {{ request()->routeIs('teacher.question-bank') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                    <span>Question Bank</span>
                </a>
                
                <a href="{{ route('teacher.monitoring.show') }}" class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.monitoring.show') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                    <i class="fa-solid fa-desktop w-5 text-center text-lg {{ request()->routeIs('teacher.monitoring.show') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                    <span>Monitoring</span>
                </a>
                
                <a href="{{ route('teacher.grading.show', ['student_id' => 1]) }}" class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.grading.*') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                    <i class="fa-solid fa-file-signature w-5 text-center text-lg {{ request()->routeIs('teacher.grading.*') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                    <span>Grading</span>
                </a>
                
                <a href="{{ route('teacher.analytics') }}" class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.analytics') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                    <i class="fa-solid fa-chart-line w-5 text-center text-lg {{ request()->routeIs('teacher.analytics') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                    <span>Analytics</span>
                </a>

                <a href="{{ route('teacher.settings') }}" class="group flex items-center gap-3 px-4 py-3 {{ request()->routeIs('teacher.settings') ? 'bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10' : 'text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium' }} transition-all rounded-xl">
                    <i class="fa-solid fa-gear w-5 text-center text-lg {{ request()->routeIs('teacher.settings') ? 'text-white' : 'text-[#64748B] group-hover:text-[#1E293B]' }} transition-colors"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-slate-200 flex items-center gap-3 bg-slate-50 m-4 rounded-xl">
            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 bg-white">
                <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0F172A] leading-tight">{{ Auth::user()->full_name ?? 'Teacher Alex' }}</h4>
                <p class="text-xs text-slate-400 font-medium mt-0.5">Senior Faculty</p>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-50">
        
        <header class="h-20 bg-white border-b border-slate-200 px-8 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center gap-6">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Advanced Calculus II</h1>
                    <p class="text-xs font-semibold uppercase tracking-wider text-blue-600">Live Monitoring</p>
                </div>
                <div class="relative w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" placeholder="Search student by name or ID..." class="w-full bg-slate-100 pl-10 pr-4 py-2 rounded-xl text-sm border-0 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="flex items-center gap-4">
                <span class="flex items-center gap-1.5 px-3 py-1 bg-emerald-50 text-emerald-600 text-xs font-bold rounded-full uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Live
                </span>
                
                <div class="relative inline-block">
                    <button onclick="toggleTimeSelector()" class="bg-slate-100 hover:bg-slate-200 px-4 py-2 rounded-xl font-mono text-slate-700 font-bold border border-slate-200 flex items-center gap-2 transition-colors cursor-pointer" title="Click to adjust exam runtime">
                        <i class="fa-regular fa-clock text-blue-600"></i>
                        <span id="examTimer">01:40:54</span>
                        <i class="fa-solid fa-chevron-down text-xs text-slate-400 ml-1"></i>
                    </button>

                    <div id="timeDropdown" class="hidden absolute right-0 mt-2 w-56 bg-white border border-slate-200 rounded-xl shadow-lg py-3 z-50 text-sm">
                        <p class="text-[11px] font-bold text-slate-400 px-4 py-1 uppercase tracking-wider mb-1">Preset Durations</p>
                        <button onclick="setExamDuration(1, 0)" class="w-full text-left px-4 py-2 hover:bg-slate-50 font-medium text-slate-700 flex justify-between"><span>1 Hour</span><span class="text-xs text-slate-400">60m</span></button>
                        <button onclick="setExamDuration(1, 30)" class="w-full text-left px-4 py-2 hover:bg-slate-50 font-medium text-slate-700 flex justify-between"><span>1 Hour 30m</span><span class="text-xs text-slate-400">90m</span></button>
                        <button onclick="setExamDuration(2, 0)" class="w-full text-left px-4 py-2 hover:bg-slate-50 font-medium text-slate-700 flex justify-between"><span>2 Hours</span><span class="text-xs text-slate-400">120m</span></button>
                        <button onclick="setExamDuration(2, 30)" class="w-full text-left px-4 py-2 hover:bg-slate-50 font-medium text-slate-700 flex justify-between"><span>2 Hours 30m</span><span class="text-xs text-slate-400">150m</span></button>
                        
                        <div class="border-t border-slate-100 my-2"></div>
                        
                        <div class="px-4">
                            <label class="text-[11px] font-bold text-slate-400 uppercase block mb-1.5">Custom Duration</label>
                            <div class="flex gap-2 mb-2">
                                <div class="flex-1">
                                    <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Hours</span>
                                    <input type="number" id="customHoursInput" min="0" max="24" value="0" class="w-full bg-slate-50 px-2 py-1.5 rounded border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                                <div class="flex-1">
                                    <span class="text-[10px] text-slate-400 font-bold block mb-0.5">Minutes</span>
                                    <input type="number" id="customMinutesInput" min="0" max="59" value="0" class="w-full bg-slate-50 px-2 py-1.5 rounded border border-slate-200 text-xs font-semibold focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>
                            <button onclick="applyCustomTime()" class="w-full bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold py-2 rounded-lg transition-colors shadow-sm">
                                Set Custom Time
                            </button>
                        </div>
                    </div>
                </div>

                <button type="button" onclick="confirmEndExam()" class="px-5 py-2 border border-red-200 text-red-600 hover:bg-red-50 font-semibold rounded-xl text-sm transition tracking-wide shadow-sm cursor-pointer">
                    End Exam
                </button>
            </div>
        </header>

        <div class="flex-1 flex overflow-hidden">
            
            <div class="flex-1 overflow-y-auto p-8">
                <div class="flex border-b border-slate-200 mb-6 gap-6 text-sm font-semibold">
                    <button class="border-b-2 border-blue-600 pb-3 text-blue-600 px-1">All (42)</button>
                    <button class="text-slate-400 hover:text-slate-600 pb-3 px-1">Suspicious (3)</button>
                    <button class="text-slate-400 hover:text-slate-600 pb-3 px-1">Completed (12)</button>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-6">
                    
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                        <div class="p-4 relative bg-slate-900 aspect-[4/3] flex items-center justify-center group">
                            <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] uppercase font-black px-2 py-0.5 rounded tracking-wider z-10 shadow-sm">Active</span>
                            <video class="webcam-feed w-full h-full object-cover rounded-lg transform scale-x-[-1]" autoplay playsinline muted></video>
                            <div class="camera-placeholder absolute inset-0 bg-[#1e293b] flex flex-col items-center justify-center text-slate-400 gap-3">
                                <div class="w-10 h-10 bg-red-500/10 rounded-full flex items-center justify-center text-red-500"><i class="fa-solid fa-triangle-exclamation text-lg"></i></div>
                                <span class="text-xs font-semibold text-center max-w-[180px]">Camera access denied or unavailable</span>
                            </div>
                        </div>
                        <div class="p-4 border-t border-slate-100 bg-white">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-slate-800 text-sm">Alex Rivera</h4>
                                <span class="text-xs text-slate-400">42m left</span>
                            </div>
                            <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> No incidents recorded</p>
                            <div class="w-full bg-emerald-500 h-1 rounded-full mt-3"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border-2 border-red-500 shadow-md overflow-hidden flex flex-col justify-between">
                        <div class="p-4 relative bg-slate-900 aspect-[4/3] flex items-center justify-center">
                            <span class="absolute top-3 left-3 bg-red-500 text-white text-[10px] uppercase font-black px-2 py-0.5 rounded tracking-wider z-10 shadow-sm">Suspicious</span>
                            <video class="webcam-feed w-full h-full object-cover rounded-lg transform scale-x-[-1]" autoplay playsinline muted></video>
                            <div class="camera-placeholder absolute inset-0 bg-[#1e293b] flex flex-col items-center justify-center text-slate-400 gap-3">
                                <div class="w-10 h-10 bg-red-500/10 rounded-full flex items-center justify-center text-red-500"><i class="fa-solid fa-triangle-exclamation text-lg"></i></div>
                                <span class="text-xs font-semibold text-center max-w-[180px]">Camera access denied or unavailable</span>
                            </div>
                        </div>
                        <div class="p-4 border-t border-slate-100 bg-white">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-slate-800 text-sm">Sarah Chen</h4>
                                <span class="text-xs text-slate-400">42m left</span>
                            </div>
                            <p class="text-xs text-red-500 font-bold flex items-center gap-1.5"><i class="fa-solid fa-triangle-exclamation"></i> Tab switch detected (3x)</p>
                            <div class="w-full bg-red-500 h-1 rounded-full mt-3"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between">
                        <div class="p-4 relative bg-slate-900 aspect-[4/3] flex items-center justify-center">
                            <span class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] uppercase font-black px-2 py-0.5 rounded tracking-wider z-10 shadow-sm">Active</span>
                            <video class="webcam-feed w-full h-full object-cover rounded-lg transform scale-x-[-1]" autoplay playsinline muted></video>
                            <div class="camera-placeholder absolute inset-0 bg-[#1e293b] flex flex-col items-center justify-center text-slate-400 gap-3">
                                <div class="w-10 h-10 bg-red-500/10 rounded-full flex items-center justify-center text-red-500"><i class="fa-solid fa-triangle-exclamation text-lg"></i></div>
                                <span class="text-xs font-semibold text-center max-w-[180px]">Camera access denied or unavailable</span>
                            </div>
                        </div>
                        <div class="p-4 border-t border-slate-100 bg-white">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-slate-800 text-sm">Marcus Thorne</h4>
                                <span class="text-xs text-slate-400">42m left</span>
                            </div>
                            <p class="text-xs text-emerald-600 font-semibold flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> No incidents recorded</p>
                            <div class="w-full bg-emerald-500 h-1 rounded-full mt-3"></div>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between opacity-70">
                        <div class="p-4 relative bg-slate-100 aspect-[4/3] flex flex-col items-center justify-center text-slate-400 gap-1">
                            <span class="absolute top-3 left-3 bg-slate-500 text-white text-[10px] uppercase font-black px-2 py-0.5 rounded tracking-wider z-10">Completed</span>
                            <i class="fa-solid fa-user-check text-3xl text-slate-300"></i>
                            <span class="text-xs font-medium">Feed disconnected</span>
                        </div>
                        <div class="p-4 border-t border-slate-100 bg-slate-50">
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="font-bold text-slate-700 text-sm">Elena Rossi</h4>
                                <span class="text-xs text-slate-400">Finished 5m ago</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium flex items-center gap-1.5"><i class="fa-solid fa-file-circle-check"></i> Exam submitted</p>
                            <div class="w-full bg-slate-300 h-1 rounded-full mt-3"></div>
                        </div>
                    </div>

                </div>
            </div>

            <aside class="w-80 bg-white border-l border-slate-200 flex flex-col justify-between flex-shrink-0">
                <div class="flex-1 overflow-y-auto">
                    <div class="p-6 border-b border-slate-100">
                        <h3 class="font-bold text-sm uppercase tracking-wider text-slate-800">Incident Log</h3>
                    </div>

                    <div class="divide-y divide-slate-100">
                        <div class="p-4 hover:bg-slate-50 transition">
                            <div class="flex justify-between items-start text-xs mb-1">
                                <span class="font-bold text-slate-800">Sarah Chen</span>
                                <span class="text-slate-400 font-mono">10:42:15</span>
                            </div>
                            <p class="text-xs text-red-600 font-semibold mb-3">Unauthorized tab switch detected</p>
                            <div class="flex gap-4 text-xs font-bold">
                                <button onclick="reviewIncident('Sarah Chen')" class="text-blue-600 hover:text-blue-800 cursor-pointer">Review</button>
                                <button onclick="ignoreIncident()" class="text-slate-400 hover:text-slate-600 cursor-pointer">Ignore</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 bg-slate-50">
                    <a href="{{ route('teacher.monitoring.exportLog') }}" class="w-full py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-300 rounded-xl font-semibold text-sm transition tracking-wide flex items-center justify-center gap-2 shadow-sm">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> Export Session Log
                    </a>
                </div>
            </aside>

        </div>
    </main>

    <script>
        // 1. DYNAMIC DIGITAL TIMER SELECTION ENGINE
        let totalSeconds = (1 * 3600) + (40 * 60) + 54; 
        const timerDisplay = document.getElementById('examTimer');
        const dropdownElement = document.getElementById('timeDropdown');
        let countdownInterval;

        function startTimer() {
            if (countdownInterval) clearInterval(countdownInterval);

            countdownInterval = setInterval(() => {
                if (totalSeconds <= 0) {
                    clearInterval(countdownInterval);
                    timerDisplay.textContent = "00:00:00";
                    alert("Exam monitoring time frame has expired.");
                    return;
                }
                totalSeconds--;

                let hrs = Math.floor(totalSeconds / 3600);
                let mins = Math.floor((totalSeconds % 3600) / 60);
                let secs = totalSeconds % 60;

                timerDisplay.textContent = 
                    `${String(hrs).padStart(2, '0')}:${String(mins).padStart(2, '0')}:${String(secs).padStart(2, '0')}`;
            }, 1000);
        }

        function toggleTimeSelector() {
            dropdownElement.classList.toggle('hidden');
        }

        function setExamDuration(hours, minutes) {
            totalSeconds = (parseInt(hours) * 3600) + (parseInt(minutes) * 60);
            startTimer();
            dropdownElement.classList.add('hidden');
        }

        function applyCustomTime() {
            const hrsInput = document.getElementById('customHoursInput').value || 0;
            const minsInput = document.getElementById('customMinutesInput').value || 0;
            
            if (hrsInput >= 0 && minsInput >= 0 && (hrsInput > 0 || minsInput > 0)) {
                setExamDuration(hrsInput, minsInput);
            }
        }

        window.addEventListener('click', function(e) {
            if (!e.target.closest('#examTimer') && !e.target.closest('#timeDropdown')) {
                dropdownElement.classList.add('hidden');
            }
        });

        // 2. HARDWARE SYSTEM WEBCAM AGENT
        async function initWebcamFeeds() {
            const videoElements = document.querySelectorAll('.webcam-feed');
            const placeholders = document.querySelectorAll('.camera-placeholder');

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { width: 400, height: 300 }, 
                    audio: false 
                });
                
                videoElements.forEach((video, index) => {
                    video.srcObject = stream;
                    video.onloadedmetadata = () => {
                        video.play();
                        if (placeholders[index]) placeholders[index].style.opacity = '0';
                        setTimeout(() => { if (placeholders[index]) placeholders[index].remove(); }, 300);
                    };
                });
            } catch (err) {
                console.warn("Webcam blocked or unconfigured:", err);
            }
        }

        // Initialization triggers
        window.addEventListener('DOMContentLoaded', () => {
            startTimer();
            initWebcamFeeds();
        });

        // 3. BASELINE BUTTON COMMAND HANDLERS
        // MODIFIED: Intercepts form submit and transfers routing directly to confirmation screen
        function confirmEndExam() {
            window.location.href = "{{ route('teacher.monitoring.endConfirmation') }}";
        }

        function reviewIncident(studentName) {
            alert(`Opening historical video playback reference context for: ${studentName}`);
        }

        function ignoreIncident() {
            alert("Incident flag updated context status to ignored.");
        }
    </script>
</body>
</html>