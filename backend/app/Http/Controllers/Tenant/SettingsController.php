<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\UpdateBrandingRequest;
use App\Models\Tenant;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit(Tenant $tenant): View
    {
        $settings = $tenant->settings ?? [];

        return view('tenant.settings.edit', compact('tenant', 'settings'));
    }

    public function update(UpdateBrandingRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();
        $settings = $tenant->settings ?? [];

        if ($request->hasFile('appearance.logo')) {
            $path = $request->file('appearance.logo')->store("tenants/{$tenant->slug}", 'public');
            data_set($settings, 'appearance.logo', Storage::disk('public')->url($path));
        }

        if ($request->hasFile('appearance.favicon')) {
            $path = $request->file('appearance.favicon')->store("tenants/{$tenant->slug}", 'public');
            data_set($settings, 'appearance.favicon', Storage::disk('public')->url($path));
        }

        if (isset($data['brand'])) {
            data_set($settings, 'brand', $data['brand']);
        }

        if (isset($data['theme'])) {
            data_set($settings, 'theme', $data['theme']);
        }

        if (isset($data['footer'])) {
            data_set($settings, 'footer', $data['footer']);
        }

        $tenant->update([
            'settings' => $settings,
        ]);

        return Redirect::back()->with('status', __('Configuración guardada correctamente.'));
    }
}
