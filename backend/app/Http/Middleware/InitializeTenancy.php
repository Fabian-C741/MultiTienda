<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use App\Services\Tenancy\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class InitializeTenancy
{
    protected TenantResolver $resolver;
    protected TenantManager $manager;

    public function __construct(TenantResolver $resolver, TenantManager $manager)
    {
        $this->resolver = $resolver;
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si estamos en contexto global
        if ($this->resolver->isGlobalContext($request)) {
            Log::debug('Request en contexto global, omitiendo inicialización de tenant');
            return $next($request);
        }

        // Resolver el tenant
        $tenant = $this->resolver->resolve($request);

        if (!$tenant) {
            Log::warning('No se pudo resolver el tenant para la request', [
                'host' => $request->getHost(),
                'path' => $request->path(),
                'user_agent' => $request->userAgent(),
            ]);

            return $this->tenantNotFound($request);
        }

        // Verificar si el tenant está activo
        if (!$tenant->is_active) {
            Log::warning('Intento de acceso a tenant inactivo', [
                'tenant_id' => $tenant->id,
                'tenant_name' => $tenant->name,
                'host' => $request->getHost(),
            ]);

            return $this->tenantInactive($tenant);
        }

        // Establecer el tenant en el manager
        $this->manager->setTenant($tenant);

        Log::debug('Tenant inicializado', [
            'tenant_id' => $tenant->id,
            'tenant_name' => $tenant->name,
            'tenant_slug' => $tenant->slug,
        ]);

        $response = $next($request);

        // Limpiar el contexto del tenant al finalizar
        $this->manager->forget();

        return $response;
    }

    /**
     * Respuesta cuando no se encuentra el tenant.
     */
    protected function tenantNotFound(Request $request): Response
    {
        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Tenant no encontrado',
                'message' => 'No se pudo resolver el tenant para esta solicitud',
            ], 404);
        }

        return response()->view('errors.tenant-not-found', [
            'host' => $request->getHost(),
        ], 404);
    }

    /**
     * Respuesta cuando el tenant está inactivo.
     */
    protected function tenantInactive($tenant): Response
    {
        return response()->json([
            'error' => 'Tenant inactivo',
            'message' => 'Este tenant se encuentra temporalmente deshabilitado',
            'tenant' => $tenant->name,
        ], 503);
    }
}