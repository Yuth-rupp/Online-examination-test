<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>ExamSystem - Teacher Support</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <script>
    (function () {
      if (localStorage.getItem('darkMode') === 'true') {
        document.documentElement.classList.add('dark');
      }
    })();
  </script>

  <script src="https://cdn.tailwindcss.com"></script>
  <script>tailwind.config = { darkMode: 'class' }</script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <style>
    * { box-sizing: border-box; }
    body { font-family: 'Inter', sans-serif; }
    .form-input { transition: all .2s ease; }
    .form-input:focus { outline: none; border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,.12); }
    .drop-zone { border: 2px dashed #E2E8F0; transition: all .2s ease; }
    .drop-zone.hover { border-color: #2563EB; background: rgba(37,99,235,.04); }
    .toast { animation: toastIn .3s ease, toastOut .3s ease 3.7s forwards; }
    @keyframes toastIn { from { opacity:0; transform: translateY(16px);} to { opacity:1; transform: translateY(0);} }
    @keyframes toastOut { from { opacity:1; } to { opacity:0; } }
    ::-webkit-scrollbar { width: 5px; }
    ::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 99px; }
  </style>
</head>
<body class="bg-[#F8FAFC] dark:bg-slate-950">

<div class="flex min-h-screen">

  @include('partials.teacher-sidebar')

  <main class="flex-1 min-h-screen flex flex-col">

    <header class="h-[72px] flex items-center justify-between px-8 border-b border-[#E2E8F0] bg-white sticky top-0 z-20">
      <div>
        <h1 class="text-[19px] font-extrabold text-[#0F172A]">Support &amp; Help</h1>
        <p class="text-[12.5px] text-[#64748B]">Report a problem — it goes straight to the admin team.</p>
      </div>
    </header>

    <div class="p-7 max-w-5xl w-full mx-auto space-y-7">

      @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 text-[13.5px] font-medium rounded-xl px-4 py-3 flex items-center gap-2">
          <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
      @endif

      <!-- Submit new ticket -->
      <section class="bg-white border border-[#E2E8F0] rounded-2xl p-6">
        <h2 class="text-[15px] font-bold text-[#0F172A] mb-1">Report a Problem</h2>
        <p class="text-[12.5px] text-[#64748B] mb-5">Describe the issue clearly — admin will review and respond here.</p>

        <form action="{{ route('teacher.support.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
          @csrf

          <div>
            <label class="text-[12.5px] font-semibold text-[#334155] mb-1.5 block">Subject</label>
            <select name="subject" required
              class="form-input w-full border border-[#E2E8F0] rounded-xl px-3.5 py-2.5 text-[13.5px] bg-white">
              <option value="">Select an issue category</option>
              <option>Grading tools not working</option>
              <option>Monitoring / proctoring issue</option>
              <option>Question bank problem</option>
              <option>Course / roster issue</option>
              <option>Account or login problem</option>
              <option>Other</option>
            </select>
          </div>

          <div>
            <label class="text-[12.5px] font-semibold text-[#334155] mb-1.5 block">Priority</label>
            <select name="priority"
              class="form-input w-full border border-[#E2E8F0] rounded-xl px-3.5 py-2.5 text-[13.5px] bg-white">
              <option value="high" selected>High</option>
              <option value="medium">Medium</option>
              <option value="low">Low</option>
            </select>
          </div>

          <div>
            <label class="text-[12.5px] font-semibold text-[#334155] mb-1.5 block">Description</label>
            <textarea name="description" rows="4" required
              class="form-input w-full border border-[#E2E8F0] rounded-xl px-3.5 py-2.5 text-[13.5px]"
              placeholder="What happened? Include the exam/course name if relevant."></textarea>
          </div>

          <div>
            <label class="text-[12.5px] font-semibold text-[#334155] mb-1.5 block">Screenshot (optional)</label>
            <label id="dropZone" class="drop-zone rounded-xl flex flex-col items-center justify-center py-8 cursor-pointer">
              <i class="fa-solid fa-cloud-arrow-up text-[22px] text-[#94A3B8] mb-2"></i>
              <span class="text-[12.5px] text-[#64748B]" id="dropZoneText">Click to upload or drag an image here</span>
              <input type="file" name="screenshot" id="screenshotInput" accept="image/*" class="hidden">
            </label>
          </div>

          <button type="submit"
            class="w-full py-3 rounded-xl text-white font-bold text-[13.5px]"
            style="background:linear-gradient(135deg,#2563EB 0%,#1D4ED8 100%);">
            <i class="fa-solid fa-paper-plane mr-1.5"></i> Submit to Admin
          </button>
        </form>
      </section>

      <!-- Ticket history -->
      <section class="bg-white border border-[#E2E8F0] rounded-2xl p-6">
        <h2 class="text-[15px] font-bold text-[#0F172A] mb-5">Your Tickets</h2>

        @if($tickets->isEmpty())
          <p class="text-[13px] text-[#94A3B8] text-center py-8">No tickets submitted yet.</p>
        @else
          <div class="space-y-3">
            @foreach($tickets as $t)
              <div class="border border-[#E2E8F0] rounded-xl p-4">
                <div class="flex items-center justify-between mb-1.5">
                  <span class="text-[13px] font-bold text-[#0F172A]">{{ $t['subject'] }}</span>
                  @php
                    $badge = match($t['status']) {
                      'PENDING' => 'bg-amber-50 text-amber-700 border-amber-200',
                      'INVESTIGATING' => 'bg-blue-50 text-blue-700 border-blue-200',
                      'RESOLVED' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                      default => 'bg-slate-50 text-slate-700 border-slate-200',
                    };
                  @endphp
                  <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full border {{ $badge }}">{{ $t['status'] }}</span>
                </div>
                <p class="text-[12.5px] text-[#64748B] mb-2">{{ $t['description'] }}</p>
                <p class="text-[11px] text-[#94A3B8] mb-2">Ticket {{ $t['ticket_no'] }} · Updated {{ $t['updated_at'] }}</p>

                @if($t['admin_comment'])
                  <div class="mt-2 bg-[#F8FAFC] border border-[#E2E8F0] rounded-lg p-3">
                    <p class="text-[11px] font-bold text-[#334155] mb-1"><i class="fa-solid fa-reply mr-1"></i> Admin reply</p>
                    <p class="text-[12.5px] text-[#475569]">{{ $t['admin_comment'] }}</p>
                  </div>
                @endif
              </div>
            @endforeach
          </div>
        @endif
      </section>

    </div>
  </main>
</div>

<script>
  const dropZone = document.getElementById('dropZone');
  const input = document.getElementById('screenshotInput');
  const text = document.getElementById('dropZoneText');

  input.addEventListener('change', () => {
    if (input.files[0]) text.textContent = input.files[0].name;
  });

  ['dragover', 'dragleave', 'drop'].forEach(evt => {
    dropZone.addEventListener(evt, e => {
      e.preventDefault();
      dropZone.classList.toggle('hover', evt === 'dragover');
      if (evt === 'drop' && e.dataTransfer.files[0]) {
        input.files = e.dataTransfer.files;
        text.textContent = e.dataTransfer.files[0].name;
      }
    });
  });

  // Poll every 20s for tickets admin has resolved since page load
  setInterval(async () => {
    try {
      const res = await fetch("{{ route('teacher.support.notifications') }}");
      const data = await res.json();
      if (data.count > 0) {
        // simple reload keeps status badges accurate; swap for a toast if preferred
        // location.reload();
      }
    } catch (e) { /* silent */ }
  }, 20000);
</script>

</body>
</html>
