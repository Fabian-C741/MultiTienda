<?php

declare(strict_types=1);

namespace App\Models;

use App\Models\Tenant\Category;
use App\Models\Tenant\Media;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends TenantModel
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'sku',
        'category_id',
        'description',
        'price',
        'compare_price',
        'stock',
        'status',
        'is_featured',
        'image_url',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'compare_price' => 'decimal:2',
        'stock' => 'integer',
        'is_featured' => 'boolean',
        'metadata' => 'array',
    ];

    /**
     * Categoría principal del producto.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Categorías adicionales (muchos a muchos).
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    /**
     * Imágenes del producto.
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->ordered();
    }

    /**
     * Imagen principal.
     */
    public function getPrimaryImageAttribute(): ?string
    {
        if ($this->image_url) {
            return $this->image_url;
        }

        return $this->media->first()?->url;
    }

    public function isInStock(): bool
    {
        return $this->stock > 0;
    }

    public function decrementStock(int $quantity = 1): void
    {
        $this->decrement('stock', $quantity);
    }

    public function incrementStock(int $quantity = 1): void
    {
        $this->increment('stock', $quantity);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function scopeByCategory($query, int $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
