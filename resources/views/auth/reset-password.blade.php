<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Exam - Reset Password</title>
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
            <div class="max-w-md w-full space-y-6">
                
                <div class="text-center space-y-2">
                    <div class="w-12 h-12 bg-blue-50 text-[#1E4277] rounded-full flex items-center justify-center mx-auto text-xl">
                        <i class="fas fa-key"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-800 tracking-tight">Set new password</h2>
                    <p class="text-sm text-gray-500 font-normal">
                        Please enter your new security credentials below to update your account.
                    </p>
                </div>

                @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-lg text-sm space-y-1">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Institutional Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="far fa-envelope"></i>
                            </span>
                            <input type="email" name="email" value="{{ request()->email }}" required placeholder="Name@University.edu"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-md focus:outline-none focus:border-[#1E4277] text-sm text-gray-700">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">New Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-lock"></i>
                            </span>
                            <input type="password" name="password" required placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-md focus:outline-none focus:border-[#1E4277] text-sm text-gray-700">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-xs font-bold text-gray-500 uppercase tracking-wider">Confirm New Password</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                                <i class="fas fa-check-double"></i>
                            </span>
                            <input type="password" name="password_confirmation" required placeholder="••••••••"
                                class="w-full pl-10 pr-4 py-2.5 border border-gray-200 rounded-md focus:outline-none focus:border-[#1E4277] text-sm text-gray-700">
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#1E4277] hover:bg-[#16335C] text-white font-medium py-3 px-4 rounded-md transition duration-200 shadow-sm text-sm cursor-pointer">
                            Update Password <i class="fas fa-arrow-right text-xs ml-1.5 opacity-80"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center pt-2">
                    <a href="{{ route('login.page') }}" class="text-sm text-gray-500 hover:text-gray-900 transition font-medium">
                        <i class="fas fa-arrow-left text-xs mr-1.5 opacity-80"></i> Back to Login
                    </a>
                </div>

            </div>
        </div>

    </div>

</body>
</html>