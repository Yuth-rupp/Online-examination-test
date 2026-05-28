<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Verify Code</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50 h-screen font-sans overflow-hidden">
    <div class="flex h-full w-full">
        
        <div class="hidden lg:flex lg:w-1/2 bg-[#1e5fa7] text-white flex-col justify-between p-12 md:p-16 relative">
            
            <div class="flex items-center gap-3 text-xl font-bold tracking-wide uppercase">
                <i class="fa-solid fa-graduation-cap text-2xl"></i> Online Exam
            </div>
            
            <div class="max-w-md my-auto">
                <h1 class="text-5xl font-extrabold leading-[1.15] mb-6 tracking-tight">
                    Institutional Grade<br>Security<br>Infrastructure.
                </h1>
                <p class="text-blue-100 text-lg leading-relaxed max-w-sm font-light">
                    Accessing the central core of architectural security systems. All actions are cryptographically signed.
                </p>
            </div>
            
            <div class="mt-auto pt-8 border-t border-white/20">
                <div class="flex gap-6 text-[10px] text-white uppercase tracking-widest font-semibold mb-6">
                    <span class="flex items-center gap-2"><i class="fa-solid fa-shield-halved text-blue-300"></i> Secure Access</span>
                    <span class="flex items-center gap-2"><i class="fa-solid fa-chart-line text-blue-300"></i> Real-Time Monitoring</span>
                    <span class="flex items-center gap-2"><i class="fa-solid fa-sliders text-blue-300"></i> Full System Control</span>
                </div>
                <div class="flex justify-between text-[10px] text-blue-300 uppercase tracking-widest font-semibold">
                    <span>System Status: Operational</span>
                    <span>Lat: 40.7128° N | Lon: 74.0060° W</span>
                </div>
            </div>
        </div>

        <div class="w-full lg:w-1/2 bg-white flex flex-col items-center justify-center relative p-8 sm:p-12 md:p-20">
            
            <div class="w-full max-w-md mx-auto flex flex-col items-center">
                
                <h2 class="text-3xl font-extrabold text-gray-900 text-center mb-3">Enter Verification Code</h2>
                <p class="text-sm text-gray-500 text-center mb-10 max-w-xs leading-relaxed">
                    We have sent a 6-digit code to your email. Please enter it below to confirm your identity.
                </p>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm mb-6 shadow-sm flex items-center gap-2 w-full">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('superadmin.verify') }}" method="POST" class="w-full flex flex-col items-center">
                    @csrf
                    
                    <div class="flex gap-2 sm:gap-3 mb-10 justify-center w-full" id="otp-container">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#1e5fa7] focus:bg-white transition-all">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#1e5fa7] focus:bg-white transition-all">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#1e5fa7] focus:bg-white transition-all">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#1e5fa7] focus:bg-white transition-all">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#1e5fa7] focus:bg-white transition-all">
                        <input type="text" maxlength="1" class="otp-input w-12 h-14 sm:w-14 sm:h-16 bg-gray-50 border border-gray-200 rounded-xl text-center text-2xl font-bold text-gray-800 focus:outline-none focus:ring-2 focus:ring-[#1e5fa7] focus:bg-white transition-all">
                    </div>
                    
                    <input type="hidden" name="verification_code" id="full-code">

                    <button type="submit" class="w-full py-4 bg-[#173a70] text-white font-semibold text-sm rounded-xl shadow-sm hover:bg-[#112a52] transition-all flex justify-center items-center gap-2 mb-8">
                        Verify & Login <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>

                <div class="flex flex-col items-center gap-4">
                    <form action="{{ route('superadmin.sendcode') }}" method="POST">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('superadmin_attempt_email') }}">
                        <button type="submit" class="text-gray-800 text-xs font-extrabold tracking-wider uppercase hover:text-black transition-colors">
                            Resend Code
                        </button>
                    </form>
                    <a href="{{ route('login.page') }}" class="text-gray-400 text-xs hover:text-gray-600 transition-colors">
                        Use another method
                    </a>
                </div>
            </div>

            <div class="absolute bottom-8 w-full text-center text-[10px] font-bold text-gray-300 uppercase tracking-widest">
                <i class="fa-solid fa-lock mr-1"></i> All access is monitored and logged
            </div>
        </div>
    </div>

    <script>
        const inputs = document.querySelectorAll('.otp-input');
        const hiddenInput = document.getElementById('full-code');

        inputs.forEach((input, index) => {
            // Auto-advance to the next field when typing
            input.addEventListener('input', (e) => {
                e.target.value = e.target.value.replace(/[^0-9]/g, ''); // Allow only numbers

                if (e.target.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
                updateHiddenInput();
            });

            // Handle backspace to go to the previous field
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && e.target.value === '' && index > 0) {
                    inputs[index - 1].focus();
                }
            });

            // Allow user to paste a 6-digit code directly
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pastedData = e.clipboardData.getData('text').replace(/[^0-9]/g, '').split('');
                
                if (pastedData.length > 0) {
                    inputs.forEach((input, i) => {
                        if (pastedData[i]) {
                            input.value = pastedData[i];
                        }
                    });
                    
                    // Focus on the last filled input
                    const lastIndex = Math.min(pastedData.length - 1, inputs.length - 1);
                    inputs[lastIndex].focus();
                    
                    updateHiddenInput();
                }
            });
        });

        // Combine the 6 boxes into one string before form submission
        function updateHiddenInput() {
            let code = '';
            inputs.forEach(input => code += input.value);
            hiddenInput.value = code;
        }
    </script>
</body>
</html>