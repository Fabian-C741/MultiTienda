<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class ListTenantsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'tenant:list 
                            {--active : Show only active tenants}
                            {--inactive : Show only inactive tenants}
                            {--format=table : Output format (table|json)}';

    /**
     * The console command description.
     */
    protected $description = 'List all tenants';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $query = Tenant::query()->withCount('users');

        // Aplicar filtros
        if ($this->option('active')) {
            $query->where('is_active', true);
        } elseif ($this->option('inactive')) {
            $query->where('is_active', false);
        }

        $tenants = $query->orderBy('created_at', 'desc')->get();

        if ($tenants->isEmpty()) {
            $this->info('No se encontraron tenants.');
            return 0;
        }

        $format = $this->option('format');

        if ($format === 'json') {
            $this->line($tenants->toJson(JSON_PRETTY_PRINT));
        } else {
            $headers = ['ID', 'Nombre', 'Slug', 'Dominio', 'BD', 'Usuarios', 'Estado', 'Creado'];
            
            $rows = $tenants->map(function ($tenant) {
                return [
                    $tenant->id,
                    $tenant->name,
                    $tenant->slug,
                    $tenant->domain ?: '-',
                    $tenant->database,
                    $tenant->users_count,
                    $tenant->is_active ? '✓ Activo' : '✗ Inactivo',
                    $tenant->created_at->format('d/m/Y H:i'),
                ];
            })->toArray();

            $this->table($headers, $rows);
            $this->info("Total: {$tenants->count()} tenants");
        }

        return 0;
    }
}