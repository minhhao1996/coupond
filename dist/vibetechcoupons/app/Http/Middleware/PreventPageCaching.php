<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class PreventPageCaching
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // HTML contains session-specific CSRF tokens, including public coupon pages.
        if (str_contains((string) $response->headers->get('Content-Type'), 'text/html')) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
            $response->headers->set('X-LiteSpeed-Cache-Control', 'no-cache');
        }

        return $response;
    }
}
