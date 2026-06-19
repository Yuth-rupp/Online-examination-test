<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Create Course</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-md bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden animate-fadeIn">
        <div class="p-6 border-b border-[#E2E8F0] bg-slate-50/50 flex items-center gap-3">
            <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center text-[#1D4ED8]">
                <i class="fa-solid fa-book-bookmark text-sm"></i>
            </div>
            <div>
                <h3 class="font-bold text-[#0F172A] text-lg">Create New Course</h3>
                <p class="text-xs text-[#64748B] mt-0.5">Add a new curriculum subject to your workspace portal.</p>
            </div>
        </div>

        <form action="{{ route('teacher.courses.store') }}" method="POST" class="p-6 space-y-5">
            @csrf
            
            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Course Name</label>
                <input type="text" name="name" placeholder="e.g., Introduction to Physics" required class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium">
            </div>

            <div class="p-3.5 bg-blue-50/60 rounded-xl border border-blue-100 flex items-start gap-2.5 text-[#1D4ED8]">
                <i class="fa-solid fa-wand-magic-sparkles text-sm mt-0.5"></i>
                <p class="text-xs font-medium leading-relaxed">
                    <strong>Smart Automation:</strong> The system will automatically compile a distinct curriculum shortcode (e.g., IP-482) based on your course name.
                </p>
            </div>

            <div class="space-y-1.5">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Description (Optional)</label>
                <textarea name="description" placeholder="Brief overview of course modules..." rows="3" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium"></textarea>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <a href="{{ route('teacher.dashboard') }}" class="w-1/2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-center font-bold text-sm py-3 px-4 rounded-xl transition-all">Cancel</a>
                <button type="submit" class="w-1/2 bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-sm py-3 px-4 rounded-xl shadow-sm transition-all transform active:scale-[0.98]">
                    Save Course
                </button>
            </div>
        </form>
    </div>

</body>
</html>