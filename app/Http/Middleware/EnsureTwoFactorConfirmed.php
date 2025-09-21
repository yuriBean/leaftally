<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureTwoFactorConfirmed
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user) {
            return $next($request);
        }

        // Only for main 'users' guard (not customers/vendors)
        if (auth()->getDefaultDriver() !== 'web') {
            return $next($request);
        }
        if ($request->user()->two_factor_secret === null) {
            return $next($request); // 2FA not enabled
        }

        // Remembered device cookie?
        $rememberCookie = "2fa_remember_{$user->id}";
        if ($request->cookies->get($rememberCookie) === '1') {
            return $next($request);
        }

        // Session flag after challenge
        if ($request->session()->get('2fa_passed') === true) {
            return $next($request);
        }

        // Allow the challenge pages themselves
        if ($request->routeIs('2fa.challenge.show') || $request->routeIs('2fa.challenge.confirm')) {
            return $next($request);
        }

        return redirect()->route('2fa.challenge.show');
    }
}
