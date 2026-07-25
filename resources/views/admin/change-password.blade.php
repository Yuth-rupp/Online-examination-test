<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Change Password — ExamSystem Admin Console">
    <title>{{ $forced ? 'Set a New Password' : 'Change Password' }} — ExamSystem Admin</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
      (function () {
        if (localStorage.getItem('darkMode') === 'true') {
          document.documentElement.classList.add('dark');
        }
      })();
    </script>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://unpkg.com/lucide@latest"></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --body-bg    : #f1f5f9;
            --card-bg    : #ffffff;
            --card-border: #e8edf5;
            --text-1     : #0f172a;
            --text-2     : #64748b;
            --text-muted : #94a3b8;
            --blue       : #2563eb;
            --input-bg   : #f8fafc;
            --input-br   : #e2e8f0;
            --radius     : 18px;
        }
        * { font-family: 'Inter', sans-serif; box-sizing: border-box; }
        body {
            background: var(--body-bg);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }
        .card {
            width: 100%;
            max-width: 460px;
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: var(--radius);
            padding: 36px 32px;
            box-shadow: 0 1px 4px rgba(0,0,0,0.04), 0 12px 32px rgba(0,0,0,0.06);
        }
        .brand-icon {
            width: 48px; height: 48px;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 6px 16px rgba(37,99,235,0.35);
        }
        .f-group { margin-bottom: 18px; }
        .f-label {
            display: block;
            font-size: 12.5px;
            font-weight: 700;
            color: var(--text-1);
            margin-bottom: 6px;
        }
        .f-input {
            width: 100%;
            padding: 11px 14px;
            font-size: 13.5px;
            background: var(--input-bg);
            border: 1.5px solid var(--input-br);
            border-radius: 10px;
            color: var(--text-1);
            transition: border-color .15s, box-shadow .15s;
        }
        .f-input:focus {
            outline: none;
            border-color: var(--blue);
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .f-error {
            font-size: 11.5px;
            color: #dc2626;
            margin-top: 5px;
            font-weight: 500;
        }
        .f-hint {
            font-size: 11.5px;
            color: var(--text-muted);
            margin-top: 5px;
        }
        .btn-primary {
            width: 100%;
            display: flex; align-items: center; justify-content: center; gap: 8px;
            padding: 12px 18px;
            background: var(--blue);
            color: #fff;
            font-weight: 700;
            font-size: 13.5px;
            border-radius: 11px;
            border: none;
            cursor: pointer;
            transition: background .15s;
        }
        .btn-primary:hover { background: #1d4ed8; }
        .banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            border-radius: 11px;
            padding: 12px 14px;
            font-size: 12.5px;
            line-height: 1.55;
            margin-bottom: 22px;
        }
        .success-banner {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            color: #166534;
            border-radius: 11px;
            padding: 12px 14px;
            font-size: 12.5px;
            line-height: 1.55;
            margin-bottom: 22px;
        }
    </style>
</head>
<body>

    <div class="card">
        <div class="flex items-center gap-3 mb-6">
            <div class="brand-icon">
                <i data-lucide="lock" class="w-5 h-5 text-white"></i>
            </div>
            <div>
                <h1 class="font-black text-lg" style="color:var(--text-1)">
                    {{ $forced ? 'Set a New Password' : 'Change Password' }}
                </h1>
                <p class="text-xs" style="color:var(--text-2)">ExamSystem Admin Console</p>
            </div>
        </div>

        @if($forced)
            <div class="banner">
                <i data-lucide="triangle-alert" class="w-4 h-4 flex-shrink-0" style="margin-top:1px"></i>
                <span>A Super Admin reset your password. Enter the temporary password you were emailed, then choose a new one to continue.</span>
            </div>
        @endif

        @if(session('success'))
            <div class="success-banner">
                <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0" style="margin-top:1px"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.settings.password.update') }}">
            @csrf

            <div class="f-group">
                <label class="f-label" for="current_password">
                    {{ $forced ? 'Temporary Password' : 'Current Password' }}
                </label>
                <input type="password" name="current_password" id="current_password" class="f-input"
                       placeholder="Enter your {{ $forced ? 'temporary' : 'current' }} password" required autofocus>
                @error('current_password')
                    <div class="f-error">{{ $message }}</div>
                @enderror
            </div>

            <div class="f-group">
                <label class="f-label" for="new_password">New Password</label>
                <input type="password" name="new_password" id="new_password" class="f-input"
                       placeholder="At least 8 characters" required minlength="8">
                @error('new_password')
                    <div class="f-error">{{ $message }}</div>
                @enderror
                <div class="f-hint">Use something you haven't used before on this account.</div>
            </div>

            <div class="f-group" style="margin-bottom:26px">
                <label class="f-label" for="new_password_confirmation">Confirm New Password</label>
                <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="f-input"
                       placeholder="Re-enter your new password" required minlength="8">
            </div>

            <button type="submit" class="btn-primary">
                <i data-lucide="save" class="w-4 h-4"></i>
                {{ $forced ? 'Set Password & Continue' : 'Update Password' }}
            </button>

            @unless($forced)
                <a href="{{ route('admin.settings') }}"
                   class="mt-3 flex items-center justify-center gap-1.5 text-xs font-semibold"
                   style="color:var(--text-2)">
                    <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i>
                    Back to Settings
                </a>
            @endunless
        </form>
    </div>

    <script>lucide.createIcons();</script>
</body>
</html>
