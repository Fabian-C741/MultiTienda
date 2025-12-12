<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $connection = 'tenant';

    protected $table = 'media';

    protected $fillable = [
        'disk',
        'path',
        'url',
        'filename',
        'original_name',
        'mime_type',
        'size',
        'type',
        'collection',
        'mediable_type',
        'mediable_id',
        'alt_text',
        'title',
        'metadata',
        'sort_order',
    ];

    protected $casts = [
        'size' => 'integer',
        'metadata' => 'array',
        'sort_order' => 'integer',
    ];

    protected $appends = ['human_size'];

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute($value): string
    {
        // Si hay URL guardada directamente, usarla
        if ($value) {
            return $value;
        }
        
        // Si no, generarla desde el path
        $disk = $this->disk ?? 'public';
        return Storage::disk($disk)->url($this->path);
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size ?? 0;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getAltAttribute(): ?string
    {
        return data_get($this->metadata, 'alt');
    }

    public function getTitleAttribute(): ?string
    {
        return data_get($this->metadata, 'title');
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    public function delete(): bool
    {
        // Eliminar archivo físico
        Storage::disk($this->disk)->delete($this->path);

        return parent::delete();
    }

    public function scopeCollection($query, string $collection)
    {
        return $query->where('collection', $collection);
    }

    public function scopeImages($query)
    {
        return $query->where('mime_type', 'like', 'image/%');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }
}
