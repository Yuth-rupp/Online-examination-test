# ExamSystem Admin — Fix Pack

Drop these files into your project at the matching paths (they overwrite the
originals 1:1 — same folder structure as your Laravel app root):

```
app/Http/Controllers/AdminController.php
routes/web.php
resources/views/admin/dashboard.blade.php
resources/views/admin/users.blade.php
resources/views/admin/exams.blade.php
resources/views/admin/security.blade.php
resources/views/admin/support.blade.php
```

No other files were touched. No migrations, no new dependencies.

---

## 1. Sidebar logo/icons missing on User Management

**Cause:** every admin page calls `lucide.createIcons()` after load to render
the `<i data-lucide="...">` tags (this is what draws the sidebar's
graduation-cap logo, nav icons, etc.). `users.blade.php` was the one page
that never called it, so those icons just sat there un-rendered.

**Fix:** added the missing `if (window.lucide) lucide.createIcons();` call
at the end of that page's script block.

## 2. Exams page wasn't real-time — the numbers were fake, not stale

Two separate bugs, both in the backend:

- `AdminController::examWorkspace()` hardcoded `'students' => 45,
'submitted' => 12` for **every exam**, no matter what. The stat cards
  (Active / Draft / Closed / Total Submissions) were never even passed to
  the view, so the template silently fell back to its baked-in defaults
  (`2`, `1`, `1`, `86`) — exactly what you saw on screen.
- The page already had JS polling `/admin/exams/api` every 8 seconds, but
  that route didn't exist, so every request 404'd and failed silently.

**Fix:**

- `getExamWorkspaceData()` (new private helper) now computes real numbers:
  actual student count, actual submissions per exam (from the
  `submissions` table), actual question count, actual instructor.
- Registered `GET /admin/exams/api` → `AdminController@getExamsDataApi`.
- Rewrote the front-end poller so it rebuilds **both** the stat cards and
  every exam card (status badge, participation %, submitted count) from
  live data every 8 seconds — new sign-ups and new submissions now show up
  without a manual reload.

> Note: this schema has no per-course enrollment table, so "students" per
> exam = total accounts with `role = student`. If you add enrollment
> tracking later, swap that one line in `getExamWorkspaceData()`.

## 3. Inconsistent top bar across admin pages

Settings and the ticket-review page already had a correct **sticky**
topbar (stays visible on scroll, blurred white/slate background, subtle
shadow) in the admin's blue palette. Dashboard, Users, Exams, Security,
and Support did not — their headers scrolled away with the page content,
unlike the student portal's persistent topbar.

**Fix:** converted all five pages to the same sticky-topbar structure:

```html
<main class="flex-1 ml-64 min-h-screen flex flex-col">
    <header class="... sticky top-0 z-20 backdrop-blur-xl ...">...</header>
    <div class="p-7"><!-- scrollable content --></div>
</main>
```

Same content, same admin blue (`#2563eb → #1d4ed8`) branding — it just no
longer disappears when you scroll down.

---

## Suggested next steps (not included here)

- Apply the same treatment to `resolve_ticket.blade.php`'s minor
  inconsistencies if you spot any, and to any future admin pages you add.
- If you want the exam cards' 3-dot menu (Edit/Assign/Adjust) to do more
  than open the same drawer, that's a separate feature, not a bug fix.
