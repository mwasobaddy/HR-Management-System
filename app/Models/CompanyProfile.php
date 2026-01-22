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
        'address_line_2',
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
        'working_hours',
        'holidays',
        'ai_provider',
        'ai_model',
        'ai_api_key',
        'google_calendar_api_key',
        'google_meet_api_key',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'smtp_from_address',
        'smtp_from_name',
    ];

    protected $casts = [
        'working_hours' => 'array',
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
