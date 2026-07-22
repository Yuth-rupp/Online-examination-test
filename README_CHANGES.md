# ExamSystem — Grading & Real-Time Analytics Fixes

## How to apply
Copy each file below into the matching path in your project (overwriting the
existing file), then run:

```
php artisan route:clear
php artisan view:clear
php artisan config:clear
```

Files in this bundle:

```
app/Support/RubricSplitter.php                                 (NEW FILE)
app/Http/Controllers/GradingController.php                     (replace)
app/Http/Controllers/TeacherController.php                     (replace)
resources/views/teacher/grading/evaluate.blade.php              (replace)
resources/views/teacher/grading_queue.blade.php                 (replace)
resources/views/teacher/teacher-analytic.blade.php               (replace)
resources/views/partials/teacher-notification-realtime.blade.php (replace)
routes/web.php                                                  (replace)
```

No database migrations are needed — every fix works with your existing schema.

---

## 1. Essay rubric max-score bug (the "25 vs 5" issue) — FIXED

**What was happening:** the Accuracy / Depth / Clarity sliders on the grading
screen were hardcoded to a max of 10 + 10 + 5 = **25**, no matter what you
actually set the essay question's points to when building the exam. So if
you configured an essay question worth 5 points, the sliders still let you
go up to 25, and the sub-total label said "/25" while your exam was really
only worth 5 — the numbers could never line up.

**Fix:** added `app/Support/RubricSplitter.php`, a small shared helper that
takes the essay question's *real* configured points and splits it into
Accuracy (40%) / Depth (40%) / Clarity (20%) sub-maximums that always add up
exactly to the real total. Both the grading screen (which sets the slider
`max` attributes) and `GradingController::store()` (which validates the
submitted scores) now call the exact same helper — so the UI and the saved
score can never disagree again. The sliders are still fully live/reactive
via Alpine.js: drag them and the sub-total and the "Grand Total Score" ring
update instantly, before you even save.

Old scores saved under the previous 10/10/5 scale are automatically clamped
down to the new max so they still display correctly.

The Grading Queue table had the same problem in a different spot — it
always showed "score / 40" for every submission regardless of what the
exam was actually worth. That's fixed too; it now shows each submission's
real configured max.

## 2. "Save & Grade Next" — was actually broken, now fixed

Two bugs, both fixed:
- The redirect used the wrong route-parameter name (`id` instead of the
  route's real `student_id`), which meant the button could fail to advance
  to the next paper at all.
- "Next" was picked by raw database ID across *every* submission in the
  system, rather than the next still-pending paper in **your own** queue.

Now: **Save & Grade Next** jumps straight to the next `pending_grading`
submission in your queue (oldest-first, same order as the queue page). If
there's nobody left to grade — including the "only one student" case you
described — it takes you back to the Grading Queue with a clear
"You're all caught up!" message instead of doing nothing.

## 3. Real-time score delivery to students

This was mostly already wired up: saving a grade creates a `Notification`
record, and a model observer (`NotificationObserver`) automatically
broadcasts it live over the student's private `notifications.{userId}`
channel the instant it's created — no extra work needed there. That flow is
untouched and still fires exactly the same way.

## 4. Real-time Analytics — now actually real-time

The Analytics page was labelled "LIVE" but only ever computed its numbers
once, from data baked in at page load. It's now genuinely live:

- Refactored `TeacherController::analytics()` so both the page and a new
  JSON endpoint (`GET /teacher/analytics/live-data`,
  route name `teacher.analytics.liveData`) share one data-building method —
  they can never drift out of sync.
- The Analytics page now **polls that endpoint every 8 seconds** and
  re-renders the stat cards, charts, and table with fresh numbers — no
  page reload.
- It also listens for an `examsystem:live-update` browser event, which the
  existing notification real-time script now fires the instant a new
  "Exam Graded" (or any other) notification arrives over your existing
  Echo/Reverb/Pusher socket — so grading a paper reflects on the dashboard
  within a second or two, not up to 8 seconds later.
- A small "· updated Ns ago" indicator next to the LIVE badge shows the
  data is actually refreshing.

This reuses the exact same Echo + polling-fallback pattern your app already
uses for the notification bell, so it works whether or not your
`BROADCAST_CONNECTION` / Pusher/Reverb keys are configured — polling keeps
it "live enough" even with sockets off, and upgrades automatically once
they're on.
