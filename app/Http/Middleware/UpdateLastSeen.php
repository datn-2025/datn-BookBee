<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UpdateLastSeen
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Only update every 5 minutes to reduce database load
            $shouldUpdate = !$user->last_seen || $user->last_seen->diffInMinutes(now()) >= 5;
            
            if ($shouldUpdate) {
                $user->updateLastSeen();
            }
        }

        return $next($request);
    }
}
