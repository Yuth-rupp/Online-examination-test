<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End Examination Session</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#fcfbfc] text-gray-800 antialiased min-h-screen flex flex-col justify-between">

    <header class="bg-white border-b border-gray-200 px-8 py-4 flex items-center justify-between">
        <div class="flex items-center space-x-3">
            <h1 class="text-xl font-bold text-gray-900">End Examination Session</h1>
            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-50 text-red-600 border border-red-200">
                <span class="w-1.5 h-1.5 mr-1.5 rounded-full bg-red-500 animate-pulse"></span>
                LIVE
            </span>
        </div>
        <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2 bg-gray-50 border border-gray-200 rounded-lg px-3 py-1.5 text-sm font-medium text-gray-700">
                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>01:42:15</span>
            </div>
            <button onclick="returnToMonitoring()" class="inline-flex items-center space-x-2 bg-white border border-gray-300 rounded-lg px-4 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-blue-600 transition-colors shadow-sm cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                <span>Return to Monitoring</span>
            </button>
        </div>
    </header>

    <div class="px-8 py-2 border-b border-gray-100 bg-[#fbfbfa]">
        <p class="text-xs text-gray-500 font-medium">Advanced Calculus II • Section 04A</p>
    </div>

    <main class="flex-1 max-w-[1600px] w-full mx-auto px-8 py-6 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-red-50 border border-red-200 rounded-xl p-5 flex items-start space-x-4">
                <div class="p-2 bg-red-100 rounded-lg text-red-600 shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">You are about to end this exam session</h3>
                    <p class="text-sm text-gray-600 mt-1 leading-relaxed">
                        Ending this session will immediately submit all active student attempts. This action is irreversible and will stop all ongoing exam activities for the current roster.
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div onclick="filterTable('all')" class="bg-white border border-gray-200 hover:border-gray-400 cursor-pointer rounded-xl p-5 shadow-xs transition-all">
                    <p class="text-[11px] font-bold text-gray-500 tracking-wider uppercase">Total Students</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">42</p>
                </div>
                <div onclick="filterTable('completed')" class="bg-white border border-gray-200 hover:border-gray-400 cursor-pointer rounded-xl p-5 shadow-xs transition-all">
                    <p class="text-[11px] font-bold text-gray-500 tracking-wider uppercase">Completed</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">12</p>
                </div>
                <div onclick="filterTable('active')" class="bg-white border border-gray-200 hover:border-gray-400 cursor-pointer rounded-xl p-5 shadow-xs transition-all">
                    <p class="text-[11px] font-bold text-gray-500 tracking-wider uppercase">Still Active</p>
                    <p class="text-3xl font-extrabold text-gray-900 mt-2">26</p>
                </div>
                <div onclick="reviewSuspiciousCases()" class="bg-red-50/40 border border-red-200 hover:border-red-400 cursor-pointer rounded-xl p-5 shadow-xs transition-all animate-pulse">
                    <p class="text-[11px] font-bold text-red-700 tracking-wider uppercase">Suspicious Cases</p>
                    <p class="text-3xl font-extrabold text-red-600 mt-2">04</p>
                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between gap-4 shadow-xs">
                <div class="relative w-full sm:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="studentSearch" onkeyup="searchStudent()" placeholder="Quick filter student name..." class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-hidden focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                </div>
                <div class="flex items-center space-x-2 w-full sm:w-auto justify-end">
                    <span class="text-xs text-gray-400 font-medium">Bulk Action:</span>
                    <button onclick="bulkResolveSuspicious()" class="text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 px-3 py-1.5 rounded-lg hover:bg-amber-100 transition-colors cursor-pointer">
                        Ignore All Minor Logs
                    </button>
                </div>
            </div>

            <div id="sessionTableContainer" class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-xs transition-all">
                <div class="p-4 border-b border-gray-200 flex justify-between items-center bg-white">
                    <h2 class="text-sm font-bold text-gray-900">Active & Pending Sessions</h2>
                    <button onclick="viewAllDetails()" class="text-xs font-semibold text-blue-600 hover:text-blue-800 hover:underline bg-transparent border-0 cursor-pointer">
                        View All Details
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="studentTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Student Name</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Activity</th>
                                <th scope="col" class="px-6 py-3 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Incidents</th>
                                <th scope="col" class="px-6 py-3 text-center text-[10px] font-bold text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200 text-sm">
                            <tr id="row-sarah" class="student-row item-suspicious hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 target-name">Sarah Chen</div>
                                    <div class="text-[11px] text-gray-400 font-medium">ID: ST-8821</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold tracking-wide bg-red-50 text-red-600 border border-red-100 uppercase">Suspicious</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">Multiple tab switches</td>
                                <td class="px-6 py-4 whitespace-nowrap text-red-600 font-bold flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    <span>3</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button onclick="manageSingleStudent('Sarah Chen')" class="text-blue-600 hover:text-blue-800 font-semibold text-xs px-2.5 py-1 bg-gray-50 border border-gray-200 rounded hover:bg-gray-100 cursor-pointer">
                                        Review Log
                                    </button>
                                </td>
                            </tr>
                            <tr class="student-row item-active hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 target-name">Alex Rivera</div>
                                    <div class="text-[11px] text-gray-400 font-medium">ID: ST-1104</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold tracking-wide bg-gray-100 text-gray-700 border border-gray-200 uppercase">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">Question 24/40</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">0</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button onclick="manageSingleStudent('Alex Rivera')" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-2.5 py-1 bg-gray-50 rounded cursor-pointer">
                                        Options
                                    </button>
                                </td>
                            </tr>
                            <tr class="student-row item-active hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 target-name">Marcus Thorne</div>
                                    <div class="text-[11px] text-gray-400 font-medium">ID: ST-9023</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold tracking-wide bg-gray-100 text-gray-700 border border-gray-200 uppercase">Active</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-600 font-medium">Question 38/40</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">0</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button onclick="manageSingleStudent('Marcus Thorne')" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-2.5 py-1 bg-gray-50 rounded cursor-pointer">
                                        Options
                                    </button>
                                </td>
                            </tr>
                            <tr class="student-row item-completed hover:bg-gray-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-bold text-gray-900 target-name">Elena Rossi</div>
                                    <div class="text-[11px] text-gray-400 font-medium">ID: ST-4451</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold tracking-wide bg-neutral-800 text-white uppercase">Completed</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">Submitted @ 14:10</td>
                                <td class="px-6 py-4 whitespace-nowrap text-gray-500 font-medium">0</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button onclick="manageSingleStudent('Elena Rossi')" class="text-gray-600 hover:text-gray-900 font-medium text-xs px-2.5 py-1 bg-gray-50 rounded cursor-pointer">
                                        View Paper
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-[#f3f4f3] rounded-2xl p-6 border border-gray-200 h-full flex flex-col justify-between">
                <div>
                    <div class="flex items-center space-x-2 text-gray-700 font-bold text-sm mb-6">
                        <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <h2>Session Impact Summary</h2>
                    </div>

                    <div class="mb-8">
                        <div class="flex justify-between items-baseline mb-2">
                            <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Estimated Submissions</span>
                            <span class="text-2xl font-black text-gray-900">38</span>
                        </div>
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-gray-900 h-2 rounded-full" style="width: 90.4%"></div>
                        </div>
                    </div>

                    <ul class="space-y-6">
                        <li class="flex items-start space-x-3 cursor-pointer group" onclick="filterTable('incomplete')">
                            <span class="w-2 h-2 rounded-full bg-black shrink-0 mt-1.5 group-hover:scale-125 transition-transform"></span>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wide group-hover:text-blue-600 transition-colors">Incomplete Attempts</h4>
                                <p class="text-sm text-gray-600 font-medium mt-0.5">4 students have &lt; 50% progress</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-3 cursor-pointer group" onclick="filterTable('review')">
                            <span class="w-2 h-2 rounded-full bg-gray-400 shrink-0 mt-1.5 group-hover:scale-125 transition-transform"></span>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900 uppercase tracking-wide group-hover:text-blue-600 transition-colors">Manual Reviews Required</h4>
                                <p class="text-sm text-gray-600 font-medium mt-0.5">6 students require grade checks</p>
                            </div>
                        </li>
                        <li class="flex items-start space-x-3 cursor-pointer group" onclick="reviewSuspiciousCases()">
                            <span class="w-2 h-2 rounded-full bg-red-600 shrink-0 mt-1.5 group-hover:scale-125 transition-transform"></span>
                            <div>
                                <h4 class="text-xs font-bold text-red-600 uppercase tracking-wide group-hover:underline">Integrity Risk</h4>
                                <p class="text-sm text-gray-600 font-medium mt-0.5">High risk flagged for Section 04A</p>
                            </div>
                        </li>
                    </ul>
                </div>

                <div class="mt-12 bg-white border border-gray-200 rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 italic leading-relaxed">
                        "Ending now will finalize all scores based on the current snapshot. No further student edits will be accepted."
                    </p>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 px-8 py-5 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-left">
            <p class="text-xs font-bold text-gray-900 uppercase tracking-wider">Confirmation Required</p>
            <p class="text-xs text-gray-500 mt-0.5 font-medium">Once ended, this session cannot be reopened without administrator action.</p>
        </div>
        <div class="flex items-center space-x-3 w-full sm:w-auto justify-end">
            <button onclick="returnToMonitoring()" class="px-5 py-2.5 bg-white border border-gray-300 text-sm font-bold text-gray-700 rounded-lg hover:bg-gray-50 transition-colors shadow-xs cursor-pointer">
                Cancel
            </button>
            <button onclick="reviewSuspiciousCases()" class="px-5 py-2.5 bg-amber-50 border border-amber-300 text-sm font-bold text-amber-800 rounded-lg hover:bg-amber-100 transition-colors shadow-xs cursor-pointer">
                Review Suspicious Cases First
            </button>
            
            <form id="endExamForm" action="{{ route('teacher.monitoring.endExam') }}" method="POST">
                @csrf
                <button type="submit" class="px-5 py-2.5 bg-[#b91c1c] text-sm font-bold text-white rounded-lg hover:bg-red-800 transition-colors shadow-sm cursor-pointer">
                    End Exam Now
                </button>
            </form>
        </div>
    </footer>

    <script>
        // 1. Action: Return to Live Monitoring Room Screen
        function returnToMonitoring() {
            window.location.href = "{{ route('teacher.monitoring.show') }}"; 
        }

        // 2. Action: View All Details Deep-Dive Panel
        function viewAllDetails() {
            alert("Opening deep inspection modal for all 42 concurrent test streams.");
        }

        // 3. Action: Highlight and focus row anomaly instantly
        function reviewSuspiciousCases() {
            document.getElementById('studentSearch').value = ""; 
            filterTable('suspicious');
            
            const targetRow = document.getElementById('row-sarah');
            if(targetRow) {
                targetRow.classList.add('bg-red-100/80');
                targetRow.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => targetRow.classList.remove('bg-red-100/80'), 2500);
            }
        }

        // 4. Action: View Context Data Sheets for Singular Student Nodes
        function manageSingleStudent(name) {
            alert("Opening single focus timeline viewer details for: " + name);
        }

        // 5. Action: Bulk Resolve Anomalies Log Group
        function bulkResolveSuspicious() {
            if(confirm("Are you sure you want to mark all 4 open monitoring anomalies as reviewed?")) {
                alert("Anomalies updated. Flags cleared successfully.");
            }
        }

        // 6. Action: Dynamic Client-Side Search Engine Filter
        function searchStudent() {
            const query = document.getElementById('studentSearch').value.toLowerCase();
            const rows = document.querySelectorAll('.student-row');
            
            rows.forEach(row => {
                const studentName = row.querySelector('.target-name').textContent.toLowerCase();
                row.style.display = studentName.includes(query) ? "" : "none";
            });
        }

        // 7. Action: Grid/Sidebar Category Metrics Tab Sorter Matrix
        function filterTable(type) {
            const rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                if (type === 'all') row.style.display = "";
                else if (type === 'suspicious' && row.classList.contains('item-suspicious')) row.style.display = "";
                else if (type === 'active' && row.classList.contains('item-active')) row.style.display = "";
                else if (type === 'completed' && row.classList.contains('item-completed')) row.style.display = "";
                else row.style.display = "none";
            });
        }
    </script>
</body>
</html>