<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateTestTenant extends Command
{
    protected $signature = 'tenant:create-test';
    protected $description = 'Crear tenant de prueba para development';

    public function handle()
    {
        // Detectar qué columnas existen en la tabla
        $columns = \DB::getSchemaBuilder()->getColumnListing('tenants');
        $this->info("Columnas disponibles: " . implode(', ', $columns));

        // Datos base que siempre debería tener
        $data = [
            'name' => 'Tienda Demo',
            'slug' => 'demo',
            'status' => 'active',
        ];

        // Agregar columnas opcionales solo si existen
        if (in_array('subdomain', $columns)) {
            $data['subdomain'] = 'demo';
        }

        if (in_array('description', $columns)) {
            $data['description'] = 'Tienda de demostración';
        }

        if (in_array('domain', $columns)) {
            $data['domain'] = null;
        }

        if (in_array('database', $columns)) {
            $data['database'] = 'tenant_demo';
        }

        if (in_array('database_host', $columns)) {
            $data['database_host'] = '127.0.0.1';
        }

        if (in_array('database_port', $columns)) {
            $data['database_port'] = '3306';
        }

        if (in_array('database_username', $columns)) {
            $data['database_username'] = 'tenant_demo';
        }

        if (in_array('database_password', $columns)) {
            $data['database_password'] = 'password';
        }

        if (in_array('plan', $columns)) {
            $data['plan'] = 'basic';
        }

        if (in_array('settings', $columns)) {
            $data['settings'] = [
                'theme' => 'default',
                'features' => ['products', 'cart', 'payments']
            ];
        }

        if (in_array('metadata', $columns)) {
            $data['metadata'] = [
                'theme' => 'default',
                'features' => ['products', 'cart', 'payments']
            ];
        }

        if (in_array('is_active', $columns)) {
            $data['is_active'] = true;
        }

        try {
            $tenant = Tenant::create($data);
            $this->info("✅ Tenant creado: {$tenant->name} (slug: {$tenant->slug})");
            $this->info("🌐 URL: " . url("/tienda/{$tenant->slug}"));
            $this->info("⚙️ Admin: " . url("/tienda/{$tenant->slug}/admin"));
        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->info("Datos que se intentaron insertar:");
            foreach ($data as $key => $value) {
                $this->line("  {$key}: " . (is_array($value) ? json_encode($value) : $value));
            }
        }
        
        return Command::SUCCESS;
    }
}