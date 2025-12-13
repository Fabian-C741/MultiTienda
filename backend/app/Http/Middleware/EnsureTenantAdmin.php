<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureTenantAdmin
{
    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $guard = Auth::guard('tenant');

        if (!$guard->check()) {
            $tenant = $this->tenantManager->current();
            $slug = $tenant ? $tenant->slug : $request->route('tenant');

            return Redirect::route('tenant.login.show', ['tenant' => $slug]);
        }

        $user = $guard->user();
        $tenant = $this->tenantManager->current();

        if (!$tenant || !$user->isTenantAdminFor($tenant)) {
            throw new AccessDeniedHttpException('Acceso restringido al panel de la tienda.');
        }

        return $next($request);
    }
}
