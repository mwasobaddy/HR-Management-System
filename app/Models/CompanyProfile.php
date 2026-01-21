<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class CompanyProfile extends Model
{
    use BelongsToTenant;

    protected $fillable = [
        'tenant_id',
        'company_name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'logo',
        'industry',
        'company_size',
        'registration_number',
        'tax_number',
        'fiscal_year_start',
        'timezone',
        'currency',
        'work_start_time',
        'work_end_time',
        'work_days',
        'holidays',
    ];

    protected $casts = [
        'work_days' => 'array',
        'holidays' => 'array',
    ];

    /**
     * Get the tenant that owns the company profile.
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }
}
