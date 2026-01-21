<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('company_name')->nullable()->after('id');
            $table->string('slug')->unique()->nullable()->after('company_name');
            $table->foreignId('plan_id')->nullable()->constrained('subscription_plans')->after('slug');
            $table->string('subscription_status')->default('trial')->after('plan_id'); // trial, active, suspended, cancelled
            $table->timestamp('trial_ends_at')->nullable()->after('subscription_status');
            $table->timestamp('subscription_ends_at')->nullable()->after('trial_ends_at');
            $table->boolean('onboarding_completed')->default(false)->after('subscription_ends_at');
            $table->string('database_type')->default('shared')->after('onboarding_completed'); // shared or dedicated
            $table->string('database_name')->nullable()->after('database_type'); // For dedicated databases
            $table->boolean('is_demo')->default(false)->after('database_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropForeign(['plan_id']);
            $table->dropColumn([
                'company_name',
                'slug',
                'plan_id',
                'subscription_status',
                'trial_ends_at',
                'subscription_ends_at',
                'onboarding_completed',
                'database_type',
                'database_name',
                'is_demo',
            ]);
        });
    }
};
