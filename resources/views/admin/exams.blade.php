<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exams | ExamSystem Admin</title>
    <meta name="description" content="Create, schedule, and manage exams for your department in ExamSystem.">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-flash dark mode (matches the dashboard) -->
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">

    <style>
        * { font-family: 'Inter', sans-serif; }
        .font-mono { font-family: 'JetBrains Mono', monospace; }
        body { background: #f8fafc; }
        [x-cloak] { display: none !important; }

        /* ── Shared admin brand + nav (matches Dashboard/User Management) ── */
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-nav-active { background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
        .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }

        /* Dark mode surface overrides — driven by Alpine's `darkMode` */
        .dark-surface { background:#0f172a; }
        .dark-card { --card-bg:#1e293b; --card-br:#334155; --row-hover:#1e293b; }

        /* ── Cards ── */
        .stat-card { background:#fff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(0,0,0,0.04),0 4px 16px rgba(0,0,0,0.03); transition:all 0.2s ease; }
        .stat-card:hover { box-shadow:0 4px 20px rgba(37,99,235,0.09); border-color:#bfdbfe; transform:translateY(-1px); }
        .exam-card { background:#fff; border:1px solid #e8edf5; box-shadow:0 1px 4px rgba(0,0,0,0.03); transition:all 0.22s ease; }
        .exam-card:hover { box-shadow:0 6px 24px rgba(0,0,0,0.08); border-color:#bfdbfe; transform:translateY(-2px); }

        /* ── Progress bar ── */
        .progress-bar { height:5px; border-radius:999px; background:#f1f5f9; overflow:hidden; }
        .progress-fill { height:100%; border-radius:999px; transition:width 1s ease; }

        /* ── Status badges ── */
        .badge-active  { background:#f0fdf4; color:#15803d; border:1px solid #bbf7d0; }
        .badge-draft   { background:#fffbeb; color:#92400e; border:1px solid #fde68a; }
        .badge-closed  { background:#fff1f2; color:#be123c; border:1px solid #fecdd3; }
        .badge-upcoming { background:#eff6ff; color:#1d4ed8; border:1px solid #bfdbfe; }

        /* ── Filter tabs ── */
        .tab-btn { border:1px solid #e2e8f0; background:#fff; color:#64748b; transition:all 0.18s ease; }
        .tab-btn:hover { background:#f8fafc; color:#334155; }
        .tab-active { background:linear-gradient(135deg,#2563eb,#1d4ed8) !important; border-color:#2563eb !important; color:#fff !important; box-shadow:0 3px 10px rgba(37,99,235,0.3); }

        /* ── Drawer animation ── */
        #exam-drawer { transform:translateX(100%); transition:transform 0.3s cubic-bezier(0.16,1,0.3,1); }
        #exam-drawer.open { transform:translateX(0); }

        /* ── Form inputs ── */
        .form-input { background:#f8fafc; border:1px solid #e2e8f0; transition:all 0.18s ease; }
        .form-input:focus { background:#fff; border-color:#93c5fd; box-shadow:0 0 0 3px rgba(147,197,253,0.25); outline:none; }

        /* ── Primary button ── */
        .btn-primary { background:linear-gradient(135deg,#2563eb,#1d4ed8); box-shadow:0 2px 8px rgba(37,99,235,0.3); }
        .btn-primary:hover { background:linear-gradient(135deg,#1d4ed8,#1e3a8a); box-shadow:0 4px 14px rgba(37,99,235,0.35); }

        /* ── Modal ── */
        .modal-overlay { background:rgba(15,23,42,0.35); backdrop-filter:blur(4px); }
        .modal-card { background:#fff; border:1px solid #e8edf5; box-shadow:0 20px 60px rgba(0,0,0,0.13); }

        /* ── Dropdown menu ── */
        .action-menu { background:#fff; border:1px solid #e8edf5; box-shadow:0 8px 24px rgba(0,0,0,0.1); }

        /* ── Section time editor ── */
        .time-editor { background:#f8fafc; border:1px solid #e2e8f0; }

        /* ── Scrollbar ── */
        ::-webkit-scrollbar { width:5px; height:5px; }
        ::-webkit-scrollbar-track { background:#f1f5f9; }
        ::-webkit-scrollbar-thumb { background:#cbd5e1; border-radius:10px; }

        /* ── Instructor avatar ── */
        .instructor-chip { background:#f5f3ff; border:1px solid #ddd6fe; color:#6d28d9; }

        /* ── Pulse ── */
        @keyframes outerPulse { 0%,100%{transform:scale(1);opacity:1} 50%{transform:scale(1.7);opacity:0} }
        .pulse-dot { animation:outerPulse 1.8s ease-in-out infinite; }

        /* ── Count anim ── */
        @keyframes fadeUp { from{opacity:0;transform:translateY(6px)} to{opacity:1;transform:translateY(0)} }
        .fade-up { animation:fadeUp 0.4s ease forwards; }

        /* ── Quick action menu ── */
        .action-item { transition:background 0.15s ease; }
        .action-item:hover { background:#f8fafc; }

        /* ── Participation fill colors ── */
        .fill-active  { background:#22c55e; }
        .fill-draft   { background:#fbbf24; }
        .fill-closed  { background:#f87171; }
        .fill-default { background:#3b82f6; }
    </style>
    @include('partials.notification-styles')
</head>
<body class="antialiased transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="darkMode ? 'dark-surface text-slate-100' : 'bg-slate-50 text-slate-800'">
<div class="flex min-h-screen">

    @include('partials.admin-sidebar')

    <!-- ════════════ MAIN CONTENT ════════════ -->
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        <!-- STICKY TOPBAR (matches the student portal's persistent topbar,
             in the admin's professional blue palette) -->
        <header class="flex items-center justify-between mb-0 flex-wrap gap-4 px-7 py-4 border-b sticky top-0 z-20 backdrop-blur-xl transition-colors duration-300"
                :class="darkMode ? 'bg-slate-900/95 border-slate-800' : 'bg-white/95 border-slate-100'"
                style="box-shadow:0 1px 4px rgba(0,0,0,0.04)">
            <div>
                <h2 class="text-xl font-bold flex items-center gap-2.5" :class="darkMode ? 'text-white' : 'text-slate-900'">
                    <span class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                        <i class="fa-solid fa-file-pen text-sm"></i>
                    </span>
                    Exams
                </h2>
                <p class="text-sm text-slate-400 mt-1">Create, schedule, and manage exams for your department.</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- Live indicator -->
                <div class="flex items-center gap-2 text-xs border px-3 py-1.5 rounded-xl"
                     :class="darkMode ? 'text-slate-400 bg-slate-800 border-slate-700' : 'text-slate-400 bg-white border-slate-200'">
                    <span class="relative flex items-center justify-center w-2 h-2">
                        <span class="pulse-dot absolute inline-flex w-full h-full rounded-full bg-emerald-400 opacity-70"></span>
                        <span class="relative inline-flex w-2 h-2 rounded-full bg-emerald-500"></span>
                    </span>
                    <span id="last-refresh" class="font-mono">--:--:--</span>
                </div>

                @include('partials.admin-darkmode-toggle')

                @include('partials.admin-notification-bell')

                <!-- Admin -->
                <div class="flex items-center gap-3 pl-3 border-l" :class="darkMode ? 'border-slate-700' : 'border-slate-200'">
                    <div class="text-right hidden sm:block">
                        <h4 class="text-sm font-semibold leading-tight" :class="darkMode ? 'text-white' : 'text-slate-900'">{{ Auth::user()->full_name ?? 'Admin User' }}</h4>
                        <span class="text-xs text-slate-400">Administrator</span>
                    </div>
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3)">
                        {{ Auth::user()->initials ?? 'AD' }}
                    </div>
                </div>
            </div>
        </header>

        <!-- SCROLLABLE PAGE BODY -->
        <div class="p-7">

        <!-- STAT CARDS -->
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
            <div class="stat-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Active</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f0fdf4;border:1px solid #bbf7d0">
                        <i class="fa-solid fa-circle-play text-emerald-600" style="font-size:11px"></i>
                    </div>
                </div>
                <p id="stat-active" class="text-3xl font-black text-slate-900 fade-up">{{ $stats['active'] ?? 2 }}</p>
                <div class="progress-bar mt-2"><div class="progress-fill fill-active" style="width:60%"></div></div>
            </div>
            <div class="stat-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Draft</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fffbeb;border:1px solid #fde68a">
                        <i class="fa-solid fa-pen-ruler text-amber-600" style="font-size:11px"></i>
                    </div>
                </div>
                <p id="stat-draft" class="text-3xl font-black text-slate-900 fade-up">{{ $stats['draft'] ?? 1 }}</p>
                <div class="progress-bar mt-2"><div class="progress-fill fill-draft" style="width:30%"></div></div>
            </div>
            <div class="stat-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Closed</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#fff1f2;border:1px solid #fecdd3">
                        <i class="fa-solid fa-lock text-red-500" style="font-size:11px"></i>
                    </div>
                </div>
                <p id="stat-closed" class="text-3xl font-black text-slate-900 fade-up">{{ $stats['closed'] ?? 1 }}</p>
                <div class="progress-bar mt-2"><div class="progress-fill fill-closed" style="width:25%"></div></div>
            </div>
            <div class="stat-card rounded-2xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Total Submissions</p>
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background:#f5f3ff;border:1px solid #ddd6fe">
                        <i class="fa-solid fa-paper-plane text-violet-600" style="font-size:11px"></i>
                    </div>
                </div>
                <p id="stat-submissions" class="text-3xl font-black text-slate-900 fade-up">{{ $stats['totalSubmissions'] ?? 86 }}</p>
                <div class="progress-bar mt-2"><div class="progress-fill bg-violet-400" style="width:80%"></div></div>
            </div>
        </div>

        <!-- TOOLBAR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <!-- Filter tabs -->
            <div class="flex items-center gap-1.5 bg-white border border-slate-200 rounded-xl p-1.5 shadow-sm">
                <button data-filter="all"     class="tab-btn tab-active px-4 py-2 rounded-lg text-xs font-bold transition-all">All</button>
                <button data-filter="active"  class="tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block mr-1.5 align-middle"></span>Active
                </button>
                <button data-filter="draft"   class="tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-400 inline-block mr-1.5 align-middle"></span>Draft
                </button>
                <button data-filter="closed"  class="tab-btn px-4 py-2 rounded-lg text-xs font-bold transition-all">
                    <span class="w-1.5 h-1.5 rounded-full bg-red-400 inline-block mr-1.5 align-middle"></span>Closed
                </button>
            </div>

            <div class="flex items-center gap-2.5">
                <!-- Search -->
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input id="exam-search" type="text" placeholder="Search exams..."
                        class="form-input pl-10 pr-4 py-2.5 rounded-xl text-sm text-slate-700 placeholder-slate-400 w-52">
                </div>
                <!-- Create button -->
                <button onclick="openCreateModal()"
                    class="btn-primary flex items-center gap-2 text-white text-xs font-bold px-4 py-2.5 rounded-xl transition-all">
                    <i class="fa-solid fa-plus"></i> Create Exam
                </button>
            </div>
        </div>

        <!-- Flash -->
        @if(session('success'))
        <div class="mb-5 p-3.5 rounded-xl flex items-center gap-2.5 text-sm font-medium text-emerald-700" style="background:#f0fdf4;border:1px solid #bbf7d0">
            <i class="fa-solid fa-circle-check text-emerald-500"></i> {{ session('success') }}
        </div>
        @endif

        <!-- EXAM CARDS GRID -->
        @php
        $demoExams = count($exams ?? []) > 0 ? $exams : [
            ['id'=>1,'title'=>'Microeconomics Midterm','subject'=>'Economics 201','status'=>'active','students'=>32,'submitted'=>21,'closes'=>'2026-07-16 18:00','instructor'=>'Dr. Chea Sophea','instructor_initials'=>'CS','sections'=>[['name'=>'Section 1','duration'=>45],['name'=>'Section 2','duration'=>30]],'questions'=>25],
            ['id'=>2,'title'=>'Data Structures Quiz 3','subject'=>'Computer Science 210','status'=>'active','students'=>28,'submitted'=>18,'closes'=>'2026-07-15 17:00','instructor'=>'Mr. Vannak Pich','instructor_initials'=>'VP','sections'=>[['name'=>'Section A','duration'=>60]],'questions'=>40],
            ['id'=>3,'title'=>'Flutter Widgets Practical','subject'=>'Mobile Development','status'=>'draft','students'=>0,'submitted'=>0,'closes'=>null,'instructor'=>'Ms. Srey Leak','instructor_initials'=>'SL','sections'=>[['name'=>'Part 1','duration'=>90],['name'=>'Part 2','duration'=>45]],'questions'=>15],
            ['id'=>4,'title'=>'Laravel Fundamentals Final','subject'=>'Web Backend 300','status'=>'closed','students'=>30,'submitted'=>30,'closes'=>'2026-07-09 20:00','instructor'=>'Dr. Chea Sophea','instructor_initials'=>'CS','sections'=>[['name'=>'Section 1','duration'=>120]],'questions'=>60],
            ['id'=>5,'title'=>'Calculus II Semester Exam','subject'=>'Mathematics 202','status'=>'draft','students'=>0,'submitted'=>0,'closes'=>null,'instructor'=>null,'instructor_initials'=>null,'sections'=>[['name'=>'Part A','duration'=>60],['name'=>'Part B','duration'=>60]],'questions'=>30],
            ['id'=>6,'title'=>'English Academic Writing','subject'=>'English 101','status'=>'active','students'=>45,'submitted'=>38,'closes'=>'2026-07-17 12:00','instructor'=>'Mrs. Linda Chhun','instructor_initials'=>'LC','sections'=>[['name'=>'Essay Section','duration'=>90]],'questions'=>10],
        ];
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5" id="exam-grid">
            @foreach($demoExams as $exam)
            @php
                $examId         = data_get($exam, 'id') ?? data_get($exam, 'exam_id');
                $examStatus     = data_get($exam, 'status', 'draft');
                $examTitle      = data_get($exam, 'title', '');
                $examSubject    = data_get($exam, 'subject', '');
                $examStudents   = data_get($exam, 'students', 0);
                $examSubmitted  = data_get($exam, 'submitted', 0);
                $examCloses     = data_get($exam, 'closes');
                $examQuestions  = data_get($exam, 'questions', 0);
                $examInstructor = data_get($exam, 'instructor');
                $examInstructorInitials = data_get($exam, 'instructor_initials', 'AD');

                $badgeCls = ['active'=>'badge-active','draft'=>'badge-draft','closed'=>'badge-closed'][$examStatus] ?? 'badge-draft';
                $fillCls  = ['active'=>'fill-active','draft'=>'fill-draft','closed'=>'fill-closed'][$examStatus] ?? 'fill-default';
                $pct      = ($examStudents > 0) ? round(($examSubmitted / $examStudents) * 100) : 0;
                
                $closesAt = (!empty($examCloses) && $examCloses !== 'Not scheduled yet') ? \Carbon\Carbon::parse($examCloses) : null;
                $closeLabel = $closesAt ? ($closesAt->isFuture() ? 'Closes '.$closesAt->diffForHumans() : 'Closed '.$closesAt->diffForHumans()) : 'Not scheduled';
                
                $statusIcon = ['active'=>'fa-circle-play','draft'=>'fa-pen-ruler','closed'=>'fa-lock'][$examStatus] ?? 'fa-circle';
            @endphp
            <div class="exam-card rounded-2xl p-5 cursor-pointer relative group"
                data-status="{{ $examStatus }}"
                data-title="{{ strtolower($examTitle) }}"
                onclick='openExamDrawer(@json($exam))'>

                <!-- Top row -->
                <div class="flex items-start justify-between mb-4">
                    <span class="{{ $badgeCls }} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                        <i class="fa-solid {{ $statusIcon }}" style="font-size:8px"></i>
                        {{ $examStatus }}
                    </span>
                    <!-- 3-dot menu -->
                    <div class="relative" onclick="event.stopPropagation()">
                        <button class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-600 hover:bg-slate-100 transition-all"
                            onclick="toggleMenu({{ $examId }})">
                            <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                        </button>
                        <div id="menu-{{ $examId }}" class="action-menu hidden absolute right-0 top-9 rounded-xl w-44 py-1.5 z-10">
                            <button onclick='openExamDrawer(@json($exam))' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5">
                                <i class="fa-solid fa-eye text-blue-500 w-4 text-center"></i> View Details
                            </button>
                            <button onclick='openExamDrawer(@json($exam))' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5">
                                <i class="fa-solid fa-pencil text-violet-500 w-4 text-center"></i> Edit Questions
                            </button>
                            <button onclick='openExamDrawer(@json($exam))' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5">
                                <i class="fa-solid fa-user-tie text-emerald-500 w-4 text-center"></i> Assign Instructor
                            </button>
                            <button onclick='openExamDrawer(@json($exam))' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5">
                                <i class="fa-solid fa-clock-rotate-left text-amber-500 w-4 text-center"></i> Adjust Time
                            </button>
                            <div class="border-t border-slate-100 my-1"></div>
                            
                            @if(Route::has('admin.exams.destroy'))
                            <form action="{{ route('admin.exams.destroy', $examId) }}" method="POST" onsubmit="return confirm('Delete this exam?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-red-600 flex items-center gap-2.5">
                                    <i class="fa-solid fa-trash-can w-4 text-center"></i> Delete
                                </button>
                            </form>
                            @else
                            <button onclick="alert('Delete operations are managed explicitly via the Teacher controller.')" class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-400 flex items-center gap-2.5">
                                <i class="fa-solid fa-trash-can w-4 text-center text-slate-300"></i> Delete Locked
                            </button>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Title & subject -->
                <h4 class="font-bold text-slate-900 text-sm leading-tight mb-1">{{ $examTitle }}</h4>
                <p class="text-xs text-slate-400 mb-4">{{ $examSubject }}</p>

                <!-- Instructor chip -->
                @if($examInstructor)
                <div class="flex items-center gap-2 mb-4">
                    <div class="instructor-chip w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0">
                        {{ $examInstructorInitials }}
                    </div>
                    <span class="text-xs text-slate-500 font-medium">{{ $examInstructor }}</span>
                </div>
                @else
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] bg-slate-100 border border-dashed border-slate-300 text-slate-400">
                        <i class="fa-solid fa-plus" style="font-size:7px"></i>
                    </div>
                    <span class="text-xs text-slate-400 italic">No instructor assigned</span>
                </div>
                @endif

                <!-- Participation progress -->
                <div class="flex items-center justify-between text-xs mb-1.5">
                    <span class="text-slate-400 font-medium">Participation</span>
                    <span class="font-bold text-slate-700">{{ $examSubmitted }}/{{ $examStudents }}
                        @if($examStudents > 0)
                        <span class="text-slate-400 font-normal">({{ $pct }}%)</span>
                        @endif
                    </span>
                </div>
                <div class="progress-bar mb-4">
                    <div class="progress-fill {{ $fillCls }}" style="width:{{ $pct }}%"></div>
                </div>

                <!-- Footer row -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <i class="fa-regular fa-clock text-slate-300"></i>
                        <span>{{ $closeLabel }}</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400">
                        <i class="fa-solid fa-circle-question text-slate-300"></i>
                        <span>{{ $examQuestions }} Qs</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        </div><!-- /page body -->
    </main>
</div>

<!-- ════════════ CREATE EXAM MODAL ════════════ -->
<div id="create-exam-modal" class="modal-overlay hidden fixed inset-0 z-30 flex items-center justify-center p-4">
    <div class="modal-card rounded-2xl w-full max-w-lg overflow-hidden max-h-[90vh] flex flex-col">
        <!-- Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0" style="background:#f8fafc">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <i class="fa-solid fa-file-plus text-sm"></i>
                </div>
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Create New Exam</h3>
                    <p class="text-[11px] text-slate-400">Fill in the exam details below</p>
                </div>
            </div>
            <button onclick="closeCreateModal()" class="w-7 h-7 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-all">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>

        <!-- Body -->
        <div class="p-6 overflow-y-auto flex-1">
            <form action="{{ Route::has('admin.exams.store') ? route('admin.exams.store') : '#' }}" method="POST" class="space-y-4" id="create-exam-form">
                @csrf

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            <i class="fa-solid fa-heading mr-1 text-slate-400"></i>Exam Title
                        </label>
                        <input name="title" required type="text" placeholder="e.g. Algorithms Final Exam"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400">
                    </div>

                    <div class="col-span-2">
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            <i class="fa-solid fa-book mr-1 text-slate-400"></i>Subject / Course
                        </label>
                        <input name="subject" required type="text" placeholder="e.g. Computer Science 210"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800 placeholder-slate-400">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            <i class="fa-regular fa-calendar mr-1 text-slate-400"></i>Start Date & Time
                        </label>
                        <input name="starts_at" type="datetime-local"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            <i class="fa-regular fa-calendar-xmark mr-1 text-slate-400"></i>End Date & Time
                        </label>
                        <input name="ends_at" type="datetime-local"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            <i class="fa-solid fa-stopwatch mr-1 text-slate-400"></i>Duration (min)
                        </label>
                        <input name="duration" type="number" value="60" min="5"
                            class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-800">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                            <i class="fa-solid fa-user-tie mr-1 text-slate-400"></i>Assign Instructor
                        </label>
                        <select name="instructor_id" class="form-input w-full px-4 py-2.5 rounded-xl text-sm text-slate-700 bg-white cursor-pointer">
                            <option value="">— Select instructor —</option>
                            @foreach($instructors ?? [] as $instructor)
                            <option value="{{ $instructor->user_id }}">{{ $instructor->full_name }}</option>
                            @endforeach
                            <option value="1">Dr. Chea Sophea</option>
                            <option value="2">Mr. Vannak Pich</option>
                            <option value="3">Ms. Srey Leak</option>
                        </select>
                    </div>
                </div>

                <!-- Sections builder -->
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            <i class="fa-solid fa-layer-group mr-1 text-slate-400"></i>Sections
                        </label>
                        <button type="button" onclick="addSection()" class="text-xs font-bold text-blue-600 flex items-center gap-1 hover:text-blue-800">
                            <i class="fa-solid fa-plus text-[9px]"></i> Add Section
                        </button>
                    </div>
                    <div id="sections-list" class="space-y-2">
                        <div class="section-row flex items-center gap-2">
                            <input type="text" name="sections[0][name]" placeholder="Section name" value="Section 1"
                                class="form-input flex-1 px-3 py-2 rounded-xl text-xs text-slate-800 placeholder-slate-400">
                            <input type="number" name="sections[0][duration]" placeholder="Min" value="60"
                                class="form-input w-20 px-3 py-2 rounded-xl text-xs text-slate-800">
                            <span class="text-[10px] text-slate-400 shrink-0">min</span>
                        </div>
                    </div>
                </div>

                <!-- Save as -->
                <div>
                    <label class="block text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">
                        <i class="fa-solid fa-floppy-disk mr-1 text-slate-400"></i>Save As
                    </label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all" style="border-color:#fde68a;background:#fffbeb" id="save-draft-label">
                            <input type="radio" name="status" value="draft" checked class="accent-amber-500" onchange="highlightSave()">
                            <div>
                                <p class="text-xs font-bold text-amber-700">Draft</p>
                                <p class="text-[10px] text-amber-600">Save for later</p>
                            </div>
                        </label>
                        <label class="flex-1 flex items-center gap-2.5 p-3 rounded-xl border-2 cursor-pointer transition-all" style="border-color:#e2e8f0;background:#f8fafc" id="save-active-label">
                            <input type="radio" name="status" value="active" class="accent-blue-600" onchange="highlightSave()">
                            <div>
                                <p class="text-xs font-bold text-slate-700">Publish Now</p>
                                <p class="text-[10px] text-slate-400">Make it live</p>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex gap-3 pt-1">
                    <button type="button" onclick="closeCreateModal()" class="flex-1 py-2.5 rounded-xl text-sm font-semibold text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 btn-primary text-white py-2.5 rounded-xl text-sm font-bold transition-all">
                        <i class="fa-solid fa-floppy-disk mr-1.5"></i>Save Exam
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ════════════ EXAM DETAIL DRAWER ════════════ -->
<div id="exam-drawer-backdrop" class="hidden fixed inset-0 z-30" style="background:rgba(15,23,42,0.3);backdrop-filter:blur(3px)" onclick="closeDrawer()"></div>
<div id="exam-drawer" class="fixed top-0 right-0 h-full w-full max-w-md bg-white z-40 shadow-2xl overflow-y-auto flex flex-col" style="border-left:1px solid #e8edf5">

    <!-- Drawer header -->
    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between shrink-0" style="background:#f8fafc">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">
                <i class="fa-solid fa-file-invoice text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-slate-900 text-sm">Exam Details</h3>
                <p class="text-[11px] text-slate-400">View, edit and manage this exam</p>
            </div>
        </div>
        <button onclick="closeDrawer()" class="w-7 h-7 rounded-lg hover:bg-slate-200 flex items-center justify-center text-slate-400 transition-all">
            <i class="fa-solid fa-xmark text-sm"></i>
        </button>
    </div>

    <!-- Drawer body — populated by JS -->
    <div id="drawer-body" class="flex-1 p-6 space-y-5 overflow-y-auto"></div>

    <!-- Drawer footer actions -->
    <div id="drawer-footer" class="shrink-0 px-6 py-4 border-t border-slate-100 flex gap-3" style="background:#f8fafc">
        <a id="drawer-edit-btn" href="#" class="flex-1 btn-primary text-white py-2.5 rounded-xl text-xs font-bold text-center flex items-center justify-center gap-2 transition-all">
            <i class="fa-solid fa-pencil"></i> Edit Questions
        </a>
        <button onclick="closeDrawer()" class="flex-1 py-2.5 rounded-xl text-xs font-semibold text-slate-600 border border-slate-200 hover:bg-slate-50 transition-all">
            Close
        </button>
    </div>
</div>

<!-- ════════════ SCRIPTS ════════════ -->
<script>
    if (window.lucide) lucide.createIcons();

    /* ── Clock ── */
    function tick() {
        document.getElementById('last-refresh').textContent = new Date().toLocaleTimeString();
    }
    tick(); setInterval(tick, 1000);

    /* ── Section counter ── */
    let sectionCount = 1;
    function addSection() {
        const list = document.getElementById('sections-list');
        const idx  = sectionCount++;
        const row  = document.createElement('div');
        row.className = 'section-row flex items-center gap-2';
        row.innerHTML = `
            <input type="text" name="sections[${idx}][name]" placeholder="Section name" value="Section ${idx+1}"
                class="form-input flex-1 px-3 py-2 rounded-xl text-xs text-slate-800 placeholder-slate-400">
            <input type="number" name="sections[${idx}][duration]" placeholder="Min" value="60"
                class="form-input w-20 px-3 py-2 rounded-xl text-xs text-slate-800">
            <span class="text-[10px] text-slate-400 shrink-0">min</span>
            <button type="button" onclick="this.parentElement.remove()" class="w-6 h-6 rounded-lg flex items-center justify-center text-red-400 hover:bg-red-50 transition-all shrink-0">
                <i class="fa-solid fa-xmark text-[10px]"></i>
            </button>`;
        list.appendChild(row);
    }

    /* ── Create modal ── */
    function openCreateModal() {
        document.getElementById('create-exam-modal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('create-exam-modal').classList.add('hidden');
    }
    document.getElementById('create-exam-modal').addEventListener('click', function(e) {
        if (e.target === this) closeCreateModal();
    });

    /* ── 3-dot menu ── */
    let openMenuId = null;
    function toggleMenu(id) {
        if (openMenuId && openMenuId !== id) document.getElementById('menu-' + openMenuId)?.classList.add('hidden');
        const menu = document.getElementById('menu-' + id);
        menu?.classList.toggle('hidden');
        openMenuId = menu?.classList.contains('hidden') ? null : id;
    }
    document.addEventListener('click', () => {
        if (openMenuId) { document.getElementById('menu-' + openMenuId)?.classList.add('hidden'); openMenuId = null; }
    });

    /* ── Filter tabs ── */
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.tab-btn').forEach(b => { b.classList.remove('tab-active'); });
            btn.classList.add('tab-active');
            const filter = btn.dataset.filter;
            document.querySelectorAll('.exam-card').forEach(card => {
                card.classList.toggle('hidden', filter !== 'all' && card.dataset.status !== filter);
            });
        });
    });

    /* ── Live search ── */
    document.getElementById('exam-search').addEventListener('input', function() {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.exam-card').forEach(card => {
            card.classList.toggle('hidden', q && !card.dataset.title.includes(q));
        });
    });

    /* ── Exam drawer ── */
    function openExamDrawer(exam) {
        const students = exam.students || 0;
        const submitted = exam.submitted || 0;
        const id = exam.id || exam.exam_id;
        const status = exam.status || 'draft';
        const title = exam.title || '';
        const subject = exam.subject || '';
        const questions = exam.questions || 0;
        const closes = exam.closes;
        
        const pct = students > 0 ? Math.round((submitted / students) * 100) : 0;
        const badgeCls = {active:'badge-active',draft:'badge-draft',closed:'badge-closed'}[status] ?? 'badge-draft';
        const fillCls  = {active:'fill-active',draft:'fill-draft',closed:'fill-closed'}[status] ?? 'fill-default';

        /* Build section time editors */
        const sections = exam.sections || [{name:'Section 1',duration:60}];
        let sectionsHtml = sections.map((s,i) => `
            <div class="time-editor rounded-xl p-4 flex items-center justify-between gap-3">
                <div>
                    <p class="text-xs font-bold text-slate-700">${s.name}</p>
                    <p class="text-[10px] text-slate-400 mt-0.5">Base: ${s.duration} min</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="adjustTime(${i}, -5)" class="w-7 h-7 rounded-lg flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all">−</button>
                    <span id="time-display-${i}" class="text-sm font-black text-slate-900 w-16 text-center font-mono">${s.duration} min</span>
                    <button onclick="adjustTime(${i}, 5)" class="w-7 h-7 rounded-lg flex items-center justify-center bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 text-xs font-bold transition-all">+</button>
                </div>
                <button onclick="applyTimeChange(${id}, ${i}, '${s.name}')"
                    class="text-[10px] font-bold px-2.5 py-1.5 rounded-lg transition-all" style="background:#eff6ff;border:1px solid #bfdbfe;color:#1d4ed8"
                    onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                    Apply
                </button>
            </div>`).join('');

        document.getElementById('drawer-body').innerHTML = `
            <!-- Status & title -->
            <div>
                <span class="${badgeCls} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide mb-2">
                    ${status}
                </span>
                <h4 class="text-lg font-black text-slate-900 leading-tight">${title}</h4>
                <p class="text-sm text-slate-400 mt-0.5">${subject}</p>
            </div>

            <!-- Key stats -->
            <div class="grid grid-cols-3 gap-3">
                <div class="p-3.5 rounded-xl text-center" style="background:#eff6ff;border:1px solid #bfdbfe">
                    <p class="text-[10px] text-blue-500 font-bold uppercase mb-1">Students</p>
                    <p class="text-xl font-black text-blue-700">${students}</p>
                </div>
                <div class="p-3.5 rounded-xl text-center" style="background:#f0fdf4;border:1px solid #bbf7d0">
                    <p class="text-[10px] text-emerald-500 font-bold uppercase mb-1">Submitted</p>
                    <p class="text-xl font-black text-emerald-700">${submitted}</p>
                </div>
                <div class="p-3.5 rounded-xl text-center" style="background:#f5f3ff;border:1px solid #ddd6fe">
                    <p class="text-[10px] text-violet-500 font-bold uppercase mb-1">Questions</p>
                    <p class="text-xl font-black text-violet-700">${questions}</p>
                </div>
            </div>

            <!-- Participation bar -->
            <div>
                <div class="flex items-center justify-between text-xs mb-2">
                    <span class="font-bold text-slate-600">Participation Rate</span>
                    <span class="font-black text-slate-900">${pct}%</span>
                </div>
                <div class="progress-bar" style="height:8px">
                    <div class="progress-fill ${fillCls}" style="width:${pct}%"></div>
                </div>
            </div>

            <!-- Instructor -->
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Assigned Instructor</p>
                <div class="flex items-center justify-between p-3.5 rounded-xl" style="background:#f8fafc;border:1px solid #e2e8f0">
                    ${exam.instructor ? `
                    <div class="flex items-center gap-3">
                        <div class="instructor-chip w-9 h-9 rounded-xl flex items-center justify-center text-xs font-bold">${exam.instructor_initials}</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800">${exam.instructor}</p>
                            <p class="text-[10px] text-slate-400">Lead Instructor</p>
                        </div>
                    </div>
                    <button class="text-xs text-blue-600 font-bold hover:text-blue-800">Change</button>
                    ` : `
                    <p class="text-sm text-slate-400 italic">No instructor assigned yet</p>
                    <button class="text-xs font-bold px-3 py-1.5 rounded-lg text-blue-600" style="background:#eff6ff;border:1px solid #bfdbfe">Assign</button>
                    `}
                </div>
            </div>

            <!-- Section time editor -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest">Section Time Editor</p>
                    <span class="text-[10px] text-slate-400 font-mono bg-amber-50 border border-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-semibold">
                        <i class="fa-solid fa-triangle-exclamation text-[8px] mr-1"></i>Live changes apply instantly
                    </span>
                </div>
                <div class="space-y-2" id="section-editors">
                    ${sectionsHtml}
                </div>
            </div>

            <!-- Schedule -->
            <div>
                <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Schedule</p>
                <div class="p-4 rounded-xl flex items-center gap-3" style="background:#fffbeb;border:1px solid #fde68a">
                    <i class="fa-regular fa-calendar text-amber-500"></i>
                    <div>
                        <p class="text-xs font-bold text-amber-800">${closes && closes !== 'Not scheduled yet' ? 'Closing: ' + new Date(closes).toLocaleString() : 'Not scheduled yet'}</p>
                        <p class="text-[10px] text-amber-600 mt-0.5">Click Edit Questions to update the schedule</p>
                    </div>
                </div>
            </div>`;

        /* Store section durations for +/- */
        window._drawerSections = sections.map(s => ({...s}));
        window._drawerExamId   = id;

        document.getElementById('drawer-edit-btn').href = `/admin/exams/${id}/questions`;
        document.getElementById('exam-drawer-backdrop').classList.remove('hidden');
        document.getElementById('exam-drawer').classList.add('open');
    }

    /* Section time adjust */
    window._drawerSections = [];
    function adjustTime(idx, delta) {
        const sec = window._drawerSections[idx];
        sec.duration = Math.max(5, sec.duration + delta);
        document.getElementById('time-display-' + idx).textContent = sec.duration + ' min';
    }

    function applyTimeChange(examId, idx, sectionName) {
        const newDuration = window._drawerSections[idx].duration;
        fetch(`/admin/exams/${examId}/sections/time`, {
            method: 'PATCH',
            headers: {'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]')?.content || ''},
            body: JSON.stringify({section_index: idx, section_name: sectionName, duration: newDuration})
        }).then(r => {
            if (r.ok) {
                const btn = document.querySelector(`#section-editors .time-editor:nth-child(${idx+1}) button:last-child`);
                if (btn) {
                    const orig = btn.textContent;
                    btn.textContent = '✓ Saved';
                    btn.style.background = '#f0fdf4'; btn.style.borderColor = '#bbf7d0'; btn.style.color = '#15803d';
                    setTimeout(() => { btn.textContent = orig; btn.style.background='#eff6ff'; btn.style.borderColor='#bfdbfe'; btn.style.color='#1d4ed8'; }, 2000);
                }
            }
        }).catch(() => {
            /* Demo fallback */
            alert(`✓ Applied: ${sectionName} → ${newDuration} min`);
        });
    }

    function closeDrawer() {
        document.getElementById('exam-drawer').classList.remove('open');
        document.getElementById('exam-drawer-backdrop').classList.add('hidden');
    }

    /* ── Real-time refresh: stat cards + exam grid ──
       Polls the live admin.exams.api endpoint and rebuilds both the
       summary numbers and every exam card's status/participation, so
       newly registered students, new submissions, and newly published
       exams show up on their own — no manual page reload required. */
    function examCardHtml(exam) {
        const students   = exam.students || 0;
        const submitted  = exam.submitted || 0;
        const id         = exam.id;
        const status     = exam.status || 'draft';
        const title      = exam.title || '';
        const subject    = exam.subject || '';
        const questions  = exam.questions || 0;
        const instructor = exam.instructor;
        const initials   = exam.instructor_initials || 'AD';
        const pct        = students > 0 ? Math.round((submitted / students) * 100) : 0;

        const badgeCls = {active:'badge-active', draft:'badge-draft', closed:'badge-closed'}[status] ?? 'badge-draft';
        const fillCls  = {active:'fill-active', draft:'fill-draft', closed:'fill-closed'}[status] ?? 'fill-default';
        const iconCls  = {active:'fa-circle-play', draft:'fa-pen-ruler', closed:'fa-lock'}[status] ?? 'fa-circle';

        let closeLabel = 'Not scheduled';
        if (exam.closes && exam.closes !== 'Not scheduled yet') {
            const d = new Date(exam.closes);
            if (!isNaN(d)) closeLabel = (d > new Date() ? 'Closes ' : 'Closed ') + d.toLocaleString();
        }

        const examJson = JSON.stringify(exam).replace(/'/g, '&#39;');
        const instructorBlock = instructor
            ? `<div class="flex items-center gap-2 mb-4">
                 <div class="instructor-chip w-6 h-6 rounded-full flex items-center justify-center text-[9px] font-bold shrink-0">${initials}</div>
                 <span class="text-xs text-slate-500 font-medium">${instructor}</span>
               </div>`
            : `<div class="flex items-center gap-2 mb-4">
                 <div class="w-6 h-6 rounded-full flex items-center justify-center text-[9px] bg-slate-100 border border-dashed border-slate-300 text-slate-400"><i class="fa-solid fa-plus" style="font-size:7px"></i></div>
                 <span class="text-xs text-slate-400 italic">No instructor assigned</span>
               </div>`;

        return `
        <div class="exam-card rounded-2xl p-5 cursor-pointer relative group"
             data-status="${status}" data-title="${title.toLowerCase()}"
             onclick='openExamDrawer(${examJson})'>
            <div class="flex items-start justify-between mb-4">
                <span class="${badgeCls} inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide">
                    <i class="fa-solid ${iconCls}" style="font-size:8px"></i>${status}
                </span>
                <div class="relative" onclick="event.stopPropagation()">
                    <button class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-300 hover:text-slate-600 hover:bg-slate-100 transition-all" onclick="toggleMenu('${id}')">
                        <i class="fa-solid fa-ellipsis-vertical text-xs"></i>
                    </button>
                    <div id="menu-${id}" class="action-menu hidden absolute right-0 top-9 rounded-xl w-44 py-1.5 z-10">
                        <button onclick='openExamDrawer(${examJson})' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5"><i class="fa-solid fa-eye text-blue-500 w-4 text-center"></i> View Details</button>
                        <button onclick='openExamDrawer(${examJson})' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5"><i class="fa-solid fa-pencil text-violet-500 w-4 text-center"></i> Edit Questions</button>
                        <button onclick='openExamDrawer(${examJson})' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5"><i class="fa-solid fa-user-tie text-emerald-500 w-4 text-center"></i> Assign Instructor</button>
                        <button onclick='openExamDrawer(${examJson})' class="action-item w-full text-left px-4 py-2.5 text-xs font-semibold text-slate-700 flex items-center gap-2.5"><i class="fa-solid fa-clock-rotate-left text-amber-500 w-4 text-center"></i> Adjust Time</button>
                    </div>
                </div>
            </div>
            <h4 class="font-bold text-slate-900 text-sm leading-tight mb-1">${title}</h4>
            <p class="text-xs text-slate-400 mb-4">${subject}</p>
            ${instructorBlock}
            <div class="flex items-center justify-between text-xs mb-1.5">
                <span class="text-slate-400 font-medium">Participation</span>
                <span class="font-bold text-slate-700">${submitted}/${students}${students > 0 ? ` <span class="text-slate-400 font-normal">(${pct}%)</span>` : ''}</span>
            </div>
            <div class="progress-bar mb-4"><div class="progress-fill ${fillCls}" style="width:${pct}%"></div></div>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5 text-xs text-slate-400"><i class="fa-regular fa-clock text-slate-300"></i><span>${closeLabel}</span></div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400"><i class="fa-solid fa-circle-question text-slate-300"></i><span>${questions} Qs</span></div>
            </div>
        </div>`;
    }

    function applyActiveFilterAndSearch() {
        const activeTab = document.querySelector('.tab-btn.tab-active');
        const filter = activeTab ? activeTab.dataset.filter : 'all';
        const q = (document.getElementById('exam-search').value || '').toLowerCase();
        document.querySelectorAll('.exam-card').forEach(card => {
            const matchesFilter = filter === 'all' || card.dataset.status === filter;
            const matchesSearch = !q || card.dataset.title.includes(q);
            card.classList.toggle('hidden', !(matchesFilter && matchesSearch));
        });
    }

    function refreshStats() {
        const targetApiEndpoint = '{{ route("admin.exams.api") }}';

        fetch(targetApiEndpoint, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.stats) {
                    const map = {
                        'stat-active':      data.stats.active,
                        'stat-draft':       data.stats.draft,
                        'stat-closed':      data.stats.closed,
                        'stat-submissions': data.stats.totalSubmissions,
                    };
                    Object.entries(map).forEach(([id, val]) => {
                        const el = document.getElementById(id);
                        if (el && el.textContent !== String(val)) el.textContent = val;
                    });
                }

                if (Array.isArray(data.exams)) {
                    const grid = document.getElementById('exam-grid');
                    grid.innerHTML = data.exams.map(examCardHtml).join('');
                    applyActiveFilterAndSearch();
                }

                const clock = document.getElementById('last-refresh');
                if (clock) clock.textContent = new Date().toLocaleTimeString();
            }).catch(() => {});
    }
    refreshStats();
    setInterval(refreshStats, 8000);
</script>
@include('partials.admin-notification-realtime')
</body>
</html>