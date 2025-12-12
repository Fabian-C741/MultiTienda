<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class EnsureSuperAdmin
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if (!$user) {
            return Redirect::route('admin.login');
        }

        if (!$user->isSuperAdmin()) {
            throw new AccessDeniedHttpException('Acceso restringido al super administrador.');
        }

        return $next($request);
    }
}
