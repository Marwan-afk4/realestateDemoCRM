<?php

namespace Database\Seeders;

use App\Enums\ActivationStatus;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Create all admin permissions, the super-admin role, and a panel admin user
     * with every permission (Spatie role: super-admin, users.role: admin for login).
     */
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

        // users.role must be "admin" for web login / role:admin middleware.
        // Spatie role "super-admin" grants all permissions.
        $user = User::updateOrCreate(
            ['email' => 'admin@delar.test'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'phone' => '01000000000',
                'password' => 'Admin@123456',
                'role' => 'admin',
                'provider' => 'local',
                'provider_id' => 'local',
                'status' => ActivationStatus::Active,
            ]
        );

        $user->syncRoles([$superAdminRole]);

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->command?->info('Super admin ready.');
        $this->command?->info('Phone: 01000000000');
        $this->command?->info('Password: Admin@123456');
        $this->command?->info('Email: admin@delar.test');
        $this->command?->info('Permissions: '.$user->getAllPermissions()->count());
    }
}
