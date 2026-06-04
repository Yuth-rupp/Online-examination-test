<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Analytics Overview</title>
    <!-- Core Engine CSS Stylesheet Platform Loader -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .flatpickr-calendar {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05) !important;
            border: 1px solid #E2E8F0 !important;
            border-radius: 12px !important;
        }
        .flatpickr-day.selected {
            background: #2563EB !important;
            border-color: #2563EB !important;
        }

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
        .high-contrast-mode input {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
            border: 2px solid #4B5563 !important;
        }
        .high-contrast-mode .bg-gray-50 {
            background-color: #1F2937 !important;
            color: #FFFFFF !important;
        }
        .high-contrast-mode .text-[#0F172A] {
            color: #F9FAFB !important;
        }
        .high-contrast-mode .text-[#64748B] {
            color: #9CA3AF !important;
        }
    </style>
    <script>
        if (localStorage.getItem('high-contrast-enabled') === 'true') {
            document.documentElement.classList.add('high-contrast-mode');
        }
    </script>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex">

    <!-- COLUMN 1: LEFT ADMIN MANAGEMENT CONTROL NAVIGATION SIDEBAR -->
    <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between hidden md:flex select-none">
        <div>
            <!-- 🏢 System Brand Logo Area -->
            <div class="flex items-center gap-3 px-7 pt-6 pb-8">
                <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                    </svg>
                </div>
                <span class="text-lg font-bold text-gray-900 tracking-tight">ExamSystem</span>
            </div>
            
            <nav class="px-4 space-y-1">
                <!-- 📊 Link: Dashboard -->
                <a href="{{ route('teacher.dashboard') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v4a2 2 0 01-2 2h-2a2 2 0 01-2-2v-4z"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>

                <!-- 🏦 Link: Question Bank -->
                <a href="{{ route('teacher.question-bank') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>Question Bank</span>
                </a>

                <!-- 👁️ Link: Monitoring -->
                <a href="#" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>Monitoring</span>
                </a>

                <!-- 📝 Link: Grading Panel (Fully Operational Route Attached) -->
                <a href="{{ route('teacher.grading.show', 1) }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Grading</span>
                </a>

                <!-- 📈 Link: Analytics Active Style Block (Matches Master Layout Framework) -->
                <div class="flex items-center gap-3.5 text-white bg-blue-600 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight shadow-sm shadow-blue-600/10">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    <span>Analytics</span>
                </div>

                <!-- ⚙️ Link: Settings -->
                <a href="{{ route('teacher.settings') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <!-- 📌 BOTTOM ACCOUNT FOOTER UNIT -->
        <div class="p-4 border-t border-[#E2E8F0] flex items-center gap-3 bg-[#F8FAFC] m-4 rounded-xl">
            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 bg-white">
                <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div class="flex flex-col">
                <h4 class="text-sm font-bold text-[#0F172A] leading-tight">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</h4>
                <p class="text-[11px] text-[#94A3B8] font-medium mt-0.5">Senior Faculty</p>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0">
        <header class="h-16 bg-white border-b border-[#E2E8F0] px-8 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-[#0F172A]">Analytics Overview</h1>
            
            <div class="flex items-center gap-6">
                <div class="relative w-64">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-[#94A3B8] text-sm"></i>
                    <input type="text" placeholder="Search data points..." class="w-full pl-9 pr-4 py-1.5 bg-[#F1F5F9] border-none rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                
                <button class="text-[#64748B] hover:text-gray-900 relative">
                    <i class="fa-solid fa-bell text-lg"></i>
                    <span class="absolute top-0 right-0 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>
                
                <div class="relative flex items-center">
                    <button id="calendarBtn" class="text-[#64748B] hover:text-gray-900 cursor-pointer p-1 rounded-lg hover:bg-gray-100 transition">
                        <i class="fa-solid fa-calendar text-lg"></i>
                    </button>
                    <input type="text" id="calendarInput" class="absolute inset-0 opacity-0 w-0 pointer-events-none">
                </div>
                
                <div class="flex items-center gap-3 border-l pl-6 border-[#E2E8F0]">
                    <div class="text-right">
                        <h4 class="text-xs font-bold text-[#0F172A]">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</h4>
                        <p class="text-[10px] text-[#94A3B8] tracking-wider uppercase font-semibold">Faculty User</p>
                    </div>
                    <div class="w-9 h-9 rounded-full overflow-hidden border border-gray-200 bg-white">
                        <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
                    </div>
                </div>
            </div>
        </header>

        <div class="p-8 space-y-8 max-w-[1400px] w-full mx-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-[#0F172A] tracking-tight">Semester Performance</h2>
                    <p class="text-sm text-[#64748B]">Detailed real-time metrics for the 2024 Academic Year.</p>
                </div>
                <button onclick="exportDashboardData()" class="flex items-center gap-2 px-4 py-2.5 bg-[#E2E8F0] hover:bg-[#CBD5E1] text-[#0F172A] font-medium text-sm rounded-lg transition shadow-sm cursor-pointer">
                    <i class="fa-solid fa-download"></i> Export Dataset (CSV)
                </button>
            </div>

            <!-- CARDS Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#EFF6FF] text-[#2563EB] rounded-xl"><i class="fa-solid fa-users text-lg"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#DCFCE7] text-[#15803D] rounded-full">+12%</span>
                    </div>
                    <p class="text-xs font-medium text-[#64748B]">Total Students</p>
                    <h3 class="text-2xl font-bold text-[#0F172A] mt-1">{{ number_format($totalStudentsCount ?? 12480) }}</h3>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#EFF6FF] text-[#2563EB] rounded-xl"><i class="fa-solid fa-book-open text-lg"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#DCFCE7] text-[#15803D] rounded-full">+5%</span>
                    </div>
                    <p class="text-xs font-medium text-[#64748B]">Active Courses</p>
                    <h3 class="text-2xl font-bold text-[#0F172A] mt-1">{{ $activeCoursesCount ?? 156 }}</h3>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#EFF6FF] text-[#2563EB] rounded-xl"><i class="fa-solid fa-user-check text-lg"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#FEE2E2] text-[#B91C1C] rounded-full">-0.8%</span>
                    </div>
                    <p class="text-xs font-medium text-[#64748B]">Avg. Attendance</p>
                    <h3 class="text-2xl font-bold text-[#0F172A] mt-1">{{ $avgAttendanceRate ?? '94.2%' }}</h3>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0] relative">
                    <div class="flex justify-between items-start mb-4">
                        <div class="p-2.5 bg-[#EFF6FF] text-[#2563EB] rounded-xl"><i class="fa-solid fa-graduation-cap text-lg"></i></div>
                        <span class="text-xs font-semibold px-2 py-0.5 bg-[#DCFCE7] text-[#15803D] rounded-full">+2%</span>
                    </div>
                    <p class="text-xs font-medium text-[#64748B]">Graduation Rate</p>
                    <h3 class="text-2xl font-bold text-[#0F172A] mt-1">{{ $graduationRate ?? '89%' }}</h3>
                </div>
            </div>

            <!-- GRAPH ELEMENTS -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0]">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-[#0F172A]">Student Enrollment Trend</h3>
                            <p class="text-xs text-[#64748B] mt-0.5">Monthly progression for 2025</p>
                        </div>
                        <span class="text-xs font-semibold text-[#15803D] bg-[#DCFCE7] px-2 py-1 rounded-md">+15.4%</span>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="enrollmentLineChart"></canvas>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-[#E2E8F0]">
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="font-bold text-[#0F172A]">Monthly Revenue (K)</h3>
                            <p class="text-xs text-[#64748B] mt-0.5">Tuition and grant income</p>
                        </div>
                        <span class="text-xs font-semibold text-[#15803D] bg-[#DCFCE7] px-2 py-1 rounded-md">+8.2%</span>
                    </div>
                    <div class="h-64 relative">
                        <canvas id="revenueBarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
        const chartLabels = {!! json_encode($monthsLabels ?? ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']) !!};
        const enrollmentDataPoints = {!! json_encode($enrollmentChartData ?? [30, 34, 56, 29, 62, 45]) !!};
        const revenueDataPoints = {!! json_encode($revenueChartData ?? [46, 35, 76, 41, 81, 48]) !!};

        // Line Chart Setup
        const ctxLine = document.getElementById('enrollmentLineChart').getContext('2d');
        const blueGradient = ctxLine.createLinearGradient(0, 0, 0, 240);
        blueGradient.addColorStop(0, 'rgba(29, 78, 216, 0.12)');
        blueGradient.addColorStop(1, 'rgba(29, 78, 216, 0.0)');

        new Chart(ctxLine, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: enrollmentDataPoints,
                    borderColor: '#1D4ED8',
                    borderWidth: 3,
                    pointRadius: 0, 
                    fill: true,
                    backgroundColor: blueGradient,
                    tension: 0.45 
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { display: false }
                }
            }
        });

        // Bar Chart Setup
        const ctxBar = document.getElementById('revenueBarChart').getContext('2d');
        new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: revenueDataPoints,
                    backgroundColor: '#E2E8F0',
                    hoverBackgroundColor: '#CBD5E1',
                    borderRadius: 6, 
                    borderSkipped: false,
                    barThickness: 48
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, border: { display: false } },
                    y: { display: false }
                }
            }
        });

        function exportDashboardData() {
            const csvRows = [['Month', 'Student Enrollment Index', 'Revenue Amount (K)']];
            chartLabels.forEach((label, index) => {
                csvRows.push([label, enrollmentDataPoints[index], revenueDataPoints[index]]);
            });
            let csvContent = "data:text/csv;charset=utf-8," + csvRows.map(e => e.join(",")).join("\n");
            const encodedUri = encodeURI(csvContent);
            const downloadAnchor = document.createElement("a");
            downloadAnchor.setAttribute("href", encodedUri);
            downloadAnchor.setAttribute("download", "semester_performance_report_2024.csv");
            document.body.appendChild(downloadAnchor);
            downloadAnchor.click();
            document.body.removeChild(downloadAnchor);
        }

        flatpickr("#calendarInput", {
            defaultDate: "today",
            altInput: true,
            altFormat: "F j, Y",
            dateFormat: "Y-m-d",
            position: "auto right"
        });

        document.getElementById('calendarBtn').addEventListener('click', function() {
            document.getElementById('calendarInput')._flatpickr.open();
        });
    </script>
</body>
</html>