<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\DatabaseConfig;

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
     * Define custom columns that should be stored as real database columns
     * instead of in the data JSON column.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'slug',
            'company_name',
            'plan_id',
            'subscription_status',
            'trial_ends_at',
            'subscription_ends_at',
            'subscription_type',
            'onboarding_completed',
            'database_type',
            'database_name',
            'is_demo',
        ];
    }

    /**
     * Get the subscription plan for the tenant.
     */
    public function plan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }

    /**
     * Get all users for this tenant.
     */
    public function users()
    {
        return $this->hasMany(User::class);
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
     * Check if subscription has expired.
     */
    public function isExpired(): bool
    {
        if ($this->subscription_status === 'trial') {
            return $this->trial_ends_at && $this->trial_ends_at->isPast();
        }

        return $this->subscription_ends_at && $this->subscription_ends_at->isPast();
    }

    /**
     * Check if tenant can access a feature.
     */
    public function hasFeature(string $feature): bool
    {
        if (! $this->plan) {
            return false;
        }

        return $this->plan->{'has_'.$feature} ?? false;
    }

    /**
     * Get current user count.
     */
    public function getUserCount(): int
    {
        return $this->users()->count();
    }

    /**
     * Check if tenant has reached user limit.
     */
    public function hasReachedUserLimit(): bool
    {
        if (! $this->plan || $this->plan->max_users === -1) {
            return false;
        }

        return $this->getUserCount() >= $this->plan->max_users;
    }

    /**
     * Check if tenant can add more users.
     */
    public function canAddUsers(int $count = 1): bool
    {
        if (! $this->plan || $this->plan->max_users === -1) {
            return true;
        }

        return ($this->getUserCount() + $count) <= $this->plan->max_users;
    }

    /**
     * Check if tenant has reached job post limit.
     */
    public function hasReachedJobPostLimit(): bool
    {
        if (! $this->plan || $this->plan->max_job_posts === -1) {
            return false;
        }

        // TODO: Implement when JobPost model is available
        // return $this->jobPosts()->count() >= $this->plan->max_job_posts;
        return false;
    }

    /**
     * Check if this is a demo tenant.
     */
    public function isDemo(): bool
    {
        return $this->is_demo;
    }

    /**
     * Get days remaining in trial/subscription.
     */
    public function getDaysRemaining(): ?int
    {
        $endDate = $this->subscription_status === 'trial'
            ? $this->trial_ends_at
            : $this->subscription_ends_at;

        if (! $endDate) {
            return null;
        }

        return max(0, now()->diffInDays($endDate, false));
    }

    /**
     * Renew subscription for another billing cycle.
     */
    public function renewSubscription(): void
    {
        $this->subscription_ends_at = now()->addMonth();
        $this->subscription_status = 'active';
        $this->save();
    }

    /**
     * Suspend tenant subscription.
     */
    public function suspend(): void
    {
        $this->subscription_status = 'suspended';
        $this->save();
    }

    /**
     * Cancel tenant subscription.
     */
    public function cancel(): void
    {
        $this->subscription_status = 'cancelled';
        $this->save();
    }

    /**
     * Upgrade tenant to a new plan.
     */
    public function upgradePlan(SubscriptionPlan $newPlan): void
    {
        $this->plan_id = $newPlan->id;
        $this->database_type = $newPlan->database_type;
        $this->save();
    }

    /**
     * Get database configuration for this tenant.
     */
    public function database(): DatabaseConfig
    {
        $config = new DatabaseConfig($this);

        if ($this->database_type === 'shared') {
            $config->name = config('database.connections.'.config('tenancy.database.central_connection').'.database');
        } else {
            $config->name = $this->database_name ?? config('tenancy.database.prefix').$this->id.config('tenancy.database.suffix');
        }

        return $config;
    }

    /**
     * Scope query to only active tenants.
     */
    public function scopeActive($query)
    {
        return $query->where('subscription_status', 'active')
            ->where(function ($q) {
                $q->whereNull('subscription_ends_at')
                    ->orWhere('subscription_ends_at', '>', now());
            });
    }

    /**
     * Scope query to only trial tenants.
     */
    public function scopeOnTrial($query)
    {
        return $query->where('subscription_status', 'trial')
            ->where('trial_ends_at', '>', now());
    }

    /**
     * Scope query to expired tenants.
     */
    public function scopeExpired($query)
    {
        return $query->where(function ($q) {
            $q->where('subscription_status', 'trial')
                ->where('trial_ends_at', '<=', now());
        })->orWhere(function ($q) {
            $q->where('subscription_status', 'active')
                ->where('subscription_ends_at', '<=', now());
        });
    }
}
