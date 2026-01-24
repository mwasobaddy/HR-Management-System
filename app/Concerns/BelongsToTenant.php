<?php

namespace App\Concerns;

use App\Database\TenantScope;
use Stancl\Tenancy\Contracts\Tenant;

/**
 * @property-read Tenant $tenant
 */
trait BelongsToTenant
{
    public static $tenantIdColumn = 'tenant_id';

    /**
     * Get the name of the tenant ID column.
     */
    public function getTenantIdColumn(): string
    {
        return static::$tenantIdColumn;
    }

    public function tenant()
    {
        return $this->belongsTo(config('tenancy.tenant_model'), $this->getTenantIdColumn());
    }

    public static function bootBelongsToTenant()
    {
        static::addGlobalScope(new TenantScope);

        static::creating(function ($model) {
            if (! $model->getAttribute($model->getTenantIdColumn()) && ! $model->relationLoaded('tenant')) {
                if (tenancy()->initialized) {
                    $model->setAttribute($model->getTenantIdColumn(), tenant()->getTenantKey());
                    $model->setRelation('tenant', tenant());
                }
            }
        });
    }
}
