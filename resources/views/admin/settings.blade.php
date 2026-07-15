<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Settings — ExamSystem Console">
    <title>Settings — ExamSystem Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ═══════════════════════════════════════════
           DESIGN SYSTEM — ExamSystem Admin Console
        ═══════════════════════════════════════════ */
        :root {
            --sidebar-w  : 256px; /* Exactly matching dashboard.blade.php sidebar size */
            --body-bg    : #f8fafc;
            --card-bg    : #ffffff;
            --card-border: #e8edf5;
            --card-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 4px 16px rgba(0,0,0,0.03);
            --text-1     : #0f172a;
            --text-2     : #64748b;
            --text-muted : #94a3b8;
            --blue       : #2563eb;
            --input-bg   : #f8fafc;
            --input-br   : #e2e8f0;
            --radius     : 16px;
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, sans-serif;
            background  : var(--body-bg);
            color       : var(--text-1);
            min-height  : 100vh;
            display     : flex;
        }

        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        /* ─── Sidebar ─── */
        .sidebar {
            width     : var(--sidebar-w);
            min-height: 100vh;
            background: var(--card-bg);
            border-right: 1px solid var(--card-border);
            box-shadow: 2px 0 12px rgba(0,0,0,0.04);
            display   : flex;
            flex-direction: column;
            position  : fixed;
            top:0; left:0; z-index:100;
            justify-content: space-between;
        }
        .sb-brand {
            display:flex; align-items:center; gap:12px;
            padding:20px 24px; border-bottom:1px solid #f1f5f9;
        }
        .sb-icon {
            width:40px; height:40px; border-radius:12px; flex-shrink:0;
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            display:flex; align-items:center; justify-content:center;
            color:#fff; font-size:16px;
            box-shadow:0 4px 12px rgba(37,99,235,0.3);
        }
        .sb-name { font-size:14px; font-weight:700; color:var(--text-1); letter-spacing:-.3px; line-height: 1.2; }
        .sb-sub  { font-size:11px; color:var(--text-muted); font-weight:500; }
        .sb-nav  { padding:12px; display:flex; flex-direction:column; gap:2px; }
        .nav-item {
            display:flex; align-items:center; gap:12px;
            padding:10px 12px; border-radius:12px; text-decoration:none;
            color:var(--text-2); font-size:14px; font-weight:500;
            border-left:3px solid transparent; transition:all 0.18s ease;
        }
        .nav-item:hover { background:#f8fafc; color:var(--text-1); border-left-color:#94a3b8; }
        .nav-item.active { background:linear-gradient(135deg,#eff6ff,#dbeafe); color:#1d4ed8; border:1px solid #bfdbfe; border-left:3px solid #2563eb; font-weight:600; }
        .nav-item.active i { color: #2563eb; }
        .nav-item i { width:20px; text-align:center; font-size:14px; color: #64748b; }
        .sb-footer { padding:12px; border-top:1px solid #f1f5f9; }
        .sb-logout {
            display:flex; align-items:center; gap:12px;
            padding:10px 12px; border-radius:12px; border:none; cursor:pointer;
            background:transparent; color:#ef4444; font-size:14px; font-weight:600;
            font-family:'Inter',sans-serif; width:100%; transition:all 0.15s ease;
        }
        .sb-logout:hover { background:#fff1f2; }

        /* ─── Main ─── */
        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

        /* ─── Topbar ─── */
        .topbar {
            background:var(--card-bg); border-bottom:1px solid var(--card-border);
            padding:16px 28px; display:flex; align-items:center; justify-content:space-between;
            position:sticky; top:0; z-index:50;
            box-shadow:0 1px 4px rgba(0,0,0,0.04);
        }
        .status-chip {
            display:flex; align-items:center; gap:8px;
            padding:6px 14px; border-radius:9999px;
            border:1px solid #bbf7d0; background:#f0fdf4; color:#15803d;
            font-size:12px; font-weight:600;
        }
        .live-chip {
            display:flex; align-items:center; gap:6px;
            padding:6px 12px; border-radius:12px;
            background:#f8fafc; border:1px solid var(--input-br);
            color:var(--text-muted); font-size:11px;
            font-family:'JetBrains Mono',monospace;
        }
        .topbar-right { display:flex; align-items:center; gap:12px; }
        .admin-av {
            width:40px; height:40px; border-radius:12px; flex-shrink:0;
            background:linear-gradient(135deg,#2563eb,#1d4ed8);
            color:#fff; font-size:14px; font-weight:700;
            display:flex; align-items:center; justify-content:center;
            box-shadow:0 3px 10px rgba(37,99,235,0.3);
        }

        /* ─── Page body ─── */
        .page-body { flex:1; padding:28px; }
        .page-wrap  { margin:0 auto; } /* Removed strict width caps to scale seamlessly alongside Dashboard templates */

        /* ─── Stat cards (2 only) ─── */
        .stat-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:20px; margin-bottom:24px; }
        .stat-card {
            background:var(--card-bg); border:1px solid var(--card-border);
            border-radius:var(--radius); padding:20px;
            box-shadow:var(--card-shadow);
            display:flex; justify-content:space-between; align-items:center;
            transition:all 0.22s ease;
        }
        .stat-card:hover { box-shadow:0 4px 24px rgba(37,99,235,0.09); border-color:#bfdbfe; transform:translateY(-2px); }
        .stat-val  { font-size:30px; font-weight:900; color:var(--text-1); letter-spacing:-.5px; margin-top:2px; }
        .stat-lbl  { font-size:12px; color:var(--text-muted); font-weight:700; uppercase tracking-widest; }
        .stat-icon { width:44px; height:44px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:14px; flex-shrink:0; border: 1px solid transparent; }
        .si-blue   { background:#eff6ff; color:#2563eb; border-color: #bfdbfe; }
        .si-green  { background:#f0fdf4; color:#16a34a; border-color: #bbf7d0; }

        /* ─── Scope notice ─── */
        .scope-notice {
            display:flex; align-items:flex-start; gap:10px;
            background:#f0f9ff; border:1px solid #bae6fd; border-radius:12px;
            padding:14px; margin-bottom:24px; font-size:12.5px; color:#0369a1; line-height:1.6;
        }

        /* ─── Config header ─── */
        .cfg-header {
            background:var(--card-bg); border:1px solid var(--card-border);
            border-radius:var(--radius); padding:20px 24px;
            box-shadow:var(--card-shadow); margin-bottom:20px;
            display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:14px;
        }
        .cfg-title { font-size:18px; font-weight:800; color:var(--text-1); letter-spacing:-.3px; }
        .cfg-sub   { font-size:12px; color:var(--text-muted); margin-top:2px; }

        /* ─── Tabs ─── */
        .tab-group {
            display:flex; gap:2px; background:#f1f5f9; padding:4px;
            border-radius:12px; border:1px solid var(--card-border);
        }
        .tab-btn {
            padding:8px 18px; border-radius:8px; font-size:12px; font-weight:600;
            border:none; cursor:pointer; font-family:'Inter',sans-serif; transition:all 0.15s;
            color:var(--text-2); background:transparent;
        }
        .tab-btn.active { background:var(--card-bg); color:var(--text-1); box-shadow:0 1px 4px rgba(0,0,0,0.08); }

        /* ─── Cards ─── */
        .card { background:var(--card-bg); border:1px solid var(--card-border); border-radius:var(--radius); box-shadow:var(--card-shadow); overflow:hidden; margin-bottom:20px; }
        .card-head { padding:16px 22px; border-bottom:1px solid #f1f5f9; display:flex; align-items:center; gap:10px; }
        .card-head-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:13px; flex-shrink:0; }
        .card-body  { padding:22px; }
        .card-title { font-size:14px; font-weight:700; color:var(--text-1); }
        .card-desc  { font-size:12px; color:var(--text-muted); margin-top:1px; }

        /* ─── Form ─── */
        .f-group { margin-bottom:18px; }
        .f-label { display:block; font-size:11.5px; font-weight:600; color:var(--text-2); margin-bottom:6px; }
        .f-hint  { font-size:11px; color:var(--text-muted); margin-top:4px; }
        .f-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .f-input {
            width:100%; background:var(--input-bg); border:1px solid var(--input-br);
            border-radius:10px; padding:10px 14px; font-size:13.5px;
            font-family:'Inter',sans-serif; color:var(--text-1); outline:none;
            transition:border-color 0.2s,box-shadow 0.2s,background 0.2s; appearance:none;
        }
        .f-input:focus { background:#fff; border-color:#93c5fd; box-shadow:0 0 0 3px rgba(147,197,253,0.25); }

        /* ─── Toggle rows ─── */
        .toggle-row {
            display:flex; align-items:flex-start; gap:12px;
            padding:14px; background:#f8fafc; border:1px solid var(--input-br);
            border-radius:10px; cursor:pointer; transition:all 0.15s; margin-bottom:10px;
        }
        .toggle-row:hover { background:#f1f5f9; }
        .toggle-row:last-child { margin-bottom:0; }
        .custom-cb { width:18px; height:18px; accent-color:#2563eb; cursor:pointer; flex-shrink:0; margin-top:1px; }
        .toggle-label { font-size:13px; font-weight:600; color:var(--text-1); }
        .toggle-sub   { font-size:12px; color:var(--text-muted); margin-top:2px; line-height:1.5; }

        /* ─── Buttons ─── */
        .btn-primary {
            display:inline-flex; align-items:center; justify-content:center; gap:7px;
            padding:10px 22px; background:linear-gradient(135deg,#2563eb,#1d4ed8);
            color:#fff; border:none; border-radius:10px;
            font-size:13px; font-weight:600; font-family:'Inter',sans-serif;
            cursor:pointer; box-shadow:0 2px 8px rgba(37,99,235,0.28);
            transition:opacity 0.15s,transform 0.1s;
        }
        .btn-primary:hover  { opacity:.92; transform:translateY(-1px); }
        .btn-primary:active { transform:translateY(0); }
        .btn-primary:disabled { opacity:.6; cursor:not-allowed; transform:none; }
        .btn-ghost {
            display:inline-flex; align-items:center; justify-content:center; gap:7px;
            padding:10px 20px; background:transparent; border:1px solid var(--card-border);
            color:var(--text-2); border-radius:10px; font-size:13px; font-weight:500;
            cursor:pointer; font-family:'Inter',sans-serif; text-decoration:none;
            transition:background 0.15s,color 0.15s;
        }
        .btn-ghost:hover { background:#f8fafc; color:var(--text-1); }

        /* ─── Avatar upload ─── */
        .av-wrap { position:relative; width:80px; height:80px; flex-shrink:0; }
        .av-wrap img { width:80px; height:80px; border-radius:50%; border:3px solid #fff; box-shadow:0 2px 12px rgba(0,0,0,0.12); object-fit:cover; }
        .av-overlay {
            position:absolute; inset:0; background:rgba(0,0,0,0.42); border-radius:50%;
            display:flex; align-items:center; justify-content:center; color:#fff;
            font-size:14px; opacity:0; cursor:pointer; transition:opacity 0.2s;
        }
        .av-wrap:hover .av-overlay { opacity:1; }

        .divider { height:1px; background:#f1f5f9; margin:20px 0; }

        /* ─── Toast ─── */
        #toast {
            position:fixed; bottom:28px; right:28px; z-index:9999;
            display:flex; align-items:center; gap:10px;
            padding:12px 20px; border-radius:12px; font-size:13.5px; font-weight:500;
            box-shadow:0 4px 20px rgba(0,0,0,0.16); color:#fff;
            opacity:0; transform:translateY(10px); transition:all 0.3s ease;
            pointer-events:none; min-width:200px; background:#0f172a;
        }
        #toast.show { opacity:1; transform:translateY(0); }

        /* ─── Pulse ─── */
        @keyframes pulse-ring {
            0%  { box-shadow:0 0 0 0 rgba(34,197,94,.5); }
            70% { box-shadow:0 0 0 6px rgba(34,197,94,0); }
            100%{ box-shadow:0 0 0 0 rgba(34,197,94,0); }
        }
        .pulse-dot { width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; animation:pulse-ring 1.8s ease-out infinite; }

        @media(max-width:1024px) {
            .sidebar { display:none; } .main { margin-left:0; }
            .page-body { padding:16px; }
            .stat-grid { grid-template-columns:1fr; }
            .f-grid-2  { grid-template-columns:1fr; }
        }
    </style>
</head>
<body>

<!-- ════════════════ SIDEBAR ════════════════ -->
<aside class="sidebar">
    <div>
        <div class="sb-brand">
            <div class="sb-icon"><i class="fas fa-graduation-cap"></i></div>
            <div>
                <div class="sb-name">ExamSystem</div>
                <div class="sb-sub">Admin Console</div>
            </div>
        </div>

        <nav class="sb-nav">
            <a href="{{ route('admin.dashboard') }}" class="nav-item"><i class="fas fa-chart-line"></i> Dashboard</a>
            <a href="{{ route('admin.users') }}"     class="nav-item"><i class="fas fa-users-gear"></i> User Management</a>
            <a href="{{ route('admin.exams') }}"     class="nav-item"><i class="fas fa-file-pen"></i> Exams</a>
            <a href="{{ route('admin.support') }}"   class="nav-item"><i class="fas fa-headset"></i> Support Desk</a>
            <a href="{{ route('admin.security') }}"  class="nav-item"><i class="fas fa-shield-halved"></i> Security</a>
        </nav>
    </div>

    <!-- Align Settings tab cleanly below at footer to match dashboards layout -->
    <div>
        <div style="padding: 12px; border-bottom: 1px solid #f1f5f9;">
            <a href="{{ route('admin.settings') }}" class="nav-item active"><i class="fas fa-gear"></i> Settings</a>
        </div>
        <div class="sb-footer">
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
            <button class="sb-logout" onclick="confirmLogout()">
                <i class="fas fa-arrow-right-from-bracket"></i> Sign Out
            </button>
        </div>
    </div>
</aside>

<!-- ════════════════ MAIN ════════════════ -->
<div class="main">

    <!-- Topbar -->
    <header class="topbar">
        <div class="status-chip">
            <span class="pulse-dot"></span>
            System Status: <strong style="margin-left:4px">Active</strong>
        </div>
        <div class="topbar-right">
            <div class="live-chip">
                <i class="fas fa-circle" style="font-size:7px;color:#3b82f6"></i>
                <span id="live-clock">--:--:--</span>
            </div>
            <div style="text-align:right">
                <div style="font-size:13.5px;font-weight:700;color:var(--text-1)">{{ Auth::user()->full_name ?? 'Admin User' }}</div>
                <div style="font-size:11px;color:var(--text-muted)">Administrator</div>
            </div>
            @php
                $initials = collect(explode(' ', Auth::user()->full_name ?? 'Admin User'))->take(2)->map(fn($p) => strtoupper($p[0]))->join('');
            @endphp
            <div class="admin-av" id="topbar-avatar">{{ $initials }}</div>
        </div>
    </header>

    <!-- Page body -->
    <main class="page-body">
        <div class="page-wrap">

            <!-- ── 2 Stat Cards (dept-scoped only) ── -->
            <div class="stat-grid">
                <div class="stat-card">
                    <div>
                        <div class="stat-lbl">Users in Your Department</div>
                        <div class="stat-val" id="stat-users">{{ number_format($totalUsers ?? 0) }}</div>
                        <div style="font-size:11px;color:#22c55e;margin-top:3px;display:flex;align-items:center;gap:3px">
                            <span class="pulse-dot" style="width:6px;height:6px;animation:none;flex-shrink:0"></span>
                            <span>Live count</span>
                        </div>
                    </div>
                    <div class="stat-icon si-blue"><i class="fas fa-users"></i></div>
                </div>
                <div class="stat-card">
                    <div>
                        <div class="stat-lbl">Your Active Exams</div>
                        <div class="stat-val" id="stat-exams">{{ $activeExams ?? 0 }}</div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:3px">Running right now</div>
                    </div>
                    <div class="stat-icon si-green"><i class="fas fa-file-pen"></i></div>
                </div>
            </div>

            <!-- Flash messages -->
            @if(session('success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:12px 16px;margin-bottom:18px;color:#15803d;font-size:13px;display:flex;align-items:center;gap:8px">
                <i class="fas fa-circle-check"></i> {{ session('success') }}
            </div>
            @endif
            @if($errors->any())
            <div style="background:#fff1f2;border:1px solid #fecdd3;border-radius:10px;padding:12px 16px;margin-bottom:18px;color:#be123c;font-size:13px">
                <div style="font-weight:700;margin-bottom:6px"><i class="fas fa-triangle-exclamation"></i> Please fix the following:</div>
                <ul style="padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <!-- Scope notice -->
            <div class="scope-notice">
                <i class="fas fa-circle-info" style="font-size:14px;flex-shrink:0;margin-top:1px"></i>
                <span>
                    Settings on this page apply <strong>only to your department's exams</strong>.
                    Platform-wide controls (database, system health, log retention, session management) are managed by Super Admin.
                </span>
            </div>

            <!-- Config header -->
            <div class="cfg-header">
                <div style="display:flex;align-items:center;gap:14px">
                    <div style="width:44px;height:44px;border-radius:11px;background:#eff6ff;border:1px solid #bfdbfe;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                        <i class="fas fa-sliders" style="color:#2563eb;font-size:17px"></i>
                    </div>
                    <div>
                        <div class="cfg-title">Settings</div>
                        <div class="cfg-sub">Proctoring rules, exam parameters, and your admin profile — all scoped to your department</div>
                    </div>
                </div>
                <!-- Tabs -->
                <div class="tab-group">
                    <button class="tab-btn active" id="tab-btn-exam"    onclick="switchTab('exam')">
                        <i class="fas fa-shield-halved" style="font-size:11px;margin-right:4px"></i> Exam Rules
                    </button>
                    <button class="tab-btn"         id="tab-btn-profile" onclick="switchTab('profile')">
                        <i class="fas fa-user-gear" style="font-size:11px;margin-right:4px"></i> My Profile
                    </button>
                </div>
            </div>

            <!-- ════ TAB: EXAM RULES ════ -->
            <div id="tab-exam">
                <form method="POST" action="{{ route('admin.settings.rules') }}" id="exam-rules-form">
                    @csrf

                    <!-- 1. Live Proctoring & Integrity Rules -->
                    <div class="card">
                        <div class="card-head" style="background:linear-gradient(135deg,#fffbeb,#fef9c3)">
                            <div class="card-head-icon" style="background:#fef08a;color:#b45309">
                                <i class="fas fa-eye"></i>
                            </div>
                            <div>
                                <div class="card-title">Live Proctoring &amp; Integrity Rules</div>
                                <div class="card-desc">Control how violations are handled during your exams</div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="f-grid-2" style="margin-bottom:18px">
                                <div class="f-group" style="margin-bottom:0">
                                    <label class="f-label" for="max-switches">
                                        <i class="fas fa-repeat" style="font-size:10px;margin-right:3px"></i>
                                        Max Allowed Tab Switches
                                    </label>
                                    <input type="number" name="proctor_max_switches" id="max-switches"
                                           class="f-input" value="{{ old('proctor_max_switches', $settings->proctor_max_switches ?? 3) }}"
                                           min="0" max="20">
                                    <div class="f-hint">Exam is auto-locked after this limit</div>
                                </div>
                                <div class="f-group" style="margin-bottom:0">
                                    <label class="f-label" for="warn-threshold">
                                        <i class="fas fa-bell" style="font-size:10px;margin-right:3px"></i>
                                        Warning Threshold
                                    </label>
                                    <input type="number" name="proctor_warn_threshold" id="warn-threshold"
                                           class="f-input" value="{{ old('proctor_warn_threshold', $settings->proctor_warn_threshold ?? 2) }}"
                                           min="0" max="20">
                                    <div class="f-hint">Student sees a warning at this switch count</div>
                                </div>
                            </div>

                            <label class="toggle-row" for="block-rightclick">
                                <input type="checkbox" class="custom-cb" name="block_right_click" id="block-rightclick"
                                    {{ old('block_right_click', $settings->block_right_click ?? true) ? 'checked' : '' }}>
                                <div>
                                    <div class="toggle-label">Block Right-Click &amp; Copy-Paste</div>
                                    <div class="toggle-sub">Prevents students from right-clicking or using keyboard copy shortcuts during exams</div>
                                </div>
                            </label>

                            <label class="toggle-row" for="force-fullscreen">
                                <input type="checkbox" class="custom-cb" name="force_fullscreen" id="force-fullscreen"
                                    {{ old('force_fullscreen', $settings->force_fullscreen ?? true) ? 'checked' : '' }}>
                                <div>
                                    <div class="toggle-label">Enforce Fullscreen Mode</div>
                                    <div class="toggle-sub">Exam auto-expands to fullscreen; exiting the window is logged as a violation</div>
                                </div>
                            </label>

                            <label class="toggle-row" for="webcam-monitor">
                                <input type="checkbox" class="custom-cb" name="webcam_monitor" id="webcam-monitor"
                                    {{ old('webcam_monitor', $settings->webcam_monitor ?? false) ? 'checked' : '' }}>
                                <div>
                                    <div class="toggle-label">
                                        Webcam Monitoring
                                        <span style="background:#eff6ff;color:#1d4ed8;border:1px solid #bfdbfe;font-size:10px;font-weight:700;padding:1px 7px;border-radius:99px;margin-left:6px">Optional</span>
                                    </div>
                                    <div class="toggle-sub">Enable webcam-based identity verification during live exams</div>
                                </div>
                            </label>

                        </div>
                    </div>

                    <!-- 2. My Department's Exam Parameters -->
                    <div class="card">
                        <div class="card-head" style="background:linear-gradient(135deg,#f8fafc,#eff6ff)">
                            <div class="card-head-icon" style="background:#dbeafe;color:#2563eb">
                                <i class="fas fa-sliders"></i>
                            </div>
                            <div>
                                <div class="card-title">My Department's Exam Parameters</div>
                                <div class="card-desc">Default values applied to exams created in your department — not platform-wide</div>
                            </div>
                        </div>
                        <div class="card-body">

                            <div class="f-grid-2">
                                <div class="f-group">
                                    <label class="f-label" for="sync-interval">
                                        <i class="fas fa-rotate" style="font-size:10px;margin-right:3px"></i>
                                        Browser Sync Interval
                                    </label>
                                    <select name="sync_interval" id="sync-interval" class="f-input">
                                        <option value="5"  {{ old('sync_interval', $settings->sync_interval ?? 10) == 5  ? 'selected' : '' }}>Every 5 seconds</option>
                                        <option value="10" {{ old('sync_interval', $settings->sync_interval ?? 10) == 10 ? 'selected' : '' }}>Every 10 seconds</option>
                                        <option value="30" {{ old('sync_interval', $settings->sync_interval ?? 10) == 30 ? 'selected' : '' }}>Every 30 seconds</option>
                                    </select>
                                    <div class="f-hint">How often the exam syncs answers to the server</div>
                                </div>
                                <div class="f-group">
                                    <label class="f-label" for="passing-rate">
                                        <i class="fas fa-percent" style="font-size:10px;margin-right:3px"></i>
                                        Default Passing Score (%)
                                    </label>
                                    <input type="number" name="passing_rate" id="passing-rate"
                                           class="f-input" value="{{ old('passing_rate', $settings->passing_rate ?? 50) }}"
                                           min="0" max="100">
                                    <div class="f-hint">Minimum score to pass — applies to your department's exams</div>
                                </div>
                                <div class="f-group">
                                    <label class="f-label" for="time-limit">
                                        <i class="fas fa-clock" style="font-size:10px;margin-right:3px"></i>
                                        Default Time Limit (minutes)
                                    </label>
                                    <input type="number" name="default_time_limit" id="time-limit"
                                           class="f-input" value="{{ old('default_time_limit', $settings->default_time_limit ?? 60) }}"
                                           min="5" max="600">
                                    <div class="f-hint">Can be overridden when creating individual exams</div>
                                </div>
                                <div class="f-group">
                                    <label class="f-label" for="max-attempts">
                                        <i class="fas fa-redo" style="font-size:10px;margin-right:3px"></i>
                                        Max Retake Attempts
                                    </label>
                                    <input type="number" name="max_attempts" id="max-attempts"
                                           class="f-input" value="{{ old('max_attempts', $settings->max_attempts ?? 1) }}"
                                           min="1" max="10">
                                    <div class="f-hint">How many times a student may retake an exam</div>
                                </div>
                            </div>

                            <div class="divider"></div>
                            <div style="display:flex;gap:10px;justify-content:flex-end;flex-wrap:wrap">
                                <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
                                    <i class="fas fa-xmark"></i> Cancel
                                </a>
                                <button type="submit" class="btn-primary" id="save-exam-btn" onclick="onSaveExam(event)">
                                    <i class="fas fa-floppy-disk"></i> Save Exam Rules
                                </button>
                            </div>

                        </div>
                    </div>

                </form>
            </div>

            <!-- ════ TAB: MY PROFILE ════ -->
            <div id="tab-profile" style="display:none">

                <div class="card">
                    <div class="card-head" style="background:linear-gradient(135deg,#f8fafc,#eff6ff)">
                        <div class="card-head-icon" style="background:#dbeafe;color:#2563eb">
                            <i class="fas fa-user-gear"></i>
                        </div>
                        <div>
                            <div class="card-title">My Profile</div>
                            <div class="card-desc">Update your display name and profile photo</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.settings.profile') }}" enctype="multipart/form-data" id="profile-form">
                            @csrf

                            <!-- Avatar row -->
                            <div style="display:flex;align-items:center;gap:18px;padding:18px;background:#f8fafc;border:1px solid var(--input-br);border-radius:12px;margin-bottom:20px">
                                <div class="av-wrap" onclick="document.getElementById('avatar-input').click()" title="Click to change photo">
                                    <img id="av-preview"
                                         src="{{ asset(Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->full_name ?? 'Admin') . '&background=f59e0b&color=fff&size=200') }}"
                                         alt="Profile photo">
                                    <div class="av-overlay"><i class="fas fa-camera"></i></div>
                                </div>
                                <div>
                                    <div style="font-size:14px;font-weight:700;color:var(--text-1)">Profile Photo</div>
                                    <div style="font-size:12px;color:var(--text-muted);margin-top:3px">JPG, PNG or WEBP — max 2 MB</div>
                                    <button type="button" onclick="document.getElementById('avatar-input').click()"
                                            style="margin-top:8px;padding:6px 14px;background:#fff;border:1px solid var(--input-br);border-radius:7px;font-size:12px;font-weight:600;color:var(--text-2);cursor:pointer;font-family:'Inter',sans-serif;transition:all 0.15s"
                                            onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='#fff'">
                                        <i class="fas fa-upload" style="font-size:10px;margin-right:4px"></i> Choose File
                                    </button>
                                    <input type="file" id="avatar-input" name="avatar_photo" accept="image/*" style="display:none" onchange="previewAvatar(this)">
                                </div>
                            </div>

                            <!-- Name -->
                            <div class="f-group">
                                <label class="f-label" for="full-name">
                                    <i class="fas fa-id-badge" style="font-size:10px;margin-right:3px"></i>
                                    Display Name
                                </label>
                                <input type="text" name="full_name" id="full-name" class="f-input"
                                       value="{{ Auth::user()->full_name ?? 'Admin User' }}"
                                       required oninput="updateInitials(this.value)">
                                <div class="f-hint">Shown in the sidebar header and all pages</div>
                            </div>

                            <!-- Email (read-only) -->
                            <div class="f-group">
                                <label class="f-label">
                                    <i class="fas fa-envelope" style="font-size:10px;margin-right:3px"></i>
                                    Email Address
                                </label>
                                <input type="email" class="f-input"
                                       value="{{ Auth::user()->email ?? '' }}" readonly
                                       style="background:#f8fafc;color:var(--text-muted);cursor:not-allowed">
                                <div class="f-hint">Contact Super Admin to change your email address</div>
                            </div>

                            <!-- Password link -->
                            <div style="display:flex;align-items:flex-start;gap:9px;background:#f0f9ff;border:1px solid #bae6fd;border-radius:9px;padding:11px 14px;margin-bottom:20px;font-size:12.5px;color:#0369a1;line-height:1.55">
                                <i class="fas fa-lock" style="font-size:12px;flex-shrink:0;margin-top:1px"></i>
                                <span>To change your password, visit the <a href="{{ route('admin.settings.password') ?? '#' }}" style="color:#0369a1;font-weight:700;text-decoration:underline">Change Password</a> page.</span>
                            </div>

                            <div class="divider"></div>
                            <div style="display:flex;gap:10px;justify-content:flex-end">
                                <a href="{{ route('admin.dashboard') }}" class="btn-ghost">
                                    <i class="fas fa-xmark"></i> Cancel
                                </a>
                                <button type="submit" class="btn-primary" id="save-profile-btn" onclick="onSaveProfile(event)">
                                    <i class="fas fa-floppy-disk"></i> Save Profile
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div><!-- /tab-profile -->

        </div><!-- /page-wrap -->
    </main>
</div>

<!-- Toast -->
<div id="toast"><i id="toast-icon" class="fas fa-circle-check"></i> <span id="toast-text"></span></div>

<!-- ════════════════ SCRIPTS ════════════════ -->
<script>
(function () {
    'use strict';

    /* ── Live clock ── */
    function tick() { document.getElementById('live-clock').textContent = new Date().toLocaleTimeString(); }
    tick(); setInterval(tick, 1000);

    /* ── Toast ── */
    function toast(msg, icon = 'fa-circle-check') {
        const el = document.getElementById('toast');
        document.getElementById('toast-icon').className = 'fas ' + icon;
        document.getElementById('toast-text').textContent = msg;
        el.classList.add('show');
        setTimeout(() => el.classList.remove('show'), 3500);
    }

    /* ── Tab switch ── */
    window.switchTab = function (tab) {
        document.getElementById('tab-exam').style.display    = tab === 'exam'    ? '' : 'none';
        document.getElementById('tab-profile').style.display = tab === 'profile' ? '' : 'none';
        document.getElementById('tab-btn-exam').className    = 'tab-btn' + (tab === 'exam'    ? ' active' : '');
        document.getElementById('tab-btn-profile').className = 'tab-btn' + (tab === 'profile' ? ' active' : '');
    };

    /* ── Real-time dept stat polling (users + exams only) ── */
    function refreshStats() {
        fetch("{{ route('admin.dashboard.api') }}")
            .then(r => r.json())
            .then(d => {
                if (d.totalUsers !== undefined) document.getElementById('stat-users').textContent = Number(d.totalUsers).toLocaleString();
                if (d.activeExams !== undefined) document.getElementById('stat-exams').textContent = d.activeExams;
            })
            .catch(() => {});
    }
    refreshStats();
    setInterval(refreshStats, 3000);

    /* ── Save exam rules spinner ── */
    window.onSaveExam = function (e) {
        const btn = document.getElementById('save-exam-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';
        setTimeout(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-floppy-disk"></i> Save Exam Rules'; }, 6000);
    };

    /* ── Save profile spinner ── */
    window.onSaveProfile = function (e) {
        const btn = document.getElementById('save-profile-btn');
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';
        setTimeout(() => { btn.disabled = false; btn.innerHTML = '<i class="fas fa-floppy-disk"></i> Save Profile'; }, 6000);
    };

    /* ── Avatar preview ── */
    window.previewAvatar = function (input) {
        if (!input.files || !input.files[0]) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('av-preview').src = e.target.result;
            toast('Photo ready — click Save Profile to apply');
        };
        reader.readAsDataURL(input.files[0]);
    };

    /* ── Update initials in topbar as name is typed ── */
    window.updateInitials = function (name) {
        const parts = name.trim().split(' ').filter(Boolean);
        const init  = parts.slice(0, 2).map(p => p[0].toUpperCase()).join('');
        document.getElementById('topbar-avatar').textContent = init || '?';
    };

    /* ── Sign out ── */
    window.confirmLogout = function () {
        if (confirm('Sign out of the admin console?')) {
            document.getElementById('logout-form').submit();
        }
    };

})();
</script>
</body>
</html>