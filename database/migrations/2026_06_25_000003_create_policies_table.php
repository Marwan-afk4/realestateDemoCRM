<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('policies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->timestamps();
        });

        // Seed permission for managing policies
        try {
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

            $permission = Permission::firstOrCreate([
                'name' => 'view-policies',
                'guard_name' => 'web'
            ]);

            // Sync permission with super-admin role
            $superAdmin = Role::where('name', 'super-admin')->first();
            if ($superAdmin) {
                $superAdmin->givePermissionTo($permission);
            }
        } catch (\Exception $e) {
            // Silently catch exceptions if Spatie tables aren't fully setup in other contexts
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('policies');

        // Delete permission
        try {
            Permission::where('name', 'view-policies')->delete();
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        } catch (\Exception $e) {
            // Silently catch exceptions
        }
    }
};
