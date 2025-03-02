<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            if (auth()->user()->status !== 'Active') {
                Auth::logout(); // Logout user if status is inactive
                return redirect()->route('auth.login')->with('error', 'Your account is inactive. Please contact support.');
            }

            if (auth()->user()->role === 'admin') {
                return $next($request);
            }
        }

        return redirect()->route('auth.login')->with('error', 'Unauthorized Access');
    }
}
