<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam - Account Created Successfully</title>
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

        <div class="w-full md:w-[55%] bg-white flex flex-col justify-between items-center p-8 relative">
            
            <div class="hidden md:block"></div>

            <div class="max-w-md w-full text-center space-y-6 my-auto">
                
                <div class="relative inline-block mx-auto">
                    <div class="w-20 h-20 bg-blue-50 rounded-full flex items-center justify-center">
                        <i class="fas fa-shield-alt text-3xl text-[#1E4277]"></i>
                    </div>
                    <div class="absolute bottom-1 right-1 bg-[#22C55E] border-4 border-white text-white rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                        <i class="fas fa-check text-[9px]"></i>
                    </div>
                </div>

                <div>
                    <span class="bg-[#E8F9F1] text-[#22C55E] text-xs font-bold tracking-wider uppercase px-4 py-1.5 rounded-full">
                        Success
                    </span>
                </div>

                <div class="space-y-3">
                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight leading-tight">Account Created Successfully</h2>
                    <p class="text-sm text-gray-500 font-normal leading-relaxed">
                        Your account has been created using <br>
                        <span class="font-semibold text-gray-700">{{ session('registered_email', 'name@university.edu') }}</span>. 
                        You can now sign in and start your examination journey.
                    </p>
                </div>

                @if(session('registered_institutional_id'))
                    <div class="bg-[#F4F6F9] border border-gray-100 rounded-lg p-4 text-left">
                        <p class="text-[10px] font-bold tracking-wider text-gray-400 uppercase mb-1">Your Institutional ID</p>
                        <p class="text-lg font-bold text-[#1E4277] tracking-wide">{{ session('registered_institutional_id') }}</p>
                        <p class="text-xs text-gray-500 mt-1">Save this ID -- you'll use it to identify yourself on campus.</p>
                    </div>
                @endif

                <div class="bg-[#F4F6F9] border border-gray-100 rounded-lg p-4 flex items-start space-x-3 text-left">
                    <i class="far fa-envelope text-[#1E4277] mt-0.5 text-base"></i>
                    <p class="text-xs text-gray-500 leading-normal font-normal">
                        A verification email has been sent to your inbox. Please click the link to activate your full feature set.
                    </p>
                </div>

                <div class="pt-2">
                    <a href="{{ route('login.page') }}" class="w-full flex items-center justify-center bg-[#0F2963] hover:bg-[#0A1D47] text-white font-medium py-3 px-4 rounded-md transition duration-200 shadow-sm text-sm">
                        Go to Login <i class="fas fa-arrow-right text-xs ml-2 opacity-90"></i>
                    </a>
                </div>

                <div class="space-y-2 pt-1">
                    <form method="POST" action="#" id="resend-verification-form">
                        @csrf
                        <button type="submit" class="text-sm text-blue-600 hover:text-blue-800 font-medium transition cursor-pointer bg-transparent border-none outline-none">
                            Resend Verification Email
                        </button>
                    </form>
                    <p class="text-xs text-gray-400 font-light">
                        Didn't receive the email? Check your spam folder.
                    </p>
                </div>

            </div>

            <div class="flex items-center space-x-4 text-xs text-gray-400 font-light pt-6">
                <a href="#" class="hover:text-gray-600 transition">Privacy Policy</a>
                <span class="text-gray-200">•</span>
                <a href="#" class="hover:text-gray-600 transition">Terms of Service</a>
                <span class="text-gray-200">•</span>
                <a href="#" class="hover:text-gray-600 transition">Support Center</a>
            </div>
        </div>

    </div>

</body>
</html>