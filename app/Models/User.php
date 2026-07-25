<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens;

    /**
     * The primary key associated with the table.
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',
        'role',
        'status',
        'institution_id',
        'institutional_id',
        'department_id',
        'profile_image',
        'telegram_username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Overrule standard auth password finder to point to custom column.
     * 
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash;
    }

    /**
     * Relationship to the user profile table.
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id');
    }

    /**
     * This user's primary/home department.
     */
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    /**
     * Departments managed by this user (for Department Admins).
     * Uses department_user pivot table.
     */
    public function managedDepartments()
    {
        return $this->belongsToMany(Department::class, 'department_user', 'user_id', 'department_id');
    }

    /**
     * Every department this user (as a TEACHER) is assigned to teach in.
     * Uses department_teacher pivot table.
     */
    public function departments()
    {
        return $this->belongsToMany(Department::class, 'department_teacher', 'user_id', 'department_id')
                    ->withTimestamps();
    }

    /**
     * Whether a super_admin already exists in the system.
     */
    public static function superAdminExists(?int $excludingUserId = null): bool
    {
        return static::query()
            ->where('role', 'super_admin')
            ->when($excludingUserId, fn ($q) => $q->where('user_id', '!=', $excludingUserId))
            ->exists();
    }

    /**
     * True if this user is currently the ONLY super_admin account in the system.
     */
    public function isSoleSuperAdmin(): bool
    {
        if ($this->role !== 'super_admin') {
            return false;
        }

        return !static::superAdminExists($this->user_id);
    }

    /**
     * True if this user is an admin scoped to a department.
     */
    public function isDepartmentAdmin(): bool
    {
        return $this->role === 'admin' && !is_null($this->department_id);
    }

    /**
     * The list of department IDs this user is allowed to manage/see data for.
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
     * Query scope: User::inDepartments([1,2,3])
     */
    public function scopeInDepartments($query, array $departmentIds)
    {
        if (empty($departmentIds)) {
            return $query;
        }

        return $query->whereIn('department_id', $departmentIds);
    }

    /**
     * Accessor for user avatar URL.
     */
    public function getAvatarUrlAttribute()
    {
        $path = $this->profile?->avatar_url ?: $this->profile_image;

        if (empty($path)) {
            return null;
        }

        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        if (Str::startsWith($path, 'profile_photos/') || Str::startsWith($path, 'avatars/')) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }
}