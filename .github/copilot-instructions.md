<laravel-boost-guidelines>
=== .ai/spartie_roles_permission rules ===

# Spatie Laravel Permission - Comprehensive Guide for Generative AI Agents

## Package Information
- **Package Name**: `spatie/laravel-permission`
- **Version**: 6.x
- **Repository**: https://github.com/spatie/laravel-permission
- **Documentation**: https://spatie.be/docs/laravel-permission/v6

## What is Laravel Permission?

This package allows you to manage user permissions and roles in a database. It provides a flexible way to associate users with permissions and roles, where every role is associated with multiple permissions. All permissions are registered on Laravel's gate, allowing you to use Laravel's default authorization features.

## Core Concepts

### Roles
- Groupings of permissions
- Assigned to users
- Multiple roles per user supported
- Examples: `admin`, `writer`, `editor`, `manager`

### Permissions
- Specific actions users can perform
- Assigned to roles (recommended) or directly to users
- More granular than roles
- Examples: `edit articles`, `delete users`, `view dashboard`

### Best Practice Hierarchy
```
Users → Have → Roles → Have → Permissions
```

**Important**: Always check against **permissions** in your app logic, not roles. This provides maximum flexibility.

## Installation

### 1. Install via Composer
```bash
composer require spatie/laravel-permission
```

### 2. Publish Configuration and Migration
```bash
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
```

This creates:
- `config/permission.php` - Configuration file
- Migration file for creating permission tables

### 3. Important Pre-Migration Considerations

#### For UUID/ULID Support
If using UUIDs, modify migration and config BEFORE running migrations. See Advanced Usage → UUID section.

#### For Teams Feature
If using teams, update `config/permission.php` BEFORE migrating:
```php
'teams' => true,
'team_foreign_key' => 'team_id', // Optional: customize foreign key name
```

#### For MySQL 8+
Check migration files for index key length notes. Edit if you encounter `ERROR: 1071 Specified key was too long`.

#### For Database Cache Store
If using `CACHE_STORE=database`, install Laravel's cache migration first:
```bash
php artisan cache:table
php artisan migrate
```

### 4. Clear Config Cache
```bash
php artisan optimize:clear
# or
php artisan config:clear
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Add Trait to User Model
```php
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    // ...
}
```

## Database Tables Created

The package creates these tables:
- `roles` - Stores role definitions
- `permissions` - Stores permission definitions
- `model_has_permissions` - Pivot table for users with direct permissions
- `model_has_roles` - Pivot table for users with roles
- `role_has_permissions` - Pivot table for roles with permissions

## Basic Usage

### Creating Permissions and Roles

#### Create Permission
```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

// Create permission
$permission = Permission::create(['name' => 'edit articles']);

// Create role
$role = Role::create(['name' => 'writer']);
```

#### Assign Permission to Role
```php
// Method 1: From role
$role->givePermissionTo($permission);
$role->givePermissionTo('edit articles');

// Method 2: From permission
$permission->assignRole($role);
$permission->assignRole('writer');
```

#### Sync Permissions to Role
```php
// Replace all permissions
$role->syncPermissions(['edit articles', 'delete articles']);
$permission->syncRoles(['writer', 'editor']);
```

#### Remove Permission from Role
```php
$role->revokePermissionTo('edit articles');
$permission->removeRole('writer');
```

### Assigning Roles to Users

#### Assign Role
```php
// Single role
$user->assignRole('writer');

// Multiple roles at once
$user->assignRole('writer', 'admin');
$user->assignRole(['writer', 'admin']);
```

#### Remove Role
```php
$user->removeRole('writer');
```

#### Sync Roles
```php
// Replace all current roles
$user->syncRoles(['writer', 'admin']);
```

### Direct Permissions to Users

**Best Practice**: Assign permissions to roles, then roles to users. Only use direct permissions when necessary.

#### Give Direct Permission
```php
$user->givePermissionTo('edit articles');

// Multiple permissions
$user->givePermissionTo('edit articles', 'delete articles');
$user->givePermissionTo(['edit articles', 'delete articles']);
```

#### Revoke Direct Permission
```php
$user->revokePermissionTo('edit articles');
```

#### Sync Direct Permissions
```php
$user->syncPermissions(['edit articles', 'delete articles']);
```

## Checking Permissions and Roles

### Using Laravel's Native Can Method (Recommended)

```php
// In controllers
if ($user->can('edit articles')) {
    //
}

// In Blade
@can('edit articles')
    <!-- Show edit button -->
@endcan

@cannot('edit articles')
    <!-- Show read-only message -->
@endcannot

@canany(['edit articles', 'delete articles'])
    <!-- User can do at least one -->
@endcanany
```

### Checking Permissions

```php
// Single permission
$user->hasPermissionTo('edit articles');
$user->hasPermissionTo(1); // By ID
$user->hasPermissionTo($permission); // By object

// Any of array
$user->hasAnyPermission(['edit articles', 'publish articles']);

// All of array
$user->hasAllPermissions(['edit articles', 'publish articles']);
```

### Checking Roles

```php
// Single role
$user->hasRole('writer');
$user->hasRole(['editor', 'moderator']); // Has at least one

// Any of roles
$user->hasAnyRole(['writer', 'reader']);
$user->hasAnyRole('writer', 'reader');

// All of roles
$user->hasAllRoles(Role::all());

// Exact roles (only these, no more)
$user->hasExactRoles(['writer', 'editor']);
```

### Checking Direct Permissions

```php
// Has specific direct permission (not via role)
$user->hasDirectPermission('edit articles');

// Has all direct permissions
$user->hasAllDirectPermissions(['edit articles', 'delete articles']);

// Has any direct permission
$user->hasAnyDirectPermission(['create articles', 'delete articles']);
```

### Getting User Permissions and Roles

```php
// Get permission names
$permissionNames = $user->getPermissionNames(); // Collection of strings

// Get permission objects
$permissions = $user->permissions; // Collection

// Get all permissions (direct + via roles)
$user->getAllPermissions();

// Get only direct permissions
$user->getDirectPermissions();

// Get permissions via roles
$user->getPermissionsViaRoles();

// Get role names
$roleNames = $user->getRoleNames(); // Collection of strings

// Get role objects  
$roles = $user->roles; // Collection
```

### Querying Roles and Permissions

```php
// Get all permissions for a role
$role->permissions; // Collection
$role->permissions->pluck('name'); // Just names
count($role->permissions);

// Check if role has permission
$role->hasPermissionTo('edit articles');
```

## Blade Directives

### Permission Directives (Recommended)

```php
@can('edit articles')
    <!-- User can edit articles -->
@endcan

@can('edit articles', 'api') // Specify guard
    <!-- User can edit articles on api guard -->
@endcan

@cannot('edit articles')
    <!-- User cannot edit articles -->
@endcannot

@canany(['edit articles', 'delete articles'])
    <!-- User has at least one permission -->
@endcanany

// Package-specific directive
@haspermission('edit articles')
    <!-- Alternative to @can -->
@endhaspermission
```

### Role Directives (Use Sparingly)

**Best Practice**: Check permissions, not roles!

```php
@role('writer')
    <!-- User has writer role -->
@else
    <!-- User doesn't have writer role -->
@endrole

@hasrole('writer')
    <!-- Same as @role -->
@endhasrole

@hasanyrole(['writer', 'admin'])
    <!-- User has at least one role -->
@endhasanyrole

@hasanyrole('writer|admin') // Pipe-separated
    <!-- User has at least one role -->
@endhasanyrole

@hasallroles(['writer', 'admin'])
    <!-- User has all roles -->
@endhasallroles

@hasexactroles('writer|admin')
    <!-- User has exactly these roles, no more -->
@endhasexactroles

@unlessrole('admin')
    <!-- User doesn't have admin role -->
@endunlessrole
```

## Middleware

### Built-in Laravel Middleware

For single permission checks, use Laravel's built-in `Authorize` middleware:

```php
Route::group(['middleware' => ['can:publish articles']], function () {
    //
});

// Laravel 10.9+ static method
use Illuminate\Auth\Middleware\Authorize;

Route::group(['middleware' => [Authorize::using('publish articles')]], function () {
    //
});
```

### Package Middleware

The package provides three middleware classes:
- `RoleMiddleware` - Check for roles
- `PermissionMiddleware` - Check for permissions
- `RoleOrPermissionMiddleware` - Check for either role or permission

#### Register Middleware Aliases

**Laravel 11+** in `/bootstrap/app.php`:
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

**Laravel 9-10** in `app/Http/Kernel.php`:
```php
protected $middlewareAliases = [
    // ... other middleware
    'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
    'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
    'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
];
```

#### Using Middleware in Routes

```php
// Single role
Route::group(['middleware' => ['role:manager']], function () {
    //
});

// Single permission
Route::group(['middleware' => ['permission:publish articles']], function () {
    //
});

// Role or permission
Route::group(['middleware' => ['role_or_permission:publish articles']], function () {
    //
});

// With guard
Route::group(['middleware' => ['role:manager,api']], function () {
    //
});

// Multiple using OR (pipe)
Route::group(['middleware' => ['role:manager|writer']], function () {
    //
});

Route::group(['middleware' => ['permission:publish articles|edit articles']], function () {
    //
});

// Multiple middleware (AND)
Route::group(['middleware' => ['role:manager', 'permission:publish articles']], function () {
    //
});
```

#### Using Middleware in Controllers

**Laravel 11** (with `HasMiddleware` interface):
```php
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ArticleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role_or_permission:manager|edit articles',
            new Middleware('role:author', only: ['index']),
            new Middleware(\Spatie\Permission\Middleware\RoleMiddleware::using('manager'), except: ['show']),
            new Middleware(\Spatie\Permission\Middleware\PermissionMiddleware::using('delete records,api'), only: ['destroy']),
        ];
    }
}
```

**Laravel 10 and older**:
```php
public function __construct()
{
    $this->middleware(['role:manager', 'permission:publish articles|edit articles']);
    $this->middleware(['role_or_permission:manager|edit articles,api']);
}
```

#### Static Method Usage

```php
use Spatie\Permission\Middleware\RoleMiddleware;
use Spatie\Permission\Middleware\PermissionMiddleware;
use Spatie\Permission\Middleware\RoleOrPermissionMiddleware;

Route::group(['middleware' => [RoleMiddleware::using('manager')]], function () {
    //
});

Route::group(['middleware' => [PermissionMiddleware::using('publish articles|edit articles')]], function () {
    //
});

Route::group(['middleware' => [RoleOrPermissionMiddleware::using(['manager', 'edit articles'])]], function () {
    //
});
```

## Eloquent Scopes and Queries

### Scopes

```php
// Users with specific role
$users = User::role('writer')->get();

// Users without role
$nonEditors = User::withoutRole('editor')->get();

// Users with permission (direct or via role)
$users = User::permission('edit articles')->get();

// Users without permission
$users = User::withoutPermission('edit articles')->get();
```

### Eloquent Queries

```php
// All users with all their roles
$users = User::with('roles')->get();

// All users with all their direct permissions
$users = User::with('permissions')->get();

// All role names
$allRoles = Role::all()->pluck('name');

// Users without any roles
$usersWithoutRoles = User::doesntHave('roles')->get();

// Roles except certain ones
$roles = Role::whereNotIn('name', ['role A', 'role B'])->get();

// Count users with specific role
$managersCount = User::with('roles')->get()->filter(
    fn ($user) => $user->roles->where('name', 'Manager')->toArray()
)->count();
```

## Multiple Guards

### What are Guards?
Laravel guards define how users are authenticated (web, api, admin, etc.). This package supports multiple guards.

### Setting Guard Name

When using multiple guards, specify `guard_name` when creating permissions/roles:

```php
// Create permission for specific guard
Permission::create(['name' => 'edit articles', 'guard_name' => 'api']);
Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);

// Create role for specific guard
Role::create(['name' => 'writer', 'guard_name' => 'api']);
```

### Default Guard

If you don't specify `guard_name`, it uses the default from `config/auth.php`.

### Assigning with Guards

```php
// Assign role with guard
$user->assignRole('writer'); // Uses default guard
$user->assignRole('api-writer'); // If role has api guard

// Give permission with guard
$user->givePermissionTo('edit articles'); // Uses default guard
```

### Checking with Guards

```php
// Check permission with specific guard
$user->hasPermissionTo('edit articles', 'api');

// In Blade
@can('edit articles', 'api')
    //
@endcan
```

## Teams Permissions

Teams permissions allow scoping permissions to specific teams/organizations.

### Enabling Teams

**BEFORE running migrations**, update `config/permission.php`:
```php
'teams' => true,
'team_foreign_key' => 'team_id', // Optional custom foreign key
```

### Upgrading Existing Installation

If already migrated, run:
```bash
php artisan permission:setup-teams
php artisan migrate
```

### Setting Active Team

Create middleware to set active team:

```php
namespace App\Http\Middleware;

class TeamsPermission
{
    public function handle($request, \Closure $next)
    {
        if (!empty(auth()->user())) {
            // Set from session (set on login)
            setPermissionsTeamId(session('team_id'));
        }
        
        return $next($request);
    }
}
```

**Important**: Set middleware priority BEFORE `SubstituteBindings` in Laravel 11.27+:

```php
// AppServiceProvider
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Middleware\SubstituteBindings;

public function boot(): void
{
    $kernel = app()->make(Kernel::class);
    $kernel->addToMiddlewarePriorityBefore(
        \App\Http\Middleware\TeamsPermission::class,
        SubstituteBindings::class
    );
}
```

### Creating Roles with Teams

```php
// Global role (null team_id, can be assigned to any team)
Role::create(['name' => 'writer', 'team_id' => null]);

// Team-specific role (can have same name on different teams)
Role::create(['name' => 'reader', 'team_id' => 1]);
Role::create(['name' => 'reader', 'team_id' => 2]);

// Role takes current team_id if not specified
Role::create(['name' => 'reviewer']);
```

### Switching Teams

When switching teams, reset cached relations:

```php
// Set new team
setPermissionsTeamId($new_team_id);

// Reset cached relations
$user->unsetRelation('roles')->unsetRelation('permissions');

// Now check permissions/roles for new team
$user->hasRole('manager');
$user->can('edit articles');
```

### Super-Admin on Teams

When creating new team, assign global role to super-admin:

```php
// In Team model
protected static function boot()
{
    parent::boot();
    
    self::created(function ($model) {
        $session_team_id = getPermissionsTeamId();
        setPermissionsTeamId($model->id);
        
        User::find($superAdminId)->assignRole('Super Admin');
        
        setPermissionsTeamId($session_team_id);
    });
}
```

## Wildcard Permissions

Wildcard permissions allow pattern matching for permissions.

### Enabling Wildcards

In `config/permission.php`:
```php
'enable_wildcard_permission' => true,
```

### Using Wildcards

```php
// Create wildcard permission
Permission::create(['name' => 'articles.*']);
Permission::create(['name' => 'articles.*.id']); // Can use multiple wildcards

// Give to user
$user->givePermissionTo('articles.*');

// Check permission
$user->can('articles.create'); // true
$user->can('articles.edit'); // true
$user->can('articles.delete'); // true
```

### Wildcard Patterns

```php
// Match any
'articles.*' matches: 'articles.create', 'articles.edit', 'articles.delete'

// Match with prefix
'admin.*' matches: 'admin.users', 'admin.settings', 'admin.reports'

// Multiple wildcards
'*.articles.*' matches: 'blog.articles.edit', 'news.articles.delete'
```

## Super-Admin

### Defining Super-Admin

Use Laravel's `Gate::before()` to create super-admin that bypasses all permission checks:

```php
// In AuthServiceProvider
use Illuminate\Support\Facades\Gate;

public function boot()
{
    // Implicitly grant "Super Admin" role all permissions
    Gate::before(function ($user, $ability) {
        return $user->hasRole('Super Admin') ? true : null;
    });
}
```

**Important**: Return `null` for non-super-admins to allow normal permission checks.

### Alternative: Check Specific Attribute

```php
Gate::before(function ($user, $ability) {
    return $user->is_super_admin ? true : null;
});
```

## Artisan Commands

### Create Permission
```bash
php artisan permission:create-permission "edit articles"
php artisan permission:create-permission "edit articles" api  # with guard
```

### Create Role
```bash
php artisan permission:create-role writer
php artisan permission:create-role writer api  # with guard
```

### Assign Permission to Role
```bash
php artisan permission:assign-permission-to-role "edit articles" writer
```

### Assign Role to User
```bash
php artisan permission:assign-role-to-user writer 1  # user ID
php artisan permission:assign-role-to-user writer admin@example.com  # email
```

### Cache Management
```bash
# Reset cache
php artisan permission:cache-reset

# Show permissions
php artisan permission:show

# Setup teams (for existing installations)
php artisan permission:setup-teams
```

## Cache Management

### Automatic Cache Reset

Cache automatically resets when using built-in methods:

```php
// These automatically reset cache
$role->givePermissionTo('edit articles');
$role->revokePermissionTo('edit articles');
$permission->assignRole('writer');
$permission->removeRole('writer');
```

**Note**: User role/permission assignments are kept in-memory (v4.4.0+), so no cache reset needed:

```php
// These do NOT trigger cache reset (in-memory)
$user->assignRole('writer');
$user->removeRole('writer');
$user->syncRoles([...]);
$user->givePermissionTo('edit articles');
```

### Manual Cache Reset

```php
// In code
app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

// Via artisan
php artisan permission:cache-reset
```

### Cache Configuration

In `config/permission.php`:

```php
'cache' => [
    // Expiration time (default: 24 hours)
    'expiration_time' => \DateInterval::createFromDateString('24 hours'),
    
    // Cache key
    'key' => 'spatie.permission.cache',
    
    // Cache store (use any from config/cache.php)
    'store' => 'default',
],
```

### Custom Cache Store

Set custom cache store:
```php
'cache' => [
    'store' => 'redis', // or 'memcached', 'database', etc.
],
```

### Disabling Cache

Set cache store to `array` to disable caching:
```php
'cache' => [
    'store' => 'array',
],
```

Or in `.env` (development only):
```
CACHE_DRIVER=array
```

### Multitenancy Cache Considerations

For multitenancy, set unique cache prefix in `/config/cache.php`:
```php
'prefix' => env('CACHE_PREFIX', 'tenant_'.tenant('id')),
```

When switching tenants/cache config:
```php
app()->make(\Spatie\Permission\PermissionRegistrar::class)->initializeCache();
```

## Database Seeding

### Simple Seeding

```php
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

// Create permissions
$permissions = [
    'view articles',
    'create articles',
    'edit articles',
    'delete articles',
];

foreach ($permissions as $permission) {
    Permission::create(['name' => $permission]);
}

// Create roles and assign permissions
$role = Role::create(['name' => 'writer']);
$role->givePermissionTo(['create articles', 'edit articles']);

$role = Role::create(['name' => 'admin']);
$role->givePermissionTo(Permission::all());

// Assign roles to users
$user = User::find(1);
$user->assignRole('admin');
```

### Advanced Seeding with Guards

```php
// Reset cache
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

// Create permissions for web guard
Permission::create(['name' => 'edit articles', 'guard_name' => 'web']);
Permission::create(['name' => 'delete articles', 'guard_name' => 'web']);

// Create permissions for api guard
Permission::create(['name' => 'edit articles', 'guard_name' => 'api']);
Permission::create(['name' => 'delete articles', 'guard_name' => 'api']);

// Create roles and assign permissions
$role = Role::create(['name' => 'writer', 'guard_name' => 'web']);
$role->givePermissionTo(['edit articles', 'delete articles']);

$role = Role::create(['name' => 'admin', 'guard_name' => 'api']);
$role->givePermissionTo(['edit articles', 'delete articles']);
```

## Testing

### Setup for Tests

```php
use Spatie\Permission\PermissionServiceProvider;

protected function getPackageProviders($app)
{
    return [
        PermissionServiceProvider::class,
    ];
}

protected function setUp(): void
{
    parent::setUp();
    
    // Run migrations
    $this->artisan('migrate');
    
    // Seed permissions
    Permission::create(['name' => 'edit articles']);
    Role::create(['name' => 'admin']);
}
```

### Testing Permissions

```php
/** @test */
public function user_can_have_permission()
{
    $user = User::factory()->create();
    $user->givePermissionTo('edit articles');
    
    $this->assertTrue($user->can('edit articles'));
    $this->assertTrue($user->hasPermissionTo('edit articles'));
}

/** @test */
public function user_can_have_role()
{
    $user = User::factory()->create();
    $user->assignRole('admin');
    
    $this->assertTrue($user->hasRole('admin'));
}

/** @test */
public function middleware_blocks_unauthorized_access()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/admin');
    
    $response->assertStatus(403);
    
    $user->givePermissionTo('access admin');
    
    $response = $this->actingAs($user)->get('/admin');
    
    $response->assertStatus(200);
}
```

## Events

The package fires events when permissions/roles change:

```php
// Role events
Spatie\Permission\Events\RoleCreated
Spatie\Permission\Events\RoleUpdated
Spatie\Permission\Events\RoleDeleted

// Permission events  
Spatie\Permission\Events\PermissionCreated
Spatie\Permission\Events\PermissionUpdated
Spatie\Permission\Events\PermissionDeleted
```

### Listening to Events

```php
// In EventServiceProvider
protected $listen = [
    \Spatie\Permission\Events\RoleCreated::class => [
        \App\Listeners\RoleCreatedListener::class,
    ],
];
```

### Event Example

```php
namespace App\Listeners;

use Spatie\Permission\Events\RoleCreated;

class RoleCreatedListener
{
    public function handle(RoleCreated $event)
    {
        $role = $event->role;
        
        // Do something with the role
        logger("Role created: {$role->name}");
    }
}
```

## Extending Models

### Custom Role Model

```php
namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    // Add custom methods or properties
    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'model_id')
            ->where('model_type', User::class);
    }
}
```

Update config:
```php
// config/permission.php
'models' => [
    'role' => App\Models\Role::class,
],
```

### Custom Permission Model

```php
namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    // Add custom methods
    public function category()
    {
        return $this->belongsTo(PermissionCategory::class);
    }
}
```

Update config:
```php
'models' => [
    'permission' => App\Models\Permission::class,
],
```

## UUID/ULID Support

### Setup for UUID

1. Update migration BEFORE running it:
```php
// In migration file
Schema::create('permissions', function (Blueprint $table) {
    $table->uuid('id')->primary(); // Change from bigIncrements
    // ...
});
```

2. Update models to use UUID:
```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Permission extends Model
{
    use HasUuids;
    
    public $incrementing = false;
    protected $keyType = 'string';
}
```

3. Update config:
```php
'column_names' => [
    'model_morph_key' => 'model_uuid', // if using UUIDs on User model
],
```

## Best Practices

### 1. Always Check Permissions, Not Roles

**Good**:
```php
@can('edit articles')
    <!-- Edit button -->
@endcan

if ($user->can('edit articles')) {
    // Allow editing
}
```

**Bad**:
```php
@if($user->hasRole('editor'))
    <!-- Edit button -->
@endif
```

**Why?** Checking permissions is more flexible and allows role changes without code updates.

### 2. Granular Permissions

**Good**:
```php
Permission::create(['name' => 'view articles']);
Permission::create(['name' => 'create articles']);
Permission::create(['name' => 'edit articles']);
Permission::create(['name' => 'delete articles']);
```

**Bad**:
```php
Permission::create(['name' => 'manage articles']);
```

**Why?** Granular permissions provide fine-grained control.

### 3. Use Roles to Group Permissions

```php
// Create permissions
$permissions = [
    'view articles',
    'create articles',
    'edit articles',
    'delete articles',
];

// Create role and assign relevant permissions
$writer = Role::create(['name' => 'writer']);
$writer->givePermissionTo(['view articles', 'create articles', 'edit articles']);

$admin = Role::create(['name' => 'admin']);
$admin->givePermissionTo($permissions); // All permissions
```

### 4. Avoid Direct Permissions

Assign permissions to roles, then roles to users:

```php
// Good
$role = Role::create(['name' => 'editor']);
$role->givePermissionTo(['edit articles', 'publish articles']);
$user->assignRole('editor');

// Avoid (unless necessary)
$user->givePermissionTo('edit articles');
```

### 5. Use Gates and Policies

Combine with Laravel's authorization:

```php
// In AuthServiceProvider
Gate::define('update-article', function ($user, $article) {
    return $user->can('edit articles') && $user->id === $article->user_id;
});

// In Policy
public function update(User $user, Article $article)
{
    return $user->can('edit articles') && $user->owns($article);
}
```

### 6. Permission Naming Convention

Use clear, consistent naming:

```php
// Resource-based
'view articles'
'create articles'
'edit articles'
'delete articles'

// Action-based
'publish article'
'approve comment'
'manage users'

// Feature-based
'access admin panel'
'use api'
'export data'
```

## Performance Tips

### 1. Eager Load Relationships

```php
// Bad (N+1 problem)
$users = User::all();
foreach ($users as $user) {
    $user->roles; // N+1 queries
}

// Good
$users = User::with('roles', 'permissions')->get();
```

### 2. Cache Permission Checks

```php
// Cache expensive checks
$canEdit = Cache::remember("user_{$user->id}_can_edit_articles", 3600, function () use ($user) {
    return $user->can('edit articles');
});
```

### 3. Use Scopes Efficiently

```php
// Efficient
$writers = User::role('writer')->with('permissions')->get();

// Less efficient
$writers = User::all()->filter(fn($u) => $u->hasRole('writer'));
```

### 4. Limit Wildcard Usage

Wildcards are powerful but can impact performance. Use specific permissions when possible.

## Common Patterns

### Admin Panel Access

```php
// Create admin permission
Permission::create(['name' => 'access admin']);

// Middleware on admin routes
Route::prefix('admin')->middleware(['auth', 'permission:access admin'])->group(function () {
    // Admin routes
});
```

### Resource-Based Permissions

```php
// Create CRUD permissions for resource
$resources = ['articles', 'users', 'categories'];
$actions = ['view', 'create', 'edit', 'delete'];

foreach ($resources as $resource) {
    foreach ($actions as $action) {
        Permission::create(['name' => "$action $resource"]);
    }
}
```

### Dynamic Permission Checking

```php
public function can($action, $resource)
{
    return auth()->user()->can("$action $resource");
}

// Usage
if ($this->can('edit', 'articles')) {
    //
}
```

### Team-Scoped Permissions

```php
// Set team context
setPermissionsTeamId($request->user()->current_team_id);

// Check permission in team context
if ($user->can('manage projects')) {
    // User can manage projects in current team
}
```

## Troubleshooting

### Issue: "Call to undefined method can()"

**Solution**: Ensure `HasRoles` trait is added to User model:
```php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
}
```

### Issue: Permissions not updating

**Solution**: Reset cache:
```bash
php artisan permission:cache-reset
```

Or in code:
```php
app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
```

### Issue: "Table not found" errors

**Solution**: Run migrations:
```bash
php artisan migrate
```

### Issue: 403 when expected 404

**Solution**: Check middleware priority. Permission middleware should run before `SubstituteBindings`:
```php
// Laravel 11.27+
$kernel->addToMiddlewarePriorityBefore(
    \Spatie\Permission\Middleware\PermissionMiddleware::class,
    \Illuminate\Routing\Middleware\SubstituteBindings::class
);
```

### Issue: Role/Permission not working with multiple guards

**Solution**: Specify guard name:
```php
Permission::create(['name' => 'edit articles', 'guard_name' => 'api']);
$user->hasPermissionTo('edit articles', 'api');
```

### Issue: Cached permissions showing for wrong team

**Solution**: Reset cached relations when switching teams:
```php
setPermissionsTeamId($newTeamId);
$user->unsetRelation('roles')->unsetRelation('permissions');
```

### Issue: Database cache store errors

**Solution**: Install Laravel cache table:
```bash
php artisan cache:table
php artisan migrate
```

## Security Considerations

### 1. Never Trust User Input

```php
// Bad
$permission = request('permission');
$user->givePermissionTo($permission);

// Good
$allowedPermissions = ['view articles', 'edit articles'];
$permission = request('permission');
if (in_array($permission, $allowedPermissions)) {
    $user->givePermissionTo($permission);
}
```

### 2. Validate Role/Permission Assignment

```php
// Validate before assignment
$validator = Validator::make($request->all(), [
    'role' => 'required|exists:roles,name',
]);

if ($validator->fails()) {
    return back()->withErrors($validator);
}

$user->assignRole($request->role);
```

### 3. Use Policies for Model-Specific Authorization

```php
// ArticlePolicy
public function update(User $user, Article $article)
{
    return $user->can('edit articles') && $user->id === $article->user_id;
}
```

### 4. Log Permission Changes

```php
// In EventServiceProvider
use Spatie\Permission\Events\RoleCreated;

Event::listen(RoleCreated::class, function ($event) {
    Log::info('Role created', ['role' => $event->role->name]);
});
```

## Quick Reference

### Most Common Methods

```php
// User methods
$user->assignRole('writer');
$user->removeRole('writer');
$user->syncRoles(['writer', 'admin']);
$user->hasRole('writer');
$user->can('edit articles');
$user->givePermissionTo('edit articles');
$user->revokePermissionTo('edit articles');

// Role methods
$role->givePermissionTo('edit articles');
$role->revokePermissionTo('edit articles');
$role->syncPermissions(['edit articles', 'delete articles']);
$role->hasPermissionTo('edit articles');

// Permission methods
$permission->assignRole('writer');
$permission->removeRole('writer');
$permission->syncRoles(['writer', 'editor']);

// Checking
$user->hasPermissionTo('edit articles');
$user->hasAnyPermission(['edit articles', 'delete articles']);
$user->hasAllPermissions(['edit articles', 'delete articles']);
```

### Configuration Locations

- **Config file**: `config/permission.php`
- **Cache config**: `config/cache.php`
- **Guard config**: `config/auth.php`

### Useful Artisan Commands

```bash
php artisan permission:create-permission "permission name"
php artisan permission:create-role "role name"
php artisan permission:cache-reset
php artisan permission:show
php artisan permission:setup-teams
```

---

## Summary

The Spatie Laravel Permission package provides a robust, flexible system for managing roles and permissions in Laravel applications. Key takeaways:

1. **Always check permissions, not roles** in application logic
2. **Permissions are assigned to roles**, roles are assigned to users
3. **Use granular permissions** for maximum flexibility
4. **Leverage Laravel's native `can()` and `@can`** directives
5. **Cache is automatically managed** when using built-in methods
6. **Teams feature** enables multi-tenant permission scoping
7. **Multiple guards** are fully supported
8. **Extends seamlessly** with custom models and logic

For the latest updates and detailed examples, always refer to the official documentation at https://spatie.be/docs/laravel-permission/v6.

=== .ai/review-controllers rules ===

# Controller Refactoring Guide

## Overview
This guide provides a systematic approach to refactoring Laravel controllers following best practices and SOLID principles. Use this as a checklist when reviewing and refactoring each controller in the application.

---

## Core Principles

### Controllers Should ONLY:
1. ✅ Handle HTTP requests and responses
2. ✅ Validate incoming data
3. ✅ Delegate business logic to services
4. ✅ Return views/JSON responses
5. ✅ Handle redirects with appropriate messages

### Controllers Should NEVER:
1. ❌ Contain complex business logic
2. ❌ Directly manipulate multiple models
3. ❌ Handle database transactions
4. ❌ Contain validation logic in closures
5. ❌ Have methods longer than 20-30 lines
6. ❌ Directly send emails/notifications (delegate to services)

---

## Refactoring Checklist

### Step 1: Identify Code Smells

Review each controller method for these red flags:

- [ ] **Fat Methods**: Methods with 30+ lines of code
- [ ] **Database Transactions**: `DB::transaction()` or `DB::beginTransaction()` in controller
- [ ] **Multiple Model Operations**: Creating/updating 3+ models in one method
- [ ] **Complex Validation**: Validation rules with closures or custom logic
- [ ] **Business Logic**: Calculations, data transformations, complex conditionals
- [ ] **Direct Email/Notifications**: Sending emails directly in controller
- [ ] **Query Builder Usage**: Raw queries or complex Eloquent operations
- [ ] **Error Handling**: Try-catch blocks with complex error handling logic

### Step 2: Extract to Appropriate Layers

Based on what you find, extract code to:

#### A. Service Classes (`app/Services/`)
**When to use:**
- Complex business operations involving multiple steps
- Operations that coordinate multiple models
- Database transactions
- Third-party API interactions
- Complex calculations or data transformations

**Example:**
```php
// app/Services/TenantCreationService.php
namespace App\Services;

class TenantCreationService
{
    public function createTenant(array $data): Tenant
    {
        return DB::transaction(function () use ($data) {
            // Complex tenant creation logic
        });
    }
}
```

#### B. Actions (`app/Actions/`)
**When to use:**
- Single, focused operations (Single Responsibility Principle)
- Reusable operations across multiple contexts
- Simple, atomic business operations

**Example:**
```php
// app/Actions/SendWelcomeEmail.php
namespace App\Actions;

class SendWelcomeEmail
{
    public function execute(User $user, string $password): void
    {
        $user->notify(new WelcomeCredentials($password));
    }
}
```

#### C. Custom Request Classes (`app/Http/Requests/`)
**When to use:**
- Complex validation rules
- Validation with closures
- Authorization logic
- Conditional validation

**Example:**
```php
// app/Http/Requests/StoreSubscriptionRequest.php
namespace App\Http\Requests;

class StoreSubscriptionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'domain' => ['required', 'string', new UniqueTenantDomain()],
            // ... other rules
        ];
    }
    
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Complex validation logic
        });
    }
}
```

#### D. Custom Validation Rules (`app/Rules/`)
**When to use:**
- Reusable validation logic
- Complex validation that doesn't fit in a rule string
- Database-dependent validation

**Example:**
```php
// app/Rules/UniqueTenantDomain.php
namespace App\Rules;

class UniqueTenantDomain implements Rule
{
    public function passes($attribute, $value): bool
    {
        // Validation logic
    }
}
```

#### E. Model Methods
**When to use:**
- Data manipulation specific to that model
- Accessors/Mutators
- Query scopes
- Relationships
- Simple helper methods about the model's state

**Example:**
```php
// In Tenant.php model
public function isActive(): bool
{
    return $this->subscription_status === 'active';
}

public function scopeActive($query)
{
    return $query->where('subscription_status', 'active');
}
```

#### F. Traits (`app/Traits/`)
**When to use:**
- Shared functionality across multiple models
- Reusable behavior patterns
- Cross-cutting concerns

**Example:**
```php
// app/Traits/HasSubscription.php
namespace App\Traits;

trait HasSubscription
{
    public function isSubscriptionActive(): bool { }
    public function renewSubscription(): void { }
    public function cancelSubscription(): void { }
}
```

---

## Step-by-Step Refactoring Process

### For Each Controller:

#### 1. **Analyze Current State**
```bash
# Review the controller
- Count lines per method
- Identify dependencies
- List all operations performed
- Note any external service calls
```

#### 2. **Plan the Refactoring**
```markdown
Create a refactoring plan:
- [ ] What needs to move to services?
- [ ] What validation needs extraction?
- [ ] What can move to model methods?
- [ ] What traits could be created?
- [ ] What actions are needed?
```

#### 3. **Create New Files**
```bash
# Generate necessary files
php artisan make:service TenantCreationService
php artisan make:request StoreSubscriptionRequest
php artisan make:rule UniqueTenantDomain
```

#### 4. **Move Code Systematically**

**Priority Order:**
1. Extract validation → Request classes or Rules
2. Extract business logic → Services or Actions
3. Extract model operations → Model methods
4. Extract shared behavior → Traits
5. Clean up controller → Keep only HTTP concerns

#### 5. **Update Controller**

**Before:**
```php
public function store(Request $request)
{
    $validated = $request->validate([...]);
    
    DB::beginTransaction();
    try {
        $tenant = Tenant::create([...]);
        $user = User::create([...]);
        $user->notify(new WelcomeEmail());
        DB::commit();
        return redirect('/success');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->withErrors(['error' => $e->getMessage()]);
    }
}
```

**After:**
```php
public function store(StoreSubscriptionRequest $request)
{
    try {
        $tenant = $this->tenantService->createTenant(
            $request->validated()
        );
        
        return redirect('/success')
            ->with('success', 'Account created!');
    } catch (\Exception $e) {
        Log::error('Tenant creation failed', [
            'error' => $e->getMessage()
        ]);
        
        return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create account.']);
    }
}
```

---

## Controller Method Templates

### Index Method
```php
public function index(Request $request)
{
    $items = $this->service->getPaginated(
        $request->query('filter'),
        $request->query('sort')
    );
    
    return view('items.index', compact('items'));
}
```

### Show Method
```php
public function show(Model $model)
{
    $this->authorize('view', $model);
    
    return view('items.show', compact('model'));
}
```

### Create Method
```php
public function create()
{
    $options = $this->service->getFormOptions();
    
    return view('items.create', compact('options'));
}
```

### Store Method
```php
public function store(StoreModelRequest $request)
{
    try {
        $model = $this->service->create($request->validated());
        
        return redirect()
            ->route('items.show', $model)
            ->with('success', 'Created successfully!');
    } catch (\Exception $e) {
        Log::error('Creation failed', ['error' => $e->getMessage()]);
        
        return back()
            ->withInput()
            ->withErrors(['error' => 'Creation failed.']);
    }
}
```

### Update Method
```php
public function update(UpdateModelRequest $request, Model $model)
{
    $this->authorize('update', $model);
    
    try {
        $this->service->update($model, $request->validated());
        
        return redirect()
            ->route('items.show', $model)
            ->with('success', 'Updated successfully!');
    } catch (\Exception $e) {
        Log::error('Update failed', ['error' => $e->getMessage()]);
        
        return back()
            ->withInput()
            ->withErrors(['error' => 'Update failed.']);
    }
}
```

### Destroy Method
```php
public function destroy(Model $model)
{
    $this->authorize('delete', $model);
    
    try {
        $this->service->delete($model);
        
        return redirect()
            ->route('items.index')
            ->with('success', 'Deleted successfully!');
    } catch (\Exception $e) {
        Log::error('Deletion failed', ['error' => $e->getMessage()]);
        
        return back()
            ->withErrors(['error' => 'Deletion failed.']);
    }
}
```

---

## Common Refactoring Patterns

### Pattern 1: Multi-Model Creation
**Before:**
```php
public function store(Request $request)
{
    $user = User::create($request->only('name', 'email'));
    $profile = Profile::create(['user_id' => $user->id, ...]);
    $settings = Settings::create(['user_id' => $user->id, ...]);
    // More operations...
}
```

**After:**
```php
// Controller
public function store(StoreUserRequest $request)
{
    $user = $this->userService->createWithProfile($request->validated());
    return redirect()->route('users.show', $user);
}

// Service
class UserCreationService
{
    public function createWithProfile(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::create($data);
            $this->createProfile($user, $data);
            $this->createSettings($user);
            return $user;
        });
    }
}
```

### Pattern 2: Complex Validation
**Before:**
```php
$request->validate([
    'domain' => [
        'required',
        function ($attribute, $value, $fail) {
            if (Domain::where('name', $value)->exists()) {
                $fail('Domain taken.');
            }
        },
    ],
]);
```

**After:**
```php
// Controller
$request->validate([
    'domain' => ['required', new UniqueDomain()],
]);

// Rule
class UniqueDomain implements Rule
{
    public function passes($attribute, $value): bool
    {
        return !Domain::where('name', $value)->exists();
    }
}
```

### Pattern 3: API Interactions
**Before:**
```php
public function process(Request $request)
{
    $response = Http::post('https://api.example.com/endpoint', [...]);
    $data = $response->json();
    // Process data...
}
```

**After:**
```php
// Controller
public function process(Request $request)
{
    $result = $this->apiService->processData($request->validated());
    return response()->json($result);
}

// Service
class ExternalApiService
{
    public function processData(array $data): array
    {
        $response = Http::post($this->endpoint, $data);
        return $this->transformResponse($response->json());
    }
}
```

---

## Testing Strategy

After refactoring, ensure you have tests for:

### Unit Tests
- [ ] Service classes
- [ ] Action classes
- [ ] Custom validation rules
- [ ] Model methods

### Feature Tests
- [ ] Controller endpoints
- [ ] End-to-end workflows
- [ ] Authentication/Authorization

### Example Service Test
```php
class TenantCreationServiceTest extends TestCase
{
    /** @test */
    public function it_creates_tenant_with_all_resources()
    {
        $service = new TenantCreationService();
        
        $tenant = $service->createTenant([
            'company_name' => 'Test Company',
            'domain' => 'test',
            // ...
        ]);
        
        $this->assertDatabaseHas('tenants', [
            'company_name' => 'Test Company'
        ]);
        $this->assertDatabaseHas('users', [
            'tenant_id' => $tenant->id
        ]);
    }
}
```

---

## Quality Metrics

After refactoring each controller, verify:

- [ ] **Method Length**: No method exceeds 25 lines
- [ ] **Cyclomatic Complexity**: Each method has complexity < 10
- [ ] **Dependencies**: Controller has 3 or fewer injected dependencies
- [ ] **Single Responsibility**: Each method does ONE thing
- [ ] **Testability**: Each method can be easily unit tested
- [ ] **Readability**: Code is self-documenting

---

## File Organization

Maintain this structure:

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Api/
│   │   │   └── V1/
│   │   ├── Auth/
│   │   └── [FeatureControllers].php
│   └── Requests/
│       ├── [Feature]/
│       │   ├── Store[Feature]Request.php
│       │   └── Update[Feature]Request.php
│       └── [OtherRequests].php
├── Services/
│   ├── [Feature]/
│   │   └── [Feature]Service.php
│   └── [OtherServices].php
├── Actions/
│   └── [Feature]/
│       └── [Action].php
├── Rules/
│   └── [ValidationRule].php
├── Traits/
│   └── [BehaviorTrait].php
└── Models/
    └── [Model].php
```

---

## Common Mistakes to Avoid

1. ❌ **Over-engineering**: Don't create services for simple CRUD
2. ❌ **Service Layer Bloat**: Keep services focused on specific domains
3. ❌ **Circular Dependencies**: Services shouldn't depend on controllers
4. ❌ **Inconsistent Patterns**: Use the same pattern across similar features
5. ❌ **Premature Optimization**: Refactor when you see patterns, not before
6. ❌ **Ignoring Type Hints**: Always use return types and parameter types
7. ❌ **Poor Naming**: Use descriptive names that reveal intent

---

## Refactoring Priority

Prioritize controllers in this order:

1. **High Priority**: Controllers with security implications (Auth, Payment, User Management)
2. **Medium Priority**: Core business logic controllers (Orders, Subscriptions, Tenant Management)
3. **Low Priority**: Simple CRUD controllers with minimal logic
4. **Last**: Admin/Dashboard controllers with mostly read operations

---

## Review Checklist

Before marking a controller as "refactored," verify:

- [ ] All business logic moved to appropriate services/actions
- [ ] Complex validation extracted to Request classes or Rules
- [ ] Model-specific logic moved to models
- [ ] Shared behavior extracted to traits
- [ ] Controller methods are thin and readable
- [ ] Proper error handling and logging implemented
- [ ] Type hints added to all methods
- [ ] Tests updated or created
- [ ] Documentation updated
- [ ] Code review completed by peer

---

## Example: Complete Refactoring

### Original Controller (Bad)
```php
class OrderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'payment_method' => 'required|in:card,paypal',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception('Insufficient stock');
                }
                $total += $product->price * $item['quantity'];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'total' => $total,
                'status' => 'pending',
            ]);

            foreach ($request->items as $item) {
                $product = Product::find($item['product_id']);
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                ]);
                $product->decrement('stock', $item['quantity']);
            }

            if ($request->payment_method === 'card') {
                $payment = Stripe::charge([
                    'amount' => $total * 100,
                    'currency' => 'usd',
                    'source' => $request->token,
                ]);
            }

            Mail::to(auth()->user())->send(new OrderConfirmation($order));

            DB::commit();
            return redirect()->route('orders.show', $order);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
```

### Refactored Version (Good)

**Controller:**
```php
class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->createOrder(
                auth()->user(),
                $request->validated()
            );

            return redirect()
                ->route('orders.show', $order)
                ->with('success', 'Order placed successfully!');
        } catch (InsufficientStockException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Some items are out of stock.']);
        } catch (PaymentFailedException $e) {
            return back()
                ->withInput()
                ->withErrors(['error' => 'Payment failed. Please try again.']);
        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);

            return back()
                ->withInput()
                ->withErrors(['error' => 'Failed to create order.']);
        }
    }
}
```

**Request:**
```php
class StoreOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => ['required', 'integer', 'min:1', new SufficientStock()],
            'payment_method' => 'required|in:card,paypal',
            'token' => 'required_if:payment_method,card',
        ];
    }
}
```

**Service:**
```php
class OrderService
{
    public function __construct(
        private PaymentService $paymentService,
        private NotificationService $notificationService
    ) {}

    public function createOrder(User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {
            $total = $this->calculateTotal($data['items']);

            $order = Order::create([
                'user_id' => $user->id,
                'total' => $total,
                'status' => 'pending',
            ]);

            $this->createOrderItems($order, $data['items']);
            $this->updateProductStock($data['items']);

            $this->paymentService->processPayment(
                $order,
                $data['payment_method'],
                $data['token'] ?? null
            );

            $this->notificationService->sendOrderConfirmation($order);

            return $order;
        });
    }

    protected function calculateTotal(array $items): float
    {
        return collect($items)->sum(function ($item) {
            $product = Product::find($item['product_id']);
            return $product->price * $item['quantity'];
        });
    }

    protected function createOrderItems(Order $order, array $items): void
    {
        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            $order->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->price,
            ]);
        }
    }

    protected function updateProductStock(array $items): void
    {
        foreach ($items as $item) {
            Product::find($item['product_id'])
                ->decrement('stock', $item['quantity']);
        }
    }
}
```

---

## Conclusion

Use this guide as your refactoring blueprint. Work through controllers systematically, applying these patterns consistently. The goal is clean, maintainable code that follows Laravel best practices and SOLID principles.

Remember: **Refactor incrementally, test thoroughly, and commit frequently.**

=== .ai/multitenancy_laravel rules ===

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

=== foundation rules ===

# Laravel Boost Guidelines

The Laravel Boost guidelines are specifically curated by Laravel maintainers for this application. These guidelines should be followed closely to enhance the user's satisfaction building Laravel applications.

## Foundational Context
This application is a Laravel application and its main Laravel ecosystems package & versions are below. You are an expert with them all. Ensure you abide by these specific packages & versions.

- php - 8.4.7
- inertiajs/inertia-laravel (INERTIA) - v2
- laravel/fortify (FORTIFY) - v1
- laravel/framework (LARAVEL) - v12
- laravel/prompts (PROMPTS) - v0
- laravel/wayfinder (WAYFINDER) - v0
- laravel/mcp (MCP) - v0
- laravel/pint (PINT) - v1
- laravel/sail (SAIL) - v1
- pestphp/pest (PEST) - v4
- phpunit/phpunit (PHPUNIT) - v12
- @inertiajs/react (INERTIA) - v2
- react (REACT) - v19
- tailwindcss (TAILWINDCSS) - v4
- @laravel/vite-plugin-wayfinder (WAYFINDER) - v0
- eslint (ESLINT) - v9
- prettier (PRETTIER) - v3

## Conventions
- You must follow all existing code conventions used in this application. When creating or editing a file, check sibling files for the correct structure, approach, and naming.
- Use descriptive names for variables and methods. For example, `isRegisteredForDiscounts`, not `discount()`.
- Check for existing components to reuse before writing a new one.

## Verification Scripts
- Do not create verification scripts or tinker when tests cover that functionality and prove it works. Unit and feature tests are more important.

## Application Structure & Architecture
- Stick to existing directory structure; don't create new base folders without approval.
- Do not change the application's dependencies without approval.

## Frontend Bundling
- If the user doesn't see a frontend change reflected in the UI, it could mean they need to run `npm run build`, `npm run dev`, or `composer run dev`. Ask them.

## Replies
- Be concise in your explanations - focus on what's important rather than explaining obvious details.

## Documentation Files
- You must only create documentation files if explicitly requested by the user.

=== boost rules ===

## Laravel Boost
- Laravel Boost is an MCP server that comes with powerful tools designed specifically for this application. Use them.

## Artisan
- Use the `list-artisan-commands` tool when you need to call an Artisan command to double-check the available parameters.

## URLs
- Whenever you share a project URL with the user, you should use the `get-absolute-url` tool to ensure you're using the correct scheme, domain/IP, and port.

## Tinker / Debugging
- You should use the `tinker` tool when you need to execute PHP to debug code or query Eloquent models directly.
- Use the `database-query` tool when you only need to read from the database.

## Reading Browser Logs With the `browser-logs` Tool
- You can read browser logs, errors, and exceptions using the `browser-logs` tool from Boost.
- Only recent browser logs will be useful - ignore old logs.

## Searching Documentation (Critically Important)
- Boost comes with a powerful `search-docs` tool you should use before any other approaches when dealing with Laravel or Laravel ecosystem packages. This tool automatically passes a list of installed packages and their versions to the remote Boost API, so it returns only version-specific documentation for the user's circumstance. You should pass an array of packages to filter on if you know you need docs for particular packages.
- The `search-docs` tool is perfect for all Laravel-related packages, including Laravel, Inertia, Livewire, Filament, Tailwind, Pest, Nova, Nightwatch, etc.
- You must use this tool to search for Laravel ecosystem documentation before falling back to other approaches.
- Search the documentation before making code changes to ensure we are taking the correct approach.
- Use multiple, broad, simple, topic-based queries to start. For example: `['rate limiting', 'routing rate limiting', 'routing']`.
- Do not add package names to queries; package information is already shared. For example, use `test resource table`, not `filament 4 test resource table`.

### Available Search Syntax
- You can and should pass multiple queries at once. The most relevant results will be returned first.

1. Simple Word Searches with auto-stemming - query=authentication - finds 'authenticate' and 'auth'.
2. Multiple Words (AND Logic) - query=rate limit - finds knowledge containing both "rate" AND "limit".
3. Quoted Phrases (Exact Position) - query="infinite scroll" - words must be adjacent and in that order.
4. Mixed Queries - query=middleware "rate limit" - "middleware" AND exact phrase "rate limit".
5. Multiple Queries - queries=["authentication", "middleware"] - ANY of these terms.

=== php rules ===

## PHP

- Always use curly braces for control structures, even if it has one line.

### Constructors
- Use PHP 8 constructor property promotion in `__construct()`.
    - <code-snippet>public function __construct(public GitHub $github) { }</code-snippet>
- Do not allow empty `__construct()` methods with zero parameters unless the constructor is private.

### Type Declarations
- Always use explicit return type declarations for methods and functions.
- Use appropriate PHP type hints for method parameters.

<code-snippet name="Explicit Return Types and Method Params" lang="php">
protected function isAccessible(User $user, ?string $path = null): bool
{
    ...
}
</code-snippet>

## Comments
- Prefer PHPDoc blocks over inline comments. Never use comments within the code itself unless there is something very complex going on.

## PHPDoc Blocks
- Add useful array shape type definitions for arrays when appropriate.

## Enums
- Typically, keys in an Enum should be TitleCase. For example: `FavoritePerson`, `BestLake`, `Monthly`.

=== tests rules ===

## Test Enforcement

- Every change must be programmatically tested. Write a new test or update an existing test, then run the affected tests to make sure they pass.
- Run the minimum number of tests needed to ensure code quality and speed. Use `php artisan test --compact` with a specific filename or filter.

=== inertia-laravel/core rules ===

## Inertia

- Inertia.js components should be placed in the `resources/js/Pages` directory unless specified differently in the JS bundler (`vite.config.js`).
- Use `Inertia::render()` for server-side routing instead of traditional Blade views.
- Use the `search-docs` tool for accurate guidance on all things Inertia.

<code-snippet name="Inertia Render Example" lang="php">
// routes/web.php example
Route::get('/users', function () {
    return Inertia::render('Users/Index', [
        'users' => User::all()
    ]);
});
</code-snippet>

=== inertia-laravel/v2 rules ===

## Inertia v2

- Make use of all Inertia features from v1 and v2. Check the documentation before making any changes to ensure we are taking the correct approach.

### Inertia v2 New Features
- Deferred props.
- Infinite scrolling using merging props and `WhenVisible`.
- Lazy loading data on scroll.
- Polling.
- Prefetching.

### Deferred Props & Empty States
- When using deferred props on the frontend, you should add a nice empty state with pulsing/animated skeleton.

### Inertia Form General Guidance
- The recommended way to build forms when using Inertia is with the `<Form>` component - a useful example is below. Use the `search-docs` tool with a query of `form component` for guidance.
- Forms can also be built using the `useForm` helper for more programmatic control, or to follow existing conventions. Use the `search-docs` tool with a query of `useForm helper` for guidance.
- `resetOnError`, `resetOnSuccess`, and `setDefaultsOnSuccess` are available on the `<Form>` component. Use the `search-docs` tool with a query of `form component resetting` for guidance.

=== laravel/core rules ===

## Do Things the Laravel Way

- Use `php artisan make:` commands to create new files (i.e. migrations, controllers, models, etc.). You can list available Artisan commands using the `list-artisan-commands` tool.
- If you're creating a generic PHP class, use `php artisan make:class`.
- Pass `--no-interaction` to all Artisan commands to ensure they work without user input. You should also pass the correct `--options` to ensure correct behavior.

### Database
- Always use proper Eloquent relationship methods with return type hints. Prefer relationship methods over raw queries or manual joins.
- Use Eloquent models and relationships before suggesting raw database queries.
- Avoid `DB::`; prefer `Model::query()`. Generate code that leverages Laravel's ORM capabilities rather than bypassing them.
- Generate code that prevents N+1 query problems by using eager loading.
- Use Laravel's query builder for very complex database operations.

### Model Creation
- When creating new models, create useful factories and seeders for them too. Ask the user if they need any other things, using `list-artisan-commands` to check the available options to `php artisan make:model`.

### APIs & Eloquent Resources
- For APIs, default to using Eloquent API Resources and API versioning unless existing API routes do not, then you should follow existing application convention.

### Controllers & Validation
- Always create Form Request classes for validation rather than inline validation in controllers. Include both validation rules and custom error messages.
- Check sibling Form Requests to see if the application uses array or string based validation rules.

### Queues
- Use queued jobs for time-consuming operations with the `ShouldQueue` interface.

### Authentication & Authorization
- Use Laravel's built-in authentication and authorization features (gates, policies, Sanctum, etc.).

### URL Generation
- When generating links to other pages, prefer named routes and the `route()` function.

### Configuration
- Use environment variables only in configuration files - never use the `env()` function directly outside of config files. Always use `config('app.name')`, not `env('APP_NAME')`.

### Testing
- When creating models for tests, use the factories for the models. Check if the factory has custom states that can be used before manually setting up the model.
- Faker: Use methods such as `$this->faker->word()` or `fake()->randomDigit()`. Follow existing conventions whether to use `$this->faker` or `fake()`.
- When creating tests, make use of `php artisan make:test [options] {name}` to create a feature test, and pass `--unit` to create a unit test. Most tests should be feature tests.

### Vite Error
- If you receive an "Illuminate\Foundation\ViteException: Unable to locate file in Vite manifest" error, you can run `npm run build` or ask the user to run `npm run dev` or `composer run dev`.

=== laravel/v12 rules ===

## Laravel 12

- Use the `search-docs` tool to get version-specific documentation.
- Since Laravel 11, Laravel has a new streamlined file structure which this project uses.

### Laravel 12 Structure
- In Laravel 12, middleware are no longer registered in `app/Http/Kernel.php`.
- Middleware are configured declaratively in `bootstrap/app.php` using `Application::configure()->withMiddleware()`.
- `bootstrap/app.php` is the file to register middleware, exceptions, and routing files.
- `bootstrap/providers.php` contains application specific service providers.
- The `app\Console\Kernel.php` file no longer exists; use `bootstrap/app.php` or `routes/console.php` for console configuration.
- Console commands in `app/Console/Commands/` are automatically available and do not require manual registration.

### Database
- When modifying a column, the migration must include all of the attributes that were previously defined on the column. Otherwise, they will be dropped and lost.
- Laravel 12 allows limiting eagerly loaded records natively, without external packages: `$query->latest()->limit(10);`.

### Models
- Casts can and likely should be set in a `casts()` method on a model rather than the `$casts` property. Follow existing conventions from other models.

=== wayfinder/core rules ===

## Laravel Wayfinder

Wayfinder generates TypeScript functions and types for Laravel controllers and routes which you can import into your client-side code. It provides type safety and automatic synchronization between backend routes and frontend code.

### Development Guidelines
- Always use the `search-docs` tool to check Wayfinder correct usage before implementing any features.
- Always prefer named imports for tree-shaking (e.g., `import { show } from '@/actions/...'`).
- Avoid default controller imports (prevents tree-shaking).
- Run `php artisan wayfinder:generate` after route changes if Vite plugin isn't installed.

### Feature Overview
- Form Support: Use `.form()` with `--with-form` flag for HTML form attributes — `<form {...store.form()}>` → `action="/posts" method="post"`.
- HTTP Methods: Call `.get()`, `.post()`, `.patch()`, `.put()`, `.delete()` for specific methods — `show.head(1)` → `{ url: "/posts/1", method: "head" }`.
- Invokable Controllers: Import and invoke directly as functions. For example, `import StorePost from '@/actions/.../StorePostController'; StorePost()`.
- Named Routes: Import from `@/routes/` for non-controller routes. For example, `import { show } from '@/routes/post'; show(1)` for route name `post.show`.
- Parameter Binding: Detects route keys (e.g., `{post:slug}`) and accepts matching object properties — `show("my-post")` or `show({ slug: "my-post" })`.
- Query Merging: Use `mergeQuery` to merge with `window.location.search`, set values to `null` to remove — `show(1, { mergeQuery: { page: 2, sort: null } })`.
- Query Parameters: Pass `{ query: {...} }` in options to append params — `show(1, { query: { page: 1 } })` → `"/posts/1?page=1"`.
- Route Objects: Functions return `{ url, method }` shaped objects — `show(1)` → `{ url: "/posts/1", method: "get" }`.
- URL Extraction: Use `.url()` to get URL string — `show.url(1)` → `"/posts/1"`.

### Example Usage

<code-snippet name="Wayfinder Basic Usage" lang="typescript">
    // Import controller methods (tree-shakable)...
    import { show, store, update } from '@/actions/App/Http/Controllers/PostController'

    // Get route object with URL and method...
    show(1) // { url: "/posts/1", method: "get" }

    // Get just the URL...
    show.url(1) // "/posts/1"

    // Use specific HTTP methods...
    show.get(1) // { url: "/posts/1", method: "get" }
    show.head(1) // { url: "/posts/1", method: "head" }

    // Import named routes...
    import { show as postShow } from '@/routes/post' // For route name 'post.show'
    postShow(1) // { url: "/posts/1", method: "get" }
</code-snippet>

### Wayfinder + Inertia
If your application uses the `<Form>` component from Inertia, you can use Wayfinder to generate form action and method automatically.
<code-snippet name="Wayfinder Form Component (React)" lang="typescript">

<Form {...store.form()}><input name="title" /></Form>

</code-snippet>

=== pint/core rules ===

## Laravel Pint Code Formatter

- You must run `vendor/bin/pint --dirty` before finalizing changes to ensure your code matches the project's expected style.
- Do not run `vendor/bin/pint --test`, simply run `vendor/bin/pint` to fix any formatting issues.

=== pest/core rules ===

## Pest
### Testing
- If you need to verify a feature is working, write or update a Unit / Feature test.

### Pest Tests
- All tests must be written using Pest. Use `php artisan make:test --pest {name}`.
- You must not remove any tests or test files from the tests directory without approval. These are not temporary or helper files - these are core to the application.
- Tests should test all of the happy paths, failure paths, and weird paths.
- Tests live in the `tests/Feature` and `tests/Unit` directories.
- Pest tests look and behave like this:
<code-snippet name="Basic Pest Test Example" lang="php">
it('is true', function () {
    expect(true)->toBeTrue();
});
</code-snippet>

### Running Tests
- Run the minimal number of tests using an appropriate filter before finalizing code edits.
- To run all tests: `php artisan test --compact`.
- To run all tests in a file: `php artisan test --compact tests/Feature/ExampleTest.php`.
- To filter on a particular test name: `php artisan test --compact --filter=testName` (recommended after making a change to a related file).
- When the tests relating to your changes are passing, ask the user if they would like to run the entire test suite to ensure everything is still passing.

### Pest Assertions
- When asserting status codes on a response, use the specific method like `assertForbidden` and `assertNotFound` instead of using `assertStatus(403)` or similar, e.g.:
<code-snippet name="Pest Example Asserting postJson Response" lang="php">
it('returns all', function () {
    $response = $this->postJson('/api/docs', []);

    $response->assertSuccessful();
});
</code-snippet>

### Mocking
- Mocking can be very helpful when appropriate.
- When mocking, you can use the `Pest\Laravel\mock` Pest function, but always import it via `use function Pest\Laravel\mock;` before using it. Alternatively, you can use `$this->mock()` if existing tests do.
- You can also create partial mocks using the same import or self method.

### Datasets
- Use datasets in Pest to simplify tests that have a lot of duplicated data. This is often the case when testing validation rules, so consider this solution when writing tests for validation rules.

<code-snippet name="Pest Dataset Example" lang="php">
it('has emails', function (string $email) {
    expect($email)->not->toBeEmpty();
})->with([
    'james' => 'james@laravel.com',
    'taylor' => 'taylor@laravel.com',
]);
</code-snippet>

=== pest/v4 rules ===

## Pest 4

- Pest 4 is a huge upgrade to Pest and offers: browser testing, smoke testing, visual regression testing, test sharding, and faster type coverage.
- Browser testing is incredibly powerful and useful for this project.
- Browser tests should live in `tests/Browser/`.
- Use the `search-docs` tool for detailed guidance on utilizing these features.

### Browser Testing
- You can use Laravel features like `Event::fake()`, `assertAuthenticated()`, and model factories within Pest 4 browser tests, as well as `RefreshDatabase` (when needed) to ensure a clean state for each test.
- Interact with the page (click, type, scroll, select, submit, drag-and-drop, touch gestures, etc.) when appropriate to complete the test.
- If requested, test on multiple browsers (Chrome, Firefox, Safari).
- If requested, test on different devices and viewports (like iPhone 14 Pro, tablets, or custom breakpoints).
- Switch color schemes (light/dark mode) when appropriate.
- Take screenshots or pause tests for debugging when appropriate.

### Example Tests

<code-snippet name="Pest Browser Test Example" lang="php">
it('may reset the password', function () {
    Notification::fake();

    $this->actingAs(User::factory()->create());

    $page = visit('/sign-in'); // Visit on a real browser...

    $page->assertSee('Sign In')
        ->assertNoJavascriptErrors() // or ->assertNoConsoleLogs()
        ->click('Forgot Password?')
        ->fill('email', 'nuno@laravel.com')
        ->click('Send Reset Link')
        ->assertSee('We have emailed your password reset link!')

    Notification::assertSent(ResetPassword::class);
});
</code-snippet>

<code-snippet name="Pest Smoke Testing Example" lang="php">
$pages = visit(['/', '/about', '/contact']);

$pages->assertNoJavascriptErrors()->assertNoConsoleLogs();
</code-snippet>

=== inertia-react/core rules ===

## Inertia + React

- Use `router.visit()` or `<Link>` for navigation instead of traditional links.

<code-snippet name="Inertia Client Navigation" lang="react">

import { Link } from '@inertiajs/react'
<Link href="/">Home</Link>

</code-snippet>

=== inertia-react/v2/forms rules ===

## Inertia v2 + React Forms

<code-snippet name="`<Form>` Component Example" lang="react">

import { Form } from '@inertiajs/react'

export default () => (
    <Form action="/users" method="post">
        {({
            errors,
            hasErrors,
            processing,
            wasSuccessful,
            recentlySuccessful,
            clearErrors,
            resetAndClearErrors,
            defaults
        }) => (
        <>
        <input type="text" name="name" />

        {errors.name && <div>{errors.name}</div>}

        <button type="submit" disabled={processing}>
            {processing ? 'Creating...' : 'Create User'}
        </button>

        {wasSuccessful && <div>User created successfully!</div>}
        </>
    )}
    </Form>
)

</code-snippet>

=== tailwindcss/core rules ===

## Tailwind CSS

- Use Tailwind CSS classes to style HTML; check and use existing Tailwind conventions within the project before writing your own.
- Offer to extract repeated patterns into components that match the project's conventions (i.e. Blade, JSX, Vue, etc.).
- Think through class placement, order, priority, and defaults. Remove redundant classes, add classes to parent or child carefully to limit repetition, and group elements logically.
- You can use the `search-docs` tool to get exact examples from the official documentation when needed.

### Spacing
- When listing items, use gap utilities for spacing; don't use margins.

<code-snippet name="Valid Flex Gap Spacing Example" lang="html">
    <div class="flex gap-8">
        <div>Superior</div>
        <div>Michigan</div>
        <div>Erie</div>
    </div>
</code-snippet>

### Dark Mode
- If existing pages and components support dark mode, new pages and components must support dark mode in a similar way, typically using `dark:`.

=== tailwindcss/v4 rules ===

## Tailwind CSS 4

- Always use Tailwind CSS v4; do not use the deprecated utilities.
- `corePlugins` is not supported in Tailwind v4.
- In Tailwind v4, configuration is CSS-first using the `@theme` directive — no separate `tailwind.config.js` file is needed.

<code-snippet name="Extending Theme in CSS" lang="css">
@theme {
  --color-brand: oklch(0.72 0.11 178);
}
</code-snippet>

- In Tailwind v4, you import Tailwind using a regular CSS `@import` statement, not using the `@tailwind` directives used in v3:

<code-snippet name="Tailwind v4 Import Tailwind Diff" lang="diff">
   - @tailwind base;
   - @tailwind components;
   - @tailwind utilities;
   + @import "tailwindcss";
</code-snippet>

### Replaced Utilities
- Tailwind v4 removed deprecated utilities. Do not use the deprecated option; use the replacement.
- Opacity values are still numeric.

| Deprecated |	Replacement |
|------------+--------------|
| bg-opacity-* | bg-black/* |
| text-opacity-* | text-black/* |
| border-opacity-* | border-black/* |
| divide-opacity-* | divide-black/* |
| ring-opacity-* | ring-black/* |
| placeholder-opacity-* | placeholder-black/* |
| flex-shrink-* | shrink-* |
| flex-grow-* | grow-* |
| overflow-ellipsis | text-ellipsis |
| decoration-slice | box-decoration-slice |
| decoration-clone | box-decoration-clone |
</laravel-boost-guidelines>
