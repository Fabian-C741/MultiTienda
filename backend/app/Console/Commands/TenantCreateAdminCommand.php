<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class TenantCreateAdminCommand extends Command
{
    protected $signature = 'tenant:create-admin
                            {tenant : ID o slug del tenant}
                            {--name= : Nombre del administrador}
                            {--email= : Email del administrador}
                            {--password= : Contraseña del administrador}';

    protected $description = 'Crea un usuario administrador para un tenant específico';

    public function __construct(
        protected TenantManager $tenantManager
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $tenantId = $this->argument('tenant');

        $tenant = Tenant::query()
            ->where('id', $tenantId)
            ->orWhere('slug', $tenantId)
            ->first();

        if (!$tenant) {
            $this->error("Tenant [{$tenantId}] no encontrado.");
            return self::FAILURE;
        }

        $this->info("Creando administrador para: {$tenant->name}");
        $this->newLine();

        // Obtener datos del admin
        $name = $this->option('name') ?? $this->ask('Nombre del administrador');
        $email = $this->option('email') ?? $this->ask('Email del administrador');
        $password = $this->option('password') ?? $this->secret('Contraseña (mínimo 8 caracteres)');

        // Validar datos
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        try {
            $user = $this->tenantManager->createTenantAdmin($tenant, [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);

            $this->info("✓ Administrador creado exitosamente:");
            $this->table(
                ['Campo', 'Valor'],
                [
                    ['Nombre', $user->name],
                    ['Email', $user->email],
                    ['Rol', $user->role],
                    ['Tenant', $tenant->name],
                ]
            );

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Error creando administrador: {$e->getMessage()}");
            return self::FAILURE;
        }
    }
}
