<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Exam extends Model
{
    use HasFactory;

    // 1. Specify the custom Primary Key name from your SQL
    protected $primaryKey = 'exam_id';

    // 2. Tell Laravel IDs are NOT auto-incrementing integers
    public $incrementing = false;

    // 3. Specify that the ID type is a string (UUID)
    protected $keyType = 'string';

    // 4. Allowed fields for mass assignment
    protected $fillable = [
        'title',
        'course_id',
        'created_by',
        'duration',
        'pass_mark',
        'instructions',
        'status',
        'start_time',
        'end_time',
    ];

    // 5. Define the relationship to the Course
    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}