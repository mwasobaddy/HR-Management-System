<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed subscription plans
        $this->call([
            SubscriptionPlanSeeder::class,
            TenantPermissionsSeeder::class,
            DemoTenantSeeder::class,
        ]);
    }
}
