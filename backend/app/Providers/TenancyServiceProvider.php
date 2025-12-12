<?php

declare(strict_types=1);

namespace App\Providers;

use App\Models\Product;
use App\Models\Tenant;
use App\Services\Tenancy\TenantManager;
use App\Services\Tenancy\TenantResolver;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class TenancyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(TenantManager::class, fn () => new TenantManager());
        $this->app->singleton(TenantResolver::class, fn () => new TenantResolver());
    }

    public function boot(): void
    {
        $this->configureBindings();
        $this->configureConnections();
    }

    protected function configureBindings(): void
    {
        Route::model('tenant', Tenant::class);
        Route::bind('product', function ($value, $route) {
            $tenantParam = $route->parameter('tenant');

            if (!$tenantParam) {
                abort(404);
            }

            $tenant = $tenantParam instanceof Tenant
                ? $tenantParam
                : Tenant::query()->where('slug', $tenantParam)->first();

            if (!$tenant) {
                abort(404);
            }

            return app(TenantManager::class)->runFor($tenant, static fn () => Product::query()->findOrFail($value));
        });
    }

    protected function configureConnections(): void
    {
        Config::set('database.connections.tenant', Config::get('database.connections.mysql'));
    }
}
