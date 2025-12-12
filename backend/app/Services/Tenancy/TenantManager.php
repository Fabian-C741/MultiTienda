<?php

declare(strict_types=1);

namespace App\Services\Tenancy;

use App\Models\Tenant;
use App\Models\User;
use Closure;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PDO;
use PDOException;
use RuntimeException;

class TenantManager
{
    protected ?Tenant $tenant = null;

    public function current(): ?Tenant
    {
        return $this->tenant;
    }

    public function setTenant(Tenant $tenant): void
    {
        $this->tenant = $tenant;

        Config::set('database.connections.tenant', $tenant->databaseConfig());
        DB::purge('tenant');
    }

    public function forget(): void
    {
        $this->tenant = null;
        DB::purge('tenant');
    }

    public function runFor(Tenant $tenant, Closure $callback): mixed
    {
        $previous = $this->tenant;

        $this->setTenant($tenant);

        try {
            return $callback($tenant);
        } finally {
            if ($previous) {
                $this->setTenant($previous);
            } else {
                $this->forget();
            }
        }
    }

    public function connection(): string
    {
        return 'tenant';
    }

    /**
     * Provisiona la base de datos del tenant: crea el schema si no existe y ejecuta migraciones.
     */
    public function provision(Tenant $tenant): void
    {
        $this->createDatabase($tenant);
        $this->runMigrations($tenant);
    }

    /**
     * Crea la base de datos física para el tenant usando PDO (sin Eloquent).
     */
    public function createDatabase(Tenant $tenant): void
    {
        $config = $tenant->databaseConfig();
        $database = $config['database'];

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s', $config['host'], $config['port']),
                $config['username'],
                $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            // Crear base de datos si no existe
            $pdo->exec(sprintf(
                'CREATE DATABASE IF NOT EXISTS `%s` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci',
                $database
            ));

            Log::info("Base de datos [{$database}] creada/verificada para tenant [{$tenant->name}].");
        } catch (PDOException $e) {
            Log::error("Error creando base de datos [{$database}]: {$e->getMessage()}");
            throw new RuntimeException("No se pudo crear la base de datos del tenant: {$e->getMessage()}");
        }
    }

    /**
     * Ejecuta las migraciones específicas del tenant.
     */
    public function runMigrations(Tenant $tenant): void
    {
        $this->setTenant($tenant);

        $migrationsPath = database_path('tenant-migrations');

        if (!is_dir($migrationsPath)) {
            Log::warning("Directorio de migraciones de tenant no encontrado: {$migrationsPath}");
            return;
        }

        try {
            Artisan::call('migrate', [
                '--database' => 'tenant',
                '--path' => 'database/tenant-migrations',
                '--force' => true,
            ]);

            Log::info("Migraciones ejecutadas para tenant [{$tenant->name}].");
        } catch (\Exception $e) {
            Log::error("Error ejecutando migraciones para tenant [{$tenant->name}]: {$e->getMessage()}");
            throw new RuntimeException("Error en migraciones del tenant: {$e->getMessage()}");
        } finally {
            $this->forget();
        }
    }

    /**
     * Crea el usuario administrador inicial del tenant.
     */
    public function createTenantAdmin(Tenant $tenant, array $adminData): User
    {
        $user = User::create([
            'name' => $adminData['name'],
            'email' => $adminData['email'],
            'password' => Hash::make($adminData['password']),
            'role' => 'tenant_admin',
            'tenant_id' => $tenant->id,
            'email_verified_at' => now(),
        ]);

        Log::info("Usuario admin [{$user->email}] creado para tenant [{$tenant->name}].");

        return $user;
    }

    /**
     * Flujo completo de provisionamiento: BD + migraciones + usuario admin.
     */
    public function provisionFull(Tenant $tenant, array $adminData): User
    {
        DB::beginTransaction();

        try {
            // 1. Crear base de datos física
            $this->createDatabase($tenant);

            // 2. Ejecutar migraciones del tenant
            $this->runMigrations($tenant);

            // 3. Crear usuario administrador
            $admin = $this->createTenantAdmin($tenant, $adminData);

            DB::commit();

            return $admin;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Provisionamiento fallido para tenant [{$tenant->name}]: {$e->getMessage()}");
            throw $e;
        }
    }

    /**
     * Elimina la base de datos del tenant (usar con precaución).
     */
    public function dropDatabase(Tenant $tenant): void
    {
        $config = $tenant->databaseConfig();
        $database = $config['database'];

        try {
            $pdo = new PDO(
                sprintf('mysql:host=%s;port=%s', $config['host'], $config['port']),
                $config['username'],
                $config['password'],
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );

            $pdo->exec(sprintf('DROP DATABASE IF EXISTS `%s`', $database));

            Log::info("Base de datos [{$database}] eliminada para tenant [{$tenant->name}].");
        } catch (PDOException $e) {
            Log::error("Error eliminando base de datos [{$database}]: {$e->getMessage()}");
            throw new RuntimeException("No se pudo eliminar la base de datos: {$e->getMessage()}");
        }
    }
}
