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
            'view-contacts',
            'view-pipeline',
            'view-all-pipeline',
            'view-team-pipeline',
            'view-crm-tasks',
            'view-crm-reports',
            'view-message-templates',
            'view-leads',
            'view-inventory',
            'view-collections',
            'view-marketing-agencies',
            'view-agency-workspace',
            'view-developer-portal',
            'view-after-sales',
            'view-unit-matching',
            'manage-developer-brokers',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $all = Permission::where('guard_name', 'web')->get();

        Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web'])
            ->syncPermissions($all);

        Role::firstOrCreate(['name' => 'broker', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-contacts', 'view-pipeline', 'view-team-pipeline', 'view-crm-tasks',
                'view-crm-reports', 'view-deals', 'view-inventory', 'view-collections',
                'view-leads', 'view-unit-matching',
            ]);

        Role::firstOrCreate(['name' => 'sales-manager', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-contacts', 'view-pipeline', 'view-all-pipeline', 'view-team-pipeline',
                'view-crm-tasks', 'view-crm-reports', 'view-deals', 'view-inventory',
                'view-collections', 'view-leads', 'view-brockers', 'view-unit-matching',
            ]);

        Role::firstOrCreate(['name' => 'accountant', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-collections', 'view-deals', 'view-crm-reports',
            ]);

        Role::firstOrCreate(['name' => 'after-sales', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-after-sales', 'view-deals', 'view-contacts', 'view-inventory',
            ]);

        Role::firstOrCreate(['name' => 'agency-manager', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-agency-workspace', 'view-marketing-agencies', 'view-leads',
                'view-pipeline', 'view-contacts', 'view-unit-matching',
            ]);

        Role::firstOrCreate(['name' => 'developer-admin', 'guard_name' => 'web'])
            ->syncPermissions([
                'view-developer-portal', 'view-inventory', 'view-deals', 'manage-developer-brokers',
            ]);
    }
}
