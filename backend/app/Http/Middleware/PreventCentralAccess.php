<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PreventCentralAccess
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * Este middleware previene el acceso a rutas que requieren 
     * estar en el contexto central (sin tenant).
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->manager->current();

        if ($tenant) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Acceso no permitido en contexto de tenant',
                    'message' => 'Esta operación solo está disponible en el contexto central',
                ], 403);
            }

            abort(403, 'Acceso no permitido en contexto de tenant');
        }

        return $next($request);
    }
}