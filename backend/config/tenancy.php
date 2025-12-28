<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Configuración de Tenancy
    |--------------------------------------------------------------------------
    |
    | Configuraciones para el sistema multitenancy.
    |
    */

    /**
     * Dominios centrales (admin, instalación, etc.)
     * Estos dominios no resolverán a ningún tenant.
     */
    'central_domains' => array_filter(array_map('trim', explode(',', env('CENTRAL_DOMAINS', 'admin.localhost,app.localhost')))),

    /**
     * Identificador de path para resolución por URL
     * Formato: domain.com/tenant/{tenant-slug}/...
     */
    'path_identifier' => env('TENANT_PATH_IDENTIFIER', 'tenant'),

    /**
     * Prefijo para bases de datos de tenants
     */
    'database_prefix' => env('TENANT_DATABASE_PREFIX', 'tenant_'),

    /**
     * Configuración de caché para resolución de tenants
     */
    'cache' => [
        'ttl' => env('TENANT_CACHE_TTL', 3600), // 1 hora
        'key_prefix' => env('TENANT_CACHE_PREFIX', 'tenant:'),
    ],

    /**
     * Configuración de migraciones de tenant
     */
    'migrations' => [
        'path' => database_path('tenant-migrations'),
        'table' => 'tenant_migrations',
    ],

    /**
     * Límites y restricciones
     */
    'limits' => [
        'slug_max_length' => 50,
        'domain_max_length' => 255,
        'name_max_length' => 255,
        'max_tenants_per_user' => env('MAX_TENANTS_PER_USER', 5),
    ],

    /**
     * Configuraciones por defecto para nuevos tenants
     */
    'defaults' => [
        'is_active' => true,
        'settings' => [
            'timezone' => 'America/Mexico_City',
            'currency' => 'MXN',
            'language' => 'es',
            'theme' => 'default',
        ],
        'database' => [
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', 3306),
            'username' => env('DB_USERNAME', 'root'),
            'password' => env('DB_PASSWORD', ''),
        ],
    ],

    /**
     * Configuración de subdominios
     */
    'subdomains' => [
        'reserved' => [
            'www', 'api', 'admin', 'app', 'mail', 
            'ftp', 'staging', 'test', 'dev', 'blog',
            'shop', 'store', 'dashboard', 'panel',
        ],
        'max_length' => 63, // RFC 1123
    ],

    /**
     * Features del sistema
     */
    'features' => [
        'auto_db_creation' => env('TENANT_AUTO_DB_CREATION', true),
        'auto_migrations' => env('TENANT_AUTO_MIGRATIONS', true),
        'domain_resolution' => env('TENANT_DOMAIN_RESOLUTION', true),
        'subdomain_resolution' => env('TENANT_SUBDOMAIN_RESOLUTION', true),
        'path_resolution' => env('TENANT_PATH_RESOLUTION', true),
        'header_resolution' => env('TENANT_HEADER_RESOLUTION', true),
    ],

    /**
     * Configuración de logging específico para tenancy
     */
    'logging' => [
        'enabled' => env('TENANT_LOGGING', true),
        'level' => env('TENANT_LOG_LEVEL', 'info'),
        'channel' => env('TENANT_LOG_CHANNEL', 'single'),
    ],
];
