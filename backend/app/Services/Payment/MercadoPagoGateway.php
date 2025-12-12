<?php

declare(strict_types=1);

namespace App\Services\Payment;

use App\Models\Tenant\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MercadoPagoGateway extends PaymentGateway
{
    protected function getBaseUrl(): string
    {
        return 'https://api.mercadopago.com';
    }

    protected function getAccessToken(): string
    {
        return $this->getCredential('access_token') ?? '';
    }

    public function createPayment(Order $order): array
    {
        $items = [];

        foreach ($order->items as $item) {
            $items[] = [
                'id' => (string) $item->product_id,
                'title' => $item->product_name,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'currency_id' => $order->currency,
            ];
        }

        $preferenceData = [
            'items' => $items,
            'payer' => [
                'name' => $order->customer_name,
                'email' => $order->customer_email,
            ],
            'external_reference' => $order->order_number,
            'notification_url' => route('api.webhooks.mercadopago'),
            'back_urls' => [
                'success' => route('storefront.checkout.success', ['order' => $order->order_number]),
                'failure' => route('storefront.checkout.failure', ['order' => $order->order_number]),
                'pending' => route('storefront.checkout.pending', ['order' => $order->order_number]),
            ],
            'auto_return' => 'approved',
            'statement_descriptor' => config('app.name'),
        ];

        if ($order->shipping_address) {
            $preferenceData['shipments'] = [
                'receiver_address' => [
                    'street_name' => $order->shipping_address,
                    'city_name' => $order->shipping_city,
                    'state_name' => $order->shipping_state,
                    'zip_code' => $order->shipping_postal_code,
                ],
            ];
        }

        try {
            $response = Http::withToken($this->getAccessToken())
                ->post("{$this->getBaseUrl()}/checkout/preferences", $preferenceData);

            if (!$response->successful()) {
                Log::error('MercadoPago createPayment error', [
                    'status' => $response->status(),
                    'body' => $response->json(),
                ]);
                
                throw new \RuntimeException('Error creando preferencia de MercadoPago: ' . $response->body());
            }

            $data = $response->json();

            return [
                'success' => true,
                'preference_id' => $data['id'],
                'init_point' => $this->isSandbox() ? $data['sandbox_init_point'] : $data['init_point'],
                'raw' => $data,
            ];

        } catch (\Exception $e) {
            Log::error('MercadoPago createPayment exception', [
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
        $type = $payload['type'] ?? null;
        $data = $payload['data'] ?? [];

        if ($type !== 'payment') {
            return ['processed' => false, 'reason' => 'Not a payment notification'];
        }

        $paymentId = $data['id'] ?? null;

        if (!$paymentId) {
            return ['processed' => false, 'reason' => 'No payment ID'];
        }

        $paymentStatus = $this->checkPaymentStatus($paymentId);

        if (!$paymentStatus['success']) {
            return ['processed' => false, 'reason' => $paymentStatus['error'] ?? 'Unknown error'];
        }

        return [
            'processed' => true,
            'payment_id' => $paymentId,
            'status' => $paymentStatus['status'],
            'external_reference' => $paymentStatus['external_reference'],
            'raw' => $paymentStatus['raw'],
        ];
    }

    public function checkPaymentStatus(string $paymentId): array
    {
        try {
            $response = Http::withToken($this->getAccessToken())
                ->get("{$this->getBaseUrl()}/v1/payments/{$paymentId}");

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Error consultando pago',
                ];
            }

            $data = $response->json();

            return [
                'success' => true,
                'payment_id' => $data['id'],
                'status' => $data['status'], // approved, pending, rejected, etc.
                'status_detail' => $data['status_detail'],
                'external_reference' => $data['external_reference'],
                'amount' => $data['transaction_amount'],
                'currency' => $data['currency_id'],
                'raw' => $data,
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getPaymentUrl(array $paymentData): string
    {
        return $paymentData['init_point'] ?? '';
    }

    public function refund(string $paymentId, ?float $amount = null): array
    {
        try {
            $body = $amount ? ['amount' => $amount] : [];

            $response = Http::withToken($this->getAccessToken())
                ->post("{$this->getBaseUrl()}/v1/payments/{$paymentId}/refunds", $body);

            if (!$response->successful()) {
                return [
                    'success' => false,
                    'error' => 'Error procesando reembolso',
                ];
            }

            return [
                'success' => true,
                'refund_id' => $response->json('id'),
                'raw' => $response->json(),
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}
