<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Hall Ticket — {{ $user->full_name }}</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@500;700&display=swap" rel="stylesheet">
  <script src="https://unpkg.com/lucide@latest"></script>

  <style>
    *, *::before, *::after { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    .mono { font-family: 'JetBrains Mono', monospace; }

    .brand-grad { background: linear-gradient(135deg,#4F6EF7,#7C3AED); }

    /* Dashed tear-line between stub and main card */
    .tear-line {
      background-image: linear-gradient(to bottom, transparent 0, transparent 50%, #CBD5E1 50%, #CBD5E1 100%);
      background-size: 2px 10px;
      background-repeat: repeat-y;
    }

    /* Watermark pattern */
    .watermark {
      background-image: repeating-linear-gradient(-45deg, rgba(79,110,247,0.035) 0, rgba(79,110,247,0.035) 1px, transparent 1px, transparent 26px);
    }

    @media print {
      @page { size: A4; margin: 10mm; }
      body { background: white !important; }
      .no-print { display: none !important; }
      .print-card { box-shadow: none !important; border: 1px solid #E2E8F0 !important; }
    }
  </style>
</head>

<body class="min-h-screen bg-slate-100 py-10 px-4">

  <!-- Toolbar -->
  <div class="no-print max-w-3xl mx-auto mb-5 flex items-center justify-between">
    <a href="{{ route('student.dashboard') }}" class="flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">
      <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Back to Dashboard
    </a>
    <button onclick="window.print()"
            class="flex items-center gap-2 px-5 py-2.5 brand-grad text-white text-xs font-black rounded-xl shadow-lg shadow-indigo-200 hover:opacity-90 transition-opacity cursor-pointer">
      <i data-lucide="printer" class="w-3.5 h-3.5"></i> Print Hall Ticket
    </button>
  </div>

  <!-- ════ HALL TICKET CARD ════ -->
  <div class="print-card max-w-3xl mx-auto bg-white rounded-3xl shadow-xl shadow-slate-200/60 overflow-hidden watermark">

    <!-- Header band -->
    <div class="brand-grad px-8 py-6 flex items-center justify-between">
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center flex-shrink-0">
          @if($institution && $institution->logo)
            <img src="{{ Storage::url($institution->logo) }}" class="w-7 h-7 object-contain">
          @else
            <i data-lucide="graduation-cap" class="w-6 h-6 text-white"></i>
          @endif
        </div>
        <div>
          <p class="text-white font-black text-sm leading-tight">{{ $institution->name ?? '{{ $platformName }} Academy' }}</p>
          <p class="text-indigo-100 text-[10px] font-bold uppercase tracking-widest">Examination Hall Ticket</p>
        </div>
      </div>
      <div class="text-right">
        <p class="text-indigo-100 text-[9px] font-black uppercase tracking-widest mb-0.5">Ticket No.</p>
        <p class="text-white font-black text-sm mono">{{ $ticketNo }}</p>
      </div>
    </div>

    <!-- Body -->
    <div class="px-8 py-7">

      <div class="flex items-start gap-6 mb-7">
        <!-- Photo -->
        <div class="w-24 h-28 rounded-xl overflow-hidden border-2 border-slate-200 flex-shrink-0 shadow-sm">
          @if($user->profile_image)
            <img src="{{ Storage::url($user->profile_image) }}" class="w-full h-full object-cover">
          @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center">
              <span class="text-2xl font-black text-indigo-400 uppercase">{{ strtoupper(substr($user->full_name,0,2)) }}</span>
            </div>
          @endif
        </div>

        <!-- Student details -->
        <div class="flex-1 grid grid-cols-2 gap-x-6 gap-y-3.5">
          <div class="col-span-2">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Candidate Name</p>
            <p class="text-lg font-black text-slate-900 leading-tight">{{ $user->full_name }}</p>
          </div>
          <div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Student ID</p>
            <p class="text-sm font-black text-slate-800 mono">{{ $user->institutional_id ?? ('STU-' . str_pad($user->user_id, 5, '0', STR_PAD_LEFT)) }}</p>
          </div>
          <div>
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Email</p>
            <p class="text-sm font-bold text-slate-700 truncate">{{ $user->email }}</p>
          </div>
        </div>
      </div>

      <!-- Exam schedule table -->
      <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2.5">Scheduled Examinations</p>

      @if($exams->count())
        <div class="border border-slate-200 rounded-2xl overflow-hidden mb-6">
          <table class="w-full text-left">
            <thead>
              <tr class="bg-slate-50 border-b border-slate-200">
                <th class="px-4 py-2.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Course / Exam</th>
                <th class="px-4 py-2.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Date</th>
                <th class="px-4 py-2.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Time</th>
                <th class="px-4 py-2.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Duration</th>
                <th class="px-4 py-2.5 text-[9px] font-black text-slate-400 uppercase tracking-widest">Access Code</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
              @foreach($exams as $exam)
                <tr>
                  <td class="px-4 py-3">
                    <p class="text-xs font-black text-slate-900 leading-tight">{{ $exam->title }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">{{ $exam->course->name ?? '—' }}</p>
                  </td>
                  <td class="px-4 py-3 text-xs font-bold text-slate-700">
                    {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('M d, Y') : '—' }}
                  </td>
                  <td class="px-4 py-3 text-xs font-bold text-slate-700">
                    {{ $exam->start_time ? \Carbon\Carbon::parse($exam->start_time)->format('h:i A') : '—' }}
                  </td>
                  <td class="px-4 py-3 text-xs font-bold text-slate-700">
                    {{ $exam->duration ? $exam->duration . ' min' : '—' }}
                  </td>
                  <td class="px-4 py-3">
                    <span class="mono text-xs font-black text-indigo-600 bg-indigo-50 px-2 py-1 rounded-lg">{{ $exam->access_code }}</span>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="border border-dashed border-slate-200 rounded-2xl px-4 py-6 text-center mb-6">
          <p class="text-xs font-bold text-slate-400">No upcoming exams scheduled at this time.</p>
        </div>
      @endif

      <!-- Instructions -->
      <div class="bg-amber-50 border border-amber-100 rounded-2xl px-5 py-4 mb-7">
        <p class="text-[10px] font-black text-amber-700 uppercase tracking-widest mb-2 flex items-center gap-1.5">
          <i data-lucide="info" class="w-3.5 h-3.5"></i> Entry Instructions
        </p>
        <ul class="space-y-1.5 text-[11px] text-amber-800 font-medium leading-relaxed list-disc list-inside">
          <li>Carry this hall ticket along with a valid photo ID to the examination venue.</li>
          <li>Arrive at least 15 minutes before the scheduled start time.</li>
          <li>Enter the access code shown above to unlock your exam session.</li>
          <li>Electronic devices other than the testing device are not permitted.</li>
        </ul>
      </div>

      <!-- Signatures -->
      <div class="grid grid-cols-2 gap-8 pt-2">
        <div>
          <div class="h-10 border-b border-slate-300"></div>
          <p class="text-[10px] font-bold text-slate-400 mt-1.5">Candidate&apos;s Signature</p>
        </div>
        <div>
          <div class="h-10 border-b border-slate-300"></div>
          <p class="text-[10px] font-bold text-slate-400 mt-1.5">Invigilator&apos;s Signature</p>
        </div>
      </div>
    </div>

    <!-- Tear-off stub -->
    <div class="tear-line relative">
      <div class="absolute -left-3 -top-3 w-6 h-6 bg-slate-100 rounded-full print-card:bg-white"></div>
      <div class="absolute -right-3 -top-3 w-6 h-6 bg-slate-100 rounded-full print-card:bg-white"></div>
    </div>

    <div class="px-8 py-4 bg-slate-50 flex items-center justify-between">
      <div class="flex items-center gap-2">
        <i data-lucide="shield-check" class="w-3.5 h-3.5 text-slate-400"></i>
        <p class="text-[9px] font-mono text-slate-400">VERIFY: {{ substr(md5($user->email . $ticketNo), 0, 20) }}…</p>
      </div>
      <p class="text-[9px] font-bold text-slate-400">Issued {{ now()->format('M d, Y') }}</p>
    </div>
  </div>

  <script>
    window.addEventListener('DOMContentLoaded', () => lucide.createIcons());
  </script>
</body>
</html>