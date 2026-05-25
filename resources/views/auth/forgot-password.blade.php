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
<body class="bg-gray-50 min-h-screen flex">

    <div class="hidden lg:flex lg:w-1/2 bg-[#1e4ea1] text-white p-16 flex-col justify-between relative overflow-hidden">
        
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
        </div>

        <div class="grid grid-cols-2 gap-6 z-10">
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

        <div class="absolute -bottom-20 -left-20 w-96 h-96 bg-blue-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
    </div>

    <div class="w-full lg:w-1/2 flex flex-col justify-between bg-white px-8 py-12 md:p-16 lg:p-24">
        
        <div></div>

        <div class="max-w-md w-full mx-auto text-center">
            
            <div class="inline-flex items-center justify-center w-14 h-10 bg-blue-100 rounded-full text-[#1e4ea1] mb-6">
                <i class="fa-solid fa-key text-md transform -rotate-45"></i>
            </div>

            <h2 class="text-3xl font-bold text-slate-900 mb-3 tracking-tight">Forgot Password?</h2>
            <p class="text-gray-500 text-sm leading-relaxed mb-8 px-4">
                No problem. Enter the email associated with your account and we'll send you a reset link.
            </p>

            @if (session('status'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm text-left">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm text-left">
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
                    <label for="email" class="block text-xs font-semibold text-slate-700 uppercase tracking-wider mb-2">
                        Institutional Email
                    </label>
                    <div class="relative rounded-lg shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-gray-400">
                            <i class="fa-regular fa-envelope"></i>
                        </div>
                        <input 
                            type="email" 
                            name="email" 
                            id="email" 
                            value="{{ old('email') }}"
                            required 
                            placeholder="Name@University.edu" 
                            class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all text-sm"
                        >
                    </div>
                </div>

                <button 
                    type="submit" 
                    class="w-full flex items-center justify-center space-x-2 py-3.5 px-4 bg-[#11357c] hover:bg-[#1a4494] text-white text-sm font-medium rounded-xl shadow-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all group"
                >
                    <span>Send Reset Link</span>
                    <i class="fa-solid fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform"></i>
                </button>
            </form>

            <div class="mt-8">
                <a href="{{ route('login.page') }}" class="inline-flex items-center space-x-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>I remember my password</span>
                </a>
            </div>
        </div>

        <div class="flex items-center justify-center space-x-6 text-[10px] font-bold tracking-widest text-gray-400 uppercase mt-12">
            <a href="#" class="hover:text-gray-600 transition-colors">Privacy Policy</a>
            <span>•</span>
            <a href="#" class="hover:text-gray-600 transition-colors">Support</a>
        </div>
    </div>

</body>
</html>