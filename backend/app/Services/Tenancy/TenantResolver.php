<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class TenantResolver
{
    public function resolve(Request $request): ?Tenant
    {
        $tenant = $this->byDomain($request);

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

        return Tenant::query()->where('domain', $host)->first();
    }

    protected function byHeader(Request $request): ?Tenant
    {
        $slug = $request->headers->get('X-Tenant');

        if (!$slug) {
            return null;
        }

        return Tenant::query()->where('slug', $slug)->first();
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
}
