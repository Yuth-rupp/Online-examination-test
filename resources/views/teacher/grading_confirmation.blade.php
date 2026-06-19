<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advanced Algorithms Midterm - Grading Workflow</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-[#f8fafc] font-sans text-gray-800 antialiased selection:bg-blue-100 selection:text-blue-900">

    <header class="flex justify-between items-center px-8 py-4 border-b border-gray-100 bg-white shadow-xs">
        <div>
            <h1 class="text-md font-bold text-gray-900 tracking-tight">Advanced Algorithms Midterm</h1>
            <p class="text-xs text-gray-400 mt-0.5">Question 4 • Grading Workflow</p>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-24 bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full" style="width: {{ $progressPercentage }}%;"></div>
                </div>
                <span class="text-xs font-bold text-gray-500">{{ $completedCount }} / {{ $totalStudents }} graded</span>
            </div>
            <a href="{{ route('teacher.dashboard') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center shadow-sm shadow-blue-500/10">
                Return to Queue
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto grid grid-cols-3 gap-8 p-8">
        
        <section class="col-span-2 space-y-6">
            
            <div class="bg-white border border-emerald-100 rounded-2xl p-5 flex items-start gap-4 shadow-xs">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-sm">
                    ✓
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 tracking-tight">Grade Saved Successfully</h2>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $lastGraded->student->full_name ?? 'Alex Johnson' }}'s response has been recorded and finalized.</p>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6 shadow-xs relative">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Last Graded</span>
                        <a href="{{ route('teacher.grading.show', $lastGraded->id) }}" class="text-lg font-black text-gray-900 tracking-tight hover:text-blue-600 transition group block mt-0.5">
                            {{ $lastGraded->student->full_name ?? 'Alex Johnson' }} 
                            <span class="text-xs text-blue-500 font-semibold opacity-0 group-hover:opacity-100 ml-1.5 transition">✎ Edit Scores</span>
                        </a>
                    </div>
                    <span class="bg-emerald-50 text-emerald-600 font-black text-[9px] uppercase px-2.5 py-1 rounded-md tracking-widest border border-emerald-100">
                        Passed
                    </span>
                </div>

                <div class="flex justify-between items-center bg-[#fbfcfd] border border-gray-50 rounded-xl p-5">
                    <div class="grid grid-cols-3 gap-8 flex-1">
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Accuracy</p>
                            <p class="text-base font-extrabold text-gray-800 mt-0.5">{{ $lastGraded->accuracy_score ?? 9 }}<span class="text-xs font-normal text-gray-400">/10</span></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Depth</p>
                            <p class="text-base font-extrabold text-gray-800 mt-0.5">{{ $lastGraded->depth_score ?? 7 }}<span class="text-xs font-normal text-gray-400">/10</span></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Clarity</p>
                            <p class="text-base font-extrabold text-gray-800 mt-0.5">{{ $lastGraded->clarity_score ?? 5 }}<span class="text-xs font-normal text-gray-400">/5</span></p>
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-100 rounded-xl px-5 py-3 text-center min-w-[100px] shadow-2xs">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Total Score</p>
                        <p class="text-2xl font-black text-blue-600 tracking-tight mt-0.5">{{ $lastGraded->total_score ?? 21 }}<span class="text-xs font-normal text-gray-400">/25</span></p>
                    </div>
                </div>

                @if(!empty($lastGraded->feedback))
                    <div class="text-xs text-gray-500 italic leading-relaxed bg-gray-50/50 border border-gray-100 border-dashed rounded-xl p-4">
                        "{{ $lastGraded->feedback }}"
                    </div>
                @endif
            </div>

            @if($nextStudent)
                <div class="bg-white border-2 border-blue-500 rounded-3xl p-6 space-y-5 shadow-xs relative">
                    <span class="absolute top-0 left-6 -translate-y-1/2 bg-blue-600 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-md tracking-widest shadow-sm">
                        Up Next
                    </span>

                    <div class="flex items-center gap-4 pt-2">
                        <div class="w-12 h-12 bg-gray-100 rounded-2xl flex items-center justify-center font-bold text-blue-600 text-sm border border-gray-200">
                            {{ strtoupper(substr($nextStudent->student->full_name ?? 'SC', 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900 tracking-tight">{{ $nextStudent->student->full_name ?? 'Sarah Chen' }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Submitted {{ $nextStudent->created_at ? $nextStudent->created_at->format('h:i A') : '10:42 AM' }} • <span class="text-blue-500 font-bold uppercase text-[10px] tracking-wider">Awaiting Review</span></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 space-y-2">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Response Preview</p>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            {{ Str::limit($nextStudent->text_response ?? 'The implementation of the Bellman-Ford algorithm in this context requires a specific focus on handling negative weight cycles within the graph structure. By utilizing a dynamic programming model...', 160) }}
                        </p>
                    </div>

                    <div class="flex justify-between items-center border border-gray-100 rounded-xl p-4 bg-[#f8fafc]">
                        <div class="flex items-center gap-3">
                            <span class="text-xl">📄</span>
                            <div>
                                <span class="text-xs font-bold text-gray-700 block truncate max-w-[280px]">
                                    {{ basename($nextStudent->document_path ?? 'Algorithm_Complexity_Proof.pdf') }}
                                </span>
                                <span class="text-[10px] text-gray-400 block mt-0.5">2.4 MB • PDF Document</span>
                            </div>
                        </div>
                        <a href="{{ route('teacher.submissions.download', ['filename' => basename($nextStudent->document_path ?? 'mock.pdf')]) }}" target="_blank" class="text-xs font-bold text-gray-500 hover:text-blue-600 bg-white border border-gray-200 rounded-lg p-2 shadow-2xs transition flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="{{ route('teacher.grading.show', $nextStudent->id) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-xs font-bold text-center transition shadow-md shadow-blue-500/10">
                            Continue to Next Student →
                        </a>
                        <a href="{{ route('teacher.dashboard') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-xl text-xs font-bold text-center transition">
                            View Grading Queue
                        </a>
                    </div>
                </div>
            @else
                <div class="bg-white border border-gray-100 rounded-3xl p-8 text-center shadow-xs">
                    <p class="text-sm font-bold text-gray-500">🎉 All submissions for this exam module queue have been fully evaluated!</p>
                    <a href="{{ route('teacher.dashboard') }}" class="inline-block mt-4 text-xs font-bold text-blue-600 hover:underline">Return to Dashboard</a>
                </div>
            @endif
        </section>

        <aside class="col-span-1 space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6 shadow-xs">
                <div>
                    <h3 class="text-xs font-bold text-gray-900 tracking-tight">Grading Progress</h3>
                </div>

                <div class="flex justify-center py-4">
                    <div class="relative w-36 h-36 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-blue-600 transition-all duration-500" stroke-dasharray="{{ $progressPercentage }}, 100" stroke-width="3" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-2xl font-black text-gray-900 tracking-tight">{{ $progressPercentage }}%</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase block tracking-wider mt-0.5">Complete</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3.5 border-t border-gray-50 pt-4 text-xs font-medium">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Students</span>
                        <span class="font-bold text-gray-800">{{ $totalStudents }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Completed</span>
                        <span class="font-bold text-gray-800">{{ $completedCount }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-50 pt-3.5">
                        <span class="text-gray-400">Remaining</span>
                        <span class="font-bold text-blue-600">{{ $remainingCount }}</span>
                    </div>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-xl p-4 text-center">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Estimated Time to Finish</p>
                    <p class="text-xl font-black text-gray-900 tracking-tight mt-1">~{{ $remainingCount * 2 }} mins</p>
                </div>

                <div class="bg-blue-50/30 border border-blue-100/50 rounded-xl p-4 flex gap-3 items-start">
                    <span class="text-sm">✨</span>
                    <div class="text-[11px] leading-relaxed text-gray-500">
                        <strong class="text-gray-800 font-bold block mb-0.5">GRADING ASSISTANT</strong>
                        You are grading faster than your average pace. Take a short break to maintain consistency?
                    </div>
                </div>
            </div>
        </aside>
    </main>

</body>
</html>