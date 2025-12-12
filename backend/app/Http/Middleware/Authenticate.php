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

        if ($request->routeIs('tenant.*')) {
            $tenant = $request->route('tenant');

            return $tenant ? route('tenant.login.show', ['tenant' => $tenant]) : url('/');
        }

        return route('admin.login');
    }
}
