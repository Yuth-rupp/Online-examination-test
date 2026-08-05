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
     * Get the accurate user count for this institution without duplicates.
     */
    public function getUsersCountAttribute()
    {
        // For specific email domains (e.g. gmail.com)
        if ($this->domain !== '@' && !empty($this->domain)) {
            $cleanDomain = ltrim(strtolower(trim($this->domain)), '@');
            
            return User::where(function ($q) use ($cleanDomain) {
                $q->where('email', 'LIKE', "%@{$cleanDomain}")
                  ->orWhere('institution_id', $this->id);
            })->count();
        }

        // For the Fallback Main Campus Institution (@)
        // Count users assigned directly to this institution OR users whose domain isn't in any registered institution
        $otherDomains = Institution::where('domain', '!=', '@')
            ->whereNotNull('domain')
            ->pluck('domain')
            ->map(fn($d) => ltrim(strtolower(trim($d)), '@'))
            ->filter()
            ->values()
            ->toArray();

        return User::where('institution_id', $this->id)
            ->orWhere(function ($query) use ($otherDomains) {
                $query->whereNull('institution_id');
                foreach ($otherDomains as $domain) {
                    $query->where('email', 'NOT LIKE', "%@{$domain}");
                }
            })->count();
    }
}