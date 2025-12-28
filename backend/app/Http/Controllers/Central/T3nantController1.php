<?php

declare(strict_types=1);

namespace App\Http\Controllers\Central;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class TenantController extends Controller
{
    protected TenantManager $tenantManager;

    public function __construct(TenantManager $tenantManager)
    {
        $this->tenantManager = $tenantManager;
    }

    /**
     * Display a listing of tenants.
     */
    public function index(Request $request)
    {
        $tenants = Tenant::with(['users'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        if ($request->expectsJson()) {
            return response()->json($tenants);
        }

        return view('central.tenants.index', compact('tenants'));
    }

    /**
     * Display the central dashboard.
     */
    public function dashboard()
    {
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $suspendedTenants = Tenant::where('status', 'suspended')->count();
        $thisMonthTenants = Tenant::whereMonth('created_at', now()->month)->count();
        $recentTenants = Tenant::orderBy('created_at', 'desc')->take(5)->get();

        return view('central.dashboard', compact(
            'totalTenants',
            'activeTenants', 
            'suspendedTenants',
            'thisMonthTenants',
            'recentTenants'
        ));
    }

    /**
     * Display statistics.
     */
    public function stats()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('status', 'active')->count(),
            'suspended_tenants' => Tenant::where('status', 'suspended')->count(),
            'inactive_tenants' => Tenant::where('status', 'inactive')->count(),
            'today_tenants' => Tenant::whereDate('created_at', today())->count(),
            'week_tenants' => Tenant::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
            'month_tenants' => Tenant::whereMonth('created_at', now()->month)->count(),
            'tenant_growth' => $this->calculateTenantGrowth(),
            'total_products' => 0, // Se calculará desde las bases de datos de tenants
            'total_users' => User::count(),
            'plans' => [
                'basic' => Tenant::where('plan', 'basic')->count(),
                'standard' => Tenant::where('plan', 'standard')->count(),
                'premium' => Tenant::where('plan', 'premium')->count(),
            ],
            'growth_labels' => $this->getGrowthLabels(),
            'growth_data' => $this->getGrowthData(),
        ];

        $topTenants = Tenant::orderBy('updated_at', 'desc')->take(10)->get();

        return view('central.stats', compact('stats', 'topTenants'));
    }

    private function calculateTenantGrowth()
    {
        $currentMonth = Tenant::whereMonth('created_at', now()->month)->count();
        $lastMonth = Tenant::whereMonth('created_at', now()->subMonth()->month)->count();
        
        if ($lastMonth == 0) return 0;
        
        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 2);
    }

    private function getGrowthLabels()
    {
        $labels = [];
        for ($i = 11; $i >= 0; $i--) {
            $labels[] = now()->subMonths($i)->format('M Y');
        }
        return $labels;
    }

    private function getGrowthData()
    {
        $data = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $data[] = Tenant::whereYear('created_at', $date->year)
                          ->whereMonth('created_at', $date->month)
                          ->count();
        }
        return $data;
    }

    /**
     * Show the form for creating a new tenant.
     */
    public function create()
    {
        return view('central.tenants.create');
    }

    /**
     * Store a newly created tenant.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:100|unique:tenants,subdomain|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'owner_name' => 'required|string|max:255',
            'owner_email' => 'required|email|unique:users,email',
            'owner_password' => 'required|string|min:8',
            'plan' => 'nullable|string|in:basic,standard,premium',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'currency' => 'nullable|string|max:3',
            'timezone' => 'nullable|string|max:50',
            'language' => 'nullable|string|max:5',
            'max_products' => 'nullable|integer|min:0',
            'max_storage' => 'nullable|integer|min:0',
            'setup_demo_data' => 'boolean',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            // Crear el tenant
            $tenant = Tenant::create([
                'name' => $request->name,
                'subdomain' => $request->subdomain,
                'description' => $request->description,
                'plan' => $request->plan ?? 'basic',
                'status' => $request->status ?? 'active',
                'settings' => [
                    'currency' => $request->currency ?? 'USD',
                    'timezone' => $request->timezone ?? 'America/New_York',
                    'language' => $request->language ?? 'es',
                    'max_products' => $request->max_products ?? 1000,
                    'max_storage' => $request->max_storage ?? 1000,
                ],
            ]);

            // Provisionar completamente el tenant
            $admin = $this->tenantManager->provisionFull($tenant, [
                'name' => $request->owner_name,
                'email' => $request->owner_email,
                'password' => $request->owner_password,
            ], $request->boolean('setup_demo_data', false));

            DB::commit();

            Log::info("Tenant {$tenant->name} creado exitosamente con admin {$admin->email}");

            if ($request->expectsJson()) {
                return response()->json([
                    'tenant' => $tenant->load('users'),
                    'message' => 'Tenant creado exitosamente'
                ], 201);
            }

            return redirect()->route('central.tenants.show', $tenant)
                           ->with('success', 'Tienda creada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error creando tenant: {$e->getMessage()}");

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error interno del servidor'], 500);
            }

            return back()->with('error', 'Error creando la tienda. Intenta nuevamente.')
                        ->withInput();
        }
    }

    /**
     * Display the specified tenant.
     */
    public function show(Request $request, Tenant $tenant)
    {
        $tenant->load(['users']);

        if ($request->expectsJson()) {
            return response()->json($tenant);
        }

        return view('central.tenants.show', compact('tenant'));
    }

    /**
     * Show the form for editing the specified tenant.
     */
    public function edit(Tenant $tenant)
    {
        return view('central.tenants.edit', compact('tenant'));
    }

    /**
     * Update the specified tenant.
     */
    public function update(Request $request, Tenant $tenant)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'subdomain' => 'required|string|max:100|unique:tenants,subdomain,' . $tenant->id . '|regex:/^[a-z0-9-]+$/',
            'description' => 'nullable|string|max:500',
            'plan' => 'nullable|string|in:basic,standard,premium',
            'status' => 'nullable|string|in:active,inactive,suspended',
            'settings' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $tenant->update($request->all());

            Log::info("Tenant {$tenant->name} actualizado exitosamente");

            if ($request->expectsJson()) {
                return response()->json([
                    'tenant' => $tenant->fresh(),
                    'message' => 'Tenant actualizado exitosamente',
                ]);
            }

            return redirect()->route('central.tenants.show', $tenant)
                           ->with('success', 'Tienda actualizada exitosamente');

        } catch (\Exception $e) {
            Log::error("Error actualizando tenant {$tenant->id}: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error interno del servidor'], 500);
            }

            return back()->with('error', 'Error actualizando la tienda. Intenta nuevamente.')
                        ->withInput();
        }
    }

    /**
     * Remove the specified tenant.
     */
    public function destroy(Request $request, Tenant $tenant)
    {
        try {
            DB::beginTransaction();

            $tenantName = $tenant->name;
            
            // Eliminar base de datos del tenant si existe
            try {
                $this->tenantManager->dropDatabase($tenant);
            } catch (\Exception $e) {
                Log::warning("No se pudo eliminar la base de datos del tenant {$tenant->id}: " . $e->getMessage());
            }

            // Eliminar el tenant
            $tenant->delete();

            DB::commit();

            Log::warning("Tenant {$tenantName} eliminado");

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tenant eliminado exitosamente']);
            }

            return redirect()->route('central.tenants.index')
                           ->with('success', 'Tienda eliminada exitosamente');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error eliminando tenant {$tenant->id}: " . $e->getMessage());

            if ($request->expectsJson()) {
                return response()->json(['error' => 'Error interno del servidor'], 500);
            }

            return back()->with('error', 'Error eliminando la tienda. Intenta nuevamente.');
        }
    }

    /**
     * Suspend a tenant.
     */
    public function suspend(Request $request, Tenant $tenant)
    {
        try {
            $tenant->update(['status' => 'suspended']);

            Log::info("Tenant {$tenant->name} suspendido");

            return response()->json([
                'success' => true,
                'message' => 'Tienda suspendida exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error("Error suspendiendo tenant {$tenant->id}: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Activate a tenant.
     */
    public function activate(Request $request, Tenant $tenant)
    {
        try {
            $tenant->update(['status' => 'active']);

            Log::info("Tenant {$tenant->name} activado");

            return response()->json([
                'success' => true,
                'message' => 'Tienda activada exitosamente'
            ]);

        } catch (\Exception $e) {
            Log::error("Error activando tenant {$tenant->id}: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Bulk activate tenants.
     */
    public function bulkActivate(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        try {
            $updated = Tenant::whereIn('id', $request->ids)->update(['status' => 'active']);

            Log::info("Activados {$updated} tenants en lote");

            return response()->json([
                'success' => true,
                'message' => "{$updated} tiendas activadas exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error("Error en activación masiva: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Bulk suspend tenants.
     */
    public function bulkSuspend(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        try {
            $updated = Tenant::whereIn('id', $request->ids)->update(['status' => 'suspended']);

            Log::info("Suspendidos {$updated} tenants en lote");

            return response()->json([
                'success' => true,
                'message' => "{$updated} tiendas suspendidas exitosamente"
            ]);

        } catch (\Exception $e) {
            Log::error("Error en suspensión masiva: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }

    /**
     * Bulk delete tenants.
     */
    public function bulkDelete(Request $request)
    {
        $request->validate(['ids' => 'required|array']);

        try {
            DB::beginTransaction();

            $tenants = Tenant::whereIn('id', $request->ids)->get();
            
            foreach ($tenants as $tenant) {
                // Intentar eliminar base de datos
                try {
                    $this->tenantManager->dropDatabase($tenant);
                } catch (\Exception $e) {
                    Log::warning("No se pudo eliminar la base de datos del tenant {$tenant->id}: " . $e->getMessage());
                }
            }

            $deleted = Tenant::whereIn('id', $request->ids)->delete();

            DB::commit();

            Log::warning("Eliminados {$deleted} tenants en lote");

            return response()->json([
                'success' => true,
                'message' => "{$deleted} tiendas eliminadas exitosamente"
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error en eliminación masiva: " . $e->getMessage());
            return response()->json(['error' => 'Error interno del servidor'], 500);
        }
    }
}