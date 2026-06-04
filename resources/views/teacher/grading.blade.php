<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Dynamic Grading Workspace</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <style>
        /* Base range input reset */
        input[type="range"] {
            -webkit-appearance: none;
            appearance: none;
            background: transparent;
        }

        /* Modern styling overrides for HTML5 range slider handles (Thumbs) */
        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            border: 2px solid #2563eb;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
            transition: transform 0.1s ease, background-color 0.1s ease;
            margin-top: -6px; /* Centers the thumb perfectly over the 4px track */
        }
        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.15);
            background-color: #f8fafc;
        }
        input[type="range"]::-moz-range-thumb {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            background: #ffffff;
            cursor: pointer;
            border: 2px solid #2563eb;
            box-shadow: 0 2px 4px rgba(37, 99, 235, 0.2);
        }

        /* Track baseline styles */
        input[type="range"]::-webkit-slider-runnable-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            border-radius: 9999px;
            background: transparent; /* Background color handled dynamically by JS fill logic */
        }
        input[type="range"]::-moz-range-track {
            width: 100%;
            height: 4px;
            cursor: pointer;
            border-radius: 9999px;
            background: #e2e8f0;
        }
    </style>
</head>
<body class="bg-[#f8fafc] font-sans text-gray-800 antialiased selection:bg-blue-100 selection:text-blue-900">

@if(session('success'))
    <div class="fixed top-4 right-4 bg-green-600 text-white px-4 py-3 rounded-xl shadow-lg z-50 text-sm font-semibold flex items-center gap-2">
        <span>✅</span> {{ session('success') }}
    </div>
@endif

<form action="{{ route('teacher.grading.store', $submission->id) }}" method="POST" id="gradingForm">
    @csrf
    <input type="hidden" name="action" id="formAction" value="save">

    <div class="grid grid-cols-4 min-h-screen">
        
        <div class="col-span-1 bg-white border-r border-gray-100 p-6 flex flex-col justify-between select-none">
            <div class="space-y-1">
                <div class="flex items-center gap-3 px-3 pt-2 pb-8">
                    <div class="w-8 h-8 bg-blue-600 rounded-xl flex items-center justify-center text-white shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l9-5-9-5-9 5 9 5z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path>
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 tracking-tight">ExamSystem</span>
                </div>

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

                <div class="flex items-center gap-3.5 text-white bg-blue-600 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight shadow-sm shadow-blue-600/10">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    <span>Grading</span>
                </div>

                <a href="{{ route('teacher.analytics') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9-1V4a2 2 0 00-2-2h-2a2 2 0 00-2 2v14a2 2 0 002 2h2a2 2 0 002-2z"></path>
                    </svg>
                    <span>Analytics</span>
                </a>

                <a href="{{ route('teacher.settings') }}" class="flex items-center gap-3.5 text-gray-500 hover:bg-gray-50 hover:text-gray-900 px-4 py-3 rounded-xl text-sm font-semibold tracking-tight transition group">
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-500 transition" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span>Settings</span>
                </a>
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
        </div>

        <div class="col-span-2 flex flex-col h-screen overflow-hidden bg-[#fbfcfd]">
            <div class="flex justify-between items-center px-8 py-5 border-b border-gray-100 bg-white">
                <div>
                    <h1 class="text-md font-bold text-gray-900 tracking-tight">Advanced Algorithms Midterm</h1>
                    <p class="text-xs text-gray-400 mt-1">Question 4 • Student: <span class="font-medium text-gray-600">{{ $submission->student->full_name ?? $submission->student_name ?? 'Alex Johnson' }}</span></p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-xs bg-gray-50 text-gray-500 border border-gray-100 px-2.5 py-1 rounded-lg font-medium">18 / 40 graded</span>
                    <button type="submit" onclick="document.getElementById('formAction').value='save_next'" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition cursor-pointer flex items-center gap-1.5 shadow-sm shadow-blue-500/10">
                        Save & Next <span>&rarr;</span>
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-8 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-100 p-8 space-y-6">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 bg-blue-50/50 px-2.5 py-1 rounded-md inline-block">Question 4</span>
                    
                    <h2 class="text-base font-bold text-gray-900 tracking-tight">Compare BFS and DFS algorithms</h2>
                    
                    <div class="text-gray-500 leading-relaxed text-sm space-y-4 font-normal">
                        <p>Breadth-First Search (BFS) is an algorithm for traversing or searching tree or graph data structures. It starts at the tree root and explores all nodes at the present depth prior to moving on to the nodes at the next depth level. To maintain this order, BFS uses a <mark class="bg-amber-50 text-amber-800 font-medium px-1 rounded">queue (FIFO)</mark> data structure.</p>
                        <p>In contrast, Depth-First Search (DFS) starts at the root and explores as far as possible along each branch before backtracking. It utilizes a <mark class="bg-amber-50 text-amber-800 font-medium px-1 rounded">Stack (LIFO)</mark> or recursion. One significant difference is memory usage; BFS can be more memory-intensive if the graph is very wide, whereas DFS might consume more memory if the graph is very deep due to the call stack.</p>
                        <p>For connectivity problems in graphs, both are effective, but BFS is generally preferred for finding the shortest path in unweighted graphs, while DFS is often used in topological sorting or solving puzzles like mazes where we need to find if a path exists.</p>
                    </div>

                    <div class="flex justify-between items-center border border-gray-100 rounded-xl p-4 bg-[#f8fafc] mt-8">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📎</span>
                            <div>
                                <span class="text-xs font-bold text-gray-700 block">Original Submission.pdf</span>
                            </div>
                        </div>
                        <a href="{{ route('teacher.submissions.download', ['filename' => basename($submission->document_path ?? 'mock.pdf')]) }}" target="_blank" class="text-xs font-bold text-blue-600 hover:text-blue-700 bg-white border border-gray-150 rounded-lg px-3 py-1.5 shadow-2xs transition">
                            View Full Document
                        </a>
                    </div>
                </div>
            </div>

            <div class="bg-white border-t border-gray-100 px-8 py-4 flex justify-between items-center select-none">
                @if($prev)
                    <a href="{{ route('teacher.grading.show', $prev->id ?? $prev) }}" class="text-xs font-bold text-gray-400 hover:text-gray-700 transition flex items-center gap-1">
                        &lsaquo; Previous Student
                    </a>
                @else
                    <span class="text-xs font-bold text-gray-200 cursor-not-allowed flex items-center gap-1">&lsaquo; Previous Student</span>
                @endif

                <div class="w-2/5 flex flex-col items-center gap-1.5">
                    <div class="w-full bg-gray-100 h-1.5 rounded-full overflow-hidden">
                        <div class="bg-blue-600 h-full w-[45%] transition-all duration-500 rounded-full"></div>
                    </div>
                    <span class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Grading Progress: 18 of 40 (45%)</span>
                </div>

                @if($next)
                    <a href="{{ route('teacher.grading.show', $next->id ?? $next) }}" class="text-xs font-bold text-gray-400 hover:text-gray-700 transition flex items-center gap-1">
                        Next Student &rsaquo;
                    </a>
                @else
                    <span class="text-xs font-bold text-gray-200 cursor-not-allowed flex items-center gap-1">Next Student &rsaquo;</span>
                @endif
            </div>
        </div>

        <div class="col-span-1 bg-white border-l border-gray-100 p-6 flex flex-col justify-between h-screen overflow-y-auto">
            <div class="space-y-8">
                <h3 class="text-xs font-bold text-gray-800 tracking-tight mt-2">Grading Rubric</h3>

                <div class="space-y-7">
                    <div>
                        <div class="flex justify-between items-center text-xs font-semibold mb-2.5">
                            <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold">Accuracy</span>
                            <span class="text-blue-600 text-xs font-bold"><span id="accuracy_val">9</span> / 10</span>
                        </div>
                        <input type="range" name="accuracy" min="0" max="10" value="{{ old('accuracy', $submission->accuracy_score ?? 9) }}" class="w-full dynamic-slider">
                    </div>

                    <div>
                        <div class="flex justify-between items-center text-xs font-semibold mb-2.5">
                            <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold">Depth</span>
                            <span class="text-blue-600 text-xs font-bold"><span id="depth_val">7</span> / 10</span>
                        </div>
                        <input type="range" name="depth" min="0" max="10" value="{{ old('depth', $submission->depth_score ?? 7) }}" class="w-full dynamic-slider">
                    </div>

                    <div>
                        <div class="flex justify-between items-center text-xs font-semibold mb-2.5">
                            <span class="text-gray-400 uppercase tracking-wider text-[10px] font-bold">Clarity</span>
                            <span class="text-blue-600 text-xs font-bold"><span id="clarity_val">5</span> / 5</span>
                        </div>
                        <input type="range" name="clarity" min="0" max="5" value="{{ old('clarity', $submission->clarity_score ?? 5) }}" class="w-full dynamic-slider">
                    </div>
                </div>

                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-3xs flex justify-between items-center">
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Total Score</p>
                        <p class="text-2xl font-black text-gray-900 tracking-tight mt-0.5"><span id="total_score">21</span><span class="text-xs font-normal text-gray-400"> / 25</span></p>
                    </div>
                    <div>
                        <span id="badge_passed" class="hidden bg-emerald-50 text-emerald-600 font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-md tracking-widest border border-emerald-100">Passed</span>
                        <span id="badge_failed" class="hidden bg-rose-50 text-rose-600 font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-md tracking-widest border border-rose-100">Failed</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">General Feedback</label>
                    <textarea name="feedback" rows="6" placeholder="Provide constructive feedback for the student..." class="w-full border border-gray-150 rounded-xl p-4 text-xs focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500/20 transition bg-[#fcfdfe] text-gray-600 leading-relaxed resize-none">{{ old('feedback', $submission->feedback ?? '') }}</textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const sliders = document.querySelectorAll('.dynamic-slider');
        const totalScoreElement = document.getElementById('total_score');
        const passedBadge = document.getElementById('badge_passed');
        const failedBadge = document.getElementById('badge_failed');

        // Pass threshold limit assignment rule (Pass >= 15 / 25)
        const PASS_THRESHOLD = 15;

        function runLiveCalculations() {
            let runningSumTotal = 0;

            sliders.forEach(slider => {
                const numericValue = parseInt(slider.value);
                const max = parseInt(slider.max);
                runningSumTotal += numericValue;

                // 🌟 FIGMA LIVE-COMPLIANCE FILL TRACK TRACKER
                const percentage = (numericValue / max) * 100;
                slider.style.background = `linear-gradient(to right, #2563eb 0%, #2563eb ${percentage}%, #e2e8f0 ${percentage}%, #e2e8f0 100%)`;

                // Update text label value nodes
                document.getElementById(`${slider.name}_val`).innerText = numericValue;
            });

            // Update score aggregation counters
            totalScoreElement.innerText = runningSumTotal;

            // Handle functional layout outcome badges
            if (runningSumTotal >= PASS_THRESHOLD) {
                passedBadge.classList.remove('hidden');
                failedBadge.classList.add('hidden');
            } else {
                failedBadge.classList.remove('hidden');
                passedBadge.classList.add('hidden');
            }
        }

        // Apply visual event registration logic triggers
        sliders.forEach(slider => {
            slider.addEventListener('input', runLiveCalculations);
        });

        // Fire initialization sequence on viewport frame generation load
        runLiveCalculations();
    });
</script>
</body>
</html>