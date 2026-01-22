<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price_monthly',
        'price_yearly',
        'max_users',
        'max_job_posts',
        'has_onboarding_framework',
        'has_ai_features',
        'has_api_access',
        'has_payroll',
        'has_subdomain',
        'has_custom_domain',
        'database_type',
        'is_active',
        'features',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price_monthly' => 'decimal:2',
        'price_yearly' => 'decimal:2',
        'has_onboarding_framework' => 'boolean',
        'has_ai_features' => 'boolean',
        'has_api_access' => 'boolean',
        'has_payroll' => 'boolean',
        'has_subdomain' => 'boolean',
        'has_custom_domain' => 'boolean',
        'is_active' => 'boolean',
        'features' => 'array',
    ];

    /**
     * Get the tenants for this plan.
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'plan_id');
    }

    /**
     * Check if this is the free plan.
     */
    public function isFree(): bool
    {
        return $this->slug === 'free';
    }

    /**
     * Check if plan uses shared database.
     */
    public function usesSharedDatabase(): bool
    {
        return $this->database_type === 'shared';
    }

    /**
     * Check if plan uses dedicated database.
     */
    public function usesDedicatedDatabase(): bool
    {
        return $this->database_type === 'dedicated';
    }

    /**
     * Check if plan has unlimited users.
     */
    public function hasUnlimitedUsers(): bool
    {
        return $this->max_users === -1;
    }

    /**
     * Check if plan has unlimited job posts.
     */
    public function hasUnlimitedJobPosts(): bool
    {
        return $this->max_job_posts === -1;
    }

    /**
     * Get yearly price savings compared to monthly.
     */
    public function getYearlySavings(): float
    {
        if ($this->price_monthly == 0) {
            return 0;
        }

        $monthlyTotal = $this->price_monthly * 12;
        return $monthlyTotal - $this->price_yearly;
    }

    /**
     * Get yearly savings as a percentage.
     */
    public function getYearlySavingsPercentage(): float
    {
        if ($this->price_monthly == 0) {
            return 0;
        }

        $monthlyTotal = $this->price_monthly * 12;
        if ($monthlyTotal == 0) {
            return 0;
        }

        return (($monthlyTotal - $this->price_yearly) / $monthlyTotal) * 100;
    }

    /**
     * Scope query to only active plans.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Check if plan includes a specific feature.
     */
    public function hasFeature(string $feature): bool
    {
        // Check boolean feature flags
        $featureColumn = 'has_' . $feature;
        if (isset($this->attributes[$featureColumn])) {
            return (bool) $this->$featureColumn;
        }

        // Check in features array
        if (is_array($this->features)) {
            return in_array($feature, $this->features);
        }

        return false;
    }
}

