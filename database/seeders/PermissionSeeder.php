<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'view-home',
            'view-home-names',
            'view-users',
            'view-brockers',
            'view-uptowns',
            'view-uptown-types',
            'view-unit-sub-types',
            'view-developers',
            'view-deals',
            'view-sell-requests',
            'view-apartment-installments',
            'view-requests',
            'view-ads',
            'view-contracts',
            'view-contract-agreements',
            'view-bot-messages',
            'view-admins',
            'view-roles',
            'view-policies',
            'view-push-notifications',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdminRole = Role::firstOrCreate([
            'name' => 'super-admin',
            'guard_name' => 'web',
        ]);

        $superAdminRole->syncPermissions(Permission::where('guard_name', 'web')->get());
    }
}
