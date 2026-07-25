<?php
// app/Http/Middleware/SecurityHeaders.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // 1. Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 2. Prevent clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 3. Enable browser XSS filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Referrer policy — only send referrer for same-origin
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. Permissions Policy — restrict sensitive browser features
        $response->headers->set('Permissions-Policy',
            'camera=(self), ' .
            'geolocation=(self), ' .
            'microphone=(), ' .
            'payment=(), ' .
            'usb=()'
        );

// 6. Content Security Policy (CSP)
if (app()->environment('local', 'development')) {
    // Permissive for development – allows Vite, Bunny Fonts, and all local assets
    $csp = "default-src 'self' http://localhost:8000 http://[::1]:8000; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://localhost:8000 http://[::1]:8000; " .
           "style-src 'self' 'unsafe-inline' https://fonts.bunny.net http://localhost:8000 http://[::1]:8000; " .
           "font-src 'self' https://fonts.bunny.net; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self' ws: wss: http://localhost:8000 http://[::1]:8000; " .
           "frame-src 'self'; " .
           "object-src 'none'; " .
           "base-uri 'self'; " .
           "form-action 'self';";
} else {
    // Strict for production
    $csp = "default-src 'self'; " .
           "script-src 'self' 'unsafe-inline' 'unsafe-eval'; " .
           "style-src 'self' 'unsafe-inline' https://fonts.bunny.net; " .
           "font-src 'self' https://fonts.bunny.net; " .
           "img-src 'self' data: https:; " .
           "connect-src 'self' ws: wss:; " .
           "frame-src 'self'; " .
           "object-src 'none'; " .
           "base-uri 'self'; " .
           "form-action 'self';";
}

$response->headers->set('Content-Security-Policy', $csp);

        $response->headers->set('Content-Security-Policy', $csp);

        // 7. HSTS (only in production, force HTTPS)
        if (app()->environment('production')) {
            $response->headers->set('Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload');
        }

        // 8. Remove server identity
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        return $response;
    }
}