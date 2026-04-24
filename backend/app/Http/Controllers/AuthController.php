<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validated = $request->validate([
                'email' => 'required|string',
                'password' => 'required|string',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }
        
        // Convert username to email if needed
        $email = $validated['email'];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailMap = [
                'admin' => 'admin@ccs.edu',
                'professor' => 'professor@ccs.edu',
                'student' => 'student@ccs.edu',
                'maria' => 'maria@ccs.edu'
            ];
            $email = $emailMap[$email] ?? $email;
        }
        
        if (!Auth::attempt(['email' => $email, 'password' => $validated['password']])) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = Auth::user();

        return response()->json([
            'user' => $user,
            'token' => $user->createToken('auth_token')->plainTextToken,
            'role' => $user->role,
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    public function me(Request $request)
    {
        $user = $request->user();
        
        $userData = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ];

        if ($user->role === 'student' && $user->student) {
            $userData['profile'] = $user->student->load(['skills', 'activities', 'academicHistory', 'affiliations', 'violations']);
        } elseif ($user->role === 'professor' && $user->professor) {
            $userData['profile'] = $user->professor;
        }

        return response()->json($userData);
    }
}
