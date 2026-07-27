<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Online Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen font-sans overflow-hidden">
    <div class="flex h-full w-full">

        <div class="hidden lg:flex lg:w-1/2 bg-[#1e4ea1] text-white p-12 flex-col justify-between relative overflow-hidden">

            <div class="flex items-center space-x-3 z-10">
                <i class="fa-solid fa-graduation-cap text-2xl"></i>
                <span class="text-xl font-bold tracking-wide">Online Exam</span>
            </div>

            <div class="max-w-xl my-auto z-10">
                <h1 class="text-4xl font-bold tracking-tight mb-6 leading-tight">
                    Empowering Academic Integrity
                </h1>
                <p class="text-blue-100 text-lg leading-relaxed font-light">
                    Access your secure scholarly sanctuary with enterprise-grade protection for every examination.
                </p>

                <div class="grid grid-cols-2 gap-6 mt-12">
                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/10">
                        <div class="mb-3 text-xl">
                            <i class="fa-solid fa-shield-halved"></i>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Secure Protocol</h3>
                        <p class="text-xs text-blue-200 font-light leading-normal">
                            Advanced proctoring algorithms for fair evaluation.
                        </p>
                    </div>

                    <div class="bg-white/10 backdrop-blur-md rounded-xl p-5 border border-white/10">
                        <div class="mb-3 text-xl">
                            <i class="fa-solid fa-chart-simple"></i>
                        </div>
                        <h3 class="font-semibold text-sm mb-1">Instant Insights</h3>
                        <p class="text-xs text-blue-200 font-light leading-normal">
                            Real-time performance analytics for students.
                        </p>
                    </div>
                </div>
            </div>

            <div class="z-10 text-[10px] text-blue-300 font-semibold tracking-widest uppercase">
                © 2024 Scholaris Pro OnlineXM. Academic Authority & Digital Serenity.
            </div>

            <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
        </div>

        <div class="w-full lg:w-1/2 bg-white flex flex-col items-center overflow-y-auto relative p-8 sm:p-12 md:p-16">

            <div class="w-full max-w-md mx-auto text-center my-auto py-6">

                <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full text-[#1e4ea1] mb-5">
                    <i class="fa-solid fa-user-shield text-2xl"></i>
                </div>

                @if (session('admin_request_sent'))
                    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl text-sm text-left flex items-start gap-2">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <span>Your request has been sent to the Super Admin. They'll reset your password and reach out to you shortly.</span>
                    </div>
                @endif

                @if ($errors->has('admin_email'))
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm text-left flex items-start gap-2">
                        <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                        <span>{{ $errors->first('admin_email') }}</span>
                    </div>
                @endif

                <div id="student-teacher-panel">
                <h2 class="text-3xl font-bold text-slate-900 mb-3 tracking-tight">Password Reset Restricted</h2>
                <p class="text-gray-500 text-sm leading-relaxed mb-6 px-2">
                    For security reasons, students and teachers cannot reset their password directly.
                    Select your department below to find the admin who can reset it for you.
                </p>

                <!-- Department picker -->
                <div class="text-left mb-4">
                    <label for="department-select" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                        Your Department
                    </label>
                    <div class="relative">
                        <select id="department-select"
                                class="w-full appearance-none pl-4 pr-10 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
                            <option value="" selected disabled>Select your department…</option>
                            @forelse (($departments ?? collect()) as $department)
                                <option value="{{ $department['id'] }}">
                                    {{ $department['name'] }}{{ $department['institution_name'] ? ' — ' . $department['institution_name'] : '' }}
                                </option>
                            @empty
                                <option value="" disabled>No departments have been set up yet</option>
                            @endforelse
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-solid fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                <!-- Contact cards, filled in by JS once a department is picked. One
                     card per admin currently assigned to that department — 2 admins
                     show 2 cards, 3 admins show 3, etc. Fetched live from the server
                     every time a department is selected so it never goes stale. -->
                <div id="admin-contact-wrap" class="hidden text-left mb-4">
                    <div class="flex items-center justify-between mb-2 px-1">
                        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-wider">Department Admins</span>
                        <span id="admin-count-badge" class="text-[10px] font-bold text-[#1e4ea1] bg-blue-50 px-2 py-0.5 rounded-full">—</span>
                    </div>
                    <div id="admin-contact-loading" class="hidden text-xs text-gray-400 flex items-center gap-2 py-3">
                        <i class="fa-solid fa-circle-notch fa-spin"></i> Loading current admins…
                    </div>
                    <div id="admin-contact-list" class="space-y-3"></div>
                    <p id="admin-contact-empty" class="hidden text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2.5 leading-relaxed">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        No admin has been assigned to this department yet. Please use the support options below instead.
                    </p>
                </div>

                <p id="no-department-hint" class="text-xs text-gray-400 mb-4">
                    Pick your department above and the admins who can reset your password will appear here.
                </p>

                <div class="flex items-center gap-3 my-4">
                    <div class="h-px bg-gray-200 flex-1"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Or</span>
                    <div class="h-px bg-gray-200 flex-1"></div>
                </div>

                <div class="space-y-2 text-left">
                    <a href="mailto:admin@yourexam.com" class="flex items-center gap-3 py-3 px-4 bg-white border border-gray-200 rounded-xl text-sm text-slate-700 hover:border-blue-300 hover:bg-blue-50/50 transition-all">
                        <i class="fa-regular fa-envelope text-[#1e4ea1] w-4 text-center"></i>
                        <span>admin@yourexam.com</span>
                    </a>
                    <a href="tel:+855000000000" class="flex items-center gap-3 py-3 px-4 bg-white border border-gray-200 rounded-xl text-sm text-slate-700 hover:border-blue-300 hover:bg-blue-50/50 transition-all">
                        <i class="fa-solid fa-phone text-[#1e4ea1] w-4 text-center"></i>
                        <span>+855 00 000 000</span>
                    </a>
                </div>

                <div class="mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="showAdminPanel()" class="text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                        <i class="fa-solid fa-user-tie mr-1"></i>
                        Are you an Admin? <span class="text-[#1e4ea1] underline">Request a reset from the Super Admin</span>
                    </button>
                </div>
                </div><!-- /student-teacher-panel -->

                <!-- Admin request panel, hidden until toggled -->
                <div id="admin-panel" class="hidden text-left">
                    <div class="text-center">
                        <h2 class="text-3xl font-bold text-slate-900 mb-3 tracking-tight">Admin Password Reset</h2>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6 px-2">
                            Only the Super Admin can reset an admin's password. Submit a request below and they'll be notified immediately.
                        </p>
                    </div>

                    <form action="{{ route('password.admin.request') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="admin-email" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                                Your Admin Email
                            </label>
                            <div class="relative rounded-lg shadow-sm">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                    <i class="fa-regular fa-envelope"></i>
                                </div>
                                <input
                                    type="email"
                                    name="email"
                                    id="admin-email"
                                    value="{{ old('email') }}"
                                    required
                                    placeholder="you@yourexam.com"
                                    class="block w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                                >
                            </div>
                        </div>

                        <div class="mb-6">
                            <label for="admin-message" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                                Message <span class="normal-case font-medium text-gray-400">(optional)</span>
                            </label>
                            <textarea
                                name="message"
                                id="admin-message"
                                rows="3"
                                placeholder="Anything the Super Admin should know…"
                                class="block w-full px-4 py-3 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm resize-none"
                            >{{ old('message') }}</textarea>
                        </div>

                        <button
                            type="submit"
                            class="w-full flex items-center justify-center space-x-2 py-3.5 px-4 bg-[#11357c] hover:bg-[#1a4494] text-white text-sm font-medium rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all group"
                        >
                            <span>Send Request to Super Admin</span>
                            <i class="fa-solid fa-paper-plane text-xs transform group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>

                    <div class="mt-6 text-center">
                        <button type="button" onclick="showStudentPanel()" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-500 hover:text-slate-900 transition-colors">
                            <i class="fa-solid fa-arrow-left"></i>
                            <span>I'm a student or teacher instead</span>
                        </button>
                    </div>
                </div><!-- /admin-panel -->

                <div class="mt-6 text-center">
                    <a href="{{ route('login.page') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-400 hover:text-slate-900 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Back to login</span>
                    </a>
                </div>
            </div>

            <div class="w-full text-center flex justify-center gap-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest pt-6 pb-2 flex-shrink-0">
                <a href="#" class="hover:text-gray-600 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-gray-600 transition-colors">Support</a>
            </div>

        </div>
    </div>

    <script>
        // Toggle between the Student/Teacher department flow and the Admin
        // "request a reset from the Super Admin" flow.
        const studentPanel = document.getElementById('student-teacher-panel');
        const adminPanel   = document.getElementById('admin-panel');

        window.showAdminPanel = function () {
            studentPanel.classList.add('hidden');
            adminPanel.classList.remove('hidden');
        };

        window.showStudentPanel = function () {
            adminPanel.classList.add('hidden');
            studentPanel.classList.remove('hidden');
        };

        @if (session('admin_request_sent') || $errors->has('admin_email'))
            showAdminPanel();
        @endif

        // Real-time admin lookup: instead of baking a fixed admin list into
        // the page at render time, we hit a live JSON endpoint every time a
        // department is picked (and again on a short interval while it's
        // open). That way if a department has 2 admins it shows 2 cards, if
        // it has 3 it shows 3, and any change made in the admin panel a
        // moment ago (new admin assigned, one removed, a Telegram handle
        // updated) shows up immediately without a page refresh.
        const departmentSelect = document.getElementById('department-select');
        const contactWrap      = document.getElementById('admin-contact-wrap');
        const contactLoading   = document.getElementById('admin-contact-loading');
        const contactList      = document.getElementById('admin-contact-list');
        const contactEmpty     = document.getElementById('admin-contact-empty');
        const countBadge       = document.getElementById('admin-count-badge');
        const noDeptHint       = document.getElementById('no-department-hint');

        const contactsUrlBase = "{{ url('/forgot-password/department') }}";
        let refreshTimer = null;

        function renderAdmins(dept) {
            const admins = dept.admins || [];

            countBadge.textContent = admins.length === 1 ? '1 admin' : (admins.length + ' admins');
            contactList.innerHTML = '';

            if (admins.length === 0) {
                contactEmpty.classList.remove('hidden');
                return;
            }
            contactEmpty.classList.add('hidden');

            admins.forEach(function (admin) {
                const card = document.createElement('div');
                card.className = 'bg-slate-50 border border-slate-200 rounded-xl p-4';

                const hasTelegram = !!admin.telegram_username;
                card.innerHTML = `
                    <div class="flex items-center gap-3 mb-3">
                        <div class="w-10 h-10 rounded-full bg-[#1e4ea1]/10 text-[#1e4ea1] flex items-center justify-center flex-shrink-0">
                            <i class="fa-solid fa-user-tie"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="text-sm font-bold text-slate-900 truncate">${admin.name ?? 'Department Admin'}</div>
                            <div class="text-xs text-gray-500">Department Admin</div>
                        </div>
                    </div>
                    ${hasTelegram ? `
                        <a href="https://t.me/${admin.telegram_username}" target="_blank" rel="noopener noreferrer"
                           class="w-full flex items-center justify-center space-x-2 py-2.5 px-4 bg-[#229ED9] hover:bg-[#1c8bc0] text-white text-sm font-medium rounded-xl shadow-md transition-all group">
                            <i class="fa-brands fa-telegram text-base"></i>
                            <span>Message on Telegram</span>
                            <i class="fa-solid fa-arrow-up-right-from-square text-xs opacity-80 transform group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                        </a>
                    ` : `
                        <p class="text-xs text-amber-700 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2 leading-relaxed">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            Hasn't added a Telegram contact yet — use the support options below.
                        </p>
                    `}
                `;
                contactList.appendChild(card);
            });
        }

        function fetchDepartmentContacts(deptId) {
            contactWrap.classList.remove('hidden');
            noDeptHint.classList.add('hidden');
            contactLoading.classList.remove('hidden');

            fetch(`${contactsUrlBase}/${deptId}/contacts`, {
                headers: { 'Accept': 'application/json' }
            })
                .then(res => res.ok ? res.json() : Promise.reject())
                .then(dept => {
                    contactLoading.classList.add('hidden');
                    renderAdmins(dept);
                })
                .catch(() => {
                    contactLoading.classList.add('hidden');
                    countBadge.textContent = '—';
                    contactList.innerHTML = '';
                    contactEmpty.classList.remove('hidden');
                });
        }

        departmentSelect.addEventListener('change', function () {
            if (refreshTimer) clearInterval(refreshTimer);

            const deptId = this.value;
            if (!deptId) {
                contactWrap.classList.add('hidden');
                noDeptHint.classList.remove('hidden');
                return;
            }

            fetchDepartmentContacts(deptId);

            // Keep it live while the visitor is sitting on this page —
            // picks up admin changes made elsewhere without a reload.
            refreshTimer = setInterval(() => fetchDepartmentContacts(deptId), 15000);
        });
    </script>
</body>
</html>