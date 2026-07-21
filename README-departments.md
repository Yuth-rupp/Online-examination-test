# Department Admin System — What This Adds

This gives your exam system real "departments" (like Data Science, Bio
Engineering, Mathematics), so:

- A **department admin** only sees/manages their own department's
  users, courses, and exams — not the whole university.
- A **super admin** still sees and controls everything, and is the one
  who creates departments and puts an admin in charge of each one.
- A **teacher can teach in more than one department** at the same time
  (e.g. Data Science + Bio Engineering + Mathematics).

## 1. Copy these files into your project

Copy every file in this zip into the matching path in your Laravel
project (they will overwrite the existing versions of `User.php`,
`Course.php`, `Exam.php`, `AdminController.php`, `SuperAdminController.php`,
`routes/web.php`, and `admin/users.blade.php` — the rest are new files).

## 2. Run the new migrations

```
php artisan migrate
```

This creates:
- `departments` — the department list
- `department_teacher` — the table that lets one teacher belong to
  many departments
- adds `department_id` to `users` and `courses`

Nothing in your existing data is touched or deleted. Every existing
user/course simply has `department_id = NULL` until you assign one.

## 3. How to use it (as super admin)

1. Go to `/super-admin/departments` (new page). Create your
   departments there (e.g. "Data Science", "Bio Engineering",
   "Mathematics").
2. For each department, pick an existing admin from the dropdown to
   put them in charge of it. (If you don't have a spare admin yet,
   create one first from **Super Admin → Admins**, leaving their
   department blank, then come back and assign them.)
3. That's it — the moment an admin has a department, everything they
   see in their Admin dashboard (`/admin/dashboard`, `/admin/users`,
   `/admin/exams`) is automatically filtered to just their department.
   An admin with NO department assigned still sees everything (so
   nothing breaks for admins you haven't migrated to a department
   yet).

## 4. How a teacher ends up in multiple departments

From `/admin/departments/{id}/teachers` (linked from the department
card on the Departments page, or reachable directly), a department
admin can search for ANY existing teacher by name/email and add them
to their department's teaching roster. This does not remove that
teacher from any other department they already teach in — it just
adds one more link. A teacher's dashboard/courses aren't changed by
this doc set (that's a separate follow-up if you want teachers to
pick which department a course belongs to when creating it).

## Notes / things you may want next
- New courses don't yet have a department picker in the teacher's
  "create course" screen — right now `courses.department_id` has to
  be set manually or via `$course->department_id = ...`. Say the word
  and I'll wire that dropdown in too.
- Support tickets aren't department-scoped (they don't carry a
  department reference in your schema), so every admin still sees all
  of them regardless of department.
