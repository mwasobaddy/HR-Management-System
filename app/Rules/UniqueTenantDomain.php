<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class UniqueTenantDomain implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     */
    public function passes($attribute, $value): bool
    {
        $fullDomain = $this->buildTenantDomain($value);

        return ! Domain::where('domain', $fullDomain)->exists();
    }

    /**
     * Get the validation error message.
     */
    public function message(): string
    {
        return 'This domain is already taken.';
    }

    /**
     * Build the fully qualified tenant domain using configured base and optional local prefix.
     */
    protected function buildTenantDomain(string $subdomain): string
    {
        $baseDomain = $this->resolveBaseDomain();
        $localPrefix = $this->resolveLocalPrefix();

        $segments = array_filter([
            $localPrefix,
            $subdomain,
            $baseDomain,
        ]);

        return implode('.', $segments);
    }

    protected function resolveBaseDomain(): string
    {
        if (app()->environment('production')) {
            return ltrim((string) (config('tenancy.tenant_production_base_domain') ?: config('tenancy.tenant_base_domain')), '.');
        }

        return ltrim((string) (config('tenancy.tenant_local_base_domain') ?: config('tenancy.tenant_base_domain')), '.');
    }

    protected function resolveLocalPrefix(): ?string
    {
        if (app()->environment('production')) {
            return null;
        }

        $prefix = trim((string) (config('tenancy.tenant_local_prefix') ?? ''));

        return $prefix !== '' ? $prefix : null;
    }
}
