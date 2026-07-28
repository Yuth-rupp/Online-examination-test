<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem – Edit Question #{{ $question->id }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 99px; }

        /* ── SIDEBAR ── */
        .nav-link { display:flex; align-items:center; gap:11px; padding:9px 12px; border-radius:12px; text-decoration:none; font-size:13.5px; font-weight:500; color:#64748B; transition:all .2s; }
        .nav-link:hover { background:#F8FAFC; color:#1E293B; }
        .nav-link.active { background:#EFF6FF; color:#1D4ED8; font-weight:700; }
        .nav-icon { width:34px; height:34px; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; transition:all .2s; }
        .nav-link:hover .nav-icon { background:#F1F5F9; }
        .nav-link.active .nav-icon { background:#1D4ED8; color:#fff; }

        /* ── FORM ── */
        .fi { transition:all .2s; }
        .fi:focus { outline:none; border-color:#2563EB; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.12); }

        /* ── SEGMENTED TYPE BUTTONS ── */
        .seg { flex:1; padding:7px 8px; border-radius:9px; font-size:12px; font-weight:600; cursor:pointer; transition:all .2s; color:#64748B; background:none; border:none; font-family:inherit; }
        .seg:hover:not(.seg-on) { background:#F1F5F9; color:#1E293B; }
        .seg.seg-mcq   { background:#2563EB; color:#fff; box-shadow:0 2px 8px rgba(37,99,235,.25); }
        .seg.seg-tf    { background:#10B981; color:#fff; box-shadow:0 2px 8px rgba(16,185,129,.25); }
        .seg.seg-essay { background:#8B5CF6; color:#fff; box-shadow:0 2px 8px rgba(139,92,246,.25); }

        /* ── DIFFICULTY BUTTONS ── */
        .dbtn { flex:1; padding:7px 8px; border-radius:9px; font-size:12px; font-weight:700; cursor:pointer; transition:all .2s; border:none; font-family:inherit; color:#64748B; background:none; }
        .dbtn:hover:not(.dbtn-on) { background:#F1F5F9; color:#1E293B; }
        .dbtn.d-easy   { background:#10B981; color:#fff; box-shadow:0 2px 8px rgba(16,185,129,.25); }
        .dbtn.d-medium { background:#F59E0B; color:#fff; box-shadow:0 2px 8px rgba(245,158,11,.25); }
        .dbtn.d-hard   { background:#EF4444; color:#fff; box-shadow:0 2px 8px rgba(239,68,68,.25); }

        /* ── MCQ ROW ── */
        .mcq-row { display:flex; align-items:center; gap:12px; padding:12px 14px; border:1.5px solid #E2E8F0; border-radius:14px; background:#F8FAFC; transition:all .2s; }
        .mcq-row:hover { border-color:#CBD5E1; background:#fff; }
        .mcq-row:focus-within { border-color:#2563EB; background:#fff; box-shadow:0 0 0 3px rgba(37,99,235,.08); }
        .mcq-row.correct { border-color:#10B981; background:#ECFDF5; }
        .lbadge { width:26px; height:26px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:10px; font-weight:900; flex-shrink:0; }

        /* ── TF CARD ── */
        .tf-card { display:flex; align-items:center; gap:12px; padding:14px 16px; border:1.5px solid #E2E8F0; border-radius:14px; cursor:pointer; transition:all .2s; background:#F8FAFC; }
        .tf-card:hover { border-color:#CBD5E1; background:#fff; }
        .tf-card.sel-true  { border-color:#10B981; background:#ECFDF5; }
        .tf-card.sel-false { border-color:#EF4444; background:#FEF2F2; }

        /* ── UPLOAD ZONE ── */
        .upzone { border:2px dashed #E2E8F0; border-radius:14px; padding:18px; text-align:center; cursor:pointer; transition:all .2s; background:#FAFCFF; }
        .upzone:hover { border-color:#2563EB; background:#EFF6FF; }
        .upzone.csv:hover { border-color:#10B981; background:#ECFDF5; }

        /* ── LIVE DOT ── */
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
        .ldot { animation:pulse-dot 1.6s infinite; }

        /* ── TOAST ── */
        #toast-box { position:fixed; bottom:22px; right:22px; z-index:9999; display:flex; flex-direction:column; gap:8px; }
        @keyframes toastIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
        .toast { display:flex; align-items:center; gap:10px; color:#fff; border-radius:12px; padding:11px 16px; font-size:13px; font-weight:600; box-shadow:0 8px 24px rgba(0,0,0,.15); animation:toastIn .3s ease; min-width:220px; font-family:'Inter',sans-serif; }
        .toast.success { background:#10B981; }
        .toast.info    { background:#2563EB; }
        .toast.warning { background:#F59E0B; }
        .toast.error   { background:#EF4444; }

        /* ── FADE ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .fu { animation:fadeUp .3s ease both; }
    </style>
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] min-h-screen flex overflow-x-hidden">

<!-- ══════════════ SIDEBAR ══════════════ -->
@include('partials.teacher-sidebar')

<!-- ══════════════ MAIN ══════════════ -->
<div class="flex-1 flex flex-col min-w-0">

    <!-- HEADER -->
    <header class="h-[72px] bg-white border-b border-[#E2E8F0] flex items-center justify-between px-7 sticky top-0 z-10 flex-shrink-0">
        <div>
            <div class="flex items-center gap-1.5 text-[11px] font-semibold text-[#94A3B8] mb-0.5">
                <a href="{{ route('teacher.question-bank') }}" class="hover:text-[#2563EB] transition-colors">Question Bank</a>
                <i class="fa-solid fa-chevron-right text-[9px]"></i>
                <span class="text-[#64748B]">Edit Question</span>
            </div>
            <div class="flex items-center gap-2.5">
                <h1 class="text-xl font-black text-[#0F172A] tracking-tight leading-none">Edit Question</h1>
                <span class="text-[11px] font-bold text-[#64748B] bg-[#F1F5F9] border border-[#E2E8F0] px-2.5 py-1 rounded-lg font-mono">#{{ $question->id }}</span>
                <span class="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded-lg" id="mode-badge"
                      style="background:#EEF2FF;color:#4338CA;">MCQ</span>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="hidden md:block text-xs font-bold text-[#64748B] bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-2 rounded-lg font-mono tabular-nums" id="live-clock">--:--:--</div>
            <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-semibold text-[#94A3B8] bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-2 rounded-xl">
                <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] ldot"></span>
                <span id="save-status">Editing</span>
            </div>
            <a href="{{ route('teacher.question-bank') }}"
               class="px-4 py-2 text-sm font-bold text-[#64748B] bg-white border border-[#E2E8F0] hover:bg-[#F8FAFC] rounded-xl transition-all">
                Cancel
            </a>
            <button type="submit" form="editQuestionForm"
                    class="flex items-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold px-5 py-2 rounded-xl shadow-md shadow-blue-500/15 text-sm transition-all">
                <i class="fa-solid fa-floppy-disk"></i> Save Changes
            </button>
        </div>
    </header>

    <main class="flex-1 overflow-y-auto">
    <div class="p-7 max-w-[1440px] mx-auto w-full space-y-5">

        @if ($errors->any())
        <div class="p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-2xl fu">
            <div class="flex items-start gap-3">
                <div class="w-8 h-8 rounded-xl bg-red-500 flex items-center justify-center text-white flex-shrink-0">
                    <i class="fa-solid fa-triangle-exclamation text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-bold text-[#991B1B] mb-1">Please fix the following errors:</p>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li class="text-xs font-medium text-[#B91C1C]">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif

        <form action="{{ route('questions.update', $question->id) }}" method="POST" enctype="multipart/form-data" id="editQuestionForm">
        @csrf @method('PUT')
        <input type="hidden" id="remove_image" name="remove_image" value="0">
        <input type="hidden" id="remove_csv"   name="remove_csv"   value="0">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            <!-- ── LEFT COLUMN ─────────────────────────── -->
            <div class="lg:col-span-2 space-y-5">

                <!-- ① META CONTROLS -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fu">
                    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">

                        <!-- Exam ID (read-only — reassigning a question to a different
                             exam here used to be a plain editable text box; a stray edit
                             or paste would silently unlink it from its exam, which is what
                             caused graded submissions to later show "No questions found"
                             even though students had already answered them.) -->
                        <div class="md:col-span-5 space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-link text-[#2563EB] text-xs"></i> Exam Assignment ID
                            </label>
                            <input type="text" value="{{ $question->exam_id ?? 'Not assigned to an exam' }}"
                                   readonly disabled
                                   class="fi w-full bg-[#F1F5F9] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-semibold text-[#64748B] font-mono cursor-not-allowed">
                            <input type="hidden" name="exam_id" value="{{ $question->exam_id }}">
                        </div>

                        <!-- Type -->
                        <div class="md:col-span-4 space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-shapes text-[#2563EB] text-xs"></i> Question Type
                            </label>
                            <!-- Hidden select kept for form submission -->
                            <select id="question_type" name="question_type" class="hidden">
                                <option value="MCQ"        {{ old('question_type', strtoupper($question->type))=='MCQ'        ? 'selected':'' }}>MCQ</option>
                                <option value="TRUE/FALSE" {{ old('question_type', strtoupper($question->type))=='TRUE/FALSE' ? 'selected':'' }}>True/False</option>
                                <option value="ESSAY"      {{ old('question_type', strtoupper($question->type))=='ESSAY'      ? 'selected':'' }}>Essay</option>
                            </select>
                            <div class="flex gap-1 bg-[#F1F5F9] p-1 rounded-xl">
                                <button type="button" onclick="switchType('MCQ')"        id="sbtn-MCQ"  class="seg">MCQ</button>
                                <button type="button" onclick="switchType('TRUE/FALSE')" id="sbtn-TF"   class="seg">True/False</button>
                                <button type="button" onclick="switchType('ESSAY')"      id="sbtn-ESS"  class="seg">Essay</button>
                            </div>
                        </div>

                        <!-- Points -->
                        <div class="md:col-span-2 space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5 justify-center">
                                <i class="fa-solid fa-star text-[#F59E0B] text-xs"></i> Points
                            </label>
                            <input type="number" name="points" min="1"
                                   value="{{ old('points', $question->points ?? 1) }}"
                                   oninput="syncPts(this.value)"
                                   class="fi w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-2 py-2.5 text-sm font-black text-center text-[#1E293B]">
                        </div>

                        <!-- Difficulty -->
                        <div class="md:col-span-12 space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-signal text-[#2563EB] text-xs"></i> Difficulty Level
                            </label>
                            <select id="difficulty_select" name="difficulty" class="hidden">
                                <option value="Easy"   {{ strtolower($question->difficulty)==='easy'   ? 'selected':'' }}>Easy</option>
                                <option value="Medium" {{ (strtolower($question->difficulty)==='medium'||empty($question->difficulty)) ? 'selected':'' }}>Medium</option>
                                <option value="Hard"   {{ strtolower($question->difficulty)==='hard'   ? 'selected':'' }}>Hard</option>
                            </select>
                            <div class="flex gap-1.5 bg-[#F1F5F9] p-1 rounded-xl max-w-xs">
                                <button type="button" onclick="switchDiff('Easy')"   id="dbtn-Easy"   class="dbtn">🟢 Easy</button>
                                <button type="button" onclick="switchDiff('Medium')" id="dbtn-Medium" class="dbtn">🟡 Medium</button>
                                <button type="button" onclick="switchDiff('Hard')"   id="dbtn-Hard"   class="dbtn">🔴 Hard</button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ② QUESTION TEXT -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden fu" style="animation-delay:.05s">
                    <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#E2E8F0] bg-[#FAFCFF]">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                                <i class="fa-solid fa-pencil text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#0F172A]">Question Prompt</p>
                                <p class="text-[11px] text-[#94A3B8]">Edit the question text below</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-[#94A3B8] tabular-nums">
                            <span id="char-count">0</span> chars
                        </span>
                    </div>
                    <div class="px-5 pt-3 pb-4">
                        <textarea id="question_text" name="question_text" rows="5"
                                  placeholder="Type your question here…"
                                  oninput="syncCharCount(this.value);updatePreview();"
                                  class="fi w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] resize-none leading-relaxed">{!! old('question_text', $question->content) !!}</textarea>
                    </div>
                </div>

                <!-- ③ FILE UPLOADS -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden fu" style="animation-delay:.1s">
                    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-[#E2E8F0] bg-[#FAFCFF]">
                        <div class="w-8 h-8 rounded-xl bg-[#FEF3C7] flex items-center justify-center text-[#F59E0B]">
                            <i class="fa-solid fa-paperclip text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#0F172A]">File Attachments</p>
                            <p class="text-[11px] text-[#94A3B8]">Replace or remove linked media files</p>
                        </div>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

                        <!-- Image -->
                        <div class="space-y-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-regular fa-image text-[#2563EB] text-xs"></i> Question Image
                            </p>
                            <div class="upzone" onclick="document.getElementById('question_image').click()">
                                <i class="fa-regular fa-image text-xl text-[#2563EB] opacity-50 mb-1.5 block"></i>
                                <p class="text-xs font-semibold text-[#64748B]">Click to replace image</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">PNG, JPG, WebP — max 5MB</p>
                            </div>
                            <input type="file" id="question_image" name="question_image" accept="image/*" class="hidden" onchange="handleNewImage(this)">

                            @if(!empty($question->media_url))
                            <div id="saved-image-preview" class="flex items-center justify-between bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $question->media_full_url }}" class="h-12 w-16 rounded-lg border border-[#E2E8F0] object-cover shadow-sm" alt="Attached Image">
                                    <div>
                                        <p class="text-xs font-bold text-[#1E293B]">Current image</p>
                                        <p class="text-[10px] text-[#94A3B8] mt-0.5">Click above to replace</p>
                                    </div>
                                </div>
                                <button type="button" onclick="removeFile('question_image','saved-image-preview','remove_image')"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#FEF2F2] text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition-all text-xs">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                            @endif
                            <div id="new-image-preview" class="hidden">
                                <div class="relative inline-block rounded-xl overflow-hidden border border-[#E2E8F0] shadow-sm">
                                    <img id="new-image-el" class="h-20 w-auto object-cover" alt="New image">
                                    <span class="absolute top-1.5 left-1.5 text-[9px] font-black bg-[#10B981] text-white rounded-md px-1.5 py-0.5">NEW</span>
                                </div>
                            </div>
                        </div>

                        <!-- CSV -->
                        <div class="space-y-3">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-file-csv text-[#10B981] text-xs"></i> CSV File
                            </p>
                            <div class="upzone csv" onclick="document.getElementById('question_csv').click()">
                                <i class="fa-solid fa-file-csv text-xl text-[#10B981] opacity-60 mb-1.5 block"></i>
                                <p class="text-xs font-semibold text-[#64748B]">Click to replace CSV</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">Accepts .csv spreadsheet files</p>
                            </div>
                            <input type="file" id="question_csv" name="question_csv" accept=".csv" class="hidden" onchange="handleNewCsv(this)">

                            @if(!empty($question->csv_url))
                            <div id="saved-csv-preview" class="flex items-center justify-between bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-lg bg-[#ECFDF5] flex items-center justify-center text-[#10B981]">
                                        <i class="fa-solid fa-file-csv"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-xs font-bold text-[#1E293B] truncate max-w-[130px]">{{ $question->original_filename ?? basename($question->csv_url) }}</p>
                                        <p class="text-[10px] text-[#94A3B8] mt-0.5">Current file</p>
                                    </div>
                                </div>
                                <button type="button" onclick="removeFile('question_csv','saved-csv-preview','remove_csv')"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg bg-[#FEF2F2] text-[#EF4444] hover:bg-[#EF4444] hover:text-white transition-all text-xs">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                            @endif
                            <div id="new-csv-badge" class="hidden flex items-center justify-between bg-[#ECFDF5] border border-[#A7F3D0] rounded-xl px-3 py-2.5">
                                <span class="text-[11px] font-bold text-[#065F46] truncate flex-1 mr-2" id="new-csv-name"></span>
                                <button type="button" onclick="clearNewCsv()" class="text-[#94A3B8] hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- ④ ANSWER SECTION (switches by type) -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden fu" style="animation-delay:.15s">
                    <div class="flex items-center gap-3 px-5 py-3.5 border-b border-[#E2E8F0] bg-[#FAFCFF]">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0" id="ans-icon-wrap" style="background:#ECFDF5;">
                            <i class="fa-solid fa-circle-check text-[#10B981] text-sm"></i>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-[#0F172A]" id="ans-title">Answer Options</p>
                            <p class="text-[11px] text-[#94A3B8]" id="ans-sub">Select the radio next to the correct answer</p>
                        </div>
                    </div>

                    <div class="p-5 space-y-3">

                        <!-- MCQ -->
                        <div id="mcq_options_section">
                            @php $correctAnswer = $question->correct_option; @endphp
                            <div class="space-y-2.5">
                                @php $letters=[['A','letter-a','#DBEAFE','#1D4ED8'],['B','letter-b','#D1FAE5','#065F46'],['C','letter-c','#FEF3C7','#92400E'],['D','letter-d','#F5F3FF','#6D28D9']]; @endphp
                                @foreach($letters as [$lbl,$cls,$bg,$color])
                                <div class="mcq-row mcq-option-row {{ $correctAnswer==$lbl ? 'correct' : '' }}" id="row-{{ $lbl }}">
                                    <input type="radio" name="mcq_correct_option" value="{{ $lbl }}"
                                           {{ $correctAnswer==$lbl ? 'checked':'' }}
                                           class="w-4 h-4 accent-[#2563EB] cursor-pointer flex-shrink-0"
                                           onchange="highlightRows()">
                                    <div class="lbadge" style="background:{{ $bg }};color:{{ $color }};">{{ $lbl }}</div>
                                    <input type="text" name="mcq_options[{{ $lbl }}]"
                                           value="{{ $question->{'option_'.strtolower($lbl)} ?? '' }}"
                                           placeholder="Option {{ $lbl }}…"
                                           class="flex-1 bg-transparent border-none outline-none text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                                </div>
                                @endforeach
                            </div>
                            <p class="text-[11px] text-[#94A3B8] font-medium mt-3 flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info text-[#BFDBFE]"></i>
                                The highlighted row is the correct answer.
                            </p>
                        </div>

                        <!-- True/False -->
                        @php $tfCorrect = strtoupper($question->correct_option ?? ''); @endphp
                        <div id="tf_options_section" class="hidden space-y-2.5">
                            <label class="tf-card {{ $tfCorrect==='TRUE' ? 'sel-true':'' }}" onclick="selectTF(this,'TRUE')">
                                <input type="radio" name="tf_correct_option" value="TRUE" {{ $tfCorrect==='TRUE' ? 'checked':'' }} class="w-4 h-4 accent-[#10B981] cursor-pointer">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" id="tf-true-icon"
                                     style="{{ $tfCorrect==='TRUE' ? 'background:#10B981;color:#fff;' : 'background:#E2E8F0;color:#94A3B8;' }}">
                                    <i class="fa-solid fa-check text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold {{ $tfCorrect==='TRUE' ? 'text-[#065F46]' : 'text-[#1E293B]' }}">True</p>
                                    <p class="text-[11px] text-[#94A3B8]">This statement is factually correct</p>
                                </div>
                            </label>
                            <label class="tf-card {{ $tfCorrect==='FALSE' ? 'sel-false':'' }}" onclick="selectTF(this,'FALSE')">
                                <input type="radio" name="tf_correct_option" value="FALSE" {{ $tfCorrect==='FALSE' ? 'checked':'' }} class="w-4 h-4 accent-[#EF4444] cursor-pointer">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" id="tf-false-icon"
                                     style="{{ $tfCorrect==='FALSE' ? 'background:#EF4444;color:#fff;' : 'background:#E2E8F0;color:#94A3B8;' }}">
                                    <i class="fa-solid fa-xmark text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold {{ $tfCorrect==='FALSE' ? 'text-[#991B1B]' : 'text-[#1E293B]' }}">False</p>
                                    <p class="text-[11px] text-[#94A3B8]">This statement is incorrect</p>
                                </div>
                            </label>
                        </div>

                        <!-- Essay -->
                        <div id="essay_rubric_section" class="hidden space-y-4">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                    <i class="fa-solid fa-clipboard-list text-[#8B5CF6] text-xs"></i> Grading Rubric & Model Answer
                                </label>
                                <textarea name="essay_rubric_guidelines" rows="4"
                                          placeholder="Define grading criteria, key concepts, expected structure…"
                                          class="fi w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] resize-none">{{ $question->essay_rubric }}</textarea>
                            </div>
                            <div class="flex items-start gap-2.5 p-3.5 bg-[#F5F3FF] border border-[#DDD6FE] rounded-xl">
                                <i class="fa-solid fa-circle-info text-[#8B5CF6] text-sm flex-shrink-0 mt-0.5"></i>
                                <p class="text-[11px] font-semibold text-[#6D28D9] leading-relaxed">Essay answers are manually graded. Adding rubrics helps ensure consistent evaluation.</p>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- SUBMIT ROW (mobile) -->
                <div class="flex lg:hidden justify-end gap-3">
                    <a href="{{ route('teacher.question-bank') }}" class="px-4 py-2 text-sm font-bold text-[#64748B] bg-white border border-[#E2E8F0] hover:bg-[#F8FAFC] rounded-xl transition-all">Cancel</a>
                    <button type="submit" class="flex items-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold px-5 py-2 rounded-xl shadow-md text-sm transition-all">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes
                    </button>
                </div>

            </div><!-- /left -->

            <!-- ── RIGHT COLUMN ─────────────────────────── -->
            <div class="space-y-5">

                <!-- Quick Info -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm p-5 fu" style="animation-delay:.2s">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                            <i class="fa-solid fa-circle-info text-xs"></i>
                        </div>
                        <p class="text-sm font-bold text-[#0F172A]">Record Info</p>
                    </div>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-[#94A3B8] font-semibold">Database ID</span>
                            <span class="text-[11px] font-black text-[#1E293B] font-mono">#{{ $question->id }}</span>
                        </div>
                        <div class="h-px bg-[#F1F5F9]"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-[#94A3B8] font-semibold">Created</span>
                            <span class="text-[11px] font-bold text-[#1E293B]">{{ $question->created_at?->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="h-px bg-[#F1F5F9]"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-[#94A3B8] font-semibold">Last Updated</span>
                            <span class="text-[11px] font-bold text-[#1E293B]">{{ $question->updated_at?->format('M d, Y') ?? 'N/A' }}</span>
                        </div>
                        <div class="h-px bg-[#F1F5F9]"></div>
                        <div class="flex items-center justify-between">
                            <span class="text-[11px] text-[#94A3B8] font-semibold">Linked Exam</span>
                            <span class="text-[11px] font-bold text-[#1E293B] font-mono truncate max-w-[110px]">{{ $question->exam_id ?? 'None' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Live Preview -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm p-5 fu" style="animation-delay:.25s">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-[#F5F3FF] flex items-center justify-center text-[#8B5CF6]">
                            <i class="fa-solid fa-eye text-xs"></i>
                        </div>
                        <p class="text-sm font-bold text-[#0F172A]">Live Preview</p>
                        <div class="flex items-center gap-1 ml-auto">
                            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] ldot"></span>
                            <span class="text-[10px] text-[#94A3B8] font-semibold">Auto-updates</span>
                        </div>
                    </div>
                    <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 space-y-2.5">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" id="prev-type" style="background:#EEF2FF;color:#4338CA;">MCQ</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" id="prev-diff" style="background:#FEF3C7;color:#92400E;">Medium</span>
                            <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1" style="background:#ECFDF5;color:#065F46;">
                                <i class="fa-solid fa-star text-[8px]"></i>
                                <span id="prev-pts">{{ $question->points ?? 1 }}</span> pts
                            </span>
                        </div>
                        <p class="text-xs font-medium text-[#1E293B] leading-relaxed" id="prev-q">{{ Str::limit($question->content, 120) }}</p>
                    </div>
                </div>

                <!-- Save CTA -->
                <div class="rounded-2xl p-5 text-white shadow-lg fu" style="background:linear-gradient(135deg,#2563EB,#1E40AF);animation-delay:.3s;">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-solid fa-wand-magic-sparkles text-blue-200 text-sm"></i>
                        <p class="text-xs font-bold text-blue-100 uppercase tracking-wide">Ready to update?</p>
                    </div>
                    <p class="text-sm font-medium text-white/90 leading-relaxed">All changes are saved to the question bank and linked exams immediately.</p>
                    <button type="submit" form="editQuestionForm"
                            class="mt-4 w-full flex items-center justify-center gap-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold text-sm py-2.5 rounded-xl transition-all">
                        <i class="fa-solid fa-floppy-disk"></i> Save Changes Now
                    </button>
                </div>

            </div><!-- /right -->

        </div><!-- /grid -->
        </form>
    </div>
    </main>
</div>

<div id="toast-box"></div>

<script>
// ── CLOCK ─────────────────────────────────────
function updateClock() { document.getElementById('live-clock').textContent = new Date().toLocaleTimeString('en-US',{hour12:false}); }
updateClock(); setInterval(updateClock,1000);

// ── AUTO-SAVE STATUS ──────────────────────────
let saveTimer;
document.querySelectorAll('input,textarea,select').forEach(el => {
    el.addEventListener('input',()=>{
        const s=document.getElementById('save-status');
        if(s){s.textContent='Unsaved…';clearTimeout(saveTimer);saveTimer=setTimeout(()=>s.textContent='Editing',2000);}
    });
});

// ── CHAR COUNT ────────────────────────────────
function syncCharCount(v){ document.getElementById('char-count').textContent=v.length; }
(function(){ const t=document.getElementById('question_text'); if(t) syncCharCount(t.value); })();

// ── LIVE PREVIEW ──────────────────────────────
function updatePreview(){
    const ta=document.getElementById('question_text');
    const pq=document.getElementById('prev-q');
    if(ta&&pq) pq.textContent=ta.value.trim().slice(0,140)||(ta.value.trim().slice(0,140)||'Your question will appear here…');
}

// ── TOAST ─────────────────────────────────────
function toast(msg,type='info'){
    const icons={success:'fa-circle-check',info:'fa-circle-info',warning:'fa-triangle-exclamation',error:'fa-circle-xmark'};
    const box=document.getElementById('toast-box');
    const t=document.createElement('div');
    t.className=`toast ${type}`;
    t.innerHTML=`<i class="fa-solid ${icons[type]}"></i>${msg}`;
    box.appendChild(t);
    setTimeout(()=>{t.style.transition='all .3s';t.style.opacity='0';t.style.transform='translateY(8px)';setTimeout(()=>t.remove(),300);},3000);
}

// ── POINTS SYNC ───────────────────────────────
function syncPts(v){ document.getElementById('prev-pts').textContent=v; }

// ── TYPE SWITCH ───────────────────────────────
const typeStyles={
    MCQ:    {seg:'seg-mcq',  badge:['#EEF2FF','#4338CA'],  icon:['#ECFDF5','fa-circle-check','#10B981'],  title:'Answer Options',         sub:'Select the radio next to the correct answer'},
    'TRUE/FALSE':{seg:'seg-tf',   badge:['#ECFDF5','#065F46'],  icon:['#FEF3C7','fa-circle-half-stroke','#F59E0B'], title:'True / False',           sub:'Mark which statement is correct'},
    ESSAY:  {seg:'seg-essay',badge:['#F5F3FF','#6D28D9'],  icon:['#F5F3FF','fa-pen-nib','#8B5CF6'],      title:'Essay Rubric & Criteria', sub:'Define grading rubrics and word limits'},
};
function switchType(type){
    document.getElementById('question_type').value=type;
    // Buttons
    [['MCQ','sbtn-MCQ'],['TRUE/FALSE','sbtn-TF'],['ESSAY','sbtn-ESS']].forEach(([k,id])=>{
        const b=document.getElementById(id);
        b.className='seg'+(k===type?' '+typeStyles[type].seg:'');
    });
    // Sections
    document.getElementById('mcq_options_section').classList.toggle('hidden',type!=='MCQ');
    document.getElementById('tf_options_section').classList.toggle('hidden',type!=='TRUE/FALSE');
    document.getElementById('essay_rubric_section').classList.toggle('hidden',type!=='ESSAY');
    // Badge header
    const s=typeStyles[type];
    const mb=document.getElementById('mode-badge');
    if(mb){mb.textContent=type+' MODE';mb.style.background=s.badge[0];mb.style.color=s.badge[1];}
    // Prev badge
    const pt=document.getElementById('prev-type');
    if(pt){pt.textContent=type;pt.style.background=s.badge[0];pt.style.color=s.badge[1];}
    // Ans icon
    const iw=document.getElementById('ans-icon-wrap');
    if(iw)iw.innerHTML=`<i class="fa-solid ${s.icon[1]} text-sm" style="color:${s.icon[2]}"></i>`;
    if(iw)iw.style.background=s.icon[0];
    document.getElementById('ans-title').textContent=s.title;
    document.getElementById('ans-sub').textContent=s.sub;
}

// ── DIFFICULTY SWITCH ─────────────────────────
const diffMap={Easy:['d-easy','#ECFDF5','#065F46'],Medium:['d-medium','#FEF3C7','#92400E'],Hard:['d-hard','#FEF2F2','#991B1B']};
function switchDiff(level){
    document.getElementById('difficulty_select').value=level;
    ['Easy','Medium','Hard'].forEach(l=>{
        const b=document.getElementById('dbtn-'+l);
        b.className='dbtn'+(l===level?' '+diffMap[level][0]:'');
    });
    const pd=document.getElementById('prev-diff');
    if(pd){pd.textContent=level;pd.style.background=diffMap[level][1];pd.style.color=diffMap[level][2];}
}

// ── MCQ HIGHLIGHT ─────────────────────────────
function highlightRows(){
    ['A','B','C','D'].forEach(l=>{
        const r=document.getElementById('row-'+l);
        const rb=document.querySelector(`input[name="mcq_correct_option"][value="${l}"]`);
        if(r&&rb) r.classList.toggle('correct',rb.checked);
    });
}

// ── TRUE/FALSE ────────────────────────────────
function selectTF(label,val){
    document.querySelectorAll('.tf-card').forEach(c=>c.classList.remove('sel-true','sel-false'));
    label.classList.add(val==='TRUE'?'sel-true':'sel-false');
    const ti=document.getElementById('tf-true-icon'),fi=document.getElementById('tf-false-icon');
    if(val==='TRUE'){
        if(ti){ti.style.background='#10B981';ti.style.color='#fff';}
        if(fi){fi.style.background='#E2E8F0';fi.style.color='#94A3B8';}
    }else{
        if(fi){fi.style.background='#EF4444';fi.style.color='#fff';}
        if(ti){ti.style.background='#E2E8F0';ti.style.color='#94A3B8';}
    }
}

// ── FILE REMOVE ───────────────────────────────
function removeFile(inputId, previewId, hiddenId){
    document.getElementById(inputId).value='';
    const p=document.getElementById(previewId);
    if(p) p.style.display='none';
    document.getElementById(hiddenId).value='1';
    toast('File removed','warning');
}

// ── NEW IMAGE PREVIEW ─────────────────────────
function handleNewImage(input){
    if(input.files&&input.files[0]){
        const reader=new FileReader();
        reader.onload=e=>{
            document.getElementById('new-image-el').src=e.target.result;
            document.getElementById('new-image-preview').classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
        toast('New image selected','success');
    }
}

// ── NEW CSV ───────────────────────────────────
function handleNewCsv(input){
    if(input.files&&input.files[0]){
        document.getElementById('new-csv-name').textContent=input.files[0].name;
        document.getElementById('new-csv-badge').classList.remove('hidden');
        toast('CSV selected: '+input.files[0].name,'success');
    }
}
function clearNewCsv(){
    document.getElementById('question_csv').value='';
    document.getElementById('new-csv-badge').classList.add('hidden');
    toast('CSV removed','warning');
}

// ── INIT ──────────────────────────────────────
(function(){
    const sel=document.getElementById('question_type');
    const raw=sel.value;
    switchType(raw);
    // set difficulty from PHP
    @php
        $dk = strtolower($question->difficulty ?? 'medium');
        $dInit = $dk==='easy' ? 'Easy' : ($dk==='hard' ? 'Hard' : 'Medium');
    @endphp
    switchDiff('{{ $dInit }}');
    updatePreview();
    // init char count
    const ta=document.getElementById('question_text');
    if(ta) syncCharCount(ta.value);
})();
</script>
</body>
</html>