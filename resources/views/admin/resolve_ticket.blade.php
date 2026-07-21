<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Resolve support ticket — ExamSystem Admin Panel">
    <title>Resolve Ticket #{{ $ticket->ticket_no }} — ExamSystem Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Anti-flash dark mode (matches the dashboard) -->
    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        /* ═══════════════════════════════════════════════
           DESIGN SYSTEM — matches all ExamSystem pages
        ═══════════════════════════════════════════════ */
        :root {
            --sidebar-w      : 256px;
            --body-bg        : #f1f5f9;
            --card-bg        : #ffffff;
            --card-border    : #e8edf5;
            --card-shadow    : 0 1px 4px rgba(0,0,0,0.06),0 6px 24px rgba(0,0,0,0.04);
            --text-primary   : #0f172a;
            --text-secondary : #64748b;
            --text-muted     : #94a3b8;
            --blue           : #2563eb;
            --blue-dark      : #1d4ed8;
            --input-bg       : #f8fafc;
            --input-border   : #e2e8f0;
        }

        /* See settings.blade.php for why the old `margin:0;padding:0` reset
           was removed — it silently stripped Tailwind's spacing utilities
           (including inside the shared sidebar partial) sitewide. */
        *, *::before, *::after { box-sizing: border-box; }
        [x-cloak] { display: none !important; }

        /* ── Shared admin brand + nav (matches Dashboard/User Management) ── */
        .admin-brand-gradient { background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%); }
        .admin-nav-active { background: linear-gradient(135deg,#2563eb 0%,#1e40af 100%); color: #fff; box-shadow: 0 4px 14px rgba(37,99,235,0.35); }
        .nav-link { transition: all 0.18s cubic-bezier(0.4,0,0.2,1); }
        .dark-surface { background:#0f172a; }
        .dark-card { --card-bg:#1e293b; --card-br:#334155; --row-hover:#1e293b; }

        body {
            font-family : 'Inter', -apple-system, sans-serif;
            background  : var(--body-bg);
            color       : var(--text-primary);
            min-height  : 100vh;
            display     : flex;
        }

        /* ── Main ── */
        .main { margin-left:var(--sidebar-w); flex:1; display:flex; flex-direction:column; min-height:100vh; }

        /* ── Top bar ── */
        .back-pill {
            display:inline-flex; align-items:center; gap:7px;
            padding:7px 16px; background:#eff6ff; border:1px solid #bfdbfe;
            border-radius:9999px; color:#1d4ed8; font-size:13px; font-weight:600;
            text-decoration:none; transition:all 0.15s;
        }
        .back-pill:hover { background:#dbeafe; transform:translateX(-2px); box-shadow:0 2px 8px rgba(37,99,235,0.15); }

        /* ── Body ── */
        .page-body { flex:1; padding:28px 32px; }
        .page-wrap  { max-width:940px; margin:0 auto; }

        /* ── Page title ── */
        .page-title-row { margin-bottom:22px; display:flex; align-items:center; gap:12px; }
        .page-icon { width:40px; height:40px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:#eff6ff; border:1px solid #bfdbfe; flex-shrink:0; }
        .page-title   { font-size:19px; font-weight:800; color:var(--text-primary); letter-spacing:-0.4px; }
        .page-subtitle { font-size:13px; color:var(--text-muted); margin-top:2px; }

        /* ── Cards ── */
        .card { background:var(--card-bg); border:1px solid var(--card-border); border-radius:16px; box-shadow:var(--card-shadow); overflow:hidden; }
        .card-head { padding:18px 22px; border-bottom:1px solid #f1f5f9; }
        .card-head-blue  { background:linear-gradient(135deg,#f8fafc,#eff6ff); }
        .card-head-green { background:#f0fdf4; border-bottom-color:#dcfce7; }
        .card-body { padding:22px; }
        .card-title { font-size:14px; font-weight:700; color:var(--text-primary); display:flex; align-items:center; gap:8px; }
        .card-icon  { width:28px; height:28px; border-radius:7px; display:flex; align-items:center; justify-content:center; font-size:12px; }

        /* ── Grid ── */
        .resolve-grid { display:grid; grid-template-columns:2fr 1fr; gap:20px; align-items:start; }
        @media(max-width:768px) {
            .resolve-grid { grid-template-columns:1fr; }
            .sidebar { display:none; } .main { margin-left:0; }
            .page-body { padding:16px; }
        }

        /* ── Badges ── */
        .badge {
            display:inline-flex; align-items:center; gap:5px;
            padding:3px 10px; border-radius:9999px; font-size:11px; font-weight:600;
            border:1px solid transparent;
        }
        .badge-urgent  { background:#fff1f2; color:#be123c; border-color:#fecdd3; }
        .badge-high    { background:#fffbeb; color:#92400e; border-color:#fde68a; }
        .badge-medium  { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
        .badge-low     { background:#f8fafc; color:#64748b; border-color:#e2e8f0; }
        .badge-s-pending      { background:#fffbeb; color:#92400e; border-color:#fde68a; }
        .badge-s-investigating { background:#eff6ff; color:#1d4ed8; border-color:#bfdbfe; }
        .badge-s-resolved     { background:#f0fdf4; color:#15803d; border-color:#bbf7d0; }

        /* ── Ticket header ── */
        .ticket-ref { font-family:'JetBrains Mono',monospace; font-size:15px; font-weight:700; letter-spacing:1px; color:var(--text-primary); }

        /* ── Reporter row ── */
        .reporter-row { display:flex; align-items:center; gap:12px; padding:14px 0; border-bottom:1px solid #f1f5f9; }
        .reporter-av  { width:44px; height:44px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:15px; font-weight:700; color:#fff; flex-shrink:0; }

        /* ── Section label ── */
        .slabel { font-size:10.5px; font-weight:700; letter-spacing:.8px; text-transform:uppercase; color:var(--text-muted); margin-top:18px; margin-bottom:8px; display:flex; align-items:center; gap:5px; }

        /* ── Description box ── */
        .desc-box { background:#f8fafc; border:1px solid #e2e8f0; border-left:4px solid #93c5fd; border-radius:0 8px 8px 0; padding:14px 16px; font-size:13.5px; line-height:1.75; white-space:pre-wrap; word-break:break-word; }

        /* ── Screenshot ── */
        .ss-img { width:100%; max-height:260px; object-fit:cover; border-radius:12px; border:1px solid var(--card-border); cursor:pointer; transition:transform 0.2s,box-shadow 0.2s; }
        .ss-img:hover { transform:scale(1.01); box-shadow:0 4px 16px rgba(0,0,0,0.1); }

        /* ── Timeline ── */
        .tl-item { display:flex; gap:12px; padding:11px 0; border-bottom:1px dashed #e8edf5; }
        .tl-item:last-child { border:none; }
        .tl-dot  { width:10px; height:10px; border-radius:50%; background:#93c5fd; border:2px solid #bfdbfe; margin-top:4px; flex-shrink:0; }

        /* ── Form ── */
        .f-label { display:block; font-size:12px; font-weight:600; color:var(--text-secondary); margin-bottom:6px; }
        .f-input {
            width:100%; background:var(--input-bg); border:1px solid var(--input-border);
            border-radius:8px; padding:10px 14px; font-size:13.5px; font-family:'Inter',sans-serif;
            color:var(--text-primary); outline:none; transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;
            appearance:none;
        }
        .f-input:focus { background:#fff; border-color:#93c5fd; box-shadow:0 0 0 3px rgba(147,197,253,0.25); }
        textarea.f-input { resize:vertical; line-height:1.65; }
        #status-sel.s-pending      { border-left:4px solid #f59e0b; }
        #status-sel.s-investigating { border-left:4px solid #2563eb; }
        #status-sel.s-resolved     { border-left:4px solid #16a34a; }
        .char-count { font-size:11.5px; color:var(--text-muted); text-align:right; margin-top:4px; }

        /* ── Buttons ── */
        .btn-primary {
            display:inline-flex; align-items:center; justify-content:center; gap:8px; width:100%;
            padding:12px 20px; background:linear-gradient(135deg,#2563eb,#1d4ed8);
            color:#fff; border:none; border-radius:8px; font-size:14px; font-weight:600;
            font-family:'Inter',sans-serif; cursor:pointer;
            box-shadow:0 2px 8px rgba(37,99,235,0.3);
            transition:opacity 0.15s,transform 0.1s,box-shadow 0.15s;
        }
        .btn-primary:hover  { opacity:0.92; transform:translateY(-1px); box-shadow:0 4px 14px rgba(37,99,235,0.38); }
        .btn-primary:active { transform:translateY(0); }
        .btn-primary:disabled { opacity:0.65; cursor:not-allowed; transform:none; }
        .btn-cancel {
            display:inline-flex; align-items:center; justify-content:center; gap:7px; width:100%;
            padding:10px 20px; background:transparent; border:1px solid var(--card-border);
            border-radius:8px; font-size:13.5px; font-weight:500; color:var(--text-secondary);
            text-decoration:none; cursor:pointer; margin-top:10px; font-family:'Inter',sans-serif;
            transition:background 0.15s,color 0.15s;
        }
        .btn-cancel:hover { background:#f8fafc; color:var(--text-primary); }

        /* ── Info hint ── */
        .info-hint { display:flex; align-items:flex-start; gap:8px; background:#f0f9ff; border:1px solid #bae6fd; border-radius:8px; padding:10px 14px; font-size:12px; color:#0369a1; margin-top:16px; line-height:1.5; }

        /* ── Draft saved indicator ── */
        .draft-indicator { font-size:11px; color:var(--text-muted); display:flex; align-items:center; gap:4px; margin-top:4px; min-height:18px; }
        .draft-saved  { color:#15803d; }
        .draft-saving { color:#92400e; }

        /* ── Status conflict warning ── */
        .conflict-banner {
            display:none; align-items:center; gap:10px;
            background:#fffbeb; border:1px solid #fde68a; border-radius:10px;
            padding:12px 16px; margin-bottom:18px; font-size:13px; color:#92400e; font-weight:500;
        }
        .conflict-banner.show { display:flex; }

        /* ── Activity feed ── */
        .activity-feed { margin-top:4px; }
        .activity-loading { font-size:12px; color:var(--text-muted); text-align:center; padding:12px 0; }

        /* ── Toast ── */
        #toast {
            position:fixed; bottom:28px; right:28px; z-index:999;
            background:#0f172a; color:#fff; padding:11px 18px; border-radius:12px;
            font-size:13px; font-weight:500; display:flex; align-items:center; gap:8px;
            box-shadow:0 4px 20px rgba(0,0,0,0.18); opacity:0;
            transform:translateY(10px); transition:all 0.3s ease;
            pointer-events:none;
        }
        #toast.show { opacity:1; transform:translateY(0); }

        /* ── Pulse ── */
        @keyframes pulse-ring {
            0%   { box-shadow:0 0 0 0 rgba(34,197,94,.5); }
            70%  { box-shadow:0 0 0 6px rgba(34,197,94,0); }
            100% { box-shadow:0 0 0 0 rgba(34,197,94,0); }
        }
        .pulse-dot { width:8px; height:8px; border-radius:50%; background:#22c55e; display:inline-block; animation:pulse-ring 1.8s ease-out infinite; }

        /* Status preview row */
        .preview-row { display:flex; align-items:center; gap:8px; margin-top:8px; }

        .divider { height:1px; background:#f1f5f9; margin:18px 0; }
    </style>
    @include('partials.notification-styles')
</head>
<body class="antialiased transition-colors duration-300"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="darkMode ? 'dark-surface text-slate-100' : ''">

@include('partials.admin-sidebar')

<!-- ════════════════ MAIN ════════════════ -->
<div class="main">

    <!-- Top bar -->
    <header class="flex items-center justify-between px-7 py-3.5 border-b sticky top-0 z-40"
             :class="darkMode ? 'bg-slate-900 border-slate-800' : 'bg-white border-slate-100'"
             style="box-shadow:0 1px 4px rgba(0,0,0,0.04)">
        <a href="{{ route('admin.support') }}" class="back-pill">
            <i class="fas fa-arrow-left" style="font-size:11px"></i> Back to Support Desk
        </a>
        <div class="flex items-center gap-3.5">
            <div class="text-xs font-mono flex items-center gap-1.5 border px-3 py-1.5 rounded-xl"
                 :class="darkMode ? 'text-slate-400 bg-slate-800 border-slate-700' : 'text-slate-400 bg-white border-slate-200'">
                <span class="pulse-dot"></span>
                <span id="live-clock">--:--:--</span>
            </div>

            @include('partials.admin-darkmode-toggle')

            @include('partials.admin-notification-bell')

            <div class="flex items-center gap-3 pl-3 border-l" :class="darkMode ? 'border-slate-700' : 'border-slate-200'">
                <div class="text-right hidden sm:block">
                    <div style="font-size:13.5px;font-weight:600;color:var(--text-primary)">{{ Auth::user()->full_name }}</div>
                    <div style="font-size:11px;color:var(--text-muted)">Administrator</div>
                </div>
                @php
                    $initials = collect(explode(' ', Auth::user()->full_name))->take(2)->map(fn($p) => strtoupper($p[0]))->join('');
                @endphp
                <div class="w-9 h-9 rounded-xl flex items-center justify-center text-white font-bold text-sm" style="background:linear-gradient(135deg,#2563eb,#1d4ed8);box-shadow:0 3px 10px rgba(37,99,235,0.3)">{{ $initials }}</div>
            </div>
        </div>
    </header>

    <!-- Page body -->
    <main class="page-body">
        <div class="page-wrap">

            <!-- Page title -->
            <div class="page-title-row">
                <div class="page-icon"><i class="fas fa-ticket" style="color:#2563eb;font-size:16px"></i></div>
                <div>
                    <h1 class="page-title">Resolve Ticket</h1>
                    <p class="page-subtitle">Review the issue, update the status, and send a resolution to the reporter.</p>
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
                <div style="font-weight:700;margin-bottom:6px"><i class="fas fa-triangle-exclamation"></i> Please fix these errors:</div>
                <ul style="padding-left:18px">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
            </div>
            @endif

            <!-- Conflict banner (another agent changed status) -->
            <div class="conflict-banner" id="conflict-banner">
                <i class="fas fa-triangle-exclamation" style="font-size:15px;flex-shrink:0"></i>
                <span id="conflict-text">Status was changed by another agent. Refresh to see the latest.</span>
                <button onclick="location.reload()" style="margin-left:auto;background:#fff8dc;border:1px solid #fde68a;border-radius:7px;padding:4px 12px;font-size:12px;font-weight:700;color:#92400e;cursor:pointer">Refresh</button>
            </div>

            <!-- Two-column grid -->
            <div class="resolve-grid">

                <!-- ════ LEFT: Ticket Details ════ -->
                <div>
                    <div class="card">
                        <!-- Card header -->
                        <div class="card-head card-head-blue">
                            <div class="card-title">
                                <div class="card-icon" style="background:linear-gradient(135deg,#eff6ff,#dbeafe)"><i class="fas fa-ticket" style="color:#2563eb"></i></div>
                                Ticket Details
                            </div>
                            <div style="margin-top:12px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px">
                                <span class="ticket-ref">#{{ $ticket->ticket_no }}</span>
                                @php
                                    $priority = strtolower($ticket->priority ?? 'high');
                                    $pBadge   = ['urgent'=>'badge-urgent','high'=>'badge-high','medium'=>'badge-medium','low'=>'badge-low'][$priority] ?? 'badge-high';
                                    $pEmoji   = ['urgent'=>'🔴','high'=>'🟠','medium'=>'🟡','low'=>'🔵'][$priority] ?? '🟠';
                                    $status   = strtolower($ticket->status ?? 'pending');
                                    $sBadge   = ['pending'=>'badge-s-pending','investigating'=>'badge-s-investigating','in_progress'=>'badge-s-investigating','resolved'=>'badge-s-resolved'][$status] ?? 'badge-s-pending';
                                    $sEmoji   = ['pending'=>'⏳','investigating'=>'🔍','in_progress'=>'🔍','resolved'=>'✅'][$status] ?? '⏳';
                                @endphp
                                <div style="display:flex;gap:6px;flex-wrap:wrap">
                                    <span class="badge {{ $pBadge }}" id="priority-hdr-badge">{{ $pEmoji }} {{ ucfirst($priority) }}</span>
                                    <span class="badge {{ $sBadge }}" id="status-hdr-badge">{{ $sEmoji }} {{ ucfirst(str_replace('_',' ',$status)) }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Card body -->
                        <div class="card-body">

                            <!-- Reporter -->
                            @php
                                $ri = collect(explode(' ',$ticket->reporter_name))->take(2)->map(fn($p)=>strtoupper($p[0]))->join('');
                                $avColors = ['#2563eb','#7c3aed','#059669','#dc2626','#0891b2','#ea580c'];
                                $avBg = $avColors[abs(crc32($ticket->reporter_name??''))%count($avColors)];
                            @endphp
                            <div class="reporter-row">
                                <div class="reporter-av" style="background:linear-gradient(135deg,{{ $avBg }},{{ $avBg }}bb)">{{ $ri }}</div>
                                <div>
                                    <div style="font-size:14px;font-weight:700;color:var(--text-primary)">{{ $ticket->reporter_name }}</div>
                                    <div style="font-family:'JetBrains Mono',monospace;font-size:12px;color:var(--text-secondary);margin-top:2px">{{ $ticket->reporter_email }}</div>
                                    <div style="font-size:11.5px;color:var(--text-muted);margin-top:3px">
                                        <i class="fas fa-clock" style="font-size:9px"></i>
                                        Submitted {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->diffForHumans() : '—' }}
                                        &nbsp;·&nbsp; {{ $ticket->created_at ? \Carbon\Carbon::parse($ticket->created_at)->format('d M Y, H:i') : '' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="slabel"><i class="fas fa-layer-group" style="font-size:9px"></i> Category</div>
                            <span class="badge badge-medium" style="font-size:12px;padding:5px 13px">
                                <i class="fas fa-tag" style="font-size:10px"></i> {{ $ticket->issue_category ?? 'General' }}
                            </span>

                            <!-- Description -->
                            <div class="slabel"><i class="fas fa-align-left" style="font-size:9px"></i> Issue Description</div>
                            <div class="desc-box">{{ $ticket->description }}</div>

                            <!-- Screenshot -->
                            @if(!empty($ticket->screenshot))
                            <div class="slabel"><i class="fas fa-image" style="font-size:9px"></i> Attachment</div>
                            <img src="{{ asset('storage/'.$ticket->screenshot) }}"
                                 class="ss-img" alt="Ticket screenshot"
                                 onclick="window.open(this.src,'_blank')"
                                 title="Click to open full size">
                            <div style="font-size:11px;color:var(--text-muted);margin-top:5px;text-align:center">
                                <i class="fas fa-expand" style="font-size:9px"></i> Click image to open full size
                            </div>
                            @endif

                            <!-- Previous comments timeline -->
                            @if(!empty($ticket->admin_comment))
                            @php
                                $raw = $ticket->admin_comment;
                                $comments = is_array($raw) ? $raw
                                    : (is_string($raw) && str_starts_with(trim($raw),'[') ? json_decode($raw,true)??[] : [['text'=>$raw,'at'=>$ticket->updated_at]]);
                            @endphp
                            @if(count($comments) > 0)
                            <div class="slabel"><i class="fas fa-clock-rotate-left" style="font-size:9px"></i> Previous Responses</div>
                            @foreach($comments as $c)
                            <div class="tl-item">
                                <div class="tl-dot"></div>
                                <div>
                                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:3px">
                                        <i class="fas fa-user-tie" style="font-size:9px"></i> Administrator
                                        @if(!empty($c['at'])) &nbsp;·&nbsp; {{ \Carbon\Carbon::parse($c['at'])->format('d M Y, H:i') }} @endif
                                    </div>
                                    <div style="font-size:13px;color:var(--text-secondary);line-height:1.6">{{ is_array($c) ? ($c['text']??'') : $c }}</div>
                                </div>
                            </div>
                            @endforeach
                            @endif
                            @endif

                            <!-- Live activity feed -->
                            <div class="slabel">
                                <i class="fas fa-rss" style="font-size:9px"></i> Live Activity
                                <span style="margin-left:auto;font-size:10px;color:var(--text-muted);font-family:'JetBrains Mono',monospace" id="activity-refresh-label">refreshing…</span>
                            </div>
                            <div class="activity-feed" id="activity-feed">
                                <div class="activity-loading"><i class="fas fa-rotate fa-spin"></i> Loading…</div>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ════ RIGHT: Resolution Panel ════ -->
                <div>
                    <div class="card">
                        <div class="card-head card-head-green">
                            <div class="card-title">
                                <div class="card-icon" style="background:#dcfce7"><i class="fas fa-circle-check" style="color:#16a34a"></i></div>
                                Resolution Panel
                            </div>
                            <div style="font-size:12px;color:#15803d;margin-top:4px;line-height:1.4">
                                Update status, write a resolution note, and assign an agent.
                            </div>
                        </div>
                        <div class="card-body">
                            <form method="POST"
                                  action="{{ route('admin.support.resolve', $ticket->ticket_id) }}"
                                  id="resolution-form" novalidate>
                                @csrf
                                @method('PUT')

                                <!-- Status -->
                                <div style="margin-bottom:18px">
                                    <label class="f-label" for="status-sel">
                                        <i class="fas fa-circle-half-stroke" style="font-size:10px"></i> Ticket Status
                                    </label>
                                    <select class="f-input" id="status-sel" name="status" required>
                                        <option value="pending"       {{ old('status',$ticket->status)==='pending'       ?'selected':'' }}>⏳ Pending</option>
                                        <option value="in_progress"   {{ old('status',$ticket->status)==='in_progress'   ?'selected':'' }}>🔍 Investigating</option>
                                        <option value="resolved"      {{ old('status',$ticket->status)==='resolved'      ?'selected':'' }}>✅ Resolved</option>
                                    </select>
                                    <!-- Live preview badge -->
                                    <div class="preview-row">
                                        <span style="font-size:11.5px;color:var(--text-muted)">Preview:</span>
                                        <span class="badge" id="status-preview"></span>
                                    </div>
                                </div>

                                <!-- Priority -->
                                <div style="margin-bottom:18px">
                                    <label class="f-label" for="priority-sel">
                                        <i class="fas fa-flag" style="font-size:10px"></i> Priority Level
                                    </label>
                                    <select class="f-input" id="priority-sel" name="priority">
                                        <option value="urgent" {{ old('priority',$ticket->priority??'high')==='urgent'?'selected':'' }}>🔴 Urgent</option>
                                        <option value="high"   {{ old('priority',$ticket->priority??'high')==='high'  ?'selected':'' }}>🟠 High</option>
                                        <option value="medium" {{ old('priority',$ticket->priority??'high')==='medium'?'selected':'' }}>🟡 Medium</option>
                                        <option value="low"    {{ old('priority',$ticket->priority??'high')==='low'   ?'selected':'' }}>🔵 Low</option>
                                    </select>
                                </div>

                                <!-- Resolution note -->
                                <div style="margin-bottom:18px">
                                    <label class="f-label" for="admin-comment">
                                        <i class="fas fa-pen-to-square" style="font-size:10px"></i> Resolution Note
                                    </label>
                                    <textarea class="f-input" id="admin-comment" name="admin_comment"
                                              rows="5" maxlength="2000"
                                              placeholder="Describe what you did to resolve this issue, or any instructions for the reporter…">{{ old('admin_comment') }}</textarea>
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-top:4px">
                                        <div class="draft-indicator" id="draft-status"></div>
                                        <div class="char-count"><span id="char-count">0</span> / 2000</div>
                                    </div>
                                </div>

                                <!-- Assign agent -->
                                <div style="margin-bottom:18px">
                                    <label class="f-label" for="agent-sel">
                                        <i class="fas fa-user-tie" style="font-size:10px"></i> Assign to Agent
                                    </label>
                                    <select class="f-input" id="agent-sel" name="assigned_agent_id">
                                        <option value="">— Unassigned —</option>
                                        @foreach($agents ?? [] as $agent)
                                        <option value="{{ $agent->id }}" {{ old('assigned_agent_id')==$agent->id?'selected':'' }}>{{ $agent->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="divider"></div>

                                <!-- Submit -->
                                <button type="submit" class="btn-primary" id="submit-btn">
                                    <i class="fas fa-paper-plane"></i> Save &amp; Send Resolution
                                </button>

                                <a href="{{ route('admin.support') }}" class="btn-cancel">
                                    <i class="fas fa-xmark" style="font-size:12px"></i> Cancel
                                </a>

                                <!-- Info hint -->
                                <div class="info-hint">
                                    <i class="fas fa-link" style="font-size:12px;flex-shrink:0;margin-top:1px"></i>
                                    <span>Changes appear <strong>instantly</strong> in the Support Desk queue and notify the reporter.</span>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>

            </div><!-- /grid -->
        </div><!-- /wrap -->
    </main>
</div>

<!-- Toast -->
<div id="toast"><i class="fas fa-circle-check"></i> <span id="toast-text"></span></div>

<!-- ════════════════ SCRIPTS ════════════════ -->
<script>
(function() {
    'use strict';

    if (window.lucide) lucide.createIcons();

    /* ── Constants ── */
    const TICKET_ID   = {{ $ticket->ticket_id }};
    const DRAFT_KEY   = 'resolve_draft_' + TICKET_ID;

    const STATUS_CFG = {
        pending:     { emoji:'⏳', label:'Pending',       badgeCls:'badge-s-pending',       selCls:'s-pending' },
        in_progress: { emoji:'🔍', label:'Investigating', badgeCls:'badge-s-investigating', selCls:'s-investigating' },
        investigating:{ emoji:'🔍', label:'Investigating', badgeCls:'badge-s-investigating', selCls:'s-investigating' },
        resolved:    { emoji:'✅', label:'Resolved',      badgeCls:'badge-s-resolved',      selCls:'s-resolved' },
    };
    const PRIORITY_CFG = {
        urgent: { emoji:'🔴', label:'Urgent', badgeCls:'badge-urgent'  },
        high:   { emoji:'🟠', label:'High',   badgeCls:'badge-high'    },
        medium: { emoji:'🟡', label:'Medium', badgeCls:'badge-medium'  },
        low:    { emoji:'🔵', label:'Low',    badgeCls:'badge-low'     },
    };
    const ALL_S_BADGE  = ['badge-s-pending','badge-s-investigating','badge-s-resolved'];
    const ALL_S_SEL    = ['s-pending','s-investigating','s-resolved'];
    const ALL_P_BADGE  = ['badge-urgent','badge-high','badge-medium','badge-low'];

    /* ── DOM refs ── */
    const statusSel    = document.getElementById('status-sel');
    const prioritySel  = document.getElementById('priority-sel');
    const preview      = document.getElementById('status-preview');
    const statusHdrBdg = document.getElementById('status-hdr-badge');
    const priorityHdrB = document.getElementById('priority-hdr-badge');
    const textarea     = document.getElementById('admin-comment');
    const charCount    = document.getElementById('char-count');
    const submitBtn    = document.getElementById('submit-btn');
    const form         = document.getElementById('resolution-form');
    const draftStatus  = document.getElementById('draft-status');
    const activityFeed = document.getElementById('activity-feed');
    const conflictBanner = document.getElementById('conflict-banner');
    const toast        = document.getElementById('toast');

    /* ── Helpers ── */
    function swapCls(el, remove, add) {
        if (!el) return;
        el.classList.remove(...remove);
        if (add) el.classList.add(add);
    }

    function showToast(msg, icon = 'fa-circle-check', color = '#22c55e') {
        const toastText = document.getElementById('toast-text');
        toast.querySelector('i').className = 'fas ' + icon;
        toast.querySelector('i').style.color = color;
        toastText.textContent = msg;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    /* ── Status UI sync ── */
    function applyStatus(val) {
        const cfg = STATUS_CFG[val];
        if (!cfg) return;
        swapCls(statusSel,    ALL_S_SEL,   cfg.selCls);
        swapCls(preview,      ALL_S_BADGE, cfg.badgeCls);
        swapCls(statusHdrBdg, ALL_S_BADGE, cfg.badgeCls);
        preview.textContent      = cfg.emoji + ' ' + cfg.label;
        statusHdrBdg.textContent = cfg.emoji + ' ' + cfg.label;
    }

    /* ── Priority UI sync ── */
    function applyPriority(val) {
        const cfg = PRIORITY_CFG[val];
        if (!cfg) return;
        swapCls(priorityHdrB, ALL_P_BADGE, cfg.badgeCls);
        priorityHdrB.textContent = cfg.emoji + ' ' + cfg.label;
    }

    /* ── Character counter ── */
    function updateCharCount() {
        const len = textarea.value.length;
        charCount.textContent = len;
        charCount.style.color = len > 1800 ? '#dc2626' : len > 1500 ? '#f59e0b' : '';
    }

    /* ── Auto-save draft to localStorage ── */
    let draftTimer = null;
    function scheduleDraftSave() {
        clearTimeout(draftTimer);
        draftStatus.innerHTML = '<i class="fas fa-circle-dot"></i> Saving draft…';
        draftStatus.className = 'draft-indicator draft-saving';
        draftTimer = setTimeout(() => {
            localStorage.setItem(DRAFT_KEY, textarea.value);
            draftStatus.innerHTML = '<i class="fas fa-check-circle"></i> Draft saved';
            draftStatus.className = 'draft-indicator draft-saved';
        }, 1200);
    }

    /* ── Restore draft ── */
    function restoreDraft() {
        const saved = localStorage.getItem(DRAFT_KEY);
        if (saved && !textarea.value.trim()) {
            textarea.value = saved;
            draftStatus.innerHTML = '<i class="fas fa-rotate-left"></i> Draft restored';
            draftStatus.className = 'draft-indicator draft-saved';
            updateCharCount();
        }
    }

    /* ── Live clock ── */
    function tick() {
        document.getElementById('live-clock').textContent = new Date().toLocaleTimeString();
    }
    tick(); setInterval(tick, 1000);

    /* ── Poll ticket status (detect external changes) ── */
    let knownStatus = statusSel.value;
    function pollStatus() {
        fetch('/admin/support/' + TICKET_ID + '/status-check')
            .then(r => r.json())
            .then(data => {
                if (data.status && data.status !== knownStatus) {
                    conflictBanner.classList.add('show');
                    document.getElementById('conflict-text').textContent =
                        'This ticket was updated to "' + data.status.replace('_',' ') + '" by another agent.';
                }
            }).catch(() => {});
    }
    setInterval(pollStatus, 5000);

    /* ── Activity feed ── */
    function refreshActivity() {
        const label = document.getElementById('activity-refresh-label');
        fetch('/admin/support/' + TICKET_ID + '/activity')
            .then(r => r.json())
            .then(data => {
                label.textContent = 'updated ' + new Date().toLocaleTimeString();
                if (!data.items || data.items.length === 0) {
                    activityFeed.innerHTML = '<div class="activity-loading" style="color:var(--text-muted)">No activity yet.</div>';
                    return;
                }
                activityFeed.innerHTML = data.items.map(item => `
                    <div class="tl-item">
                        <div class="tl-dot" style="background:${item.color||'#93c5fd'};border-color:${item.color||'#bfdbfe'}99"></div>
                        <div>
                            <div style="font-size:11px;color:var(--text-muted);margin-bottom:2px">${item.actor||'System'} · ${item.time||''}</div>
                            <div style="font-size:13px;color:var(--text-secondary)">${item.message||''}</div>
                        </div>
                    </div>`).join('');
            }).catch(() => {
                label.textContent = 'unavailable';
                activityFeed.innerHTML = '<div class="activity-loading" style="color:var(--text-muted)"><i class="fas fa-ticket"></i> Ticket opened — waiting for activity.</div>';
            });
    }
    refreshActivity();
    setInterval(refreshActivity, 8000);

    /* ── Form submit ── */
    function onSubmit() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving…';
        localStorage.removeItem(DRAFT_KEY);
        setTimeout(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-paper-plane"></i> Save &amp; Send Resolution';
        }, 8000);
    }

    /* ── Event listeners ── */
    statusSel   && statusSel.addEventListener('change',   () => { applyStatus(statusSel.value);       showToast('Status updated to ' + (STATUS_CFG[statusSel.value]?.label||statusSel.value)); });
    prioritySel && prioritySel.addEventListener('change', () => { applyPriority(prioritySel.value);   showToast('Priority set to '   + (PRIORITY_CFG[prioritySel.value]?.label||prioritySel.value), 'fa-flag'); });
    textarea    && textarea.addEventListener('input',     () => { updateCharCount(); scheduleDraftSave(); });
    form        && form.addEventListener('submit',        onSubmit);

    /* ── Init ── */
    if (statusSel)   applyStatus(statusSel.value);
    if (prioritySel) applyPriority(prioritySel.value);
    updateCharCount();
    restoreDraft();

})();
</script>
@include('partials.admin-notification-realtime')
</body>
</html>