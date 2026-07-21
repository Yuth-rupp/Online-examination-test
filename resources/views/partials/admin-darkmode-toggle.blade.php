<!-- ═══════════════════════════════════════════════════
       ADMIN DARK MODE TOGGLE
       Same pattern as the student/teacher portals.
  ════════════════════════════════════════════════════ -->
<button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode);"
        title="Toggle dark mode"
        class="p-2.5 rounded-xl transition-colors cursor-pointer"
        :class="darkMode ? 'bg-slate-800 text-amber-400 hover:bg-slate-700' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'">
  <i data-lucide="sun" class="w-4 h-4" x-show="darkMode"></i>
  <i data-lucide="moon" class="w-4 h-4" x-show="!darkMode"></i>
</button>
