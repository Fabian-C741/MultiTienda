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
        $tenant = Tenant::create([
            'name' => 'Tienda Demo',
            'slug' => 'demo', 
            'subdomain' => 'demo',
            'description' => 'Tienda de demostración',
            'domain' => null,
            'database' => 'tenant_demo',
            'database_host' => '127.0.0.1',
            'database_port' => '3306',
            'database_username' => 'tenant_demo',
            'database_password' => 'password',
            'plan' => 'basic',
            'status' => 'active',
            'settings' => [
                'theme' => 'default',
                'features' => ['products', 'cart', 'payments']
            ],
            'is_active' => true
        ]);

        $this->info("✅ Tenant creado: {$tenant->name} (slug: {$tenant->slug})");
        $this->info("🌐 URL: " . url("/tienda/{$tenant->slug}"));
        $this->info("⚙️ Admin: " . url("/tienda/{$tenant->slug}/admin"));
        
        return Command::SUCCESS;
    }
}