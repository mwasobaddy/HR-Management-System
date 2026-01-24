<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionService
{
    /**
     * Create default roles and permissions for a tenant
     */
    public function createTenantRolesAndPermissions(): void
    {
        // Define permissions
        $permissions = [
            // User management
            'view users',
            'create users',
            'edit users',
            'delete users',

            // Department management
            'view departments',
            'create departments',
            'edit departments',
            'delete departments',

            // Company profile
            'view company profile',
            'edit company profile',

            // Dashboard
            'view dashboard',
            'view reports',

            // Settings
            'manage settings',
        ];

        // Create permissions if they don't exist
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Define roles and their permissions
        $roles = [
            'super-admin' => $permissions, // All permissions
            'tech-admin' => $permissions, // All permissions (for system owner)
            'admin' => [
                'view users', 'create users', 'edit users',
                'view departments', 'create departments', 'edit departments', 'delete departments',
                'view company profile', 'edit company profile',
                'view dashboard', 'view reports',
                'manage settings',
            ],
            'manager' => [
                'view users', 'create users', 'edit users',
                'view departments', 'create departments', 'edit departments',
                'view company profile',
                'view dashboard', 'view reports',
            ],
            'employee' => [
                'view dashboard',
            ],
        ];

        // Create roles and assign permissions
        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($rolePermissions);
        }
    }

    /**
     * Assign a role to a user
     */
    public function assignRoleToUser(User $user, string $role): void
    {
        $user->assignRole($role);
    }

    /**
     * Remove a role from a user
     */
    public function removeRoleFromUser(User $user, string $role): void
    {
        $user->removeRole($role);
    }

    /**
     * Get all permissions for a user
     */
    public function getUserPermissions(User $user): Collection
    {
        return $user->getAllPermissions();
    }

    /**
     * Get all roles for a user
     */
    public function getUserRoles(User $user): Collection
    {
        return $user->roles;
    }

    /**
     * Check if user has permission
     */
    public function userHasPermission(User $user, string $permission): bool
    {
        return $user->hasPermissionTo($permission);
    }

    /**
     * Check if user has role
     */
    public function userHasRole(User $user, string $role): bool
    {
        return $user->hasRole($role);
    }

    /**
     * Create a custom role for a tenant
     */
    public function createTenantRole(string $name, array $permissions = []): Role
    {
        $role = Role::firstOrCreate([
            'name' => $name,
            'guard_name' => 'web',
        ]);

        if (! empty($permissions)) {
            $role->syncPermissions($permissions);
        }

        return $role;
    }

    /**
     * Get all available permissions
     */
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    /**
     * Get all available roles
     */
    public function getAllRoles(): Collection
    {
        return Role::all();
    }
}
