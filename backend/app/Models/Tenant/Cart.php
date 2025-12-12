<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'session_id',
        'customer_id',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getSubtotalAttribute(): float
    {
        return $this->items->sum(fn ($item) => $item->quantity * $item->unit_price);
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('quantity');
    }

    public function addProduct(Product $product, int $quantity = 1): CartItem
    {
        $item = $this->items()->where('product_id', $product->id)->first();

        if ($item) {
            $item->increment('quantity', $quantity);
            return $item->fresh();
        }

        return $this->items()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'unit_price' => $product->price,
        ]);
    }

    public function updateQuantity(int $productId, int $quantity): ?CartItem
    {
        $item = $this->items()->where('product_id', $productId)->first();

        if (!$item) {
            return null;
        }

        if ($quantity <= 0) {
            $item->delete();
            return null;
        }

        $item->update(['quantity' => $quantity]);
        return $item->fresh();
    }

    public function removeProduct(int $productId): bool
    {
        return $this->items()->where('product_id', $productId)->delete() > 0;
    }

    public function clear(): void
    {
        $this->items()->delete();
    }

    public static function getOrCreate(string $sessionId, ?int $customerId = null): self
    {
        return self::firstOrCreate(
            ['session_id' => $sessionId],
            ['customer_id' => $customerId]
        );
    }
}
