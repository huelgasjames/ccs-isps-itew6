<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DemoAuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        // Check if this is a demo token
        if ($token && str_starts_with($token, 'demo-')) {
            // For demo tokens, we'll bypass the sanctum authentication
            // and allow the request to proceed
            return $next($request);
        }
        
        // For non-demo tokens, we need to ensure sanctum authentication passes
        // If we reach here, sanctum middleware will handle the authentication
        return $next($request);
    }
}
