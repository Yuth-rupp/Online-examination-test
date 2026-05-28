<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password; 
use Illuminate\Support\Facades\Mail;     // ✅ For sending OTPs
use Illuminate\Support\Facades\Cache;    // ✅ For storing OTPs temporarily
use App\Mail\SuperAdminLoginCode;         // ✅ Our new Mail class

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
            'institutional_id' => 'required|string|max:255|unique:users,institutional_id', 
            'role'             => 'nullable|string', 
        ]);

        $fullName = $data['first_name'] . ' ' . $data['last_name'];

        // 2. Automate role detection matching the Institutional ID prefixes
        // Converts to uppercase so inputs like "adm-5566-7788" work flawlessly too!
        $idPrefix = strtoupper($data['institutional_id']); 

        if (str_starts_with($idPrefix, 'ADM-')) {
            $assignedRole = 'admin';
        } elseif (str_starts_with($idPrefix, 'STU-')) {
            $assignedRole = 'student';
        } else {
            // Fallback to form payload string fallback if no prefixes match
            $assignedRole = $request->input('role', 'teacher');
        }

        // 3. Save and commit the user data fields into your database table
        $user = User::create([
            'full_name'        => $fullName,
            'email'            => $data['email'],
            'password_hash'    => Hash::make($data['password']), // ✅ Maps to your migration column
            'role'             => $assignedRole,                 
            'status'           => 'active',
            'institution_id'   => null,                                             
            'institutional_id' => $data['institutional_id'], 
        ]);

        // Flash parameters to short-term session memory for your success page card layout
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
            'role'     => 'required|string|in:student,teacher,admin,super_admin', 
        ]);

        // Find the user profile relative to their unique email field string
        $user = User::where('email', $request->email)->first();

        // ✅ Uses manual hash check mapping cleanly against your custom column: 'password_hash'
        if ($user && Hash::check($request->password, $user->password_hash)) {
            
            if ($user->role !== $request->role) {
                return redirect()->back()
                    ->with('error', 'The selected role does not match your account type.')
                    ->withInput($request->only('email'));
            }

            // Log the user context into the browser web state guard directly
            Auth::guard('web')->login($user);
            
            $request->session()->regenerate();

            // Redirect user to their corresponding dynamic layout workspace names
            switch ($user->role) {
                case 'super_admin':
                case 'admin':
                    return redirect()->intended('/admin/dashboard');
                case 'teacher':
                    return redirect()->route('teacher.dashboard');   
                case 'student':
                default:
                    return redirect()->intended('/student/dashboard');
            }
        }

        return redirect()->back()
            ->with('error', 'The provided credentials do not match our records.')
            ->withInput($request->only('email'));
    }

    /**
     * Handle incoming browser request password reset link generation.
     */
    public function sendResetLink(Request $request)
    {
        // 1. Validate that the input field matches an active user profile
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'We cannot find a user with that institutional email address.'
        ]);

        $user = User::where('email', $request->email)->first();

        // 👑 2. SUPER ADMIN CUSTOM RECOVERY FLOW (6-Digit OTP)
        if ($user && $user->role === 'super_admin') {
            
            // Generate a random 6-digit code
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

            // Store the code in Laravel's Cache for exactly 5 minutes
            Cache::put('superadmin_otp_' . $user->email, $otp, now()->addMinutes(5));

            // Send the email with the code
            Mail::to($user->email)->send(new SuperAdminLoginCode($otp));

            // Save the email in the session so the OTP page knows who is trying to recover their account
            session()->put('superadmin_attempt_email', $user->email);

            // Redirect them to the 6 little boxes!
            return redirect()->route('superadmin.verify.page');
        }

        // 🎓 3. NORMAL USERS FLOW (Standard Reset Link)
        $status = Password::broker()->sendResetLink($request->only('email'));

        if ($status === Password::RESET_LINK_SENT) {
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

        $status = Password::broker()->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                // ✅ Intercepts update logic to sync password straight with your custom column name
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

    /* |--------------------------------------------------------------------------
    | SUPER ADMIN PASSWORDLESS OTP LOGIC
    |--------------------------------------------------------------------------
    |
    */

    /**
     * Generate OTP and send it via Email (Used by the "Resend Code" button).
     */
    public function sendSuperAdminCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->where('role', 'super_admin')->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Unauthorized. This email does not have Super Admin privileges.']);
        }

        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        Cache::put('superadmin_otp_' . $request->email, $otp, now()->addMinutes(5));
        Mail::to($request->email)->send(new SuperAdminLoginCode($otp));
        session()->put('superadmin_attempt_email', $request->email);

        return redirect()->route('superadmin.verify.page');
    }

    /**
     * Verify the OTP and log the Super Admin in.
     */
    public function verifySuperAdminCode(Request $request)
    {
        $request->validate(['verification_code' => 'required|string|size:6']);

        $email = session()->get('superadmin_attempt_email');

        if (!$email) {
            return redirect()->route('password.request')->withErrors(['email' => 'Session expired. Please request a new code.']);
        }

        $cachedOtp = Cache::get('superadmin_otp_' . $email);

        if ($cachedOtp && $cachedOtp === $request->verification_code) {
            
            $user = User::where('email', $email)->first();
            Auth::guard('web')->login($user);
            $request->session()->regenerate();

            Cache::forget('superadmin_otp_' . $email);
            session()->forget('superadmin_attempt_email');

            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['verification_code' => 'Invalid or expired code. Please try again.']);
    }
}