<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Request Submitted Successfully</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex items-center justify-center p-4 selection:bg-blue-500/20">

    <div class="w-full max-w-5xl bg-white border border-[#E2E8F0] rounded-3xl p-8 md:p-12 shadow-xl space-y-8 relative">
        
        <!-- Header Banner -->
        <div class="text-center space-y-4">
            <div class="w-16 h-16 bg-[#10B981] rounded-full flex items-center justify-center text-white text-2xl mx-auto shadow-lg shadow-emerald-500/20">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-[#0F172A] tracking-tight">Request Submitted Successfully</h2>
            <p class="text-sm text-[#64748B] max-w-xl mx-auto leading-relaxed">
                Your support request has been received. Our academic assistance team will review your ticket and get back to you shortly.[cite: 3]
            </p>
        </div>

        <!-- Meta Summary Grid -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-8 text-left items-stretch">
            <div class="md:col-span-3 bg-[#F8FAFC] border border-[#E2E8F0] p-6 rounded-2xl flex flex-col justify-between space-y-6 shadow-sm">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fa-solid fa-ticket text-[#1D4ED8] text-sm"></i>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Ticket ID</span>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-md uppercase tracking-wide">Pending Review</span>
                </div>
                
                <h4 class="text-3xl font-black text-[#1D4ED8] tracking-widest font-mono">
                    {{ $ticket->ticket_no }}
                </h4>
                
                <div class="space-y-2.5 pt-4 border-t border-slate-200/60 text-sm font-medium">
                    <div class="flex justify-between items-center gap-4">
                        <span class="text-slate-400 flex-shrink-0">Subject:</span>
                        <span class="text-slate-800 font-bold truncate max-w-xs text-right">{{ $ticket->subject }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Submitted Time:</span>
                        <span class="text-slate-600 font-semibold">{{ $ticket->created_at }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400">Urgency:</span>
                        <span class="text-red-600 font-bold uppercase tracking-wide text-xs bg-red-50 border border-red-100 px-2 py-0.5 rounded">{{ $ticket->urgency }}</span>
                    </div>
                </div>
            </div>

            <!-- Estimated response information layout card modules -->
            <div class="md:col-span-2 bg-white border border-[#E2E8F0] p-6 rounded-2xl flex flex-col justify-between space-y-6 shadow-sm relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 text-slate-50/60 -mr-4 -mt-4 pointer-events-none">
                    <i class="fa-regular fa-clock text-8xl"></i>
                </div>
                <div>
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Estimated Response</span>
                    <h4 class="text-xl font-extrabold text-[#0F172A] mt-1">Within 24 hours</h4>
                    <p class="text-xs text-slate-400 leading-relaxed mt-2">
                        Our support specialists are operating at peak capacity but strive for prompt same-day resolution metrics.[cite: 3]
                    </p>
                </div>
                <div class="space-y-2">
                    <span class="text-[11px] font-bold text-slate-400 block uppercase tracking-wide">While you wait...</span>
                    <div class="text-xs font-bold text-[#1D4ED8] space-y-2">
                        <a href="#" class="flex items-center gap-2 text-slate-700 hover:text-blue-600 transition-colors"><i class="fa-solid fa-circle-info text-blue-600 w-4 text-center"></i> Check Technical FAQ</a>
                        <a href="#" class="flex items-center gap-2 text-slate-700 hover:text-indigo-600 transition-colors"><i class="fa-solid fa-book-open text-indigo-600 w-4 text-center"></i> Review Exam Guidelines</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Buttons -->
        <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4 border-t border-slate-100">
            <a href="{{ route('student.support') }}" class="w-full sm:w-auto px-8 py-3.5 text-center text-sm font-bold text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-xl transition-all">
                Back to Support
            </a>
            <a href="{{ route('student.support') }}" class="w-full sm:w-auto px-8 py-3.5 text-center text-sm font-bold text-white bg-[#1D4ED8] hover:bg-blue-800 rounded-xl shadow-md transition-all">
                View My Requests
            </a>
        </div>
    </div>

    <!-- Live Local Storage Real-Time Connector Loop -->
    <script>
        (function processTicketPersistenceStream() {
            const ticketID = "{{ $ticket->ticket_no }}";
            const subject = "{{ $ticket->subject }}";
            const description = `{!! addslashes($ticket->description) !!}`;
            const screenshot = "{{ $ticket->screenshot }}";

            const newTicket = {
                ticket_id: ticketID,
                reporter_name: "You Phatyuth",
                reporter_email: "student1@examsystem.com",
                user_type: "student",
                issue_category: subject,
                description: description,
                priority: "high",
                status: "pending",
                screenshot: screenshot,
                admin_comment: ""
            };

            let existingTickets = JSON.parse(localStorage.getItem('exam_system_tickets') || '[]');
            
            // Deduplicate to avoid adding duplicate tickets on refreshing the page
            if (!existingTickets.some(t => t.ticket_id === ticketID)) {
                existingTickets.unshift(newTicket);
                localStorage.setItem('exam_system_tickets', JSON.stringify(existingTickets));
            }
        })();
    </script>
</body>
</html>