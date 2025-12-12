<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Tenant\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UalaGateway extends PaymentGateway
{
    protected function getBaseUrl(): string
    {
        return $this->isSandbox()
            ? 'https://checkout.stage.ua.la/1/checkout'
            : 'https://checkout.prod.ua.la/1/checkout';
    }

    protected function getApiKey(): string
    {
        return $this->getCredential('api_key') ?? '';
    }

    protected function getUsername(): string
    {
        return $this->getCredential('username') ?? '';
    }

    public function createPayment(Order $order): array
    {
        $checkoutData = [
            'userName' => $this->getUsername(),
            'amount' => (string) number_format((float) $order->total, 2, '.', ''),
            'description' => "Pedido {$order->order_number}",
            'external_reference' => $order->order_number,
            'callback_fail' => route('storefront.checkout.failure', ['order' => $order->order_number]),
            'callback_success' => route('storefront.checkout.success', ['order' => $order->order_number]),
            'notification_url' => route('api.webhooks.uala'),
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer {$this->getApiKey()}",
                'Content-Type' => 'application/json',
            ])->post($this->getBaseUrl(), $checkoutData);

            if (!$response->successful()) {
                Log::error('Ualá createPayment error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);

                throw new \RuntimeException('Error creando checkout de Ualá: ' . $response->body());
            }

            $data = $response->json();

            return [
                'success' => true,
                'checkout_id' => $data['uuid'] ?? null,
                'checkout_url' => $data['links']['checkout_link'] ?? null,
                'raw' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('Ualá createPayment exception', [
                'message' => $e->getMessage(),
                'order' => $order->order_number,
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function handleWebhook(array $payload): array
    {
        $status = $payload['status'] ?? null;
        $externalReference = $payload['external_reference'] ?? null;
        $uuid = $payload['uuid'] ?? null;

        if (!$externalReference) {
            return ['processed' => false, 'reason' => 'No external reference'];
        }

        // Mapear estados de Ualá
        $mappedStatus = match ($status) {
            'APPROVED' => 'approved',
            'REJECTED' => 'rejected',
            'PENDING' => 'pending',
            default => $status,
        };

        return [
            'processed' => true,
            'payment_id' => $uuid,
            'status' => $mappedStatus,
            'external_reference' => $externalReference,
            'raw' => $payload,
        ];
    }

    public function checkPaymentStatus(string $paymentId): array
    {
        // Ualá no tiene endpoint público para verificar estado
        // El estado se recibe vía webhook
        return [
            'success' => false,
            'error' => 'Ualá no soporta verificación de estado. Use webhooks.',
        ];
    }

    public function getPaymentUrl(array $paymentData): string
    {
        return $paymentData['checkout_url'] ?? '';
    }
}
