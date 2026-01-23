<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Concerns\TenantAwareCommand;
use Tests\TestCase;

class TenantTestCase extends TestCase
{
    use RefreshDatabase;

    protected $tenancy = false;
    protected Tenant $tenant;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    protected function initializeTenancy(): void
    {
        $this->tenant = Tenant::factory()->create([
            'id' => 'test-tenant-' . now()->timestamp,
        ]);

        $this->tenant->domains()->create([
            'domain' => 'test-tenant-' . now()->timestamp . '.localhost'
        ]);

        tenancy()->initialize($this->tenant);
    }

    protected function tearDown(): void
    {
        if ($this->tenancy) {
            tenancy()->end();
        }

        parent::tearDown();
    }
}