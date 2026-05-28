<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - Secure Access</title>
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

            <div class="mt-auto pt-10 border-t border-white/10 text-[10px] text-blue-200/60 uppercase tracking-widest font-semibold">
                Architectural Security Systems © 2026
            </div>
        </div>

        <div class="w-full lg:w-1/2 bg-white flex flex-col items-center justify-center relative p-8 sm:p-12 md:p-20">

            <div class="w-full max-w-md mx-auto">

                <div class="text-center mb-10">
                    <h2 class="text-3xl font-bold text-gray-900 mb-3">Secure Super Admin Access</h2>
                    <p class="text-sm text-gray-500">
                        Enter your email to receive a secure login code
                    </p>
                </div>

                @if($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-3 rounded text-sm mb-6 shadow-sm flex items-center gap-2">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('superadmin.sendcode') }}" method="POST">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-2">Admin Email</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400">
                                <i class="fa-solid fa-at"></i>
                            </span>
                            <input type="email" name="email" required class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#1e3a8a] focus:border-transparent text-gray-700 transition-all placeholder-gray-400" placeholder="superadmin@system.com">
                        </div>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-[#1e3a8a] text-white font-semibold text-sm rounded-xl shadow-sm hover:bg-[#152c6b] transition-all flex justify-center items-center gap-2 group">
                        <span>Send Login Code</span>
                        <i class="fa-solid fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                    </button>
                </form>

                <div class="mt-8 bg-gray-50 border-l-4 border-gray-800 p-4 rounded-r-lg flex gap-3 items-start">
                    <i class="fa-solid fa-circle-info text-gray-800 mt-0.5 text-sm"></i>
                    <p class="text-xs text-gray-600 leading-relaxed">
                        This system uses passwordless authentication for enhanced security. You will receive a unique, single-use code via your encrypted administrative email.
                    </p>
                </div>

                <div class="text-center mt-8">
                    <a href="{{ route('login.page') }}" class="text-gray-500 text-sm font-semibold hover:text-gray-800 transition-colors flex items-center justify-center gap-2">
                        <i class="fa-solid fa-arrow-left"></i> Back to login
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