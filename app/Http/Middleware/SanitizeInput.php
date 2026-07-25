<?php

// app/Http/Middleware/SanitizeInput.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SanitizeInput
{
    /**
     * Strip dangerous content from all incoming request data.
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->routeIs('livewire.update') || $request->header('X-Livewire')) {
            return $next($request);
        }
        $input = $request->all();

        array_walk_recursive($input, function (&$value, $key) {
            if (!is_string($value)) {
                return;
            }
            $value = str_replace(chr(0), '', $value);
            $value = preg_replace('/on\w+\s*=/i', '', $value);
            $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);
            $value = str_replace(['--', '/*', '*/'], '', $value);
            $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        });


        $request->merge($input);

        return $next($request);
    }
}
