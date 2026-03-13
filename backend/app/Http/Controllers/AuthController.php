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
        \Log::info('Login attempt', ['data' => $request->all(), 'headers' => $request->headers->all()]);
        
        try {
            $validated = $request->validate([
                'email' => 'required|string|email',
                'password' => 'required',
            ]);
            \Log::info('Validation passed', $validated);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        \Log::info('Attempting auth', ['email' => $request->email]);
        
        if (!Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $request->email)->first();

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
            $userData['profile'] = $user->student->load(['skills', 'talents', 'sports', 'certificates', 'violations', 'organizations']);
        } elseif ($user->role === 'professor' && $user->professor) {
            $userData['profile'] = $user->professor;
        }

        return response()->json($userData);
    }
}
