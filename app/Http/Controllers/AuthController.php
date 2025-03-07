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
use App\Traits\ActivityLogger;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    use ActivityLogger; // Include the Activity Logger Trait

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
            'g-recaptcha-response' => 'required', // Ensure reCAPTCHA is submitted
        ]);

        // Verify Google reCAPTCHA
        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => env('GOOGLE_RECAPTCHA_SECRET_KEY'),
            'response' => $request->input('g-recaptcha-response'),
            'remoteip' => $request->ip(),
        ]);

        $responseData = $response->json();

        if (!$responseData['success']) {
            Log::warning('reCAPTCHA verification failed', ['ip' => $request->ip()]);
            return redirect()->route('auth.login')->withErrors(['captcha' => 'reCAPTCHA verification failed.'])->withInput();
        }

        // Proceed with normal login process after reCAPTCHA passes
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

        // Generate 2FA OTP
        $otp = rand(100000, 999999);

        // Save OTP in session
        Session::put('2fa:user_id', $user->id);
        Session::put('2fa:otp', $otp);
        Session::put('2fa:email', $user->email);
        Session::put('2fa:expires_at', now()->addMinutes(5)); // OTP expires in 5 minutes

        // Send OTP via email
        Mail::to($user->email)->send(new TwoFactorCodeMail($otp));

        Log::info('2FA OTP sent', ['email' => $user->email, 'otp' => $otp]);

        // Store activity
        $this->storeActivity("2FA OTP sent to {$user->email} for login.");

        // Redirect to 2FA verification page
        return redirect()->route('auth.2fa.verify')->with('success', 'A verification code has been sent to your email.');
    }


    /**
     * Handle logout.
     */
    public function logout()
    {
        // Store logout activity
        $this->storeActivity("User " . Auth::user()->email . " logged out.");

        Auth::logout();
        Cache::flush(); // Clears all cache

        return redirect()->route('auth.login')->with('success', 'Logged out successfully');
    }

    /**
     * Handle 2FA verification.
     */
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

                // Store activity
                $this->storeActivity("User ID {$userId} successfully verified 2FA and logged in.");

                return response()->json(['success' => true, 'message' => 'Verification successful']);
            }

            return response()->json(['success' => false, 'message' => 'Invalid OTP'], 400);
        } catch (\Exception $e) {
            Log::error('2FA verification error', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * Resend 2FA OTP.
     */
    public function resend2FA(Request $request)
    {
        if (!session()->has('2fa:user_id')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized access.'], 403);
        }

        // Regenerate OTP and send it via email
        $newOtp = rand(100000, 999999);
        session(['2fa:otp' => $newOtp]);

        Mail::to(session('2fa:email'))->send(new TwoFactorCodeMail($newOtp));

        // Store activity
        $this->storeActivity("New 2FA OTP sent to " . session('2fa:email'));

        return response()->json(['success' => true, 'message' => 'A new code has been sent to your email.']);
    }
}
