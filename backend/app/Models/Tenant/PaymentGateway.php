<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Crypt;

class PaymentGateway extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'name',
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
        'settings' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Encriptar/desencriptar credenciales automáticamente.
     */
    protected function credentials(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? json_decode(Crypt::decryptString($value), true) : [],
            set: fn ($value) => $value ? Crypt::encryptString(json_encode($value)) : null,
        );
    }

    public function getCredential(string $key, mixed $default = null): mixed
    {
        return data_get($this->credentials, $key, $default);
    }

    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public static function getMercadoPago(): ?self
    {
        return static::where('name', 'mercadopago')->first();
    }

    public static function getUala(): ?self
    {
        return static::where('name', 'uala')->first();
    }
}
