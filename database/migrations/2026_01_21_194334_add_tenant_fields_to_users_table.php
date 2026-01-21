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
        Schema::table('users', function (Blueprint $table) {
            // Add tenant_id for single-database tenancy
            $table->string('tenant_id')->after('id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index('tenant_id');

            // Make email unique per tenant instead of globally
            $table->dropUnique(['email']);
            $table->unique(['tenant_id', 'email']);

            // Add HRMS-specific fields
            $table->string('role')->default('employee')->after('password'); // admin, hr_manager, manager, employee
            $table->string('employee_id')->nullable()->after('role');
            $table->string('phone')->nullable()->after('employee_id');
            $table->string('avatar')->nullable()->after('phone');
            $table->boolean('is_active')->default(true)->after('avatar');
            $table->foreignId('department_id')->nullable()->after('is_active')->constrained('departments')->nullOnDelete();

            // Add unique constraint for employee_id per tenant
            $table->unique(['tenant_id', 'employee_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['department_id']);
            $table->dropForeign(['tenant_id']);
            $table->dropUnique(['tenant_id', 'email']);
            $table->dropUnique(['tenant_id', 'employee_id']);
            $table->dropColumn(['tenant_id', 'role', 'employee_id', 'phone', 'avatar', 'is_active', 'department_id']);
            $table->unique('email');
        });
    }
};
