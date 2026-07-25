<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_id',
        'name',
        'code',
        'description',
        'is_active',
    ];

    /**
     * The institution (university) this department sits inside.
     */
    public function institution()
    {
        return $this->belongsTo(Institution::class, 'institution_id', 'id');
    }

    /**
     * Everyone whose home department (users.department_id) is this department.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'id');
    }

    /**
     * Convenience relationship: only students belonging to this department.
     */
    public function students()
    {
        return $this->hasMany(User::class, 'department_id', 'id')->where('role', 'student');
    }

    /**
     * Department Admins directly assigned via users.department_id
     */
    public function admins()
    {
        return $this->hasMany(User::class, 'department_id', 'id')->where('role', 'admin');
    }

    /**
     * The admin students/teachers in this department should contact for a
     * password reset. Prefers an admin who has actually set a Telegram
     * handle; falls back to any department admin if none has one yet.
     */
    public function contactAdmin()
    {
        $admins = $this->admins()->get();

        return $admins->firstWhere('telegram_username', '!=', null) ?: $admins->first();
    }

    /**
     * All teachers linked to this department through the pivot table.
     */
    public function teachers()
    {
        return $this->belongsToMany(User::class, 'department_teacher', 'department_id', 'user_id')
                    ->withTimestamps();
    }

    /**
     * Courses filed under this department.
     */
    public function courses()
    {
        return $this->hasMany(Course::class, 'department_id', 'id');
    }
}