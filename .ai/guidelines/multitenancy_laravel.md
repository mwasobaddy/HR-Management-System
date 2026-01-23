# Tenancy for Laravel - Comprehensive Guide for AI Agents

## Package Information
- **Package Name**: `stancl/tenancy`
- **Version**: 3.x
- **Repository**: https://github.com/stancl/tenancy
- **Documentation**: https://tenancyforlaravel.com/docs/v3

## What is Multi-Tenancy?

Multi-tenancy is the ability to provide a service to multiple users (tenants) from a single hosted instance of an application, rather than deploying the application separately for each user. This package is built around the idea that multi-tenancy usually means letting tenants have their own users with their own resources (e.g., todo tasks), not just users having tasks.

### Simple vs Package-Based Multi-Tenancy
- For simple scoping (e.g., `auth()->user()->tasks()`), no package is needed
- This package is for complex scenarios where tenants have their own users with their own resources

## Types of Multi-Tenancy

### 1. Single-Database Tenancy
- Tenants share one database
- Data separated using clauses like `WHERE tenant_id = 1`
- Lower devops complexity, higher code complexity
- Requires manual scoping

### 2. Multi-Database Tenancy
- Each tenant has their own database
- Higher devops complexity, lower code complexity
- Package focuses primarily on this approach
- Automatic scoping and isolation

## Tenancy Modes

### 1. Automatic Mode (Default)
After tenant identification, the package automatically:
- Switches database connections
- Replaces CacheManager with scoped version
- Suffixes filesystem paths
- Makes queues tenant-aware

### 2. Manual Mode
- Tenant identification only
- Manual scoping using model traits
- More control, more work

## Core Concepts

### The Two Applications

#### Central Application
- Executes when there is no tenant
- Contains signup pages where tenants are created
- Admin panel for managing tenants
- Landing pages and marketing content

#### Tenant Application
- Executes in tenant context (with tenant's database, cache, etc.)
- Contains the actual service used by tenants
- Larger part of the application

### Central Domains
Domains that serve central app content. Must be configured in `config/tenancy.php`:

```php
'central_domains' => [
    'saas.test',  // Development
    // or for Laravel Sail:
    '127.0.0.1',
    'localhost',
],
```

## Installation

### 1. Install Package
```bash
composer require stancl/tenancy
```

### 2. Run Installation Command
```bash
php artisan tenancy:install
```

This creates:
- Migrations
- Config file
- Route file (`routes/tenant.php`)
- Service provider (`app/Providers/TenancyServiceProvider.php`)

### 3. Run Migrations
```bash
php artisan migrate
```

### 4. Register Service Provider
In `bootstrap/providers.php`:
```php
return [
    App\Providers\AppServiceProvider::class,
    App\Providers\TenancyServiceProvider::class, // Add this
];
```

## Tenant Model Configuration

### Creating Custom Tenant Model
Most applications need domain/subdomain identification and tenant databases.

Create `app/Models/Tenant.php`:
```php
<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;
}
```

### Configure Tenant Model
In `config/tenancy.php`:
```php
'tenant_model' => \App\Models\Tenant::class,
```

### Tenant Model Features

#### Built-in Features
- Forced central connection (interact with tenants even in tenant context)
- Data column trait (store arbitrary keys in JSON)
- ID generation (auto-generates UUID if not supplied)

#### Custom Columns
Attributes without columns are stored in `data` JSON column:
```php
$tenant->update([
    'customAttribute' => 'value', // Stored in data column
    'plan' => 'free', // Stored in dedicated column
]);
```

Define custom columns:
```php
public static function getCustomColumns(): array
{
    return [
        'id',
        'plan',
        // Add more custom columns here
    ];
}
```

#### Querying Data Column
```php
// Query data inside data column
Tenant::where('data->foo', 'bar')->get();
```

**Best Practice**: Use dedicated columns for frequently queried data.

#### Using Incrementing IDs
By default, uses UUIDs. To use auto-incrementing IDs:
```php
public function getIncrementing()
{
    return true;
}
```

### Creating Tenants
```php
$tenant = Tenant::create(['plan' => 'free']);
```

Events fire automatically, creating and migrating database.

### Running Code in Tenant Context
```php
$tenant->run(function () {
    User::create([...]);
});
```

### Accessing Current Tenant
```php
// Using helper
$tenant = tenant();
$tenantId = tenant('id');

// Or via dependency injection
public function __construct(\Stancl\Tenancy\Contracts\Tenant $tenant)
{
    // ...
}
```

## Domains

### Domain Model
Represents tenant domains/subdomains. Relationship: `Tenant hasMany Domain`

### Creating Domains
```php
$tenant->domains()->create(['domain' => 'foo.localhost']);
```

### Domain Storage
- For domain identification: store full hostname (e.g., `acme.com`)
- For subdomain identification: store subdomain only (e.g., `acme`)
- For combined: records with dots = domains, without dots = subdomains

## Tenant Identification

The package provides multiple identification methods:

### 1. Domain Identification
```php
// Middleware
InitializeTenancyByDomain::class

// Example: acme.com
$tenant->domains()->create(['domain' => 'acme.com']);
```

### 2. Subdomain Identification
```php
// Middleware
InitializeTenancyBySubdomain::class

// Example: acme.yoursaas.com
$tenant->domains()->create(['domain' => 'acme']);
```

### 3. Combined Domain/Subdomain
```php
// Middleware
InitializeTenancyByDomainOrSubdomain::class

// Records with dots = domains (foo.bar.com)
// Records without dots = subdomains (foo)
```

### 4. Path Identification
```php
// Middleware
InitializeTenancyByPath::class

// Routes must be prefixed with /{tenant}
Route::group([
    'prefix' => '/{tenant}',
    'middleware' => [InitializeTenancyByPath::class],
], function () {
    Route::get('/foo', 'FooController@index');
});
```

### 5. Request Data Identification
```php
// Middleware
InitializeTenancyByRequestData::class

// Default: looks for X-Tenant header, then tenant query parameter
// Customize:
InitializeTenancyByRequestData::$header = 'X-Team';
InitializeTenancyByRequestData::$queryParameter = null; // Disable query param
```

### Customizing onFail Logic
```php
\Stancl\Tenancy\Middleware\InitializeTenancyByDomain::$onFail = function ($exception, $request, $next) {
    return redirect('https://my-central-domain.com/');
};
```

## Routes

### Central Routes Structure

#### Laravel 11+ (No RouteServiceProvider)
Wrap routes in `Route::domain()` groups:
```php
// routes/web.php
foreach (config('tenancy.central_domains') as $domain) {
    Route::domain($domain)->group(function () {
        // Your central routes here
        Route::get('/', [HomeController::class, 'index']);
    });
}
```

#### Laravel 10 and Below (With RouteServiceProvider)
```php
// app/Providers/RouteServiceProvider.php
protected function mapWebRoutes()
{
    foreach ($this->centralDomains() as $domain) {
        Route::middleware('web')
            ->domain($domain)
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }
}

protected function centralDomains(): array
{
    return config('tenancy.central_domains', []);
}
```

### Tenant Routes
Default structure in `routes/tenant.php`:
```php
Route::middleware([
    'web',
    InitializeTenancyByDomain::class,
    PreventAccessFromCentralDomains::class,
])->group(function () {
    Route::get('/', function () {
        return 'Tenant: ' . tenant('id');
    });
});
```

**Important**: Use `PreventAccessFromCentralDomains` middleware to prevent tenant routes from being accessible on central domains.

### Route Precedence
- Tenant routes take precedence over central routes
- Always use `PreventAccessFromCentralDomains` middleware

## Migrations

### Tenant Migrations Location
Move tenant-specific migrations to `database/migrations/tenant/`

```bash
# Example: Move users table to tenant migrations
mv database/migrations/0001_01_01_000000_create_users_table.php \
   database/migrations/tenant/
```

### Running Tenant Migrations
```bash
# All tenants
php artisan tenants:migrate

# Specific tenant
php artisan tenants:migrate --tenants=8075a580-1cb8-11e9-8822-49c5d8f8ff23

# Custom path
php artisan tenants:migrate --path=database/migrations/custom
```

### Configuring Migration Paths
In `config/tenancy.php`:
```php
'migration_parameters' => [
    '--path' => [
        'database/migrations/tenant',
        'app/Modules/*/Database/Migrations/tenant',
    ],
],
```

### Migration Naming
All migrations share the same PHP namespace. Even if using the same table name in central and tenant databases, use different class names.

## Event System

### How Events Work

1. Request hits identification middleware
2. Middleware finds tenant and calls `tenancy()->initialize($tenant)`
3. `Tenancy` class sets tenant and fires `TenancyInitialized` event
4. `BootstrapTenancy` listener executes tenancy bootstrappers
5. Bootstrappers transition app to tenant context
6. `TenancyBootstrapped` event fires

### Key Events

#### Tenancy Lifecycle
- `InitializingTenancy` → `TenancyInitialized`
- `BootstrappingTenancy` → `TenancyBootstrapped`
- `EndingTenancy` → `TenancyEnded`
- `RevertingToCentralContext` → `RevertedToCentralContext`

#### Tenant Events (Most Important)
- `TenantCreated` ⭐
- `TenantDeleted` ⭐
- `TenantUpdated`
- `TenantSaved`

#### Domain Events
- `DomainCreated` ⭐
- `DomainDeleted` ⭐
- `DomainUpdated`

#### Database Events (Multi-Database Only)
- `DatabaseCreated` ⭐
- `DatabaseDeleted` ⭐
- `DatabaseMigrated`
- `DatabaseSeeded`
- `DatabaseRolledBack`

**Note**: Database events fire in tenant context.

### Job Pipelines

Convert jobs into event listeners using JobPipeline:

```php
use Stancl\JobPipeline\JobPipeline;
use Stancl\Tenancy\Events\TenantCreated;
use Stancl\Tenancy\Jobs\{CreateDatabase, MigrateDatabase, SeedDatabase};

// In TenancyServiceProvider
Event::listen(TenantCreated::class, JobPipeline::make([
    CreateDatabase::class,
    MigrateDatabase::class,
    SeedDatabase::class,
])->send(function (TenantCreated $event) {
    return $event->tenant;
})->shouldBeQueued(false)->toListener());
```

**Benefits**:
- Jobs run sequentially in correct order
- Can be queued or synchronous
- Single job or multiple jobs
- Automatic event-to-job data passing

## Tenancy Bootstrappers

Bootstrappers transition the application into tenant context. Configure in `config/tenancy.php`:

```php
'bootstrappers' => [
    Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
    // Add custom bootstrappers here
],
```

### Common Bootstrappers

#### DatabaseTenancyBootstrapper
- Switches default database connection to tenant's database
- Essential for multi-database tenancy

#### CacheTenancyBootstrapper
- Scopes cache to current tenant
- Adds tenant-specific cache tags

#### FilesystemTenancyBootstrapper
- Suffixes filesystem paths with tenant ID
- Isolates tenant files

#### QueueTenancyBootstrapper
- Makes queued jobs tenant-aware
- Stores tenant ID with job
- Initializes tenancy when processing

## Queues

### Automatic Tenant Awareness
With `QueueTenancyBootstrapper`, jobs dispatched from tenant context are automatically tenant-aware.

### Important Considerations

#### Database Queue Driver

Force database queue to use central connection:
```php
// config/queue.php
'connections' => [
    'database' => [
        'driver' => 'database',
        'connection' => 'central',
        'after_commit' => true, // CRITICAL: Prevents lost jobs when dispatching inside DB transactions
        // ...
    ],
],
```

**Why 'after_commit' matters:**
If you dispatch queued jobs (including notifications) inside a database transaction, you must set `'after_commit' => true` on your queue connection. Otherwise, jobs may never be created if the transaction is not committed, leading to lost or missing jobs. This is especially important in multi-tenant applications where tenant creation and onboarding often use transactions.

Force database queue to use central connection:
```php
// config/queue.php
'connections' => [
    'database' => [
        'connection' => 'central', // Add this
        // ... other config
    ],
],
```

#### Redis Queue Driver
Ensure Redis connection is not in `tenancy.redis.prefixed_connections`.

### Central Queues
Create dedicated central queue connection:
```php
// config/queue.php
'central' => [
    'driver' => 'database',
    'table' => 'jobs',
    'queue' => 'default',
    'retry_after' => 90,
    'central' => true, // Important!
],
```

Dispatch to central queue:
```php
dispatch(new SomeJob(...))->onConnection('central');
```

## Multi-Database Tenancy

### Database Managers
Manage tenant database creation and deletion. Support for:
- MySQL
- PostgreSQL
- SQLite
- PostgreSQL with schemas (one schema per tenant)

### Configuration
In `config/tenancy.php`:
```php
'database' => [
    'based_on' => null, // Template database
    'managers' => [
        'mysql' => Stancl\Tenancy\TenantDatabaseManagers\MySQLDatabaseManager::class,
        'pgsql' => Stancl\Tenancy\TenantDatabaseManagers\PostgreSQLDatabaseManager::class,
        'sqlite' => Stancl\Tenancy\TenantDatabaseManagers\SQLiteDatabaseManager::class,
    ],
],
```

### Automatic Database Setup
By default, when a tenant is created:
1. `CreateDatabase` job runs
2. `MigrateDatabase` job runs
3. Optionally `SeedDatabase` runs

This happens via JobPipeline in `TenancyServiceProvider`.

## Single-Database Tenancy

### When to Use
- Too many shared resources between tenants
- Don't want many cross-database queries
- Accept higher code complexity for lower devops complexity

### Setup

1. **Disable Database Bootstrapper**
```php
// config/tenancy.php
'bootstrappers' => [
    // Comment out or remove:
    // Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
    // ... other bootstrappers
],
```

2. **Disable Database Jobs**
Remove from `TenancyServiceProvider` event mappings:
```php
// Remove: CreateDatabase, MigrateDatabase, SeedDatabase
```

### Model Types in Single-Database Tenancy

#### 1. Primary Models (Direct Tenant Relationship)
Models that directly belong to tenants:
```php
class Post extends Model
{
    use BelongsToTenant;

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
```

#### 2. Secondary Models (Indirect Tenant Relationship)
Models that belong to tenants through parent models:
```php
class Comment extends Model
{
    use BelongsToPrimaryModel;

    public function getRelationshipToPrimaryModel(): string
    {
        return 'post';
    }

    public function post()
    {
        return $this->belongsTo(Post::class);
    }
}
```

#### 3. Global Models
Models not scoped to any tenant.

### Database Considerations

#### Unique Indexes
```php
// Primary models
$table->unique(['tenant_id', 'slug']);

// Secondary models
$table->unique(['post_id', 'user_id']);
```

#### Validation Rules
```php
use Illuminate\Validation\Rule;

$rules = [
    'slug' => Rule::unique('posts', 'slug')->where('tenant_id', tenant('id')),
    'email' => Rule::exists('users', 'email')->where('tenant_id', tenant('id')),
];

// Or with HasScopedValidationRules trait:
$rules = [
    'slug' => $tenant->unique('posts', 'slug'),
    'email' => $tenant->exists('users', 'email'),
];
```

#### Making Global Queries
```php
// Disable tenant scoping
Post::withoutTenancy()->get();
```

#### Customizing Column Name
```php
// In service provider
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

BelongsToTenant::$tenantIdColumn = 'team_id';
```

### Best Practices
- Avoid direct queries like `Comment::all()` on secondary models
- Access secondary models through parent relationships
- Use dedicated columns for frequently queried data
- Be careful with `DB` facade calls (not automatically scoped)

## Console Commands

### Tenant-Aware Commands
Run for all tenants by default. Use `--tenants` option for specific tenants.

#### Migrate
```bash
# All tenants
php artisan tenants:migrate

# Specific tenants
php artisan tenants:migrate --tenants=tenant-id-1 --tenants=tenant-id-2

# Custom path
php artisan tenants:migrate --path=database/migrations/custom
```

#### Rollback
```bash
php artisan tenants:rollback
php artisan tenants:rollback --tenants=tenant-id
```

#### Seed
```bash
php artisan tenants:seed
php artisan tenants:seed --class=TenantDatabaseSeeder
```

#### Migrate Fresh
```bash
# Runs db:wipe and tenants:migrate
php artisan tenants:migrate-fresh
```

#### Run Custom Commands
```bash
php artisan tenants:run email:send \
  --tenants=tenant-id \
  --option="queue=1" \
  --option="subject=New Feature" \
  --argument="body=We launched a new feature"
```

From code:
```php
Artisan::call('tenants:run', [
    'commandname' => 'email:send',
    '--tenants' => ['tenant-id'],
    '--option' => ['queue=1', 'subject=New Feature'],
    '--argument' => ['body=Message here'],
]);
```

#### List Tenants
```bash
php artisan tenants:list
```

#### Clear Tenant Cache
```bash
# Clear specific tenant cache
php artisan cache:clear --tags=tenantTENANT_ID_HERE

# Tag format: config('tenancy.cache.tag_base') . $id
```

## Testing

### Event Faking Caveat
The package uses events heavily. Be selective with `Event::fake()`:

```php
// Good - specific events
Event::fake([MyEvent::class]);

// Bad - breaks tenancy
Event::fake();
```

### Testing Central App
Write normal Laravel tests.

### Testing Tenant App

**Note**: Cannot use `:memory:` SQLite or `RefreshDatabase` trait with multi-database automatic mode.

#### Setup Pattern 1: Property-Based
```php
class TestCase extends BaseTestCase
{
    protected $tenancy = false;

    public function setUp(): void
    {
        parent::setUp();

        if ($this->tenancy) {
            $this->initializeTenancy();
        }
    }

    public function initializeTenancy()
    {
        $tenant = Tenant::create();
        tenancy()->initialize($tenant);
    }
}

// In test class
class FooTest extends TestCase
{
    protected $tenancy = true;

    /** @test */
    public function some_test()
    {
        $this->assertTrue(...);
    }
}
```

#### Setup Pattern 2: Separate TestCase
Create dedicated `TenantTestCase` class for tenant tests.

## Configuration Best Practices

### 1. Set Central Domains
```php
// config/tenancy.php
'central_domains' => [
    env('CENTRAL_DOMAIN', 'saas.test'),
],
```

### 2. Choose ID Type
```php
// UUID (default)
'id_generator' => Stancl\Tenancy\UUIDGenerator::class,

// Or auto-increment (set to null)
'id_generator' => null,
```

### 3. Configure Bootstrappers
```php
'bootstrappers' => [
    // Required for multi-database
    Stancl\Tenancy\Bootstrappers\DatabaseTenancyBootstrapper::class,
    
    // Recommended
    Stancl\Tenancy\Bootstrappers\CacheTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\FilesystemTenancyBootstrapper::class,
    Stancl\Tenancy\Bootstrappers\QueueTenancyBootstrapper::class,
],
```

### 4. Migration Parameters
```php
'migration_parameters' => [
    '--force' => true,
    '--path' => ['database/migrations/tenant'],
],
```

### 5. Seeder Parameters
```php
'seeder_parameters' => [
    '--class' => 'TenantDatabaseSeeder',
],
```

## Common Patterns and Use Cases

### Creating Tenant with Domain
```php
$tenant = Tenant::create(['id' => 'acme']);
$tenant->domains()->create(['domain' => 'acme.yoursaas.com']);
```

### Running Operations for Multiple Tenants
```php
Tenant::all()->runForEach(function ($tenant) {
    User::factory()->create();
});
```

### Manual Tenancy Initialization
```php
// Initialize
tenancy()->initialize($tenant);

// End tenancy
tenancy()->end();
```

### Accessing Tenant in Blade
```php
// In Blade templates
{{ tenant('id') }}
{{ tenant()->name }}
```

### Custom Tenant Attributes
```php
// Create tenant with custom data
$tenant = Tenant::create([
    'id' => 'acme',
    'plan' => 'premium',
    'settings' => ['feature_x' => true],
    'custom_field' => 'value', // Stored in data JSON column
]);

// Access
$plan = $tenant->plan; // From column
$customField = $tenant->custom_field; // From data column
```

## Important Notes and Gotchas

### 1. Internal Keys
Keys starting with `tenancy_` are reserved for internal use.

### 2. Database Event Context
`DatabaseMigrated`, `DatabaseSeeded`, and similar events fire in **tenant context**. Be careful when interacting with central database in these listeners.

### 3. Tenant vs Tenancy
- **Initializing Tenancy**: Loading tenant into Tenancy object
- **Bootstrapping Tenancy**: Transitioning app to tenant context (happens after initialization)

### 4. Migration Class Names
All migrations share the same namespace. Use different class names for central and tenant migrations even if they create the same table names.

### 5. Route Names with Multiple Domains
Cannot use route names when using multiple central domains (different domain + path combinations can't share names).

### 6. Queue Connection Isolation
Don't mix queue connections for central and tenant jobs to avoid leftover global state.

### 7. Low-Level Database Queries
`DB` facade calls are NOT automatically scoped to tenants in single-database tenancy.

### 8. Tenant Scoping in Single-Database
- Primary models: automatically scoped with `BelongsToTenant` trait
- Secondary models: scoped through parent relationships
- Direct queries on secondary models require `BelongsToPrimaryModel` trait

## Quick Reference

### Helper Functions
```php
tenant()           // Get current tenant
tenant('id')       // Get tenant attribute
tenancy()          // Get Tenancy manager instance
```

### Key Interfaces
```php
Stancl\Tenancy\Contracts\Tenant
Stancl\Tenancy\Contracts\TenantWithDatabase
```

### Key Traits
```php
// Tenant model
Stancl\Tenancy\Database\Concerns\HasDatabase
Stancl\Tenancy\Database\Concerns\HasDomains

// Single-database tenancy
Stancl\Tenancy\Database\Concerns\BelongsToTenant
Stancl\Tenancy\Database\Concerns\BelongsToPrimaryModel
Stancl\Tenancy\Database\Concerns\HasScopedValidationRules
```

### Key Middleware
```php
Stancl\Tenancy\Middleware\InitializeTenancyByDomain
Stancl\Tenancy\Middleware\InitializeTenancyBySubdomain
Stancl\Tenancy\Middleware\InitializeTenancyByDomainOrSubdomain
Stancl\Tenancy\Middleware\InitializeTenancyByPath
Stancl\Tenancy\Middleware\InitializeTenancyByRequestData
Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains
```

## Troubleshooting Common Issues

### Issue: "Tenant could not be identified"
**Solutions:**
- Verify tenant exists with correct domain
- Check central domains configuration
- Ensure correct identification middleware is used
- Use `PreventAccessFromCentralDomains` middleware on tenant routes

### Issue: Migrations Not Running for Tenants
**Solutions:**
- Ensure migrations are in `database/migrations/tenant/`
- Check `TenancyServiceProvider` event mappings
- Verify `DatabaseTenancyBootstrapper` is enabled
- Check if `CreateDatabase` and `MigrateDatabase` jobs are mapped to `TenantCreated` event

### Issue: Queued Jobs Not Tenant-Aware
**Solutions:**
- Enable `QueueTenancyBootstrapper`
- For database queue driver, set `connection` to `central`
- For Redis queue, ensure connection not in `prefixed_connections`

### Issue: Cache Not Isolated Between Tenants
**Solutions:**
- Enable `CacheTenancyBootstrapper`
- Verify cache driver supports tagging (Redis, Memcached)
- Check cache configuration

### Issue: Single-Database Queries Not Scoped
**Solutions:**
- Add `BelongsToTenant` trait to primary models
- Add `BelongsToPrimaryModel` trait to secondary models
- Avoid direct queries on secondary models
- Access through parent relationships

### Issue: Wayfinder Route Duplication
**Symptoms:**
- TypeScript errors like "Identifier 'home' has already been declared"
- Vite build errors during asset generation
- Duplicate export const declarations in generated route files

**Cause:**
Wayfinder generates duplicate named route exports because `routes/web.php` registers the same named routes inside a domain loop for multiple central domains. This creates multiple `export const home` declarations, causing TypeScript redeclaration errors.

**Solution:**
Modify `routes/web.php` to only register routes for the first domain during console runs (asset generation):

```php
$centralDomains = config('tenancy.central_domains', []);
foreach ($centralDomains as $index => $domain) {
    if (app()->runningInConsole() && $index > 0) {
        break; // Only register first domain during console runs
    }
    Route::domain($domain)->group(function () {
        // Your routes here
    });
}
```

**Why this works:**
- Console runs (like `php artisan wayfinder:generate` or `npm run dev`) only register the first domain, preventing duplicate exports
- Runtime HTTP requests still register routes for all domains, preserving full application behavior
- Regenerate Wayfinder outputs after the change: `php artisan wayfinder:generate`

## Advanced Topics

### Custom Tenant Identification
Create custom identification resolver by implementing `TenantResolver` interface.

### Custom Bootstrappers
Create custom bootstrappers by implementing `TenancyBootstrapper` interface.

### Custom Database Managers
Implement `TenantDatabaseManager` interface for custom database management logic.

### Synced Resources Between Tenants
Use synced resource models for data shared across all tenants (requires multi-database).

### Session Scoping
Ensure sessions don't leak between tenants on shared domains.

### Early Identification
Identify tenants before routing for special use cases.

---

## Package Architecture Summary

```
Request
  ↓
Identification Middleware
  ↓
tenancy()->initialize($tenant)
  ↓
TenancyInitialized Event
  ↓
BootstrapTenancy Listener
  ↓
Execute Bootstrappers
  ↓
TenancyBootstrapped Event
  ↓
Application in Tenant Context
```

This guide provides comprehensive coverage of Tenancy for Laravel v3.x. For the most up-to-date information, always refer to the official documentation at https://tenancyforlaravel.com/docs/v3.