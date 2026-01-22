<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Stancl\Tenancy\Database\Models\Domain;

class UniqueTenantDomain implements Rule
{
    protected string $suffix;

    public function __construct(string $suffix = '.hrms.test')
    {
        $this->suffix = $suffix;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $fullDomain = $value . $this->suffix;
        return !Domain::where('domain', $fullDomain)->exists();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'This domain is already taken.';
    }
}