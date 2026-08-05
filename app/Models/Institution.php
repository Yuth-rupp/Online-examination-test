<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Course;

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

    /**
     * Override or complement users_count.
     * If domain is '@' or empty, return total count of all users across the system.
     */
    public function getUsersCountAttribute()
    {
        if ($this->domain === '@' || empty($this->domain)) {
            return User::count();
        }

        // Return the relationship count if loaded, or query it
        return $this->attributes['users_count'] ?? $this->users()->count();
    }
}