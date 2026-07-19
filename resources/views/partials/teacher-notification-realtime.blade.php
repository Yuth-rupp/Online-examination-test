{{-- ═══════════════════════════════════════════════════════════
     TEACHER REAL-TIME NOTIFICATIONS
     Powers the bell/drawer on Dashboard and the bell/dropdown on
     Analytics. Live push via Laravel Echo (Reverb/Pusher) over the
     `notifications.{userId}` private channel, with a 20s polling
     fallback so it still behaves "real time" even if the socket
     connection isn't configured yet (e.g. blank PUSHER_* keys).
     Safe to include on any teacher page — it only touches DOM
     elements that actually exist on that page.
════════════════════════════════════════════════════════════ --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>

<script>
(function () {
    const CSRF_TOKEN   = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const AUTH_USER_ID = {{ Auth::user()->user_id ?? 'null' }};

    let notifCache  = [];
    let unreadCache = 0;

    // ── Bootstrap Echo (Pusher-compatible; works with Reverb or Pusher Cloud) ──
    function initEcho() {
        if (!AUTH_USER_ID || window.Echo) return;

        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') ?: 'examsystemkeyabc123' }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') ?: 'mt1' }}',
            forceTLS: true,
            authEndpoint: '{{ url('/broadcasting/auth') }}',
            auth: {
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept': 'application/json',
                }
            }
        });

        try {
            window.Echo.private('notifications.' + AUTH_USER_ID)
                .listen('.NotificationCreated', (payload) => {
                    notifCache.unshift({ ...payload, read: false });
                    unreadCache += 1;
                    renderAll();
                    pulseBell();
                    showNotifToast(payload.title, payload.body);
                });

            window.Echo.connector.pusher.connection.bind('connected', () => console.log('[Notifications] Live channel connected'));
            window.Echo.connector.pusher.connection.bind('error', (err) => console.warn('[Notifications] Socket error — relying on polling fallback', err));
        } catch (e) {
            console.warn('[Notifications] Echo unavailable — relying on polling fallback', e);
        }
    }

    // ── Initial + fallback fetch over plain HTTP ──
    async function fetchNotifications() {
        try {
            const res = await fetch('{{ route('teacher.notifications') }}', { headers: { 'Accept': 'application/json' } });
            if (!res.ok) return;
            const data = await res.json();
            notifCache  = data.notifications || [];
            unreadCache = data.unread_count || 0;
            renderAll();
        } catch (e) {
            console.warn('[Notifications] Failed to load', e);
        }
    }

    function renderAll() {
        renderDashboardDrawer();
        renderAnalyticsDropdown();
        updateBadges();
    }

    // ── Severity → visual style mapping (shared by both renderers) ──
    function styleFor(type) {
        if (type === 'danger' || type === 'high' || type === 'alert' || type === 'error') {
            return { label: '🔴 High Alert', badgeBg: '#FEE2E2', badgeText: '#991B1B', cardBg: '#FEF2F2', cardBorder: '#FECACA', text: '#7F1D1D', time: '#EF4444' };
        }
        if (type === 'warning' || type === 'warn') {
            return { label: '⚠️ Warning', badgeBg: '#FEF3C7', badgeText: '#92400E', cardBg: '#FFFBEB', cardBorder: '#FDE68A', text: '#78350F', time: '#F59E0B' };
        }
        if (type === 'success') {
            return { label: '✅ Success', badgeBg: '#DCFCE7', badgeText: '#15803D', cardBg: '#F0FDF4', cardBorder: '#BBF7D0', text: '#14532D', time: '#22C55E' };
        }
        return { label: 'ℹ️ Info', badgeBg: '#DBEAFE', badgeText: '#1E40AF', cardBg: '#EFF6FF', cardBorder: '#BFDBFE', text: '#1E3A8A', time: '#2563EB' };
    }

    // ── DASHBOARD (drawer-panel style cards) ──
    function renderDashboardDrawer() {
        const list = document.getElementById('teacher-notif-drawer-list');
        if (!list) return;

        if (!notifCache.length) {
            list.innerHTML = `
                <div class="py-14 text-center">
                    <i class="fa-regular fa-bell-slash text-3xl text-[#CBD5E1] mb-3 block"></i>
                    <p class="text-xs font-bold text-[#94A3B8]">You're all caught up!</p>
                    <p class="text-[11px] text-[#CBD5E1] mt-1">No alerts yet — new activity will show up here instantly.</p>
                </div>`;
            return;
        }

        list.innerHTML = notifCache.map(n => {
            const s = styleFor(n.type);
            return `
                <div class="p-4 rounded-2xl space-y-2 cursor-pointer transition-opacity teacher-notif-row" data-id="${n.id}"
                     style="background:${s.cardBg};border:1px solid ${s.cardBorder};opacity:${n.read ? '.55' : '1'}">
                    <div class="flex items-center justify-between">
                        <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-md uppercase tracking-wider" style="background:${s.badgeBg};color:${s.badgeText}">${s.label}</span>
                        <span class="text-[10px] font-medium" style="color:${s.time}">${n.time ?? ''}</span>
                    </div>
                    <p class="text-xs font-semibold leading-relaxed" style="color:${s.text}">${escapeHtml(n.title ?? '')}${n.body ? ' — ' + escapeHtml(n.body) : ''}</p>
                </div>`;
        }).join('');

        list.querySelectorAll('.teacher-notif-row').forEach(row => {
            row.addEventListener('click', () => markNotifRead(row.dataset.id));
        });
    }

    // ── ANALYTICS (dropdown list style rows) ──
    function renderAnalyticsDropdown() {
        const list = document.getElementById('teacher-notif-list-analytics');
        if (!list) return;

        if (!notifCache.length) {
            list.innerHTML = `
                <div class="py-8 text-center">
                    <i class="fa-regular fa-bell-slash text-2xl text-slate-300 mb-2 block"></i>
                    <p class="text-xs text-slate-400 font-medium">No alerts</p>
                </div>`;
            return;
        }

        list.innerHTML = notifCache.map(n => `
            <div class="flex gap-3 px-4 py-3 hover:bg-slate-50 transition-colors cursor-pointer teacher-notif-row" data-id="${n.id}" style="opacity:${n.read ? '.55' : '1'}">
                <div class="w-7 h-7 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600 flex-shrink-0 mt-0.5">
                    <i class="fa-solid fa-bell text-xs"></i>
                </div>
                <div>
                    <p class="text-[11px] text-slate-700 font-medium leading-snug">${escapeHtml(n.title ?? '')}${n.body ? ' — ' + escapeHtml(n.body) : ''}</p>
                    <span class="text-[9px] text-slate-400 font-bold mt-0.5 flex items-center gap-1">
                        <i class="fa-regular fa-clock"></i>${n.time ?? ''}
                    </span>
                </div>
            </div>`).join('');

        list.querySelectorAll('.teacher-notif-row').forEach(row => {
            row.addEventListener('click', () => markNotifRead(row.dataset.id));
        });
    }

    function escapeHtml(str) {
        const d = document.createElement('div');
        d.textContent = str;
        return d.innerHTML;
    }

    // ── Badges shared across pages ──
    function updateBadges() {
        // Dashboard: bell dot
        const bellDot = document.getElementById('teacher-notif-bell-dot');
        if (bellDot) bellDot.classList.toggle('hidden', unreadCache === 0);

        // Dashboard: "Alerts N" quick-action pill
        const alertsCount = document.getElementById('teacher-alerts-count');
        if (alertsCount) {
            alertsCount.textContent = unreadCache;
            alertsCount.classList.toggle('hidden', unreadCache === 0);
        }

        // Dashboard: drawer subtitle "N new alerts"
        const subtitle = document.getElementById('teacher-notif-subtitle');
        if (subtitle) subtitle.textContent = unreadCache > 0 ? `${unreadCache} new alert${unreadCache === 1 ? '' : 's'}` : 'All caught up';

        // Analytics: bell dot
        const analyticsDot = document.getElementById('teacher-notif-dot-analytics');
        if (analyticsDot) analyticsDot.classList.toggle('hidden', unreadCache === 0);

        // Analytics: pill next to "System Alerts"
        const analyticsPill = document.getElementById('teacher-notif-pill-analytics');
        if (analyticsPill) {
            analyticsPill.textContent = unreadCache;
            analyticsPill.classList.toggle('hidden', unreadCache === 0);
        }
    }

    function pulseBell() {
        document.querySelectorAll('#bell-btn i, #notif-bell-btn-analytics i').forEach(icon => {
            icon.style.transition = 'transform .15s ease';
            icon.style.transform = 'rotate(-14deg)';
            setTimeout(() => { icon.style.transform = 'rotate(10deg)'; }, 150);
            setTimeout(() => { icon.style.transform = 'rotate(0)'; }, 300);
        });
    }

    function showNotifToast(title, body) {
        // Reuses whichever toast container the current page already defines.
        if (typeof window.showToast === 'function') {
            window.showToast(`${title || 'New notification'}${body ? ': ' + body : ''}`, 'info');
        } else if (typeof window.toast === 'function') {
            window.toast(`${title || 'New notification'}${body ? ': ' + body : ''}`, 'info');
        }
    }

    async function markNotifRead(id) {
        const notif = notifCache.find(n => String(n.id) === String(id));
        if (notif && !notif.read) {
            notif.read = true;
            unreadCache = Math.max(0, unreadCache - 1);
            renderAll();
            try {
                await fetch(`/teacher/notifications/${id}/read`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
                });
            } catch (e) { console.warn('[Notifications] Failed to mark read', e); }
        }
    }

    window.teacherClearAllNotifs = async function () {
        notifCache  = [];
        unreadCache = 0;
        renderAll();
        try {
            await fetch('{{ route('teacher.notifications.clear') }}', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
        } catch (e) { console.warn('[Notifications] Failed to clear', e); }
    };

    document.addEventListener('DOMContentLoaded', () => {
        initEcho();
        fetchNotifications();
        // Fallback polling in case the websocket connection ever drops or isn't configured yet.
        setInterval(fetchNotifications, 20000);

        const clearBtnDashboard = document.getElementById('teacher-notif-clear-dashboard');
        if (clearBtnDashboard) clearBtnDashboard.addEventListener('click', window.teacherClearAllNotifs);

        const clearBtnAnalytics = document.getElementById('teacher-notif-clear-analytics');
        if (clearBtnAnalytics) clearBtnAnalytics.addEventListener('click', window.teacherClearAllNotifs);
    });
})();
</script>
