<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForcePasswordChange
{
    /**
     * Locks a logged-in user out of every other page until they've replaced
     * a Super-Admin-issued temporary password with one of their own choosing.
     * Only the change-password page itself and logout stay reachable while
     * the flag is set, so there's no way to get stuck.
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if ($user && $user->must_change_password) {
            $allowedRouteNames = [
                'admin.settings.password',
                'admin.settings.password.update',
                'logout',
            ];

            if (!in_array($request->route()?->getName(), $allowedRouteNames)) {
                return redirect()
                    ->route('admin.settings.password')
                    ->with('force_password_change', true);
            }
        }

        return $next($request);
    }
}
