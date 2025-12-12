<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Tenant\Order;

class CashGateway extends PaymentGateway
{
    public function createPayment(Order $order): array
    {
        return [
            'success' => true,
            'payment_method' => 'cash',
            'instructions' => $this->getCashInstructions($order),
        ];
    }

    protected function getCashInstructions(Order $order): string
    {
        return <<<TEXT
Pago en efectivo al momento de la entrega.

Monto a pagar: $ {$order->total}
Número de pedido: {$order->order_number}

Por favor tenga el monto exacto preparado.
TEXT;
    }

    public function handleWebhook(array $payload): array
    {
        return [
            'processed' => false,
            'reason' => 'Cash payments are confirmed manually',
        ];
    }

    public function checkPaymentStatus(string $paymentId): array
    {
        return [
            'success' => false,
            'error' => 'Cash payments are confirmed manually',
        ];
    }

    public function getPaymentUrl(array $paymentData): string
    {
        return '';
    }
}
