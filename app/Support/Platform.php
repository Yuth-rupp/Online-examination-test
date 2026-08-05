<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Single source of truth for the platform's display name.
 *
 * Backed by the `system_settings` table (key = 'site_name'), which is what
 * Super Admin → Global Settings → Platform Identity already writes to.
 * Cached forever and busted the moment Super Admin saves a new name, so
 * every request after a save picks up the new name with zero downtime and
 * no server restart — matching the "applied in real-time" promise on the
 * Global Settings page.
 */
class Platform
{
    public const CACHE_KEY = 'setting.site_name';
    public const DEFAULT_NAME = 'ExamSystem';

    /**
     * The platform's display name, as set by Super Admin.
     * Falls back to the default if unset or the DB is unreachable
     * (e.g. during early boot / before migrations run).
     */
    public static function name(): string
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                $value = DB::table('system_settings')->where('key', 'site_name')->value('value');
                return $value !== null && $value !== '' ? $value : self::DEFAULT_NAME;
            } catch (\Throwable $e) {
                return self::DEFAULT_NAME;
            }
        });
    }

    /**
     * Filesystem/URL-safe version — for things like CSV export filenames,
     * where the raw name might contain spaces or symbols.
     */
    public static function slug(): string
    {
        $slug = Str::slug(self::name(), '_');
        return $slug !== '' ? $slug : 'examsystem';
    }

    /**
     * Call this immediately after Super Admin saves the setting so the new
     * name is live on the very next request — no cache wait, no restart.
     */
    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}
