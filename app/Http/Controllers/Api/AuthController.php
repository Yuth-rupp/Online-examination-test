<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password; // For password reset broker operations

class AuthController extends Controller
{
    /**
     * Handle an incoming browser registration request.
     */
    public function register(Request $request)
    {
        // 1. Validate incoming data payloads from the submission form
        $data = $request->validate([
            'first_name'       => 'required|string|max:255',
            'last_name'        => 'required|string|max:255',
            'email'            => 'required|string|email|max:255|unique:users,email',
            'password'         => 'required|string|min:8',
            'institutional_id' => 'required|string|max:255|unique:users,institutional_id', // Enforced real-world unique constraint
            'role'             => 'nullable|string', // Dynamic role entry validation rule
        ]);

        // 2. ✅ FIXED ORDER: Concatenate First Name followed by Last Name
        // Example: First Name = "Yun", Last Name = "Dalin" -> Output: "Yun Dalin"
        $fullName = $data['first_name'] . ' ' . $data['last_name'];

        // 3. 🎯 DYNAMIC ROLE ATTRIBUTION WITH TEACHER FALLBACK
        // Grabs the hidden input 'role' parameter. Defaults strictly to 'teacher' if left blank.
        $assignedRole = $request->input('role', 'teacher');

        // 4. Save and commit the user data fields into your phpMyAdmin database table
        $user = User::create([
            'full_name'        => $fullName,
            'email'            => $data['email'],
            'password_hash'    => Hash::make($data['password']), // Maps to your custom column name
            'role'             => $assignedRole,                 // Saves 'teacher', 'student', or 'admin'
            'status'           => 'active',
            'institution_id'   => 1,                             // Assigns to Institution #1 automatically for local testing
            'institutional_id' => $data['institutional_id'], 
        ]);

        // 5. Flash parameters to short-term session memory for your success page card layout
        return redirect()->route('register.success')->with([
            'registered_name'  => $user->full_name,
            'registered_email' => $user->email,
            'registered_role'  => $user->role
        ]);
    }

    /**
     * Handle an incoming browser login form submission.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|string|email',
            'password' => 'required|string',
        ]);

        // 1. ✅ FIX: Fetch the user manually first to accommodate the custom password_hash column
        $user = User::where('email', $request->email)->first();

        // 2. ✅ FIX: Check if user exists and manually verify hash signature values
        if ($user && Hash::check($request->password, $user->password_hash)) {
            
            // Log the user context into the framework session storage explicitly
            Auth::guard('web')->login($user);
            
            $request->session()->regenerate();

            if ($user->role === 'teacher') {
                return redirect()->route('teacher.dashboard');
            }
            
            return redirect()->intended('/user');
        }

        return redirect()->back()->with('error', 'The provided credentials do not match our records.')->withInput($request->only('email'));
    }

    /**
     * Handle incoming browser request password reset link generation.
     */
    public function sendResetLink(Request $request)
    {
        // Validate that the input field matches an active user profile
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We cannot find a user with that institutional email address.'
        ]);

        // Dispatches background tokens and reset link configurations to the user's inbox
        $status = Password::broker()->sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
            // ✅ FLASH EMAIL AND REDIRECT TO DESIGN PAGE
            session()->flash('reset_email', $request->email);
            return redirect()->route('password.success');
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Handle updating the database table with the newly changed password credentials.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'    => 'required',
            'email'    => 'required|email|exists:users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        // Use Laravel's Password broker to handle updating users table elements automatically
        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // Update user password field explicitly using your custom database column name
                $user->password_hash = Hash::make($password);
                $user->save();
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login.page')->with('success', __($status));
        }

        return back()->withErrors(['email' => __($status)]);
    }

    /**
     * Destroy an authenticated web browser session.
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.page')->with('success', 'Logged out securely.');
    }
}