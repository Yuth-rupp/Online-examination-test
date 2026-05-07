<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;
// use App\Models\Exam; // only if needed

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'domain', 'logo', 'is_active', 'settings'
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean'
    ];

    public function users() {
        return $this->hasMany(User::class);
    }

    public function courses() {
        return $this->hasMany(Course::class);
    }
}