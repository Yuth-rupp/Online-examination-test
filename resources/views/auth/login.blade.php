<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 h-screen font-sans overflow-hidden">
    <div class="flex h-full w-full">
        <!-- Left Panel: Branding / Info Docks -->
        <div class="hidden lg:flex lg:w-1/2 bg-[#1e5fa7] text-white flex-col justify-between p-12 relative">
            <div class="text-xl font-bold tracking-wide">Online Exam</div>
            <div class="flex flex-col items-center justify-center text-center my-auto">
                <div class="bg-white p-5 rounded-2xl shadow-lg mb-6 text-[#1e5fa7] text-4xl w-20 h-20 flex items-center justify-center">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h1 class="text-5xl font-extrabold tracking-tight mb-4">OnlineExam</h1>
                <p class="text-lg text-blue-100 font-light max-w-md">Empowering Academic Integrity</p>
                <div class="grid grid-cols-2 gap-4 mt-12 w-full max-w-xl">
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl text-left border border-white/10 shadow-sm">
                        <div class="text-blue-200 mb-2 text-xl"><i class="fa-solid fa-shield-halved"></i></div>
                        <h4 class="font-semibold text-sm mb-1">Secure Protocol</h4>
                        <p class="text-xs text-blue-100 font-light leading-relaxed">Advanced proctoring algorithms for fair evaluation.</p>
                    </div>
                    <div class="bg-white/10 backdrop-blur-md p-5 rounded-xl text-left border border-white/10 shadow-sm">
                        <div class="text-blue-200 mb-2 text-xl"><i class="fa-solid fa-chart-simple"></i></div>
                        <h4 class="font-semibold text-sm mb-1">Instant Insights</h4>
                        <p class="text-xs text-blue-100 font-light leading-relaxed">Real-time performance analytics for students.</p>
                    </div>
                </div>
            </div>
            <div class="text-xs text-blue-200/60 tracking-wider">© 2024 Scholaris Pro OnlineXM. Academic Authority & Digital Serenity.</div>
        </div>

        <!-- Right Panel: Core Authentication Form Interface -->
        <div class="w-full lg:w-1/2 bg-white flex flex-col justify-between p-8 sm:p-12 md:p-20 relative">
            <div></div>
            <div class="w-full max-w-md mx-auto">
                <h2 class="text-3xl font-bold text-gray-800 mb-2">Welcome Back</h2>
                <p class="text-sm text-gray-500 mb-8">Please enter your credentials to access the portal.</p>

                <!-- Status / Validation Errors Alerts Workspace -->
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

                    <!-- Role Switcher Interface Docks -->
                    <div class="bg-gray-100 p-1 rounded-full flex justify-between gap-1 mb-8 text-[11px] font-bold tracking-wider uppercase text-gray-500">
                        <button type="button" class="role-btn w-full py-2 px-2 rounded-full text-center bg-[#2d3748] text-white shadow-sm transition-all" data-role="student">
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

                    <!-- Email Input Group -->
                    <div class="mb-5">
                        <label class="block text-xs font-semibold text-gray-700 mb-2">Email Address</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fa-regular fa-user"></i></span>
                            <input type="email" name="email" value="{{ old('email') }}" required class="w-full pl-11 pr-4 py-3 bg-[#f3f4f6] rounded-xl text-sm focus:outline-none focus:bg-white focus:border-blue-500 text-gray-700 transition-all" placeholder="j.doe@university.edu">
                        </div>
                    </div>

                    <!-- Password Input Group -->
                    <div class="mb-5">
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-xs font-semibold text-gray-700">Password</label>
                            <a id="forgot_password_link" href="{{ route('password.request') }}" class="text-xs text-[#1e5fa7] font-semibold hover:underline">Forgot Password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400"><i class="fa-solid fa-lock"></i></span>
                            <input type="password" name="password" id="login_password_field" required class="w-full pl-11 pr-12 py-3 bg-[#f3f4f6] rounded-xl text-sm focus:outline-none focus:bg-white focus:border-blue-500 transition-all text-gray-700" placeholder="••••••••">
                            <button type="button" onclick="togglePasswordVisibility('login_password_field', 'login_eye_icon')" class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 hover:text-slate-600 transition-all cursor-pointer focus:outline-none">
                                <i class="fa-regular fa-eye text-sm" id="login_eye_icon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="flex items-center mb-6">
                        <input id="remember_device" type="checkbox" name="remember" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500">
                        <label for="remember_device" class="ml-2 text-xs font-medium text-gray-500 select-none cursor-pointer">Remember this device</label>
                    </div>

                    <!-- Submit Submission Action Trigger Button -->
                    <button type="submit" class="w-full py-3 bg-[#0a2569] text-white font-bold text-sm tracking-wider uppercase rounded-xl shadow-md hover:bg-[#061743] transition-all transform active:scale-[0.99]">
                        Sign In
                    </button>

                    <div class="text-center mt-6 text-sm text-gray-600">
                        Don't have an account? <a href="{{ route('register.page') }}" class="text-[#1e5fa7] font-bold hover:underline ml-1">Register now</a>
                    </div>
                </form>
            </div>
            
            <!-- Trust Badges Footer Area -->
            <div class="w-full max-w-md mx-auto pt-8 border-t border-gray-100 flex justify-between text-[10px] uppercase font-bold tracking-wider text-gray-400/80 gap-4">
                <div><i class="fa-solid fa-shield-circle-check"></i> ISO 27001 Certified</div>
                <div><i class="fa-solid fa-lock"></i> SSL Encrypted</div>
            </div>
        </div>
    </div>

    <!-- ⚡ SYSTEM ENGINE JAVASCRIPT LAYER -->
    <script>
        // Inline utility configuration context for explicit character visibility triggers
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
            const superAdminLoginUrl = "{{ route('superadmin.sendcode') }}";

            const activeClasses = ['bg-[#2d3748]', 'text-white', 'shadow-sm'];
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

                    // Dynamically structuralize action end-points based on specific system permission rules
                    if (selectedRole === 'super_admin') {
                        forgotPasswordLink.href = superAdminForgotUrl;
                        authForm.action = superAdminLoginUrl;
                    } else {
                        forgotPasswordLink.href = standardForgotUrl;
                        authForm.action = standardLoginUrl;
                    }
                });
            });
        });
    </script>
</body>
</html>