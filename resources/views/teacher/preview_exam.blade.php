<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Master Review Sheet</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2 family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } @media print { .no-print { display: none; } } </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen p-8 selection:bg-blue-500/20">

    <div class="max-w-4xl mx-auto space-y-6">
        
        <div class="no-print bg-white border border-[#E2E8F0] p-4 rounded-2xl flex items-center justify-between shadow-xs">
            <a href="{{ route('teacher.dashboard') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors bg-slate-100 px-3 py-2 rounded-xl">
                <i class="fa-solid fa-arrow-left"></i> Dashboard
            </a>
            <button onclick="window.print()" class="bg-[#1D4ED8] hover:bg-blue-800 text-white text-xs font-bold px-4 py-2 rounded-xl shadow-sm flex items-center gap-2 transition-all">
                <i class="fa-solid fa-print"></i> Print / Save PDF
            </button>
        </div>

        <header class="bg-white border border-[#E2E8F0] rounded-2xl p-8 shadow-sm space-y-4 relative overflow-hidden">
            <div class="absolute right-0 top-0 bg-blue-50 text-blue-600 px-4 py-1.5 rounded-bl-xl font-mono text-xs font-bold tracking-wider">
                CODE: {{ $exam->access_code ?? 'UNASSIGNED' }}
            </div>
            
            <div class="space-y-1">
                <span class="text-xs font-extrabold text-[#1D4ED8] uppercase tracking-wider font-mono">{{ $exam->course->name ?? 'General Curriculum' }}</span>
                <h1 class="text-2xl font-extrabold text-[#0F172A] tracking-tight">{{ $exam->title }}</h1>
            </div>

            <div class="grid grid-cols-3 gap-4 pt-4 border-t border-slate-100 text-xs text-slate-500 font-semibold">
                <div class="flex items-center gap-2"><i class="fa-regular fa-clock text-base text-slate-400"></i> Duration: {{ $exam->duration }} Mins</div>
                <div class="flex items-center gap-2"><i class="fa-regular fa-circle-check text-base text-slate-400"></i> Pass Threshold: {{ $exam->pass_mark }}%</div>
                <div class="flex items-center gap-2"><i class="fa-regular fa-folder-open text-base text-slate-400"></i> Total Items: {{ $exam->questions->count() }} Questions</div>
            </div>
        </header>

        <main class="space-y-6">
            @forelse($exam->questions as $index => $question)
                <section class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm space-y-4">
                    <div class="flex justify-between items-start border-b border-slate-100 pb-3">
                        <div class="flex items-center gap-2.5">
                            <span class="w-7 h-7 bg-slate-900 text-white rounded-lg flex items-center justify-center font-bold text-sm">Q{{ $index + 1 }}</span>
                            <span class="px-2.5 py-0.5 bg-slate-100 border border-slate-200 text-slate-600 rounded-md text-[10px] font-extrabold uppercase tracking-wide">{{ $question->type }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider font-mono">{{ $question->marks }} Points</span>
                    </div>

                    <div class="text-sm text-slate-800 font-medium leading-relaxed">
                        {!! $question->content !!}
                    </div>

                    @if($question->type === 'MCQ')
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-2">
                            @foreach($question->options as $optIndex => $optionText)
                                @php 
                                    $isCorrectMCQ = isset($question->correct_answer['mcq']) && (string)$question->correct_answer['mcq'] === (string)$optIndex;
                                @endphp
                                <div class="flex items-center gap-3 p-3 rounded-xl text-xs font-medium border {{ $isCorrectMCQ ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-100 text-slate-600' }}">
                                    <i class="fa-regular {{ $isCorrectMCQ ? 'fa-circle-dot text-emerald-600' : 'fa-circle text-slate-300' }} text-sm"></i>
                                    <span>{{ $optionText }}</span>
                                    @if($isCorrectMCQ) <span class="ml-auto text-[10px] font-bold text-emerald-600 uppercase tracking-wider font-mono">[Solution Answer Key]</span> @endif
                                </div>
                            @endforeach
                        </div>

                    @elseif($question->type === 'TF')
                        @php 
                            $correctTF = $question->correct_answer['tf'] ?? 'true';
                        @endphp
                        <div class="grid grid-cols-2 gap-3 pt-2">
                            <div class="flex items-center gap-2.5 p-3 rounded-xl text-xs font-bold border {{ $correctTF === 'true' ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                <i class="fa-regular {{ $correctTF === 'true' ? 'fa-square-check text-emerald-600' : 'fa-square' }} text-base"></i> True Statement
                            </div>
                            <div class="flex items-center gap-2.5 p-3 rounded-xl text-xs font-bold border {{ $correctTF === 'false' ? 'bg-emerald-50/60 border-emerald-200 text-emerald-800' : 'bg-slate-50 border-slate-100 text-slate-400' }}">
                                <i class="fa-regular {{ $correctTF === 'false' ? 'fa-square-check text-emerald-600' : 'fa-square' }} text-base"></i> False Statement
                            </div>
                        </div>

                    @elseif($question->type === 'Essay')
                        <div class="p-4 bg-amber-50/40 border border-amber-200/60 rounded-xl space-y-1.5">
                            <div class="text-[10px] font-bold text-amber-700 uppercase tracking-wider flex items-center gap-1.5"><i class="fa-solid fa-graduation-cap"></i> Ideal Solution Rubric Grading Guideline Key:</div>
                            <p class="text-xs text-amber-900/90 font-medium leading-relaxed italic">
                                {{ $question->correct_answer['rubric'] ?? 'No formal text rubric grading solution map was logged for this essay question box entry.' }}
                            </p>
                        </div>
                    @endif
                </section>
            @empty
                <div class="bg-white border border-[#E2E8F0] p-12 text-center rounded-2xl text-xs text-slate-400 italic shadow-sm">
                    There are currently no active question model instances linked inside this examination record workspace framework.
                </div>
            @endforelse
        </main>
    </div>

</body>
</html>