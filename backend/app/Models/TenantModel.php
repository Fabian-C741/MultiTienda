<?php

declare(strict_types=1);

namespace App\Models;

use App\Services\Tenancy\TenantManager;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use RuntimeException;

abstract class TenantModel extends Model
{
    protected $connection = 'tenant';

    /**
     * Boot del modelo para asegurar contexto de tenant.
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new TenantScope);
        
        // Verificar contexto de tenant al crear/actualizar
        static::creating(function ($model) {
            if (!app(TenantManager::class)->current()) {
                throw new RuntimeException(
                    'No se puede crear un registro de tenant sin contexto de tenant activo'
                );
            }
        });

        static::updating(function ($model) {
            if (!app(TenantManager::class)->current()) {
                throw new RuntimeException(
                    'No se puede actualizar un registro de tenant sin contexto de tenant activo'
                );
            }
        });
    }

    /**
     * Obtiene el tenant actual.
     */
    protected function getCurrentTenant(): ?Tenant
    {
        return app(TenantManager::class)->current();
    }

    /**
     * Verifica si el modelo está en el contexto correcto del tenant.
     */
    public function isInTenantContext(): bool
    {
        $currentTenant = $this->getCurrentTenant();
        
        if (!$currentTenant) {
            return false;
        }

        return $this->getConnectionName() === 'tenant';
    }

    /**
     * Override para asegurar que las relaciones usen la conexión correcta.
     */
    public function newRelatedInstance($class): Model
    {
        $instance = new $class;
        
        // Si es un TenantModel, asegurar que use la conexión tenant
        if ($instance instanceof TenantModel) {
            $instance->setConnection($this->getConnectionName());
        }
        
        return $instance;
    }
}

/**
 * Scope global para modelos de tenant.
 */
class TenantScope implements \Illuminate\Database\Eloquent\Scope
{
    public function apply(\Illuminate\Database\Eloquent\Builder $builder, Model $model): void
    {
        // El scope se aplica automáticamente por usar la conexión 'tenant'
        // que ya está configurada para el tenant actual
    }
}
