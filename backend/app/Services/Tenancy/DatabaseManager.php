<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Models\Tenant;
use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager as IlluminateDatabaseManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;
use RuntimeException;

class DatabaseManager
{
    protected IlluminateDatabaseManager $manager;

    public function __construct(IlluminateDatabaseManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Configura la conexión de base de datos para un tenant.
     */
    public function configureTenantConnection(Tenant $tenant): void
    {
        $config = $this->buildConnectionConfig($tenant);
        
        Config::set('database.connections.tenant', $config);
        
        // Purgar la conexión si ya existe
        if ($this->manager->getConnections()['tenant'] ?? null) {
            $this->manager->purge('tenant');
        }
    }

    /**
     * Construye la configuración de conexión para un tenant.
     */
    protected function buildConnectionConfig(Tenant $tenant): array
    {
        $centralConfig = Config::get('database.connections.' . Config::get('database.default'));
        
        return array_merge($centralConfig, [
            'database' => $this->getTenantDatabaseName($tenant),
            'prefix' => '',
        ]);
    }

    /**
     * Obtiene el nombre de la base de datos para un tenant.
     */
    public function getTenantDatabaseName(Tenant $tenant): string
    {
        return Config::get('tenancy.database_prefix', 'tenant_') . $tenant->id;
    }

    /**
     * Obtiene la conexión del tenant actual.
     */
    public function getTenantConnection(): Connection
    {
        return $this->manager->connection('tenant');
    }

    /**
     * Obtiene la conexión central/principal.
     */
    public function getCentralConnection(): Connection
    {
        return $this->manager->connection();
    }

    /**
     * Verifica si existe la base de datos del tenant.
     */
    public function tenantDatabaseExists(Tenant $tenant): bool
    {
        $databaseName = $this->getTenantDatabaseName($tenant);
        
        try {
            $result = $this->getCentralConnection()
                ->select("SHOW DATABASES LIKE ?", [$databaseName]);
            
            return count($result) > 0;
        } catch (\Exception $e) {
            Log::error("Error verificando existencia de BD del tenant {$tenant->id}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Crea la base de datos física para el tenant.
     */
    public function createTenantDatabase(Tenant $tenant): void
    {
        $databaseName = $this->getTenantDatabaseName($tenant);
        
        if ($this->tenantDatabaseExists($tenant)) {
            Log::info("La base de datos {$databaseName} ya existe para el tenant {$tenant->name}");
            return;
        }

        try {
            $centralConnection = $this->getCentralConnection();
            $centralConnection->statement("CREATE DATABASE `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            Log::info("Base de datos {$databaseName} creada para el tenant {$tenant->name}");
        } catch (\Exception $e) {
            Log::error("Error creando BD {$databaseName} para tenant {$tenant->name}: " . $e->getMessage());
            throw new RuntimeException("No se pudo crear la base de datos del tenant: " . $e->getMessage());
        }
    }

    /**
     * Elimina la base de datos del tenant.
     */
    public function dropTenantDatabase(Tenant $tenant): void
    {
        $databaseName = $this->getTenantDatabaseName($tenant);
        
        if (!$this->tenantDatabaseExists($tenant)) {
            Log::info("La base de datos {$databaseName} no existe para el tenant {$tenant->name}");
            return;
        }

        try {
            $centralConnection = $this->getCentralConnection();
            $centralConnection->statement("DROP DATABASE `{$databaseName}`");
            
            Log::info("Base de datos {$databaseName} eliminada para el tenant {$tenant->name}");
        } catch (\Exception $e) {
            Log::error("Error eliminando BD {$databaseName} para tenant {$tenant->name}: " . $e->getMessage());
            throw new RuntimeException("No se pudo eliminar la base de datos del tenant: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta las migraciones del tenant.
     */
    public function runTenantMigrations(Tenant $tenant, bool $fresh = false): void
    {
        $this->configureTenantConnection($tenant);
        
        try {
            if ($fresh) {
                Artisan::call('migrate:fresh', [
                    '--database' => 'tenant',
                    '--path' => 'database/tenant-migrations',
                    '--force' => true,
                ]);
                Log::info("Migraciones frescas ejecutadas para tenant {$tenant->name}");
            } else {
                Artisan::call('migrate', [
                    '--database' => 'tenant',
                    '--path' => 'database/tenant-migrations',
                    '--force' => true,
                ]);
                Log::info("Migraciones ejecutadas para tenant {$tenant->name}");
            }
        } catch (\Exception $e) {
            Log::error("Error ejecutando migraciones para tenant {$tenant->name}: " . $e->getMessage());
            throw new RuntimeException("Error en migraciones del tenant: " . $e->getMessage());
        }
    }

    /**
     * Ejecuta los seeders del tenant.
     */
    public function runTenantSeeders(Tenant $tenant, array $seeders = []): void
    {
        $this->configureTenantConnection($tenant);
        
        try {
            if (empty($seeders)) {
                Artisan::call('db:seed', [
                    '--database' => 'tenant',
                    '--force' => true,
                ]);
            } else {
                foreach ($seeders as $seeder) {
                    Artisan::call('db:seed', [
                        '--database' => 'tenant',
                        '--class' => $seeder,
                        '--force' => true,
                    ]);
                }
            }
            
            Log::info("Seeders ejecutados para tenant {$tenant->name}");
        } catch (\Exception $e) {
            Log::error("Error ejecutando seeders para tenant {$tenant->name}: " . $e->getMessage());
            throw new RuntimeException("Error en seeders del tenant: " . $e->getMessage());
        }
    }

    /**
     * Verifica el estado de las migraciones del tenant.
     */
    public function getTenantMigrationStatus(Tenant $tenant): array
    {
        $this->configureTenantConnection($tenant);
        
        try {
            Artisan::call('migrate:status', [
                '--database' => 'tenant',
                '--path' => 'database/tenant-migrations',
            ]);
            
            return [
                'status' => 'success',
                'output' => Artisan::output(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Hace rollback de las migraciones del tenant.
     */
    public function rollbackTenantMigrations(Tenant $tenant, int $steps = 1): void
    {
        $this->configureTenantConnection($tenant);
        
        try {
            Artisan::call('migrate:rollback', [
                '--database' => 'tenant',
                '--path' => 'database/tenant-migrations',
                '--step' => $steps,
                '--force' => true,
            ]);
            
            Log::info("Rollback de {$steps} pasos ejecutado para tenant {$tenant->name}");
        } catch (\Exception $e) {
            Log::error("Error en rollback para tenant {$tenant->name}: " . $e->getMessage());
            throw new RuntimeException("Error en rollback del tenant: " . $e->getMessage());
        }
    }

    /**
     * Obtiene información sobre el tamaño de la base de datos del tenant.
     */
    public function getTenantDatabaseSize(Tenant $tenant): array
    {
        $databaseName = $this->getTenantDatabaseName($tenant);
        
        try {
            $result = $this->getCentralConnection()->selectOne("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS size_mb,
                    COUNT(*) AS table_count
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$databaseName]);
            
            return [
                'database_name' => $databaseName,
                'size_mb' => $result->size_mb ?? 0,
                'table_count' => $result->table_count ?? 0,
            ];
        } catch (\Exception $e) {
            Log::error("Error obteniendo tamaño de BD para tenant {$tenant->name}: " . $e->getMessage());
            return [
                'database_name' => $databaseName,
                'size_mb' => 0,
                'table_count' => 0,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Purga todas las conexiones de tenant.
     */
    public function purgeAllTenantConnections(): void
    {
        $this->manager->purge('tenant');
        Log::debug('Conexiones de tenant purgadas');
    }
}