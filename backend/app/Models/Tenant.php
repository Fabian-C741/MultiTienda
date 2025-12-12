<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database',
        'database_host',
        'database_port',
        'database_username',
        'database_password',
        'settings',
        'is_active',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    protected $hidden = [
        'database_password',
    ];

    /**
     * Usuarios asociados a este tenant.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Configuración de conexión a la base de datos del tenant.
     */
    public function databaseConfig(): array
    {
        return [
            'driver' => 'mysql',
            'host' => $this->database_host,
            'port' => $this->database_port,
            'database' => $this->database,
            'username' => $this->database_username,
            'password' => $this->database_password,
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
        ];
    }

    /**
     * Obtener una configuración específica del tenant.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return data_get($this->settings, $key, $default);
    }

    /**
     * Establecer una configuración específica del tenant.
     */
    public function setSetting(string $key, mixed $value): self
    {
        $settings = $this->settings ?? [];
        data_set($settings, $key, $value);
        $this->settings = $settings;

        return $this;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
