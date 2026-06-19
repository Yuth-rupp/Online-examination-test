<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem - Student Support Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-[#F8FAFC] text-[#1E293B] min-h-screen flex selection:bg-blue-500/20 relative overflow-x-hidden flex-row">

    <aside class="w-64 bg-white border-r border-[#E2E8F0] flex flex-col justify-between flex-shrink-0 z-20">
        <div>
            <div class="h-20 flex items-center px-6 gap-2.5">
                <div class="w-9 h-9 bg-[#1D4ED8] rounded-xl flex items-center justify-center text-white shadow-sm">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span class="font-bold text-xl text-[#0F172A] tracking-tight">ExamSystem</span>
            </div>

            <nav class="px-4 py-2 space-y-1">
                <a href="{{ route('student.dashboard') }}" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium rounded-xl transition-all">
                    <i class="fa-solid fa-table-columns w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B]"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium rounded-xl transition-all">
                    <i class="fa-regular fa-file-lines w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B]"></i>
                    <span>Exams</span>
                </a>
                <a href="#" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium rounded-xl transition-all">
                    <i class="fa-solid fa-history w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B]"></i>
                    <span>History</span>
                </a>
                <a href="{{ route('student.support') }}" class="group flex items-center gap-3 px-4 py-3 bg-[#1D4ED8] text-white font-semibold shadow-md shadow-blue-500/10 rounded-xl transition-all">
                    <i class="fa-solid fa-circle-question w-5 text-center text-lg text-white"></i>
                    <span>Support</span>
                </a>
                <a href="#" class="group flex items-center gap-3 px-4 py-3 text-[#64748B] hover:text-[#1E293B] hover:bg-slate-50 font-medium rounded-xl transition-all">
                    <i class="fa-solid fa-gear w-5 text-center text-lg text-[#64748B] group-hover:text-[#1E293B]"></i>
                    <span>Settings</span>
                </a>
            </nav>
        </div>

        <div class="p-4 border-t border-[#E2E8F0] flex items-center gap-3 bg-[#F8FAFC] m-4 rounded-xl">
            <div class="w-10 h-10 rounded-full bg-[#1D4ED8] text-white font-bold flex items-center justify-center border border-gray-200">
                {{ strtoupper(substr(Auth::user()->full_name ?? 'YP', 0, 2)) }}
            </div>
            <div>
                <h4 class="text-sm font-bold text-[#0F172A] leading-tight">{{ Auth::user()->full_name ?? 'You Phatyuth' }}</h4>
                <p class="text-xs text-[#94A3B8] font-medium mt-0.5">Student Account</p>
            </div>
        </div>
    </aside>

    <main class="flex-1 flex flex-col min-w-0 max-w-[1400px] mx-auto w-full p-8 space-y-8 overflow-y-auto">
        <div>
            <h1 class="text-3xl font-extrabold text-[#0F172A]">Support & Help</h1>
            <p class="text-sm text-[#64748B] mt-1">Get help and resolve your issues with institutional precision.</p>
        </div>

        <div class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div id="card-faq" onclick="toggleKnowledgeSection('faq')" class="bg-white border-2 border-transparent p-6 rounded-2xl shadow-xs hover:border-blue-500/50 hover:shadow-sm cursor-pointer transition-all group relative">
                    <div id="icon-faq" class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center text-lg font-bold group-hover:bg-[#1D4ED8] group-hover:text-white transition-all">
                        <i class="fa-solid fa-circle-question"></i>
                    </div>
                    <h3 class="font-bold text-lg text-[#0F172A] mt-4">FAQ</h3>
                    <p class="text-xs text-[#64748B] mt-1.5 leading-relaxed">Most common student system verification questions answered instantly.</p>
                    <div id="indicator-faq" class="absolute bottom-0 left-6 right-6 h-1 bg-[#1D4ED8] rounded-t-full hidden"></div>
                </div>

                <div id="card-guidelines" onclick="toggleKnowledgeSection('guidelines')" class="bg-white border-2 border-transparent p-6 rounded-2xl shadow-xs hover:border-blue-500/50 hover:shadow-sm cursor-pointer transition-all group relative">
                    <div id="icon-guidelines" class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center text-lg font-bold group-hover:bg-indigo-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-book-open"></i>
                    </div>
                    <h3 class="font-bold text-lg text-[#0F172A] mt-4">Exam Guidelines</h3>
                    <p class="text-xs text-[#64748B] mt-1.5 leading-relaxed">Official institution instructions for upcoming midterm and final testing.</p>
                    <div id="indicator-guidelines" class="absolute bottom-0 left-6 right-6 h-1 bg-indigo-600 rounded-t-full hidden"></div>
                </div>

                <div id="card-proctoring" onclick="toggleKnowledgeSection('proctoring')" class="bg-white border-2 border-transparent p-6 rounded-2xl shadow-xs hover:border-blue-500/50 hover:shadow-sm cursor-pointer transition-all group relative">
                    <div id="icon-proctoring" class="w-10 h-10 bg-purple-50 text-purple-600 rounded-xl flex items-center justify-center text-lg font-bold group-hover:bg-purple-600 group-hover:text-white transition-all">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <h3 class="font-bold text-lg text-[#0F172A] mt-4">Proctoring Rules</h3>
                    <p class="text-xs text-[#64748B] mt-1.5 leading-relaxed">Webcam verification tracking algorithms setup and canvas policies guidelines.</p>
                    <div id="indicator-proctoring" class="absolute bottom-0 left-6 right-6 h-1 bg-purple-600 rounded-t-full hidden"></div>
                </div>
            </div>

            <div id="knowledgeDropdownBoard" class="hidden bg-white border border-[#E2E8F0] rounded-2xl p-6 shadow-xs animate-fadeIn space-y-4">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-2.5">
                        <div id="boardTitleIcon" class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold"></div>
                        <h3 id="boardTitleText" class="text-lg font-bold text-[#0F172A]">Documentation Module</h3>
                    </div>
                    <button onclick="closeKnowledgeBoard()" class="text-xs font-bold text-slate-400 hover:text-slate-600 flex items-center gap-1 bg-slate-50 hover:bg-slate-100 px-3 py-1.5 rounded-xl transition-all">
                        <i class="fa-solid fa-minus-min"></i> Collapse View
                    </button>
                </div>
                <div id="boardContentBody" class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-2">
                    </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
            
            <div class="lg:col-span-2 bg-white border border-[#E2E8F0] rounded-2xl shadow-xs overflow-hidden">
                <div class="p-6 border-b border-[#E2E8F0] flex items-center gap-3 bg-slate-50/50">
                    <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center"><i class="fa-regular fa-envelope text-sm"></i></div>
                    <h3 class="font-bold text-[#0F172A] text-lg">Contact Support center</h3>
                </div>

                <form id="supportTicketForm" onsubmit="handleTicketSubmission(event)" class="p-6 space-y-5">
                    @csrf
                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Subject Parameter</label>
                        <input type="text" id="ticketSubject" required placeholder="Briefly describe the issue (e.g., Webcam disconnect)" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium transition-all">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold uppercase tracking-wider text-slate-500">Detailed Description</label>
                        <textarea id="ticketDescription" required rows="5" placeholder="Provide detailed operational information about the system error context..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:bg-white text-slate-800 font-medium transition-all"></textarea>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-3">
                            <input type="file" id="screenshotInput" accept="image/*" class="hidden" onchange="previewScreenshot(event)">
                            <button type="button" onclick="document.getElementById('screenshotInput').click()" class="inline-flex items-center gap-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 px-4 py-2.5 rounded-xl text-xs font-bold text-slate-700 transition-all">
                                <i class="fa-solid fa-paperclip"></i> Upload Screenshot
                            </button>
                            <span id="fileStatusText" class="text-xs text-slate-400 italic">No image file staged</span>
                        </div>
                        <div id="screenshotPreviewContainer" class="hidden relative w-44 rounded-xl overflow-hidden border border-slate-200 mt-2">
                            <img id="screenshotImageElement" src="#" class="w-full h-auto object-cover" alt="Staged Attachment Preview">
                            <button type="button" onclick="purgeStagedScreenshot()" class="absolute top-1 right-1 bg-red-500 hover:bg-red-600 text-white w-6 h-6 rounded-full flex items-center justify-center text-xs shadow-xs transition-all">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>

                    <div class="pt-2 border-t border-slate-100 flex justify-end">
                        <button type="submit" class="bg-[#1D4ED8] hover:bg-blue-800 text-white font-bold text-sm py-3 px-6 rounded-xl shadow-md shadow-blue-500/10 flex items-center gap-2 transition-all transform active:scale-98">
                            <i class="fa-solid fa-paper-plane"></i> Submit Request
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-xs p-6 flex flex-col justify-between">
                <div>
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="font-bold text-[#0F172A] text-lg">My Requests</h3>
                        <span id="activeTicketBadgeCount" class="text-[10px] font-bold px-2 py-0.5 bg-blue-50 text-blue-600 rounded-full tracking-wider uppercase">Active: 3</span>
                    </div>

                    <div id="ticketsInteractiveTrackWrapper" class="space-y-3.5">
                        @forelse($tickets as $index => $ticket)
                            <div onclick="viewTicketModalDetail('{{ $ticket['ticket_no'] }}', '{{ $ticket['subject'] }}', '{{ $ticket['status'] }}', '{{ $ticket['updated_at'] }}', '{{ addslashes($ticket['description']) }}')" class="border border-slate-100 hover:border-blue-400/50 p-4 rounded-xl cursor-pointer hover:bg-slate-50/50 transition-all flex flex-col justify-between gap-2 shadow-xs group">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <span class="text-[10px] font-mono font-bold text-slate-400 tracking-wider">{{ $ticket['ticket_no'] }}</span>
                                        <h4 class="text-sm font-bold text-[#1E293B] group-hover:text-blue-600 transition-colors mt-0.5">{{ $ticket['subject'] }}</h4>
                                    </div>
                                    <span class="text-[9px] font-extrabold tracking-wider px-2 py-0.5 rounded-md uppercase {{ $ticket['status'] === 'PENDING' ? 'bg-amber-50 text-amber-700 border border-amber-200/50' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/50' }}">
                                        {{ $ticket['status'] }}
                                    </span>
                                </div>
                                <div class="text-[10px] font-medium text-[#94A3B8] flex items-center gap-1">
                                    <i class="fa-regular fa-clock"></i> Updated: {{ $ticket['updated_at'] }}
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-xs text-slate-400 italic">No historical queries logged.</div>
                        @endforelse
                    </div>
                </div>

                <button type="button" class="w-full mt-6 py-2.5 bg-slate-50 hover:bg-slate-100 text-[#475569] border border-[#E2E8F0] font-bold text-xs rounded-xl transition-all shadow-xs">View All Support History</button>
            </div>
        </div>
    </main>

    <div id="ticketDetailModal" class="fixed inset-0 bg-slate-950/40 backdrop-blur-xs z-50 flex items-center justify-center hidden">
        <div class="bg-white border border-slate-200 w-full max-w-lg rounded-2xl shadow-2xl p-6 relative m-4 space-y-4">
            <div class="flex justify-between items-start">
                <div>
                    <span id="modalTicketNo" class="text-xs font-mono font-bold text-slate-400 tracking-wider">#ASC-0000</span>
                    <h3 id="modalTicketSubject" class="text-lg font-bold text-[#0F172A] mt-0.5">Subject</h3>
                </div>
                <button onclick="toggleModal('ticketDetailModal')" class="p-1.5 hover:bg-slate-100 text-slate-400 hover:text-slate-600 rounded-lg transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="border-y border-slate-100 py-4 space-y-3">
                <div class="flex justify-between text-xs font-medium">
                    <span class="text-slate-400">Status Partition:</span>
                    <span id="modalTicketStatus" class="font-bold px-2 py-0.5 rounded-md uppercase">BADGE</span>
                </div>
                <div class="flex justify-between text-xs font-medium">
                    <span class="text-slate-400">Last Telemetry Update:</span>
                    <span id="modalTicketTime" class="text-slate-700">Time</span>
                </div>
                <div class="space-y-1">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Log Scope Details</span>
                    <p id="modalTicketDescription" class="text-sm text-[#475569] bg-slate-50 p-3 rounded-xl border border-slate-100 leading-relaxed max-h-40 overflow-y-auto">Text content.</p>
                </div>
            </div>
            <div class="flex justify-end"><button onclick="toggleModal('ticketDetailModal')" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-bold transition-all">Close Window</button></div>
        </div>
    </div>

    <script>
        const databaseDocuments = {
            faq: {
                title: "Frequently Asked Questions",
                classes: "bg-blue-50 text-blue-600",
                icon: "fa-solid fa-circle-question",
                html: `
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <h4 class="font-bold text-sm text-[#1E293B]">What happens if my connection drops mid-exam?</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">The system automatically saves configuration data caches locally. Re-authenticate within 5 minutes to restore context sync states safely.</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <h4 class="font-bold text-sm text-[#1E293B]">Why does it return biometric calibration errors?</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Ensure proper room lighting parameters. Face tracking modules require solid illumination to map facial recognition points accurately.</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <h4 class="font-bold text-sm text-[#1E293B]">Can I use external monitors during the testing session?</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">No. Security verification modules explicitly flag dual desktop monitors setups as a strict canvas hardware violation environment.</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <h4 class="font-bold text-sm text-[#1E293B]">How are short essay text fields graded?</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Essay responses commit directly to your course lecturer panel files workspace for manual structural rubrics evaluation processing.</p>
                    </div>`
            },
            guidelines: {
                title: "Institutional Examination Guidelines",
                classes: "bg-indigo-50 text-indigo-600",
                icon: "fa-solid fa-book-open",
                html: `
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-2 col-span-2">
                        <h4 class="font-bold text-sm text-[#1E293B]">Pre-Exam Initialization Verification Checklists</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">All official assessments initialize exactly at scheduled course calendar slots. Review tracking criteria below carefully before launching token entry prompts:</p>
                        <ul class="list-disc pl-5 text-xs text-slate-500 space-y-1 mt-1 leading-relaxed">
                            <li>Ensure your physical student identity registration smart card is present for visual webcam validations.</li>
                            <li>Clear peripheral desk surfaces of any smartwatches, text records papers, or unapproved reference utilities.</li>
                            <li>Close all background networking applications (Discord, Teams, Telegram) to protect device thread logs memory allocations.</li>
                        </ul>
                    </div>`
            },
            proctoring: {
                title: "Automated AI Proctoring Rules Matrix",
                classes: "bg-purple-50 text-purple-600",
                icon: "fa-solid fa-shield-halved",
                html: `
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <h4 class="font-bold text-sm text-red-600"><i class="fa-solid fa-eye text-xs"></i> Intelligent Gaze Analysis Mapping</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">AI monitoring systems verify user coordinate orientation arrays constantly. Shifting eye focus away from terminal dimensions for >10 seconds triggers flag metrics logs blocks.</p>
                    </div>
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                        <h4 class="font-bold text-sm text-red-600"><i class="fa-solid fa-window-restore text-xs"></i> Secure Fullscreen window Sweeps</h4>
                        <p class="text-xs text-slate-500 leading-relaxed">Minimizing or switching tabs automatically flags execution scripts. Multiple persistent violations lock input values and force terminal closure states.</p>
                    </div>`
            }
        };

        let currentActiveSectionKey = null;

        // Interactive Inline Accordion dropdown panel controller
        function toggleKnowledgeSection(key) {
            const board = document.getElementById('knowledgeDropdownBoard');
            
            // Clean out styling on all active elements prior to evaluation switching loops
            Object.keys(databaseDocuments).forEach(k => {
                document.getElementById(`card-${k}`).classList.remove('border-blue-500', 'bg-slate-50/50 shadow-inner');
                document.getElementById(`indicator-${k}`).classList.add('hidden');
            });

            // If user clicked the same tab card that is already open, collapse it
            if (currentActiveSectionKey === key) {
                closeKnowledgeBoard();
                return;
            }

            // Bind current configuration item references data properties
            const doc = databaseDocuments[key];
            currentActiveSectionKey = key;

            // Update title text elements and render layout content structures
            document.getElementById('boardTitleText').innerText = doc.title;
            document.getElementById('boardContentBody').innerHTML = doc.html;

            // Sync visual styles matching icon styles configurations properties
            const titleIconWrap = document.getElementById('boardTitleIcon');
            titleIconWrap.className = `w-8 h-8 rounded-lg flex items-center justify-center text-sm font-bold ${doc.classes}`;
            titleIconWrap.innerHTML = `<i class="${doc.icon}"></i>`;

            // Apply highlighting borders directly onto active targeted card layout arrays
            document.getElementById(`card-${key}`).classList.add('border-blue-500', 'bg-slate-50/50', 'shadow-inner');
            document.getElementById(`indicator-${key}`).classList.remove('hidden');

            // Slide open the dashboard board dropdown container module
            board.classList.remove('hidden');
        }

        function closeKnowledgeBoard() {
            if(!currentActiveSectionKey) return;
            document.getElementById('knowledgeDropdownBoard').classList.add('hidden');
            document.getElementById(`card-${currentActiveSectionKey}`).classList.remove('border-blue-500', 'bg-slate-50/50', 'shadow-inner');
            document.getElementById(`indicator-${currentActiveSectionKey}`).classList.add('hidden');
            currentActiveSectionKey = null;
        }

        function toggleModal(id) {
            const modal = document.getElementById(id);
            modal.classList.toggle('hidden');
        }

        function viewTicketModalDetail(no, subject, status, time, description) {
            document.getElementById('modalTicketNo').innerText = no;
            document.getElementById('modalTicketSubject').innerText = subject;
            document.getElementById('modalTicketTime').innerText = time;
            document.getElementById('modalTicketDescription').innerText = description;
            
            const badge = document.getElementById('modalTicketStatus');
            badge.innerText = status;
            badge.className = `text-[10px] font-extrabold tracking-wider px-2 py-0.5 rounded-md uppercase ${status === 'PENDING' ? 'bg-amber-50 text-amber-700 border border-amber-200/50' : 'bg-emerald-50 text-emerald-700 border border-emerald-200/50'}`;
            
            toggleModal('ticketDetailModal');
        }

        function previewScreenshot(event) {
            const reader = new FileReader();
            reader.onload = function(){
                const output = document.getElementById('screenshotImageElement');
                output.src = reader.result;
                document.getElementById('screenshotPreviewContainer').classList.remove('hidden');
                document.getElementById('fileStatusText').innerText = event.target.files[0].name;
            };
            if(event.target.files[0]) {
                reader.readAsDataURL(event.target.files[0]);
            }
        }

        function purgeStagedScreenshot() {
            document.getElementById('screenshotInput').value = "";
            document.getElementById('screenshotImageElement').src = "#";
            document.getElementById('screenshotPreviewContainer').classList.add('hidden');
            document.getElementById('fileStatusText').innerText = "No image file staged";
        }

        function handleTicketSubmission(event) {
            event.preventDefault();
            const subject = document.getElementById('ticketSubject').value;
            const description = document.getElementById('ticketDescription').value;
            const dateStr = new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ", " + new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute:'2-digit' });
            const ticketId = '#ASC-' + Math.floor(3000 + Math.random() * 7000);

            const newTicketHtml = `
                <div onclick="viewTicketModalDetail('${ticketId}', '${subject.replace(/'/g, "\\'")}', 'PENDING', '${dateStr}', '${description.replace(/'/g, "\\'")}')" class="border border-slate-100 hover:border-blue-400/50 p-4 rounded-xl cursor-pointer hover:bg-slate-50/50 transition-all flex flex-col justify-between gap-2 shadow-xs group">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <span class="text-[10px] font-mono font-bold text-slate-400 tracking-wider">${ticketId}</span>
                            <h4 class="text-sm font-bold text-[#1E293B] group-hover:text-blue-600 transition-colors mt-0.5">${subject}</h4>
                        </div>
                        <span class="text-[9px] font-extrabold tracking-wider px-2 py-0.5 rounded-md uppercase bg-amber-50 text-amber-700 border border-amber-200/50">
                            PENDING
                        </span>
                    </div>
                    <div class="text-[10px] font-medium text-[#94A3B8] flex items-center gap-1">
                        <i class="fa-regular fa-clock"></i> Updated: Just Now
                    </div>
                </div>`;

            document.getElementById('ticketsInteractiveTrackWrapper').insertAdjacentHTML('afterbegin', newTicketHtml);
            document.getElementById('supportTicketForm').reset();
            purgeStagedScreenshot();
            alert(`Support ticket initialized successfully! Tracking Ref ID: ${ticketId}`);
        }
    </script>
</body>
</html>