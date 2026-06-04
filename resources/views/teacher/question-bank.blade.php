<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Question Bank</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        /* Smooth transition for dropdown menus */
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
        .high-contrast-mode td,
        .high-contrast-mode th {
            color: #E5E7EB !important;
            border-color: #374151 !important;
        }
        .high-contrast-mode tr:hover {
            background-color: #1F2937 !important;
        }
        .high-contrast-mode .text-[#0F172A],
        .high-contrast-mode .text-[#1E293B] {
            color: #F9FAFB !important;
        }
        .high-contrast-mode .text-[#64748B] {
            color: #9CA3AF !important;
        }
        .high-contrast-mode .bg-[#FAFCFF],
        .high-contrast-mode .bg-[#F8FAFC] {
            background-color: #030712 !important;
        }
    </style>
    <script>
        // Check storage state instantly before document body builds to minimize white glitching
        if (localStorage.getItem('high-contrast-enabled') === 'true') {
            document.documentElement.classList.add('high-contrast-mode');
        }
    </script>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex selection:bg-blue-500/20">

    <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between flex-shrink-0 z-20">
        <div>
            <a href="{{ route('teacher.dashboard') }}" class="h-20 flex items-center px-6 gap-2.5 hover:opacity-90 transition-opacity">
                <div class="w-9 h-9 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span class="font-bold text-xl text-[#0F172A] tracking-tight">ExamSystem</span>
            </a>

            <nav class="px-4 py-2 space-y-1">
                <a href="{{ route('teacher.dashboard') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-table-columns w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('teacher.question-bank') }}" class="group flex items-center gap-3 px-4 py-3 bg-[#1D4ED8] text-white font-semibold rounded-xl shadow-md shadow-blue-500/10 transition-all">
                    <i class="fa-solid fa-database w-5 text-center text-lg text-white"></i>
                    <span>Question Bank</span>
                </a>
                
                <a href="#" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-desktop w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Monitoring</span>
                </a>

                <a href="{{ route('teacher.grading.show', 1) }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-file-signature w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Grading</span>
                </a>
                
                <a href="{{ route('teacher.analytics') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-chart-line w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Analytics</span>
                </a>

                <a href="{{ route('teacher.settings') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium transition-all rounded-xl">
                    <i class="fa-solid fa-gear w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B] transition-colors"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-[#E2E8F0] flex items-center gap-3 bg-[#F8FAFC] m-4 rounded-xl">
            <div class="w-10 h-10 rounded-full overflow-hidden flex items-center justify-center border border-gray-200 bg-white">
                <img src="{{ Auth::user()->profile_photo_path ?? Auth::user()->avatar_path ?? Auth::user()->profile_image ?? Auth::user()->image ?? Auth::user()->avatar ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=Alex' }}" class="w-full h-full object-cover" alt="Avatar">
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0F172A] leading-tight">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</h4>
                <p class="text-xs text-[#94A3B8] font-medium mt-0.5">Senior Faculty</p>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0">
        <header class="h-20 bg-white border-b border-[#E2E8F0] flex items-center justify-between px-8 z-10 flex-shrink-0">
            <h1 class="text-2xl font-bold text-[#0F172A]">Question Bank</h1>
            
            <div class="flex items-center gap-6">
                <div class="flex items-center gap-3 bg-[#F1F5F9] px-4 py-2 rounded-full border border-[#E2E8F0]">
                    <span class="text-xs font-semibold text-[#475569]">Shuffle questions in exams</span>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" checked class="sr-only peer">
                        <div class="w-11 h-6 bg-[#CBD5E1] rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#1D4ED8]"></div>
                    </label>
                </div>

                <button class="p-2.5 hover:bg-[#F1F5F9] rounded-xl relative border border-[#E2E8F0] bg-white text-[#64748B] transition-colors">
                    <i class="fa-regular fa-bell text-lg"></i>
                    <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-red-500 rounded-full"></span>
                </button>

                <a href="{{ route('questions.create') }}" class="flex items-center gap-2 bg-[#1D4ED8] hover:bg-blue-800 text-white font-semibold px-4 py-2.5 rounded-xl shadow-sm transition-all text-sm text-center">
                    <i class="fa-solid fa-plus font-bold"></i>
                    Add Question
                </a>
            </div>
        </header>

        <div class="p-8 flex-1 space-y-6 overflow-y-auto">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="relative flex-1 max-w-xl">
                    <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-[#94A3B8]"></i>
                    <input id="search-input" type="text" placeholder="Search by question text or keywords..." class="w-full bg-white border border-[#E2E8F0] rounded-xl pl-11 pr-4 py-2.5 text-sm placeholder-[#94A3B8] focus:outline-none focus:border-[#1D4ED8] transition-all shadow-sm focus:ring-2 focus:ring-blue-500/10">
                </div>

                <div class="flex items-center gap-2.5 relative">
                    <button onclick="toggleFilters()" class="flex items-center gap-2 bg-white border border-[#E2E8F0] px-4 py-2.5 rounded-xl text-sm font-medium text-[#475569] hover:bg-gray-50 shadow-sm transition-colors">
                        <i class="fa-solid fa-sliders text-[#64748B]"></i> <span id="filter-btn-text">Filter</span>
                    </button>

                    <div class="relative inline-block text-left">
                        <button id="type-dropdown-btn" onclick="toggleDropdown('type-dropdown')" class="flex items-center gap-2 bg-white border border-[#E2E8F0] px-4 py-2.5 rounded-xl text-sm font-medium text-[#475569] hover:bg-gray-50 shadow-sm transition-colors">
                            <span>Type: All</span> <i class="fa-solid fa-chevron-down text-xs text-[#94A3B8]"></i>
                        </button>
                        <div id="type-dropdown" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-[#E2E8F0] rounded-xl shadow-lg z-30 py-1">
                            <button onclick="selectType('All')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#1E293B] font-medium transition-colors">Type: All</button>
                            <button onclick="selectType('MCQ')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#1E293B] font-medium transition-colors">MCQ</button>
                            <button onclick="selectType('Essay')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#1E293B] font-medium transition-colors">Essay</button>
                        </div>
                    </div>

                    <div class="relative inline-block text-left">
                        <button id="difficulty-dropdown-btn" onclick="toggleDropdown('difficulty-dropdown')" class="flex items-center gap-2 bg-white border border-[#E2E8F0] px-4 py-2.5 rounded-xl text-sm font-medium text-[#475569] hover:bg-gray-50 shadow-sm transition-colors">
                            <span>Difficulty: All</span> <i class="fa-solid fa-chevron-down text-xs text-[#94A3B8]"></i>
                        </button>
                        <div id="difficulty-dropdown" class="dropdown-menu absolute right-0 mt-2 w-48 bg-white border border-[#E2E8F0] rounded-xl shadow-lg z-30 py-1">
                            <button onclick="selectDifficulty('All')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#1E293B] font-medium transition-colors">Difficulty: All</button>
                            <button onclick="selectDifficulty('Easy')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#10B981] font-semibold transition-colors">Easy</button>
                            <button onclick="selectDifficulty('Medium')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#F59E0B] font-semibold transition-colors">Medium</button>
                            <button onclick="selectDifficulty('Hard')" class="w-full text-left px-4 py-2.5 text-sm hover:bg-slate-50 text-[#EF4444] font-semibold transition-colors">Hard</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white border border-[#E2E8F0] rounded-2xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-[#E2E8F0] bg-[#FAFCFF]">
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#94A3B8] w-[5%] text-center">#</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#94A3B8] w-[50%]">Question Text</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#94A3B8]">Type</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#94A3B8]">Difficulty</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#94A3B8]">Last Used</th>
                                <th class="px-6 py-4 text-xs font-bold uppercase tracking-wider text-[#94A3B8] text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="question-table-body" class="divide-y divide-[#F1F5F9]">
                            @forelse($questions as $question)
                                <tr class="hover:bg-[#F8FAFC] transition-colors row-item" data-type="{{ $question->type }}" data-difficulty="{{ $question->difficulty ?? 'Medium' }}">
                                    <td class="px-6 py-4.5 text-center font-semibold text-sm text-[#64748B]">
                                        {{ ($questions->currentPage() - 1) * $questions->perPage() + $loop->iteration }}
                                    </td>

                                    <td class="px-6 py-4.5">
                                        <div class="font-bold text-[#1E293B] mb-1 text-[15px] search-target">{!! $question->content !!}</div>
                                        <div class="flex items-center gap-1.5 text-xs font-medium text-[#94A3B8]">
                                            <span>Exam ID: {{ $question->exam_id ?? 'Unassigned' }}</span>
                                            @if($question->explanation)
                                                <span>•</span>
                                                <span>{{ $question->explanation }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <span class="inline-block px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide {{ strtolower($question->type) === 'mcq' ? 'bg-[#EFF6FF] text-[#1D4ED8]' : 'bg-[#FDF2F8] text-[#DB2777]' }}">
                                            {{ strtoupper($question->type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4.5">
                                        <div class="flex items-center gap-1.5">
                                            @if(strtolower($question->difficulty ?? 'medium') === 'easy')
                                                <span class="w-2 h-2 rounded-full bg-[#10B981]"></span>
                                                <span class="text-sm font-semibold text-[#10B981]">Easy</span>
                                            @elseif(strtolower($question->difficulty ?? 'medium') === 'hard')
                                                <span class="w-2 h-2 rounded-full bg-[#EF4444]"></span>
                                                <span class="text-sm font-semibold text-[#EF4444]">Hard</span>
                                            @else
                                                <span class="w-2 h-2 rounded-full bg-[#F59E0B]"></span>
                                                <span class="text-sm font-semibold text-[#F59E0B]">Medium</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4.5 text-sm font-medium text-[#64748B]">
                                        {{ $question->updated_at ? $question->updated_at->format('M d, Y') : 'Never' }}
                                    </td>
                                    <td class="px-6 py-4.5 text-center">
                                        <div class="flex items-center justify-center gap-3 text-[#94A3B8]">
                                            <a href="{{ route('questions.edit', $question->id) }}" class="hover:text-[#1D4ED8] transition-colors" title="Edit Question">
                                                <i class="fa-regular fa-pen-to-square text-base"></i>
                                            </a>
                                            
                                            <form action="{{ route('questions.destroy', $question->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to remove this question record from storage bank?');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="hover:text-red-500 transition-colors" title="Delete Question">
                                                    <i class="fa-regular fa-trash-can text-base"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-sm font-medium text-[#94A3B8]">
                                        No questions managed inside the bank collection layer yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-6 py-4 border-t border-[#E2E8F0] flex items-center justify-between bg-white text-sm">
                    <span class="font-semibold text-[#64748B]">
                        Showing <span class="text-[#1E293B]">{{ $questions->firstItem() ?? 0 }} to {{ $questions->lastItem() ?? 0 }}</span> of <span class="text-[#1E293B]">{{ $questions->total() }}</span> questions
                    </span>
                    
                    @if ($questions->hasPages())
                        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
                            @if ($questions->onFirstPage())
                                <span class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-100 text-[#CBD5E1] cursor-not-allowed bg-gray-50/50">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </span>
                            @else
                                <a href="{{ $questions->previousPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full border border-[#E2E8F0] text-[#64748B] hover:bg-slate-50 transition-colors">
                                    <i class="fa-solid fa-chevron-left text-xs"></i>
                                </a>
                            @endif

                            @foreach ($questions->renderableUrlElements() as $element)
                                @if (is_string($element))
                                    <span class="w-9 h-9 flex items-center justify-center text-[#94A3B8] font-medium">...</span>
                                @endif

                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $questions->currentPage())
                                            <span class="w-9 h-9 flex items-center justify-center rounded-full bg-[#1D4ED8] text-white font-bold text-sm shadow-md shadow-blue-500/10">
                                                {{ $page }}
                                            </span>
                                        @else
                                            <a href="{{ $url }}" class="w-9 h-9 flex items-center justify-center rounded-full text-[#475569] hover:bg-slate-100 font-semibold text-sm transition-colors">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                            @endforeach

                            @if ($questions->hasMorePages())
                                <a href="{{ $questions->nextPageUrl() }}" class="w-9 h-9 flex items-center justify-center rounded-full border border-[#E2E8F0] text-[#64748B] hover:bg-slate-50 transition-colors"><i class="fa-solid fa-chevron-right text-xs"></i></a>
                            @endif
                        </nav>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider mb-2">Total Questions</p>
                    <h3 class="text-3xl font-extrabold text-[#0F172A]">{{ $questions->total() }}</h3>
                </div>
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider mb-2">MCQ Count</p>
                    <h3 class="text-3xl font-extrabold text-[#1D4ED8]">{{ $mcqCount }}</h3>
                </div>
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider mb-2">Essay Count</p>
                    <h3 class="text-3xl font-extrabold text-[#DB2777]">{{ $essayCount }}</h3>
                </div>
                <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm">
                    <p class="text-xs font-bold text-[#94A3B8] uppercase tracking-wider mb-2">Unused Bank</p>
                    <h3 class="text-3xl font-extrabold text-[#10B981]">{{ $unusedPercentage }}%</h3>
                </div>
            </div>
        </div>
    </main>

    <script>
        let selectedType = 'All';
        let selectedDifficulty = 'All';

        function toggleDropdown(id) {
            const dropdown = document.getElementById(id);
            const isShown = dropdown.classList.contains('show');
            document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
            if (!isShown) {
                dropdown.classList.add('show');
            }
        }

        window.onclick = function(event) {
            if (!event.target.closest('.relative')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
            }
        }

        function selectType(type) {
            selectedType = type;
            document.getElementById('type-dropdown-btn').querySelector('span').innerText = type === 'All' ? 'Type: All' : 'Type: ' + type;
            document.getElementById('type-dropdown').classList.remove('show');
            applyFilters();
        }

        function selectDifficulty(diff) {
            selectedDifficulty = diff;
            document.getElementById('difficulty-dropdown-btn').querySelector('span').innerText = diff === 'All' ? 'Difficulty: All' : 'Difficulty: ' + diff;
            document.getElementById('difficulty-dropdown').classList.remove('show');
            applyFilters();
        }

        function toggleFilters() {
            const filterBtnText = document.getElementById('filter-btn-text');
            if(filterBtnText.innerText === "Filter") {
                filterBtnText.innerText = "Clear Filters";
                selectType('All');
                selectDifficulty('All');
                document.getElementById('search-input').value = '';
            } else {
                filterBtnText.innerText = "Filter";
            }
            applyFilters();
        }

        function applyFilters() {
            const searchQuery = document.getElementById('search-input').value.toLowerCase();
            const rows = document.querySelectorAll('.row-item');

            rows.forEach(row => {
                const rowType = row.getAttribute('data-type');
                const rowDiff = row.getAttribute('data-difficulty');
                const targetEl = row.querySelector('.search-target');
                const questionText = targetEl ? targetEl.innerText.toLowerCase() : '';

                const matchesType = (selectedType === 'All' || rowType.toLowerCase() === selectedType.toLowerCase());
                const matchesDiff = (selectedDifficulty === 'All' || rowDiff.toLowerCase() === selectedDifficulty.toLowerCase());
                const matchesSearch = questionText.includes(searchQuery);

                if (matchesType && matchesDiff && matchesSearch) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.getElementById('search-input').addEventListener('input', applyFilters);
    </script>
</body>
</html>