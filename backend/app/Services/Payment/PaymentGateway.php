<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Tenant\Order;
use App\Models\Tenant\PaymentSetting;

abstract class PaymentGateway
{
    protected PaymentSetting $settings;

    public function __construct(PaymentSetting $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Crear una preferencia de pago / intención de pago.
     */
    abstract public function createPayment(Order $order): array;

    /**
     * Procesar el webhook/callback del gateway.
     */
    abstract public function handleWebhook(array $payload): array;

    /**
     * Verificar el estado de un pago.
     */
    abstract public function checkPaymentStatus(string $paymentId): array;

    /**
     * Obtener la URL de redirección para el pago.
     */
    abstract public function getPaymentUrl(array $paymentData): string;

    /**
     * Refund del pago.
     */
    public function refund(string $paymentId, ?float $amount = null): array
    {
        throw new \RuntimeException('Refund no soportado por este gateway');
    }

    protected function isSandbox(): bool
    {
        return $this->settings->is_sandbox;
    }

    protected function getCredential(string $key): ?string
    {
        return $this->settings->getCredential($key);
    }
}
