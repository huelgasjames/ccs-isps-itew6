<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HandleDemoTokens
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        
        // Check if this is a demo token
        if ($token && str_starts_with($token, 'demo-')) {
            // Extract user info from demo token
            $demoUsers = [
                'demo-admin-token' => [
                    'id' => 1,
                    'name' => 'Demo Admin',
                    'email' => 'admin@ccs.edu',
                    'role' => 'admin'
                ],
                'demo-professor-token' => [
                    'id' => 2,
                    'name' => 'Demo Professor',
                    'email' => 'professor@ccs.edu',
                    'role' => 'professor'
                ],
                'demo-student-token' => [
                    'id' => 3,
                    'name' => 'Demo Student',
                    'email' => 'student@ccs.edu',
                    'role' => 'student'
                ],
                'demo-maria-token' => [
                    'id' => 4,
                    'name' => 'Maria Santos',
                    'email' => 'maria@ccs.edu',
                    'role' => 'student'
                ]
            ];

            // Extract token type (remove timestamp if present)
            $tokenType = explode('-', $token);
            if (count($tokenType) >= 3) {
                $tokenType = implode('-', array_slice($tokenType, 0, 3));
            } else {
                $tokenType = $token;
            }

            if (isset($demoUsers[$tokenType])) {
                // Create a simple stdClass object for the demo user
                $demoUser = (object) $demoUsers[$tokenType];
                
                // Store the demo user in the request for later use
                $request->attributes->set('demo_user', $demoUser);
                
                // Set a custom header to indicate this is a demo request
                $request->headers->set('X-Demo-Auth', 'true');
                
                // Return the response directly without going through sanctum
                return $next($request);
            }
        }
        
        // If not a demo token or invalid demo token, continue with normal authentication
        return $next($request);
    }
}
