<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class TenantUserAccess
{
    protected TenantManager $manager;

    public function __construct(TenantManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handle an incoming request.
     *
     * Este middleware verifica que el usuario autenticado 
     * pertenece al tenant actual.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $tenant = $this->manager->current();
        $user = Auth::user();

        if (!$tenant || !$user) {
            return $next($request);
        }

        // Verificar que el usuario pertenece al tenant actual
        if ($this->userBelongsToTenant($user, $tenant->id)) {
            return $next($request);
        }

        // Si el usuario es un super admin, permitir acceso
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        Auth::logout();

        if ($request->expectsJson()) {
            return response()->json([
                'error' => 'Acceso no autorizado',
                'message' => 'No tienes permisos para acceder a este tenant',
            ], 403);
        }

        return redirect()->route('login')->with('error', 'No tienes permisos para acceder a este tenant');
    }

    /**
     * Verifica si el usuario pertenece al tenant.
     */
    protected function userBelongsToTenant(User $user, int $tenantId): bool
    {
        // Usuario global (sin tenant_id) o del tenant específico
        return $user->tenant_id === null || $user->tenant_id === $tenantId;
    }
}