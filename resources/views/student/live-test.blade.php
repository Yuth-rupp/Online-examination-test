<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $exam->title ?? 'Live Assessment' }} — ExamSystem</title>
  <meta name="description" content="Live exam session on ExamSystem.">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    [x-cloak] { display: none !important; }
    ::-webkit-scrollbar { width: 4px; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }

    .brand-gradient { background: linear-gradient(135deg, #4F6EF7 0%, #7C3AED 100%); }

    /* Timer urgency states */
    .timer-normal  { background: linear-gradient(135deg, #4F6EF7, #7C3AED); }
    .timer-warning { background: linear-gradient(135deg, #F59E0B, #EF4444); }
    .timer-danger  { background: linear-gradient(135deg, #EF4444, #B91C1C); animation: timerShake 0.4s ease infinite alternate; }
    @keyframes timerShake { from{transform:translateX(-2px)} to{transform:translateX(2px)} }

    /* Option card animations */
    .option-card { transition: all 0.15s cubic-bezier(0.4,0,0.2,1); }
    .option-card:hover { transform: translateY(-1px); }

    /* Slide transitions */
    [x-transition\:enter]   { transition: opacity 0.2s ease, transform 0.2s ease; }
    [x-transition\:enter-start] { opacity:0; transform:translateX(16px); }
    [x-transition\:enter-end]   { opacity:1; transform:translateX(0); }
    [x-transition\:leave]   { transition: opacity 0.12s ease; }
    [x-transition\:leave-end]   { opacity:0; }

    /* Confirm modal */
    .modal-enter { animation: mIn 0.2s ease both; }
    @keyframes mIn { from{opacity:0;transform:scale(0.95)translateY(8px)} to{opacity:1;transform:scale(1)translateY(0)} }

    /* Tab-switch toast */
    .toast-enter { animation: toastIn 0.3s ease both; }
    @keyframes toastIn { from{opacity:0;transform:translateY(-20px)} to{opacity:1;transform:translateY(0)} }

    /* Save pulse */
    .save-pulse { animation: savePulse 1.5s ease both; }
    @keyframes savePulse { 0%{opacity:0.4} 50%{opacity:1} 100%{opacity:0.4} }

    .live-dot { animation: livePulse 1.5s ease infinite; }
    @keyframes livePulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.5;transform:scale(1.5)} }
  </style>
</head>

<body class="bg-slate-50 text-slate-800 antialiased min-h-screen flex flex-col"
      x-data="examApp"
      @keydown.left.window="if(currentQ > 0) currentQ--"
      @keydown.right.window="if(currentQ < totalQ - 1) currentQ++">

  <!-- ════ TAB-SWITCH TOAST ════ -->
  <div x-show="tabWarning" x-cloak
       class="fixed top-4 left-1/2 -translate-x-1/2 z-[100] toast-enter">
    <div class="flex items-center gap-3 bg-amber-500 text-white px-5 py-3 rounded-2xl shadow-xl font-bold text-sm">
      <i data-lucide="alert-triangle" class="w-4 h-4 flex-shrink-0"></i>
      <span>Warning: Tab switching detected! (<span x-text="tabSwitchCount"></span> time<span x-show="tabSwitchCount>1">s</span>)</span>
    </div>
  </div>

  <!-- ════ CONFIRM SUBMIT MODAL ════ -->
  <div x-show="confirmOpen" x-cloak
       class="fixed inset-0 bg-slate-950/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="modal-enter bg-white rounded-3xl border border-slate-100 shadow-2xl max-w-sm w-full overflow-hidden">
      <div class="px-6 py-5 border-b border-slate-100">
        <div class="flex items-center gap-3 mb-1">
          <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-sm">
            <i data-lucide="send" class="w-4 h-4 text-white"></i>
          </div>
          <h3 class="text-sm font-black text-slate-900">Submit Exam?</h3>
        </div>
        <p class="text-xs text-slate-400 font-medium mt-2">This action is final and cannot be undone.</p>
      </div>
      <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-3">
          <span class="text-xs font-bold text-slate-500">Answered</span>
          <span class="text-sm font-black text-emerald-600" x-text="answeredCount + ' / ' + totalQ"></span>
        </div>
        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-full brand-gradient rounded-full transition-all duration-500"
               :style="'width:' + (answeredCount/totalQ*100) + '%'"></div>
        </div>
        <template x-if="unansweredList.length > 0">
          <div class="mt-4 p-3 bg-amber-50 border border-amber-100 rounded-xl">
            <p class="text-[11px] font-black text-amber-700 mb-1.5">Unanswered questions:</p>
            <div class="flex flex-wrap gap-1.5">
              <template x-for="n in unansweredList" :key="n">
                <span class="w-7 h-7 rounded-lg bg-amber-100 text-amber-700 text-[11px] font-black flex items-center justify-center" x-text="n"></span>
              </template>
            </div>
          </div>
        </template>
      </div>
      <div class="px-6 py-4 border-t border-slate-100 flex justify-end gap-2">
        <button @click="confirmOpen=false"
                class="px-4 py-2 text-xs font-bold border border-slate-200 text-slate-600 rounded-xl hover:bg-slate-50 cursor-pointer transition-colors">
          Keep Answering
        </button>
        <button @click="$refs.examForm.submit()"
                class="px-5 py-2 brand-gradient text-white text-xs font-black rounded-xl shadow-sm hover:opacity-90 cursor-pointer transition-opacity">
          Yes, Submit Now
        </button>
      </div>
    </div>
  </div>

  <!-- ════ HEADER ════ -->
  <header class="bg-white border-b border-slate-100 px-5 py-3 sticky top-0 z-30 select-none shadow-sm">
    <div class="max-w-[1680px] mx-auto flex items-center justify-between gap-4">

      <!-- Left: title + progress -->
      <div class="flex items-center gap-4 min-w-0">
        <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-md shadow-indigo-200 flex-shrink-0">
          <i data-lucide="graduation-cap" class="w-4 h-4 text-white"></i>
        </div>
        <div class="min-w-0">
          <h1 class="text-sm font-black text-slate-900 leading-none truncate max-w-[280px]">{{ $exam->title ?? 'Live Assessment' }}</h1>
          <p class="text-[11px] text-slate-400 font-medium mt-0.5">
            Question <span class="font-black text-indigo-600" x-text="currentQ + 1"></span> of <span x-text="totalQ"></span>
          </p>
        </div>
      </div>

      <!-- Center: progress bar -->
      <div class="flex-1 max-w-xs hidden sm:block">
        <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 mb-1">
          <span x-text="answeredCount + ' answered'"></span>
          <span x-text="Math.round(answeredCount/totalQ*100) + '%'"></span>
        </div>
        <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
          <div class="h-full brand-gradient rounded-full transition-all duration-500"
               :style="'width:' + (totalQ > 0 ? answeredCount/totalQ*100 : 0) + '%'"></div>
        </div>
      </div>

      <!-- Right: timer + save + user -->
      <div class="flex items-center gap-2.5 flex-shrink-0">
        <!-- Auto-save -->
        <div class="hidden sm:flex items-center gap-1.5 text-[11px] font-bold text-emerald-500">
          <i data-lucide="cloud" class="w-3.5 h-3.5" :class="saving?'save-pulse':''"></i>
          <span x-text="saving?'Saving…':'Saved'"></span>
        </div>

        <!-- Timer -->
        <div class="flex items-center gap-2 px-3.5 py-2 rounded-xl text-white text-xs font-black tabular-nums shadow-sm transition-all duration-500"
             :class="secondsLeft > 600 ? 'timer-normal' : secondsLeft > 300 ? 'timer-warning' : 'timer-danger'">
          <i data-lucide="clock" class="w-3.5 h-3.5"></i>
          <span x-text="formatTime()"></span>
        </div>

        <div class="w-px h-6 bg-slate-200"></div>

        <!-- User -->
        <div class="flex items-center gap-2">
          <div class="hidden sm:block text-right">
            <p class="text-xs font-black text-slate-800 leading-none">{{ Auth::user()->full_name ?? 'Student' }}</p>
            <p class="text-[10px] text-slate-400 font-mono mt-0.5">{{ Auth::user()->user_id ?? 'STU' }}</p>
          </div>
          <div class="w-8 h-8 rounded-xl overflow-hidden bg-gradient-to-br from-amber-400 to-orange-400 flex items-center justify-center text-[11px] font-black text-amber-900">
            @if(Auth::user() && Auth::user()->avatar_url)
              <img src="{{ Auth::user()->avatar_url }}" class="w-full h-full object-cover" alt="{{ Auth::user()->full_name }}">
            @else
              {{ Auth::user() ? strtoupper(substr(Auth::user()->full_name, 0, 2)) : 'ST' }}
            @endif
          </div>
        </div>
      </div>
    </div>
  </header>

  <!-- ════ MAIN LAYOUT ════ -->
  <div class="flex-1 max-w-[1680px] w-full mx-auto flex flex-col lg:flex-row gap-5 px-5 py-6">

    <!-- ── QUESTION CARD ── -->
    <form id="examForm" x-ref="examForm" method="POST" action="{{ route('student.submission.store') }}"
          class="flex-1 flex flex-col bg-white border border-slate-100 rounded-3xl shadow-sm overflow-hidden">
      @csrf
      <input type="hidden" name="exam_id" value="{{ $exam->exam_id ?? '1' }}">

      <!-- Question area -->
      <div class="flex-1 p-7 lg:p-9">

        @forelse($exam->questions ?? [] as $index => $question)
          @php
            // ── Normalize question type ──
            $qType = strtoupper($question->type ?? $question->question_type ?? 'MCQ');
            $qTypeNorm = match(true) {
              in_array($qType, ['TRUE/FALSE','TRUE_FALSE','BOOLEAN','TF']) => 'TF',
              in_array($qType, ['ESSAY','SHORT_ANSWER','LONG_ANSWER','SHORT','LONG']) => 'ESSAY',
              default => 'MCQ',
            };

            // ── Normalize question text ──
            $qText = $question->question_text
                  ?? $question->question
                  ?? $question->content
                  ?? $question->body
                  ?? $question->text
                  ?? $question->title
                  ?? '(No question text found)';

            // ── Normalize question ID ──
            $qId = $question->id ?? $question->question_id ?? ($index + 1);

            // ── Priority Explicit Mapping for Independent Column Options ──
            if (isset($question->option_a) || isset($question->option_b)) {
                $rawOptions = [];
                if (!empty($question->option_a)) $rawOptions['A'] = $question->option_a;
                if (!empty($question->option_b)) $rawOptions['B'] = $question->option_b;
                if (!empty($question->option_c)) $rawOptions['C'] = $question->option_c;
                if (!empty($question->option_d)) $rawOptions['D'] = $question->option_d;
            } else {
                $rawOptions = $question->options ?? $question->choices ?? $question->answers ?? [];
            }

            // Decode JSON strings safely if options are stored as structured strings
            if (is_string($rawOptions)) {
                $rawOptions = json_decode($rawOptions, true) ?? [];
            }

            if ($rawOptions instanceof \Illuminate\Support\Collection) {
                $rawOptions = $rawOptions->toArray();
            }
            $rawOptions = (array) $rawOptions;

            // Handle plain non-associative mapping labels
            $isAssoc = count($rawOptions) > 0 && array_keys($rawOptions) !== range(0, count($rawOptions) - 1);
            if (!$isAssoc && count($rawOptions) > 0) {
                $letters = ['A','B','C','D','E','F','G','H'];
                $labeled = [];
                foreach (array_values($rawOptions) as $i => $v) {
                    if (is_array($v) || is_object($v)) {
                        $v = (array) $v;
                        $v = $v['option_text'] ?? $v['text'] ?? $v['value'] ?? $v['content'] ?? reset($v);
                    }
                    $labeled[$letters[$i] ?? ($i+1)] = $v;
                }
                $rawOptions = $labeled;
            } else {
                foreach ($rawOptions as $k => $v) {
                    if (is_array($v) || is_object($v)) {
                        $v = (array) $v;
                        $rawOptions[$k] = $v['option_text'] ?? $v['text'] ?? $v['value'] ?? $v['content'] ?? reset($v);
                    }
                }
            }
          @endphp

          <div x-show="currentQ === {{ $index }}" class="space-y-7">

            <!-- Question header -->
            <div class="flex items-start gap-4">
              <div class="w-10 h-10 brand-gradient rounded-2xl flex items-center justify-center text-white font-black text-sm shadow-md shadow-indigo-200 flex-shrink-0">
                {{ $index + 1 }}
              </div>
              <div class="flex-1 min-w-0">
                <div class="flex flex-wrap items-center gap-2 mb-2.5">
                  <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg
                    @if($qTypeNorm==='ESSAY') bg-violet-50 text-violet-700
                    @elseif($qTypeNorm==='TF') bg-teal-50 text-teal-700
                    @else bg-indigo-50 text-indigo-700 @endif">
                    @if($qTypeNorm==='ESSAY') Essay
                    @elseif($qTypeNorm==='TF') True / False
                    @else Multiple Choice @endif
                  </span>
                  @if(isset($question->points))
                    <span class="px-2.5 py-1 text-[10px] font-black uppercase tracking-wider rounded-lg bg-amber-50 text-amber-700">
                      {{ $question->points }} pts
                    </span>
                  @endif
                </div>
                <h2 class="text-xl font-black text-slate-900 leading-snug">
                  {{ $qText }}
                </h2>

                @if(!empty($question->media_url))
                  <div class="mt-4 max-w-xl">
                    <img src="{{ $question->media_full_url }}"
                         alt="Question image"
                         class="rounded-2xl border border-slate-100 shadow-sm max-h-80 object-contain">
                  </div>
                @endif
              </div>
            </div>

            <!-- ─── MCQ Choices ─── -->
            @if($qTypeNorm === 'MCQ')
              <div class="space-y-3 max-w-2xl">
                @forelse($rawOptions as $optKey => $optionText)
                  <label class="option-card flex items-center gap-4 p-4 border-2 rounded-2xl cursor-pointer select-none group"
                         :class="answers[{{ $index }}] === '{{ $optKey }}'
                           ? 'border-indigo-500 bg-indigo-50'
                           : 'border-slate-100 bg-white hover:border-indigo-200 hover:bg-indigo-50/30'">
                    <input type="radio" name="questions[{{ $qId }}]" value="{{ $optKey }}"
                           @change="setAnswer({{ $index }}, '{{ $optKey }}')" class="sr-only">
                    <div class="w-9 h-9 rounded-xl font-black text-sm flex items-center justify-center border-2 flex-shrink-0 transition-all duration-150"
                         :class="answers[{{ $index }}] === '{{ $optKey }}'
                           ? 'brand-gradient text-white border-transparent shadow-sm shadow-indigo-200'
                           : 'border-slate-200 text-slate-400 bg-slate-50 group-hover:border-indigo-300'">
                      {{ strtoupper($optKey) }}
                    </div>
                    <span class="text-sm font-semibold leading-snug"
                          :class="answers[{{ $index }}] === '{{ $optKey }}' ? 'text-indigo-900' : 'text-slate-700'">
                      {{ $optionText }}
                    </span>
                    <div class="ml-auto flex-shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition-all"
                         :class="answers[{{ $index }}] === '{{ $optKey }}'
                           ? 'border-indigo-500 bg-indigo-500'
                           : 'border-slate-200'">
                      <i data-lucide="check" class="w-3 h-3 text-white" x-show="answers[{{ $index }}] === '{{ $optKey }}'"></i>
                    </div>
                  </label>
                @empty
                  <div class="p-4 bg-amber-50 border border-amber-200 rounded-2xl text-xs font-bold text-amber-700 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                    No options found for this question. Please contact your instructor.
                  </div>
                @endforelse
              </div>

            <!-- ─── TRUE / FALSE Choices ─── -->
            @elseif($qTypeNorm === 'TF')
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-w-2xl">
                <label class="option-card flex flex-col items-center justify-center gap-3 p-7 border-2 rounded-2xl cursor-pointer select-none"
                       :class="answers[{{ $index }}] === 'TRUE'
                         ? 'border-teal-500 bg-teal-50'
                         : 'border-slate-100 bg-white hover:border-teal-200 hover:bg-teal-50/30'">
                <input type="radio" name="questions[{{ $qId }}]" value="TRUE"
                         @change="setAnswer({{ $index }}, 'TRUE')" class="sr-only">
                  <div class="w-14 h-14 rounded-2xl border-2 flex items-center justify-center transition-all"
                       :class="answers[{{ $index }}] === 'TRUE'
                         ? 'bg-teal-500 border-teal-500 shadow-md shadow-teal-200'
                         : 'border-slate-200 bg-slate-50'">
                    <i data-lucide="check" class="w-6 h-6" :class="answers[{{ $index }}] === 'TRUE' ? 'text-white' : 'text-slate-400'"></i>
                  </div>
                  <span class="text-base font-black" :class="answers[{{ $index }}] === 'TRUE' ? 'text-teal-800' : 'text-slate-700'">TRUE</span>
                </label>

                <label class="option-card flex flex-col items-center justify-center gap-3 p-7 border-2 rounded-2xl cursor-pointer select-none"
                       :class="answers[{{ $index }}] === 'FALSE'
                         ? 'border-rose-500 bg-rose-50'
                         : 'border-slate-100 bg-white hover:border-rose-200 hover:bg-rose-50/30'">
                <input type="radio" name="questions[{{ $qId }}]" value="FALSE"
                         @change="setAnswer({{ $index }}, 'FALSE')" class="sr-only">
                  <div class="w-14 h-14 rounded-2xl border-2 flex items-center justify-center transition-all"
                       :class="answers[{{ $index }}] === 'FALSE'
                         ? 'bg-rose-500 border-rose-500 shadow-md shadow-rose-200'
                         : 'border-slate-200 bg-slate-50'">
                    <i data-lucide="x" class="w-6 h-6" :class="answers[{{ $index }}] === 'FALSE' ? 'text-white' : 'text-slate-400'"></i>
                  </div>
                  <span class="text-base font-black" :class="answers[{{ $index }}] === 'FALSE' ? 'text-rose-800' : 'text-slate-700'">FALSE</span>
                </label>
              </div>

            <!-- ─── ESSAY Workspace ─── -->
            @elseif($qTypeNorm === 'ESSAY')
              <div class="max-w-2xl space-y-2">
                <textarea name="questions[{{ $qId }}]"
                          rows="8"
                          placeholder="Write your answer here. Be clear and detailed…"
                          @input="setAnswer({{ $index }}, $event.target.value)"
                          class="w-full px-5 py-4 border-2 border-slate-200 rounded-2xl text-sm font-medium leading-relaxed bg-slate-50/40 focus:outline-none focus:ring-0 focus:border-indigo-400 focus:bg-white resize-y transition-all placeholder-slate-300"></textarea>
                <div class="flex items-center justify-between px-1 text-[11px] font-bold text-slate-400">
                  <span>Minimum 3 sentences recommended</span>
                  <span x-text="(answers[{{ $index }}] ? answers[{{ $index }}].split(' ').filter(w=>w).length : 0) + ' words · ' + (answers[{{ $index }}] ? answers[{{ $index }}].length : 0) + ' chars'"></span>
                </div>
              </div>
            @endif
          </div>

        @empty
          <div class="flex flex-col items-center justify-center py-24 text-center">
            <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mb-4">
              <i data-lucide="inbox" class="w-7 h-7 text-slate-400"></i>
            </div>
            <h3 class="text-base font-black text-slate-700 mb-1">No Questions Found</h3>
            <p class="text-sm text-slate-400">This exam has no questions loaded yet.</p>
          </div>
        @endforelse
      </div>

      <!-- ── Navigation footer ── -->
      <div class="px-7 lg:px-9 py-5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between gap-4 select-none">
        <button type="button" @click="if(currentQ > 0) currentQ--"
                :disabled="currentQ === 0"
                :class="currentQ === 0 ? 'opacity-30 cursor-not-allowed' : 'hover:bg-white hover:shadow-sm cursor-pointer'"
                class="flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 transition-all">
          <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Previous
        </button>

        <!-- Dot indicators -->
        <div class="flex items-center gap-1.5">
          @foreach($exam->questions ?? [] as $i => $q)
            <button type="button" @click="currentQ = {{ $i }}"
                    class="rounded-full transition-all duration-200 cursor-pointer"
                    :class="currentQ === {{ $i }} ? 'w-6 h-2 brand-gradient' : (answers[{{ $i }}] !== undefined && answers[{{ $i }}] !== '' ? 'w-2 h-2 bg-emerald-400' : 'w-2 h-2 bg-slate-300')">
            </button>
          @endforeach
        </div>

        <div class="flex items-center gap-2">
          <button type="button" @click="if(currentQ < totalQ - 1) currentQ++"
                  x-show="currentQ < totalQ - 1"
                  class="flex items-center gap-2 px-5 py-2.5 border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-white hover:shadow-sm cursor-pointer transition-all">
            Next <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
          </button>
          <button type="button" @click="confirmOpen = true"
                  x-show="currentQ === totalQ - 1"
                  class="flex items-center gap-2 px-5 py-2.5 brand-gradient text-white text-xs font-black rounded-xl shadow-md shadow-indigo-200 hover:opacity-90 cursor-pointer transition-all">
            <i data-lucide="send" class="w-3.5 h-3.5"></i> Submit
          </button>
        </div>
      </div>
    </form>

    <!-- ── RIGHT SIDEBAR ── -->
    <aside class="w-full lg:w-72 xl:w-80 flex flex-col gap-4 select-none">

      <!-- Exam map -->
      <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm">
        <div class="flex items-center justify-between mb-4">
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Navigation</p>
            <h3 class="text-sm font-black text-slate-900 mt-0.5">Exam Map</h3>
          </div>
          <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-xl bg-emerald-50 border border-emerald-100">
            <span class="text-[11px] font-black text-emerald-700" x-text="answeredCount"></span>
            <span class="text-[10px] font-medium text-emerald-500">/ <span x-text="totalQ"></span></span>
          </div>
        </div>

        <!-- Legend -->
        <div class="flex items-center gap-3 mb-3 text-[10px] font-bold text-slate-400">
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm brand-gradient"></span> Current</span>
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-emerald-400"></span> Done</span>
          <span class="flex items-center gap-1"><span class="w-3 h-3 rounded-sm bg-slate-200"></span> Empty</span>
        </div>

        <div class="grid grid-cols-5 gap-2 max-h-52 overflow-y-auto pr-0.5">
          @if(isset($exam->questions) && count($exam->questions) > 0)
            @foreach($exam->questions as $index => $q)
              <button type="button" @click="currentQ = {{ $index }}"
                      class="aspect-square rounded-xl text-[11px] font-black flex items-center justify-center border-2 transition-all duration-200 cursor-pointer relative"
                      :class="currentQ === {{ $index }}
                        ? 'brand-gradient text-white border-transparent shadow-md shadow-indigo-200'
                        : (answers[{{ $index }}] !== undefined && answers[{{ $index }}] !== ''
                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200'
                            : 'bg-slate-50 text-slate-400 border-slate-200 hover:border-indigo-200 hover:text-indigo-500')">
                {{ sprintf('%02d', $index + 1) }}
                <span x-show="answers[{{ $index }}] !== undefined && answers[{{ $index }}] !== '' && currentQ !== {{ $index }}"
                      class="absolute -top-0.5 -right-0.5 w-2 h-2 bg-emerald-500 rounded-full border border-white"></span>
              </button>
            @endforeach
          @endif
        </div>

        <div class="mt-5 pt-4 border-t border-slate-100">
          <button type="button" @click="confirmOpen = true"
                  class="w-full flex items-center justify-center gap-2 py-3 brand-gradient text-white text-xs font-black rounded-2xl shadow-md shadow-indigo-200 hover:opacity-90 cursor-pointer transition-all">
            <i data-lucide="send" class="w-4 h-4"></i>
            Submit Exam
          </button>
        </div>
      </div>

      <!-- Proctoring panel -->
      <div class="bg-white border border-slate-100 rounded-3xl p-5 shadow-sm flex flex-col gap-4">
        <!-- Status -->
        <div class="flex items-center justify-between">
          <div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Live Proctoring</p>
            <div class="flex items-center gap-2 mt-1">
              <span id="proctorDot" class="w-2 h-2 rounded-full bg-amber-400 live-dot flex-shrink-0"></span>
              <span id="proctorLabel" class="text-xs font-black text-slate-700">Awaiting Verification…</span>
            </div>
          </div>
          <div class="text-[10px] font-black text-slate-400 tabular-nums" x-text="formatTime()"></div>
        </div>

        <!-- Proctor key -->
        <div id="proctorKeyCard" class="p-3.5 bg-amber-50 border border-amber-200 rounded-2xl text-center">
          <p class="text-[9px] font-black text-amber-500 uppercase tracking-widest mb-1">Show to Instructor</p>
          <p id="proctorKeyDisplay" class="text-xl font-black font-mono text-amber-900 tracking-widest">----</p>
        </div>

        <!-- Webcam Stream -->
        <div class="relative aspect-video w-full rounded-2xl overflow-hidden bg-slate-950 border border-slate-800">
          <video id="proctorWebcam" autoplay playsinline muted
                 class="w-full h-full object-cover" style="transform:scaleX(-1)"></video>

          <div id="camOffPlaceholder" class="absolute inset-0 flex flex-col items-center justify-center text-white/40 text-center">
            <i data-lucide="video-off" class="w-7 h-7 mb-2"></i>
            <p class="text-[10px] font-bold uppercase tracking-wider">Camera Off</p>
          </div>

          <div id="recBadge" class="hidden absolute bottom-2 left-2 flex items-center gap-1.5 bg-black/70 backdrop-blur-sm px-2.5 py-1 rounded-lg border border-white/10">
            <span class="w-1.5 h-1.5 rounded-full bg-red-500 live-dot"></span>
            <span class="text-[10px] font-black text-white uppercase tracking-wide">Rec • Live</span>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <button type="button" onclick="startSecureProctorCam()"
                  class="flex items-center justify-center gap-1.5 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white text-[11px] font-black rounded-xl cursor-pointer transition-colors">
            <i data-lucide="video" class="w-3.5 h-3.5"></i> Turn On
          </button>
          <button type="button" onclick="stopSecureProctorCam()"
                  class="flex items-center justify-center gap-1.5 py-2.5 bg-slate-600 hover:bg-slate-700 text-white text-[11px] font-black rounded-xl cursor-pointer transition-colors">
            <i data-lucide="video-off" class="w-3.5 h-3.5"></i> Turn Off
          </button>
        </div>

        <canvas id="frameCompressorCanvas" class="hidden" width="320" height="240"></canvas>
      </div>
    </aside>
  </div>

  <!-- ════ PROCTORING + TIMER SCRIPTS ════ -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>

  <script>
    let activeStream = null, fallbackBroadcastLoop = null;
    let proctorAuthKey = 'PR-' + Math.floor(1000 + Math.random() * 9000);
    let isProctorApproved = false;

    document.getElementById('proctorKeyDisplay').textContent = proctorAuthKey;

    function initStudentHandshakeChannel() {
      window.Pusher = Pusher;
      window.Echo = new Echo({
        broadcaster: 'pusher',
        key: '{{ config('broadcasting.connections.pusher.key') }}',
        cluster: '{{ config('broadcasting.connections.pusher.options.cluster', 'mt1') }}',
        forceTLS: true,
        disableStats: true,
      });

      window.Echo.channel('exam-room-handshake')
        .listen('.ProctorKeyApproved', (data) => {
          if (String(data.proctor_key).trim() === String(proctorAuthKey).trim()) {
            isProctorApproved = true;

            const keyCard = document.getElementById('proctorKeyCard');
            if (keyCard) {
              keyCard.className = 'p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-center';
              keyCard.innerHTML = `<p class="text-[10px] font-black text-emerald-600 flex items-center justify-center gap-1"><i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Instructor Admitted</p>`;
              lucide.createIcons();
            }
            document.getElementById('proctorDot').className = 'w-2 h-2 rounded-full bg-emerald-500 live-dot flex-shrink-0';
            document.getElementById('proctorLabel').textContent = 'Examiner Active';
            document.getElementById('recBadge').classList.replace('hidden', 'flex');
            document.getElementById('camOffPlaceholder').style.display = 'none';
            startStreamBroadcaster(document.getElementById('proctorWebcam'));
          }
        });
    }

    async function startSecureProctorCam() {
      if (activeStream) return;
      try {
        activeStream = await navigator.mediaDevices.getUserMedia({ video: { width: 320, height: 240 }, audio: false });
        const vid = document.getElementById('proctorWebcam');
        vid.srcObject = activeStream;
        document.getElementById('camOffPlaceholder').style.display = 'none';
        registerProctorKeyOnServer();
      } catch (err) {
        alert('Camera access denied. Please allow webcam access and try again.');
      }
    }

    function registerProctorKeyOnServer() {
      fetch('/student/exams/register-proctor-key', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({
          exam_id: '{{ $exam->exam_id ?? "1" }}',
          proctor_key: proctorAuthKey,
          student_id: '{{ Auth::user()->id ?? Auth::user()->user_id ?? "2" }}',
          student_name: '{{ Auth::user()->full_name ?? "Student" }}'
        })
      }).then(r => r.json()).then(() => {
        document.getElementById('proctorLabel').textContent = 'Pending Instructor Admission…';
        startApprovalPolling();
      }).catch(() => {});
    }

    // ✅ FIX: Poll for approval over plain HTTP every 3s as a fallback.
    // The WebSocket listener above (.ProctorKeyApproved) only fires if
    // Pusher is actually configured — by default BROADCAST_CONNECTION=log
    // and the Pusher keys are empty, so that event never reaches the
    // browser. Without this fallback the student stays stuck on "Pending
    // Instructor Admission…" forever, even after the teacher clicks Admit,
    // and no frames ever get sent to the teacher's monitoring grid.
    let approvalPollLoop = null;
    function startApprovalPolling() {
      if (approvalPollLoop) return;
      approvalPollLoop = setInterval(() => {
        if (isProctorApproved) { clearInterval(approvalPollLoop); approvalPollLoop = null; return; }
        fetch('/student/exams/proctor-key-status?proctor_key=' + encodeURIComponent(proctorAuthKey))
          .then(r => r.json())
          .then(data => {
            if (data.status === 'approved' && !isProctorApproved) {
              isProctorApproved = true;
              clearInterval(approvalPollLoop);
              approvalPollLoop = null;

              const keyCard = document.getElementById('proctorKeyCard');
              if (keyCard) {
                keyCard.className = 'p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-center';
                keyCard.innerHTML = `<p class="text-[10px] font-black text-emerald-600 flex items-center justify-center gap-1"><i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Instructor Admitted</p>`;
                lucide.createIcons();
              }
              document.getElementById('proctorDot').className = 'w-2 h-2 rounded-full bg-emerald-500 live-dot flex-shrink-0';
              document.getElementById('proctorLabel').textContent = 'Examiner Active';
              document.getElementById('recBadge').classList.replace('hidden', 'flex');
              document.getElementById('camOffPlaceholder').style.display = 'none';
              startStreamBroadcaster(document.getElementById('proctorWebcam'));
            }
          })
          .catch(() => {});
      }, 3000);
    }

    function stopSecureProctorCam() {
      if (fallbackBroadcastLoop) { clearInterval(fallbackBroadcastLoop); fallbackBroadcastLoop = null; }
      if (approvalPollLoop) { clearInterval(approvalPollLoop); approvalPollLoop = null; }
      if (activeStream) { activeStream.getTracks().forEach(t => t.stop()); activeStream = null; }
      document.getElementById('proctorWebcam').srcObject = null;
      document.getElementById('camOffPlaceholder').style.display = 'flex';
      document.getElementById('proctorDot').className = 'w-2 h-2 rounded-full bg-slate-400 flex-shrink-0';
      document.getElementById('proctorLabel').textContent = 'Camera Offline';
      document.getElementById('recBadge').classList.replace('flex', 'hidden');
    }

    function startStreamBroadcaster(video) {
      if (fallbackBroadcastLoop) clearInterval(fallbackBroadcastLoop);
      const canvas = document.getElementById('frameCompressorCanvas');
      const ctx = canvas.getContext('2d');
      fallbackBroadcastLoop = setInterval(() => {
        if (!isProctorApproved || !activeStream || video.paused || video.ended) return;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        fetch('/student/exams/stream-frame', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
          body: JSON.stringify({
            exam_id: '{{ $exam->exam_id ?? "1" }}',
            image_frame: canvas.toDataURL('image/jpeg', 0.40),
            proctor_key: proctorAuthKey,
            student_id: '{{ Auth::user()->id ?? Auth::user()->user_id ?? "2" }}'
          })
        }).catch(e => console.debug(e));
      }, 3000);
    }

    window.addEventListener('DOMContentLoaded', () => {
      initStudentHandshakeChannel();
      startSecureProctorCam();
      lucide.createIcons();
    });
  </script>

  <!-- ════ ALPINE APP ════ -->
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('examApp', () => ({
        currentQ: 0,
        totalQ: {{ count($exam->questions ?? []) > 0 ? count($exam->questions) : 1 }},
        answers: {},
        secondsLeft: {{ $secondsRemaining ?? 3600 }},
        confirmOpen: false,
        tabWarning: false,
        tabSwitchCount: 0,
        saving: false,
        _timerRef: null,
        _tabTimeout: null,

        // ── Live exam rules (admin-controlled, refreshed on a poll so mid-exam
        //    changes from the admin settings panel apply without a reload) ──
        proctorMaxSwitches: {{ $examRules['proctorMaxSwitches'] ?? 3 }},
        proctorWarnThreshold: {{ $examRules['proctorWarnThreshold'] ?? 2 }},
        blockRightClick: {{ ($examRules['blockRightClick'] ?? true) ? 'true' : 'false' }},
        forceFullscreen: {{ ($examRules['forceFullscreen'] ?? true) ? 'true' : 'false' }},
        syncInterval: {{ $examRules['syncInterval'] ?? 10 }},
        disqualified: false,
        _rulesPollRef: null,

        get answeredCount() {
          return Object.values(this.answers).filter(v => v !== undefined && v !== '').length;
        },

        get unansweredList() {
          const list = [];
          for (let i = 0; i < this.totalQ; i++) {
            if (!this.answers[i] || this.answers[i] === '') list.push(i + 1);
          }
          return list;
        },

        setAnswer(index, value) {
          this.answers[index] = value;
          this.saving = true;
          clearTimeout(this._saveTimeout);
          this._saveTimeout = setTimeout(() => { this.saving = false; lucide.createIcons(); }, 1200);
          lucide.createIcons();
        },

        formatTime() {
          const h = Math.floor(this.secondsLeft / 3600);
          const m = Math.floor((this.secondsLeft % 3600) / 60);
          const s = this.secondsLeft % 60;
          return [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        },

        startTimer() {
          this._timerRef = setInterval(() => {
            if (this.secondsLeft <= 0) {
              clearInterval(this._timerRef);
              window.examSubmittedBySystem = true;
              this.$refs.examForm.submit();
            } else {
              this.secondsLeft--;
            }
          }, 1000);
        },

        setupTabDetection() {
          document.addEventListener('visibilitychange', () => {
            if (document.hidden && !this.disqualified) {
              this.tabSwitchCount++;
              this.tabWarning = true;
              if (this._tabTimeout) clearTimeout(this._tabTimeout);
              this._tabTimeout = setTimeout(() => { this.tabWarning = false; }, 4000);
              lucide.createIcons();

              // Real-time: send every violation to the server immediately so it
              // shows up on the admin's live security/threat feed right away.
              fetch('{{ route("student.exams.logViolation") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({
                  exam_id: '{{ $exam->exam_id ?? "1" }}',
                  strike: this.tabSwitchCount
                })
              }).catch(e => console.debug(e));

              // Enforce the admin's live rule: past the allowed limit, the
              // exam auto-submits and the student is locked out.
              if (this.tabSwitchCount > this.proctorMaxSwitches) {
                this.disqualified = true;
                window.examSubmittedBySystem = true;
                if (this._timerRef) clearInterval(this._timerRef);
                this.$refs.examForm.submit();
              }
            }
          });
        },

        setupIntegrityEnforcement() {
          document.addEventListener('contextmenu', (e) => { if (this.blockRightClick) e.preventDefault(); });
          ['copy', 'cut', 'paste'].forEach(evt => {
            document.addEventListener(evt, (e) => { if (this.blockRightClick) e.preventDefault(); });
          });

          if (this.forceFullscreen && document.documentElement.requestFullscreen) {
            document.documentElement.requestFullscreen().catch(() => {});
            document.addEventListener('fullscreenchange', () => {
              if (!document.fullscreenElement && this.forceFullscreen && !this.disqualified) {
                this.tabSwitchCount++;
                this.tabWarning = true;
                fetch('{{ route("student.exams.logViolation") }}', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                  body: JSON.stringify({ exam_id: '{{ $exam->exam_id ?? "1" }}', strike: this.tabSwitchCount })
                }).catch(e => console.debug(e));
              }
            });
          }
        },

        // Poll the admin's live rule settings so a change made mid-exam
        // (e.g. lowering max switches) applies immediately, no reload needed.
        pollLiveRules() {
          const refresh = () => {
            fetch('{{ route("exam.rules.live") }}')
              .then(r => r.json())
              .then(rules => {
                this.proctorMaxSwitches   = rules.proctor_max_switches;
                this.proctorWarnThreshold = rules.proctor_warn_threshold;
                this.blockRightClick      = rules.block_right_click;
                this.forceFullscreen      = rules.force_fullscreen;
              })
              .catch(e => console.debug(e));
          };
          refresh();
          this._rulesPollRef = setInterval(refresh, Math.max(this.syncInterval, 5) * 1000);
        },

        init() {
          this.startTimer();
          this.setupTabDetection();
          this.setupIntegrityEnforcement();
          this.pollLiveRules();
          lucide.createIcons();
        }
      }));
    });
  </script>
</body>
</html>