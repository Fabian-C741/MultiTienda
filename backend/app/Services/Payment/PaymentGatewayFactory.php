<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Tenant\PaymentSetting;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * Crear instancia del gateway según el tipo.
     */
    public static function make(PaymentSetting $settings): PaymentGateway
    {
        return match ($settings->gateway) {
            PaymentSetting::GATEWAY_MERCADOPAGO => new MercadoPagoGateway($settings),
            PaymentSetting::GATEWAY_UALA => new UalaGateway($settings),
            PaymentSetting::GATEWAY_TRANSFER => new TransferGateway($settings),
            PaymentSetting::GATEWAY_CASH => new CashGateway($settings),
            default => throw new InvalidArgumentException("Gateway [{$settings->gateway}] no soportado"),
        };
    }

    /**
     * Crear instancia por nombre de gateway.
     */
    public static function makeByName(string $gatewayName): PaymentGateway
    {
        $settings = PaymentSetting::where('gateway', $gatewayName)
            ->where('is_active', true)
            ->firstOrFail();

        return self::make($settings);
    }

    /**
     * Obtener todos los gateways activos.
     */
    public static function getActiveGateways(): array
    {
        $settings = PaymentSetting::active()->ordered()->get();
        $gateways = [];

        foreach ($settings as $setting) {
            try {
                $gateways[$setting->gateway] = [
                    'instance' => self::make($setting),
                    'settings' => $setting,
                ];
            } catch (\Exception $e) {
                // Skip invalid gateways
            }
        }

        return $gateways;
    }
}
