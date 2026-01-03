<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }

        // Verificar si es una ruta de tenant y tiene el parámetro
        if ($request->routeIs('tenant.*')) {
            $tenant = $request->route('tenant');
            
            if ($tenant) {
                // Si tenemos el tenant, redirigir al login del tenant
                return route('tenant.login.show', ['tenant' => $tenant]);
            }
        }

        // Para rutas admin o cuando no hay tenant válido
        if ($request->routeIs('admin.*') || $request->is('admin/*')) {
            return route('admin.login');
        }

        // Fallback a la página principal
        return url('/');
    }
}
