<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam - Check Your Email</title>
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 font-sans antialiased h-screen">

    <div class="flex h-full w-full min-h-screen">
        
        <div class="hidden md:flex md:w-[45%] bg-[#1E4277] text-white p-12 flex-col justify-between relative select-none">
            <div class="flex items-center space-x-2 text-xl font-semibold tracking-wide">
                <i class="fas fa-user-graduate"></i>
                <span>Online Exam</span>
            </div>

            <div class="flex flex-col items-center justify-center text-center space-y-4 my-auto">
                <div class="bg-white/10 p-5 rounded-2xl border border-white/10 shadow-lg">
                    <i class="fas fa-shield-alt text-4xl text-white"></i>
                </div>
                <h1 class="text-3xl font-bold tracking-tight mt-2">OnlineExam</h1>
                <p class="text-blue-200 text-sm max-w-xs font-light">Empowering Academic Integrity</p>
            </div>

            <div class="space-y-3 text-xs text-blue-100 font-light opacity-90">
                <div class="flex items-center space-x-3">
                    <i class="fas fa-lock text-sm w-4 text-center"></i>
                    <span>Encrypted Credentials</span>
                </div>
                <div class="flex items-center space-x-3">
                    <i class="fas fa-fingerprint text-sm w-4 text-center"></i>
                    <span>Biometric Readiness</span>
                </div>
            </div>
        </div>

        <div class="w-full md:w-[55%] bg-white flex flex-col justify-center items-center p-8 relative">
            <div class="max-w-md w-full text-center space-y-6">
                
                <div class="relative inline-block mx-auto">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                        <i class="far fa-envelope text-3xl text-[#1E4277]"></i>
                    </div>
                    <div class="absolute bottom-0 right-0 bg-[#22C55E] border-4 border-white text-white rounded-full w-7 h-7 flex items-center justify-center shadow-sm">
                        <i class="fas fa-check text-[10px]"></i>
                    </div>
                </div>

                <div>
                    <span class="bg-[#E8F9F1] text-[#22C55E] text-xs font-bold tracking-wider uppercase px-4 py-1.5 rounded-full">
                        Success
                    </span>
                </div>

                <div class="space-y-2">
                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Check your email</h2>
                    <p class="text-sm text-gray-500 font-normal leading-relaxed">
                        We've sent a password reset link to <br>
                        <span class="font-semibold text-gray-700">{{ session('reset_email', 'name@university.edu') }}</span>. 
                        Please check your inbox and follow the instructions.
                    </p>
                </div>

                <div class="pt-2">
                    <a href="https://mail.google.com/" target="_blank" class="w-full block bg-[#1E4277] hover:bg-[#16335C] text-white font-medium py-3 px-4 rounded-md transition duration-200 shadow-sm text-sm">
                        Open Email App <i class="fas fa-external-link-alt text-xs ml-1.5 opacity-80"></i>
                    </a>
                </div>

                <div class="flex flex-col items-center justify-center pt-2 space-y-2 text-sm">
                    <div class="flex items-center space-x-3 text-gray-400">
                        <form method="POST" action="{{ route('password.email') }}" id="resend-form">
                            @csrf
                            <input type="hidden" name="email" value="{{ session('reset_email') }}">
                            <button type="submit" id="resend-btn" class="text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer">
                                Resend Email
                            </button>
                        </form>
                        <span class="text-gray-300">|</span>
                        <a href="{{ route('login.page') }}" class="text-gray-700 hover:text-gray-900 font-medium transition">
                            Back to Login
                        </a>
                    </div>
                    
                    <span id="timer-text" class="text-xs text-gray-400 flex items-center">
                        <i class="far fa-clock mr-1 text-[11px]"></i> Resend available in <span id="countdown" class="mx-1 font-medium">30</span>s
                    </span>
                </div>

                <div class="bg-gray-50 border border-gray-100 rounded-lg p-3.5 flex items-start space-x-2.5 text-left mt-4">
                    <i class="fas fa-info-circle text-gray-400 mt-0.5 text-sm"></i>
                    <p class="text-xs text-gray-500 leading-normal font-light">
                        If you don't see the email, check your spam folder.
                    </p>
                </div>

            </div>
        </div>

    </div>

    <script>
        let seconds = 30;
        const countdownEl = document.getElementById('countdown');
        const timerTextEl = document.getElementById('timer-text');
        const resendBtn = document.getElementById('resend-btn');

        // Immediately lock resend capability upon arriving
        resendBtn.disabled = true;
        resendBtn.classList.add('opacity-40', 'cursor-not-allowed', 'text-gray-400');
        resendBtn.classList.remove('text-blue-600', 'hover:text-blue-800', 'cursor-pointer');

        const timer = setInterval(() => {
            seconds--;
            countdownEl.textContent = seconds;
            if (seconds <= 0) {
                clearInterval(timer);
                timerTextEl.classList.add('hidden');
                resendBtn.disabled = false;
                resendBtn.classList.remove('opacity-40', 'cursor-not-allowed', 'text-gray-400');
                resendBtn.classList.add('text-blue-600', 'hover:text-blue-800', 'cursor-pointer');
            }
        }, 1000);
    </script>
</body>
</html>