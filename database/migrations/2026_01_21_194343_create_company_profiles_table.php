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
        Schema::create('company_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
            $table->index('tenant_id');
            
            $table->string('company_name');
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('logo')->nullable();
            $table->string('industry')->nullable();
            $table->integer('company_size')->nullable();
            $table->string('registration_number')->nullable();
            $table->string('tax_number')->nullable();
            $table->string('fiscal_year_start')->default('01-01'); // MM-DD
            $table->string('timezone')->default('UTC');
            $table->string('currency')->default('USD');
            $table->time('work_start_time')->default('09:00:00');
            $table->time('work_end_time')->default('17:00:00');
            $table->json('work_days')->nullable(); // ['monday', 'tuesday', ...]
            $table->json('holidays')->nullable(); // Array of holiday dates
            $table->timestamps();

            // One company profile per tenant
            $table->unique('tenant_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_profiles');
    }
};
