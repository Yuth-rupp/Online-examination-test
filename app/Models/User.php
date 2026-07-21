<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 🌟 Sanctum import preserved cleanly[cite: 6]
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens; // 🌟 Setup capabilities context hooks safely[cite: 6]

    /**
     * The primary key associated with the table.
     * Tells Laravel your primary key isn't the default "id" to resolve column exceptions[cite: 6].
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * Enabled explicitly since user_id relies on typical MySQL incremental counters[cite: 6].
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     * Allowed fields for mass assignment actions[cite: 6]
     */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',   // Maps directly to your custom column name[cite: 6]
        'role',
        'status',
        'institution_id',   // ✅ Required for registration workflows[cite: 6]
        'institutional_id', // ✅ Kept the correct column name matching schema metrics[cite: 6]
        'department_id',    // this user's home department (admin/teacher/student)
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Hide sensitive attributes when converting to JSON outputs[cite: 6]
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Overrule standard auth password finder to point to your custom column name[cite: 6]
     * 
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash; // Points to custom schema credentials field[cite: 6]
    }

    /**
     * Relationship to the user profile table[cite: 6].
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id'); // Match data variables keys accurately[cite: 6]
    }

    /**
     * This user's home department (their only department if they're a
     * student or a department admin; their "primary" department if
     * they're a teacher who also teaches elsewhere via $this->departments()).
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Every department this user (as a TEACHER) is assigned to teach in.
     * This is what makes "one teacher teaches Data Science, Bio
     * Engineering, and Mathematics" possible — a teacher can have as many
     * rows here as needed.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_teacher', 'user_id', 'department_id')
                     ->withTimestamps();
    }

    /**
     * True if this user is an admin scoped to exactly one department
     * (as opposed to a super_admin, who is not tied to any department).
     */
    public function isDepartmentAdmin(): bool
    {
        return $this->role === 'admin' && !is_null($this->department_id);
    }

    /**
     * The list of department IDs this user is allowed to manage/see data
     * for. A department admin gets just their own department. A teacher
     * gets their home department plus every department they've been
     * assigned to teach in (multi-department teaching).
     */
    public function managedDepartmentIds(): array
    {
        if ($this->role === 'teacher') {
            $ids = $this->departments()->pluck('departments.id')->all();
            if ($this->department_id && !in_array($this->department_id, $ids)) {
                $ids[] = $this->department_id;
            }
            return $ids;
        }

        return $this->department_id ? [$this->department_id] : [];
    }

    /**
     * Query scope: User::inDepartments([1,2,3]) — restricts the query to
     * users whose home department is one of the given IDs. Pass an empty
     * array and the scope does nothing (used for "no restriction", e.g.
     * super_admin).
     */
    public function scopeInDepartments($query, array $departmentIds)
    {
        if (empty($departmentIds)) {
            return $query;
        }

        return $query->whereIn('department_id', $departmentIds);
    }

    /**
     * Accessor so views can use Auth::user()->avatar_url directly instead of
     * having to reach through the profile relationship every time.
     */
    public function getAvatarUrlAttribute()
    {
        // Prefer the dedicated profile row (used by teacher uploads), then
        // fall back to the users.profile_image column (used by student/admin
        // uploads) so every role resolves the same accessor consistently.
        $path = $this->profile?->avatar_url ?: $this->profile_image;

        if (empty($path)) {
            return null;
        }

        // Already an absolute URL (e.g. re-saved value) — return as-is.
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // profile_image is stored via Storage::disk('public') as
        // "profile_photos/xyz.jpg", which resolves through the storage
        // symlink at /storage/... — everything else is a relative public
        // path like "uploads/avatars/foo.png" served straight from /public.
        if (Str::startsWith($path, 'profile_photos/') || Str::startsWith($path, 'avatars/')) {
            return asset('storage/' . $path);
        }

        // Stored as a relative path like "uploads/avatars/foo.png" — this must
        // be resolved from the site root, not from whatever page is currently
        // rendering it, otherwise it breaks on any page that isn't "/".
        return asset($path);
    }
}