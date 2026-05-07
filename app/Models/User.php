<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    // Tell Laravel your primary key isn't "id"
    protected $primaryKey = 'user_id'; 

    protected $fillable = [
        'full_name',
        'email',
        'password_hash', // Use your custom column name
        'role',
        'status',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];
    
    // This tells Laravel where the password is for authentication
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}