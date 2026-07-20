<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // 🌟 Sanctum import preserved cleanly[cite: 6]
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use Notifiable, HasApiTokens; // 🌟 Setup capabilities context hooks safely[cite: 6]

    /**
     * The primary key associated with the table.
     * Tells Laravel your primary key isn't the default "id" to resolve column exceptions[cite: 6].
     */
    protected $primaryKey = 'user_id';

    /**
     * Indicates if the IDs are auto-incrementing.
     * Enabled explicitly since user_id relies on typical MySQL incremental counters[cite: 6].
     */
    public $incrementing = true;

    /**
     * The attributes that are mass assignable.
     * Allowed fields for mass assignment actions[cite: 6]
     */
    protected $fillable = [
        'full_name',
        'email',
        'password_hash',   // Maps directly to your custom column name[cite: 6]
        'role',
        'status',
        'institution_id',   // ✅ Required for registration workflows[cite: 6]
        'institutional_id', // ✅ Kept the correct column name matching schema metrics[cite: 6]
        'profile_image',
    ];

    /**
     * The attributes that should be hidden for serialization.
     * Hide sensitive attributes when converting to JSON outputs[cite: 6]
     */
    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Overrule standard auth password finder to point to your custom column name[cite: 6]
     * 
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password_hash; // Points to custom schema credentials field[cite: 6]
    }

    /**
     * Relationship to the user profile table[cite: 6].
     */
    public function profile()
    {
        return $this->hasOne(UserProfile::class, 'user_id', 'user_id'); // Match data variables keys accurately[cite: 6]
    }

    /**
     * Accessor so views can use Auth::user()->avatar_url directly instead of
     * having to reach through the profile relationship every time.
     */
    public function getAvatarUrlAttribute()
    {
        // Prefer the dedicated profile row (used by teacher uploads), then
        // fall back to the users.profile_image column (used by student/admin
        // uploads) so every role resolves the same accessor consistently.
        $path = $this->profile?->avatar_url ?: $this->profile_image;

        if (empty($path)) {
            return null;
        }

        // Already an absolute URL (e.g. re-saved value) — return as-is.
        if (Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        // profile_image is stored via Storage::disk('public') as
        // "profile_photos/xyz.jpg", which resolves through the storage
        // symlink at /storage/... — everything else is a relative public
        // path like "uploads/avatars/foo.png" served straight from /public.
        if (Str::startsWith($path, 'profile_photos/') || Str::startsWith($path, 'avatars/')) {
            return asset('storage/' . $path);
        }

        // Stored as a relative path like "uploads/avatars/foo.png" — this must
        // be resolved from the site root, not from whatever page is currently
        // rendering it, otherwise it breaks on any page that isn't "/".
        return asset($path);
    }
}