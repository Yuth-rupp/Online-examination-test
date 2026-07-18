  {{-- ═══════════════════════════════════════
       REAL-TIME NOTIFICATIONS
       (websocket via Laravel Reverb/Pusher broadcasting
       + 20s polling fallback if the socket ever drops)
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
          const res = await fetch('{{ route('student.notifications') }}', {
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
        if (type === 'success') return { icon: 'check-circle-2', wrap: 'bg-emerald-50 dark:bg-emerald-500/10', color: 'text-emerald-500' };
        if (type === 'warn' || type === 'warning') return { icon: 'alert-triangle', wrap: 'bg-amber-50 dark:bg-amber-500/10', color: 'text-amber-500' };
        return { icon: 'info', wrap: 'bg-indigo-50 dark:bg-indigo-500/10', color: 'text-indigo-500' };
      }

      function renderNotifDropdown() {
        const body = document.getElementById('notif-list-body');
        if (!body) return;
        const isDark = document.documentElement.classList.contains('dark');

        if (!notifCache.length) {
          body.innerHTML = `
            <div class="py-10 text-center">
              <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2 ${isDark ? 'text-slate-700' : 'text-slate-200'}"></i>
              <p class="text-xs text-slate-400 font-medium">You're all caught up!</p>
            </div>`;
          if (window.lucide) lucide.createIcons();
          return;
        }

        body.innerHTML = notifCache.map(n => {
          const meta = iconFor(n.type);
          return `
            <div class="px-4 py-3 border-b flex items-start gap-3 cursor-pointer transition-colors notif-row ${isDark ? 'border-slate-800 hover:bg-slate-800' : 'border-slate-50 hover:bg-slate-50'} ${n.read ? 'opacity-55' : ''}" data-id="${n.id}">
              <div class="w-8 h-8 rounded-xl flex items-center justify-center flex-shrink-0 mt-0.5 ${meta.wrap}">
                <i data-lucide="${meta.icon}" class="w-4 h-4 ${meta.color}"></i>
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-xs font-bold leading-snug ${isDark ? 'text-white' : 'text-slate-800'}">${n.title ?? ''}</p>
                <p class="text-[11px] text-slate-400 mt-0.5 leading-relaxed">${n.body ?? ''}</p>
                <p class="text-[10px] text-slate-400 mt-1">${n.time ?? ''}</p>
              </div>
              ${!n.read ? '<span class="w-2 h-2 bg-indigo-500 rounded-full flex-shrink-0 mt-2 pulse-dot"></span>' : ''}
            </div>`;
        }).join('');

        body.querySelectorAll('.notif-row').forEach(row => {
          row.addEventListener('click', () => markNotifRead(row.dataset.id));
        });

        if (window.lucide) lucide.createIcons();
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
        let container = document.getElementById('toast-container');
        if (!container) {
          container = document.createElement('div');
          container.id = 'toast-container';
          container.className = 'fixed bottom-6 right-6 z-50 space-y-2 pointer-events-none';
          document.body.appendChild(container);
        }
        const toast = document.createElement('div');
        toast.className = 'toast pointer-events-auto flex items-start gap-3 px-5 py-3.5 rounded-2xl shadow-xl text-sm font-bold bg-indigo-600 text-white max-w-xs';
        toast.style.animation = 'toastIn 0.3s ease, toastOut 0.3s ease 4.7s forwards';
        toast.innerHTML = `<i data-lucide="bell" class="w-4 h-4 flex-shrink-0 mt-0.5"></i><div><div>${title || 'New notification'}</div><div class="text-[11px] font-medium opacity-90 mt-0.5">${body || ''}</div></div>`;
        container.appendChild(toast);
        if (window.lucide) lucide.createIcons();
        setTimeout(() => toast.remove(), 5200);
      }

      async function markNotifRead(id) {
        const notif = notifCache.find(n => String(n.id) === String(id));
        if (notif && !notif.read) {
          notif.read = true;
          unreadCache = Math.max(0, unreadCache - 1);
          renderNotifDropdown();
          updateBellBadge();
          try {
            await fetch(`/student/notifications/${id}/read`, {
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
          await fetch('{{ route('student.notifications.clear') }}', {
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
        // (e.g. serverless cold start, network hiccup).
        setInterval(fetchNotifications, 20000);
      });
    })();
  </script>
