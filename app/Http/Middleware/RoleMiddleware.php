<?php

namespace App\Http\Middleware;

use Closure; // The class is imported with a capital C
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    // ✅ FIX: Changed 'closure' to 'Closure' with a capital C
    public function handle(Request $request, Closure $next, ...$roles) 
    {
        // 1. Double check if the user is even logged in
        if (!Auth::check()) {
            return redirect()->route('login.page');
        }

        // 2. Check if the user's role is in the list of allowed roles
        if (!in_array(Auth::user()->role, $roles)) {
            
            // If the request came from JavaScript (axios/fetch), return JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Forbidden. You do not have the required permissions.'
                ], 403);
            }
            
            // If they are browsing normally, show Laravel's standard 403 "Unauthorized" page
            abort(403, 'Unauthorized Action. Your current role does not grant you access to this page.');
        }

        // 3. If they pass the checks, let them proceed to the route
        return $next($request);
    }
}