<style>
    /* Notification dropdown (shared across Dashboard, History, Exams, Settings) */
    .notif-dropdown { animation: dropIn 0.18s ease; transform-origin: top right; }
    @keyframes dropIn { from { opacity:0; transform:scale(0.96) translateY(-6px); } to { opacity:1; transform:scale(1) translateY(0); } }
    @keyframes bellShake {
      0%, 100% { transform: rotate(0deg); }
      20% { transform: rotate(-15deg); }
      40% { transform: rotate(15deg); }
      60% { transform: rotate(-10deg); }
      80% { transform: rotate(8deg); }
    }
    .bell-ring { animation: bellShake 0.45s ease; }

    /* Toast popup shown by notification-realtime.blade.php's showNotifToast() */
    @keyframes toastIn { from { opacity:0; transform: translateY(12px) scale(0.97); } to { opacity:1; transform: translateY(0) scale(1); } }
    @keyframes toastOut { from { opacity:1; transform: translateY(0) scale(1); } to { opacity:0; transform: translateY(8px) scale(0.97); } }
  </style>