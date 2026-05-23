<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:8',
            'role'       => 'required|string',
        ]);

        $fullName = $data['first_name'] . ' ' . $data['last_name'];

        $user = User::create([
            'full_name' => $fullName,
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'status'    => 'active',
        ]);

        Auth::guard('web')->login($user);

        return redirect()->route('teacher.dashboard')->with('success', 'Welcome to your dashboard!');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::guard('web')->attempt($credentials)) {
            $request->session()->regenerate();
            $user = Auth::guard('web')->user();

            if ($user->role === 'teacher') {
                return redirect()->route('teacher.dashboard');
            }
            return redirect()->intended('/user');
        }

        return redirect()->back()->with('error', 'The provided credentials do not match our records.');
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.page')->with('success', 'Logged out securely.');
    }
}