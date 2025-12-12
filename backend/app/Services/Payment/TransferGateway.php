<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Tenant\Order;

class TransferGateway extends PaymentGateway
{
    public function createPayment(Order $order): array
    {
        return [
            'success' => true,
            'payment_method' => 'transfer',
            'instructions' => $this->getTransferInstructions($order),
            'bank_details' => [
                'cbu' => $this->getCredential('cbu'),
                'alias' => $this->getCredential('alias'),
                'bank_name' => $this->getCredential('bank_name'),
                'account_holder' => $this->getCredential('account_holder'),
            ],
        ];
    }

    protected function getTransferInstructions(Order $order): string
    {
        $cbu = $this->getCredential('cbu');
        $alias = $this->getCredential('alias');
        $holder = $this->getCredential('account_holder');

        return <<<TEXT
Por favor realiza la transferencia con los siguientes datos:

CBU: {$cbu}
Alias: {$alias}
Titular: {$holder}

Monto: $ {$order->total}
Referencia: {$order->order_number}

Importante: Una vez realizada la transferencia, envíanos el comprobante a nuestro WhatsApp o email.
TEXT;
    }

    public function handleWebhook(array $payload): array
    {
        // Las transferencias se confirman manualmente
        return [
            'processed' => false,
            'reason' => 'Transfer confirmations are manual',
        ];
    }

    public function checkPaymentStatus(string $paymentId): array
    {
        return [
            'success' => false,
            'error' => 'Transfer payments are confirmed manually',
        ];
    }

    public function getPaymentUrl(array $paymentData): string
    {
        return ''; // No hay URL externa
    }
}
