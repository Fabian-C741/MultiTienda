<?php

use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TenantApiController;
use App\Http\Controllers\Central\TenantController as CentralTenantController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rutas de API REST para la tienda multitenancy.
| Rate limiting aplicado para prevenir abuso.
|
*/

// API Central para gestión de tenants (sin contexto de tenant)
Route::prefix('v1/central')
    ->middleware(['api', 'throttle:api'])
    ->name('api.central.')
    ->group(function () {
        Route::apiResource('tenants', CentralTenantController::class);
        Route::patch('tenants/{tenant}/toggle-status', [CentralTenantController::class, 'toggleStatus']);
        Route::get('stats', [CentralTenantController::class, 'stats']);
    });

Route::prefix('v1/{tenant}')
    ->middleware(['initialize-tenancy', 'ensure-tenant-context', 'throttle:api'])
    ->name('api.')
    ->group(function () {

        // Información de la tienda
        Route::get('/', [TenantApiController::class, 'info'])->name('tenant.info');

        // Productos (solo lectura, más permisivo)
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductApiController::class, 'index'])->name('index');
            Route::get('/featured', [ProductApiController::class, 'featured'])->name('featured');
            Route::get('/latest', [ProductApiController::class, 'latest'])->name('latest');
            Route::get('/{identifier}', [ProductApiController::class, 'show'])->name('show');
        });

        // Categorías (solo lectura)
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryApiController::class, 'index'])->name('index');
            Route::get('/tree', [CategoryApiController::class, 'tree'])->name('tree');
            Route::get('/{slug}', [CategoryApiController::class, 'show'])->name('show');
            Route::get('/{slug}/products', [CategoryApiController::class, 'products'])->name('products');
        });

        // Carrito (rate limit moderado)
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartApiController::class, 'show'])->name('show');
            Route::post('/add', [CartApiController::class, 'add'])->name('add');
            Route::post('/update', [CartApiController::class, 'update'])->name('update');
            Route::post('/remove', [CartApiController::class, 'remove'])->name('remove');
            Route::post('/clear', [CartApiController::class, 'clear'])->name('clear');
        });

        // Pedidos (rate limit estricto)
        Route::prefix('orders')->middleware('throttle:orders')->name('orders.')->group(function () {
            Route::post('/', [OrderApiController::class, 'store'])->name('store');
            Route::get('/payment-methods', [OrderApiController::class, 'paymentMethods'])->name('payment-methods');
            Route::get('/{orderNumber}', [OrderApiController::class, 'show'])->name('show');
        });
    });

// Webhooks (sin rate limit, pero verificación de firma)
Route::post('webhook/{tenant}/{gateway}', [OrderApiController::class, 'webhook'])
    ->middleware('initialize-tenancy')
    ->name('api.webhook');
