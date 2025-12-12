<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class TenantProvisionCommand extends Command
{
    protected $signature = 'tenant:provision
                            {--name= : Nombre de la tienda}
                            {--slug= : Slug de la tienda (opcional)}
                            {--database= : Nombre de la base de datos}
                            {--db-host=127.0.0.1 : Host de la base de datos}
                            {--db-port=3306 : Puerto de la base de datos}
                            {--db-user= : Usuario de la base de datos}
                            {--db-password= : Contraseña de la base de datos}
                            {--admin-name= : Nombre del administrador}
                            {--admin-email= : Email del administrador}
                            {--admin-password= : Contraseña del administrador}
                            {--no-migrate : No ejecutar migraciones}';

    protected $description = 'Crea un nuevo tenant con su base de datos y administrador';

    public function __construct(
        protected TenantManager $tenantManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('=== Provisionamiento de nuevo tenant ===');
        $this->newLine();

        // Recopilar datos del tenant
        $name = $this->option('name') ?? $this->ask('Nombre de la tienda');
        $slug = $this->option('slug') ?? Str::slug($name);
        $database = $this->option('database') ?? $this->ask('Nombre de la base de datos', 'tenant_' . $slug);
        $dbHost = $this->option('db-host');
        $dbPort = $this->option('db-port');
        $dbUser = $this->option('db-user') ?? $this->ask('Usuario de base de datos');
        $dbPassword = $this->option('db-password') ?? $this->secret('Contraseña de base de datos');

        // Datos del admin
        $adminName = $this->option('admin-name') ?? $this->ask('Nombre del administrador');
        $adminEmail = $this->option('admin-email') ?? $this->ask('Email del administrador');
        $adminPassword = $this->option('admin-password') ?? $this->secret('Contraseña del administrador (mínimo 8 caracteres)');

        // Validar datos
        $validator = Validator::make([
            'name' => $name,
            'slug' => $slug,
            'database' => $database,
            'database_host' => $dbHost,
            'database_port' => $dbPort,
            'database_username' => $dbUser,
            'database_password' => $dbPassword,
            'admin_name' => $adminName,
            'admin_email' => $adminEmail,
            'admin_password' => $adminPassword,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:tenants,slug'],
            'database' => ['required', 'string', 'max:255', 'unique:tenants,database'],
            'database_host' => ['required', 'string'],
            'database_port' => ['required', 'string'],
            'database_username' => ['required', 'string'],
            'database_password' => ['required', 'string'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'unique:users,email'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error("✗ {$error}");
            }
            return self::FAILURE;
        }

        // Confirmar
        $this->newLine();
        $this->table(['Campo', 'Valor'], [
            ['Nombre', $name],
            ['Slug', $slug],
            ['Base de datos', $database],
            ['Host', $dbHost],
            ['Admin email', $adminEmail],
        ]);

        if (!$this->confirm('¿Crear este tenant?', true)) {
            $this->warn('Operación cancelada.');
            return self::SUCCESS;
        }

        try {
            // Crear tenant en la base de datos central
            $tenant = Tenant::create([
                'name' => $name,
                'slug' => $slug,
                'database' => $database,
                'database_host' => $dbHost,
                'database_port' => $dbPort,
                'database_username' => $dbUser,
                'database_password' => $dbPassword,
                'is_active' => true,
            ]);

            $this->info("✓ Tenant creado en base de datos central");

            // Crear base de datos física
            $this->tenantManager->createDatabase($tenant);
            $this->info("✓ Base de datos [{$database}] creada");

            // Ejecutar migraciones
            if (!$this->option('no-migrate')) {
                $this->tenantManager->runMigrations($tenant);
                $this->info("✓ Migraciones ejecutadas");
            }

            // Crear administrador
            $admin = $this->tenantManager->createTenantAdmin($tenant, [
                'name' => $adminName,
                'email' => $adminEmail,
                'password' => $adminPassword,
            ]);
            $this->info("✓ Administrador [{$admin->email}] creado");

            $this->newLine();
            $this->info("🎉 Tenant provisionado exitosamente!");
            $this->newLine();

            $this->table(['Dato', 'Valor'], [
                ['ID Tenant', $tenant->id],
                ['URL sugerida', "https://{$slug}.tudominio.com"],
                ['Login admin', $adminEmail],
            ]);

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("✗ Error durante el provisionamiento: {$e->getMessage()}");

            // Intentar rollback si el tenant fue creado
            if (isset($tenant)) {
                $tenant->delete();
                $this->warn("Tenant eliminado de la base de datos central.");
            }

            return self::FAILURE;
        }
    }
}
