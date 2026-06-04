<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Edit Question #{{ $question->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex selection:bg-blue-500/20">

    <main class="flex-1 p-8 overflow-y-auto">
        <div class="max-w-4xl mx-auto">
            
            <div class="flex items-center gap-2 text-sm text-[#64748B] font-medium mb-3">
                <a href="{{ route('teacher.question-bank') }}" class="hover:text-[#1D4ED8] transition-colors">Question Bank</a>
                <i class="fa-solid fa-chevron-right text-xs text-[#94A3B8]"></i>
                <span class="text-[#0F172A]">Edit Question Record</span>
            </div>

            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-[#E2E8F0] bg-[#FAFCFF] px-8 py-5 flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-[#0F172A]">Modify Question Component</h2>
                        <p class="text-xs text-[#94A3B8] font-medium mt-0.5">Database ID Reference Element: #{{ $question->id }}</p>
                    </div>
                    <span class="inline-block px-3 py-1 rounded-full text-xs font-bold tracking-wide bg-blue-50 text-blue-600 uppercase">
                        {{ $question->type }} Mode
                    </span>
                </div>

                <form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#475569] mb-2">Exam Assignment ID</label>
                            <input type="text" name="exam_id" value="{{ old('exam_id', $question->exam_id) }}" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-[#1D4ED8] focus:ring-2 focus:ring-blue-500/10 transition-all">
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#475569] mb-2">Question Type</label>
                            <select id="question_type" name="question_type" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-[#1D4ED8] transition-all">
                                <option value="MCQ" {{ old('question_type', strtoupper($question->type)) == 'MCQ' ? 'selected' : '' }}>Multiple Choice (MCQ)</option>
                                <option value="Essay" {{ old('question_type', strtoupper($question->type)) == 'ESSAY' ? 'selected' : '' }}>Written Essay</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#475569] mb-2">Difficulty Assessment</label>
                            @php
                                // Parsing out the custom explanation field string metadata safely
                                preg_match('/Difficulty:\s*([a-zA-Z]+)/', $question->explanation, $diffMatch);
                                $currentDifficulty = $diffMatch[1] ?? 'Medium';
                            @endphp
                            <select name="difficulty" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-[#1D4ED8] transition-all">
                                <option value="Easy" {{ $currentDifficulty == 'Easy' ? 'selected' : '' }}>Easy</option>
                                <option value="Medium" {{ $currentDifficulty == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Hard" {{ $currentDifficulty == 'Hard' ? 'selected' : '' }}>Hard</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-[#475569] mb-2">Points / Score Weight</label>
                            <input type="number" name="points" min="1" value="{{ old('points', $question->marks ?? 1) }}" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-[#1D4ED8] transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-[#475569] mb-2">Question Context Prompt</label>
                        <textarea name="question_text" rows="4" placeholder="Draft your detailed test problem parameters..." class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm font-medium focus:outline-none focus:border-[#1D4ED8] focus:ring-2 focus:ring-blue-500/10 transition-all">{!! old('question_text', $question->content) !!}</textarea>
                    </div>

                    <hr class="border-[#E2E8F0]">

                    <div id="mcq_options_section" class="{{ strtoupper($question->type) !== 'MCQ' ? 'hidden' : '' }} space-y-4">
                        <h3 class="text-sm font-bold text-[#0F172A] flex items-center gap-2">
                            <i class="fa-solid fa-list-check text-[#1D4ED8]"></i> Configured Answer Selection Metrics
                        </h3>
                        
                        @php
                            $options = is_array($question->options) ? $question->options : json_decode($question->options ?? '[]', true);
                            $correctAnswer = $question->correct_answer['mcq'] ?? '';
                        @endphp

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach(['A', 'B', 'C', 'D'] as $index => $label)
                                <div class="flex items-center gap-3 bg-[#F8FAFC] p-3 rounded-xl border border-[#E2E8F0]">
                                    <input type="radio" name="mcq_correct_option" value="{{ $label }}" {{ $correctAnswer == $label ? 'checked' : '' }} class="w-4 h-4 text-[#1D4ED8] focus:ring-blue-500">
                                    <span class="text-sm font-bold text-[#64748B]">{{ $label }}</span>
                                    <input type="text" name="mcq_options[{{ $label }}]" value="{{ $options[$label] ?? '' }}" placeholder="Fill choice parameter structure option details..." class="flex-1 bg-white border border-[#E2E8F0] rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:border-[#1D4ED8] transition-colors">
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div id="essay_rubric_section" class="{{ strtoupper($question->type) !== 'ESSAY' ? 'hidden' : '' }} space-y-3">
                        <h3 class="text-sm font-bold text-[#0F172A] flex items-center gap-2">
                            <i class="fa-solid fa-spell-check text-[#DB2777]"></i> Evaluator Assessment Matrix Guidelines
                        </h3>
                        <textarea name="essay_rubric_guidelines" rows="3" placeholder="Define expected solutions criteria matrices to aid uniform staff evaluation parameters logging..." class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm font-medium focus:outline-none focus:border-[#1D4ED8] transition-all">{{ $question->correct_answer['rubric'] ?? '' }}</textarea>
                    </div>

                    <div class="flex justify-end items-center gap-3 pt-4 border-t border-[#E2E8F0]">
                        <a href="{{ route('teacher.question-bank') }}" class="px-5 py-2.5 bg-[#F1F5F9] border border-[#E2E8F0] rounded-xl text-sm font-semibold text-[#475569] hover:bg-slate-200 transition-colors">Cancel</a>
                        <button type="submit" class="px-6 py-2.5 bg-[#1D4ED8] hover:bg-blue-800 text-white font-semibold text-sm rounded-xl shadow-md shadow-blue-500/10 transition-all">Save Matrix Updates</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        const questionTypeSelect = document.getElementById('question_type');
        const mcqSection = document.getElementById('mcq_options_section');
        const essaySection = document.getElementById('essay_rubric_section');

        questionTypeSelect.addEventListener('change', function() {
            if (this.value === 'MCQ') {
                mcqSection.classList.remove('hidden');
                essaySection.classList.add('hidden');
            } else {
                mcqSection.classList.add('hidden');
                essaySection.classList.remove('hidden');
            }
        });
    </script>
</body>
</html>