<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Examination Hall Ticket - {{ $user->institutional_id ?? '88291' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { background: white; color: black; }
            .no-print { display: none !important; }
            .print-border { border: 2px dashed #000 !important; }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans p-4 sm:p-8 text-slate-800">

    <div class="no-print max-w-2xl mx-auto mb-6 flex justify-between items-center bg-white p-4 rounded-xl shadow-sm border border-slate-200">
        <span class="text-sm text-slate-500 font-medium">📄 System Print Engine Document Preview</span>
        <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold uppercase tracking-wider rounded-lg shadow transition">Print Document</button>
    </div>

    <div class="max-w-2xl mx-auto bg-white border border-slate-300 rounded-2xl shadow-md p-8 relative print-border">
        <div class="absolute top-0 right-0 bg-slate-900 text-white font-mono text-[9px] tracking-widest px-3 py-1 uppercase rounded-bl-xl rounded-tr-xl">Secure Token Verified</div>
        
        <div class="flex items-center gap-4 border-b pb-6 mb-6 border-slate-200">
            <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white text-xl font-bold">S</div>
            <div>
                <h1 class="text-xl font-black text-slate-900 uppercase tracking-tight">ExamSystem Institutional Matrix</h1>
                <p class="text-xs font-bold text-indigo-600 tracking-wider uppercase mt-0.5">Official Verification Entry Hall Ticket</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 items-start">
            <div class="w-32 h-32 bg-slate-100 rounded-xl border border-slate-200 overflow-hidden flex flex-col items-center justify-center text-center font-bold text-slate-400 p-2 text-xs">
                <span class="text-2xl text-slate-300 mb-1">👤</span> Candidate Passport Photograph
            </div>
            <div class="sm:col-span-2 space-y-3">
                <div><label class="text-[10px] font-mono tracking-widest text-slate-400 uppercase">Full Legal Candidate Name</label><p class="text-base font-bold text-slate-900 mt-0.5">{{ $user->full_name ?? 'Jane Doe' }}</p></div>
                <div><label class="text-[10px] font-mono tracking-widest text-slate-400 uppercase">Institutional Tracker Identifier (ID)</label><p class="text-sm font-mono font-bold text-slate-700 mt-0.5">{{ $user->institutional_id ?? 'STU-88291-LDN' }}</p></div>
                <div><label class="text-[10px] font-mono tracking-widest text-slate-400 uppercase">Registered Security Account Email</label><p class="text-sm font-medium text-slate-600 mt-0.5">{{ $user->email ?? 'jane.doe@university.edu' }}</p></div>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-xs font-bold tracking-widest text-slate-400 uppercase mb-3">Authorized Active Evaluation Stream</h3>
            <table class="w-full text-left text-xs border border-slate-200 rounded-lg overflow-hidden">
                <thead><tr class="bg-slate-50 text-slate-500 font-bold uppercase border-b border-slate-200"><th class="p-3">Course Code</th><th class="p-3">Examination Target Title</th><th class="p-3 text-right">Testing Windows</th></tr></thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    <tr><td class="p-3 font-mono text-indigo-600">ENG105 / STATS</td><td class="p-3 text-slate-900">Advanced Probability & Statistics Live Session</td><td class="p-3 text-right text-slate-600">45 MIN Live Allocation Route</td></tr>
                </tbody>
            </table>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100 space-y-4">
            <div class="p-4 bg-slate-50 border border-slate-200/60 rounded-xl text-[11px] leading-relaxed text-slate-500">
                <strong>Mandatory Proctoring Instructions:</strong> Candidates must present this token interface document to their environmental camera feed during the entry stage checks. Hardware components including webcams and active microphonic trackers must remain online constantly.
            </div>
            <div class="flex justify-between items-center text-[9px] font-mono text-slate-400 uppercase tracking-wider"><span>Generated: {{ date('Y-m-d H:i:s T') }}</span><span>Digital Sign: {{ md5($user->email ?? 'verify') }}</span></div>
        </div>
    </div>
</body>
</html>