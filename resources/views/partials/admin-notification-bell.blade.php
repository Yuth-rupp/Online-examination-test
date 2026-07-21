        <!-- ── Notification Bell (real-time) ── -->
        <div class="relative" id="notif-wrapper">
          <button id="notif-bell-btn" type="button"
                  class="relative p-2.5 rounded-xl cursor-pointer transition-colors"
                  :class="darkMode?'bg-slate-800 text-slate-400 hover:bg-slate-700':'bg-slate-100 text-slate-500 hover:bg-slate-200'">
            <i data-lucide="bell" class="w-4 h-4" id="bell-icon"></i>
            <span id="notif-unread-badge"
                  class="hidden absolute -top-1 -right-1 min-w-[16px] h-4 px-1 bg-red-500 text-white text-[9px] font-black rounded-full items-center justify-center border border-white dark:border-slate-900 leading-none"></span>
          </button>

          <!-- Dropdown panel -->
          <div id="notif-dropdown"
               class="notif-dropdown hidden absolute right-0 top-12 w-80 rounded-2xl border shadow-2xl overflow-hidden z-50"
               :class="darkMode?'bg-slate-900 border-slate-700':'bg-white border-slate-100'">

            <div class="px-4 py-3 border-b flex items-center justify-between"
                 :class="darkMode?'border-slate-800':'border-slate-100'">
              <div class="flex items-center gap-2">
                <span class="text-xs font-black" :class="darkMode?'text-white':'text-slate-900'">Notifications</span>
                <span id="notif-unread-pill" class="hidden px-1.5 py-0.5 text-[10px] font-black bg-red-500 text-white rounded-md"></span>
              </div>
              <button type="button" id="notif-clear-all-btn"
                      class="text-[11px] font-bold text-blue-600 hover:text-blue-700 cursor-pointer transition-colors">
                Clear all
              </button>
            </div>

            <div class="max-h-72 overflow-y-auto" id="notif-list-body">
              <div class="py-10 text-center" id="notif-empty-state">
                <i data-lucide="bell-off" class="w-8 h-8 mx-auto mb-2" :class="darkMode?'text-slate-700':'text-slate-200'"></i>
                <p class="text-xs text-slate-400 font-medium">You're all caught up!</p>
              </div>
            </div>

            <div class="px-4 py-2.5 border-t" :class="darkMode?'border-slate-800':'border-slate-100'">
              <a href="{{ route('admin.support') }}"
                 class="w-full flex items-center justify-center gap-1.5 text-xs font-bold text-blue-600 hover:text-blue-700 transition-colors py-1">
                <i data-lucide="external-link" class="w-3 h-3"></i>
                Go to Support Desk
              </a>
            </div>
          </div>
        </div>
