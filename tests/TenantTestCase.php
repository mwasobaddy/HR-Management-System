<?php

namespace Tests;

use App\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Stancl\Tenancy\Concerns\TenantAwareCommand;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;
use Tests\TestCase;

class TenantTestCase extends TestCase
{
    use RefreshDatabase;

    protected bool $tenancy = false;
    protected Tenant $tenant;
    protected string $tenantDomain;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    protected function initializeTenancy(): void
    {
        $domain = 'test-tenant-' . now()->timestamp . '.localhost';

        $this->tenant = Tenant::factory()->create([
            'id' => 'test-tenant-' . now()->timestamp,
        ]);

        $this->tenant->domains()->create([
            'domain' => $domain,
        ]);

        $this->tenantDomain = $domain;
        $this->baseUrl = 'http://' . $this->tenantDomain;

        tenancy()->initialize($this->tenant);

        $this->withoutMiddleware([
            InitializeTenancyByDomain::class,
            PreventAccessFromCentralDomains::class,
        ]);
    }

    protected function tenantRequestHeaders(): array
    {
        return ['HTTP_HOST' => $this->tenantDomain];
    }

    protected function tearDown(): void
    {
        if ($this->tenancy) {
            tenancy()->end();
        }

        parent::tearDown();
    }
}