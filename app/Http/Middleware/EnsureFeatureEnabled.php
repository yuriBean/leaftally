<?php

namespace App\Http\Middleware;

use App\Services\Feature;
use Closure;
use Illuminate\Http\Request;

class EnsureFeatureEnabled
{
    /**
     * Only enforce for the "web" guard (users table).
     * Customers/vendors (other guards) pass through untouched.
     */
    public function handle(Request $request, Closure $next, string $featureKey)
    {
        if (! auth('web')->check()) {
            return $next($request);
        }

        $user = auth('web')->user();
        if (! $user) {
            return $next($request);
        }

        if (! Feature::for($user)->enabled($featureKey)) {
            abort(403, __('This module is not available on your plan.'));
        }

        return $next($request);
    }
}
