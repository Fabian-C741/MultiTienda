<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'order_number',
        'customer_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'subtotal',
        'tax',
        'shipping_cost',
        'discount',
        'total',
        'currency',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'shipping_city',
        'shipping_state',
        'shipping_postal_code',
        'shipping_country',
        'notes',
        'metadata',
        'paid_at',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax' => 'decimal:2',
        'shipping_cost' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2',
        'metadata' => 'array',
        'paid_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $order) {
            if (empty($order->order_number)) {
                $order->order_number = self::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        $prefix = date('Ymd');
        $random = strtoupper(Str::random(6));
        return "{$prefix}-{$random}";
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_PROCESSING]);
    }

    public function markAsPaid(string $paymentReference = null): void
    {
        $this->update([
            'payment_status' => self::PAYMENT_PAID,
            'status' => self::STATUS_PAID,
            'payment_reference' => $paymentReference,
            'paid_at' => now(),
        ]);
    }

    public function markAsShipped(): void
    {
        $this->update([
            'status' => self::STATUS_SHIPPED,
            'shipped_at' => now(),
        ]);
    }

    public function markAsDelivered(): void
    {
        $this->update([
            'status' => self::STATUS_DELIVERED,
            'delivered_at' => now(),
        ]);
    }

    public function cancel(): void
    {
        $this->update([
            'status' => self::STATUS_CANCELLED,
        ]);
    }

    public static function createFromCart(Cart $cart, array $customerData): self
    {
        $subtotal = $cart->subtotal;
        $tax = 0; // Configurable por tenant
        $shipping = 0; // Configurable
        $discount = 0;
        $total = $subtotal + $tax + $shipping - $discount;

        $order = self::create([
            'customer_id' => $cart->customer_id,
            'customer_name' => $customerData['name'],
            'customer_email' => $customerData['email'],
            'customer_phone' => $customerData['phone'] ?? null,
            'shipping_address' => $customerData['address'] ?? null,
            'shipping_city' => $customerData['city'] ?? null,
            'shipping_state' => $customerData['state'] ?? null,
            'shipping_postal_code' => $customerData['postal_code'] ?? null,
            'shipping_country' => $customerData['country'] ?? 'Argentina',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'shipping_cost' => $shipping,
            'discount' => $discount,
            'total' => $total,
            'notes' => $customerData['notes'] ?? null,
        ]);

        foreach ($cart->items as $item) {
            $order->items()->create([
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'product_sku' => $item->product->sku ?? null,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'subtotal' => $item->subtotal,
            ]);
        }

        return $order;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopePaid($query)
    {
        return $query->where('payment_status', self::PAYMENT_PAID);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING => 'Pendiente',
            self::STATUS_PROCESSING => 'Procesando',
            self::STATUS_PAID => 'Pagado',
            self::STATUS_SHIPPED => 'Enviado',
            self::STATUS_DELIVERED => 'Entregado',
            self::STATUS_CANCELLED => 'Cancelado',
            default => $this->status,
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PENDING => 'Pendiente',
            self::PAYMENT_PAID => 'Pagado',
            self::PAYMENT_FAILED => 'Fallido',
            self::PAYMENT_REFUNDED => 'Reembolsado',
            default => $this->payment_status,
        };
    }
}
