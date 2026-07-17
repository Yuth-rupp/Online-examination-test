<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable inside the database structure.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name', 
        'code', 
        'description', 
        'institution_id', 
        'teacher_id', 
        'is_active'
    ];

    /**
     * Get the institution profile managing this curriculum partition context.
     */
    public function institution() 
    {
        return $this->belongsTo(Institution::class, 'institution_id', 'id');
    }

    /**
     * Get the teacher staff profile account assigned to handle this course.
     */
    public function teacher() 
    {
        // Explicitly maps 'teacher_id' column to 'user_id' primary key column on the users table
        return $this->belongsTo(User::class, 'teacher_id', 'user_id');
    }

    /**
     * Get the collection of examination modules deployed under this course context.
     */
    public function exams() 
    {
        return $this->hasMany(Exam::class, 'course_id', 'id');
    }

    /**
     * Get the student enrollment records logged into this class section partition.
     */
    public function enrollments() 
    {
        return $this->hasMany(Enrollment::class, 'course_id', 'id');
    }
}