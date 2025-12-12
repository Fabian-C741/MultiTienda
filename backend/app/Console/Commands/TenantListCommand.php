<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class TenantListCommand extends Command
{
    protected $signature = 'tenant:list
                            {--all : Mostrar todos los tenants incluyendo inactivos}';

    protected $description = 'Lista todos los tenants del sistema';

    public function handle(): int
    {
        $query = Tenant::query()->orderBy('name');

        if (!$this->option('all')) {
            $query->where('is_active', true);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->warn('No hay tenants registrados.');
            return self::SUCCESS;
        }

        $this->info("Tenants encontrados: {$tenants->count()}");
        $this->newLine();

        $this->table(
            ['ID', 'Nombre', 'Slug', 'Base de datos', 'Activo', 'Creado'],
            $tenants->map(fn($t) => [
                $t->id,
                $t->name,
                $t->slug,
                $t->database,
                $t->is_active ? '✓' : '✗',
                $t->created_at->format('Y-m-d H:i'),
            ])
        );

        return self::SUCCESS;
    }
}
