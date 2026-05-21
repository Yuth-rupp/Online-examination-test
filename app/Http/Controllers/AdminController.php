<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    // 🔐 USER REGISTRATION
    public function register(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email',
            'password'  => 'required|string|min:6',
            'role'      => 'nullable|string|in:admin,teacher,student',
        ]);

        $user = User::create([
            'full_name'     => $data['full_name'],
            'email'         => $data['email'],
            'password_hash' => Hash::make($data['password']), // Maps to your custom column
            'role'          => $data['role'] ?? 'student',
            'status'        => 'active',
        ]);

        // Generate Sanctum API token cleanly using your custom user_id structure
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ], 201);
    }

    // 🔑 USER LOGIN
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        // 🌟 CRUCIAL: Fetch user manually by email because Laravel attempts to search 'password' instead of 'password_hash'
        $user = User::where('email', $data['email'])->first();

        // Check if user exists and match the incoming text password against your password_hash column
        if (!$user || !Hash::check($data['password'], $user->password_hash)) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 401);
        }

        // Generate token upon verified authentication
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user'         => $user,
            'access_token' => $token,
            'token_type'   => 'Bearer',
        ]);
    }

    // 🚪 USER LOGOUT
    public function logout(Request $request)
    {
        // Revoke the token that was used to gain access to this route
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

    // 👤 GET LOGGED IN USER PROFILE
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }
}