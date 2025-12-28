<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EnsureTenantContext
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * Este middleware asegura que existe un contexto de tenant activo.
     * Se ejecuta después de InitializeTenancy.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->manager->current();

        if (!$tenant) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Contexto de tenant requerido',
                    'message' => 'Esta operación requiere un contexto de tenant válido',
                ], 400);
            }

            abort(400, 'Contexto de tenant requerido');
        }

        return $next($request);
    }
}