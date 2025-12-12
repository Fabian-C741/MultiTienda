<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;

class TenantMigrateCommand extends Command
{
    protected $signature = 'tenant:migrate 
                            {tenant? : ID o slug del tenant (si no se especifica, migra todos)}
                            {--fresh : Eliminar todas las tablas y volver a ejecutar migraciones}
                            {--seed : Ejecutar seeders después de las migraciones}
                            {--force : Forzar ejecución en producción}';

    protected $description = 'Ejecuta las migraciones para uno o todos los tenants';

    public function __construct(
        protected TenantManager $tenantManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');

        if ($tenantId) {
            $tenant = Tenant::query()
                ->where('id', $tenantId)
                ->orWhere('slug', $tenantId)
                ->first();

            if (!$tenant) {
                $this->error("Tenant [{$tenantId}] no encontrado.");
                return self::FAILURE;
            }

            return $this->migrateTenant($tenant);
        }

        // Migrar todos los tenants activos
        $tenants = Tenant::query()->where('is_active', true)->get();

        if ($tenants->isEmpty()) {
            $this->warn('No hay tenants activos para migrar.');
            return self::SUCCESS;
        }

        $this->info("Migrando {$tenants->count()} tenant(s)...");
        $this->newLine();

        $failed = 0;

        foreach ($tenants as $tenant) {
            if ($this->migrateTenant($tenant) === self::FAILURE) {
                $failed++;
            }
        }

        $this->newLine();

        if ($failed > 0) {
            $this->error("{$failed} tenant(s) fallaron durante la migración.");
            return self::FAILURE;
        }

        $this->info('Todas las migraciones completadas exitosamente.');
        return self::SUCCESS;
    }

    protected function migrateTenant(Tenant $tenant): int
    {
        $this->info("▸ Migrando tenant: {$tenant->name} ({$tenant->database})");

        try {
            $this->tenantManager->setTenant($tenant);

            $options = [
                '--database' => 'tenant',
                '--path' => 'database/tenant-migrations',
            ];

            if ($this->option('force')) {
                $options['--force'] = true;
            }

            if ($this->option('fresh')) {
                $this->call('migrate:fresh', $options);
            } else {
                $this->call('migrate', $options);
            }

            if ($this->option('seed')) {
                $this->call('db:seed', [
                    '--database' => 'tenant',
                    '--class' => 'TenantDatabaseSeeder',
                    '--force' => $this->option('force'),
                ]);
            }

            $this->info("  ✓ Completado");
            $this->newLine();

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("  ✗ Error: {$e->getMessage()}");
            $this->newLine();
            return self::FAILURE;

        } finally {
            $this->tenantManager->forget();
        }
    }
}
