<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class BroadcastingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        Log::info('Broadcasting middleware called', [
            'url' => $request->url(),
            'method' => $request->method(),
            'headers' => $request->headers->all(),
            'session_id' => $request->session()->getId(),
        ]);

        // Try to get user from admin guard first
        $user = Auth::guard('admin')->user();
        $guard = 'admin';
        
        // If no admin user, try web guard
        if (!$user) {
            $user = Auth::guard('web')->user();
            $guard = 'web';
        }
        
        // If still no user, check if it's a public channel
        if (!$user) {
            Log::error('Broadcasting auth failed: No user authenticated', [
                'admin_check' => Auth::guard('admin')->check(),
                'web_check' => Auth::guard('web')->check(),
                'session_data' => $request->session()->all()
            ]);
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        Log::info('Broadcasting auth successful', [
            'user_id' => $user->id,
            'user_name' => $user->name,
            'guard' => $guard,
            'channel' => $request->input('channel_name')
        ]);

        return $next($request);
    }
} 