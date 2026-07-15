<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>End Exam Confirmation | ExamSystem</title>
    <!-- Tailwind CSS v4 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- FontAwesome for Dashboard Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#f8fafc] text-slate-700 min-h-screen flex flex-col">

    <!-- Professional Top Navigation Bar -->
    <header class="bg-[#0f172a] text-white px-6 py-4 flex items-center justify-between shadow-md">
        <div class="flex items-center gap-3">
            <div class="bg-blue-600 p-2 rounded-xl text-white">
                <i class="fa-solid fa-graduation-cap text-lg"></i>
            </div>
            <div>
                <span class="font-bold text-lg tracking-wide block">ExamSystem</span>
                <span class="text-xs text-slate-400">Live Proctoring Panel</span>
            </div>
        </div>
        <div class="flex items-center gap-3 text-sm text-slate-300">
            <i class="fa-solid fa-user-tie text-slate-400"></i>
            <span>Proctor Workspace</span>
        </div>
    </header>

    <!-- Main Workspace Container -->
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6">
        
        <!-- Professional & Friendly Modal Card -->
        <div class="bg-white border border-slate-200 shadow-xl rounded-2xl max-w-xl w-full overflow-hidden transform transition-all">
            
            <!-- Real-Time Context Header Bar -->
            <div class="bg-[#f1f5f9] border-b border-slate-200 px-6 py-4 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <span class="flex h-2.5 w-2.5 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-emerald-500"></span>
                    </span>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500">Active Exam Session</span>
                </div>
                <span class="text-xs bg-slate-200 text-slate-700 px-2.5 py-1 rounded-md font-medium">ID: #7884</span>
            </div>

            <!-- Card Body Contents -->
            <div class="p-6 sm:p-8">
                
                <!-- Friendly, Supportive Heading -->
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center text-amber-500 text-xl flex-shrink-0 border border-amber-200">
                        <i class="fa-solid fa-hourglass-end"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Ready to wrap up this session?</h2>
                        <p class="text-slate-500 text-sm mt-1">
                            Before closing down the live proctor room, please review what happens to the active candidates.
                        </p>
                    </div>
                </div>

                <!-- Live Dashboard Summary Metrics Widget -->
                <div class="grid grid-cols-2 gap-4 bg-slate-50 border border-slate-100 rounded-xl p-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fa-solid fa-users"></i>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 font-medium uppercase">Connected</span>
                            <span class="font-semibold text-slate-800 text-sm">Active Students</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 bg-rose-50 text-rose-600 rounded-lg flex items-center justify-center text-sm">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <span class="block text-xs text-slate-400 font-medium uppercase">Alert Status</span>
                            <span class="font-semibold text-slate-800 text-sm">Flags Closed</span>
                        </div>
                    </div>
                </div>

                <!-- Clear, Informative Consequence Layout -->
                <div class="space-y-3.5 mb-8">
                    <div class="flex gap-3 text-sm text-slate-600">
                        <i class="fa-regular fa-circle-check text-emerald-500 mt-0.5 text-base flex-shrink-0"></i>
                        <span>All ongoing student exam attempts will be automatically saved and marked as submitted.</span>
                    </div>
                    <div class="flex gap-3 text-sm text-slate-600">
                        <i class="fa-regular fa-circle-check text-emerald-500 mt-0.5 text-base flex-shrink-0"></i>
                        <span>Webcam streams, fraud flags logs, and real-time socket sessions will close safely.</span>
                    </div>
                </div>

                <hr class="border-slate-200 mb-6">

                <!-- Action Button Form Layout Group (Linked to target POST route context) -->
                <form action="{{ route('teacher.monitoring.endExam') }}" method="POST" class="flex flex-col sm:flex-row-reverse gap-3">
                    @csrf
                    
                    <button type="submit" class="w-full sm:w-auto bg-slate-900 hover:bg-slate-800 text-white font-semibold px-5 py-3 rounded-xl shadow-sm transition-all active:scale-[0.98] flex items-center justify-center gap-2 text-sm cursor-pointer">
                        Confirm & End Session
                    </button>

                    <a href="{{ url()->previous() }}" class="w-full sm:w-auto bg-white hover:bg-slate-50 text-slate-600 font-medium px-5 py-3 rounded-xl border border-slate-200 text-center transition-all text-sm block active:scale-[0.98]">
                        Keep Monitoring
                    </a>
                </form>

            </div>

        </div>
    </main>

    <!-- Professional Footer -->
    <footer class="py-4 text-center text-xs text-slate-400 border-t border-slate-200 bg-white">
        &copy; 2026 ExamSystem Interactive Proctoring Modules. All rights reserved.
    </footer>

</body>
</html>