<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\DatabaseConfig;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory;

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'onboarding_completed' => 'boolean',
        'is_demo' => 'boolean',
    ];

    /**
     * Get the subscription plan for the tenant.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Check if tenant is on trial.
     */
    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial' 
            && $this->trial_ends_at 
            && $this->trial_ends_at->isFuture();
    }

    /**
     * Check if tenant subscription is active.
     */
    public function isActive(): bool
    {
        return $this->subscription_status === 'active' 
            && ($this->subscription_ends_at === null || $this->subscription_ends_at->isFuture());
    }

    /**
     * Check if tenant can access a feature.
     */
    public function hasFeature(string $feature): bool
    {
        if (!$this->plan) {
            return false;
        }

        return $this->plan->{'has_' . $feature} ?? false;
    }

    /**
     * Check if tenant has reached user limit.
     */
    public function hasReachedUserLimit(): bool
    {
        if (!$this->plan || $this->plan->max_users === -1) {
            return false;
        }

        // This will be implemented when we add User model
        return false; // TODO: Implement actual user count check
    }

    /**
     * Check if tenant has reached job post limit.
     */
    public function hasReachedJobPostLimit(): bool
    {
        if (!$this->plan || $this->plan->max_job_posts === -1) {
            return false;
        }

        // This will be implemented when we add JobPost model
        return false; // TODO: Implement actual job post count check
    }

    /**
     * Check if this is a demo tenant.
     */
    public function isDemo(): bool
    {
        return $this->is_demo;
    }

    /**
     * Get database configuration for this tenant.
     */
    public function database(): DatabaseConfig
    {
        $config = new DatabaseConfig($this);

        // For shared database, use the central database connection
        if ($this->database_type === 'shared') {
            $config->name = config('database.connections.' . config('tenancy.database.central_connection') . '.database');
        } else {
            // For dedicated database, generate a unique database name
            $config->name = $this->database_name ?? config('tenancy.database.prefix') . $this->id . config('tenancy.database.suffix');
        }

        return $config;
    }
}
