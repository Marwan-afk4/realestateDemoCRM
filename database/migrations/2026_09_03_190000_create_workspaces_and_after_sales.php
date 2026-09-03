<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'marketing_agency_id')) {
                $table->foreignId('marketing_agency_id')->nullable()->after('plan_id')->constrained('marketing_agencies')->nullOnDelete();
            }
            if (! Schema::hasColumn('users', 'developer_id')) {
                $table->foreignId('developer_id')->nullable()->after('marketing_agency_id')->constrained('developers')->nullOnDelete();
            }
        });

        if (! Schema::hasTable('developer_brocker')) {
            Schema::create('developer_brocker', function (Blueprint $table) {
                $table->id();
                $table->foreignId('developer_id')->constrained('developers')->cascadeOnDelete();
                $table->foreignId('brocker_id')->constrained('brockers')->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['developer_id', 'brocker_id']);
            });
        }

        if (! Schema::hasTable('after_sales_tickets')) {
            Schema::create('after_sales_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
                $table->foreignId('inventory_unit_id')->nullable()->constrained('inventory_units')->nullOnDelete();
                $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
                $table->foreignId('developer_id')->nullable()->constrained('developers')->nullOnDelete();
                $table->foreignId('compound_id')->nullable()->constrained('compounds')->nullOnDelete();
                $table->string('type');
                $table->string('status')->default('open');
                $table->string('priority')->default('normal');
                $table->string('title');
                $table->text('description')->nullable();
                $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('resolved_at')->nullable();
                $table->timestamp('closed_at')->nullable();
                $table->timestamps();
            });
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach ([
            'view-marketing-agencies',
            'view-agency-workspace',
            'view-developer-portal',
            'view-after-sales',
            'view-unit-matching',
            'manage-developer-brokers',
        ] as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('after_sales_tickets');
        Schema::dropIfExists('developer_brocker');

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'developer_id')) {
                $table->dropConstrainedForeignId('developer_id');
            }
            if (Schema::hasColumn('users', 'marketing_agency_id')) {
                $table->dropConstrainedForeignId('marketing_agency_id');
            }
        });
    }
};
