<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserHasFilamentAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // Check if user is authenticated and has admin or moderator role
        if (!$user || (!$user->hasRole('admin') && !$user->hasRole('moderator'))) {
            abort(403, 'Access denied. You do not have permission to access this area.');
        }
        
        return $next($request);
    }
}