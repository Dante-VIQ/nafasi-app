<?php

namespace App\Http\Middleware;

use Closure;

class RequireTwoFactorAuth
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();
        if ($user && $user->two_factor_enabled && !session('two_factor_verified')) {
            return redirect()->route('two-factor.challenge');
        }
        return $next($request);
    }
}