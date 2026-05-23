<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'description', 'institution_id', 'teacher_id', 'is_active'
    ];

    public function institution() {
        return $this->belongsTo(Institution::class, 'institution_id', 'id');
    }

    public function teacher() {
        // Maps 'teacher_id' column to 'user_id' primary key column on the users table
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    public function exams() {
        return $this->hasMany(Exam::class, 'course_id', 'id');
    }

    public function enrollments() {
        return $this->hasMany(Enrollment::class, 'course_id', 'id');
    }
}