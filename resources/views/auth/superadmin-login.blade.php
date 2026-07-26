<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam - Login</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased h-screen overflow-hidden">
    <div class="flex h-full w-full min-h-screen">

        <!-- Left Panel: Branding -->
        <div class="hidden md:flex md:w-[45%] bg-[#1E4277] text-white p-12 flex-col justify-between relative select-none">
            <div class="flex items-center space-x-2 text-xl font-semibold tracking-wide">
                <i class="fas fa-user-graduate"></i>
                <span>Online Exam</span>
            </div>

            <div class="flex flex-col items-center justify-center text-center space-y-5 my-auto">
                <div class="bg-white/10 p-5 rounded-full border border-white/5 shadow-lg w-20 h-20 flex items-center justify-center">
                    <i class="fas fa-graduation-cap text-3xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold tracking-tight max-w-sm leading-tight">Empowering Academic Integrity</h1>
                <p class="text-blue-200 text-sm max-w-xs font-light leading-relaxed">
                    The Scholarly Sanctuary for secure, professional, and reliable examination environments.
                </p>
            </div>

            <div class="flex items-center space-x-12 text-xs text-blue-100 font-light opacity-90 border-t border-white/10 pt-6">
                <div class="flex items-center space-x-2.5">
                    <div class="bg-white/10 p-2 rounded-lg">
                        <i class="fas fa-lock text-xs w-3.5 text-center"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-medium text-white">Encrypted</span>
                        <span class="text-[10px] opacity-70">Credentials</span>
                    </div>
                </div>
                <div class="flex items-center space-x-2.5">
                    <div class="bg-white/10 p-2 rounded-lg">
                        <i class="fas fa-fingerprint text-xs w-3.5 text-center"></i>
                    </div>
                    <div class="flex flex-col">
                        <span class="font-medium text-white">Biometric</span>
                        <span class="text-[10px] opacity-70">Readiness</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel: Login Form -->
        <div class="w-full md:w-[55%] bg-white flex flex-col justify-between p-8 sm:p-12 md:p-16 overflow-y-auto">
            <div></div>

            <div class="w-full max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-[#0c2340] mb-2">Welcome Back</h2>
                <p class="text-sm text-gray-400 mb-8 font-light">Please enter your credentials to access the portal.</p>

                <!-- Status / Validation Errors -->
                @if(session('error'))
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm mb-5 shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm mb-5 shadow-sm">
                        <ul class="list-disc pl-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form id="auth_login_form" action="{{ route('login') }}" method="POST">
                    @csrf

                    <!-- Role payload input tracking structural node -->
                    <input type="hidden" name="role" id="selected_role" value="student">

                    <!-- Role Switcher -->
                    <div class="bg-[#F4F6F9] p-1 rounded-full flex justify-between gap-1 mb-8 text-[11px] font-bold tracking-wider uppercase text-gray-500">
                        <button type="button" class="role-btn w-full py-2 px-2 rounded-full text-center bg-[#1E4277] text-white shadow-sm transition-all" data-role="student">
                            Student
                        </button>
                        <button type="button" class="role-btn w-full py-2 px-2 rounded-full text-center hover:bg-gray-200 transition-all text-gray-500" data-role="teacher">
                            Teacher
                        </button>
                        <button type="button" class="role-btn w-full py-2 px-2 rounded-full text-center hover:bg-gray-200 transition-all text-gray-500" data-role="admin">
                            Admin
                        </button>
                        <button type="button" class="role-btn w-full py-2 px-2 rounded-full text-center hover:bg-gray-200 transition-all whitespace-nowrap text-gray-500" data-role="super_admin">
                            Super Admin
                        </button>
                    </div>

                    <!-- Email Input -->
                    <div class="mb-5">
                        <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs"><i class="fa-regular fa-user"></i></span>
                            <input type="email" name="email" value="{{ old('email') }}" required
                                class="w-full pl-11 pr-4 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="j.doe@university.edu">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-5">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-[10px] font-bold tracking-wider text-gray-500 uppercase">Password</label>
                            <a id="forgot_password_link" href="{{ route('password.request') }}" class="text-xs text-[#1E4277] font-semibold hover:underline">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 text-xs"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" id="login_password_field" required
                                class="w-full pl-11 pr-12 py-3 bg-[#f0f2f5] border border-transparent rounded-xl text-sm text-gray-700 placeholder-gray-400 focus:outline-none focus:bg-white focus:border-blue-500 transition-all shadow-inner"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility('login_password_field', 'login_eye_icon')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-slate-600 transition-all cursor-pointer focus:outline-none">
                                <i class="fa-regular fa-eye text-sm" id="login_eye_icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center mb-6">
                        <input id="remember_device" type="checkbox" name="remember" class="w-4 h-4 text-[#1E4277] bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember_device" class="ml-2 text-xs font-medium text-gray-500 select-none cursor-pointer">Remember this device</label>
                    </div>

                    <!-- Submit -->
                    <button type="submit" class="w-full py-3 bg-[#1E4277] text-white font-bold text-sm tracking-wider uppercase rounded-xl shadow-md hover:bg-[#163259] transition-all transform active:scale-[0.99]">
                        Sign In
                    </button>

                    <div class="text-center mt-6 text-sm text-gray-500 font-light">
                        Don't have an account? <a href="{{ route('register.page') }}" class="text-[#1E4277] font-bold hover:underline ml-1">Register now</a>
                    </div>
                </form>
            </div>

            <!-- Trust Badges Footer -->
            <div class="w-full max-w-md mx-auto pt-8 border-t border-gray-100 flex justify-between text-[10px] uppercase font-bold tracking-wider text-gray-400/80 gap-4">
                <div><i class="fa-solid fa-shield-halved"></i> ISO 27001 Certified</div>
                <div><i class="fa-solid fa-lock"></i> SSL Encrypted</div>
            </div>
        </div>
    </div>

    <!-- ⚡ SYSTEM ENGINE JAVASCRIPT LAYER -->
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

        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.role-btn');
            const roleInput = document.getElementById('selected_role');
            const authForm = document.getElementById('auth_login_form');

            const forgotPasswordLink = document.getElementById('forgot_password_link');

            // Explicit Routing Context Mappings From Matrix
            const standardForgotUrl = "{{ route('password.request') }}";
            const superAdminForgotUrl = "{{ route('superadmin.password.request') }}";

            const standardLoginUrl = "{{ route('login') }}";

            const activeClasses = ['bg-[#1E4277]', 'text-white', 'shadow-sm'];
            const inactiveClasses = ['hover:bg-gray-200', 'text-gray-500'];

            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    buttons.forEach(btn => {
                        btn.classList.remove(...activeClasses);
                        btn.classList.add(...inactiveClasses);
                    });

                    this.classList.remove(...inactiveClasses);
                    this.classList.add(...activeClasses);

                    const selectedRole = this.getAttribute('data-role');
                    roleInput.value = selectedRole;

                    // Super Admin now logs in with email + password just like
                    // every other role — always submit to the standard login
                    // endpoint. The OTP/code flow is kept ONLY for password
                    // recovery, so the "Forgot password" link still routes
                    // Super Admins to the code-based recovery flow.
                    authForm.action = standardLoginUrl;
                    if (selectedRole === 'super_admin') {
                        forgotPasswordLink.href = superAdminForgotUrl;
                    } else {
                        forgotPasswordLink.href = standardForgotUrl;
                    }
                });
            });
        });
    </script>
</body>
</html>