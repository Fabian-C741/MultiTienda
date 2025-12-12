<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PaymentSetting extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'gateway',
        'display_name',
        'description',
        'is_active',
        'is_sandbox',
        'credentials',
        'settings',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_sandbox' => 'boolean',
        'credentials' => 'encrypted:array',
        'settings' => 'array',
        'sort_order' => 'integer',
    ];

    protected $hidden = [
        'credentials',
    ];

    public const GATEWAY_MERCADOPAGO = 'mercadopago';
    public const GATEWAY_UALA = 'uala';
    public const GATEWAY_TRANSFER = 'transfer';
    public const GATEWAY_CASH = 'cash';

    public static function availableGateways(): array
    {
        return [
            self::GATEWAY_MERCADOPAGO => [
                'name' => 'Mercado Pago',
                'description' => 'Pagos con tarjeta, transferencia y efectivo',
                'fields' => ['access_token', 'public_key'],
            ],
            self::GATEWAY_UALA => [
                'name' => 'Ualá Bis',
                'description' => 'Pagos con Ualá',
                'fields' => ['api_key', 'username'],
            ],
            self::GATEWAY_TRANSFER => [
                'name' => 'Transferencia Bancaria',
                'description' => 'Pago por transferencia manual',
                'fields' => ['cbu', 'alias', 'bank_name', 'account_holder'],
            ],
            self::GATEWAY_CASH => [
                'name' => 'Efectivo',
                'description' => 'Pago en efectivo al recibir',
                'fields' => [],
            ],
        ];
    }

    public function getCredential(string $key): ?string
    {
        return $this->credentials[$key] ?? null;
    }

    public function setCredential(string $key, string $value): self
    {
        $credentials = $this->credentials ?? [];
        $credentials[$key] = $value;
        $this->credentials = $credentials;
        return $this;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return self::active()->ordered()->get();
    }

    public static function findByGateway(string $gateway): ?self
    {
        return self::where('gateway', $gateway)->first();
    }
}
