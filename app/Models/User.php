<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable; // 🌟 Kept clean without HasApiTokens

    // 1. Tell Laravel your primary key isn't the default "id"
    protected $primaryKey = 'user_id'; 

    // 2. Allowed fields for mass assignment
    protected $fillable = [
        'institution_id',
        'full_name',
        'email',
        'password_hash', // Maps directly to your custom column name
        'role',
        'status',
    ];

    // 3. Hide sensitive attributes when converting to JSON array outputs
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    // 4. Overrule standard auth password finder to point to your custom column name
    public function getAuthPassword()
    {
        return $this->password_hash;
    }
}