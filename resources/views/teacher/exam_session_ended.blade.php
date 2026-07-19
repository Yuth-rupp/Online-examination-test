<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Session Ended</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-[#334155] antialiased min-h-screen flex flex-col justify-between">

    <header class="bg-white border-b border-slate-200 px-12 py-4 flex items-center justify-between shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 bg-[#1D4ED8] rounded-lg flex items-center justify-center text-white shadow-sm">
                <i class="fa-solid fa-graduation-cap text-sm"></i>
            </div>
            <span class="font-bold text-lg text-[#0F172A] tracking-tight">ExamSystem</span>
        </div>
        <div class="flex items-center gap-4 text-slate-500">
            <button onclick="triggerNotificationAlert()" class="p-2 hover:bg-slate-50 rounded-full cursor-pointer transition-colors relative">
                <i class="fa-regular fa-bell text-lg"></i>
                <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-blue-600 rounded-full"></span>
            </button>
            <div class="w-8 h-8 rounded-full bg-slate-200 border border-slate-300 overflow-hidden flex items-center justify-center">
                <img src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="User Avatar">
            </div>
        </div>
    </header>

    <main class="flex-1 max-w-[1500px] w-full mx-auto px-12 py-8 flex flex-col gap-6">
        
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-[#0F172A] tracking-tight">Exam Session Ended</h1>
                <p class="text-sm font-medium text-slate-400 mt-1">Advanced Calculus II • Section 04A</p>
            </div>
            <button onclick="navigateTo('{{ route('teacher.dashboard') }}')" class="px-5 py-2.5 bg-[#e2e8f0] text-[#1e293b] text-sm font-bold rounded-xl hover:bg-[#cbd5e1] transition-all cursor-pointer shadow-xs">
                Return to Dashboard
            </button>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 space-y-6">
                
                <div class="bg-white border border-slate-200 rounded-2xl p-6 flex items-start gap-5 shadow-xs relative overflow-hidden">
                    <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center shrink-0 border border-blue-100">
                        <i class="fa-solid fa-check text-base"></i>
                    </div>
                    <div class="space-y-1">
                        <div class="flex items-center gap-3">
                            <h3 class="text-base font-bold text-[#0F172A]">The exam session has been closed</h3>
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-black tracking-wider bg-[#1D4ED8] text-white uppercase">Ended Successfully</span>
                        </div>
                        <p class="text-sm text-slate-500 leading-relaxed max-w-xl">
                            All active student attempts have been submitted and monitoring records have been archived successfully. The integrity scan was completed with no critical system failures.
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="bg-white border-l-4 border-l-slate-400 border border-slate-200 rounded-xl p-5 shadow-xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Students</p>
                        <p class="text-3xl font-extrabold text-[#0F172A] mt-2 font-mono">42</p>
                    </div>
                    <div class="bg-white border-l-4 border-l-blue-600 border border-slate-200 rounded-xl p-5 shadow-xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Final Submissions</p>
                        <p class="text-3xl font-extrabold text-[#0F172A] mt-2 font-mono">38</p>
                    </div>
                    <div class="bg-white border-l-4 border-l-red-500 border border-slate-200 rounded-xl p-5 shadow-xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Flagged Incidents</p>
                        <p class="text-3xl font-extrabold text-[#0F172A] mt-2 font-mono">6</p>
                    </div>
                    <div class="bg-white border-l-4 border-l-neutral-800 border border-slate-200 rounded-xl p-5 shadow-xs">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Manual Reviews</p>
                        <p class="text-3xl font-extrabold text-[#0F172A] mt-2 font-mono">4</p>
                    </div>
                </div>

                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs">
                    <div class="flex items-center gap-2 text-slate-400 text-xs font-bold uppercase tracking-wider mb-5">
                        <i class="fa-regular fa-folder-open text-sm"></i>
                        <h3>Archive Details</h3>
                    </div>
                    <div class="grid grid-cols-2 sm:grid-cols-5 gap-6 text-sm">
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block">End Time</span>
                            <span class="font-semibold text-[#0F172A] block mt-1 font-mono">01:42:15 PM</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block">Duration</span>
                            <span class="font-semibold text-[#0F172A] block mt-1">1 hr 42 mins</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block">Archive Status</span>
                            <span class="inline-flex items-center gap-1.5 font-bold text-emerald-600 mt-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Saved
                            </span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block">Lock Status</span>
                            <span class="font-semibold text-[#0F172A] block mt-1">Finalized</span>
                        </div>
                        <div>
                            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wide block">Last Synced</span>
                            <span id="syncTimestamp" class="font-semibold text-blue-600 block mt-1 cursor-pointer hover:underline" onclick="refreshTelemetryStream()">Just now</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Post-Session Actions</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        
                        <div onclick="reviewSuspiciousGrid()" class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-xs transition-all cursor-pointer group">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-user-group text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition-colors">Review Suspicious Cases</h4>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">6 students require attention</p>
                            </div>
                        </div>

                        <div onclick="navigateTo('{{ route('teacher.monitoring.exportLog') }}')" class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-xs transition-all cursor-pointer group">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-file-arrow-down text-lg"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition-colors">Export Monitoring Report</h4>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">PDF & CSV formats available</p>
                            </div>
                        </div>

                        <div onclick="navigateTo('{{ route('teacher.grading.show', ['student_id' => 1]) }}')" class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-xs transition-all cursor-pointer group">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-signature text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition-colors">Open Grading Workspace</h4>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Start manual scoring</p>
                            </div>
                        </div>

                        <div onclick="navigateTo('{{ route('teacher.analytics') }}')" class="bg-white border border-slate-200 rounded-2xl p-5 flex items-center gap-4 hover:border-blue-300 hover:shadow-xs transition-all cursor-pointer group">
                            <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                <i class="fa-solid fa-chart-simple text-base"></i>
                            </div>
                            <div>
                                <h4 class="text-sm font-bold text-[#0F172A] group-hover:text-blue-600 transition-colors">View Exam Analytics</h4>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Score distributions and trends</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white border border-slate-200 rounded-2xl p-6 shadow-xs space-y-6">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Post-Exam Overview</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between items-baseline">
                            <span class="text-xs font-bold text-slate-500">Completion Rate</span>
                            <span class="text-lg font-black text-[#1D4ED8] font-mono">91%</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                            <div class="bg-[#1D4ED8] h-2.5 rounded-full" style="width: 91%"></div>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-xl font-extrabold text-[#0F172A] font-mono">6</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mt-1">Students Flagged</p>
                        </div>
                        <div class="bg-slate-50 border border-slate-100 rounded-xl p-4">
                            <p class="text-xl font-extrabold text-[#0F172A] font-mono">2</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide mt-1">Disconnections</p>
                        </div>
                    </div>
                    <div class="border border-slate-100 rounded-xl p-4 space-y-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Primary Risk Factor</p>
                        <div class="flex items-center gap-2 text-sm font-bold text-[#0F172A] mt-1">
                            <i class="fa-solid fa-window-restore text-amber-500"></i>
                            <span>Tab Switching</span>
                        </div>
                    </div>
                    <div class="bg-amber-50/60 border border-amber-100 rounded-xl p-3 flex justify-between items-center text-xs font-bold">
                        <span class="text-amber-800 uppercase tracking-wider text-[10px]">Integrity Status</span>
                        <span class="px-2.5 py-0.5 bg-amber-100 text-amber-800 rounded font-extrabold uppercase text-[10px] tracking-wide">Moderate Risk</span>
                    </div>
                </div>

                <div class="bg-white border-2 border-blue-600 rounded-2xl p-6 shadow-xs flex flex-col justify-between space-y-6">
                    <div class="space-y-4">
                        <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 text-blue-600 flex items-center justify-center">
                            <i class="fa-regular fa-lightbulb text-sm"></i>
                        </div>
                        <div class="space-y-1">
                            <h4 class="text-sm font-extrabold text-[#0F172A]">Recommended Action</h4>
                            <p class="text-xs font-medium text-slate-500 leading-relaxed">
                                Review suspicious cases before grading to verify academic integrity concerns. Some flags may be false positives from network drops.
                            </p>
                        </div>
                    </div>
                    <button onclick="reviewIntegrityLog()" class="w-full py-2.5 border border-blue-600 text-blue-600 text-xs font-bold rounded-xl hover:bg-blue-50 transition-colors cursor-pointer shadow-2xs">
                        Review Integrity Log
                    </button>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-slate-200 px-12 py-5 flex flex-col sm:flex-row items-center justify-between gap-4 shrink-0 shadow-md">
        <div class="flex items-center gap-6 text-sm font-bold text-slate-500">
            <button onclick="navigateTo('{{ route('teacher.monitoring.show') }}')" class="hover:text-[#0F172A] flex items-center gap-2 cursor-pointer transition-colors bg-transparent border-0">
                <i class="fa-solid fa-arrow-left text-xs"></i> Back to Monitoring
            </button>
            <button onclick="navigateTo('{{ route('teacher.monitoring.exportLog') }}')" class="hover:text-[#0F172A] flex items-center gap-2 cursor-pointer transition-colors bg-transparent border-0">
                <i class="fa-solid fa-arrow-up-from-bracket text-xs"></i> Export Session Log
            </button>
        </div>
        <button onclick="navigateTo('{{ route('teacher.grading.show', ['student_id' => 1]) }}')" class="w-full sm:w-auto px-6 py-2.5 bg-[#1D4ED8] text-white text-sm font-bold rounded-xl hover:bg-blue-800 transition-all flex items-center justify-center gap-2 cursor-pointer shadow-sm">
            <span>Continue to Grading</span>
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </button>
    </footer>

    <script>
        function navigateTo(url) {
            if(url && url !== '' && !url.includes('route(')) {
                window.location.href = url;
            } else {
                alert("Simulating redirect to workspace dashboard node: " + url);
            }
        }
        function reviewSuspiciousGrid() { alert("Opening review queue for the 6 flagged anomalies."); }
        function reviewIntegrityLog() { alert("Streaming complete proctor incident verification logs report..."); }
        function triggerNotificationAlert() { alert("No new notifications at this time."); }
        function refreshTelemetryStream() {
            const label = document.getElementById('syncTimestamp');
            label.textContent = "Syncing logs...";
            label.className = "font-bold text-amber-500 block mt-1 font-mono animate-pulse";
            setTimeout(() => {
                const now = new Date();
                const timeString = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });
                label.textContent = "Updated at " + timeString;
                label.className = "font-semibold text-slate-400 block mt-1 font-mono";
            }, 800);
        }
    </script>
</body>
</html>