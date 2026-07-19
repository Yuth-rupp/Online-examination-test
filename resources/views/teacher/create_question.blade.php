<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem – Create Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body { font-family: 'Inter', system-ui, sans-serif; -webkit-font-smoothing: antialiased; }

        /* ── SCROLLBAR ── */
        ::-webkit-scrollbar { width: 5px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: #E2E8F0; border-radius: 99px; }

        /* ── SIDEBAR NAV LINK ── */
        .nav-link {
            display: flex; align-items: center; gap: 11px;
            padding: 9px 12px; border-radius: 12px; text-decoration: none;
            font-size: 13.5px; font-weight: 500; color: #64748B; transition: all .2s;
        }
        .nav-link:hover { background: #F8FAFC; color: #1E293B; }
        .nav-link.active { background: #EFF6FF; color: #1D4ED8; font-weight: 700; }
        .nav-icon {
            width: 34px; height: 34px; border-radius: 9px;
            display: flex; align-items: center; justify-content: center;
            font-size: 14px; flex-shrink: 0; transition: all .2s;
        }
        .nav-link:hover .nav-icon { background: #F1F5F9; }
        .nav-link.active .nav-icon { background: #1D4ED8; color: #fff; }

        /* ── FORM INPUT FOCUS ── */
        .form-input { transition: all .2s; }
        .form-input:focus { outline: none; border-color: #2563EB; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }

        /* ── QUESTION TYPE SEGMENTED CONTROL ── */
        .seg-btn { flex: 1; padding: 7px 10px; border-radius: 9px; font-size: 12px; font-weight: 600; cursor: pointer; transition: all .2s; color: #64748B; background: none; border: none; font-family: inherit; }
        .seg-btn.active-mcq     { background: #2563EB; color: #fff; box-shadow: 0 2px 8px rgba(37,99,235,.25); }
        .seg-btn.active-tf      { background: #10B981; color: #fff; box-shadow: 0 2px 8px rgba(16,185,129,.25); }
        .seg-btn.active-essay   { background: #8B5CF6; color: #fff; box-shadow: 0 2px 8px rgba(139,92,246,.25); }
        .seg-btn:not([class*="active"]):hover { background: #F1F5F9; color: #1E293B; }

        /* ── DIFFICULTY SEGMENTED CONTROL ── */
        .diff-btn { flex: 1; padding: 7px 10px; border-radius: 9px; font-size: 12px; font-weight: 700; cursor: pointer; transition: all .2s; border: none; font-family: inherit; color: #64748B; background: none; }
        .diff-btn.active-easy   { background: #10B981; color: #fff; box-shadow: 0 2px 8px rgba(16,185,129,.25); }
        .diff-btn.active-medium { background: #F59E0B; color: #fff; box-shadow: 0 2px 8px rgba(245,158,11,.25); }
        .diff-btn.active-hard   { background: #EF4444; color: #fff; box-shadow: 0 2px 8px rgba(239,68,68,.25); }
        .diff-btn:not([class*="active"]):hover { background: #F1F5F9; color: #1E293B; }

        /* ── MCQ OPTION ROW ── */
        .mcq-row { display: flex; align-items: center; gap: 12px; padding: 12px 14px; border: 1.5px solid #E2E8F0; border-radius: 14px; background: #F8FAFC; transition: all .2s; }
        .mcq-row:hover { border-color: #CBD5E1; background: #fff; }
        .mcq-row:focus-within { border-color: #2563EB; background: #fff; box-shadow: 0 0 0 3px rgba(37,99,235,.08); }
        .mcq-row.correct { border-color: #10B981; background: #ECFDF5; }
        .letter-badge { width: 26px; height: 26px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 10px; font-weight: 900; flex-shrink: 0; }
        .letter-a { background: #DBEAFE; color: #1D4ED8; }
        .letter-b { background: #D1FAE5; color: #065F46; }
        .letter-c { background: #FEF3C7; color: #92400E; }
        .letter-d { background: #F5F3FF; color: #6D28D9; }

        /* ── TF OPTION ── */
        .tf-option { display: flex; align-items: center; gap: 12px; padding: 14px 16px; border: 1.5px solid #E2E8F0; border-radius: 14px; cursor: pointer; transition: all .2s; background: #F8FAFC; }
        .tf-option:hover { border-color: #CBD5E1; background: #fff; }
        .tf-option.selected-true  { border-color: #10B981; background: #ECFDF5; }
        .tf-option.selected-false { border-color: #EF4444; background: #FEF2F2; }

        /* ── SETTINGS TAB ── */
        .stab { display: flex; align-items: center; justify-content: space-between; padding: 12px 16px; cursor: pointer; transition: all .15s; border-left: 3px solid transparent; }
        .stab:hover { background: #F8FAFC; }
        .stab.stab-active { border-left-color: #2563EB; background: #F8FAFC; }
        .stab.stab-active .stab-label { color: #1D4ED8; font-weight: 700; }

        /* ── TOGGLE SWITCH ── */
        .tgl-track { width: 40px; height: 22px; border-radius: 99px; background: #E2E8F0; position: relative; cursor: pointer; transition: background .25s; flex-shrink: 0; }
        .tgl-track.on { background: #2563EB; }
        .tgl-thumb { position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; box-shadow: 0 1px 4px rgba(0,0,0,.2); transition: transform .25s; }
        .tgl-track.on .tgl-thumb { transform: translateX(18px); }

        /* ── UPLOAD ZONE ── */
        .upload-zone { border: 2px dashed #E2E8F0; border-radius: 14px; padding: 20px; text-align: center; cursor: pointer; transition: all .2s; background: #FAFCFF; }
        .upload-zone:hover { border-color: #2563EB; background: #EFF6FF; }

        /* ── TOOLBAR BTN ── */
        .tb-btn { width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 13px; color: #64748B; cursor: pointer; transition: all .15s; border: none; background: none; font-family: inherit; }
        .tb-btn:hover { background: #fff; color: #1E293B; box-shadow: 0 1px 3px rgba(0,0,0,.08); }
        .tb-sep { width: 1px; height: 18px; background: #E2E8F0; flex-shrink: 0; margin: 0 4px; }

        /* ── TOAST ── */
        #toast-box { position: fixed; bottom: 22px; right: 22px; z-index: 9999; display: flex; flex-direction: column; gap: 8px; }
        @keyframes toastIn { from{opacity:0;transform:translateY(12px)} to{opacity:1;transform:translateY(0)} }
        .toast { display: flex; align-items: center; gap: 10px; color: #fff; border-radius: 12px; padding: 11px 16px; font-size: 13px; font-weight: 600; box-shadow: 0 8px 24px rgba(0,0,0,.15); animation: toastIn .3s ease; min-width: 220px; font-family: 'Inter', sans-serif; }
        .toast.success { background: #10B981; }
        .toast.info    { background: #2563EB; }
        .toast.warning { background: #F59E0B; }

        /* ── LIVE DOT ── */
        @keyframes pulse-dot { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(.75)} }
        .live-dot { animation: pulse-dot 1.6s infinite; }

        /* ── FADE UP ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation: fadeUp .3s ease both; }
    </style>
</head>

<body class="bg-[#F1F5F9] text-[#1E293B] min-h-screen flex overflow-x-hidden">

<!-- ══════════════════════════════════════
     SIDEBAR
══════════════════════════════════════ -->
<aside class="w-[260px] bg-white border-r border-[#E2E8F0] flex flex-col flex-shrink-0 sticky top-0 h-screen z-20">
    <a href="{{ route('teacher.dashboard') }}"
       class="h-[72px] flex items-center px-5 gap-3 border-b border-[#E2E8F0] hover:opacity-90 transition-opacity">
        <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white flex-shrink-0"
             style="background:linear-gradient(135deg,#2563EB 0%,#1E40AF 100%);box-shadow:0 4px 12px rgba(37,99,235,.35);">
            <i class="fa-solid fa-graduation-cap text-base"></i>
        </div>
        <span class="font-black text-[18px] text-[#0F172A] tracking-tight">ExamSystem</span>
    </a>

    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-1 pb-2">Main Menu</p>
        <a href="{{ route('teacher.dashboard') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-house"></i></span><span>Dashboard</span>
        </a>
        <a href="{{ route('teacher.question-bank') }}" class="nav-link active">
            <span class="nav-icon"><i class="fa-solid fa-database"></i></span><span>Question Bank</span>
        </a>
        <a href="{{ route('teacher.monitoring.show') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-display"></i></span><span>Monitoring</span>
        </a>
        <a href="{{ route('teacher.grading.queue') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-pen-to-square"></i></span>
            <span>Grading</span>
            <span class="ml-auto text-[10px] font-bold bg-red-500 text-white rounded-full px-2 py-0.5">45</span>
        </a>
        <a href="{{ route('teacher.analytics') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-chart-line"></i></span><span>Analytics</span>
        </a>
        <p class="text-[10px] font-bold text-[#94A3B8] uppercase tracking-widest px-3 pt-4 pb-2">Account</p>
        <a href="{{ route('teacher.settings') }}" class="nav-link">
            <span class="nav-icon"><i class="fa-solid fa-gear"></i></span><span>Settings</span>
        </a>
    </nav>

    <div class="p-3 border-t border-[#E2E8F0]">
        <a href="{{ route('teacher.settings') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8FAFC] transition-colors">
            <div class="w-9 h-9 rounded-full overflow-hidden border-2 border-[#E2E8F0] flex-shrink-0">
                <img src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'Alex') }}" class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-[#0F172A] truncate">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                <p class="text-xs text-[#94A3B8] font-medium">Senior Faculty</p>
            </div>
            <i class="fa-solid fa-ellipsis-vertical text-[#94A3B8] text-sm"></i>
        </a>
    </div>
</aside>

<!-- ══════════════════════════════════════
     MAIN
══════════════════════════════════════ -->
<div class="flex-1 flex flex-col min-w-0">
<form action="{{ route('questions.store') }}" method="POST" enctype="multipart/form-data" id="questionWorkspaceForm">
@csrf
<input type="hidden" name="type"               id="hidden_question_type"     value="MCQ">
<input type="hidden" name="difficulty"          id="hidden_difficulty"         value="Medium">
<input type="hidden" name="shuffle"             id="hidden_shuffle"            value="1">
<input type="hidden" name="review_required"     id="hidden_review_required"    value="0">
<input type="hidden" name="active_setting_tab"  id="hidden_active_setting_tab" value="general">

<!-- HEADER -->
<header class="h-[72px] bg-white border-b border-[#E2E8F0] flex items-center justify-between px-7 sticky top-0 z-10 flex-shrink-0">
    <div>
        <div class="flex items-center gap-1.5 text-[11px] font-semibold text-[#94A3B8] mb-0.5">
            <a href="{{ route('teacher.question-bank') }}" class="hover:text-[#2563EB] transition-colors">Question Bank</a>
            <i class="fa-solid fa-chevron-right text-[9px]"></i>
            <span class="text-[#64748B]">Add Question</span>
        </div>
        <h1 class="text-xl font-black text-[#0F172A] tracking-tight leading-none">Create New Question</h1>
    </div>
    <div class="flex items-center gap-3">
        <div class="hidden md:block text-xs font-bold text-[#64748B] bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-2 rounded-lg font-mono tabular-nums" id="live-clock">--:--:--</div>
        <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-semibold text-[#94A3B8] bg-[#F8FAFC] border border-[#E2E8F0] px-3 py-2 rounded-xl">
            <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] live-dot"></span>
            <span id="save-status">Draft saved</span>
        </div>
        <a href="{{ route('teacher.question-bank') }}"
           class="px-4 py-2 text-sm font-bold text-[#64748B] bg-white border border-[#E2E8F0] hover:bg-[#F8FAFC] rounded-xl transition-all">
            Cancel
        </a>
        <button type="submit"
                class="flex items-center gap-2 bg-[#2563EB] hover:bg-[#1D4ED8] text-white font-bold px-5 py-2 rounded-xl shadow-md shadow-blue-500/15 text-sm transition-all">
            <i class="fa-solid fa-floppy-disk"></i> Save to Bank
        </button>
    </div>
</header>

@if ($errors->any())
<div class="mx-7 mt-5 p-4 bg-[#FEF2F2] border border-[#FECACA] rounded-2xl fade-up">
    <div class="flex items-start gap-3">
        <div class="w-8 h-8 rounded-xl bg-red-500 flex items-center justify-center text-white flex-shrink-0 mt-0.5">
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

<main class="flex-1 overflow-y-auto">
<div class="p-7 max-w-[1440px] mx-auto w-full space-y-5">

    <!-- ① TOP CONTROLS -->
    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-5 shadow-sm fade-up">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
            <!-- Linked Exam -->
            <div class="md:col-span-5 space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                    <i class="fa-solid fa-link text-[#2563EB] text-xs"></i> Linked Exam Session
                </label>
                <div class="relative">
                    <i class="fa-solid fa-database absolute left-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8] text-xs pointer-events-none"></i>
                    <select name="exam_id" required
                            class="form-input w-full appearance-none bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl pl-9 pr-9 py-2.5 text-sm font-semibold text-[#1E293B]">
                        <option value="" disabled selected>Select an examination session…</option>
                        @foreach($exams as $exam)
                            <option value="{{ $exam->exam_id }}">{{ $exam->title }} — [{{ $exam->access_code ?? 'N/A' }}]</option>
                        @endforeach
                    </select>
                    <i class="fa-solid fa-chevron-down absolute right-3.5 top-1/2 -translate-y-1/2 text-[#94A3B8] text-[10px] pointer-events-none"></i>
                </div>
            </div>
            <!-- Type -->
            <div class="md:col-span-3 space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                    <i class="fa-solid fa-shapes text-[#2563EB] text-xs"></i> Question Type
                </label>
                <div class="flex gap-1 bg-[#F1F5F9] p-1 rounded-xl">
                    <button type="button" onclick="setQuestionType('MCQ')"        id="type_btn_MCQ"   class="seg-btn active-mcq">MCQ</button>
                    <button type="button" onclick="setQuestionType('True/False')" id="type_btn_TF"    class="seg-btn">True/False</button>
                    <button type="button" onclick="setQuestionType('Essay')"      id="type_btn_Essay" class="seg-btn">Essay</button>
                </div>
            </div>
            <!-- Difficulty -->
            <div class="md:col-span-3 space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                    <i class="fa-solid fa-signal text-[#2563EB] text-xs"></i> Difficulty
                </label>
                <div class="flex gap-1 bg-[#F1F5F9] p-1 rounded-xl">
                    <button type="button" onclick="setDifficulty('Easy')"   id="diff_btn_Easy"   class="diff-btn">Easy</button>
                    <button type="button" onclick="setDifficulty('Medium')" id="diff_btn_Medium" class="diff-btn active-medium">Medium</button>
                    <button type="button" onclick="setDifficulty('Hard')"   id="diff_btn_Hard"   class="diff-btn">Hard</button>
                </div>
            </div>
            <!-- Points -->
            <div class="md:col-span-1 space-y-1.5">
                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] text-center block">
                    <i class="fa-solid fa-star text-[#F59E0B] text-xs"></i> Pts
                </label>
                <input type="number" name="points" min="1" max="100" value="5"
                       oninput="syncPoints(this.value)"
                       class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-2 py-2.5 text-sm font-black text-center text-[#1E293B]">
            </div>
        </div>
    </div>

    <!-- ② MAIN GRID -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

        <!-- LEFT -->
        <div class="lg:col-span-2 space-y-5">

            <!-- Question Content Card -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden fade-up" style="animation-delay:.05s">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#E2E8F0] bg-[#FAFCFF]">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                            <i class="fa-solid fa-pencil text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#0F172A]">Question Content</h3>
                            <p class="text-[11px] text-[#94A3B8]">Write your question prompt below</p>
                        </div>
                    </div>
                    <span class="text-[11px] font-semibold text-[#94A3B8] tabular-nums">
                        <span id="char-count">17</span> chars
                    </span>
                </div>

                <!-- Rich Text Toolbar -->
                <div class="flex items-center gap-0.5 px-4 py-2.5 border-b border-[#F1F5F9] bg-[#FAFCFF]">
                    <button type="button" class="tb-btn" title="Bold"><i class="fa-solid fa-bold"></i></button>
                    <button type="button" class="tb-btn" title="Italic"><i class="fa-solid fa-italic"></i></button>
                    <button type="button" class="tb-btn" title="Underline"><i class="fa-solid fa-underline"></i></button>
                    <button type="button" class="tb-btn" title="Bullet list"><i class="fa-solid fa-list-ul"></i></button>
                    <div class="tb-sep"></div>
                    <button type="button" class="tb-btn" title="Superscript"><i class="fa-solid fa-superscript"></i></button>
                    <button type="button" class="tb-btn" title="Subscript"><i class="fa-solid fa-subscript"></i></button>
                    <div class="tb-sep"></div>
                    <button type="button" onclick="triggerImageUpload()" class="tb-btn hover:text-[#2563EB]" title="Attach image">
                        <i class="fa-regular fa-image"></i>
                    </button>
                    <input type="file" name="attachment_media" id="media_file_input" class="hidden" accept="image/*" onchange="handleImagePreview(this)">
                    <button type="button" class="flex items-center gap-1.5 h-8 px-2.5 rounded-lg text-[#64748B] hover:bg-white hover:text-[#8B5CF6] text-[11px] font-bold transition-all border-none bg-none cursor-pointer">
                        <i class="fa-solid fa-square-root-variable"></i> LaTeX
                    </button>
                </div>

                <!-- Image preview -->
                <div id="image_preview_container" class="hidden px-5 pt-4">
                    <div class="relative inline-block border border-[#E2E8F0] rounded-xl overflow-hidden shadow-sm">
                        <img src="" id="image_preview_el" class="h-32 w-auto object-cover" alt="Preview">
                        <button type="button" onclick="removeSelectedImage()"
                                class="absolute top-2 right-2 w-7 h-7 bg-red-500 hover:bg-red-600 text-white rounded-full flex items-center justify-center text-xs shadow-md transition-all">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>

                <!-- Textarea -->
                <div class="px-5 py-4">
                    <textarea name="content" id="question-textarea" rows="6" required
                              placeholder="Type your question here… Be clear and specific."
                              oninput="document.getElementById('char-count').textContent=this.value.length; updatePreview();"
                              class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] resize-none leading-relaxed">What is Database?</textarea>
                </div>
            </div>

            <!-- Answer Options Card -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden fade-up" style="animation-delay:.1s">
                <div class="flex items-center justify-between px-5 py-3.5 border-b border-[#E2E8F0] bg-[#FAFCFF]">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center" id="ans-icon-wrap" style="background:#ECFDF5;">
                            <i class="fa-solid fa-circle-check text-[#10B981] text-sm"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-[#0F172A]" id="variant_box_title">Answer Options</h3>
                            <p class="text-[11px] text-[#94A3B8]" id="variant_box_subtitle">Select the radio next to the correct answer</p>
                        </div>
                    </div>
                    <button type="button" id="shuffle_action_btn" onclick="executeManualShuffle()"
                            class="flex items-center gap-1.5 bg-[#EFF6FF] text-[#2563EB] border border-[#BFDBFE] rounded-xl px-3 py-1.5 text-[11px] font-bold hover:bg-[#DBEAFE] transition-all">
                        <i class="fa-solid fa-shuffle"></i> Shuffle Options
                    </button>
                </div>

                <div class="p-5 space-y-3">

                    <!-- MCQ -->
                    <div id="wrapper_MCQ" class="space-y-2.5">
                        <div class="mcq-row mcq-option-row correct" id="mcq-row-a">
                            <input type="radio" name="correct_option" value="A" checked class="w-4 h-4 accent-[#2563EB] cursor-pointer flex-shrink-0" onchange="highlightCorrectRow()">
                            <div class="letter-badge letter-a">A</div>
                            <input type="text" name="option_a" id="mcq_input_1" required placeholder="Type option A…" class="flex-1 bg-transparent border-none outline-none text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                        </div>
                        <div class="mcq-row mcq-option-row" id="mcq-row-b">
                            <input type="radio" name="correct_option" value="B" class="w-4 h-4 accent-[#2563EB] cursor-pointer flex-shrink-0" onchange="highlightCorrectRow()">
                            <div class="letter-badge letter-b">B</div>
                            <input type="text" name="option_b" id="mcq_input_2" required placeholder="Type option B…" class="flex-1 bg-transparent border-none outline-none text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                        </div>
                        <div class="mcq-row mcq-option-row" id="mcq-row-c">
                            <input type="radio" name="correct_option" value="C" class="w-4 h-4 accent-[#2563EB] cursor-pointer flex-shrink-0" onchange="highlightCorrectRow()">
                            <div class="letter-badge letter-c">C</div>
                            <input type="text" name="option_c" placeholder="Option C (optional)…" class="flex-1 bg-transparent border-none outline-none text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                        </div>
                        <div class="mcq-row mcq-option-row" id="mcq-row-d">
                            <input type="radio" name="correct_option" value="D" class="w-4 h-4 accent-[#2563EB] cursor-pointer flex-shrink-0" onchange="highlightCorrectRow()">
                            <div class="letter-badge" style="background:#F5F3FF;color:#6D28D9;">D</div>
                            <input type="text" name="option_d" placeholder="Option D (optional)…" class="flex-1 bg-transparent border-none outline-none text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                        </div>
                        <p class="text-[11px] text-[#94A3B8] font-medium pt-1 flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-info text-[#BFDBFE]"></i>
                            The highlighted option is marked as the correct answer.
                        </p>
                    </div>

                    <!-- True/False -->
                    <div id="wrapper_TF" class="hidden space-y-2.5">
                        <label class="tf-option selected-true" onclick="markTF(this,'True')">
                            <input type="radio" name="tf_correct" value="True" checked class="w-4 h-4 accent-[#10B981] cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-[#10B981] flex items-center justify-center text-white flex-shrink-0" id="tf-true-icon">
                                <i class="fa-solid fa-check text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#065F46]">True</p>
                                <p class="text-[11px] text-[#94A3B8] font-medium">This statement is factually correct</p>
                            </div>
                        </label>
                        <label class="tf-option" onclick="markTF(this,'False')">
                            <input type="radio" name="tf_correct" value="False" class="w-4 h-4 accent-[#EF4444] cursor-pointer">
                            <div class="w-8 h-8 rounded-full bg-[#E2E8F0] flex items-center justify-center text-[#94A3B8] flex-shrink-0" id="tf-false-icon">
                                <i class="fa-solid fa-xmark text-sm"></i>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-[#1E293B]">False</p>
                                <p class="text-[11px] text-[#94A3B8] font-medium">This statement is incorrect</p>
                            </div>
                        </label>
                    </div>

                    <!-- Essay -->
                    <div id="wrapper_Essay" class="hidden space-y-4">
                        <div class="space-y-1.5">
                            <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-clipboard-list text-[#8B5CF6] text-xs"></i> Grading Criteria & Model Answer
                            </label>
                            <textarea name="essay_guidelines" rows="4"
                                      placeholder="Provide grading rubrics, key concepts, or expected answer structure…"
                                      class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm text-[#1E293B] font-medium placeholder-[#94A3B8] resize-none"></textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8]">Min Words</label>
                                <input type="number" name="min_words" value="0" class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-bold text-[#1E293B]">
                            </div>
                            <div class="space-y-1.5">
                                <label class="text-[10px] font-bold uppercase tracking-widest text-[#94A3B8]">Max Words</label>
                                <input type="number" name="max_words" value="500" class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-bold text-[#1E293B]">
                            </div>
                        </div>
                        <div class="flex items-start gap-2.5 p-3.5 bg-[#F5F3FF] border border-[#DDD6FE] rounded-xl">
                            <i class="fa-solid fa-circle-info text-[#8B5CF6] text-sm flex-shrink-0 mt-0.5"></i>
                            <p class="text-[11px] font-semibold text-[#6D28D9] leading-relaxed">Essay answers are manually graded. Adding rubrics helps ensure consistent grading.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Settings Panel -->
        <div class="space-y-5 fade-up" style="animation-delay:.15s">

            <!-- Settings Card -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-[#E2E8F0] bg-[#FAFCFF] flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-[#EFF6FF] flex items-center justify-center text-[#2563EB]">
                        <i class="fa-solid fa-sliders text-sm"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-[#0F172A]">Question Settings</h3>
                        <p class="text-[11px] text-[#94A3B8]">Configure behavior and metadata</p>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="divide-y divide-[#F1F5F9]">
                    <div class="stab stab-active" id="tab_setting_general" onclick="switchSettingTab('general')">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-gear text-[#64748B] text-sm"></i>
                            <span class="text-sm stab-label" id="text_setting_general">General Settings</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-[#94A3B8]"></i>
                    </div>
                    <div class="stab" id="tab_setting_media" onclick="switchSettingTab('media')">
                        <div class="flex items-center gap-3">
                            <i class="fa-regular fa-images text-[#64748B] text-sm"></i>
                            <span class="text-sm stab-label" id="text_setting_media">Media / CSV Import</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-[#94A3B8]"></i>
                    </div>
                    <div class="stab" id="tab_setting_cat" onclick="switchSettingTab('cat')">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-tags text-[#64748B] text-sm"></i>
                            <span class="text-sm stab-label" id="text_setting_cat">Categorization</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-[10px] text-[#94A3B8]"></i>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="p-5">
                    <!-- General -->
                    <div id="content_setting_general" class="space-y-4">
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-semibold text-[#1E293B]">Enable Answer Shuffling</p>
                                <p class="text-[11px] text-[#94A3B8] mt-0.5">Randomize choices per student</p>
                            </div>
                            <div class="tgl-track on" id="shuffle-tgl" onclick="toggleTgl('shuffle-tgl','hidden_shuffle')">
                                <div class="tgl-thumb"></div>
                            </div>
                        </div>
                        <div class="h-px bg-[#F1F5F9]"></div>
                        <div class="flex items-start justify-between gap-3">
                            <div>
                                <p class="text-[13px] font-semibold text-[#1E293B]">Flag for Final Review</p>
                                <p class="text-[11px] text-[#94A3B8] mt-0.5">Hold deployment pending approval</p>
                            </div>
                            <div class="tgl-track" id="review-tgl" onclick="toggleTgl('review-tgl','hidden_review_required')">
                                <div class="tgl-thumb"></div>
                            </div>
                        </div>
                        <div class="h-px bg-[#F1F5F9]"></div>
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[13px] font-semibold text-[#1E293B]">Question Weight</p>
                                <p class="text-[11px] text-[#94A3B8] mt-0.5">Points for correct answer</p>
                            </div>
                            <div class="flex items-center gap-1 bg-[#FEF3C7] border border-[#FDE68A] rounded-xl px-3 py-1.5">
                                <i class="fa-solid fa-star text-[#F59E0B] text-xs"></i>
                                <span class="text-sm font-black text-[#92400E]" id="points-preview">5</span>
                                <span class="text-[10px] font-semibold text-[#92400E]">pts</span>
                            </div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div id="content_setting_media" class="hidden space-y-4">
                        <div class="space-y-2">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#94A3B8]">Question Image</p>
                            <div class="upload-zone" onclick="triggerImageUpload()">
                                <i class="fa-regular fa-image text-2xl text-[#2563EB] opacity-50 mb-2 block"></i>
                                <p class="text-[11px] font-semibold text-[#64748B]">Click to attach an image</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">PNG, JPG, WebP up to 5MB</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="h-px bg-[#E2E8F0] flex-1"></div>
                            <span class="text-[10px] font-bold text-[#94A3B8] uppercase">or</span>
                            <div class="h-px bg-[#E2E8F0] flex-1"></div>
                        </div>
                        <div class="space-y-2">
                            <p class="text-[11px] font-bold uppercase tracking-widest text-[#94A3B8]">Bulk CSV Import</p>
                            <div class="upload-zone" onclick="triggerCsvUpload()" style="border-color:#D1FAE5;">
                                <i class="fa-solid fa-file-csv text-2xl text-[#10B981] opacity-60 mb-2 block"></i>
                                <p class="text-[11px] font-semibold text-[#64748B]">Click to upload .csv file</p>
                                <p class="text-[10px] text-[#94A3B8] mt-0.5">Bulk import questions from spreadsheet</p>
                            </div>
                            <input type="file" name="questions_csv" id="csv_file_input" class="hidden" accept=".csv" onchange="handleCsvValidation(this)">
                            <div id="csv_badge_container" class="hidden flex items-center justify-between bg-[#ECFDF5] border border-[#A7F3D0] rounded-xl px-3 py-2">
                                <span id="csv_badge_name" class="text-[11px] font-bold text-[#065F46] truncate flex-1 mr-2">
                                    <i class="fa-solid fa-file-excel mr-1"></i> file.csv
                                </span>
                                <button type="button" onclick="clearCsvAttachment()" class="text-[#94A3B8] hover:text-red-500 transition-colors">
                                    <i class="fa-solid fa-xmark text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Categorization -->
                    <div id="content_setting_cat" class="hidden space-y-3">
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-tag text-[#2563EB] text-xs"></i> Course Tags
                            </label>
                            <input type="text" name="tags" placeholder="e.g., SQL, Normalization, Joins"
                                   class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                            <p class="text-[10px] text-[#94A3B8] font-medium">Separate tags with commas</p>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-[11px] font-bold uppercase tracking-widest text-[#94A3B8] flex items-center gap-1.5">
                                <i class="fa-solid fa-book text-[#2563EB] text-xs"></i> Topic / Chapter
                            </label>
                            <input type="text" name="topic" placeholder="e.g., Chapter 3 – Relational Model"
                                   class="form-input w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-medium text-[#1E293B] placeholder-[#CBD5E1]">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Preview Card -->
            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm p-5 fade-up" style="animation-delay:.2s">
                <div class="flex items-center gap-2.5 mb-4">
                    <div class="w-7 h-7 rounded-lg bg-[#F5F3FF] flex items-center justify-center text-[#8B5CF6]">
                        <i class="fa-solid fa-eye text-xs"></i>
                    </div>
                    <p class="text-sm font-bold text-[#0F172A]">Live Preview</p>
                    <div class="flex items-center gap-1 ml-auto text-[10px] text-[#94A3B8] font-medium">
                        <span class="w-1.5 h-1.5 rounded-full bg-[#10B981] live-dot"></span> Auto-updates
                    </div>
                </div>
                <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 space-y-2.5">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" id="preview-type-badge" style="background:#EEF2FF;color:#4338CA;">MCQ</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full" id="preview-diff-badge" style="background:#FEF3C7;color:#92400E;">Medium</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full flex items-center gap-1" style="background:#ECFDF5;color:#065F46;">
                            <i class="fa-solid fa-star text-[8px]"></i>
                            <span id="preview-pts">5</span> pts
                        </span>
                    </div>
                    <p class="text-xs font-medium text-[#1E293B] leading-relaxed" id="preview-q">What is Database?</p>
                </div>
            </div>

            <!-- Save CTA -->
            <div class="rounded-2xl p-5 text-white shadow-lg fade-up" style="background:linear-gradient(135deg,#2563EB 0%,#1E40AF 100%);animation-delay:.25s;">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fa-solid fa-lightbulb text-blue-200 text-sm"></i>
                    <p class="text-xs font-bold text-blue-100 uppercase tracking-wide">Pro Tip</p>
                </div>
                <p class="text-sm font-medium text-white/90 leading-relaxed">After saving, link this question to any exam from the Question Bank table.</p>
                <button type="submit" form="questionWorkspaceForm"
                        class="mt-4 w-full flex items-center justify-center gap-2 bg-white/15 hover:bg-white/25 border border-white/25 text-white font-bold text-sm py-2.5 rounded-xl transition-all">
                    <i class="fa-solid fa-floppy-disk"></i> Save to Bank
                </button>
            </div>
        </div>
    </div><!-- /grid -->

</div>
</main>
</form>
</div>

<div id="toast-box"></div>

<script>
// ── CLOCK ─────────────────────────────────────────────────
function updateClock() {
    const el = document.getElementById('live-clock');
    if (el) el.textContent = new Date().toLocaleTimeString('en-US', { hour12: false });
}
updateClock(); setInterval(updateClock, 1000);

// ── TOAST ─────────────────────────────────────────────────
function showToast(msg, type = 'info') {
    const icons = { success:'fa-circle-check', info:'fa-circle-info', warning:'fa-triangle-exclamation' };
    const box = document.getElementById('toast-box');
    const t = document.createElement('div');
    t.className = `toast ${type}`;
    t.innerHTML = `<i class="fa-solid ${icons[type]}"></i>${msg}`;
    box.appendChild(t);
    setTimeout(() => { t.style.transition='all .3s'; t.style.opacity='0'; t.style.transform='translateY(8px)'; setTimeout(()=>t.remove(),300); }, 3000);
}

// ── LIVE PREVIEW ──────────────────────────────────────────
function updatePreview() {
    const ta = document.getElementById('question-textarea');
    const el = document.getElementById('preview-q');
    if (ta && el) el.textContent = ta.value.trim() || 'Your question will appear here…';
}
updatePreview();

// ── POINTS SYNC ───────────────────────────────────────────
function syncPoints(v) {
    const pp = document.getElementById('points-preview');
    const ep = document.getElementById('preview-pts');
    if (pp) pp.textContent = v;
    if (ep) ep.textContent = v;
}

// ── AUTO-SAVE STATUS ──────────────────────────────────────
let saveTimer;
document.querySelectorAll('input, textarea, select').forEach(el => {
    el.addEventListener('input', () => {
        const s = document.getElementById('save-status');
        if (s) { s.textContent = 'Saving…'; clearTimeout(saveTimer); saveTimer = setTimeout(() => s.textContent = 'Draft saved', 1200); }
    });
});

// ── QUESTION TYPE ─────────────────────────────────────────
function setQuestionType(type) {
    document.getElementById('hidden_question_type').value = type;
    const map = { MCQ:'active-mcq', 'True/False':'active-tf', Essay:'active-essay' };
    const ids  = { MCQ:'MCQ', 'True/False':'TF', Essay:'Essay' };
    ['MCQ','True/False','Essay'].forEach(t => {
        const btn = document.getElementById('type_btn_' + ids[t]);
        if (btn) btn.className = 'seg-btn' + (t===type ? ' '+map[type] : '');
    });
    document.getElementById('wrapper_MCQ').classList.toggle('hidden',   type!=='MCQ');
    document.getElementById('wrapper_TF').classList.toggle('hidden',    type!=='True/False');
    document.getElementById('wrapper_Essay').classList.toggle('hidden', type!=='Essay');
    const sb = document.getElementById('shuffle_action_btn');
    if (sb) sb.classList.toggle('hidden', type!=='MCQ');
    const i1=document.getElementById('mcq_input_1'), i2=document.getElementById('mcq_input_2');
    if(i1) i1.required=type==='MCQ'; if(i2) i2.required=type==='MCQ';

    const titles={ MCQ:'Answer Options', 'True/False':'True / False Selection', Essay:'Essay Criteria & Rubrics' };
    const subs  ={ MCQ:'Select the radio next to the correct answer', 'True/False':'Mark which statement is correct', Essay:'Define word limits and grading rubrics' };
    const iconStyles={ MCQ:['#ECFDF5','fa-circle-check','#10B981'], 'True/False':['#FEF3C7','fa-circle-half-stroke','#F59E0B'], Essay:['#F5F3FF','fa-pen-nib','#8B5CF6'] };
    document.getElementById('variant_box_title').textContent    = titles[type];
    document.getElementById('variant_box_subtitle').textContent = subs[type];
    const wrap = document.getElementById('ans-icon-wrap');
    if (wrap) { wrap.style.background=iconStyles[type][0]; wrap.innerHTML=`<i class="fa-solid ${iconStyles[type][1]} text-sm" style="color:${iconStyles[type][2]};"></i>`; }

    const typeBadges={ MCQ:['#EEF2FF','#4338CA'], 'True/False':['#ECFDF5','#065F46'], Essay:['#F5F3FF','#6D28D9'] };
    const tb=document.getElementById('preview-type-badge');
    if(tb){ const [bg,c]=typeBadges[type]; tb.style.background=bg; tb.style.color=c; tb.textContent=type; }
}

// ── DIFFICULTY ────────────────────────────────────────────
function setDifficulty(level) {
    document.getElementById('hidden_difficulty').value = level;
    const map = { Easy:'active-easy', Medium:'active-medium', Hard:'active-hard' };
    ['Easy','Medium','Hard'].forEach(l => {
        const btn = document.getElementById('diff_btn_'+l);
        if (btn) btn.className = 'diff-btn' + (l===level ? ' '+map[l] : '');
    });
    const diffBadges={ Easy:['#ECFDF5','#065F46'], Medium:['#FEF3C7','#92400E'], Hard:['#FEF2F2','#991B1B'] };
    const db=document.getElementById('preview-diff-badge');
    if(db){ const [bg,c]=diffBadges[level]; db.style.background=bg; db.style.color=c; db.textContent=level; }
}

// ── MCQ HIGHLIGHT ─────────────────────────────────────────
function highlightCorrectRow() {
    ['a','b','c','d'].forEach((l,i) => {
        const radios = document.querySelectorAll('input[name="correct_option"]');
        const row = document.getElementById('mcq-row-'+l);
        if (row && radios[i]) row.classList.toggle('correct', radios[i].checked);
    });
}

// ── TRUE/FALSE ────────────────────────────────────────────
function markTF(label, value) {
    document.querySelectorAll('.tf-option').forEach(l => l.classList.remove('selected-true','selected-false'));
    label.classList.add(value==='True' ? 'selected-true' : 'selected-false');
    const ti=document.getElementById('tf-true-icon'), fi=document.getElementById('tf-false-icon');
    if(value==='True') {
        if(ti){ ti.style.background='#10B981'; ti.style.color='#fff'; }
        if(fi){ fi.style.background='#E2E8F0'; fi.style.color='#94A3B8'; }
    } else {
        if(fi){ fi.style.background='#EF4444'; fi.style.color='#fff'; }
        if(ti){ ti.style.background='#E2E8F0'; ti.style.color='#94A3B8'; }
    }
}

// ── SETTINGS TABS ─────────────────────────────────────────
function switchSettingTab(key) {
    document.getElementById('hidden_active_setting_tab').value = key;
    ['general','media','cat'].forEach(t => {
        const tab  = document.getElementById('tab_setting_'+t);
        const pane = document.getElementById('content_setting_'+t);
        if(t===key){ tab?.classList.add('stab-active'); pane?.classList.remove('hidden'); }
        else       { tab?.classList.remove('stab-active'); pane?.classList.add('hidden'); }
    });
}

// ── TOGGLE ────────────────────────────────────────────────
function toggleTgl(trackId, hiddenId) {
    const track = document.getElementById(trackId);
    track.classList.toggle('on');
    const isOn = track.classList.contains('on');
    const hidden = document.getElementById(hiddenId);
    if (hidden) hidden.value = isOn ? '1' : '0';
    showToast(isOn ? 'Option enabled' : 'Option disabled', 'info');
}
function toggleDatabaseFlag() {}

// ── SHUFFLE MCQ ───────────────────────────────────────────
function executeManualShuffle() {
    const container = document.getElementById('wrapper_MCQ');
    const rows = Array.from(container.querySelectorAll('.mcq-option-row'));
    const data = rows.map(r => ({ text: r.querySelector('input[type="text"]').value, checked: r.querySelector('input[type="radio"]').checked }));
    for (let i = data.length-1; i > 0; i--) { const j=Math.floor(Math.random()*(i+1)); [data[i],data[j]]=[data[j],data[i]]; }
    rows.forEach((r,i) => { r.querySelector('input[type="text"]').value=data[i].text; r.querySelector('input[type="radio"]').checked=data[i].checked; });
    highlightCorrectRow();
    showToast('Options shuffled!', 'info');
}

// ── IMAGE ─────────────────────────────────────────────────
function triggerImageUpload() { document.getElementById('media_file_input').click(); }
function handleImagePreview(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { document.getElementById('image_preview_el').src=e.target.result; document.getElementById('image_preview_container').classList.remove('hidden'); };
        reader.readAsDataURL(input.files[0]);
        showToast('Image attached', 'success');
    }
}
function removeSelectedImage() { document.getElementById('media_file_input').value=''; document.getElementById('image_preview_container').classList.add('hidden'); showToast('Image removed','warning'); }

// ── CSV ───────────────────────────────────────────────────
function triggerCsvUpload() { document.getElementById('csv_file_input').click(); }
function handleCsvValidation(input) {
    if (input.files && input.files[0]) {
        document.getElementById('csv_badge_name').innerHTML=`<i class="fa-solid fa-file-excel mr-1"></i>${input.files[0].name}`;
        document.getElementById('csv_badge_container').classList.remove('hidden');
        showToast('CSV attached: '+input.files[0].name,'success');
    }
}
function clearCsvAttachment() { document.getElementById('csv_file_input').value=''; document.getElementById('csv_badge_container').classList.add('hidden'); showToast('CSV removed','warning'); }
</script>
</body>
</html>