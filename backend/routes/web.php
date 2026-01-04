<?php

use App\Http\Controllers\Central\TenantController as CentralTenantController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\TenantController as AdminTenantController;
use App\Http\Controllers\Tenant\AuthController as TenantAuthController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\ProductController;
use App\Http\Controllers\Tenant\SettingsController;
use App\Http\Controllers\Storefront\CatalogController;
use App\Http\Controllers\Storefront\CartController;
use App\Http\Controllers\Storefront\CheckoutController;
use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - MultiTienda System
|--------------------------------------------------------------------------
*/

// Página principal del sistema
Route::get('/', function () {
    $tenants = \App\Models\Tenant::where('status', 'active')->take(5)->get();
    return view('multitienda-home', compact('tenants'));
});

// Rutas centrales para gestión de tenants (sin contexto de tenant)
Route::prefix('central')
    ->name('central.')
    ->group(function () {
        Route::get('/', [CentralTenantController::class, 'dashboard'])->name('dashboard');
        Route::resource('tenants', CentralTenantController::class);
        Route::post('tenants/{tenant}/suspend', [CentralTenantController::class, 'suspend'])->name('tenants.suspend');
        Route::post('tenants/{tenant}/activate', [CentralTenantController::class, 'activate'])->name('tenants.activate');
        Route::delete('tenants/{tenant}', [CentralTenantController::class, 'destroy'])->name('tenants.destroy');
        Route::post('tenants/bulk-activate', [CentralTenantController::class, 'bulkActivate'])->name('tenants.bulk-activate');
        Route::post('tenants/bulk-suspend', [CentralTenantController::class, 'bulkSuspend'])->name('tenants.bulk-suspend');
        Route::delete('tenants/bulk-delete', [CentralTenantController::class, 'bulkDelete'])->name('tenants.bulk-delete');
        Route::get('stats', [CentralTenantController::class, 'stats'])->name('stats');
    });

Route::prefix('admin')
    ->name('admin.')
    ->middleware('prevent-central-access')
    ->group(function () {
        Route::middleware('guest')->group(function () {
            Route::get('login', [AdminAuthController::class, 'showLogin'])->name('login');
            Route::post('login', [AdminAuthController::class, 'login'])->name('login.attempt');
        });

        Route::post('logout', [AdminAuthController::class, 'logout'])
            ->middleware('auth')
            ->name('logout');

        Route::middleware(['auth', 'super-admin'])->group(function () {
            Route::get('/', AdminDashboardController::class)->name('dashboard');
            Route::resource('tenants', AdminTenantController::class)->except(['show']);
            Route::post('tenants/{tenant}/migrate', [AdminTenantController::class, 'migrate'])->name('tenants.migrate');
        });
    });

Route::prefix(config('tenancy.path_identifier'))
    ->as('tenant.')
    ->middleware('initialize-tenancy')
    ->group(function () {
        Route::middleware('ensure-tenant-context')->group(function () {
            Route::get('/{tenant}/status', function (Request $request, Tenant $tenant, TenantManager $tenantManager) {
                return response()->json([
                    'tenant' => [
                        'id' => $tenant->id,
                        'name' => $tenant->name,
                        'slug' => $tenant->slug,
                        'domain' => $tenant->domain,
                    ],
                    'connection' => $tenantManager->connection(),
                ]);
            })->name('status');
        });

        Route::prefix('/{tenant}/admin')->group(function () {
            Route::middleware(['tenant', 'guest:tenant'])->group(function () {
                Route::get('/login', [TenantAuthController::class, 'show'])->name('login.show');
                Route::post('/login', [TenantAuthController::class, 'authenticate'])->name('login.perform');
            });

            Route::middleware(['tenant', 'auth:tenant', 'tenant-admin'])->group(function () {
                Route::post('/logout', [TenantAuthController::class, 'logout'])->name('logout');
                Route::get('/', TenantDashboardController::class)->name('dashboard');

                Route::get('/apariencia', [SettingsController::class, 'edit'])->name('settings.edit');
                Route::post('/apariencia', [SettingsController::class, 'update'])->name('settings.update');

                Route::resource('products', ProductController::class)
                    ->except(['show'])
                    ->parameters(['products' => 'product'])
                    ->names('products');

                Route::resource('categories', \App\Http\Controllers\Tenant\CategoryController::class)
                    ->except(['show'])
                    ->names('categories');

                Route::resource('orders', \App\Http\Controllers\Tenant\OrderController::class)
                    ->only(['index', 'show', 'update'])
                    ->names('orders');

                Route::get('/pagos', [\App\Http\Controllers\Tenant\PaymentSettingsController::class, 'index'])->name('payments.index');
                Route::get('/pagos/{gateway}', [\App\Http\Controllers\Tenant\PaymentSettingsController::class, 'edit'])->name('payments.edit');
                Route::post('/pagos/{gateway}', [\App\Http\Controllers\Tenant\PaymentSettingsController::class, 'update'])->name('payments.update');

                // Galería de medios
                Route::get('/media', [\App\Http\Controllers\Tenant\MediaController::class, 'index'])->name('media.index');
                Route::post('/media', [\App\Http\Controllers\Tenant\MediaController::class, 'store'])->name('media.store');
                Route::get('/media/picker', [\App\Http\Controllers\Tenant\MediaController::class, 'picker'])->name('media.picker');
                Route::delete('/media/{media}', [\App\Http\Controllers\Tenant\MediaController::class, 'destroy'])->name('media.destroy');
                Route::post('/media/url', [\App\Http\Controllers\Tenant\MediaController::class, 'uploadFromUrl'])->name('media.upload-url');
            });
        });

        // Storefront público (tienda del tenant)
        Route::prefix('/{tenant}')->middleware('tenant')->name('storefront.')->group(function () {
            Route::get('/', [CatalogController::class, 'home'])->name('home');
            Route::get('/catalogo', [CatalogController::class, 'index'])->name('catalog');
            Route::get('/categoria/{slug}', [CatalogController::class, 'category'])->name('category');
            Route::get('/producto/{slug}', [CatalogController::class, 'show'])->name('product');

            // Carrito
            Route::get('/carrito', [CartController::class, 'index'])->name('cart.index');
            Route::post('/carrito/agregar', [CartController::class, 'add'])->name('cart.add');
            Route::post('/carrito/actualizar', [CartController::class, 'update'])->name('cart.update');
            Route::post('/carrito/eliminar', [CartController::class, 'remove'])->name('cart.remove');
            Route::post('/carrito/vaciar', [CartController::class, 'clear'])->name('cart.clear');

            // Checkout
            Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
            Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
            Route::get('/pedido/{order}/confirmacion', [CheckoutController::class, 'confirmation'])->name('checkout.confirmation');
            Route::get('/pedido/{order}/exito', [CheckoutController::class, 'success'])->name('checkout.success');
            Route::get('/pedido/{order}/pendiente', [CheckoutController::class, 'pending'])->name('checkout.pending');
            Route::get('/pedido/{order}/fallido', [CheckoutController::class, 'failure'])->name('checkout.failure');
        });
    });
