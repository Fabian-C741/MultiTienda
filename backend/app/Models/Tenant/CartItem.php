<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Models\Product;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    protected $connection = 'tenant';

    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getSubtotalAttribute($value): float
    {
        // Si hay un valor guardado en BD, usarlo
        if ($value !== null) {
            return (float) $value;
        }
        // Si no, calcularlo
        return $this->quantity * $this->unit_price;
    }
}
