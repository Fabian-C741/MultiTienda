<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreTenantRequest;
use App\Http\Requests\Admin\UpdateTenantRequest;
use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class TenantController extends Controller
{
    public function __construct(
        protected TenantManager $tenantManager
    ) {}

    public function index(): View
    {
        $tenants = Tenant::query()->latest()->paginate(15);

        return view('admin.tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('admin.tenants.create');
    }

    public function store(StoreTenantRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        // Extraer datos del admin
        $adminData = $data['admin'] ?? [];
        unset($data['admin']);

        DB::beginTransaction();

        try {
            // 1. Crear registro del tenant
            $tenant = Tenant::query()->create($data);

            // 2. Provisionar base de datos y migraciones
            $this->tenantManager->createDatabase($tenant);
            $this->tenantManager->runMigrations($tenant);

            // 3. Crear usuario administrador del tenant
            if (!empty($adminData)) {
                $this->tenantManager->createTenantAdmin($tenant, $adminData);
            }

            DB::commit();

            return Redirect::route('admin.tenants.edit', $tenant)
                ->with('status', __('Tienda creada y provisionada correctamente.'));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error creando tenant: {$e->getMessage()}", [
                'data' => $data,
                'trace' => $e->getTraceAsString(),
            ]);

            return Redirect::back()
                ->withInput()
                ->with('error', __('Error al crear la tienda: ') . $e->getMessage());
        }
    }

    public function edit(Tenant $tenant): View
    {
        return view('admin.tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        return Redirect::back()->with('status', __('Datos de la tienda guardados.'));
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Eliminar usuarios asociados
            $tenant->users()->delete();

            // Opcionalmente eliminar base de datos física
            if (request()->boolean('drop_database')) {
                $this->tenantManager->dropDatabase($tenant);
            }

            $tenant->delete();

            DB::commit();

            return Redirect::route('admin.tenants.index')
                ->with('status', __('La tienda se eliminó correctamente.'));

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error("Error eliminando tenant [{$tenant->id}]: {$e->getMessage()}");

            return Redirect::back()
                ->with('error', __('Error al eliminar la tienda: ') . $e->getMessage());
        }
    }

    /**
     * Re-ejecuta las migraciones del tenant (útil para actualizaciones).
     */
    public function migrate(Tenant $tenant): RedirectResponse
    {
        try {
            $this->tenantManager->runMigrations($tenant);

            return Redirect::back()
                ->with('status', __('Migraciones ejecutadas correctamente.'));

        } catch (\Exception $e) {
            return Redirect::back()
                ->with('error', __('Error ejecutando migraciones: ') . $e->getMessage());
        }
    }
}
