<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Log;

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
            Log::warning('Failed login attempt - Email not found', [
                'email' => $request->email,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('auth.login')->withErrors(['email' => 'Email not found'])->withInput();
        }

        // Check if the account is active
        if ($user->status !== 'Active') {
            Log::warning('Login attempt for inactive account', [
                'email' => $request->email,
                'user_id' => $user->id,
                'status' => $user->status,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('auth.login')->withErrors(['email' => 'Account is inactive. Please contact support.'])->withInput();
        }

        // Log stored password hash
        Log::info('Stored Password Hash:', ['hashed_password' => $user->password]);

        // Check if the entered password matches the stored hash
        if (!Hash::check($request->password, $user->password)) {
            Log::warning('Failed login attempt - Incorrect password', [
                'email' => $request->email,
                'user_id' => $user->id,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('auth.login')->withErrors(['password' => 'Incorrect password'])->withInput();
        }

        // Check if the user has the "admin" role
        if ($user->role !== 'admin') {
            Log::warning('Unauthorized login attempt', [
                'email' => $request->email,
                'user_id' => $user->id,
                'role' => $user->role,
                'ip' => $request->ip(),
            ]);

            return redirect()->route('auth.login')->withErrors(['email' => 'Access denied. Only admins can log in.'])->withInput();
        }

        // Log in the user
        Auth::login($user, $request->filled('remember'));

        $request->session()->regenerate();

        Log::info('Admin logged in successfully', [
            'email' => $request->email,
            'user_id' => $user->id,
            'ip' => $request->ip(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Login successful');
    }


    /**
     * Handle logout.
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('auth.login')->with('success', 'Logged out successfully');
    }
}
