<?php

namespace App\Models;

use App\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use BelongsToTenant;

    /**
     * Get the name of the tenant ID column.
     */
    public function getTenantIdColumn(): string
    {
        return 'tenant_id';
    }

    protected $fillable = [
        'tenant_id',
        'name',
        'branch_name',
        'description',
        'manager_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get the tenant that owns the department.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the manager of the department.
     */
    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    /**
     * Get all users in the department.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }
}
