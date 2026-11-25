<?php

use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Percayai proxy hanya dari IP yang dikonfigurasi via TRUSTED_PROXIES di .env.
        // Gunakan '*' hanya jika aplikasi berjalan di belakang load balancer cloud
        // yang IP-nya tidak tetap (mis. AWS ELB, Cloudflare). Untuk server dengan
        // IP proxy tetap, isi TRUSTED_PROXIES dengan IP spesifik (mis. "10.0.0.1").
        $middleware->trustProxies(
            at: $_ENV['TRUSTED_PROXIES'] ?? null,
            headers: Request::HEADER_X_FORWARDED_FOR |
                     Request::HEADER_X_FORWARDED_HOST |
                     Request::HEADER_X_FORWARDED_PORT |
                     Request::HEADER_X_FORWARDED_PROTO,
        );

        $middleware->alias([
            'role' => RoleMiddleware::class,
        ]);

        // Security headers untuk semua response
        $middleware->append(SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
