<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->isSuperAdmin()) {
            return Redirect::route('admin.dashboard');
        }

        return view('admin.auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return Redirect::back()->withErrors([
                'email' => __('auth.failed'),
            ])->withInput($request->only('email'));
        }

        $request->session()->regenerate();

        if (!Auth::user()?->isSuperAdmin()) {
            Auth::logout();

            return Redirect::route('admin.login')->withErrors([
                'email' => __('No tienes permisos para acceder al panel de super administrador.'),
            ]);
        }

        return Redirect::intended(route('admin.dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::route('admin.login');
    }
}
