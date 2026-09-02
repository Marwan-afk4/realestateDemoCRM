<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('body');
            $table->string('audience')->default('all');
            $table->json('user_ids')->nullable();
            $table->unsignedInteger('sent_count')->default(0);
            $table->unsignedInteger('failed_count')->default(0);
            $table->foreignId('sent_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        if (Schema::hasTable('permissions')) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $permission = Permission::firstOrCreate([
                'name' => 'view-push-notifications',
                'guard_name' => 'web',
            ]);

            Role::where('name', 'super-admin')->where('guard_name', 'web')
                ->first()
                ?->givePermissionTo($permission);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('push_notifications');
    }
};
