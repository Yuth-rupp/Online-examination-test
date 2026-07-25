<!-- ── Notification Bell (real-time, Super Admin) ── -->
        <div class="relative" id="notif-wrapper">
          <button id="notif-bell-btn" type="button"
                  class="relative w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-500 transition-all">
            <i class="fa-regular fa-bell text-sm" id="bell-icon"></i>
            <span id="notif-unread-badge"
                  class="hidden absolute -top-1 -right-1 min-w-[16px] h-4 px-1 bg-rose-500 text-white text-[9px] font-black rounded-full items-center justify-center border border-white leading-none"></span>
          </button>

          <!-- Dropdown panel -->
          <div id="notif-dropdown"
               class="notif-dropdown hidden absolute right-0 top-12 w-80 rounded-2xl border border-slate-100 bg-white shadow-2xl overflow-hidden z-50">

            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
              <div class="flex items-center gap-2">
                <span class="text-xs font-black text-slate-900">Notifications</span>
                <span id="notif-unread-pill" class="hidden px-1.5 py-0.5 text-[10px] font-black bg-rose-500 text-white rounded-md"></span>
              </div>
              <button type="button" id="notif-clear-all-btn"
                      class="text-[11px] font-bold text-blue-600 hover:text-blue-700 cursor-pointer transition-colors">
                Clear all
              </button>
            </div>

            <div class="max-h-72 overflow-y-auto" id="notif-list-body">
              <div class="py-10 text-center" id="notif-empty-state">
                <i class="fa-regular fa-bell-slash text-2xl mx-auto mb-2 text-slate-200"></i>
                <p class="text-xs text-slate-400 font-medium">You're all caught up!</p>
              </div>
            </div>
          </div>
        </div>