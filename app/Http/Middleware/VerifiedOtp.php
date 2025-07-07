<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifiedOtp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has verified their WhatsApp number
        if (!$user->whatsapp_verified_at) {
            // Redirect to OTP verification page
            return redirect()->route('verify-otp')
                ->with('message', 'Please verify your WhatsApp number to continue.');
        }
        
        return $next($request);
    }
}