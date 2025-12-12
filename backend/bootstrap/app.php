<?php

use App\Http\Middleware\EnsureTenant;
use App\Http\Middleware\EnsureSuperAdmin;
use App\Http\Middleware\EnsureTenantAdmin;
use App\Http\Middleware\Authenticate as AppAuthenticate;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'auth' => AppAuthenticate::class,
            'tenant' => EnsureTenant::class,
            'super-admin' => EnsureSuperAdmin::class,
            'tenant-admin' => EnsureTenantAdmin::class,
            'guest' => RedirectIfAuthenticated::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        ]);

        // Protección CSRF para formularios web
        $middleware->validateCsrfTokens(except: [
            'api/*',           // API usa tokens, no CSRF
            'webhook/*',       // Webhooks de pagos
        ]);

        // Headers de seguridad
        $middleware->append(\App\Http\Middleware\SecurityHeaders::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // No mostrar detalles de errores en producción
        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json(['error' => 'Recurso no encontrado'], 404);
            }
        });
    })->create();
