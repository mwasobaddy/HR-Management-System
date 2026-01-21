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
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Free, Plus, Pro, Enterprise
            $table->string('slug')->unique(); // free, plus, pro, enterprise
            $table->text('description')->nullable();
            $table->decimal('price_monthly', 10, 2)->default(0);
            $table->decimal('price_yearly', 10, 2)->default(0);
            $table->integer('max_users')->default(15);
            $table->integer('max_job_posts')->default(15);
            $table->boolean('has_onboarding_framework')->default(false);
            $table->boolean('has_ai_features')->default(false);
            $table->boolean('has_api_access')->default(false);
            $table->boolean('has_payroll')->default(false);
            $table->boolean('has_subdomain')->default(false);
            $table->boolean('has_custom_domain')->default(false);
            $table->string('database_type')->default('shared'); // shared or dedicated
            $table->boolean('is_active')->default(true);
            $table->json('features')->nullable(); // Additional features as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscription_plans');
    }
};
