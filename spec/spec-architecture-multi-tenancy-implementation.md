---
title: 'Multi-Tenancy Implementation - Laravel 12 Specification'
version: '1.0.0'
date_created: '2026-01-23'
last_updated: '2026-01-23'
owner: 'Development Team'
framework: 'Laravel 12 + React (Inertia.js) + Tenancy for Laravel 3.x'
description: 'Comprehensive specification for implementing and completing multi-tenancy in the HR Management System using single-database tenancy approach.'
tags: ['laravel', 'react', 'inertia', 'tenancy', 'multi-tenant', 'saas', 'hr-management']
related_features: ['tenant-creation', 'user-management', 'subscription-management', 'permissions']
---

# Multi-Tenancy Implementation

## 1. Purpose & Scope

### Purpose
Implement a complete multi-tenancy solution for the HR Management System using Tenancy for Laravel package, enabling each tenant (company) to have isolated users, departments, company profiles, and other resources while sharing the same database infrastructure.

### Scope
**In Scope:**
- Single-database tenancy with automatic scoping
- Domain-based tenant identification
- Tenant creation and onboarding flow
- User management per tenant
- Department and company profile management
- Subscription plan integration
- Permission system integration (Spatie Laravel Permission)
- Queue and cache isolation
- Testing framework for tenant contexts

**Out of Scope:**
- Multi-database tenancy (separate databases per tenant)
- Subdomain or path-based identification
- Teams/organizations within tenants
- Cross-tenant data sharing
- Tenant migration between databases

### Intended Audience
- Backend Developers (Laravel)
- Frontend Developers (React)
- QA Engineers
- DevOps Engineers
- Product Managers

### Assumptions
- Laravel 12.x with Tenancy for Laravel 3.x
- MySQL 8.0+ database
- Single-database tenancy approach
- Domain-based tenant identification
- React with Inertia.js for frontend
- Pest for testing

---

## 2. Definitions & Acronyms

| Term | Definition |
|------|------------|
| **Tenant** | A company/organization using the HRMS with isolated data |
| **Central App** | The application context for tenant creation and management |
| **Tenant App** | The application context within a specific tenant's scope |
| **SaaS** | Software as a Service - multi-tenant application model |
| **HRMS** | Human Resource Management System |
| **Inertia.js** | Modern monolith architecture connecting Laravel and React |
| **Spatie Permission** | Laravel package for role-based access control |

---

## 3. Architecture Overview

### Layer Responsibilities

```
┌─────────────────────────────────────────────────┐
│  Frontend (React + Inertia.js)                  │
│  - Tenant-specific UI components                │
│  - User authentication and authorization        │
│  - Form validation (client-side)                │
└─────────────────┬───────────────────────────────┘
                  │ HTTP/Inertia
┌─────────────────▼───────────────────────────────┐
│  Controllers (app/Http/Controllers/)            │
│  - HTTP request/response handling               │
│  - Delegate to Services                         │
│  - Return Inertia responses                     │
└─────────────────┬───────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────┐
│  Services (app/Services/)                       │
│  - Business logic for tenant operations         │
│  - Tenant creation and management               │
│  - Permission and role management               │
└─────────────────┬───────────────────────────────┘
                  │
┌─────────────────▼───────────────────────────────┐
│  Models (app/Models/)                           │
│  - Eloquent models with BelongsToTenant trait   │
│  - Relationships and business logic             │
│  - Automatic tenant scoping                     │
└─────────────────┬───────────────────────────────┘
                  │ Tenancy Layer
┌─────────────────▼───────────────────────────────┐
│  Tenancy for Laravel Package                    │
│  - Tenant identification and initialization     │
│  - Automatic model scoping                      │
│  - Cache and filesystem isolation               │
│  - Queue tenant-awareness                       │
└─────────────────────────────────────────────────┘
```

### Tenancy Type: Single-Database
- All tenants share the same database
- Data isolation achieved through automatic scoping (`tenant_id` columns)
- Lower operational complexity
- Suitable for HRMS with moderate tenant count

### File Structure
```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Tenant/
│   │   │   └── [Tenant-specific controllers]
│   │   └── [Central controllers]
│   ├── Requests/
│   │   └── [Validation requests]
│   └── Middleware/
│       └── [Custom middleware]
├── Services/
│   ├── TenantCreationService.php
│   ├── PermissionService.php
│   └── [Other services]
├── Models/
│   ├── Tenant.php
│   ├── User.php (BelongsToTenant)
│   ├── Department.php (BelongsToTenant)
│   ├── CompanyProfile.php (BelongsToTenant)
│   └── [Other models]
├── Providers/
│   └── TenancyServiceProvider.php
└── Policies/
    └── [Authorization policies]

config/
├── tenancy.php
├── permission.php (Spatie)
└── [Other configs]

routes/
├── web.php (Central routes wrapped in domain groups)
├── tenant.php (Tenant routes with identification middleware)
└── [Other route files]

database/
├── migrations/
│   ├── [Central migrations]
│   └── tenant/ (if needed for tenant-specific schemas)
└── seeders/
    └── [Seeders]

tests/
├── Feature/
│   ├── Tenant/
│   │   └── [Tenant-specific tests]
│   └── [Other tests]
└── Unit/
    └── [Unit tests]
```

---

## 4. Requirements

### Functional Requirements

#### Backend Requirements

**REQ-001**: Tenant Creation and Management
- System MUST allow creation of new tenants with domain and company details
- System MUST initialize tenant context automatically after creation
- System MUST create admin user for each tenant
- System MUST send welcome credentials to tenant admin

**REQ-002**: Automatic Data Scoping
- All tenant-related models MUST use BelongsToTenant trait
- System MUST automatically add tenant_id to queries
- System MUST prevent cross-tenant data access
- System MUST support tenant context switching

**REQ-003**: User Management per Tenant
- Users MUST be scoped to their tenant
- System MUST support multiple users per tenant
- System MUST handle user authentication within tenant context
- System MUST support user roles and permissions per tenant

**REQ-004**: Department and Company Management
- Departments MUST be scoped to tenants
- Company profiles MUST be tenant-specific
- System MUST support hierarchical department structures
- System MUST validate department uniqueness within tenant

**REQ-005**: Permission System Integration
- System MUST integrate Spatie Laravel Permission
- Permissions MUST be tenant-scoped
- Roles MUST be assignable to users within tenant context
- System MUST support custom permissions per tenant

**REQ-006**: Route Separation
- Central routes MUST be wrapped in domain groups
- Tenant routes MUST use identification middleware
- System MUST prevent access to tenant routes from central domains
- System MUST support tenant-specific route parameters

#### Frontend Requirements

**REQ-007**: Tenant Context Awareness
- Frontend MUST display tenant-specific data
- System MUST handle tenant switching in UI
- Forms MUST submit within tenant context
- Navigation MUST respect tenant boundaries

**REQ-008**: User Interface Isolation
- UI components MUST display tenant-scoped data
- System MUST show tenant information in navigation
- Forms MUST validate tenant-specific constraints
- Error messages MUST be tenant-aware

### Non-Functional Requirements

**NFR-001**: Performance
- Tenant identification MUST complete in <100ms
- Database queries MUST use eager loading to prevent N+1
- Cache MUST be tenant-isolated
- Queue jobs MUST be tenant-aware

**NFR-002**: Security
- Cross-tenant data access MUST be prevented
- Tenant domains MUST be validated
- User sessions MUST be tenant-scoped
- API endpoints MUST require tenant context

**NFR-003**: Scalability
- System MUST support 1000+ tenants
- Database performance MUST degrade gracefully
- Cache isolation MUST scale horizontally
- Queue processing MUST handle tenant load

**NFR-004**: Maintainability
- Code MUST follow Laravel conventions
- Tenancy logic MUST be centralized
- Configuration MUST be environment-specific
- Documentation MUST be comprehensive

---

## 5. Constraints

**CON-001**: Technology Stack
- MUST use Laravel 12.x
- MUST use Tenancy for Laravel 3.x
- MUST use single-database tenancy
- MUST use domain-based identification
- MUST use MySQL 8.0+ or PostgreSQL 13+

**CON-002**: Laravel Best Practices
- MUST NOT use static methods for tenant operations
- MUST NOT bypass Eloquent ORM
- MUST NOT use raw SQL without parameterization
- MUST NOT store tenant data in session/localStorage

**CON-003**: Tenancy Constraints
- MUST NOT allow cross-tenant data access
- MUST NOT mix central and tenant contexts
- MUST NOT use tenant context in central operations
- MUST NOT disable tenant scoping without explicit reason

**CON-004**: Security Constraints
- MUST validate all tenant input
- MUST use HTTPS for tenant domains
- MUST implement proper authorization
- MUST log tenant-related security events

---

## 6. Guidelines

**GUI-001**: Tenant Creation
- Always use TenantCreationService for tenant creation
- Initialize tenancy context immediately after creation
- Create admin user with secure random password
- Send welcome email with credentials

**GUI-002**: Model Scoping
- Use BelongsToTenant trait on all tenant models
- Define tenant relationships explicitly
- Use tenant() helper for current tenant access
- Avoid direct tenant_id manipulation

**GUI-003**: Route Organization
- Wrap central routes in Route::domain() groups
- Use InitializeTenancyByDomain middleware on tenant routes
- Apply PreventAccessFromCentralDomains to tenant routes
- Use named routes for tenant-specific navigation

**GUI-004**: Permission Management
- Use Spatie Permission package for RBAC
- Scope permissions to tenants
- Define roles and permissions in seeders
- Check permissions using @can directives

**GUI-005**: Error Handling
- Handle tenant identification failures gracefully
- Log tenant-related errors with context
- Return user-friendly error messages
- Implement fallback for tenant unavailability

**GUI-006**: Testing
- Create separate test database for tenancy tests
- Use tenant-aware test helpers
- Test tenant isolation thoroughly
- Mock external services in tenant context

---

## 7. Database Schema

### Tables

#### tenants (Central)

```sql
CREATE TABLE tenants (
    id VARCHAR(255) PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    domain VARCHAR(255) UNIQUE NOT NULL,
    plan_id BIGINT UNSIGNED,
    subscription_status ENUM('trial', 'active', 'cancelled', 'expired') DEFAULT 'trial',
    trial_ends_at TIMESTAMP NULL,
    subscription_ends_at TIMESTAMP NULL,
    onboarding_completed BOOLEAN DEFAULT FALSE,
    is_demo BOOLEAN DEFAULT FALSE,
    data JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (plan_id) REFERENCES subscription_plans(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### domains (Central)

```sql
CREATE TABLE domains (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    domain VARCHAR(255) NOT NULL,
    tenant_id VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_domain (domain)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### users (Tenant-scoped)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    two_factor_secret TEXT NULL,
    two_factor_recovery_codes TEXT NULL,
    remember_token VARCHAR(100) NULL,
    current_team_id BIGINT UNSIGNED NULL,
    profile_photo_path VARCHAR(2048) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tenant_email (tenant_id, email),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### departments (Tenant-scoped)

```sql
CREATE TABLE departments (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    branch_name VARCHAR(255) NULL,
    description TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_tenant_name (tenant_id, name),
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    FOREIGN KEY (parent_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

#### company_profiles (Tenant-scoped)

```sql
CREATE TABLE company_profiles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    tenant_id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    address TEXT NULL,
    phone VARCHAR(255) NULL,
    email VARCHAR(255) NULL,
    website VARCHAR(255) NULL,
    logo_path VARCHAR(2048) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (tenant_id) REFERENCES tenants(id) ON DELETE CASCADE,
    UNIQUE KEY unique_tenant_profile (tenant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Migration Example

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->string('name');
            $table->string('branch_name')->nullable();
            $table->text('description')->nullable();
            $table->foreignId('parent_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->timestamps();
            
            $table->index(['tenant_id', 'name']);
            $table->foreign('tenant_id')->references('id')->on('tenants')->onDelete('cascade');
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
```

---

## 8. Models & Relationships

### Tenant Model

```php
<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains, HasFactory;
    
    protected $casts = [
        'trial_ends_at' => 'datetime',
        'subscription_ends_at' => 'datetime',
        'onboarding_completed' => 'boolean',
        'is_demo' => 'boolean',
    ];
    
    public function plan(): BelongsTo
    {
        return $this->belongsTo(SubscriptionPlan::class, 'plan_id');
    }
    
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
    
    public function departments(): HasMany
    {
        return $this->hasMany(Department::class);
    }
    
    public function companyProfile(): HasOne
    {
        return $this->hasOne(CompanyProfile::class);
    }
    
    public function isOnTrial(): bool
    {
        return $this->subscription_status === 'trial' 
            && $this->trial_ends_at 
            && $this->trial_ends_at->isFuture();
    }
    
    public function isActive(): bool
    {
        return $this->subscription_status === 'active' 
            || $this->isOnTrial();
    }
}
```

### User Model (Tenant-scoped)

```php
<?php

namespace App\Models;

use Stancl\Tenancy\Database\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, TwoFactorAuthenticatable, BelongsToTenant, HasRoles;
    
    protected $fillable = [
        'name',
        'email',
        'password',
        'tenant_id',
    ];
    
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];
    
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
    
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
```

---

## 9. API Contracts

### Controller Methods

#### Tenant Creation (Central)

```php
/**
 * Store a new tenant
 */
public function store(StoreTenantRequest $request): RedirectResponse
{
    try {
        $tenant = $this->tenantService->createTenant($request->validated());
        
        return redirect()
            ->route('tenant.show', $tenant)
            ->with('success', 'Tenant created successfully!');
    } catch (\Exception $e) {
        Log::error('Tenant creation failed', [
            'error' => $e->getMessage(),
            'data' => $request->validated()
        ]);
        
        return back()
            ->withInput()
            ->withErrors(['error' => 'Failed to create tenant.']);
    }
}
```

#### Department Management (Tenant)

```php
/**
 * Display departments for current tenant
 */
public function index(Request $request): Response
{
    $departments = Department::query()
        ->with('parent')
        ->when($request->search, fn($q) => $q->where('name', 'like', "%{$request->search}%"))
        ->paginate(15);
    
    return Inertia::render('Departments/Index', [
        'departments' => $departments,
        'filters' => $request->only(['search'])
    ]);
}
```

### Inertia Props Contract

```typescript
// Central - Tenant Creation
interface TenantCreationProps {
    plans: SubscriptionPlan[];
    domains: string[];
}

// Tenant - Dashboard
interface TenantDashboardProps {
    tenant: {
        id: string;
        company_name: string;
        domain: string;
        is_active: boolean;
        plan: SubscriptionPlan;
    };
    user: User;
    stats: {
        total_users: number;
        total_departments: number;
        active_users: number;
    };
}

// Tenant - Departments Index
interface DepartmentsIndexProps {
    departments: PaginatedData<Department>;
    filters: {
        search?: string;
    };
}
```

### Service Contract

```php
<?php

namespace App\Services;

use App\Models\Tenant;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface TenantServiceInterface
{
    public function createTenant(array $data): Tenant;
    public function getTenantStats(Tenant $tenant): array;
    public function updateTenantPlan(Tenant $tenant, int $planId): bool;
}

interface PermissionServiceInterface
{
    public function assignRoleToUser(User $user, string $role): void;
    public function createTenantRole(string $name, array $permissions = []): Role;
    public function getUserPermissions(User $user): Collection;
}
```

---

## 10. Validation Rules

### StoreTenantRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTenantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Central operation, no tenant context
    }
    
    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'domain' => [
                'required', 
                'string', 
                'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
                Rule::unique('domains', 'domain')
            ],
            'plan_id' => ['required', 'exists:subscription_plans,id'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => [
                'required', 
                'email', 
                'max:255',
                Rule::unique('users', 'email')
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'domain.regex' => 'Domain must be a valid domain name (e.g., company.com)',
            'domain.unique' => 'This domain is already taken',
            'admin_email.unique' => 'This email is already registered',
        ];
    }
}
```

### StoreDepartmentRequest

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }
    
    public function rules(): array
    {
        $tenantId = tenant('id');
        
        return [
            'name' => [
                'required', 
                'string', 
                'max:255',
                Rule::unique('departments')->where('tenant_id', $tenantId)
            ],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'parent_id' => [
                'nullable',
                'exists:departments,id',
                Rule::exists('departments')->where('tenant_id', $tenantId)
            ],
        ];
    }
}
```

---

## 11. React Component Examples

### Tenant Dashboard

```jsx
import { Head } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Dashboard({ tenant, user, stats }) {
    return (
        <AuthenticatedLayout>
            <Head title={`${tenant.company_name} - Dashboard`} />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h1 className="text-2xl font-bold mb-4">
                                Welcome to {tenant.company_name}
                            </h1>
                            
                            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div className="bg-blue-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-semibold">Total Users</h3>
                                    <p className="text-2xl">{stats.total_users}</p>
                                </div>
                                
                                <div className="bg-green-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-semibold">Departments</h3>
                                    <p className="text-2xl">{stats.total_departments}</p>
                                </div>
                                
                                <div className="bg-yellow-50 p-4 rounded-lg">
                                    <h3 className="text-lg font-semibold">Active Users</h3>
                                    <p className="text-2xl">{stats.active_users}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
```

### Departments Index

```jsx
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';

export default function Index({ departments, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    
    const handleSearch = (e) => {
        e.preventDefault();
        router.get(route('departments.index'), { search }, {
            preserveState: true,
            replace: true
        });
    };
    
    return (
        <AuthenticatedLayout>
            <Head title="Departments" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6">
                            <div className="flex justify-between items-center mb-6">
                                <h1 className="text-2xl font-bold">Departments</h1>
                                <Link href={route('departments.create')}>
                                    <PrimaryButton>Add Department</PrimaryButton>
                                </Link>
                            </div>
                            
                            <form onSubmit={handleSearch} className="mb-6">
                                <TextInput
                                    value={search}
                                    onChange={(e) => setSearch(e.target.value)}
                                    placeholder="Search departments..."
                                    className="w-full max-w-md"
                                />
                            </form>
                            
                            <div className="overflow-x-auto">
                                <table className="min-w-full divide-y divide-gray-200">
                                    <thead className="bg-gray-50">
                                        <tr>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Name
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Branch
                                            </th>
                                            <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody className="bg-white divide-y divide-gray-200">
                                        {departments.data.map((department) => (
                                            <tr key={department.id}>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {department.name}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    {department.branch_name || '-'}
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                    <Link 
                                                        href={route('departments.show', department.id)}
                                                        className="text-indigo-600 hover:text-indigo-900 mr-4"
                                                    >
                                                        View
                                                    </Link>
                                                    <Link 
                                                        href={route('departments.edit', department.id)}
                                                        className="text-indigo-600 hover:text-indigo-900"
                                                    >
                                                        Edit
                                                    </Link>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            
                            {/* Pagination component would go here */}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
```

---

## 12. Acceptance Criteria

### Feature Acceptance

**AC-001**: Tenant Creation
- Given a user submits valid tenant creation data, When the form is processed, Then a new tenant is created with domain, admin user, and company profile.

**AC-002**: Tenant Isolation
- Given a user is logged into tenant A, When they access departments, Then they only see departments from tenant A.

**AC-003**: Cross-Tenant Prevention
- Given a user from tenant A tries to access tenant B's data, When they make the request, Then they receive a 403 Forbidden or data not found.

**AC-004**: Permission System
- Given a user has 'manage departments' permission, When they access department management, Then they can create, edit, and delete departments.

**AC-005**: Domain Routing
- Given a user visits tenant.example.com, When the page loads, Then the application initializes the correct tenant context.

### Performance Acceptance

**AC-006**: Tenant Identification
- The tenant identification process MUST complete in under 100ms for 95% of requests.

**AC-007**: Database Queries
- Department listing MUST load in under 500ms with 100 departments.

**AC-008**: Cache Isolation
- Cache operations MUST be isolated per tenant without cross-contamination.

### Security Acceptance

**AC-009**: Data Isolation
- No tenant data MUST be accessible from other tenants under any circumstances.

**AC-010**: Authentication
- Users MUST only be able to authenticate within their tenant context.

**AC-011**: Authorization
- Permission checks MUST respect tenant boundaries.

---

## 13. Test Automation Strategy

### Test Levels

#### Unit Tests (Pest)
- Test Tenant model methods
- Test Service classes in isolation
- Test custom validation rules
- Test Permission service methods

```php
test('tenant can determine if on trial', function () {
    $tenant = Tenant::factory()->create([
        'subscription_status' => 'trial',
        'trial_ends_at' => now()->addDays(7)
    ]);
    
    expect($tenant->isOnTrial())->toBeTrue();
});
```

#### Feature Tests (Pest)
- Test tenant creation flow
- Test tenant isolation
- Test user authentication per tenant
- Test department CRUD operations
- Test permission enforcement

```php
test('tenant admin can create department', function () {
    $tenant = Tenant::factory()->create();
    $user = User::factory()->create(['tenant_id' => $tenant->id]);
    
    tenancy()->initialize($tenant);
    
    $this->actingAs($user)
        ->post(route('departments.store'), [
            'name' => 'Engineering',
            'description' => 'Software development team'
        ])
        ->assertRedirect();
    
    $this->assertDatabaseHas('departments', [
        'tenant_id' => $tenant->id,
        'name' => 'Engineering'
    ]);
});
```

#### Integration Tests
- Test complete tenant onboarding flow
- Test permission system integration
- Test queue job processing in tenant context
- Test cache isolation between tenants

#### Browser Tests (Pest + Laravel Dusk)
- Test tenant creation through UI
- Test cross-tenant isolation in browser
- Test permission-based UI elements
- Test tenant-specific navigation

### Test Coverage Requirements
- Services: 90% coverage minimum
- Controllers: 80% coverage minimum
- Models: 70% coverage minimum
- Overall: 80% coverage minimum

### Test Data Management
- Use model factories for all test data
- Create tenant-aware factories
- Use database transactions for test isolation
- Clean up test data between tests

---

## 14. Security Requirements

**SEC-001**: Data Isolation
- Implement strict tenant scoping on all queries
- Prevent SQL injection through proper parameterization
- Validate tenant ownership on all operations
- Log cross-tenant access attempts

**SEC-002**: Authentication
- Require tenant context for user authentication
- Prevent session leakage between tenants
- Implement secure password policies
- Support two-factor authentication per tenant

**SEC-003**: Authorization
- Use role-based access control per tenant
- Implement permission checks on all sensitive operations
- Support custom permission definitions
- Audit permission changes

**SEC-004**: Input Validation
- Validate all tenant-related input
- Sanitize domain names and company information
- Prevent XSS in tenant-specific content
- Validate file uploads per tenant

**SEC-005**: Session Security
- Isolate sessions per tenant domain
- Implement proper session timeout
- Prevent session fixation attacks
- Secure session cookie configuration

---

## 15. Dependencies & External Integrations

### Laravel Packages

**PKG-001**: Tenancy for Laravel
- Package: `stancl/tenancy`
- Version: `^3.0`
- Purpose: Multi-tenancy implementation
- Configuration: Single-database mode, domain identification

**PKG-002**: Spatie Laravel Permission
- Package: `spatie/laravel-permission`
- Version: `^6.0`
- Purpose: Role-based access control
- Integration: Tenant-scoped permissions

**PKG-003**: Laravel Fortify
- Package: `laravel/fortify`
- Version: `^1.0`
- Purpose: Authentication features
- Integration: Tenant-aware authentication

### Frontend Dependencies

**FE-001**: Inertia.js React Adapter
- Package: `@inertiajs/react`
- Version: `^2.0`
- Purpose: SPA-like experience with Laravel
- Integration: Tenant context passing

**FE-002**: React
- Version: `^19.0`
- Purpose: UI framework
- Integration: Component-based architecture

### Infrastructure Dependencies

**INF-001**: Database
- Type: MySQL 8.0+ or PostgreSQL 13+
- Purpose: Primary data store
- Configuration: UTF8MB4 charset, proper indexing

**INF-002**: Cache (Redis)
- Purpose: Tenant-isolated caching
- Configuration: Redis with tenant prefixes
- Integration: CacheTenancyBootstrapper

**INF-003**: Queue (Database/Redis)
- Purpose: Background job processing
- Configuration: Central connection for queue storage
- Integration: QueueTenancyBootstrapper

### External Services

**EXT-001**: Email Service
- Purpose: Welcome emails and notifications
- Integration: Laravel Mail with tenant context
- Fallback: Log emails if service unavailable

---

## 16. Edge Cases & Examples

### Edge Case 1: Tenant Domain Conflicts

**Scenario:** Two tenants try to register the same domain simultaneously

**Expected Behavior:** Database unique constraint prevents duplicate domains

**Implementation:**
```php
// In StoreTenantRequest
'domain' => [
    'required',
    'regex:/^[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
    Rule::unique('domains', 'domain')
],
```

### Edge Case 2: User Email Uniqueness Across Tenants

**Scenario:** Same email used in different tenants

**Expected Behavior:** Allow same email in different tenants (no global uniqueness)

**Implementation:**
```php
// Email uniqueness only within tenant context
Rule::unique('users', 'email')->where('tenant_id', tenant('id'))
```

### Edge Case 3: Tenant Context Loss

**Scenario:** Queue job loses tenant context

**Expected Behavior:** Job fails gracefully or re-queues with context

**Implementation:**
```php
// Ensure QueueTenancyBootstrapper is enabled
// Use tenant-aware job dispatching
dispatch(new ProcessTenantData($tenant))->onTenant($tenant);
```

### Edge Case 4: Cache Pollution

**Scenario:** Cache keys leak between tenants

**Expected Behavior:** Cache automatically prefixed per tenant

**Implementation:**
```php
// CacheTenancyBootstrapper handles automatic prefixing
Cache::put('user_count', $count); // Automatically scoped to tenant
```

---

## 17. Validation Criteria

### Code Review Checklist

- [ ] Tenant model uses HasDatabase and HasDomains traits
- [ ] All tenant models use BelongsToTenant trait
- [ ] Central routes wrapped in Route::domain() groups
- [ ] Tenant routes use identification middleware
- [ ] Services handle tenant operations correctly
- [ ] Permission system integrated with tenant scoping
- [ ] Queue configuration uses central connection
- [ ] Cache bootstrappers enabled
- [ ] Tests cover tenant isolation
- [ ] Validation rules respect tenant boundaries
- [ ] Error handling includes tenant context
- [ ] Documentation updated for tenant features

### Performance Validation

- [ ] Tenant identification < 100ms
- [ ] Database queries use tenant scoping
- [ ] Cache operations are isolated
- [ ] Queue jobs maintain tenant context
- [ ] Memory usage stays within limits

### Security Validation

- [ ] No cross-tenant data access possible
- [ ] Authentication requires tenant context
- [ ] Authorization checks tenant ownership
- [ ] Input validation prevents injection
- [ ] Sessions isolated per tenant

---

## 18. Rationale & Context

### Why Single-Database Tenancy?
- Simpler infrastructure management
- Easier backup and recovery
- Lower operational complexity
- Suitable for HRMS scale requirements
- Automatic scoping prevents manual errors

### Why Domain-Based Identification?
- Professional appearance for enterprise clients
- Clear tenant boundaries
- Easy DNS management
- Standard SaaS pattern

### Why Spatie Permission Integration?
- Flexible role-based access control
- Tenant-scoped permissions
- Well-maintained Laravel package
- Comprehensive permission management

### Why Automatic Scoping?
- Prevents accidental cross-tenant access
- Reduces developer cognitive load
- Ensures data consistency
- Automatic enforcement of isolation

---

## 19. Related Specifications

- [Laravel 12 Framework Documentation](https://laravel.com/docs/12.x)
- [Tenancy for Laravel Documentation](https://tenancyforlaravel.com/docs/v3)
- [Spatie Laravel Permission Documentation](https://spatie.be/docs/laravel-permission/v6)
- [Inertia.js Documentation](https://inertiajs.com)
- [React Documentation](https://react.dev)

---

## 20. Appendix

### Useful Commands

```bash
# Install tenancy package
composer require stancl/tenancy

# Run tenancy installation
php artisan tenancy:install

# Create tenant
php artisan tinker
$tenant = \App\Models\Tenant::create(['id' => 'acme']);
$tenant->domains()->create(['domain' => 'acme.localhost']);

# Run tenant migrations (if using multi-db)
php artisan tenants:migrate

# Create permission seeder
php artisan make:seeder TenantPermissionsSeeder

# Run tests with tenant context
php artisan test --filter=TenantTest

# Clear tenant cache
php artisan cache:clear
```

### Common Patterns

#### Pattern: Tenant Context Execution
```php
$tenant->run(function () {
    // Code executes in tenant context
    $users = User::all(); // Only tenant users
    $departments = Department::all(); // Only tenant departments
});
```

#### Pattern: Tenant-Aware Service
```php
class DepartmentService
{
    public function createDepartment(array $data): Department
    {
        return DB::transaction(function () use ($data) {
            return Department::create([
                'tenant_id' => tenant('id'), // Explicit but automatic
                'name' => $data['name'],
                'description' => $data['description'],
            ]);
        });
    }
}
```

#### Pattern: Permission Check in Controller
```php
public function store(StoreDepartmentRequest $request)
{
    $this->authorize('create', Department::class);
    
    $department = $this->departmentService->createDepartment($request->validated());
    
    return redirect()->route('departments.show', $department);
}
```

### Troubleshooting Guide

#### Issue: Tenant Not Identified
```
Check:
1. Domain exists in domains table
2. Domain matches exactly (case-sensitive)
3. Central domains configured correctly
4. Middleware applied to routes
5. DNS points to correct server
```

#### Issue: Cross-Tenant Data Access
```
Check:
1. BelongsToTenant trait on models
2. No raw queries bypassing Eloquent
3. Proper authorization checks
4. Cache isolation enabled
```

#### Issue: Permission Not Working
```
Check:
1. HasRoles trait on User model
2. Permission seeder run for tenant
3. Middleware applied correctly
4. Gate definitions tenant-aware
```

#### Issue: Queue Jobs Lose Context
```
Check:
1. QueueTenancyBootstrapper enabled
2. Jobs dispatched from tenant context
3. Queue connection configured as central
4. Job implements ShouldQueue interface
```

---

## Implementation Status

### ✅ Completed
- [x] Tenancy package installed and configured
- [x] Tenant model with HasDatabase and HasDomains
- [x] Basic tenant creation service
- [x] User, Department, CompanyProfile models with BelongsToTenant
- [x] Tenant routes with identification middleware
- [x] Central routes (needs domain wrapping)
- [x] Basic onboarding flow

### 🔄 In Progress
- [ ] Spatie Laravel Permission integration
- [ ] Permission seeding for tenants
- [ ] Queue configuration optimization
- [ ] Cache isolation verification
- [ ] Comprehensive testing suite

### ❌ Pending
- [ ] Central route domain wrapping (Laravel 12)
- [ ] Tenant-aware console commands
- [ ] Advanced permission management UI
- [ ] Multi-tenant file storage
- [ ] Tenant analytics and reporting
- [ ] Automated tenant cleanup
- [ ] Backup and restore procedures
- [ ] Performance monitoring
- [ ] Security audit and penetration testing

### Next Steps
1. Complete Spatie Permission integration
2. Implement comprehensive testing
3. Add tenant management features
4. Optimize performance and monitoring
5. Security hardening and audit</content>
<parameter name="filePath">/Users/app/Desktop/Laravel/HR-Management-System/spec/spec-architecture-multi-tenancy-implementation.md


❌ Still Pending (Advanced Features) dated 23rd Jan 2026
The following are optional advanced features beyond core multi-tenancy:

 Tenant-aware console commands
 Advanced permission management UI
 Multi-tenant file storage
 Tenant analytics and reporting
 Automated tenant cleanup
 Backup and restore procedures
 Performance monitoring
 Security audit and penetration testing
