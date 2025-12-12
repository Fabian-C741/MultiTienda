<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use App\Services\Tenancy\TenantResolver;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EnsureTenant
{
    public function __construct(
        protected TenantManager $tenantManager,
        protected TenantResolver $tenantResolver,
    ) {
    }

    public function handle(Request $request, Closure $next, string $mode = 'required')
    {
        if ($this->tenantManager->current()) {
            return $next($request);
        }

        $tenant = $this->tenantResolver->resolve($request);

        if (!$tenant) {
            if ($mode === 'optional') {
                return $next($request);
            }

            throw new NotFoundHttpException('Tenant not found');
        }

        if (!$tenant->is_active) {
            throw new HttpException(403, 'Tenant is inactive');
        }

        $this->tenantManager->setTenant($tenant);

        $request->attributes->set('tenant', $tenant);

        try {
            return $next($request);
        } finally {
            $this->tenantManager->forget();
        }
    }
}
