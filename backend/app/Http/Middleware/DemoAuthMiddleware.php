<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class DemoAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        // Check if this is a demo token
        if ($token && str_starts_with($token, 'demo-')) {
            // Determine role from token string (e.g. "demo-admin-token-...", "demo-professor-token-...")
            $role = 'admin'; // default
            if (str_contains($token, 'demo-professor')) {
                $role = 'professor';
            } elseif (str_contains($token, 'demo-student') || str_contains($token, 'demo-maria')) {
                $role = 'student';
            }

            // Find or use the first user with that role to satisfy Auth::id()
            $user = User::where('role', $role)->first() ?? User::first();

            if ($user) {
                Auth::setUser($user);
            }

            return $next($request);
        }
        
        // For non-demo tokens, sanctum middleware handles authentication
        return $next($request);
    }
}
