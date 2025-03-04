<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticatedFor2FA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if the user is authenticated
        if (Auth::check()) {
            // If authenticated, redirect to the dashboard or another route
            return redirect()->route('dashboard'); // Change 'dashboard' to your desired route
        }

        // Prevent guests from accessing the 2FA verification page
        if (!session()->has('2fa:user_id')) {
            return redirect()->route('auth.login')->with('error', 'Unauthorized access to 2FA verification.');
        }

        // Prevent users in the middle of 2FA from accessing the login page
        if ($request->routeIs('login') && session()->has('2fa:user_id')) {
            return redirect()->route('2fa.verify')->with('error', 'You must complete 2FA verification first.');
        }

        return $next($request);
    }
}
