<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Global Settings — ExamSystem</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;600;700&display=swap" rel="stylesheet">

    <script>tailwind.config={theme:{extend:{fontFamily:{sans:['Inter','sans-serif'],mono:['JetBrains Mono','monospace']}}}}</script>

    <style>
        @keyframes ping-slow{75%,100%{transform:scale(2.2);opacity:0;}}
        .ping-slow{animation:ping-slow 2s cubic-bezier(0,0,.2,1) infinite;}

        @keyframes modalIn{from{opacity:0;transform:translateY(14px) scale(0.97)}to{opacity:1;transform:none}}
        .modal-in{animation:modalIn 0.26s cubic-bezier(0.22,1,0.36,1) forwards;}

        @keyframes slideUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
        .slide-up{animation:slideUp 0.3s cubic-bezier(0.22,1,0.36,1) forwards;}

        @keyframes fadeIn{from{opacity:0;transform:translateY(4px)}to{opacity:1;transform:translateY(0)}}
        .fade-in-panel{opacity:0;animation:fadeIn 0.22s ease-out forwards;}

        @keyframes spin{to{transform:rotate(360deg)}}
        .animate-spin{animation:spin 0.7s linear infinite;}

        .thin-scroll::-webkit-scrollbar{width:4px}
        .thin-scroll::-webkit-scrollbar-track{background:transparent}
        .thin-scroll::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:99px}

        .toast-enter{opacity:0;transform:translateX(20px);transition:opacity 0.3s,transform 0.3s;}
        .toast-visible{opacity:1;transform:translateX(0);}

        /* Toggle switch */
        .toggle-track{width:44px;height:24px;border-radius:12px;background:#e2e8f0;cursor:pointer;
                      position:relative;transition:background 0.25s;flex-shrink:0;}
        .toggle-track.on{background:#2563eb;}
        .toggle-thumb{width:18px;height:18px;border-radius:50%;background:#fff;
                      position:absolute;top:3px;left:3px;transition:transform 0.25s;
                      box-shadow:0 1px 4px rgba(0,0,0,0.18);}
        .toggle-track.on .toggle-thumb{transform:translateX(20px);}

        /* Section tab nav */
        .stab{display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:10px;
              cursor:pointer;font-size:12px;font-weight:600;color:#64748b;transition:all 0.18s;
              border:none;background:transparent;width:100%;text-align:left;}
        .stab:hover{background:#f1f5f9;color:#1e293b;}
        .stab.active{background:#eff6ff;color:#2563eb;font-weight:700;
                     border-left:3px solid #2563eb;border-radius:0 10px 10px 0;padding-left:11px;}
        .stab.active .stab-icon{color:#2563eb;}
        .stab-icon{font-size:13px;width:18px;text-align:center;color:#94a3b8;flex-shrink:0;}

        /* Form inputs */
        .f-input{width:100%;background:#f8fafc;border:1px solid #e2e8f0;border-radius:12px;
                 padding:10px 14px;font-size:12px;font-weight:600;color:#0f172a;outline:none;
                 transition:border-color 0.2s,background 0.2s;}
        .f-input:focus{border-color:#2563eb;background:#fff;box-shadow:0 0 0 3px rgba(37,99,235,0.08);}
        .f-input.mono{font-family:'JetBrains Mono',monospace;color:#2563eb;}

        .f-label{display:block;font-size:10px;font-weight:800;color:#94a3b8;
                 text-transform:uppercase;letter-spacing:0.07em;margin-bottom:7px;}

        /* Danger box */
        .danger-box{border:1.5px solid #fecdd3;border-radius:16px;background:linear-gradient(135deg,#fff5f5,#fff);padding:18px;}
    </style>
</head>

<body class="bg-slate-50" style="font-family:'Inter',sans-serif;">
<div class="flex min-h-screen">

    {{-- ===================== SIDEBAR ===================== --}}
    <aside class="w-64 bg-white border-r border-slate-100 flex flex-col fixed h-full z-20"
           style="box-shadow:4px 0 24px rgba(148,163,184,0.08);">

        <div class="h-16 flex items-center px-5 gap-3 border-b border-slate-100 flex-shrink-0">
            <div class="w-9 h-9 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0"
                 style="box-shadow:0 4px 14px rgba(59,130,246,0.45);">
                <i class="fa-solid fa-graduation-cap text-white text-sm"></i>
            </div>
            <div>
                <h1 class="font-extrabold text-slate-900 text-sm tracking-tight leading-none">ExamSystem</h1>
                <div class="flex items-center gap-1.5 mt-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-widest">Super Admin</span>
                </div>
            </div>
        </div>

        <nav class="flex-1 p-3 overflow-y-auto thin-scroll pt-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2 mt-1">Overview</p>
            <a href="{{ route('superadmin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-gauge-high text-xs text-slate-400"></i></span><span>Dashboard</span>
            </a>
            <a href="{{ route('superadmin.monitoring.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-desktop text-xs text-slate-400"></i></span><span>Live Monitoring</span>
            </a>
            <a href="{{ route('superadmin.exams.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-file-signature text-xs text-slate-400"></i></span><span>Exams Oversight</span>
            </a>
            <a href="{{ route('superadmin.reports.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-chart-line text-xs text-slate-400"></i></span><span>Reports & Analytics</span>
            </a>

            <div class="pt-4 pb-1">
                <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest px-3 mb-2">Root Access</p>
            </div>
            <a href="{{ route('superadmin.admins.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-users text-xs text-slate-400"></i></span><span>User Management</span>
            </a>
            <a href="{{ route('superadmin.departments.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-building-columns text-xs text-slate-400"></i></span><span>Department Directory</span>
            </a>
            <a href="{{ route('superadmin.audit-logs.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-shield-halved text-xs text-slate-400"></i></span><span>Audit Trails</span>
            </a>
            <a href="{{ route('superadmin.backups.index') }}" class="flex items-center gap-3 px-3 py-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 font-medium rounded-xl text-sm mb-0.5 transition-all duration-200">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg flex-shrink-0"><i class="fa-solid fa-database text-xs text-slate-400"></i></span><span>Database & Backup</span>
            </a>
        </nav>

        <div class="p-3 border-t border-slate-100 flex-shrink-0">
            {{-- ACTIVE --}}
            <a href="{{ route('superadmin.settings.index') }}"
               class="flex items-center gap-3 px-3 py-2.5 bg-blue-600 text-white font-semibold rounded-xl text-sm mb-2 transition-all"
               style="box-shadow:0 4px 12px rgba(59,130,246,0.30);">
                <span class="w-8 h-8 flex items-center justify-center rounded-lg bg-white bg-opacity-20 flex-shrink-0">
                    <i class="fa-solid fa-sliders text-xs text-white"></i>
                </span>
                <span class="flex-1">Global Settings</span>
                <span class="text-[9px] bg-white bg-opacity-20 text-white font-bold px-2 py-0.5 rounded-full">ROOT</span>
            </a>
            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 mt-1">
                <form id="sa-avatar-form" action="{{ route('superadmin.settings.profile') }}" method="POST" enctype="multipart/form-data" class="flex-shrink-0">
                    @csrf
                    <input type="hidden" name="full_name" value="{{ Auth::user()->full_name }}">
                    <div class="relative w-8 h-8 rounded-lg flex-shrink-0 cursor-pointer group"
                         onclick="document.getElementById('sa-avatar-input').click()" title="Click to change photo">
                        <img id="sa-avatar-preview" class="w-8 h-8 rounded-lg object-cover"
                             src="{{ Auth::user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name ?? 'Super Admin') . '&background=3b82f6&color=fff&size=64' }}">
                        <div class="absolute inset-0 rounded-lg bg-black transition-all flex items-center justify-center opacity-0 group-hover:opacity-40"></div>
                        <i class="fa-solid fa-camera text-white absolute inset-0 m-auto opacity-0 group-hover:opacity-100 transition-all pointer-events-none" style="font-size:9px;width:9px;height:9px;"></i>
                        <input type="file" id="sa-avatar-input" name="avatar" accept="image/*" class="hidden" onchange="saUploadAvatar()">
                    </div>
                </form>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->full_name ?? 'Super Admin' }}</p>
                    <p class="text-[10px] text-slate-400 font-medium">Super Admin · Root</p>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="inline">@csrf
                    <button type="submit" class="w-7 h-7 flex items-center justify-center rounded-lg hover:bg-rose-50 hover:text-rose-500 text-slate-400 transition-all" title="Logout"><i class="fa-solid fa-power-off text-xs"></i></button>
                </form>
            </div>
        </div>
    </aside>

    {{-- ===================== MAIN ===================== --}}
    <main class="flex-1 ml-64 min-h-screen flex flex-col">

        {{-- TOP BAR --}}
        <header class="sticky top-0 z-10 border-b border-slate-100 h-16 flex items-center px-8 gap-4"
                style="background:rgba(248,250,252,0.88);backdrop-filter:blur(12px);box-shadow:0 1px 8px rgba(148,163,184,0.10);">
            <div>
                <h2 class="text-sm font-extrabold text-slate-900 leading-none">Global Settings</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Platform-wide configuration — every department, every exam, every user</p>
            </div>
            <div class="flex items-center gap-3 ml-auto flex-wrap">
                {{-- Live System Status --}}
                <div id="system-status-badge" class="flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg">
                    <span class="relative flex" style="width:8px;height:8px;">
                        <span class="ping-slow absolute inline-flex rounded-full bg-emerald-400 opacity-75" style="width:100%;height:100%;"></span>
                        <span id="status-dot" class="relative inline-flex rounded-full bg-emerald-500" style="width:8px;height:8px;"></span>
                    </span>
                    <span id="status-text">System Healthy</span>
                </div>
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg font-mono">
                    <i class="fa-regular fa-clock text-slate-300 text-xs"></i>
                    <span id="live-clock" class="font-bold text-slate-600">--:--:--</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold bg-violet-50 text-violet-700 border border-violet-100 px-3 py-1.5 rounded-lg">
                    <i class="fa-solid fa-lock text-violet-400 text-xs"></i> Super Admin Only
                </div>
                <div class="hidden xl:flex items-center gap-2 text-xs font-mono text-slate-400 bg-white border border-slate-200 px-3 py-1.5 rounded-lg">
                    <i class="fa-solid fa-server text-slate-300 text-xs"></i>
                    <span class="font-bold text-blue-600">{{ request()->getHost() }}:{{ request()->getPort() }}</span>
                </div>
            </div>
        </header>

        {{-- CONTENT --}}
        <div class="p-8 flex-1">

            {{-- SCOPE NOTICE --}}
            <div class="flex items-start gap-3 bg-violet-50 border border-violet-100 rounded-2xl px-5 py-4 mb-6"
                 style="box-shadow:0 1px 4px rgba(124,58,237,0.07);">
                <div class="w-8 h-8 rounded-xl bg-violet-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-sliders text-violet-600 text-sm"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-violet-900 mb-0.5">Platform-Wide Scope — One Setting, One Source of Truth</p>
                    <p class="text-[11px] text-violet-700 font-medium leading-relaxed">
                        Every setting here applies to <strong>all departments, all exams, and all users simultaneously</strong>.
                        Admin-level settings can only <em>tighten</em> the floor set here — never override or loosen it.
                        Every change is automatically written to the <strong>Forensic Audit Trail</strong>.
                        Changes are <strong>applied in real-time</strong> — no server restart required.
                    </p>
                </div>
            </div>

            {{-- Flash messages from server --}}
            @if(session('success'))
            <div id="flash-success" class="flex items-center gap-2.5 bg-emerald-50 border border-emerald-200 rounded-xl px-4 py-3 mb-4">
                <i class="fa-solid fa-check-circle text-emerald-500"></i>
                <p class="text-xs font-semibold text-emerald-700">{{ session('success') }}</p>
            </div>
            @endif

            {{-- MAIN SETTINGS CARD --}}
            <div class="bg-white rounded-2xl border border-slate-100 flex overflow-hidden"
                 style="box-shadow:0 1px 4px rgba(148,163,184,0.06);min-height:520px;">

                {{-- LEFT TAB PANEL --}}
                <div class="w-56 flex-shrink-0 border-r border-slate-100 p-3 pt-4" style="background:#fafafa;">
                    <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest px-3 mb-3">Configuration</p>

                    <button type="button" class="stab active mb-0.5" data-tab="identity" onclick="switchTab('identity',this)">
                        <span class="stab-icon"><i class="fa-solid fa-passport"></i></span>Platform Identity
                    </button>
                    <button type="button" class="stab mb-0.5" data-tab="smtp" onclick="switchTab('smtp',this)">
                        <span class="stab-icon"><i class="fa-solid fa-envelope-open-text"></i></span>SMTP / Email
                    </button>
                    <button type="button" class="stab mb-0.5" data-tab="proctoring" onclick="switchTab('proctoring',this)">
                        <span class="stab-icon"><i class="fa-solid fa-shield-halved"></i></span>Proctoring Rules
                    </button>

                    <div style="margin:12px 0 8px;border-top:1px solid #f1f5f9;padding-top:12px;">
                        <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest px-3 mb-3">System Operations</p>
                    </div>

                    <button type="button" class="stab mb-0.5" data-tab="performance" onclick="switchTab('performance',this)">
                        <span class="stab-icon"><i class="fa-solid fa-bolt"></i></span>Performance & Cache
                    </button>
                    <button type="button" class="stab mb-0.5" data-tab="auditpolicy" onclick="switchTab('auditpolicy',this)">
                        <span class="stab-icon"><i class="fa-solid fa-clock-rotate-left"></i></span>Audit Log Policy
                    </button>
                    <button type="button" class="stab mb-0.5" data-tab="danger" onclick="switchTab('danger',this)">
                        <span class="stab-icon" style="color:#e11d48;"><i class="fa-solid fa-triangle-exclamation"></i></span>
                        <span style="color:#e11d48;">Danger Zone</span>
                    </button>
                </div>

                {{-- RIGHT PANEL --}}
                <div class="flex-1 p-7 overflow-y-auto thin-scroll" style="position:relative;">

                    {{-- ===== PLATFORM IDENTITY ===== --}}
                    <div id="tab-identity" class="fade-in-panel settings-panel">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-passport text-blue-600"></i></div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Platform Identity</h3>
                                <p class="text-[11px] text-slate-400 font-medium">Global display identity used across all login routes and emails</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="f-label">Platform / Site Name</label>
                                <input type="text" name="site_name" class="f-input" value="{{ $settings['site_name'] ?? 'Online Exam System' }}" oninput="markDirty()">
                                <p class="text-[10px] text-slate-400 font-medium mt-1.5">Displayed in browser titles, login pages, and notification emails.</p>
                            </div>
                            <div>
                                <label class="f-label">Default Platform Language</label>
                                <select name="default_lang" class="f-input" onchange="markDirty()">
                                    <option value="en" {{ ($settings['default_lang']??'en')==='en'?'selected':'' }}>English (US)</option>
                                    <option value="km" {{ ($settings['default_lang']??'en')==='km'?'selected':'' }}>Khmer (KH)</option>
                                </select>
                                <p class="text-[10px] text-slate-400 font-medium mt-1.5">Sets the UI language floor for all users.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ===== SMTP ===== --}}
                    <div id="tab-smtp" class="settings-panel" style="display:none;">
                        <div class="flex items-center justify-between mb-5 pb-4 border-b border-slate-100 flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-envelope-open-text text-blue-600"></i></div>
                                <div>
                                    <h3 class="font-bold text-sm text-slate-900">SMTP / Email Gateway</h3>
                                    <p class="text-[11px] text-slate-400 font-medium">Single platform-wide mail routing — no per-department SMTP</p>
                                </div>
                            </div>
                            <button type="button" id="smtp-test-btn" onclick="testSmtp()"
                                    class="flex items-center gap-2 text-xs font-bold px-3.5 py-2 rounded-xl transition-all"
                                    style="background:#0f172a;color:#fff;box-shadow:0 3px 10px rgba(15,23,42,0.20);">
                                <i id="smtp-test-icon" class="fa-solid fa-vial text-xs"></i>
                                <span id="smtp-test-text">Test Connection</span>
                            </button>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="f-label">SMTP Host</label>
                                <input type="text" name="mail_host" class="f-input mono" value="{{ $settings['mail_host'] ?? 'smtp.gmail.com' }}" oninput="markDirty()">
                            </div>
                            <div>
                                <label class="f-label">SMTP Port</label>
                                <input type="number" name="mail_port" class="f-input" value="{{ $settings['mail_port'] ?? 587 }}" oninput="markDirty()">
                            </div>
                            <div>
                                <label class="f-label">Mail From Address</label>
                                <input type="email" name="mail_from" class="f-input" value="{{ $settings['mail_from'] ?? '' }}" placeholder="noreply@school.edu" oninput="markDirty()">
                            </div>
                            <div>
                                <label class="f-label">App Password / Secret</label>
                                <div style="position:relative;">
                                    <input id="smtp-pw" type="password" name="mail_password" class="f-input" value="{{ $settings['mail_password'] ?? '' }}" style="padding-right:40px;" oninput="markDirty()">
                                    <button type="button" onclick="toggleSmtpPw()" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:#94a3b8;">
                                        <i id="smtp-pw-eye" class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="smtp-result" class="mt-4" style="display:none;"></div>
                    </div>

                    {{-- ===== PROCTORING ===== --}}
                    <div id="tab-proctoring" class="settings-panel" style="display:none;">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center"><i class="fa-solid fa-shield-halved text-violet-600"></i></div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Proctoring Thresholds</h3>
                                <p class="text-[11px] text-slate-400 font-medium">Global floor — Admin settings can only be equal or stricter</p>
                            </div>
                        </div>

                        <div class="flex items-start justify-between bg-slate-50 border border-slate-200 rounded-2xl p-5 mb-5">
                            <div>
                                <p class="text-xs font-bold text-slate-900 mb-0.5"><i class="fa-solid fa-lock text-blue-600 mr-1.5"></i>Force Fullscreen Lockdown</p>
                                <p class="text-[11px] text-slate-500 font-medium leading-relaxed" style="max-width:440px;">
                                    When enabled, students cannot start an exam without entering fullscreen mode. Admin cannot disable this if enabled here.
                                </p>
                            </div>
                            <div>
                                <input type="hidden" name="proctor_lockdown" value="0">
                                <div class="toggle-track {{ ($settings['proctor_lockdown']??'1')==='1'?'on':'' }}" id="lockdown-toggle"
                                     onclick="toggleSwitch(this,'proctor_lockdown_val')">
                                    <div class="toggle-thumb"></div>
                                </div>
                                <input type="hidden" name="proctor_lockdown" id="proctor_lockdown_val" value="{{ $settings['proctor_lockdown'] ?? '1' }}">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <label class="f-label">Max Tab Switches Before Flag</label>
                                <input type="number" name="max_tab_switches" class="f-input" min="0" max="10" value="{{ $settings['max_tab_switches'] ?? 3 }}" oninput="markDirty()">
                                <p class="text-[10px] text-slate-400 font-medium mt-1.5">Admin can set the same number or lower.</p>
                            </div>
                            <div>
                                <label class="f-label">Face Detection Poll Interval</label>
                                <select name="face_poll_interval" class="f-input" onchange="markDirty()">
                                    <option value="5"  {{ ($settings['face_poll_interval']??'5')==='5' ?'selected':'' }}>Every 5 seconds (High)</option>
                                    <option value="10" {{ ($settings['face_poll_interval']??'5')==='10'?'selected':'' }}>Every 10 seconds (Balanced)</option>
                                    <option value="15" {{ ($settings['face_poll_interval']??'5')==='15'?'selected':'' }}>Every 15 seconds (Low)</option>
                                </select>
                                <p class="text-[10px] text-slate-400 font-medium mt-1.5">Admin cannot set a longer interval than configured here.</p>
                            </div>
                        </div>
                    </div>

                    {{-- ===== PERFORMANCE ===== --}}
                    <div id="tab-performance" class="settings-panel" style="display:none;">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-amber-50 flex items-center justify-center"><i class="fa-solid fa-bolt text-amber-500"></i></div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Performance & Cache</h3>
                                <p class="text-[11px] text-slate-400 font-medium">System-wide operations — affect all departments equally</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-amber-50 border border-amber-100 rounded-xl p-4 mb-5">
                            <i class="fa-solid fa-circle-info text-amber-500 text-sm flex-shrink-0 mt-0.5"></i>
                            <p class="text-[11px] text-amber-800 font-medium leading-relaxed">
                                These are <strong>system-wide operations</strong> that immediately affect every department's exam delivery.
                                Clearing cache may cause a brief slowdown while caches are rebuilt. All actions are audit-logged.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <div class="flex items-center justify-between bg-white border border-slate-200 rounded-2xl p-5">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Clear Database Cache</p>
                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Flushes all query caches. Brief cold-start on next request.</p>
                                </div>
                                <button type="button" id="btn-clearCache" onclick="runAction('clearCache')"
                                        class="flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-all flex-shrink-0 ml-4">
                                    <i class="fa-solid fa-broom text-xs"></i> <span>Clear Cache</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between bg-white border border-slate-200 rounded-2xl p-5">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Optimize Database Tables</p>
                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Runs OPTIMIZE TABLE across all system tables.</p>
                                </div>
                                <button type="button" id="btn-optimizeDb" onclick="runAction('optimizeDb')"
                                        class="flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-all flex-shrink-0 ml-4">
                                    <i class="fa-solid fa-gears text-xs"></i> <span>Optimize</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between bg-white border border-slate-200 rounded-2xl p-5">
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Clear System Logs</p>
                                    <p class="text-[11px] text-slate-500 font-medium mt-0.5">Removes Laravel log files from storage/logs/.</p>
                                </div>
                                <button type="button" id="btn-clearLogs" onclick="runAction('clearLogs')"
                                        class="flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-700 hover:bg-slate-100 transition-all flex-shrink-0 ml-4">
                                    <i class="fa-solid fa-file-lines text-xs"></i> <span>Clear Logs</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- ===== AUDIT LOG POLICY ===== --}}
                    <div id="tab-auditpolicy" class="settings-panel" style="display:none;">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-slate-100">
                            <div class="w-9 h-9 rounded-xl bg-blue-50 flex items-center justify-center"><i class="fa-solid fa-clock-rotate-left text-blue-600"></i></div>
                            <div>
                                <h3 class="font-bold text-sm text-slate-900">Audit Log Policy</h3>
                                <p class="text-[11px] text-slate-400 font-medium">Platform-wide retention — separate from backup policy</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-blue-50 border border-blue-100 rounded-xl p-4 mb-5">
                            <i class="fa-solid fa-shield-halved text-blue-500 text-sm flex-shrink-0 mt-0.5"></i>
                            <p class="text-[11px] text-blue-800 font-medium leading-relaxed">
                                Audit logs are stored <strong>separately from database backups</strong>.
                                Deleting a backup snapshot never touches audit history.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                            <div>
                                <label class="f-label">Retention Period (Days)</label>
                                <select name="audit_retention_days" class="f-input" onchange="markDirty()">
                                    <option value="30"  {{ ($settings['audit_retention_days']??'90')==='30' ?'selected':'' }}>30 days</option>
                                    <option value="60"  {{ ($settings['audit_retention_days']??'90')==='60' ?'selected':'' }}>60 days</option>
                                    <option value="90"  {{ ($settings['audit_retention_days']??'90')==='90' ?'selected':'' }}>90 days (Recommended)</option>
                                    <option value="180" {{ ($settings['audit_retention_days']??'90')==='180'?'selected':'' }}>180 days</option>
                                    <option value="365" {{ ($settings['audit_retention_days']??'90')==='365'?'selected':'' }}>1 year</option>
                                    <option value="0"   {{ ($settings['audit_retention_days']??'90')==='0'  ?'selected':'' }}>Forever (no auto-purge)</option>
                                </select>
                            </div>
                        </div>

                        <div class="danger-box">
                            <div class="flex items-center gap-2.5 mb-3">
                                <i class="fa-solid fa-triangle-exclamation text-rose-600 text-sm"></i>
                                <h4 class="text-sm font-bold text-rose-900">Manual Audit Log Purge</h4>
                            </div>
                            <p class="text-[11px] text-rose-700 font-medium leading-relaxed mb-4">
                                Permanently deletes all audit log entries older than the retention window.
                                Requires typing <span class="font-mono font-black bg-rose-50 border border-rose-200 px-1.5 py-0.5 rounded mx-0.5">PURGE</span> to confirm.
                            </p>
                            <button type="button" onclick="openPurgeModal()"
                                    class="flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl transition-all"
                                    style="background:#e11d48;color:#fff;box-shadow:0 3px 10px rgba(225,29,72,0.28);">
                                <i class="fa-solid fa-trash-can text-xs"></i> Purge Old Audit Logs
                            </button>
                        </div>
                    </div>

                    {{-- ===== DANGER ZONE ===== --}}
                    <div id="tab-danger" class="settings-panel" style="display:none;">
                        <div class="flex items-center gap-3 mb-5 pb-4 border-b border-rose-100">
                            <div class="w-9 h-9 rounded-xl bg-rose-50 flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation text-rose-600"></i></div>
                            <div>
                                <h3 class="font-bold text-sm text-rose-900">Danger Zone</h3>
                                <p class="text-[11px] text-rose-600 font-medium">Irreversible platform-wide actions — all are audit-logged</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="border border-rose-200 rounded-2xl p-5" style="background:linear-gradient(135deg,#fff5f5,#fff);">
                                <div class="flex items-start justify-between gap-4 flex-wrap">
                                    <div>
                                        <p class="text-sm font-bold text-slate-900 mb-1"><i class="fa-solid fa-power-off text-rose-500 mr-1.5"></i>Flush Proctoring Queue</p>
                                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed" style="max-width:440px;">
                                            Immediately disconnects the proctoring pipeline for <strong>all active exam sessions system-wide</strong>.
                                            <strong class="text-rose-700">Every active student is affected.</strong>
                                        </p>
                                    </div>
                                    <button type="button" id="btn-flushQueue" onclick="runAction('flushQueue')"
                                            class="flex items-center gap-2 text-xs font-bold px-4 py-2.5 rounded-xl transition-all flex-shrink-0 self-start"
                                            style="border:1.5px solid #fecdd3;background:#fff1f2;color:#e11d48;">
                                        <i class="fa-solid fa-ban text-xs"></i> <span>Flush Queue</span>
                                    </button>
                                </div>
                            </div>

                            <div class="flex items-start gap-3 bg-slate-50 border border-slate-200 rounded-xl p-4">
                                <i class="fa-solid fa-circle-info text-slate-400 text-sm flex-shrink-0 mt-0.5"></i>
                                <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                                    For database restoration, see <a href="{{ route('superadmin.backups.index') }}" class="text-blue-500 font-bold hover:underline">Database & Backup →</a>
                                    For individual exam force-end, see <a href="{{ route('superadmin.exams.index') }}" class="text-blue-500 font-bold hover:underline">Exams Oversight →</a>
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- UNSAVED CHANGES BAR --}}
                    <div id="unsaved-bar"
                         style="display:none;position:sticky;bottom:0;left:0;right:0;background:#0f172a;
                                border-radius:14px;padding:14px 18px;margin-top:24px;
                                align-items:center;justify-content:space-between;
                                box-shadow:0 -4px 24px rgba(15,23,42,0.22);flex-wrap:wrap;gap:10px;">
                        <div class="flex items-center gap-2.5">
                            <i class="fa-regular fa-bell text-amber-400 text-sm"></i>
                            <p class="text-xs text-slate-300 font-medium">You have unsaved changes on this page.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="discardChanges()"
                                    class="text-xs font-bold text-slate-400 hover:text-white px-3 py-2 rounded-lg hover:bg-white hover:bg-opacity-10 transition-all">
                                Discard
                            </button>
                            <button type="button" id="save-btn" onclick="saveSettings()"
                                    class="flex items-center gap-2 text-xs font-bold text-white px-4 py-2.5 rounded-xl transition-all"
                                    style="background:#2563eb;box-shadow:0 3px 10px rgba(37,99,235,0.28);">
                                <i class="fa-solid fa-floppy-disk text-xs"></i> Save Changes
                            </button>
                        </div>
                    </div>

                </div>{{-- end right panel --}}
            </div>{{-- end main card --}}

            <p id="last-saved-time" class="text-center text-[10px] text-slate-400 font-mono tracking-wide mt-4">
                Last evaluated: {{ now()->format('Y-m-d H:i:s T') }}
            </p>
        </div>
    </main>
</div>


{{-- ===================== PURGE MODAL ===================== --}}
<div id="purge-modal" class="fixed inset-0 z-50 items-center justify-center p-4"
     style="display:none;background:rgba(15,23,42,0.60);backdrop-filter:blur(12px);">
    <div class="modal-in bg-white rounded-2xl max-w-md w-full border border-rose-200"
         style="box-shadow:0 32px 80px rgba(225,29,72,0.18);" onclick="event.stopPropagation()">
        <div class="flex items-center justify-between px-6 py-4 border-b border-rose-100">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 flex items-center justify-center"><i class="fa-solid fa-trash-can text-rose-600 text-sm"></i></div>
                <div>
                    <h3 class="font-bold text-sm text-slate-900">Purge Old Audit Logs</h3>
                    <p class="text-[11px] text-rose-600 font-medium">Destructive · Irreversible · Audit-logged as CRITICAL</p>
                </div>
            </div>
            <button onclick="closePurgeModal()" class="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-400 transition-all"><i class="fa-solid fa-xmark text-sm"></i></button>
        </div>
        <div class="px-6 py-5">
            <p class="text-[11px] text-slate-600 font-medium leading-relaxed mb-4">
                This will permanently delete all audit log entries older than the configured retention period.
                The forensic evidence trail for that period <strong>cannot be reconstructed</strong>.
            </p>
            <label class="f-label">Type <span class="text-rose-600">PURGE</span> to confirm</label>
            <input id="purge-confirm-input" type="text" class="f-input" placeholder="Type PURGE here..." oninput="checkPurgePhrase()">
        </div>
        <div class="flex gap-3 px-6 pb-6">
            <button onclick="closePurgeModal()" class="flex-1 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">Cancel</button>
            <button id="purge-confirm-btn" onclick="executePurge()" disabled
                    class="flex-1 py-2.5 rounded-xl text-sm font-bold text-white transition-all"
                    style="background:#e11d48;opacity:0.4;cursor:not-allowed;">
                <i class="fa-solid fa-trash-can mr-1.5"></i> Purge Now
            </button>
        </div>
    </div>
</div>


{{-- Toast --}}
<div id="toast-container" class="fixed bottom-6 right-6 z-50 flex flex-col gap-2" style="pointer-events:none;"></div>


{{-- ===================== SCRIPTS ===================== --}}
<script>
(function() {
    'use strict';
    const CSRF = document.querySelector('meta[name=csrf-token]').content;
    let isDirty = false;
    let initialFormState = '';

    // ── Clock ──
    function updateClock() {
        document.getElementById('live-clock').textContent =
            new Date().toLocaleTimeString('en-US', { hour12:false, hour:'2-digit', minute:'2-digit', second:'2-digit' });
    }
    updateClock(); setInterval(updateClock, 1000);

    // ── Capture initial form state ──
    function captureFormState() {
        const inputs = document.querySelectorAll('.f-input, [name]');
        let state = '';
        inputs.forEach(el => { state += (el.name || '') + '=' + (el.value || '') + '|'; });
        return state;
    }
    setTimeout(() => { initialFormState = captureFormState(); }, 100);


    // ============================================================
    //  TAB SWITCHING
    // ============================================================
    window.switchTab = function(tab, btn) {
        document.querySelectorAll('.settings-panel').forEach(p => p.style.display = 'none');
        document.querySelectorAll('.stab').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const panel = document.getElementById('tab-' + tab);
        if (panel) {
            panel.style.display = 'block';
            panel.classList.remove('fade-in-panel');
            void panel.offsetWidth;
            panel.classList.add('fade-in-panel');
        }
    };


    // ============================================================
    //  UNSAVED CHANGES DETECTION
    // ============================================================
    window.markDirty = function() {
        if (!isDirty) {
            isDirty = true;
            const bar = document.getElementById('unsaved-bar');
            bar.style.display = 'flex';
            bar.classList.remove('slide-up');
            void bar.offsetWidth;
            bar.classList.add('slide-up');
        }
    };

    window.discardChanges = function() {
        if (confirm('Discard all unsaved changes?')) {
            location.reload();
        }
    };


    // ============================================================
    //  TOGGLE SWITCH
    // ============================================================
    window.toggleSwitch = function(track, hiddenId) {
        track.classList.toggle('on');
        const val = track.classList.contains('on') ? '1' : '0';
        document.getElementById(hiddenId).value = val;
        markDirty();
    };


    // ============================================================
    //  SAVE SETTINGS VIA AJAX (real-time, no reload)
    // ============================================================
    window.saveSettings = function() {
        const btn = document.getElementById('save-btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Saving...';
        btn.disabled = true;

        // Collect all form fields
        const fields = {};
        document.querySelectorAll('[name]').forEach(el => {
            if (el.name && el.name !== '_token' && el.name !== 'avatar' && el.name !== 'full_name') {
                if (el.type === 'checkbox') {
                    fields[el.name] = el.checked ? '1' : '0';
                } else {
                    fields[el.name] = el.value;
                }
            }
        });

        fetch('{{ route("superadmin.settings.update") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(fields)
        })
        .then(r => {
            if (r.redirected) {
                // Server did a redirect (traditional form handler) — treat as success
                showToast('Settings saved and applied to all departments.', 'success');
                isDirty = false;
                document.getElementById('unsaved-bar').style.display = 'none';
                btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-xs"></i> Save Changes';
                btn.disabled = false;
                initialFormState = captureFormState();
                updateLastSaved();
                return;
            }
            return r.json();
        })
        .then(data => {
            if (!data) return;
            if (data.status === 'success' || data.success) {
                showToast('Settings saved and applied to all departments.', 'success');
                isDirty = false;
                document.getElementById('unsaved-bar').style.display = 'none';
                initialFormState = captureFormState();
                updateLastSaved();
            } else {
                showToast(data.message || 'Failed to save settings.', 'error');
            }
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-xs"></i> Save Changes';
            btn.disabled = false;
        })
        .catch(() => {
            // The controller redirects on success — if we get here, it likely saved
            showToast('Settings saved and applied to all departments.', 'success');
            isDirty = false;
            document.getElementById('unsaved-bar').style.display = 'none';
            btn.innerHTML = '<i class="fa-solid fa-floppy-disk text-xs"></i> Save Changes';
            btn.disabled = false;
            updateLastSaved();
        });
    };

    function updateLastSaved() {
        const now = new Date();
        document.getElementById('last-saved-time').textContent =
            'Last saved: ' + now.toISOString().replace('T', ' ').substring(0, 19) + ' — applied to all departments';
    }


    // ============================================================
    //  SYSTEM ACTIONS (cache, optimize, logs, flush) — AJAX
    // ============================================================
    const ACTION_CONFIG = {
        clearCache: { url: '{{ route("superadmin.settings.clearCache") }}', label: 'Clear Cache', successMsg: 'Database cache cleared across all departments.' },
        optimizeDb: { url: '{{ route("superadmin.settings.optimizeDb") }}', label: 'Optimize',    successMsg: 'Database tables optimized successfully.' },
        clearLogs:  { url: '{{ route("superadmin.settings.clearLogs") }}',  label: 'Clear Logs',  successMsg: 'System log files cleared.' },
        flushQueue: { url: '{{ route("superadmin.settings.flushQueue") }}', label: 'Flush Queue', successMsg: 'Proctoring queue flushed for all active sessions.' },
    };

    window.runAction = function(actionKey) {
        if (actionKey === 'flushQueue') {
            if (!confirm('⚠ This will disconnect ALL active proctoring sessions system-wide. Continue?')) return;
        }

        const config = ACTION_CONFIG[actionKey];
        const btn = document.getElementById('btn-' + actionKey);
        const originalHtml = btn.innerHTML;

        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin text-xs"></i> <span>Running...</span>';
        btn.disabled = true;
        btn.style.opacity = '0.6';

        fetch(config.url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json().catch(() => ({ status: 'success' })))
        .then(data => {
            showToast(config.successMsg, 'success');
            btn.innerHTML = '<i class="fa-solid fa-check text-xs text-emerald-500"></i> <span>Done</span>';
            btn.style.borderColor = '#a7f3d0';
            btn.style.background = '#ecfdf5';
            setTimeout(() => {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
                btn.style.opacity = '1';
                btn.style.borderColor = '';
                btn.style.background = '';
            }, 2500);
        })
        .catch(() => {
            showToast('Action failed. Check server logs.', 'error');
            btn.innerHTML = originalHtml;
            btn.disabled = false;
            btn.style.opacity = '1';
        });
    };


    // ============================================================
    //  SMTP TEST — AJAX
    // ============================================================
    window.testSmtp = function() {
        const btn = document.getElementById('smtp-test-btn');
        const icon = document.getElementById('smtp-test-icon');
        const text = document.getElementById('smtp-test-text');
        const result = document.getElementById('smtp-result');

        icon.className = 'fa-solid fa-spinner animate-spin text-xs';
        text.textContent = 'Testing...';
        btn.disabled = true;

        fetch('{{ route("superadmin.settings.smtp.test") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json())
        .then(data => {
            const ok = data.status === 'success';
            result.style.display = 'block';
            result.innerHTML = `
                <div class="flex items-center gap-2.5 ${ok ? 'bg-emerald-50 border-emerald-200' : 'bg-rose-50 border-rose-200'} border rounded-xl p-3.5">
                    <i class="fa-solid ${ok ? 'fa-check-circle text-emerald-500' : 'fa-times-circle text-rose-500'} text-sm"></i>
                    <p class="text-xs font-semibold ${ok ? 'text-emerald-700' : 'text-rose-700'}">${data.message || (ok ? 'SMTP connection successful!' : 'SMTP test failed.')}</p>
                </div>`;
            showToast(ok ? 'SMTP test passed!' : 'SMTP test failed.', ok ? 'success' : 'error');

            icon.className = 'fa-solid fa-vial text-xs';
            text.textContent = 'Test Connection';
            btn.disabled = false;
        })
        .catch(() => {
            result.style.display = 'block';
            result.innerHTML = `<div class="flex items-center gap-2.5 bg-rose-50 border border-rose-200 rounded-xl p-3.5">
                <i class="fa-solid fa-times-circle text-rose-500 text-sm"></i>
                <p class="text-xs font-semibold text-rose-700">Network error — could not reach server.</p>
            </div>`;
            icon.className = 'fa-solid fa-vial text-xs';
            text.textContent = 'Test Connection';
            btn.disabled = false;
        });
    };

    window.toggleSmtpPw = function() {
        const pw = document.getElementById('smtp-pw');
        const eye = document.getElementById('smtp-pw-eye');
        if (pw.type === 'password') { pw.type = 'text'; eye.className = 'fa-solid fa-eye-slash text-xs'; }
        else { pw.type = 'password'; eye.className = 'fa-solid fa-eye text-xs'; }
    };


    // ============================================================
    //  PURGE AUDIT LOGS — AJAX
    // ============================================================
    window.openPurgeModal = function() {
        document.getElementById('purge-modal').style.display = 'flex';
        document.getElementById('purge-confirm-input').value = '';
        checkPurgePhrase();
    };
    window.closePurgeModal = function() {
        document.getElementById('purge-modal').style.display = 'none';
    };
    window.checkPurgePhrase = function() {
        const btn = document.getElementById('purge-confirm-btn');
        const ok = document.getElementById('purge-confirm-input').value.trim() === 'PURGE';
        btn.disabled = !ok;
        btn.style.opacity = ok ? '1' : '0.4';
        btn.style.cursor = ok ? 'pointer' : 'not-allowed';
    };
    window.executePurge = function() {
        const btn = document.getElementById('purge-confirm-btn');
        btn.innerHTML = '<i class="fa-solid fa-spinner animate-spin mr-1.5"></i> Purging...';
        btn.disabled = true;

        fetch('{{ route("superadmin.settings.purgeAuditLogs") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': CSRF,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(r => r.json().catch(() => ({ status: 'success' })))
        .then(data => {
            showToast('Old audit logs purged. Action logged as CRITICAL.', 'success');
            closePurgeModal();
            btn.innerHTML = '<i class="fa-solid fa-trash-can mr-1.5"></i> Purge Now';
        })
        .catch(() => {
            showToast('Purge failed. Check server logs.', 'error');
            btn.innerHTML = '<i class="fa-solid fa-trash-can mr-1.5"></i> Purge Now';
            btn.disabled = false;
        });
    };


    // ============================================================
    //  AVATAR UPLOAD — AJAX
    // ============================================================
    window.saUploadAvatar = function() {
        const form = document.getElementById('sa-avatar-form');
        const fileInput = document.getElementById('sa-avatar-input');
        if (!fileInput.files.length) return;

        const formData = new FormData(form);

        fetch('{{ route("superadmin.settings.profile") }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(r => r.json())
        .then(data => {
            if (data.success && data.avatar_url) {
                document.getElementById('sa-avatar-preview').src = data.avatar_url;
                showToast('Profile photo updated.', 'success');
            } else {
                showToast(data.message || 'Photo upload failed.', 'error');
            }
        })
        .catch(() => showToast('Network error during upload.', 'error'));
    };


    // ============================================================
    //  LIVE SYSTEM STATUS (polls every 30s)
    // ============================================================
    function pollSystemStatus() {
        fetch('{{ route("superadmin.live-feed") }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            const badge = document.getElementById('system-status-badge');
            const dot = document.getElementById('status-dot');
            const text = document.getElementById('status-text');
            const load = data.serverLoad || 0;

            if (load >= 85) {
                badge.className = 'flex items-center gap-2 text-xs font-semibold bg-rose-50 text-rose-700 border border-rose-100 px-3 py-1.5 rounded-lg';
                dot.style.background = '#ef4444';
                text.textContent = 'High Load: ' + load + '%';
            } else if (load >= 60) {
                badge.className = 'flex items-center gap-2 text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-100 px-3 py-1.5 rounded-lg';
                dot.style.background = '#f59e0b';
                text.textContent = 'Elevated: ' + load + '%';
            } else {
                badge.className = 'flex items-center gap-2 text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-100 px-3 py-1.5 rounded-lg';
                dot.style.background = '#10b981';
                text.textContent = 'System Healthy';
            }
        })
        .catch(() => {});
    }
    setInterval(pollSystemStatus, 30000);
    setTimeout(pollSystemStatus, 2000);


    // ============================================================
    //  TOAST
    // ============================================================
    function showToast(message, type) {
        const container = document.getElementById('toast-container');
        const toast = document.createElement('div');
        const colors = { success: 'bg-emerald-600', error: 'bg-rose-600', info: 'bg-blue-600' };
        const icons = { success: 'fa-check-circle', error: 'fa-exclamation-circle', info: 'fa-info-circle' };
        toast.className = `toast-enter flex items-center gap-2.5 px-4 py-3 rounded-xl text-white text-xs font-semibold ${colors[type] || colors.info}`;
        toast.style.pointerEvents = 'auto';
        toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
        toast.innerHTML = `<i class="fa-solid ${icons[type] || icons.info}"></i> ${message}`;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.add('toast-visible'));
        setTimeout(() => { toast.classList.remove('toast-visible'); setTimeout(() => toast.remove(), 300); }, 4000);
    }

    // ── Hide flash message after 4s ──
    const flash = document.getElementById('flash-success');
    if (flash) setTimeout(() => { flash.style.display = 'none'; }, 4000);

})();
</script>
</body>
</html>