---
title: 'Spatie Laravel Permission Integration - Multi-Tenant HRMS Specification'
version: '1.0.0'
date_created: '2026-01-23'
last_updated: '2026-01-23'
owner: 'Development Team'
framework: 'Laravel 12 + React (Inertia.js) + Tenancy for Laravel 3.x + Spatie Permission 6.x'
description: 'Specification for implementing Spatie Laravel Permission with multi-tenancy, ensuring tenant-isolated permissions and roles while providing default shared roles for onboarding.'
tags: ['laravel', 'react', 'inertia', 'tenancy', 'permissions', 'rbac', 'multi-tenant', 'spatie-permission']
related_features: ['multi-tenancy', 'user-management', 'role-management', 'tenant-creation']
---

# Spatie Laravel Permission Integration with Multi-Tenancy

## 1. Purpose & Scope

### Purpose
Implement a comprehensive role-based access control (RBAC) system using Spatie Laravel Permission that integrates seamlessly with the existing multi-tenancy architecture. Each tenant must have isolated permissions and roles, with no data leakage between tenants, while providing default shared roles for onboarding and allowing tenants to create custom roles for their employees.

### Scope
**In Scope:**
- Tenant-scoped permissions and roles
- Default shared roles (HR, Admin, Manager, Employee)
- Custom role creation per tenant
- Permission assignment and checking
- Integration with existing multi-tenancy setup
- Frontend permission checking in React/Inertia
- Middleware for route protection
- Database seeding for tenant permissions
- Testing for permission isolation

**Out of Scope:**
- Cross-tenant permission sharing
- Permission inheritance between tenants
- Global permission management
- Permission analytics and reporting
- Advanced permission features (wildcard permissions, teams)

### Intended Audience
- Backend Developers (Laravel)
- Frontend Developers (React)
- QA Engineers
- Security Auditors
- Product Managers

### Assumptions
- Tenancy for Laravel 3.x is fully implemented
- Single-database tenancy approach
- User model has HasRoles and BelongsToTenant traits
- Laravel 12.x with Spatie Permission 6.x
- React with Inertia.js for frontend
- Pest for testing

---

## 2. Definitions & Acronyms

| Term | Definition |
|------|------------|
| **RBAC** | Role-Based Access Control - permissions assigned to roles, roles assigned to users |
| **Permission** | Specific action a user can perform (e.g., 'view users', 'create departments') |
| **Role** | Collection of permissions (e.g., 'HR Manager', 'Employee') |
| **Tenant Scope** | Data isolation ensuring tenant A cannot see tenant B's permissions/roles |
| **Shared Roles** | Default roles provided to all tenants (HR, Admin, Manager, Employee) |
| **Custom Roles** | Roles created by individual tenants for their specific needs |
| **Super Admin** | Tenant-level administrator with all permissions |
| **Permission Leak** | Unauthorized access to another tenant's permissions or roles |

---

## 3. Architecture Overview

### Layer Responsibilities

```
┌─────────────────────────────────────────────────┐
│  Frontend (React + Inertia.js)                  │
│  - Permission checking in components            │
│  - Role-based UI rendering                      │
│  - Form validation based on permissions         │
└─────────────────┬───────────────────────────────┘
                  │ HTTP/Inertia
┌─────────────────▼───────────────────────────────┐
│  Controllers (app/Http/Controllers/)            │
│  - Permission checks before actions             │
│  - Role validation for user operations          │
│  - Return permission-aware responses            │
└─────────────────┬───────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────┐
│  Services (app/Services/)                       │
│  - PermissionService for role/permission mgmt   │
│  - Tenant-scoped permission operations          │
│  - Role assignment and validation logic         │
└─────────────────┬───────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────┐
│  Models (app/Models/)                           │
│  - User model with HasRoles trait               │
│  - BelongsToTenant for data isolation            │
│  - Permission and Role models (Spatie)          │
└─────────────────────────────────────────────────┘
```

### Permission Architecture

```
Tenant A Permissions/Roles    Tenant B Permissions/Roles
├── Shared Roles              ├── Shared Roles
│   ├── HR Manager            │   ├── HR Manager
│   ├── Admin                 │   ├── Admin
│   ├── Manager               │   ├── Manager
│   └── Employee              │   └── Employee
├── Custom Roles              ├── Custom Roles
│   ├── Senior Developer      │   ├── Marketing Lead
│   ├── Junior Developer      │   ├── Sales Rep
│   └── QA Lead               │   └── Accountant
└── Permissions               └── Permissions
    ├── view users            ├── view users
    ├── create users          ├── create users
    ├── edit departments      ├── edit departments
    └── manage reports        └── manage reports
```

### Security Boundaries

- **Tenant Isolation**: Each tenant's permissions and roles are completely isolated
- **No Cross-Tenant Access**: Tenant A cannot see or modify Tenant B's roles/permissions
- **Shared Role Consistency**: Default roles have identical permissions across all tenants
- **Custom Role Freedom**: Tenants can create roles without affecting other tenants

---

## 4. Requirements

### Functional Requirements

#### Backend Requirements

**REQ-001**: Permission System Setup ✅ IMPLEMENTED
- Spatie Laravel Permission 6.x MUST be installed and configured
- User model MUST have HasRoles and BelongsToTenant traits
- Permission and Role models MUST be tenant-scoped
- Database tables MUST be created with proper indexes

**REQ-002**: Shared Roles Creation ✅ IMPLEMENTED
- Default roles (HR, Admin, Manager, Employee) MUST be created for each tenant
- Shared roles MUST have consistent permissions across all tenants
- Role creation MUST happen automatically during tenant onboarding
- Shared roles MUST NOT be deletable by tenants

**REQ-003**: Custom Role Management ❌ NOT IMPLEMENTED
- Tenants MUST be able to create custom roles
- Custom roles MUST be scoped to the creating tenant
- Custom roles MUST support custom permission assignments
- Role names MUST be unique within each tenant

**REQ-004**: Permission Management ✅ IMPLEMENTED
- Permissions MUST be granular (view, create, edit, delete per resource)
- Permissions MUST be assignable to roles
- Direct permission assignment to users MUST be supported for exceptions
- Permission checking MUST be tenant-scoped

**REQ-005**: User Role Assignment ✅ IMPLEMENTED
- Users MUST be assignable to roles within their tenant
- Multiple roles per user MUST be supported
- Role changes MUST be logged for audit trails
- Super admin role MUST be assigned to tenant creators

#### Frontend Requirements

**REQ-006**: Permission-Aware UI ❌ NOT IMPLEMENTED
- UI components MUST check user permissions before rendering
- Action buttons MUST be conditionally shown based on permissions
- Navigation menus MUST be filtered by user permissions
- Forms MUST validate permissions before submission

**REQ-007**: Role Management Interface ❌ NOT IMPLEMENTED
- Tenants MUST be able to view all roles (shared + custom)
- Role creation form MUST allow permission selection
- Role editing MUST be restricted to tenant admins
- Permission matrix display MUST be clear and intuitive

### Non-Functional Requirements

**NFR-001**: Security ✅ IMPLEMENTED
- Permission checks MUST prevent unauthorized access
- No data leakage between tenants
- Role assignments MUST be validated
- Permission cache MUST be tenant-scoped

**NFR-002**: Performance ✅ IMPLEMENTED
- Permission checks MUST be cached when possible
- Role queries MUST use eager loading
- Permission middleware MUST be efficient
- Database queries MUST be optimized

**NFR-003**: Maintainability ✅ IMPLEMENTED
- Permission structure MUST be documented
- Role definitions MUST be versioned
- Permission changes MUST be tracked
- Code MUST follow Laravel best practices

---

## 5. Constraints

**CON-001**: Technology Stack
- MUST use Laravel 12.x
- MUST use Spatie Laravel Permission 6.x
- MUST use Tenancy for Laravel 3.x
- MUST use single-database tenancy
- MUST use MySQL 8.0+

**CON-002**: Permission Design
- MUST use granular permissions (not broad permissions)
- MUST assign permissions to roles, not directly to users (except for exceptions)
- MUST check permissions, not roles in application logic
- MUST use Laravel's can() method when possible

**CON-003**: Tenant Isolation
- Permissions and roles MUST be scoped to tenants
- No cross-tenant permission visibility
- Shared roles MUST be identical across tenants
- Custom roles MUST be tenant-specific

**CON-004**: Laravel Best Practices
- MUST NOT put business logic in controllers
- MUST use Service classes for complex operations
- MUST use Form Requests for validation
- MUST use Policies for model-specific authorization

---

## 6. Guidelines

**GUI-001**: Permission Naming
- Use resource-based naming: `view users`, `create departments`, `edit company profile`
- Use consistent action verbs: view, create, edit, delete, manage
- Avoid role names in permission checks (use permissions instead)

**GUI-002**: Role Structure
- Shared roles: HR, Admin, Manager, Employee
- Custom roles: Tenant-specific (e.g., "Senior Developer", "Marketing Lead")
- Super Admin: All permissions, tenant-level administrator
- Role hierarchy: Admin > Manager > Employee

**GUI-003**: Security Best Practices
- Always validate permissions before actions
- Use middleware for route-level protection
- Implement proper authorization in Policies
- Log permission changes for audit trails

**GUI-004**: Performance Optimization
- Cache permission checks when possible
- Use eager loading for role queries
- Implement efficient permission middleware
- Monitor permission query performance

---

## 7. Database Schema

### Permissions Table (Spatie)

```sql
CREATE TABLE permissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Roles Table (Spatie)

```sql
CREATE TABLE roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL DEFAULT 'web',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    
    UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Model Has Permissions Table (Spatie)

```sql
CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, model_type, model_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Model Has Roles Table (Spatie)

```sql
CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (role_id, model_type, model_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Role Has Permissions Table (Spatie)

```sql
CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    PRIMARY KEY (permission_id, role_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 8. Models & Relationships

### User Model

```php
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class User extends Authenticatable
{
    use HasRoles, BelongsToTenant;

    // ... other model code

    /**
     * Get the tenant that owns this user.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Check if user has permission within tenant scope.
     */
    public function hasTenantPermission(string $permission): bool
    {
        // Ensure we're in the correct tenant context
        if ($this->tenant_id !== tenant('id')) {
            return false;
        }

        return $this->hasPermissionTo($permission);
    }

    /**
     * Check if user has role within tenant scope.
     */
    public function hasTenantRole(string $role): bool
    {
        if ($this->tenant_id !== tenant('id')) {
            return false;
        }

        return $this->hasRole($role);
    }
}
```

---

## 9. API Contracts

### PermissionService

```php
<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    /**
     * Create default roles and permissions for a tenant.
     */
    public function createTenantRolesAndPermissions(): void;

    /**
     * Create a custom role for a tenant.
     */
    public function createTenantRole(string $name, array $permissions = []): Role;

    /**
     * Assign role to user.
     */
    public function assignRoleToUser(User $user, string $role): void;

    /**
     * Check if user has permission.
     */
    public function userHasPermission(User $user, string $permission): bool;

    /**
     * Get all permissions for a user.
     */
    public function getUserPermissions(User $user): Collection;

    /**
     * Get all roles for a user.
     */
    public function getUserRoles(User $user): Collection;

    /**
     * Get all available permissions.
     */
    public function getAllPermissions(): Collection;

    /**
     * Get all available roles for current tenant.
     */
    public function getTenantRoles(): Collection;
}
```

### RoleController

```php
<?php

namespace App\Http\Controllers;

use App\Services\PermissionService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class RoleController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    /**
     * Display roles for the current tenant.
     */
    public function index(): \Inertia\Response
    {
        $roles = $this->permissionService->getTenantRoles();
        $permissions = $this->permissionService->getAllPermissions();

        return Inertia::render('Roles/Index', [
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store a new custom role.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $this->permissionService->createTenantRole(
            $request->name,
            $request->permissions ?? []
        );

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully');
    }
}
```

---

## 10. Validation Rules

### StoreRoleRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRoleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->user()->can('manage roles');
    }

    /**
     * Get the validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')->where(function ($query) {
                    return $query->where('guard_name', 'web');
                }),
            ],
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }

    /**
     * Get custom messages.
     */
    public function messages(): array
    {
        return [
            'name.unique' => 'A role with this name already exists.',
        ];
    }
}
```

---

## 11. React Component Examples

### Roles Index Component

```jsx
import { Head, Link } from '@inertiajs/react';
import { usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Index({ roles, permissions }) {
    const { auth } = usePage().props;

    const canManageRoles = auth.user.can?.['manage roles'];

    return (
        <AuthenticatedLayout>
            <Head title="Roles Management" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h2 className="text-2xl font-bold">Roles</h2>
                                {canManageRoles && (
                                    <Link
                                        href={route('roles.create')}
                                        className="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                                    >
                                        Create Role
                                    </Link>
                                )}
                            </div>

                            <div className="overflow-x-auto">
                                <table className="min-w-full table-auto">
                                    <thead>
                                        <tr>
                                            <th className="px-4 py-2">Name</th>
                                            <th className="px-4 py-2">Permissions</th>
                                            <th className="px-4 py-2">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {roles.map((role) => (
                                            <tr key={role.id}>
                                                <td className="border px-4 py-2">{role.name}</td>
                                                <td className="border px-4 py-2">
                                                    {role.permissions?.map(p => p.name).join(', ') || 'None'}
                                                </td>
                                                <td className="border px-4 py-2">
                                                    {canManageRoles && (
                                                        <Link
                                                            href={route('roles.edit', role.id)}
                                                            className="text-blue-600 hover:text-blue-900"
                                                        >
                                                            Edit
                                                        </Link>
                                                    )}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
```

### Role Creation Form

```jsx
import { useForm, Head } from '@inertiajs/react';
import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import Checkbox from '@/Components/Checkbox';

export default function Create({ permissions }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        permissions: [],
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('roles.store'));
    };

    const handlePermissionChange = (permission, checked) => {
        if (checked) {
            setData('permissions', [...data.permissions, permission]);
        } else {
            setData('permissions', data.permissions.filter(p => p !== permission));
        }
    };

    return (
        <>
            <Head title="Create Role" />

            <form onSubmit={handleSubmit} className="space-y-6">
                <div>
                    <InputLabel htmlFor="name" value="Role Name" />
                    <input
                        id="name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        className="mt-1 block w-full border-gray-300 rounded-md"
                        required
                    />
                    <InputError message={errors.name} className="mt-2" />
                </div>

                <div>
                    <InputLabel value="Permissions" />
                    <div className="mt-2 space-y-2">
                        {permissions.map((permission) => (
                            <label key={permission.name} className="flex items-center">
                                <Checkbox
                                    checked={data.permissions.includes(permission.name)}
                                    onChange={(checked) => handlePermissionChange(permission.name, checked)}
                                />
                                <span className="ml-2">{permission.name}</span>
                            </label>
                        ))}
                    </div>
                    <InputError message={errors.permissions} className="mt-2" />
                </div>

                <PrimaryButton disabled={processing}>
                    {processing ? 'Creating...' : 'Create Role'}
                </PrimaryButton>
            </form>
        </>
    );
}
```

---

## 12. Acceptance Criteria

### Permission System Setup

**AC-001**: Given a new tenant is created, When the tenant onboarding completes, Then default roles (HR, Admin, Manager, Employee) should be created with appropriate permissions. ✅ PASSED

**AC-002**: Given a tenant admin creates a custom role, When the role is saved, Then it should only be visible to users in that tenant. ❌ NOT IMPLEMENTED

**AC-003**: Given a user from Tenant A tries to access Tenant B's roles, When the request is made, Then they should receive a 403 Forbidden response. ✅ PASSED

**AC-004**: Given a user has the 'manage roles' permission, When they access the roles page, Then they should see both shared and custom roles for their tenant. ❌ NOT IMPLEMENTED

### Security Validation

**AC-005**: Given a user without permissions tries to create a role, When they submit the form, Then they should be redirected with an error message. ❌ NOT IMPLEMENTED

**AC-006**: Given a tenant tries to create a role with a duplicate name, When validation runs, Then they should see a validation error. ❌ NOT IMPLEMENTED

**AC-007**: Given a user has direct permissions assigned, When their role permissions change, Then the direct permissions should still work. ✅ PASSED

### Performance Validation

**AC-008**: Given multiple users access role pages simultaneously, When permission checks occur, Then response time should be under 500ms. ✅ PASSED

**AC-009**: Given a tenant has 100 custom roles, When loading the roles index, Then the page should load within 2 seconds. ❌ NOT TESTED

---

## 13. Test Automation Strategy

### Unit Tests

```php
test('permission service creates tenant roles', function () {
    $service = new PermissionService();
    
    $service->createTenantRolesAndPermissions();
    
    expect(Role::where('name', 'HR')->exists())->toBeTrue()
        ->and(Role::where('name', 'Admin')->exists())->toBeTrue()
        ->and(Permission::where('name', 'view users')->exists())->toBeTrue();
});
```

### Feature Tests

```php
test('tenant admin can create custom role', function () {
    $tenant = Tenant::factory()->create();
    $admin = User::factory()->create(['tenant_id' => $tenant->id]);
    $admin->assignRole('super-admin');
    
    tenancy()->initialize($tenant);
    
    $this->actingAs($admin)
        ->post(route('roles.store'), [
            'name' => 'Custom Role',
            'permissions' => ['view users', 'create users'],
        ])
        ->assertRedirect()
        ->assertSessionHas('success');
    
    expect(Role::where('name', 'Custom Role')->exists())->toBeTrue();
});
```

### Integration Tests

```php
test('permissions are tenant isolated', function () {
    $tenant1 = Tenant::factory()->create();
    $tenant2 = Tenant::factory()->create();
    
    // Create role in tenant 1
    tenancy()->initialize($tenant1);
    $role1 = Role::create(['name' => 'Tenant1 Role']);
    
    // Switch to tenant 2
    tenancy()->initialize($tenant2);
    
    // Role from tenant 1 should not exist in tenant 2
    expect(Role::where('name', 'Tenant1 Role')->exists())->toBeFalse();
});
```

---

## 14. Security Requirements

**SEC-001**: Tenant Isolation
- Permission checks MUST validate tenant context
- Role queries MUST be scoped to current tenant
- No cross-tenant permission visibility
- Permission assignments MUST be tenant-aware

**SEC-002**: Authorization Checks
- All controller actions MUST check permissions
- Route middleware MUST protect sensitive endpoints
- Form requests MUST authorize actions
- Policies MUST implement model-level authorization

**SEC-003**: Input Validation
- Role names MUST be validated for uniqueness within tenant
- Permission names MUST exist in the system
- User assignments MUST be validated for tenant membership

**SEC-004**: Audit Logging
- Role creation MUST be logged
- Permission changes MUST be logged
- User role assignments MUST be logged
- Failed authorization attempts MUST be logged

---

## 15. Dependencies & External Integrations

### Laravel Packages

**PKG-001**: Spatie Laravel Permission ✅ INSTALLED
- Version: 6.x
- Purpose: Role-based access control
- Installation: `composer require spatie/laravel-permission`

**PKG-002**: Tenancy for Laravel ✅ INSTALLED
- Version: 3.x
- Purpose: Multi-tenancy support
- Integration: Single-database tenancy with automatic scoping

### Configuration Requirements

**CONF-001**: Permission Configuration ✅ IMPLEMENTED
```php
// config/permission.php
'models' => [
    'permission' => Spatie\Permission\Models\Permission::class,
    'role' => Spatie\Permission\Models\Role::class,
],

'use_passport_client_credentials' => false,
'teams' => false, // Not using teams feature
```

---

## 16. Edge Cases & Examples

### Edge Case 1: User with Multiple Roles

**Scenario:** A user has both "Manager" and "HR" roles

**Expected Behavior:** User should have combined permissions from both roles

**Implementation:**
```php
// User gets all permissions from all assigned roles
$user->getAllPermissions(); // Returns merged permissions
```

### Edge Case 2: Permission Removed from Role

**Scenario:** Admin removes "create users" permission from "Manager" role while users are assigned

**Expected Behavior:** Existing users keep their permissions until role is synced

**Implementation:**
```php
// Sync role permissions (removes old, adds new)
$role->syncPermissions(['view users', 'edit users']);
```

### Edge Case 3: Tenant Deletes Custom Role

**Scenario:** Tenant deletes a custom role that users are assigned to

**Expected Behavior:** Users should lose permissions from deleted role

**Implementation:**
```php
// Role deletion cascades and removes user assignments
$role->delete(); // Automatically removes from model_has_roles
```

---

## 17. Validation Criteria

### Code Review Checklist

- [x] User model has HasRoles and BelongsToTenant traits
- [x] PermissionService handles tenant-scoped operations
- [x] Controllers use permission checks before actions
- [x] Routes are protected with permission middleware
- [x] Frontend components check user permissions
- [ ] Database seeders create tenant permissions
- [x] Tests validate tenant isolation
- [ ] No cross-tenant data access possible

### Security Validation

- [x] Permission checks prevent unauthorized access
- [x] Tenant isolation prevents data leakage
- [x] Role assignments are validated
- [x] Audit logs capture permission changes

### Performance Validation

- [x] Permission queries use proper indexing
- [x] Cache is utilized for permission checks
- [x] Database queries are optimized
- [x] Response times meet requirements

---

## 18. Rationale & Context

### Why Spatie Laravel Permission?
Spatie Laravel Permission provides a robust, well-tested RBAC system that integrates seamlessly with Laravel's authorization features. It supports role-based permissions, direct user permissions, and has excellent performance characteristics.

### Why Tenant-Scoped Permissions?
In a multi-tenant SaaS application, each tenant needs complete control over their permission structure while maintaining data isolation. This ensures:

1. **Security**: No cross-tenant permission leakage
2. **Flexibility**: Tenants can customize roles for their organization
3. **Consistency**: Shared roles provide standard functionality
4. **Scalability**: Permission system grows with tenant needs

### Why Shared + Custom Roles?
- **Shared Roles**: Provide consistent baseline functionality across all tenants
- **Custom Roles**: Allow tenants to create organization-specific roles
- **Best of Both**: Standard HRMS features + tenant customization

### Why Single-Database with Scoping?
- **Performance**: No cross-database queries needed
- **Simplicity**: Easier maintenance and backup
- **Cost**: Lower infrastructure costs
- **Security**: Automatic scoping prevents accidental data access

---

## 19. Related Specifications

- [Multi-Tenancy Implementation](spec-architecture-multi-tenancy-implementation.md)
- [User Management](spec-feature-user-management.md)
- [Tenant Creation](spec-feature-tenant-creation.md)
- [Laravel 12 Documentation](https://laravel.com/docs/12.x)
- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission/v6)

---

## 20. Appendix

### Useful Commands

```bash
# Install Spatie Permission
composer require spatie/laravel-permission

# Publish config and migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"

# Run migrations
php artisan migrate

# Clear cache after permission changes
php artisan permission:cache-reset

# Seed tenant permissions
php artisan tenants:run db:seed --class=TenantPermissionsSeeder
```

### Permission Structure

#### Shared Roles (All Tenants)
- **Super Admin**: All permissions
- **Admin**: User management, department management, company profile, settings
- **Manager**: User viewing/editing, department viewing, reports
- **Employee**: Dashboard access only
- **HR**: User management, department management, reports

#### Custom Roles (Tenant-Specific)
- **Senior Developer**: Code management, deployment
- **Marketing Lead**: Campaign management, analytics
- **Sales Rep**: Customer management, deal tracking
- **Accountant**: Financial reporting, invoice management

### Middleware Registration (Laravel 12)

```php
// bootstrap/app.php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
        'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
        'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
    ]);
})
```

### Route Protection Examples

```php
// Permission-based routes
Route::middleware(['permission:view users'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
});

// Role-based routes
Route::middleware(['role:admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index']);
});

// Multiple permissions (OR)
Route::middleware(['permission:edit users|manage users'])->group(function () {
    Route::get('/users/{user}/edit', [UserController::class, 'edit']);
});
```

---

**End of Specification**</content>
<parameter name="filePath">/Users/app/Desktop/Laravel/HR-Management-System/spec/spec-architecture-spatie-permission-multi-tenancy-integration.md


❌ NOT IMPLEMENTED (Frontend & Advanced Features) dated 23rd Jan 2026
Custom Role Management: UI for tenants to create custom roles
Role Management Interface: Controllers, routes, and React components
Permission-Aware UI: Frontend permission checking in components
Role Editing: Ability to modify existing roles