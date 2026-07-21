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
     * Everyone whose "home" department (users.department_id) is this one —
     * this includes students, teachers, and the department's own admin(s).
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id', 'user_id');
    }

    /**
     * Convenience: only the students that live in this department.
     */
    public function students()
    {
        return $this->users()->where('role', 'student');
    }

    /**
     * The admin(s) whose users.department_id points at this department.
     * In the common case this is a single person.
     */
    public function admins()
    {
        return $this->users()->where('role', 'admin');
    }

    /**
     * All teachers linked to this department — through the pivot table,
     * so this naturally includes teachers who ALSO teach other
     * departments (e.g. a teacher in Data Science, Bio Engineering, and
     * Mathematics shows up here for all three).
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
