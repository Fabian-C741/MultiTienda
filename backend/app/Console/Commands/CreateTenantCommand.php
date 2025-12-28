<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateTenantCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:create 
                            {name : The name of the tenant}
                            {slug : The slug of the tenant}
                            {--domain= : Custom domain for the tenant}
                            {--admin-name= : Name of the admin user}
                            {--admin-email= : Email of the admin user}
                            {--admin-password= : Password of the admin user}
                            {--db-host=127.0.0.1 : Database host}
                            {--db-port=3306 : Database port}
                            {--db-name= : Database name (defaults to tenant_{id})}
                            {--db-user=root : Database username}
                            {--db-password= : Database password}
                            {--no-provision : Skip database provisioning}';

    /**
     * The console command description.
     */
    protected $description = 'Create a new tenant with database and admin user';

    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        parent::__construct();
        $this->tenantManager = $tenantManager;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');
        $slug = $this->argument('slug');

        // Validar que el slug no exista
        if (Tenant::where('slug', $slug)->exists()) {
            $this->error("Ya existe un tenant con el slug '{$slug}'");
            return 1;
        }

        // Obtener información del admin
        $adminName = $this->option('admin-name') ?: $this->ask('Nombre del administrador');
        $adminEmail = $this->option('admin-email') ?: $this->ask('Email del administrador');
        $adminPassword = $this->option('admin-password') ?: $this->secret('Contraseña del administrador');

        // Validar email único
        if (\App\Models\User::where('email', $adminEmail)->exists()) {
            $this->error("Ya existe un usuario con el email '{$adminEmail}'");
            return 1;
        }

        // Configuración de base de datos
        $dbHost = $this->option('db-host');
        $dbPort = (int) $this->option('db-port');
        $dbUser = $this->option('db-user');
        $dbPassword = $this->option('db-password') ?: $this->secret('Contraseña de la base de datos');

        $this->info("Creando tenant '{$name}' con slug '{$slug}'...");

        try {
            // Crear el tenant
            $tenant = Tenant::create([
                'name' => $name,
                'slug' => $slug,
                'domain' => $this->option('domain'),
                'database_host' => $dbHost,
                'database_port' => $dbPort,
                'database' => $this->option('db-name') ?: "tenant_{$slug}",
                'database_username' => $dbUser,
                'database_password' => $dbPassword,
                'is_active' => true,
            ]);

            $this->info("✓ Tenant creado con ID: {$tenant->id}");

            // Provisionar si no se especifica lo contrario
            if (!$this->option('no-provision')) {
                $this->info("Provisionando base de datos y usuario administrador...");

                $admin = $this->tenantManager->provisionFull($tenant, [
                    'name' => $adminName,
                    'email' => $adminEmail,
                    'password' => $adminPassword,
                ]);

                $this->info("✓ Base de datos provisionada");
                $this->info("✓ Usuario administrador creado: {$admin->email}");
            }

            $this->newLine();
            $this->info("🎉 Tenant '{$name}' creado exitosamente!");
            $this->table(['Campo', 'Valor'], [
                ['ID', $tenant->id],
                ['Nombre', $tenant->name],
                ['Slug', $tenant->slug],
                ['Dominio', $tenant->domain ?: 'N/A'],
                ['Base de datos', $tenant->database],
                ['Admin email', $adminEmail],
                ['Estado', $tenant->is_active ? 'Activo' : 'Inactivo'],
            ]);

            return 0;

        } catch (\Exception $e) {
            $this->error("Error creando el tenant: {$e->getMessage()}");
            return 1;
        }
    }
}