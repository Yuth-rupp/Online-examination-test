<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $lastGraded->exam->title ?? 'Exam' }} - Grading Confirmed</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] font-sans text-gray-800 antialiased selection:bg-blue-100">

    <!-- Header Panel Linked to Live Exam Data -->
    <header class="flex justify-between items-center px-8 py-4 border-b border-gray-100 bg-white shadow-xs sticky top-0 z-20">
        <div>
            <h1 class="text-md font-bold text-gray-900 tracking-tight">{{ $lastGraded->exam->title ?? 'Advanced Assessment Session' }}</h1>
            <p class="text-xs text-gray-400 mt-0.5">Course Code: <span class="font-bold text-blue-600">{{ $lastGraded->exam->course->code ?? 'EXAM-MOD' }}</span> • Evaluation Confirmation</p>
        </div>
        <div class="flex items-center gap-6">
            <div class="flex items-center gap-2">
                <div class="w-24 bg-gray-100 h-2 rounded-full overflow-hidden">
                    <div class="bg-blue-600 h-full rounded-full transition-all duration-500" style="width: {{ $progressPercentage }}%;"></div>
                </div>
                <span class="text-xs font-bold text-gray-500">{{ $completedCount }} / {{ $totalStudents }} graded</span>
            </div>
            <a href="{{ route('teacher.grading.queue') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i class="fa-solid fa-table-list"></i> Return to Queue
            </a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto grid grid-cols-3 gap-8 p-8">
        
        <!-- Left Side Submissions Evaluation History Stack -->
        <section class="col-span-2 space-y-6">
            
            <!-- Success Toast Box Trigger -->
            <div class="bg-white border border-emerald-100 rounded-2xl p-5 flex items-start gap-4 shadow-sm animate-fadeIn">
                <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center font-bold text-sm">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <div>
                    <h2 class="text-sm font-bold text-gray-900 tracking-tight">Grade Saved Successfully</h2>
                    <p class="text-xs text-gray-400 mt-0.5">The marksheet record for <span class="font-bold text-slate-700">{{ $lastGraded->student->full_name ?? 'Student' }}</span> has been recorded and finalized to database tables.</p>
                </div>
            </div>

            <!-- Last Graded Overview Card -->
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6 shadow-xs relative">
                <div class="flex justify-between items-center">
                    <div>
                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider block">Last Graded</span>
                        <a href="{{ route('teacher.grading.show', ['student_id' => $lastGraded->user_id]) }}" class="text-lg font-black text-gray-900 tracking-tight hover:text-blue-600 transition group block mt-0.5">
                            {{ $lastGraded->student->full_name ?? 'Alex Johnson' }} 
                            <span class="text-xs text-blue-500 font-semibold opacity-0 group-hover:opacity-100 ml-1.5 transition">✎ Edit Scores</span>
                        </a>
                    </div>
                    <span class="bg-emerald-50 text-emerald-600 font-black text-[9px] uppercase px-2.5 py-1 rounded-md tracking-widest border border-emerald-100">
                        {{ $lastGraded->total_score >= 20 ? 'Passed' : 'Failed' }}
                    </span>
                </div>

                <div class="flex justify-between items-center bg-[#F8FAFC] border border-slate-100 rounded-xl p-5">
                    <div class="grid grid-cols-3 gap-8 flex-1">
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Accuracy</p>
                            <p class="text-base font-extrabold text-gray-800 mt-0.5">{{ $lastGraded->accuracy_score ?? 0 }}<span class="text-xs font-normal text-gray-400">/10</span></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Depth</p>
                            <p class="text-base font-extrabold text-gray-800 mt-0.5">{{ $lastGraded->depth_score ?? 0 }}<span class="text-xs font-normal text-gray-400">/10</span></p>
                        </div>
                        <div>
                            <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Clarity</p>
                            <p class="text-base font-extrabold text-gray-800 mt-0.5">{{ $lastGraded->clarity_score ?? 0 }}<span class="text-xs font-normal text-gray-400">/5</span></p>
                        </div>
                    </div>
                    
                    <div class="bg-white border border-gray-100 rounded-xl px-5 py-3 text-center min-w-[110px] shadow-2xs">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Grand Total</p>
                        <p class="text-2xl font-black text-blue-600 tracking-tight mt-0.5">{{ $lastGraded->total_score ?? 0 }}<span class="text-xs font-normal text-gray-400">/40</span></p>
                    </div>
                </div>

                @if(!empty($lastGraded->teacher_feedback))
                    <div class="text-xs text-gray-500 italic leading-relaxed bg-gray-50/50 border border-gray-100 border-dashed rounded-xl p-4">
                        "{{ $lastGraded->teacher_feedback }}"
                    </div>
                @endif
            </div>

            <!-- DYNAMIC REAL STUDENT UP NEXT SUB-WINDOW -->
            @if($nextStudent)
                <div class="bg-white border-2 border-blue-500 rounded-3xl p-6 space-y-5 shadow-sm relative">
                    <span class="absolute top-0 left-6 -translate-y-1/2 bg-blue-600 text-white font-extrabold text-[9px] uppercase px-2.5 py-1 rounded-md tracking-widest shadow-sm">
                        Up Next
                    </span>

                    <div class="flex items-center gap-4 pt-2">
                        <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center font-bold text-blue-600 text-sm border border-blue-100 shadow-2xs">
                            {{ strtoupper(substr($nextStudent->student->full_name ?? 'ST', 0, 2)) }}
                        </div>
                        <div>
                            <h3 class="text-base font-black text-gray-900 tracking-tight">{{ $nextStudent->student->full_name ?? 'Sarah Chen' }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Submitted {{ $nextStudent->created_at ? $nextStudent->created_at->format('h:i A') : 'Live Time' }} • <span class="text-amber-500 font-bold uppercase text-[10px] tracking-wider">Awaiting Review</span></p>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-2xl p-5 space-y-2">
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-wider">Response Preview</p>
                        <p class="text-xs text-gray-600 leading-relaxed font-normal">
                            {{ Str::limit($nextStudent->text_response ?? 'The required student long form writing response model is cached and awaiting teacher feedback. Open evaluation parameters to check solutions fields directly.', 180) }}
                        </p>
                    </div>

                    <div class="flex justify-between items-center border border-gray-100 rounded-xl p-4 bg-[#F8FAFC]">
                        <div class="flex items-center gap-3">
                            <span class="text-xl"><i class="fa-solid fa-file-lines text-blue-500"></i></span>
                            <div>
                                <span class="text-xs font-bold text-gray-700 block truncate max-w-[280px]">
                                    {{ basename($nextStudent->document_path ?? 'Student_Exam_Script.pdf') }}
                                </span>
                                <span class="text-[10px] text-gray-400 block mt-0.5">2.4 MB • Verification Script Document</span>
                            </div>
                        </div>
                        <a href="{{ route('teacher.submissions.download', ['filename' => basename($nextStudent->document_path ?? 'mock.pdf')]) }}" target="_blank" class="text-xs font-bold text-gray-500 hover:text-blue-600 bg-white border border-gray-200 rounded-lg p-2 shadow-2xs transition flex items-center justify-center">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="{{ route('teacher.grading.show', ['student_id' => $nextStudent->user_id]) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white py-3 rounded-xl text-xs font-bold text-center transition shadow-md shadow-blue-500/10">
                            Continue to Next Student →
                        </a>
                        <a href="{{ route('teacher.grading.queue') }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-5 py-3 rounded-xl text-xs font-bold text-center transition">
                            View Grading Queue
                        </a>
                    </div>
                </div>
            @else
                <!-- Queue Clear Celebration State -->
                <div class="bg-white border border-gray-200 rounded-3xl p-10 text-center shadow-xs space-y-3">
                    <div class="w-14 h-14 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center text-xl mx-auto border border-emerald-100 shadow-2xs">
                        <i class="fa-solid fa-champagne-glasses"></i>
                    </div>
                    <h3 class="text-base font-bold text-slate-800">Exam Roster Queue Evaluated Completely!</h3>
                    <p class="text-xs text-gray-400 max-w-sm mx-auto">All logged candidate responses assigned to this specific evaluation node parameters have been graded.</p>
                    <a href="{{ route('teacher.grading.queue') }}" class="inline-block bg-[#0F172A] text-white text-xs font-bold px-4 py-2.5 rounded-xl transition hover:bg-slate-800 shadow-sm mt-2">Return to Dashboard Pool</a>
                </div>
            @endif
        </section>

        <!-- Right Sidebar (Analytics & Progress Layer) -->
        <aside class="col-span-1 space-y-6">
            <div class="bg-white border border-gray-100 rounded-2xl p-6 space-y-6 shadow-xs">
                <div>
                    <h3 class="text-xs font-black text-gray-900 uppercase tracking-wider">Grading Progress Analytics</h3>
                </div>

                <!-- Circular Dynamic Score Indicator -->
                <div class="flex justify-center py-4">
                    <div class="relative w-36 h-36 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-gray-100" stroke-width="2.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                            <path class="text-blue-600 transition-all duration-500" stroke-dasharray="{{ $progressPercentage }}, 100" stroke-width="2.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-2xl font-black text-gray-900 tracking-tight">{{ $progressPercentage }}%</span>
                            <span class="text-[9px] font-bold text-gray-400 uppercase block tracking-wider mt-0.5">Complete</span>
                        </div>
                    </div>
                </div>

                <div class="space-y-3.5 border-t border-gray-50 pt-4 text-xs font-semibold">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Total Registered Pool</span>
                        <span class="font-bold text-gray-800">{{ $totalStudents }} Candidates</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-400">Completed Sheets</span>
                        <span class="font-bold text-slate-800">{{ $completedCount }} Papers</span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-50 pt-3.5">
                        <span class="text-gray-400">Remaining Queue</span>
                        <span class="font-bold text-blue-600">{{ $remainingCount }} Submissions</span>
                    </div>
                </div>

                <div class="bg-slate-50 border border-slate-100 rounded-xl p-4 text-center">
                    <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest">Estimated Completion Velocity</p>
                    <p class="text-xl font-black text-slate-900 tracking-tight mt-1">~{{ $remainingCount * 2 }} mins remaining</p>
                </div>

                <div class="bg-blue-50/40 border border-blue-100/40 rounded-xl p-4 flex gap-3 items-start">
                    <span class="text-blue-600 mt-0.5"><i class="fa-solid fa-wand-magic-sparkles"></i></span>
                    <div class="text-[11px] leading-relaxed text-gray-500">
                        <strong class="text-gray-800 font-bold block mb-0.5 uppercase tracking-wide text-[9px]">Grading Assistant</strong>
                        Your verification cadence looks exceptionally consistent. Take short breaks to safeguard metrics accuracy parameters!
                    </div>
                </div>
            </div>
        </aside>
    </main>

</body>
</html> 