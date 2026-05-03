<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes:
     *   ->middleware('role:admin')
     *   ->middleware('role:admin,staff')  ← any of these roles
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Load roles if not already loaded
        if (!$user->relationLoaded('roles')) {
            $user->load('roles');
        }

        // Check if user has any of the required roles
        if (!$user->hasAnyRole($roles)) {
            abort(403, 'You do not have permission to access this page.');
        }

        // Check if user account is active
        if ($user->status !== 'active') {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Your account has been suspended. Contact support.']);
        }

        return $next($request);
    }
}