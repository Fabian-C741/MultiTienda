<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;

class TenantResolver
{
    protected static array $resolvers = [];

    /**
     * Registra un resolver personalizado.
     */
    public static function resolveUsing(string $name, Closure $resolver): void
    {
        static::$resolvers[$name] = $resolver;
    }

    public function resolve(Request $request): ?Tenant
    {
        // Primero intentar resolvers personalizados
        foreach (static::$resolvers as $name => $resolver) {
            $tenant = $resolver($request);
            if ($tenant instanceof Tenant) {
                Log::debug("Tenant resuelto usando resolver personalizado: {$name}");
                return $tenant;
            }
        }

        $tenant = $this->byDomain($request);

        if ($tenant) {
            return $tenant;
        }

        $tenant = $this->bySubdomain($request);

        if ($tenant) {
            return $tenant;
        }

        $tenant = $this->byHeader($request);

        if ($tenant) {
            return $tenant;
        }

        return $this->byRouteOrSegment($request);
    }

    protected function byDomain(Request $request): ?Tenant
    {
        $host = $request->getHost();

        if (!$host) {
            return null;
        }

        $centralDomains = Config::get('tenancy.central_domains', []);

        if (in_array($host, $centralDomains, true)) {
            return null;
        }

        return $this->findTenantWithCache('domain', $host);
    }

    /**
     * Resuelve tenant por subdominio.
     * Formato esperado: {slug}.dominio.com
     */
    protected function bySubdomain(Request $request): ?Tenant
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        if (count($parts) < 2) {
            return null;
        }

        $subdomain = $parts[0];

        // Ignorar subdominios especiales
        if (in_array($subdomain, ['www', 'api', 'admin', 'app'])) {
            return null;
        }

        return $this->findTenantWithCache('slug', $subdomain);
    }

    protected function byHeader(Request $request): ?Tenant
    {
        $slug = $request->headers->get('X-Tenant');
        $tenantId = $request->header('X-Tenant-ID');

        if ($tenantId) {
            return $this->findTenantWithCache('id', $tenantId);
        }

        if (!$slug) {
            return null;
        }

        return $this->findTenantWithCache('slug', $slug);
    }

    protected function byRouteOrSegment(Request $request): ?Tenant
    {
        $routeParam = $request->route('tenant');

        if ($routeParam instanceof Tenant) {
            return $routeParam;
        }

        if (is_string($routeParam)) {
            $tenant = Tenant::query()->where('slug', $routeParam)->first();

            if ($tenant) {
                return $tenant;
            }
        }

        $pathIdentifier = Config::get('tenancy.path_identifier');

        if ($pathIdentifier && $request->segment(1) === $pathIdentifier) {
            $slug = $request->segment(2);
        } else {
            $slug = $request->route('tenant_slug') ?? $request->segment(1);
        }

        if (!$slug) {
            return null;
        }

        return Tenant::query()->where('slug', $slug)->first();
    }

    /**
     * Busca un tenant con caché para mejorar rendimiento.
     */
    protected function findTenantWithCache(string $field, string $value): ?Tenant
    {
        $cacheKey = "tenant:{$field}:{$value}";

        return Cache::remember($cacheKey, 3600, function () use ($field, $value) {
            return Tenant::where($field, $value)
                ->where('is_active', true)
                ->first();
        });
    }

    /**
     * Invalida el caché de un tenant específico.
     */
    public function clearTenantCache(Tenant $tenant): void
    {
        $cacheKeys = [
            "tenant:id:{$tenant->id}",
            "tenant:slug:{$tenant->slug}",
        ];

        if ($tenant->domain) {
            $cacheKeys[] = "tenant:domain:{$tenant->domain}";
        }

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        Log::info("Caché limpiado para tenant: {$tenant->name}");
    }

    /**
     * Resuelve tenant por slug explícito (útil para comandos).
     */
    public function resolveBySlug(string $slug): ?Tenant
    {
        return $this->findTenantWithCache('slug', $slug);
    }

    /**
     * Resuelve tenant por ID explícito.
     */
    public function resolveById(int $tenantId): ?Tenant
    {
        return $this->findTenantWithCache('id', (string)$tenantId);
    }

    /**
     * Verifica si una request pertenece al contexto global (no-tenant).
     */
    public function isGlobalContext(Request $request): bool
    {
        $host = $request->getHost();
        $path = $request->path();

        // Rutas globales (admin, instalación, etc.)
        $globalPaths = ['admin', 'install', 'health', 'metrics'];
        
        foreach ($globalPaths as $globalPath) {
            if (str_starts_with($path, $globalPath)) {
                return true;
            }
        }

        // Subdominios globales
        $parts = explode('.', $host);
        if (count($parts) > 0 && in_array($parts[0], ['admin', 'app'])) {
            return true;
        }

        return false;
    }
}
