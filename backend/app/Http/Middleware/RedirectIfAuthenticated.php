<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectIfAuthenticated
{
    public function __construct(private readonly TenantManager $tenantManager)
    {
    }

    public function handle(Request $request, Closure $next, string ...$guards)
    {
        $guards = $guards === [] ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'tenant') {
                    $tenant = $this->tenantManager->current() ?? $request->route('tenant');

                    if ($tenant) {
                        return redirect()->route('tenant.dashboard', ['tenant' => $tenant]);
                    }

                    return redirect('/');
                }

                return redirect()->route('admin.dashboard');
            }
        }

        return $next($request);
    }
}
