<?php

use App\Http\Middleware\CacheHeaders;
use App\Http\Middleware\EnsureAdmin;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(
            fn (Request $request): string => route('admin.login'),
        );
        $middleware->alias([
            'admin' => EnsureAdmin::class,
            'public.security' => SecurityHeaders::class,
        ]);
        $middleware->append(CacheHeaders::class);
        // ponytail: '*' trusts any proxy — cukup untuk tunnel dev (trycloudflare);
        // di produksi pin IP proxy cloudflared/reverse-proxy agar header tidak bisa dispoof.
        $middleware->trustProxies(at: '*');
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );

        $exceptions->renderable(function (TokenMismatchException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Sesi telah habis. Muat ulang halaman dan coba lagi.'], 419);
            }

            return response()->view('errors.419', [], 419);
        });

        $exceptions->renderable(function (ThrottleRequestsException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Terlalu banyak permintaan. Silakan tunggu beberapa saat.'], 429);
            }

            return response()->view('errors.429', [], 429);
        });
    })->create();
