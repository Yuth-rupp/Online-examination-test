<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Create New Question</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .dropdown-menu {
            opacity: 0;
            transform: scale(0.95);
            pointer-events: none;
            transition: all 0.15s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .dropdown-menu.show {
            opacity: 1;
            transform: scale(1);
            pointer-events: auto;
        }
        [contenteditable_placeholder]:empty:before {
            content: attr(contenteditable_placeholder);
            color: #94A3B8;
            font-style: normal;
        }
        #question-editor ul {
            list-style-type: disc;
            padding-left: 24px;
            margin-top: 4px;
            margin-bottom: 4px;
        }
    </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen selection:bg-blue-500/20">

    <form action="{{ route('questions.store') }}" method="POST" id="question-creation-form" enctype="multipart/form-data" onsubmit="syncEditorContent()">
        @csrf
        <input type="hidden" name="question_text" id="hidden-question-text">

        <header class="h-20 bg-white border-b border-[#E2E8F0] flex items-center justify-between px-12 sticky top-0 z-50 shadow-sm">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span class="font-bold text-xl text-[#0F172A] tracking-tight">Exam System</span>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="submit" class="bg-[#1D4ED8] hover:bg-blue-800 text-white font-semibold px-5 py-2.5 rounded-xl shadow-md shadow-blue-500/10 transition-all text-sm">
                    Save to Bank
                </button>
                <a href="{{ route('teacher.question-bank') }}" class="bg-[#F1F5F9] hover:bg-slate-200 text-[#475569] font-semibold px-5 py-2.5 rounded-xl border border-[#E2E8F0] transition-all text-sm text-center">
                    Cancel
                </a>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-8 py-10 space-y-8">
            
            <div class="space-y-1">
                <div class="flex items-center gap-2 text-xs font-semibold text-[#94A3B8] tracking-wide uppercase">
                    <a href="{{ route('teacher.question-bank') }}" class="hover:text-[#1D4ED8] transition-colors">Question Bank</a>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                    <span class="text-[#64748B]">Add Question</span>
                </div>
                <h2 class="text-3xl font-extrabold text-[#0F172A] tracking-tight">Create New Question</h2>
            </div>

            <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                <div class="lg:col-span-4 space-y-2">
                    <label class="block text-[11px] font-extrabold text-[#94A3B8] uppercase tracking-wider">Assign to Exam</label>
                    <div class="relative">
                        <select name="parent_exam_id" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 text-sm font-medium appearance-none focus:outline-none focus:border-[#1D4ED8] transition-all cursor-pointer shadow-inner">
                            <option value="">Leave Unassigned (Question Bank Only)</option>
                            <option value="EXAM-001">Midterm Mathematics Exam</option>
                            <option value="EXAM-002">Database Systems Quiz</option>
                            <option value="EXAM-003">Introduction to Quantum Physics</option>
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-xs text-[#64748B] pointer-events-none"></i>
                    </div>
                </div>

                <div class="lg:col-span-3 space-y-2">
                    <label class="block text-[11px] font-extrabold text-[#94A3B8] uppercase tracking-wider">Question Type</label>
                    <div class="bg-[#E2E8F0]/60 p-1 rounded-xl flex items-center gap-1">
                        <input type="hidden" name="question_type" id="question_type_input" value="MCQ">
                        <button type="button" onclick="setQuestionType('MCQ')" id="type-btn-MCQ" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center bg-[#1D4ED8] text-white shadow-sm">MCQ</button>
                        <button type="button" onclick="setQuestionType('TF')" id="type-btn-TF" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center text-[#64748B] hover:text-[#1E293B]">True/False</button>
                        <button type="button" onclick="setQuestionType('Essay')" id="type-btn-Essay" class="flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center text-[#64748B] hover:text-[#1E293B]">Essay</button>
                    </div>
                </div>

                <div class="lg:col-span-3 space-y-2">
                    <label class="block text-[11px] font-extrabold text-[#94A3B8] uppercase tracking-wider">Difficulty</label>
                    <div class="flex items-center gap-1.5">
                        <input type="hidden" name="difficulty" id="difficulty_input" value="Medium">
                        <button type="button" onclick="setDifficulty('Easy')" id="diff-btn-Easy" class="flex-1 py-2 text-xs font-bold border border-[#E2E8F0] rounded-xl transition-all text-center bg-white text-[#64748B] hover:bg-slate-50">Easy</button>
                        <button type="button" onclick="setDifficulty('Medium')" id="diff-btn-Medium" class="flex-1 py-2 text-xs font-bold border border-transparent rounded-xl transition-all text-center bg-[#1D4ED8] text-white shadow-sm">Medium</button>
                        <button type="button" onclick="setDifficulty('Hard')" id="diff-btn-Hard" class="flex-1 py-2 text-xs font-bold border border-[#E2E8F0] rounded-xl transition-all text-center bg-white text-[#64748B] hover:bg-slate-50">Hard</button>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-2">
                    <label class="block text-[11px] font-extrabold text-[#94A3B8] uppercase tracking-wider">Points</label>
                    <input type="number" name="points" value="5" min="1" class="w-full bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-2.5 text-sm font-bold text-center focus:outline-none focus:border-[#1D4ED8] transition-all shadow-inner">
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                
                <div class="lg:col-span-8 bg-white border border-[#E2E8F0] rounded-2xl p-8 shadow-sm space-y-6">
                    <h3 class="text-lg font-bold text-[#0F172A] tracking-tight">Question Content</h3>
                    
                    <div class="space-y-3">
                        <div class="bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl flex items-center gap-1 p-1.5 text-[#64748B]">
                            <button type="button" onclick="formatText('bold')" id="editor-btn-bold" class="w-9 h-9 text-sm font-bold flex items-center justify-center rounded-lg border border-transparent hover:bg-white hover:border-slate-200 hover:text-[#1D4ED8] transition-all shadow-sm" title="Bold Selected Text">
                                <i class="fa-solid fa-bold"></i>
                            </button>
                            <button type="button" onclick="formatText('italic')" id="editor-btn-italic" class="w-9 h-9 text-sm italic flex items-center justify-center rounded-lg border border-transparent hover:bg-white hover:border-slate-200 hover:text-[#1D4ED8] transition-all shadow-sm" title="Italicize Selected Text">
                                <i class="fa-solid fa-italic"></i>
                            </button>
                            <button type="button" onclick="formatText('insertUnorderedList')" id="editor-btn-list" class="w-9 h-9 text-sm flex items-center justify-center rounded-lg border border-transparent hover:bg-white hover:border-slate-200 hover:text-[#1D4ED8] transition-all shadow-sm" title="Add Unordered List">
                                <i class="fa-solid fa-list-ul"></i>
                            </button>
                            <div class="h-4 w-px bg-[#E2E8F0] mx-1"></div>
                            <button type="button" onclick="switchMetadataTab('media')" class="p-2 hover:bg-white hover:text-[#1D4ED8] rounded-lg transition-all text-sm flex items-center gap-1.5 font-medium" title="Attach Files">
                                <i class="fa-regular fa-image"></i>
                            </button>
                            <button type="button" class="p-2 hover:bg-white hover:text-[#1E293B] rounded-lg transition-all text-xs font-bold tracking-tight" title="LaTeX Equation Engine">&#8721; LaTeX</button>
                        </div>
                        
                        <div id="question-editor" contenteditable="true" contenteditable_placeholder="Enter your question here..." class="w-full min-h-[160px] bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-5 text-sm focus:outline-none focus:border-[#1D4ED8] focus:ring-4 focus:ring-blue-500/5 transition-all shadow-inner overflow-y-auto leading-relaxed"></div>
                    </div>

                    <div id="dynamic-responses-root" class="space-y-4 pt-4 border-t border-[#F1F5F9]"></div>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <div class="bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-sm space-y-6">
                        <div class="space-y-1">
                            <h4 class="text-base font-bold text-[#0F172A]">Question Settings</h4>
                            <p class="text-xs text-[#94A3B8] font-medium">Configure behavior and metadata</p>
                        </div>

                        <div class="border border-[#E2E8F0] rounded-xl overflow-hidden divide-y divide-[#E2E8F0]">
                            <button type="button" onclick="switchMetadataTab('general')" id="tab-trigger-general" class="w-full flex items-center gap-3 px-4 py-3.5 text-sm font-semibold transition-all bg-slate-50 text-[#1D4ED8]">
                                <i class="fa-solid fa-gear text-base w-5 text-center"></i>
                                <span>General Settings</span>
                            </button>
                            <button type="button" onclick="switchMetadataTab('media')" id="tab-trigger-media" class="w-full flex items-center gap-3 px-4 py-3.5 text-sm font-semibold transition-all text-[#64748B] hover:bg-slate-50 hover:text-[#1E293B]">
                                <i class="fa-regular fa-images text-base w-5 text-center"></i>
                                <span>Media Library / CSV</span>
                            </button>
                            <button type="button" onclick="switchMetadataTab('categorization')" id="tab-trigger-categorization" class="w-full flex items-center gap-3 px-4 py-3.5 text-sm font-semibold transition-all text-[#64748B] hover:bg-slate-50 hover:text-[#1E293B]">
                                <i class="fa-solid fa-tags text-base w-5 text-center"></i>
                                <span>Categorization</span>
                            </button>
                        </div>

                        <div id="metadata-pane-general" class="metadata-pane space-y-5">
                            <div class="flex items-center justify-between p-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl">
                                <div class="space-y-0.5">
                                    <span class="block text-xs font-bold text-[#1E293B]">Enable Question Shuffling</span>
                                    <span class="block text-[11px] text-[#94A3B8] font-medium">Randomize answer order for students</span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_shuffled" checked class="sr-only peer">
                                    <div class="w-10 h-5 bg-[#CBD5E1] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#1D4ED8]"></div>
                                </label>
                            </div>

                            <label class="flex items-start gap-3 p-3 border border-[#E2E8F0] rounded-xl cursor-pointer hover:bg-[#F8FAFC] transition-colors">
                                <input type="checkbox" name="flag_review" class="mt-0.5 rounded border-gray-300 text-[#1D4ED8] focus:ring-blue-500/20 h-4 w-4">
                                <div class="space-y-0.5">
                                    <span class="block text-xs font-bold text-[#1E293B]">Flag for Final Review</span>
                                    <span class="block text-[11px] text-[#94A3B8] font-medium leading-relaxed">Require review by a senior faculty member.</span>
                                </div>
                            </label>
                        </div>

                        <div id="metadata-pane-media" class="metadata-pane hidden space-y-4">
                            <div id="dropzone-container" onclick="triggerFileInput()" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)" ondrop="handleDrop(event)" class="border-2 border-dashed border-[#CBD5E1] hover:border-[#1D4ED8] rounded-xl p-5 text-center cursor-pointer transition-all bg-[#F8FAFC] relative group">
                                <input type="file" id="media-file-input" name="attachment_media" accept="image/*,.csv" class="hidden" onchange="handleFileSelect(event)">
                                
                                <div id="dropzone-default-ui" class="space-y-2">
                                    <i class="fa-regular fa-cloud-arrow-up text-2xl text-[#94A3B8] group-hover:text-[#1D4ED8] transition-colors"></i>
                                    <span class="block text-xs font-bold text-[#475569]">Upload image or CSV data reference</span>
                                    <span class="block text-[10px] text-[#94A3B8] font-semibold">PNG, JPG, SVG or CSV up to 5MB</span>
                                </div>

                                <div id="dropzone-preview-ui" class="hidden flex flex-col items-center justify-center p-2">
                                    <div id="file-icon-preview" class="mb-2"></div>
                                    <span id="file-name-string" class="block text-xs font-bold text-[#1E293B] truncate max-w-full px-4"></span>
                                    <button type="button" onclick="clearSelectedFile(event)" class="mt-2 text-[11px] font-bold text-red-500 hover:text-red-700 transition-colors bg-red-50 px-2.5 py-1 rounded-lg border border-red-200">Remove File</button>
                                </div>
                            </div>
                        </div>

                        <div id="metadata-pane-categorization" class="metadata-pane hidden space-y-3">
                            <label class="block text-[11px] font-extrabold text-[#94A3B8] uppercase tracking-wider">Topic / Chapter Tags</label>
                            <div id="tags-pool-container" class="flex flex-wrap gap-1.5 p-2.5 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl min-h-[44px]">
                                <span class="inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#1D4ED8] border border-blue-100 tag-token">
                                    Big-O Notation <button type="button" onclick="removeTagToken(this)" class="hover:text-blue-900 transition-colors text-[10px]"><i class="fa-solid fa-xmark"></i></button>
                                </span>
                                <span class="inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#1D4ED8] border border-blue-100 tag-token">
                                    Binary Search <button type="button" onclick="removeTagToken(this)" class="hover:text-blue-900 transition-colors text-[10px]"><i class="fa-solid fa-xmark"></i></button>
                                </span>
                            </div>
                            <div class="flex gap-2">
                                <input id="tag-input-node" type="text" placeholder="Add topic tag..." class="flex-1 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-3 py-2 text-xs focus:outline-none focus:border-[#1D4ED8] font-medium shadow-inner">
                                <button type="button" onclick="appendTagToken()" class="bg-slate-800 hover:bg-slate-900 text-white font-semibold text-xs px-3.5 py-2 rounded-xl transition-all">Add</button>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white border border-[#E2E8F0] px-5 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <div class="space-y-0.5">
                            <span class="block text-xs font-bold text-[#1E293B]">Auto-Save Active</span>
                            <span class="block text-[11px] text-[#94A3B8] font-medium">Draft saved 2 mins ago</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </form>

    <script>
        let activeQuestionType = 'MCQ';
        let activeDifficulty = 'Medium';

        document.addEventListener('DOMContentLoaded', () => {
            renderDynamicOptionsLayout();
            
            document.getElementById('question-editor').addEventListener('keyup', refreshToolbarButtonStates);
            document.getElementById('question-editor').addEventListener('mouseup', refreshToolbarButtonStates);
        });

        function formatText(command) {
            document.execCommand(command, false, null);
            document.getElementById('question-editor').focus();
            refreshToolbarButtonStates();
        }

        function refreshToolbarButtonStates() {
            const boldActive = document.queryCommandState('bold');
            const italicActive = document.queryCommandState('italic');
            const listActive = document.queryCommandState('insertUnorderedList');

            toggleButtonActiveColor('editor-btn-bold', boldActive);
            toggleButtonActiveColor('editor-btn-italic', italicActive);
            toggleButtonActiveColor('editor-btn-list', listActive);
        }

        function toggleButtonActiveColor(id, isActive) {
            const btn = document.getElementById(id);
            if (isActive) {
                btn.classList.add('bg-blue-50', 'border-blue-200', 'text-[#1D4ED8]');
                btn.classList.remove('border-transparent', 'text-[#64748B]');
            } else {
                btn.classList.remove('bg-blue-50', 'border-blue-200', 'text-[#1D4ED8]');
                btn.classList.add('border-transparent', 'text-[#64748B]');
            }
        }

        function syncEditorContent() {
            const htmlContent = document.getElementById('question-editor').innerHTML;
            document.getElementById('hidden-question-text').value = htmlContent;
        }

        function triggerFileInput() {
            document.getElementById('media-file-input').click();
        }

        function handleDragOver(e) {
            e.preventDefault();
            document.getElementById('dropzone-container').classList.add('border-[#1D4ED8]', 'bg-blue-50/20');
        }

        document.getElementById('dropzone-container').addEventListener('click', function(e) {
            if (e.target.closest('button')) {
                e.stopPropagation();
            }
        });

        function handleDragLeave(e) {
            e.preventDefault();
            document.getElementById('dropzone-container').classList.remove('border-[#1D4ED8]', 'bg-blue-50/20');
        }

        function handleDrop(e) {
            e.preventDefault();
            const container = document.getElementById('dropzone-container');
            container.classList.remove('border-[#1D4ED8]', 'bg-blue-50/20');
            
            if (e.dataTransfer.files.length) {
                document.getElementById('media-file-input').files = e.dataTransfer.files;
                processFilePreview(e.dataTransfer.files[0]);
            }
        }

        function handleFileSelect(e) {
            if (e.target.files.length) {
                processFilePreview(e.target.files[0]);
            }
        }

        function processFilePreview(file) {
            const defaultUI = document.getElementById('dropzone-default-ui');
            const previewUI = document.getElementById('dropzone-preview-ui');
            const nameString = document.getElementById('file-name-string');
            const iconPreview = document.getElementById('file-icon-preview');

            defaultUI.classList.add('hidden');
            previewUI.classList.remove('hidden');
            nameString.innerText = file.name;

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    iconPreview.innerHTML = `<img src="${e.target.result}" class="max-h-24 mx-auto rounded-lg object-contain shadow-sm border border-slate-200">`;
                }
                reader.readAsDataURL(file);
            } else if (file.name.endsWith('.csv')) {
                iconPreview.innerHTML = `
                    <div class="w-16 h-16 bg-emerald-50 rounded-xl border border-emerald-200 flex items-center justify-center mx-auto text-emerald-600">
                        <i class="fa-solid fa-file-csv text-3xl"></i>
                    </div>`;
            } else {
                iconPreview.innerHTML = `
                    <div class="w-16 h-16 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-center mx-auto text-slate-500">
                        <i class="fa-regular fa-file text-3xl"></i>
                    </div>`;
            }
        }

        function clearSelectedFile(e) {
            e.preventDefault();
            e.stopPropagation();
            document.getElementById('media-file-input').value = '';
            document.getElementById('dropzone-default-ui').classList.remove('hidden');
            document.getElementById('dropzone-preview-ui').classList.add('hidden');
        }

        function setQuestionType(targetType) {
            activeQuestionType = targetType;
            document.getElementById('question_type_input').value = targetType;

            ['MCQ', 'TF', 'Essay'].forEach(type => {
                const button = document.getElementById(`type-btn-${type}`);
                if(type === targetType) {
                    button.className = "flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center bg-[#1D4ED8] text-white shadow-sm";
                } else {
                    button.className = "flex-1 py-2 text-xs font-bold rounded-lg transition-all text-center text-[#64748B] hover:text-[#1E293B]";
                }
            });

            renderDynamicOptionsLayout();
        }

        function setDifficulty(level) {
            activeDifficulty = level;
            document.getElementById('difficulty_input').value = level;

            ['Easy', 'Medium', 'Hard'].forEach(lvl => {
                const button = document.getElementById(`diff-btn-${lvl}`);
                if(lvl === level) {
                    button.className = "flex-1 py-2 text-xs font-bold border border-transparent rounded-xl transition-all text-center bg-[#1D4ED8] text-white shadow-sm";
                } else {
                    button.className = "flex-1 py-2 text-xs font-bold border border-[#E2E8F0] rounded-xl transition-all text-center bg-white text-[#64748B] hover:bg-slate-50";
                }
            });
        }

        function renderDynamicOptionsLayout() {
            const rootNode = document.getElementById('dynamic-responses-root');
            rootNode.innerHTML = '';

            if (activeQuestionType === 'MCQ') {
                rootNode.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-[#475569] uppercase tracking-wider">Answer Options</h4>
                        <span class="text-xs text-[#94A3B8] font-medium">Mark the correct answer using the radio button</span>
                    </div>
                    <div id="mcq-rows-stack" class="space-y-3">
                        ${createMCQRowHTML(0, "An efficient search algorithm for sorted lists.", true)}
                        ${createMCQRowHTML(1, "A way to measure algorithm complexity.", false)}
                        ${createMCQRowHTML(2, "A sorting technique using pivot elements.", false)}
                        ${createMCQRowHTML(3, "", false, "Enter option text...")}
                    </div>
                    <button type="button" onclick="addNewMcqOptionRow()" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#1D4ED8] hover:text-blue-800 transition-colors">
                        <i class="fa-solid fa-plus"></i> Add Option
                    </button>
                `;
            } else if (activeQuestionType === 'TF') {
                rootNode.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-[#475569] uppercase tracking-wider">True / False Configuration</h4>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center justify-between p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl cursor-pointer hover:bg-slate-50 transition-all select-none">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="tf_correct_index" value="true" checked class="text-[#1D4ED8] focus:ring-blue-500/10 w-4 h-4">
                                <span class="text-sm font-bold text-[#1E293B]">True Statement</span>
                            </div>
                        </label>
                        <label class="flex items-center justify-between p-4 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl cursor-pointer hover:bg-slate-50 transition-all select-none">
                            <div class="flex items-center gap-3">
                                <input type="radio" name="tf_correct_index" value="false" class="text-[#1D4ED8] focus:ring-blue-500/10 w-4 h-4">
                                <span class="text-sm font-bold text-[#1E293B]">False Statement</span>
                            </div>
                        </label>
                    </div>
                `;
            } else if (activeQuestionType === 'Essay') {
                rootNode.innerHTML = `
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-sm font-bold text-[#475569] uppercase tracking-wider">Essay Grading Guidelines</h4>
                    </div>
                    <textarea name="essay_rubric_guidelines" placeholder="Enter ideal answer markers or rubrics to help assist grading..." class="w-full min-h-[120px] bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl p-4 text-sm focus:outline-none focus:border-[#1D4ED8] transition-all shadow-inner resize-none leading-relaxed"></textarea>
                `;
            }
        }

        function createMCQRowHTML(index, textValue = '', isChecked = false, placeholderText = "Enter choice option text...") {
            return `
                <div class="flex items-center gap-3 bg-[#F8FAFC] border border-[#E2E8F0] rounded-xl px-4 py-3 group hover:border-slate-300 transition-colors">
                    <input type="radio" name="mcq_correct_option" value="${index}" ${isChecked ? 'checked' : ''} class="text-[#1D4ED8] w-4 h-4 cursor-pointer">
                    <input type="text" name="mcq_options[]" value="${textValue}" placeholder="${placeholderText}" class="flex-1 bg-transparent border-none text-sm text-[#1E293B] placeholder-[#94A3B8] focus:outline-none font-medium">
                    <button type="button" onclick="this.closest('.flex').remove()" class="opacity-0 group-hover:opacity-100 text-[#94A3B8] hover:text-red-500 transition-all text-xs px-1"><i class="fa-regular fa-trash-can"></i></button>
                </div>
            `;
        }

        function addNewMcqOptionRow() {
            const containerNode = document.getElementById('mcq-rows-stack');
            const calculatedIndex = containerNode.children.length;
            const temporaryRowWrapper = document.createElement('div');
            temporaryRowWrapper.innerHTML = createMCQRowHTML(calculatedIndex, '', false, "Enter option text...");
            containerNode.appendChild(temporaryRowWrapper.firstElementChild);
        }

        function switchMetadataTab(targetPane) {
            document.querySelectorAll('.metadata-pane').forEach(el => el.classList.add('hidden'));
            document.getElementById(`metadata-pane-${targetPane}`).classList.remove('hidden');

            ['general', 'media', 'categorization'].forEach(tab => {
                const triggerBtn = document.getElementById(`tab-trigger-${tab}`);
                if(tab === targetPane) {
                    triggerBtn.className = "w-full flex items-center gap-3 px-4 py-3.5 text-sm font-semibold transition-all bg-slate-50 text-[#1D4ED8]";
                } else {
                    triggerBtn.className = "w-full flex items-center gap-3 px-4 py-3.5 text-sm font-semibold transition-all text-[#64748B] hover:bg-slate-50 hover:text-[#1E293B]";
                }
            });
        }

        function appendTagToken() {
            const inputField = document.getElementById('tag-input-node');
            const cleanTokenValue = inputField.value.trim();

            if(cleanTokenValue) {
                const targetPool = document.getElementById('tags-pool-container');
                const tagElementWrapper = document.createElement('span');
                tagElementWrapper.className = "inline-flex items-center gap-1.5 pl-2.5 pr-1.5 py-1 rounded-lg text-xs font-semibold bg-blue-50 text-[#1D4ED8] border border-blue-100 tag-token";
                tagElementWrapper.innerHTML = `
                    ${cleanTokenValue}
                    <button type="button" onclick="removeTagToken(this)" class="hover:text-blue-900 transition-colors text-[10px]"><i class="fa-solid fa-xmark"></i></button>
                    <input type="hidden" name="tags[]" value="${cleanTokenValue}">
                `;
                targetPool.appendChild(tagElementWrapper);
                inputField.value = '';
            }
        }

        function removeTagToken(buttonNode) {
            buttonNode.closest('.tag-token').remove();
        }
    </script>
</body>
</html>