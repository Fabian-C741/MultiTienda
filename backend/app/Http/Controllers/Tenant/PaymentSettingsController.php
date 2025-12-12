<?php

declare(strict_types=1);

namespace App\Http\Controllers\Tenant;

use App\Http\Controllers\Controller;
use App\Models\Tenant\PaymentSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class PaymentSettingsController extends Controller
{
    public function index(): View
    {
        $availableGateways = PaymentSetting::availableGateways();
        $configuredGateways = PaymentSetting::all()->keyBy('gateway');

        return view('tenant.payments.index', compact('availableGateways', 'configuredGateways'));
    }

    public function edit(string $gateway): View
    {
        $availableGateways = PaymentSetting::availableGateways();

        if (!isset($availableGateways[$gateway])) {
            abort(404, 'Gateway no encontrado');
        }

        $gatewayInfo = $availableGateways[$gateway];
        $settings = PaymentSetting::firstOrNew(['gateway' => $gateway], [
            'display_name' => $gatewayInfo['name'],
            'description' => $gatewayInfo['description'],
        ]);

        return view('tenant.payments.edit', compact('gateway', 'gatewayInfo', 'settings'));
    }

    public function update(Request $request, string $gateway): RedirectResponse
    {
        $availableGateways = PaymentSetting::availableGateways();

        if (!isset($availableGateways[$gateway])) {
            abort(404, 'Gateway no encontrado');
        }

        $gatewayInfo = $availableGateways[$gateway];

        $rules = [
            'display_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['nullable', 'boolean'],
            'is_sandbox' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];

        // Agregar reglas para campos de credenciales
        foreach ($gatewayInfo['fields'] as $field) {
            $rules["credentials.{$field}"] = ['nullable', 'string', 'max:500'];
        }

        $validated = $request->validate($rules);

        $settings = PaymentSetting::updateOrCreate(
            ['gateway' => $gateway],
            [
                'display_name' => $validated['display_name'],
                'description' => $validated['description'] ?? null,
                'is_active' => $request->boolean('is_active'),
                'is_sandbox' => $request->boolean('is_sandbox', true),
                'credentials' => $validated['credentials'] ?? [],
                'sort_order' => $validated['sort_order'] ?? 0,
            ]
        );

        return Redirect::route('tenant.payments.index')
            ->with('status', "Configuración de {$gatewayInfo['name']} guardada.");
    }
}
