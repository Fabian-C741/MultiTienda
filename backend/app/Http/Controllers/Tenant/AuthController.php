<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function show(Tenant $tenant): View|RedirectResponse
    {
        $guard = Auth::guard('tenant');

        if ($guard->check() && $guard->user()->isTenantAdminFor($tenant)) {
            return Redirect::route('tenant.dashboard', ['tenant' => $tenant]);
        }

        return view('tenant.auth.login', compact('tenant'));
    }

    public function authenticate(Request $request, Tenant $tenant): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        $credentials['tenant_id'] = $tenant->getKey();

        if (!Auth::guard('tenant')->attempt($credentials, $remember)) {
            return Redirect::back()->withErrors([
                'email' => __('Credenciales inválidas para esta tienda.'),
            ])->withInput(
                $request->only('email', 'remember')
            );
        }

        $request->session()->regenerate();

        if (!Auth::guard('tenant')->user()->isTenantAdminFor($tenant)) {
            Auth::guard('tenant')->logout();

            return Redirect::route('tenant.login.show', ['tenant' => $tenant])
                ->withErrors(['email' => __('No tienes permisos para administrar esta tienda.')]);
        }

        return Redirect::route('tenant.dashboard', ['tenant' => $tenant]);
    }

    public function logout(Request $request, Tenant $tenant): RedirectResponse
    {
        Auth::guard('tenant')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('tenant.login.show', ['tenant' => $tenant]);
    }
}
