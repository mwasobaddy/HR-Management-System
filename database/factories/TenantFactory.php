<?php

namespace Database\Factories;

use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant>
 */
class TenantFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Tenant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'domain' => Str::slug(fake()->unique()->company()) . '.localhost',
            'trial_ends_at' => now()->addDays(14),
            'subscription_ends_at' => null,
            'onboarding_completed' => false,
            'is_demo' => false,
        ];
    }

    /**
     * Indicate that the tenant is a demo tenant.
     */
    public function demo(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_demo' => true,
            'trial_ends_at' => now()->addDays(30),
        ]);
    }
}