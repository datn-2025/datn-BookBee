<?php

use Illuminate\Foundation\Application;
use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\RedirectIfAdminAuthenticated;
use App\Http\Middleware\BroadcastingMiddleware;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => 'broadcasting', 'middleware' => ['web', 'auth:admin']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'guest.admin' => RedirectIfAdminAuthenticated::class,
            'broadcasting.auth' => BroadcastingMiddleware::class,
        ]);
        $middleware->group('api', [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            // 'throttle:api',
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
