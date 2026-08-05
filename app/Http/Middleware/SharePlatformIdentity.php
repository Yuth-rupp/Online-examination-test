<?php

namespace App\Http\Middleware;

use App\Support\Platform;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;

/**
 * Shares the platform's display name (Super Admin -> Global Settings ->
 * Platform Identity) with every view, on every single request.
 *
 * This replaces the old View::composer('*', ...) that lived in
 * AppServiceProvider::boot(). That approach only registers the composer
 * once when a worker boots -- under Octane/FrankenPHP the app stays in
 * memory across many requests, so a boot-once composer can silently stop
 * firing (or keep pointing at stale container state) after the first
 * request on a given worker. Running this as middleware means it re-reads
 * and re-shares the name fresh on every request, on every worker, with
 * zero reliance on boot-time state.
 */
class SharePlatformIdentity
{
    public function handle(Request $request, Closure $next)
    {
        View::share('platformName', Platform::name());
        View::share('platformNameSlug', Platform::slug());

        return $next($request);
    }
}
