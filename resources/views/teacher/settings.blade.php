<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ExamSystem – Settings</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{font-family:'Inter',system-ui,sans-serif;-webkit-font-smoothing:antialiased}

        /* SCROLLBAR */
        ::-webkit-scrollbar{width:4px}
        ::-webkit-scrollbar-track{background:transparent}
        ::-webkit-scrollbar-thumb{background:#CBD5E1;border-radius:99px}

        /* SIDEBAR */
        .nl{display:flex;align-items:center;gap:10px;padding:8px 10px;border-radius:10px;text-decoration:none;font-size:13px;font-weight:500;color:#64748B;transition:all .18s}
        .nl:hover{background:#F8FAFC;color:#1E293B}
        .nl.act{background:#EFF6FF;color:#2563EB;font-weight:700;border:1px solid #BFDBFE}
        .ni{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:13px;flex-shrink:0;transition:all .18s}
        .nl.act .ni{background:#2563EB;color:#fff}
        .nl:hover .ni{background:#F1F5F9}

        /* ANIMATIONS */
        @keyframes pdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.4;transform:scale(.7)}}
        .ld{animation:pdot 1.5s infinite}
        @keyframes fu{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .fu{animation:fu .35s ease both}
        @keyframes spin{from{transform:rotate(0deg)}to{transform:rotate(360deg)}}
        .spin{animation:spin .7s linear infinite}
        @keyframes tin{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
        @keyframes avatarGlow{0%,100%{box-shadow:0 0 0 4px rgba(79,70,229,.2)}50%{box-shadow:0 0 0 8px rgba(79,70,229,.08)}}
        .aglow{animation:avatarGlow 2.5s ease infinite}

        /* TOAST */
        #tbox{position:fixed;bottom:22px;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:8px;align-items:center;pointer-events:none}
        .toast{display:flex;align-items:center;gap:9px;color:#fff;border-radius:14px;padding:11px 18px;font-size:12px;font-weight:700;box-shadow:0 10px 30px rgba(0,0,0,.22);animation:tin .3s ease;min-width:200px;pointer-events:auto;white-space:nowrap}

        /* INPUT FIELDS */
        .fld{width:100%;padding:12px 16px;background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:12px;font-size:13px;font-weight:500;color:#1E293B;outline:none;transition:all .18s}
        .fld:focus{background:#fff;border-color:#4F46E5;box-shadow:0 0 0 3px rgba(79,70,229,.1)}
        .fld:disabled{background:#F1F5F9;color:#94A3B8;cursor:not-allowed}
        .fld::placeholder{color:#94A3B8;font-weight:400}

        /* CARD */
        .card{background:#fff;border-radius:20px;border:1.5px solid #F1F5F9;box-shadow:0 1px 3px rgba(0,0,0,.05),0 4px 12px rgba(0,0,0,.04);overflow:hidden}
        .card:hover{box-shadow:0 4px 20px rgba(0,0,0,.07)}

        /* TOGGLE */
        .tog-track{width:48px;height:26px;border-radius:99px;transition:background .25s;cursor:pointer;position:relative;flex-shrink:0}
        .tog-thumb{width:20px;height:20px;background:#fff;border-radius:50%;position:absolute;top:3px;left:3px;transition:transform .25s;box-shadow:0 1px 4px rgba(0,0,0,.2)}

        /* HIGH CONTRAST */
        .high-contrast-mode{background-color:#030712!important;color:#F9FAFB!important}
        .high-contrast-mode .card,.high-contrast-mode aside,.high-contrast-mode header,.high-contrast-mode .bg-white{background-color:#111827!important;border-color:#374151!important;color:#F9FAFB!important}
        .high-contrast-mode .fld{background:#1F2937!important;color:#fff!important;border-color:#4B5563!important}
        .high-contrast-mode .nl{color:#9CA3AF!important}
        .high-contrast-mode .nl:hover{background:#1F2937!important;color:#fff!important}

        /* MODAL */
        @keyframes mIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
        .mIn{animation:mIn .2s ease}
    </style>
    <script>
        if(localStorage.getItem('high-contrast-enabled')==='true') document.documentElement.classList.add('high-contrast-mode');
    </script>
</head>

<body id="appBody" class="bg-slate-100 text-slate-800 min-h-screen overflow-x-hidden">

<div class="flex h-screen overflow-hidden">

{{-- ═══════════════════════ SIDEBAR ═══════════════════════ --}}
<aside class="w-[260px] bg-white border-r border-slate-100 flex flex-col flex-shrink-0 h-screen z-20 shadow-sm">
    <a href="{{ route('teacher.dashboard') }}"
       class="h-16 flex items-center px-4 gap-3 border-b border-slate-100 hover:opacity-90 transition-opacity flex-shrink-0">
        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-white flex-shrink-0"
             style="background:linear-gradient(135deg,#2563EB,#1E40AF);box-shadow:0 3px 10px rgba(37,99,235,.4)">
            <i class="fa-solid fa-graduation-cap text-sm"></i>
        </div>
        <span class="font-black text-[16px] text-slate-900 tracking-tight">ExamSystem</span>
    </a>

    <nav class="flex-1 px-2.5 py-3 space-y-0.5 overflow-y-auto">
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-2 pt-1 pb-1.5">Menu</p>
        <a href="{{ route('teacher.dashboard') }}"        class="nl"><span class="ni"><i class="fa-solid fa-house text-xs"></i></span><span>Dashboard</span></a>
        <a href="{{ route('teacher.question-bank') }}"   class="nl"><span class="ni"><i class="fa-solid fa-database text-xs"></i></span><span>Question Bank</span></a>
        <a href="{{ route('teacher.monitoring.show') }}" class="nl"><span class="ni"><i class="fa-solid fa-display text-xs"></i></span><span>Monitoring</span></a>
        <a href="{{ route('teacher.grading.queue') }}"   class="nl"><span class="ni"><i class="fa-solid fa-pen-to-square text-xs"></i></span><span>Grading</span></a>
        <a href="{{ route('teacher.analytics') }}"       class="nl"><span class="ni"><i class="fa-solid fa-chart-line text-xs"></i></span><span>Analytics</span></a>
        <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest px-2 pt-3 pb-1.5">Account</p>
        <a href="{{ route('teacher.settings') }}"        class="nl act"><span class="ni"><i class="fa-solid fa-gear text-xs"></i></span><span>Settings</span></a>
    </nav>

    <div class="p-2.5 border-t border-slate-100 flex-shrink-0">
        <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl">
            <div class="w-8 h-8 rounded-full overflow-hidden border-2 border-slate-200 flex-shrink-0">
                <img src="https://api.dicebear.com/7.x/bottts/svg?seed={{ Auth::user()->full_name ?? 'I' }}" class="w-full h-full object-cover">
            </div>
            <div class="min-w-0">
                <p class="text-xs font-black text-slate-900 truncate">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                <p class="text-[10px] text-slate-400">Senior Faculty</p>
            </div>
        </div>
    </div>
</aside>

{{-- ═══════════════════════ MAIN ═══════════════════════ --}}
<div class="flex-1 flex flex-col min-w-0 overflow-hidden">

    {{-- HEADER --}}
    <div class="flex-shrink-0" style="background:linear-gradient(135deg,#0F172A 0%,#1E3A5F 55%,#312E81 100%)">
        <div class="px-6 py-4 flex items-center gap-4">
            <div class="flex-1 min-w-0">
                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest mb-0.5">Teacher Portal</p>
                <h1 class="text-[16px] font-black text-white tracking-tight">Account Settings</h1>
                <p class="text-[10px] text-white/50 mt-0.5">Manage your profile, security, and preferences</p>
            </div>
            <div class="flex items-center gap-2.5 flex-shrink-0">
                <div class="px-3 py-2 rounded-xl hidden lg:block" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <p class="text-[9px] font-bold text-white/40 uppercase tracking-wider mb-0.5">Time</p>
                    <p class="text-[12px] font-black text-white tabular-nums" id="lc">--:--:--</p>
                </div>
                <div class="flex items-center gap-1.5 px-2.5 py-2 rounded-xl" style="background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.12)">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 ld"></span>
                    <span class="text-[10px] font-bold text-white/60">Online</span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="flex items-center gap-1.5 text-[11px] font-black px-3 py-2 rounded-xl transition-all"
                            style="background:rgba(239,68,68,.2);border:1px solid rgba(239,68,68,.4);color:#FCA5A5">
                        <i class="fa-solid fa-arrow-right-from-bracket text-[10px]"></i> Sign Out
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- SCROLL AREA --}}
    <div class="flex-1 overflow-y-auto bg-slate-100 p-5">
    <div class="max-w-5xl mx-auto">

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- ══ LEFT COLUMN ══ --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Profile Card --}}
        <div class="card fu" style="animation-delay:.04s">
            <div class="px-6 py-4 border-b border-slate-100" style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-600" style="background:#EEF2FF">
                        <i class="fa-solid fa-user text-xs"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-slate-900">Profile Information</h2>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5 ml-9">Update your display name and contact details</p>
            </div>

            <div class="p-6">
            <form action="{{ route('teacher.settings.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
            @csrf

                {{-- Avatar section --}}
                <div class="flex items-center gap-6 mb-6 pb-6" style="border-bottom:1.5px dashed #F1F5F9">
                    <div class="relative flex-shrink-0">
                        {{-- Glow ring --}}
                        <div class="w-24 h-24 rounded-2xl aglow overflow-hidden border-2 border-indigo-200">
                            <img id="avatarPreview"
                                 src="{{ Auth::user()->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed='.(Auth::user()->full_name ?? 'I') }}"
                                 class="w-full h-full object-cover" alt="Profile">
                        </div>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        <input type="hidden" id="removeAvatarFlag" name="remove_avatar" value="0">
                        <button type="button" onclick="document.getElementById('avatarInput').click()"
                                class="absolute -bottom-2 -right-2 w-8 h-8 rounded-xl flex items-center justify-center text-white text-xs border-2 border-white shadow-lg transition-all hover:scale-110"
                                style="background:linear-gradient(135deg,#4F46E5,#2563EB)">
                            <i class="fa-solid fa-camera"></i>
                        </button>
                    </div>

                    <div>
                        <p class="text-sm font-black text-slate-900 mb-1">{{ Auth::user()->full_name ?? 'Yun Dalin' }}</p>
                        <p class="text-[11px] text-slate-400 mb-3">Senior Faculty · ExamSystem</p>
                        <div class="flex gap-2">
                            <button type="button" onclick="document.getElementById('avatarInput').click()"
                                    class="flex items-center gap-1.5 text-[11px] font-black px-3 py-2 rounded-xl transition-all"
                                    style="background:#EEF2FF;color:#4338CA;border:1px solid #C7D2FE">
                                <i class="fa-solid fa-arrow-up-from-bracket text-[10px]"></i> Upload Photo
                            </button>
                            <button type="button" onclick="removeAvatar()"
                                    class="flex items-center gap-1.5 text-[11px] font-bold px-3 py-2 rounded-xl transition-all"
                                    style="background:#F8FAFC;color:#64748B;border:1px solid #E2E8F0">
                                <i class="fa-solid fa-trash text-[10px]"></i> Remove
                            </button>
                        </div>
                        <p class="text-[9px] text-slate-400 mt-2">JPG, GIF or PNG · Max 800KB</p>
                    </div>
                </div>

                {{-- Name + Email --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Full Name</label>
                        <div class="relative">
                            <i class="fa-solid fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                            <input type="text" name="full_name" class="fld pl-9"
                                   value="{{ Auth::user()->full_name ?? 'Yun Dalin' }}"
                                   placeholder="Your full name" required>
                        </div>
                    </div>
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">University Email</label>
                        <div class="relative">
                            <i class="fa-solid fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                            <input type="email" name="email" class="fld pl-9"
                                   value="{{ Auth::user()->email ?? 'dalin@university.edu' }}"
                                   placeholder="your@university.edu" required>
                        </div>
                    </div>
                </div>

                {{-- University ID (read-only) --}}
                <div class="mb-6">
                    <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">
                        University ID <span class="normal-case font-normal">(read-only)</span>
                    </label>
                    <div class="relative">
                        <i class="fa-solid fa-id-badge absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                        <input type="text" class="fld pl-9 w-full sm:w-1/2"
                               value="{{ Auth::user()->institutional_id ?? '#UNI-8842-1092' }}" disabled>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4" style="border-top:1.5px solid #F1F5F9">
                    <p class="text-[10px] text-slate-400 flex items-center gap-1.5">
                        <i class="fa-solid fa-circle-info text-slate-300 text-xs"></i>
                        Changes are applied immediately
                    </p>
                    <div class="flex gap-2">
                        <button type="button" onclick="window.location.reload()"
                                class="text-[11px] font-bold px-4 py-2 rounded-xl transition-all"
                                style="background:#F8FAFC;border:1.5px solid #E2E8F0;color:#475569">
                            Cancel
                        </button>
                        <button type="submit" id="saveBtn"
                                class="flex items-center gap-2 text-[11px] font-black px-5 py-2 rounded-xl text-white transition-all"
                                style="background:linear-gradient(135deg,#4F46E5,#2563EB);box-shadow:0 4px 14px rgba(79,70,229,.35)">
                            <i class="fa-solid fa-floppy-disk" id="saveBtnIcon"></i>
                            <span id="saveBtnLabel">Save Changes</span>
                        </button>
                    </div>
                </div>

            </form>
            </div>
        </div>

        {{-- Password Card --}}
        <div class="card fu" style="animation-delay:.08s">
            <div class="px-6 py-4 border-b border-slate-100" style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-amber-600" style="background:#FEF3C7">
                        <i class="fa-solid fa-lock text-xs"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-slate-900">Change Password</h2>
                </div>
                <p class="text-[10px] text-slate-400 mt-0.5 ml-9">Keep your account secure with a strong password</p>
            </div>

            <div class="p-6">
            <form action="{{ route('teacher.settings.update.password') }}" method="POST" id="pwForm">
            @csrf
                <div class="space-y-4">
                    <div>
                        <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Current Password</label>
                        <div class="relative">
                            <i class="fa-solid fa-lock absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                            <input type="password" name="current_password" id="pw1" class="fld pl-9 pr-10" placeholder="••••••••">
                            <button type="button" onclick="togglePw('pw1','eye1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors">
                                <i id="eye1" class="fa-solid fa-eye text-xs"></i>
                            </button>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">New Password</label>
                            <div class="relative">
                                <i class="fa-solid fa-key absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                                <input type="password" name="password" id="pw2" class="fld pl-9 pr-10" placeholder="Min 8 characters" oninput="checkStrength(this.value)">
                                <button type="button" onclick="togglePw('pw2','eye2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors">
                                    <i id="eye2" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                            {{-- Strength bar --}}
                            <div class="mt-2 h-1.5 rounded-full overflow-hidden" style="background:#F1F5F9">
                                <div id="strengthBar" class="h-full rounded-full transition-all duration-400" style="width:0%"></div>
                            </div>
                            <p id="strengthLabel" class="text-[9px] font-bold mt-1 text-slate-400"></p>
                        </div>
                        <div>
                            <label class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Confirm New Password</label>
                            <div class="relative">
                                <i class="fa-solid fa-check-double absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-xs pointer-events-none"></i>
                                <input type="password" name="password_confirmation" id="pw3" class="fld pl-9 pr-10" placeholder="Repeat password">
                                <button type="button" onclick="togglePw('pw3','eye3')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-300 hover:text-slate-500 transition-colors">
                                    <i id="eye3" class="fa-solid fa-eye text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end mt-5 pt-4" style="border-top:1.5px solid #F1F5F9">
                    <button type="submit"
                            class="flex items-center gap-2 text-[11px] font-black px-5 py-2 rounded-xl text-white transition-all"
                            style="background:linear-gradient(135deg,#F59E0B,#D97706);box-shadow:0 4px 14px rgba(245,158,11,.3)">
                        <i class="fa-solid fa-key"></i> Update Password
                    </button>
                </div>
            </form>
            </div>
        </div>

        {{-- Accessibility --}}
        <div class="card fu" style="animation-delay:.12s">
            <div class="px-6 py-4 border-b border-slate-100" style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-purple-600" style="background:#F5F3FF">
                        <i class="fa-solid fa-eye text-xs"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-slate-900">Accessibility & Display</h2>
                </div>
            </div>
            <div class="p-6 space-y-4">

                {{-- High contrast --}}
                <div class="flex items-center justify-between p-4 rounded-xl" style="background:#F8FAFC;border:1.5px solid #F1F5F9">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#EDE9FE">
                            <i class="fa-solid fa-circle-half-stroke text-purple-600"></i>
                        </div>
                        <div>
                            <p class="text-[12px] font-black text-slate-900">High Contrast Mode</p>
                            <p class="text-[10px] text-slate-400 font-medium">Enhanced UI for visual accessibility</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] font-bold text-slate-400" id="contrastLabel">Off</span>
                        <div class="tog-track" id="togTrack" onclick="toggleContrast()"
                             style="background:#E2E8F0">
                            <div class="tog-thumb" id="togThumb"></div>
                        </div>
                    </div>
                </div>

                {{-- Language placeholder --}}
                <div class="flex items-center justify-between p-4 rounded-xl" style="background:#F8FAFC;border:1.5px solid #F1F5F9">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl flex items-center justify-center flex-shrink-0" style="background:#ECFDF5">
                            <i class="fa-solid fa-globe text-emerald-600"></i>
                        </div>
                        <div>
                            <p class="text-[12px] font-black text-slate-900">Interface Language</p>
                            <p class="text-[10px] text-slate-400 font-medium">Currently: English (US)</p>
                        </div>
                    </div>
                    <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-lg border border-emerald-200">EN</span>
                </div>

            </div>
        </div>

    </div>{{-- /left col --}}

    {{-- ══ RIGHT COLUMN ══ --}}
    <div class="space-y-5">

        {{-- Security & Privacy --}}
        <div class="card fu" style="animation-delay:.06s">
            <div class="px-5 py-4 border-b border-slate-100" style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-blue-600" style="background:#EFF6FF">
                        <i class="fa-solid fa-shield-halved text-xs"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-slate-900">Security</h2>
                </div>
            </div>
            <div class="p-5 space-y-3">

                {{-- Connected accounts --}}
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Connected Accounts</p>

                <div class="flex items-center justify-between p-3 rounded-xl" style="background:#F8FAFC;border:1px solid #F1F5F9">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#EFF6FF">
                            <i class="fa-brands fa-google text-blue-600 text-xs"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-700">Google SSO</span>
                    </div>
                    <span class="flex items-center gap-1 text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Connected
                    </span>
                </div>

                <div class="flex items-center justify-between p-3 rounded-xl" style="background:#F8FAFC;border:1px solid #F1F5F9">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center" style="background:#EFF6FF">
                            <i class="fa-brands fa-microsoft text-blue-600 text-xs"></i>
                        </div>
                        <span class="text-[11px] font-bold text-slate-700">Microsoft Azure</span>
                    </div>
                    <span class="flex items-center gap-1 text-[9px] font-black text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Connected
                    </span>
                </div>

                {{-- Docs --}}
                <div class="pt-2 space-y-2">
                    <button onclick="openModal('Privacy Policy','Your data is secured with end-to-end encryption and complies with global GDPR standards. No personal information is shared externally.','fa-shield-halved')"
                            class="flex items-center justify-between w-full p-3 rounded-xl text-left transition-all hover:bg-slate-50"
                            style="background:#F8FAFC;border:1px solid #F1F5F9">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-file-lines text-blue-500 text-sm"></i>
                            <span class="text-[11px] font-bold text-slate-700">Privacy Policy (GDPR)</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 text-[9px]"></i>
                    </button>
                    <button onclick="openModal('Data Usage Agreement','Your evaluation data is stored exclusively within your institution\'s private infrastructure. No analytics are sent to third parties.','fa-handshake')"
                            class="flex items-center justify-between w-full p-3 rounded-xl text-left transition-all hover:bg-slate-50"
                            style="background:#F8FAFC;border:1px solid #F1F5F9">
                        <div class="flex items-center gap-2">
                            <i class="fa-regular fa-handshake text-blue-500 text-sm"></i>
                            <span class="text-[11px] font-bold text-slate-700">Data Usage Agreement</span>
                        </div>
                        <i class="fa-solid fa-chevron-right text-slate-300 text-[9px]"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Support --}}
        <div class="card fu" style="animation-delay:.1s">
            <div class="px-5 py-4 border-b border-slate-100" style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-indigo-600" style="background:#EEF2FF">
                        <i class="fa-regular fa-circle-question text-xs"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-slate-900">Support</h2>
                </div>
            </div>
            <div class="p-5 space-y-3">
                <button type="button" onclick="openSupport()"
                        class="w-full flex items-center justify-center gap-2 text-[11px] font-black py-3 rounded-xl text-white transition-all"
                        style="background:linear-gradient(135deg,#4F46E5,#2563EB);box-shadow:0 4px 14px rgba(79,70,229,.25)">
                    <i class="fa-solid fa-headset"></i> Technical Support Helpdesk
                </button>
                <div class="grid grid-cols-2 gap-2">
                    <button onclick="openModal('Help Center','Search documentation, explore guides, or submit tickets to the admin team.','fa-circle-info')"
                            class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-[10px] font-bold transition-all hover:bg-slate-100"
                            style="background:#F8FAFC;border:1px solid #F1F5F9;color:#475569">
                        <i class="fa-solid fa-circle-info text-blue-500 text-xs"></i> Help Center
                    </button>
                    <button onclick="openModal('User Guides','Step-by-step manuals for managing questions, grading, and exporting results.','fa-book')"
                            class="flex items-center justify-center gap-1.5 py-2.5 rounded-xl text-[10px] font-bold transition-all hover:bg-slate-100"
                            style="background:#F8FAFC;border:1px solid #F1F5F9;color:#475569">
                        <i class="fa-solid fa-book text-blue-500 text-xs"></i> User Guides
                    </button>
                </div>
            </div>
        </div>

        {{-- System Alerts --}}
        <div class="card fu" style="animation-delay:.14s">
            <div class="px-5 py-4 border-b border-slate-100" style="background:linear-gradient(135deg,#FAFCFF,#F5F7FF)">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center text-red-600" style="background:#FEF2F2">
                            <i class="fa-solid fa-bell text-xs"></i>
                        </div>
                        <h2 class="text-[13px] font-black text-slate-900">System Alerts</h2>
                    </div>
                    <span class="text-[9px] font-black text-white bg-red-500 px-2 py-0.5 rounded-full">2</span>
                </div>
            </div>
            <div class="p-5">
                <div class="space-y-3">

                    <div class="flex gap-3 p-3 rounded-xl" style="background:#FEF2F2;border:1px solid #FECACA">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-red-600" style="background:#FEE2E2">
                            <i class="fa-solid fa-triangle-exclamation text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-red-900">Storage Quota Alert</p>
                            <p class="text-[9px] text-red-600 font-medium mt-0.5">2 hours ago · Action required</p>
                            <p class="text-[10px] text-red-700 font-medium mt-1">Storage is approaching 85% capacity.</p>
                        </div>
                    </div>

                    <div class="flex gap-3 p-3 rounded-xl" style="background:#ECFDF5;border:1px solid #A7F3D0">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-emerald-600" style="background:#D1FAE5">
                            <i class="fa-solid fa-shield-check text-xs"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-black text-emerald-900">Security Audit Passed</p>
                            <p class="text-[9px] text-emerald-600 font-medium mt-0.5">Yesterday · 0 threats detected</p>
                            <p class="text-[10px] text-emerald-700 font-medium mt-1">All systems secure and operational.</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Danger zone --}}
        <div class="card fu" style="animation-delay:.18s;border-color:#FECACA">
            <div class="px-5 py-4 border-b" style="background:linear-gradient(135deg,#FFF5F5,#FEF2F2);border-color:#FECACA">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-lg flex items-center justify-center text-red-600" style="background:#FEE2E2">
                        <i class="fa-solid fa-skull-crossbones text-xs"></i>
                    </div>
                    <h2 class="text-[13px] font-black text-red-900">Danger Zone</h2>
                </div>
            </div>
            <div class="p-5">
                <p class="text-[10px] text-slate-500 mb-3 leading-relaxed">Permanently delete your account and all associated data. This action <strong>cannot</strong> be undone.</p>
                <button type="button" onclick="confirmDelete()"
                        class="flex items-center gap-1.5 text-[11px] font-black px-4 py-2 rounded-xl transition-all"
                        style="background:#FEF2F2;border:1.5px solid #FECACA;color:#EF4444">
                    <i class="fa-solid fa-trash text-[10px]"></i> Delete My Account
                </button>
            </div>
        </div>

    </div>{{-- /right col --}}
    </div>{{-- /grid --}}

    <div class="h-4"></div>
    </div>{{-- /container --}}
    </div>{{-- /scroll --}}
</div>{{-- /main --}}
</div>{{-- /flex wrapper --}}

{{-- ── MODAL ── --}}
<div id="M" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4"
     style="background:rgba(15,23,42,.7);backdrop-filter:blur(4px)">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden mIn">
        <div class="flex items-center gap-3 px-6 py-4 border-b border-slate-100" style="background:#FAFCFF">
            <div class="w-9 h-9 rounded-xl flex items-center justify-center text-blue-600 flex-shrink-0" style="background:#EFF6FF">
                <i id="mIcon" class="fa-solid fa-shield-halved text-sm"></i>
            </div>
            <div class="flex-1">
                <h3 id="mTitle" class="text-[13px] font-black text-slate-900">Title</h3>
                <p class="text-[9px] font-bold text-blue-600 uppercase tracking-widest mt-0.5">ExamSystem Document</p>
            </div>
            <button onclick="closeModal()" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-400 hover:bg-slate-100 transition-colors">
                <i class="fa-solid fa-xmark text-sm"></i>
            </button>
        </div>
        <div class="p-6">
            <div class="rounded-xl p-4 max-h-64 overflow-y-auto" style="background:#F8FAFC;border:1.5px solid #F1F5F9">
                <p id="mBody" class="text-[12px] text-slate-600 leading-relaxed font-medium"></p>
            </div>
        </div>
        <div class="flex justify-end px-6 pb-5">
            <button onclick="closeModal()"
                    class="text-[11px] font-black px-5 py-2.5 rounded-xl text-white transition-all"
                    style="background:linear-gradient(135deg,#1E293B,#0F172A);box-shadow:0 4px 12px rgba(0,0,0,.2)">
                Acknowledged
            </button>
        </div>
    </div>
</div>

<div id="tbox"></div>

<script>
// ── CLOCK ──
(function tick(){ const el=document.getElementById('lc'); if(el) el.textContent=new Date().toLocaleTimeString('en-US',{hour12:false}); setTimeout(tick,1000); })();

// ── TOAST ──
function toast(m,t='success'){
    const c={success:'#10B981',info:'#4F46E5',warning:'#F59E0B',error:'#EF4444'};
    const i={success:'fa-circle-check',info:'fa-circle-info',warning:'fa-triangle-exclamation',error:'fa-circle-xmark'};
    const b=document.getElementById('tbox'),el=document.createElement('div');
    el.className='toast';el.style.background=c[t];
    el.innerHTML=`<i class="fa-solid ${i[t]}"></i>${m}`;
    b.appendChild(el);
    setTimeout(()=>{el.style.transition='all .3s';el.style.opacity='0';el.style.transform='translateY(8px)';setTimeout(()=>el.remove(),300)},3500);
}

// ── AVATAR ──
const FALLBACK = "https://api.dicebear.com/7.x/bottts/svg?seed={{ Auth::user()->full_name ?? 'I' }}";
function previewAvatar(inp){
    if(inp.files&&inp.files[0]){
        const r=new FileReader();
        r.onload=e=>{ document.getElementById('avatarPreview').src=e.target.result; document.getElementById('removeAvatarFlag').value='0'; };
        r.readAsDataURL(inp.files[0]);
    }
}
function removeAvatar(){
    if(confirm('Remove your profile photo?')){
        document.getElementById('avatarPreview').src=FALLBACK;
        document.getElementById('avatarInput').value='';
        document.getElementById('removeAvatarFlag').value='1';
    }
}

// ── PASSWORD TOGGLE ──
function togglePw(id,eyeId){
    const inp=document.getElementById(id);
    const eye=document.getElementById(eyeId);
    if(inp.type==='password'){ inp.type='text'; eye.className='fa-solid fa-eye-slash text-xs'; }
    else { inp.type='password'; eye.className='fa-solid fa-eye text-xs'; }
}

// ── PASSWORD STRENGTH ──
function checkStrength(v){
    const bar=document.getElementById('strengthBar');
    const lbl=document.getElementById('strengthLabel');
    let sc=0;
    if(v.length>=8)sc++;
    if(/[A-Z]/.test(v))sc++;
    if(/[0-9]/.test(v))sc++;
    if(/[^A-Za-z0-9]/.test(v))sc++;
    const lvl=['','Weak','Fair','Good','Strong'];
    const col=['','#EF4444','#F59E0B','#3B82F6','#10B981'];
    const pct=[0,25,50,75,100];
    bar.style.width=pct[sc]+'%';
    bar.style.background=col[sc];
    lbl.textContent=lvl[sc];
    lbl.style.color=col[sc];
}

// ── HIGH CONTRAST TOGGLE ──
const savedContrast=localStorage.getItem('high-contrast-enabled')==='true';
const togTrack=document.getElementById('togTrack');
const togThumb=document.getElementById('togThumb');
const contrastLabel=document.getElementById('contrastLabel');
function applyContrastUI(on){
    if(on){ togTrack.style.background='#4F46E5'; togThumb.style.transform='translateX(22px)'; contrastLabel.textContent='On'; }
    else  { togTrack.style.background='#E2E8F0'; togThumb.style.transform='translateX(0)';   contrastLabel.textContent='Off'; }
}
applyContrastUI(savedContrast);
function toggleContrast(){
    const on=!document.documentElement.classList.contains('high-contrast-mode');
    on ? document.documentElement.classList.add('high-contrast-mode') : document.documentElement.classList.remove('high-contrast-mode');
    localStorage.setItem('high-contrast-enabled',on);
    applyContrastUI(on);
    toast(on?'High Contrast Mode enabled':'High Contrast Mode disabled','info');
}

// ── MODAL ──
function openModal(title,body,icon='fa-shield-halved'){
    document.getElementById('mTitle').textContent=title;
    document.getElementById('mBody').textContent=body;
    document.getElementById('mIcon').className=`fa-solid ${icon} text-sm`;
    document.getElementById('M').classList.remove('hidden');
}
function closeModal(){ document.getElementById('M').classList.add('hidden'); }
document.getElementById('M').addEventListener('click',function(e){ if(e.target===this) closeModal(); });

// ── SUPPORT ──
function openSupport(){
    const msg=prompt('Describe the issue you encountered:');
    if(msg&&msg.trim()){
        const id='#'+Math.floor(1000+Math.random()*9000);
        toast(`Ticket ${id} submitted to helpdesk`,'success');
    }
}

// ── DANGER ZONE ──
function confirmDelete(){
    if(confirm('⚠️ WARNING: This will permanently delete your account and all data. Are you absolutely sure?')){
        toast('Account deletion request sent to admin','warning');
    }
}

// ── SAVE FORM ──
document.getElementById('profileForm').addEventListener('submit',function(e){
    const btn=document.getElementById('saveBtn');
    const icon=document.getElementById('saveBtnIcon');
    const lbl=document.getElementById('saveBtnLabel');
    icon.className='fa-solid fa-circle-notch spin';
    lbl.textContent='Saving…';
    btn.disabled=true;
    btn.style.opacity='.8';
    // Re-enable after submission (Laravel redirects, but just in case)
    setTimeout(()=>{ icon.className='fa-solid fa-floppy-disk'; lbl.textContent='Save Changes'; btn.disabled=false; btn.style.opacity='1'; },3000);
});
</script>
</body>
</html>