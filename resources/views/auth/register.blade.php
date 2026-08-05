<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $platformName }} - Create Account</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 h-screen font-sans overflow-hidden">

    <div class="flex h-full w-full">
        
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-b from-[#2a629a] via-[#1e4a75] to-[#123150] text-white flex-col justify-center items-center text-center p-12 relative">
            <div class="flex flex-col items-center justify-center">
                <div class="text-white text-8xl mb-6 filter drop-shadow-md animate-fade-in">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h1 class="text-5xl font-extrabold tracking-tight mb-4">{{ $platformName }}</h1>
                <p class="text-lg text-blue-100 font-light max-w-md tracking-wide">Empowering Academic Integrity</p>
                <div class="w-16 h-[3px] bg-white/30 rounded-full mt-8"></div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 bg-white flex flex-col justify-between p-8 sm:p-12 md:p-16 overflow-y-auto">
            
            <div></div>

            <div class="w-full max-w-xl mx-auto">
                <h2 class="text-3xl font-bold text-[#0c2340] mb-2">Create Account</h2>
                <p class="text-sm text-gray-400 mb-8 font-light">Please provide your institutional credentials to proceed.</p>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-xs mb-5 shadow-sm">
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('register') }}" method="POST" class="space-y-4">
                    @csrf

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">First Name</label>
                            <input type="text" name="first_name" value="{{ old('first_name') }}" required
                                class="w-full px-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="Jane">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Last Name</label>
                            <input type="text" name="last_name" value="{{ old('last_name') }}" required
                                class="w-full px-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="Doe">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Registering As</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs pointer-events-none">
                                <i class="fa-solid fa-users"></i>
                            </span>
                            <select name="role" id="register_role_select" required
                                class="w-full pl-11 pr-10 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner appearance-none cursor-pointer">
                                <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student (Default Portal Access)</option>
                                <option value="teacher" {{ old('role') == 'teacher' ? 'selected' : '' }}>Teacher (Instructor Management Workspace)</option>
                                {{-- 🔒 Admin and Super Admin are intentionally NOT selectable here.
                                     Those accounts can only be created by an existing Super Admin
                                     from /super-admin/admins, never through public self-registration. --}}
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 text-[10px] pointer-events-none">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Institutional ID</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs">
                                <i class="fa-regular fa-id-card"></i>
                            </span>
                            <div class="w-full pl-11 pr-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-400 italic shadow-inner">
                                Assigned automatically after you register
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">University Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs">
                                <i class="fa-regular fa-envelope"></i>
                            </span>
                            <input type="email" name="email" id="register_email_field" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400/80 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="jane.doe@university.edu">
                        </div>
                    </div>

                    <div id="department_field" class="hidden">
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Department</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs pointer-events-none">
                                <i class="fa-solid fa-building-columns"></i>
                            </span>
                            <select name="department_id" id="department_select"
                                class="w-full pl-11 pr-10 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner appearance-none cursor-pointer">
                                <option value="">Enter your university email first</option>
                            </select>
                            <span class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 text-[10px] pointer-events-none">
                                <i class="fa-solid fa-chevron-down"></i>
                            </span>
                        </div>
                        <p class="text-[11px] text-gray-400 mt-1.5 font-light" id="department_hint">
                            A department Admin will confirm your account before you can sign in.
                        </p>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Create Password</label>
                        <!-- 🟢 FIXED FIELD 1: SHOW/HIDE TOGGLE -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password" id="register_password_field" required
                                class="w-full pl-11 pr-12 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="••••••••••••">
                            <button type="button" onclick="togglePasswordVisibility('register_password_field', 'register_eye_icon')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-slate-600 transition-all cursor-pointer focus:outline-none">
                                <i class="fa-regular fa-eye text-sm" id="register_eye_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Confirm Password</label>
                        <!-- 🟢 FIXED FIELD 2: SHOW/HIDE TOGGLE -->
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs">
                                <i class="fa-solid fa-lock"></i>
                            </span>
                            <input type="password" name="password_confirmation" id="register_confirm_field" required
                                class="w-full pl-11 pr-12 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="••••••••••••">
                            <button type="button" onclick="togglePasswordVisibility('register_confirm_field', 'register_confirm_eye_icon')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-slate-600 transition-all cursor-pointer focus:outline-none">
                                <i class="fa-regular fa-eye text-sm" id="register_confirm_eye_icon"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-start pt-2">
                        <input id="terms" type="checkbox" required 
                            class="mt-1 w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 cursor-pointer">
                        <label for="terms" class="ml-2 text-xs leading-relaxed font-light text-gray-400 select-none cursor-pointer">
                            I agree to the <a href="#" class="text-[#1c446c] font-semibold hover:underline">Terms of Service</a> and <a href="#" class="text-[#1c446c] font-semibold hover:underline">Privacy Policy</a> regarding proctored examinations.
                        </label>
                    </div>

                    <div class="pt-4">
                        <button type="submit" 
                            class="w-full py-3.5 bg-[#173154] text-white font-medium text-sm rounded-xl shadow-md hover:bg-[#0f2139] hover:shadow-lg transition-all flex items-center justify-center gap-2 transform active:scale-[0.99]">
                            Register Account <i class="fa-solid fa-arrow-right text-xs"></i>
                        </button>
                    </div>

                    <div class="text-center pt-4">
                        <a href="{{ route('login.page') }}" class="text-xs uppercase font-bold tracking-wider text-[#1e5fa7] hover:text-blue-800 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-chevron-left text-[10px]"></i> Back to Login
                        </a>
                    </div>
                </form>
            </div>

            <div class="w-full max-w-xl mx-auto pt-8 border-t border-gray-50 flex justify-center items-center text-[9px] uppercase font-bold tracking-widest text-gray-400/70 gap-8">
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-nodes text-[10px]"></i> Secure Encryption
                </div>
                <div class="flex items-center gap-1.5">
                    <i class="fa-solid fa-scale-balanced text-[10px]"></i> Academic Policy 4.2
                </div>
            </div>
        </div>

    </div>

    <!-- 🟢 SCRIPT INJECTION -->
    <script>
        function togglePasswordVisibility(fieldId, iconId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = "password";
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Department field: both students and teachers declare which
        // department they belong to at registration (e.g. a teacher who
        // teaches in Data Science registers into Data Science). That
        // department's Admin is the one who reviews and approves them —
        // see AdminController::scopedDepartmentIds — so this is what
        // routes their pending account to the right person. A teacher can
        // still be added to additional departments later by an Admin.
        const roleSelect = document.getElementById('register_role_select');
        const emailField = document.getElementById('register_email_field');
        const departmentField = document.getElementById('department_field');
        const departmentSelect = document.getElementById('department_select');
        const departmentHint = document.getElementById('department_hint');

        function needsDepartment() {
            return roleSelect.value === 'student' || roleSelect.value === 'teacher';
        }

        function updateDepartmentVisibility() {
            const show = needsDepartment();
            departmentField.classList.toggle('hidden', !show);
            departmentSelect.required = show;
        }

        let departmentFetchController = null;

        async function loadDepartmentsForEmail() {
            if (!needsDepartment()) return;

            const email = emailField.value.trim();
            const previouslySelected = departmentSelect.value;

            if (!email.includes('@')) {
                departmentSelect.innerHTML = '<option value="">Enter your university email first</option>';
                departmentHint.textContent = "A department Admin will confirm your account before you can sign in.";
                return;
            }

            // Cancel any in-flight lookup so a fast typist doesn't get an
            // older, out-of-order response overwriting a newer one.
            if (departmentFetchController) departmentFetchController.abort();
            departmentFetchController = new AbortController();

            departmentSelect.innerHTML = '<option value="">Loading departments…</option>';

            try {
                const res = await fetch(`{{ route('register.departments') }}?email=${encodeURIComponent(email)}`, {
                    headers: { 'Accept': 'application/json' },
                    signal: departmentFetchController.signal,
                });
                const data = await res.json();
                const departments = data.departments || [];

                if (departments.length === 0) {
                    departmentSelect.innerHTML = '<option value="">No departments found for this email yet</option>';
                    departmentHint.textContent = "Double-check your university email, or contact your Admin if your department isn't listed.";
                    return;
                }

                departmentSelect.innerHTML = '<option value="">Select your department…</option>' +
                    departments.map(d => `<option value="${d.id}">${d.name}</option>`).join('');

                if (previouslySelected && departments.some(d => String(d.id) === previouslySelected)) {
                    departmentSelect.value = previouslySelected;
                }

                departmentHint.textContent = "A department Admin will confirm your account before you can sign in.";
            } catch (e) {
                if (e.name === 'AbortError') return;
                departmentSelect.innerHTML = '<option value="">Couldn\'t load departments — try again</option>';
            }
        }

        roleSelect.addEventListener('change', () => {
            updateDepartmentVisibility();
            loadDepartmentsForEmail();
        });
        emailField.addEventListener('blur', loadDepartmentsForEmail);
        emailField.addEventListener('input', () => {
            // Debounce: only look up once typing pauses briefly.
            clearTimeout(emailField._lookupTimer);
            emailField._lookupTimer = setTimeout(loadDepartmentsForEmail, 500);
        });

        updateDepartmentVisibility();
        if (emailField.value) loadDepartmentsForEmail();
    </script>
</body>
</html>