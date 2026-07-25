{{-- ═══════════════════════════════════════
       REAL-TIME NOTIFICATIONS (Super Admin)
       (websocket via Laravel Reverb/Pusher broadcasting
       + 20s polling fallback if the socket ever drops,
       same pattern already used for student/teacher/admin)
  ════════════════════════════════════════ --}}
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pusher/8.3.0/pusher.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.0/dist/echo.iife.js"></script>

  <script>
    (function () {
      const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
      const AUTH_USER_ID = {{ (Auth::user()->user_id ?? 'null') }};

      let notifCache = [];
      let unreadCache = 0;

      // ── Bootstrap Echo (Pusher-compatible, works with Reverb or Pusher Cloud) ──
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

        window.Echo.private('notifications.' + AUTH_USER_ID)
          .listen('.NotificationCreated', (payload) => {
            // Prepend the fresh notification and refresh the UI instantly —
            // no refetch needed, the server already sent us the full record.
            notifCache.unshift({ ...payload, read: false });
            unreadCache += 1;
            renderNotifDropdown();
            updateBellBadge();
            shakeBell();
            showNotifToast(payload.title, payload.body);
          });

        window.Echo.connector.pusher.connection.bind('connected', () => console.log('[Notifications] Live channel connected'));
        window.Echo.connector.pusher.connection.bind('error', (err) => console.warn('[Notifications] Socket error, falling back to polling', err));
      }

      // ── Initial + fallback fetch over plain HTTP ──
      async function fetchNotifications() {
        try {
          const res = await fetch('{{ route('superadmin.notifications') }}', {
            headers: { 'Accept': 'application/json' }
          });
          if (!res.ok) return;
          const data = await res.json();
          notifCache = data.notifications || [];
          unreadCache = data.unread_count || 0;
          renderNotifDropdown();
          updateBellBadge();
        } catch (e) {
          console.warn('Failed to load notifications', e);
        }
      }

      function iconFor(type) {
        if (type === 'success') return { icon: 'fa-circle-check', wrap: 'bg-emerald-50', color: 'text-emerald-500' };
        if (type === 'warn' || type === 'warning') return { icon: 'fa-triangle-exclamation', wrap: 'bg-amber-50', color: 'text-amber-500' };
        return { icon: 'fa-circle-info', wrap: 'bg-blue-50', color: 'text-blue-500' };
      }

      function esc(str) {
        const div = document.createElement('div');
        div.appendChild(document.createTextNode(str ?? ''));
        return div.innerHTML;
      }

      function renderNotifDropdown() {
        const body = document.getElementById('notif-list-body');
        if (!body) return;

        if (!notifCache.length) {
          body.innerHTML = `
            <div class="py-10 text-center">
              <i class="fa-regular fa-bell-slash text-2xl mx-auto mb-2 text-slate-200"></i>
              <p class="text-xs text-slate-400 font-medium">You're all caught up!</p>
            </div>`;
          return;
        }

        body.innerHTML = notifCache.map(n => {
          const meta = iconFor(n.type);
          return `
            <div class="px-4 py-3 border-b border-slate-50 flex items-start gap-3 cursor-pointer transition-colors notif-row hover:bg-slate-50 ${n.read ? 'opacity-55' : ''}" data-id="${n.id}">
              <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 ${meta.wrap}">
                <i class="fa-solid ${meta.icon} text-xs ${meta.color}"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-bold leading-snug text-slate-800">${esc(n.title)}</p>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">${esc(n.body)}</p>
                <p class="text-[10px] text-slate-400 mt-1">${esc(n.time)}</p>
              </div>
              ${!n.read ? '<span class="w-2 h-2 bg-blue-600 rounded-full flex-shrink-0 mt-2"></span>' : ''}
            </div>`;
        }).join('');

        body.querySelectorAll('.notif-row').forEach(row => {
          row.addEventListener('click', () => markNotifRead(row.dataset.id));
        });
      }

      function updateBellBadge() {
        const badge = document.getElementById('notif-unread-badge');
        const pill  = document.getElementById('notif-unread-pill');
        if (badge) {
          if (unreadCache > 0) {
            badge.textContent = unreadCache > 99 ? '99+' : unreadCache;
            badge.classList.remove('hidden');
            badge.classList.add('flex');
          } else {
            badge.classList.add('hidden');
            badge.classList.remove('flex');
          }
        }
        if (pill) {
          if (unreadCache > 0) {
            pill.textContent = unreadCache;
            pill.classList.remove('hidden');
          } else {
            pill.classList.add('hidden');
          }
        }
      }

      function shakeBell() {
        const bellIcon = document.getElementById('bell-icon');
        if (!bellIcon) return;
        bellIcon.classList.add('bell-ring');
        setTimeout(() => bellIcon.classList.remove('bell-ring'), 500);
      }

      function showNotifToast(title, body) {
        // Reuses the #toast-container element and toast-enter/toast-visible/
        // toast-leave CSS classes already defined on this dashboard page,
        // so the bell's toast looks identical to the page's other toasts.
        const container = document.getElementById('toast-container');
        if (!container) return;

        const toast = document.createElement('div');
        toast.className = 'toast-enter flex items-start gap-3 px-4 py-3 rounded-xl text-white text-xs font-semibold bg-blue-600';
        toast.style.pointerEvents = 'auto';
        toast.style.boxShadow = '0 8px 24px rgba(0,0,0,0.2)';
        toast.innerHTML = `<i class="fa-solid fa-bell flex-shrink-0 mt-0.5"></i><div><div class="font-bold">${esc(title) || 'New notification'}</div><div class="text-[11px] font-medium opacity-90 mt-0.5">${esc(body)}</div></div>`;
        container.appendChild(toast);

        requestAnimationFrame(() => toast.classList.add('toast-visible'));
        setTimeout(() => {
          toast.classList.remove('toast-visible');
          toast.classList.add('toast-leave');
          setTimeout(() => toast.remove(), 300);
        }, 4000);
      }

      async function markNotifRead(id) {
        const notif = notifCache.find(n => String(n.id) === String(id));
        if (notif && !notif.read) {
          notif.read = true;
          unreadCache = Math.max(0, unreadCache - 1);
          renderNotifDropdown();
          updateBellBadge();
          try {
            await fetch(`{{ url('/super-admin/notifications') }}/${id}/read`, {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
            });
          } catch (e) { console.warn('Failed to mark notification read', e); }
        }
      }

      async function clearAllNotifs() {
        notifCache = [];
        unreadCache = 0;
        renderNotifDropdown();
        updateBellBadge();
        document.getElementById('notif-dropdown')?.classList.add('hidden');
        try {
          await fetch('{{ route('superadmin.notifications.clear') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF_TOKEN, 'Accept': 'application/json' }
          });
        } catch (e) { console.warn('Failed to clear notifications', e); }
      }

      document.addEventListener('DOMContentLoaded', () => {
        const bellBtn = document.getElementById('notif-bell-btn');
        const dropdown = document.getElementById('notif-dropdown');
        const wrapper = document.getElementById('notif-wrapper');
        const clearBtn = document.getElementById('notif-clear-all-btn');

        if (bellBtn && dropdown && wrapper) {
          bellBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            const wasHidden = dropdown.classList.contains('hidden');
            dropdown.classList.toggle('hidden');
            if (wasHidden) shakeBell();
          });
          document.addEventListener('click', (e) => {
            if (!wrapper.contains(e.target)) dropdown.classList.add('hidden');
          });
        }
        if (clearBtn) clearBtn.addEventListener('click', clearAllNotifs);

        initEcho();
        fetchNotifications();
        // Fallback polling in case the websocket connection ever drops
        // (e.g. serverless cold start, network hiccup, or Pusher not configured).
        setInterval(fetchNotifications, 20000);
      });
    })();
  </script>