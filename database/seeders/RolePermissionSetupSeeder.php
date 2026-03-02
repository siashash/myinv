<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class RolePermissionSetupSeeder extends Seeder
{
    /**
     * Seed role/permission modules and grant full access to admin role.
     */
    public function run(): void
    {
        $modules = [
            'category',
            'sub-category',
            'units',
            'product',
            'supplier',
            'customer',
            'ac-head',
            'purchase',
            'sales',
            'purchase-payment',
            'sales-receipt',
            'purchase-return',
            'sales-return',
            'stock-report',
            'sales-report',
            'account-book',
            'cash-book',
            'bank-book',
            'sundry-creditors',
            'users',
            'roles',
            'permissions',
            'role-permissions',
        ];

        foreach ($modules as $moduleName) {
            Permission::firstOrCreate(['module_name' => $moduleName]);
        }

        $adminRole = Role::whereRaw('LOWER(role_name) = ?', ['admin'])->first();
        if (! $adminRole) {
            return;
        }

        $permissionIds = Permission::pluck('id');
        foreach ($permissionIds as $permissionId) {
            RolePermission::updateOrCreate(
                [
                    'role_id' => $adminRole->id,
                    'permission_id' => $permissionId,
                ],
                [
                    'can_view' => true,
                    'can_add' => true,
                    'can_edit' => true,
                    'can_delete' => true,
                ]
            );
        }
    }
}
