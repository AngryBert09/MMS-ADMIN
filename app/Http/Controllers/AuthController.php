<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use App\Mail\TwoFactorCodeMail;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */



    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            Log::warning('Failed login attempt - Email not found', ['email' => $request->email, 'ip' => $request->ip()]);
            return redirect()->route('auth.login')->withErrors(['email' => 'Email not found'])->withInput();
        }

        if ($user->status !== 'Active') {
            Log::warning('Login attempt for inactive account', ['email' => $request->email, 'status' => $user->status, 'ip' => $request->ip()]);
            return redirect()->route('auth.login')->withErrors(['email' => 'Account is inactive.'])->withInput();
        }

        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Failed login attempt - Incorrect password', ['email' => $request->email, 'ip' => $request->ip()]);
            return redirect()->route('auth.login')->withErrors(['password' => 'Incorrect password'])->withInput();
        }

        if ($user->role !== 'admin') {
            Log::warning('Unauthorized login attempt', ['email' => $request->email, 'role' => $user->role, 'ip' => $request->ip()]);
            return redirect()->route('auth.login')->withErrors(['email' => 'Access denied. Only admins can log in.'])->withInput();
        }

        // Generate a 6-digit OTP code
        $otp = rand(100000, 999999);

        // Save OTP in the session (or database)
        Session::put('2fa:user_id', $user->id);
        Session::put('2fa:otp', $otp);
        Session::put('2fa:email', $user->email);
        Session::put('2fa:expires_at', now()->addMinutes(5)); // OTP expires in 5 minutes

        // Send OTP to user via email
        Mail::to($user->email)->send(new TwoFactorCodeMail($otp));

        Log::info('2FA OTP sent', ['email' => $user->email, 'otp' => $otp]);

        // Redirect to 2FA verification page
        return redirect()->route('auth.2fa.verify')->with('success', 'A verification code has been sent to your email.');
    }


    /**
     * Handle logout.
     */
    public function logout()
    {
        Auth::logout();

        Cache::flush(); // Clears all cache
        return redirect()->route('auth.login')->with('success', 'Logged out successfully');
    }

    public function verify2FA(Request $request)
    {
        try {
            $request->validate(['otp' => 'required|numeric']);

            $userId = Session::get('2fa:user_id');
            $otp = Session::get('2fa:otp');
            $expiresAt = Session::get('2fa:expires_at');

            if (!$userId || !$otp || now()->gt($expiresAt)) {
                return response()->json(['success' => false, 'message' => 'OTP expired or invalid'], 400);
            }

            if ($request->otp == $otp) {
                // Clear the OTP from the session
                Session::forget(['2fa:user_id', '2fa:otp', '2fa:expires_at']);

                // Log the user in
                auth()->loginUsingId($userId);

                return response()->json(['success' => true, 'message' => 'Verification successful']);
            }

            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        } catch (\Exception $e) {
            Log::error('2FA verification error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    public function resend2FA(Request $request)
    {
        if (!session()->has('2fa:user_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        // Here, regenerate OTP and send it via email
        $newOtp = rand(100000, 999999);
        session(['2fa:otp' => $newOtp]);


        Mail::to(session('2fa:email'))->send(new TwoFactorCodeMail($newOtp));

        return response()->json(['success' => true, 'message' => 'A new code has been sent to your email.']);
    }
}
