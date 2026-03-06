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
        \Log::info('Login attempt', ['data' => $request->all()]);
        
        $request->validate([
            'email' => 'required_without:username|string',
            'username' => 'required_without:email|string',
            'password' => 'required',
        ]);

        $loginField = $request->email ?? $request->username;
        $field = filter_var($loginField, FILTER_VALIDATE_EMAIL) ? 'email' : 'email';
        
        \Log::info('Attempting auth', ['field' => $field, 'value' => $loginField]);
        
        if (!Auth::attempt([$field => $loginField, 'password' => $request->password])) {
            return response()->json([
                'message' => 'Invalid credentials'
            ], 401);
        }

        $user = User::where('email', $loginField)->first();

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
