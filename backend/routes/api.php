<?php

use App\Http\Controllers\Api\CartApiController;
use App\Http\Controllers\Api\CategoryApiController;
use App\Http\Controllers\Api\OrderApiController;
use App\Http\Controllers\Api\ProductApiController;
use App\Http\Controllers\Api\TenantApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Rutas de API REST para la tienda multitenancy.
| Todas las rutas requieren el parámetro {tenant} para identificar la tienda.
|
*/

Route::prefix('v1/{tenant}')
    ->middleware('tenant')
    ->name('api.')
    ->group(function () {

        // Información de la tienda
        Route::get('/', [TenantApiController::class, 'info'])->name('tenant.info');

        // Productos
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductApiController::class, 'index'])->name('index');
            Route::get('/featured', [ProductApiController::class, 'featured'])->name('featured');
            Route::get('/latest', [ProductApiController::class, 'latest'])->name('latest');
            Route::get('/{identifier}', [ProductApiController::class, 'show'])->name('show');
        });

        // Categorías
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryApiController::class, 'index'])->name('index');
            Route::get('/tree', [CategoryApiController::class, 'tree'])->name('tree');
            Route::get('/{slug}', [CategoryApiController::class, 'show'])->name('show');
            Route::get('/{slug}/products', [CategoryApiController::class, 'products'])->name('products');
        });

        // Carrito
        Route::prefix('cart')->name('cart.')->group(function () {
            Route::get('/', [CartApiController::class, 'show'])->name('show');
            Route::post('/add', [CartApiController::class, 'add'])->name('add');
            Route::post('/update', [CartApiController::class, 'update'])->name('update');
            Route::post('/remove', [CartApiController::class, 'remove'])->name('remove');
            Route::post('/clear', [CartApiController::class, 'clear'])->name('clear');
        });

        // Pedidos
        Route::prefix('orders')->name('orders.')->group(function () {
            Route::post('/', [OrderApiController::class, 'store'])->name('store');
            Route::get('/payment-methods', [OrderApiController::class, 'paymentMethods'])->name('payment-methods');
            Route::get('/{orderNumber}', [OrderApiController::class, 'show'])->name('show');
            Route::post('/{order}/webhook', [OrderApiController::class, 'webhook'])->name('webhook');
        });
    });
