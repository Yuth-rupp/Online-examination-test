<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 h-screen font-sans overflow-hidden">
    <div class="flex h-full w-full">

        <div class="hidden lg:flex lg:w-1/2 bg-[#255296] text-white flex-col justify-between p-12 relative">
            <div class="flex items-center gap-3 text-2xl font-bold tracking-wide">
                <i class="fa-solid fa-graduation-cap"></i> Online Exam
            </div>

            <div class="flex flex-col gap-10 mt-20 max-w-md">
                <div class="flex gap-5">
                    <div class="bg-white/10 w-12 h-12 rounded-lg flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-1">Secure Access</h3>
                        <p class="text-blue-200 text-sm leading-relaxed">Advanced encryption and hardware-level security protocols for every session.</p>
                    </div>
                </div>

                <div class="flex gap-5">
                    <div class="bg-white/10 w-12 h-12 rounded-lg flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-chart-line"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-1">Real-Time Monitoring</h3>
                        <p class="text-blue-200 text-sm leading-relaxed">Live oversight of all architectural systems and diagnostic telemetry.</p>
                    </div>
                </div>

                <div class="flex gap-5">
                    <div class="bg-white/10 w-12 h-12 rounded-lg flex items-center justify-center text-xl shrink-0">
                        <i class="fa-solid fa-sliders"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-lg mb-1">Full System Control</h3>
                        <p class="text-blue-200 text-sm leading-relaxed">Granular command over internal infrastructures and global parameters.</p>
                    </div>
                </div>
            </div>

            <div class="text-[10px] text-blue-200/60 uppercase tracking-widest font-semibold">
                Architectural Security Systems © 2026
            </div>
        </div>

        <div class="w-full lg:w-1/2 bg-white flex flex-col items-center justify-center relative p-8 sm:p-12 md:p-20">
            <div class="w-full max-w-md mx-auto text-center">
                <div class="inline-flex items-center justify-center w-14 h-10 bg-blue-50 rounded-full text-[#1e3a8a] mb-6 shadow-sm">
                    <i class="fa-solid fa-key text-md transform -rotate-45"></i>
                </div>

                <h2 class="text-3xl font-bold text-gray-900 mb-3 tracking-tight">Super Admin Recovery</h2>
                <p class="text-gray-500 text-sm leading-relaxed mb-8 px-4">
                    Enter your administrative email to receive a secure password reset link.
                </p>

                @if (session('status'))
                    <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm text-left">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded text-sm text-left shadow-sm">
                        <ul class="list-disc pl-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('password.email') }}" method="POST" class="text-left">
                    @csrf
                    <div class="mb-6">
                        <label for="email" class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">
                            Admin Email
                        </label>
                        <div class="relative rounded-lg shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                                <i class="fa-solid fa-at"></i>
                            </div>
                            <input type="email" name="email" id="email" value="{{ old('email') }}" required placeholder="superadmin@system.com" class="block w-full pl-11 pr-4 py-3.5 bg-white border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-[#1e3a8a] focus:border-transparent transition-all text-sm">
                        </div>
                    </div>

                    <button type="submit" class="w-full flex items-center justify-center space-x-2 py-3.5 px-4 bg-[#1e3a8a] hover:bg-[#152c6b] text-white text-sm font-medium rounded-xl shadow-md transition-all group">
                        <span>Send Reset Link</span>
                        <i class="fa-solid fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <div class="text-center mt-8">
                    <a href="{{ route('login.page') }}" class="text-gray-500 text-sm font-semibold hover:text-gray-800 transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> I remember my password
                    </a>
                </div>
            </div>

            <div class="absolute bottom-8 w-full text-center flex justify-center gap-6 text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                <a href="#" class="hover:text-gray-600 transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-gray-600 transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-gray-600 transition-colors">System Status</a>
            </div>
        </div>
    </div>
</body>
</html>