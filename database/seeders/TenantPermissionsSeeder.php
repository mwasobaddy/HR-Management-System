<?php

namespace Database\Seeders;

use App\Services\PermissionService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TenantPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissionService = new PermissionService();
        $permissionService->createTenantRolesAndPermissions();
    }
}
