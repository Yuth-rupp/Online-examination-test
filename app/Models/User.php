<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 🌟 FIXED: Added the missing Sanctum import

class User extends Authenticatable
{
    use Notifiable, HasApiTokens; // 🌟 FIXED: Added HasApiTokens trait here so Sanctum can generate tokens!

    // Tell Laravel your primary key isn't the default "id"
    protected $primaryKey = 'user_id'; 

    // Allowed fields for mass assignment
    protected $fillable = [
        'institution_id',
        'full_name',
        'email',
        'password_hash', // Maps directly to your custom column name
        'role',
        'status',
    ];

    // Hide sensitive attributes when converting to JSON outputs
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // Overrule standard auth password finder to point to your custom column name
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
}