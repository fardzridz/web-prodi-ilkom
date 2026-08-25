<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        $viteDev = app()->environment('local') && Vite::isRunningHot();

        $styleSrc = "'self' https://fonts.googleapis.com";
        $scriptSrc = "'self' https://cdn.jsdelivr.net";
        $connectSrc = "'self' https://cdn.jsdelivr.net";
        $fontSrc = "'self' https://fonts.gstatic.com";

        if ($viteDev) {
            $styleSrc .= ' http://localhost:*';
            $scriptSrc .= ' http://localhost:*';
            $connectSrc .= ' ws://localhost:* http://localhost:*';
        }

        $csp = "default-src 'self'; "
            ."script-src {$scriptSrc} 'unsafe-inline' 'unsafe-eval'; "
            ."style-src {$styleSrc} 'unsafe-inline'; "
            ."img-src 'self' data: https:; "
            ."font-src {$fontSrc}; "
            ."frame-src 'self' https://www.google.com https://maps.google.com; "
            ."connect-src {$connectSrc}; "
            ."frame-ancestors 'self'; "
            ."base-uri 'self'; "
            ."form-action 'self'";

        $response->headers->set('Content-Security-Policy', $csp);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        return $response;
    }
}
