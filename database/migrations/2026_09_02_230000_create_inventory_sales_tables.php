<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventory_units') && ! Schema::hasTable('unit_holds')) {
            Schema::drop('inventory_units');
        }

        Schema::create('inventory_units', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uptown_id')->nullable()->constrained('uptowns')->nullOnDelete();
            $table->foreignId('developer_id')->nullable()->constrained('developers')->nullOnDelete();
            $table->foreignId('compound_id')->nullable()->constrained('compounds')->nullOnDelete();
            $table->string('phase', 40)->default('');
            $table->string('building', 40)->default('');
            $table->string('floor', 20)->default('');
            $table->string('unit_number', 40);
            $table->string('code')->unique();
            $table->string('status')->default('available')->index();
            $table->decimal('list_price', 14, 2)->nullable();
            $table->decimal('current_price', 14, 2)->nullable();
            $table->timestamp('reserved_until')->nullable()->index();
            $table->foreignId('active_deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->unsignedBigInteger('active_hold_id')->nullable();
            $table->timestamps();

            $table->unique(['compound_id', 'phase', 'building', 'floor', 'unit_number'], 'inventory_units_address_unique');
            $table->index(['compound_id', 'status']);
        });

        Schema::create('unit_holds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_unit_id')->constrained('inventory_units')->cascadeOnDelete();
            $table->foreignId('deal_id')->nullable()->constrained('deals')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->string('type')->default('reservation');
            $table->timestamp('expires_at')->index();
            $table->timestamp('released_at')->nullable();
            $table->string('release_reason')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::table('inventory_units', function (Blueprint $table) {
            $table->foreign('active_hold_id')->references('id')->on('unit_holds')->nullOnDelete();
        });

        Schema::create('sale_offers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignId('inventory_unit_id')->nullable()->constrained('inventory_units')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('brocker_id')->nullable()->constrained('brockers')->nullOnDelete();
            $table->decimal('list_price', 14, 2)->default(0);
            $table->decimal('discount', 14, 2)->default(0);
            $table->decimal('net_price', 14, 2)->default(0);
            $table->string('payment_method')->default('cash');
            $table->unsignedTinyInteger('down_payment_percent')->nullable();
            $table->unsignedSmallInteger('installment_count')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->string('status')->default('draft')->index();
            $table->timestamp('accepted_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('sale_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignId('sale_offer_id')->nullable()->constrained('sale_offers')->nullOnDelete();
            $table->foreignId('inventory_unit_id')->nullable()->constrained('inventory_units')->nullOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('brocker_id')->nullable()->constrained('brockers')->nullOnDelete();
            $table->string('type');
            $table->string('title');
            $table->longText('body')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('signed_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('buyer_payment_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('deal_id')->constrained('deals')->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained('contacts')->nullOnDelete();
            $table->foreignId('inventory_unit_id')->nullable()->constrained('inventory_units')->nullOnDelete();
            $table->foreignId('sale_offer_id')->nullable()->constrained('sale_offers')->nullOnDelete();
            $table->decimal('total_price', 14, 2);
            $table->decimal('down_payment', 14, 2)->default(0);
            $table->string('currency', 8)->default('EGP');
            $table->date('start_date')->nullable();
            $table->string('status')->default('open')->index();
            $table->timestamps();
        });

        Schema::create('buyer_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_payment_plan_id')->constrained('buyer_payment_plans')->cascadeOnDelete();
            $table->unsignedSmallInteger('sequence')->default(1);
            $table->string('label')->nullable();
            $table->date('due_date')->index();
            $table->decimal('amount', 14, 2);
            $table->decimal('paid_amount', 14, 2)->default(0);
            $table->timestamp('paid_at')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamps();
        });

        Schema::create('buyer_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_installment_id')->constrained('buyer_installments')->cascadeOnDelete();
            $table->decimal('amount', 14, 2);
            $table->timestamp('received_at');
            $table->string('reference')->nullable();
            $table->string('path')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('commission_splits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('commission_id')->constrained('commissions')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('role');
            $table->decimal('percentage', 8, 2)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->timestamps();
        });

        Schema::table('deals', function (Blueprint $table) {
            if (! Schema::hasColumn('deals', 'inventory_unit_id')) {
                $table->foreignId('inventory_unit_id')->nullable()->after('uptown_id')->constrained('inventory_units')->nullOnDelete();
            }
            if (! Schema::hasColumn('deals', 'sale_offer_id')) {
                $table->foreignId('sale_offer_id')->nullable()->after('inventory_unit_id')->constrained('sale_offers')->nullOnDelete();
            }
        });

        Schema::table('commissions', function (Blueprint $table) {
            if (! Schema::hasColumn('commissions', 'inventory_unit_id')) {
                $table->foreignId('inventory_unit_id')->nullable()->after('uptown_id')->constrained('inventory_units')->nullOnDelete();
            }
            if (! Schema::hasColumn('commissions', 'closed_unit_price')) {
                $table->decimal('closed_unit_price', 14, 2)->nullable()->after('amount');
            }
            if (! Schema::hasColumn('commissions', 'payout_status')) {
                $table->string('payout_status')->default('accrued')->after('closed_unit_price');
            }
            if (! Schema::hasColumn('commissions', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payout_status');
            }
        });

        $this->backfillInventory();
        $this->seedPermissions();
    }

    public function down(): void
    {
        Schema::table('commissions', function (Blueprint $table) {
            if (Schema::hasColumn('commissions', 'inventory_unit_id')) {
                $table->dropConstrainedForeignId('inventory_unit_id');
            }
            foreach (['closed_unit_price', 'payout_status', 'paid_at'] as $column) {
                if (Schema::hasColumn('commissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'sale_offer_id')) {
                $table->dropConstrainedForeignId('sale_offer_id');
            }
            if (Schema::hasColumn('deals', 'inventory_unit_id')) {
                $table->dropConstrainedForeignId('inventory_unit_id');
            }
        });

        Schema::dropIfExists('commission_splits');
        Schema::dropIfExists('buyer_receipts');
        Schema::dropIfExists('buyer_installments');
        Schema::dropIfExists('buyer_payment_plans');
        Schema::dropIfExists('sale_documents');
        Schema::dropIfExists('sale_offers');

        Schema::table('inventory_units', function (Blueprint $table) {
            $table->dropForeign(['active_hold_id']);
        });
        Schema::dropIfExists('unit_holds');
        Schema::dropIfExists('inventory_units');
    }

    private function backfillInventory(): void
    {
        if (! Schema::hasTable('uptowns')) {
            return;
        }

        $uptowns = DB::table('uptowns')->select('id', 'developer_id', 'compound_id', 'code', 'strat_price', 'status', 'reserved_deal_id', 'name_en')->get();
        $now = now();

        foreach ($uptowns as $uptown) {
            $exists = DB::table('inventory_units')->where('uptown_id', $uptown->id)->exists();
            if ($exists) {
                continue;
            }

            $status = match ($uptown->status) {
                'reserved' => 'reserved',
                'sold' => 'sold',
                default => 'available',
            };

            $unitNumber = $uptown->code ?: ('U-'.$uptown->id);
            $code = 'UT-'.$uptown->id.'-'.$unitNumber;

            $inventoryId = DB::table('inventory_units')->insertGetId([
                'uptown_id' => $uptown->id,
                'developer_id' => $uptown->developer_id,
                'compound_id' => $uptown->compound_id,
                'phase' => 'listing',
                'building' => (string) $uptown->id,
                'floor' => '',
                'unit_number' => $unitNumber,
                'code' => $code,
                'status' => $status,
                'list_price' => $uptown->strat_price,
                'current_price' => $uptown->strat_price,
                'reserved_until' => null,
                'active_deal_id' => $uptown->reserved_deal_id,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            if ($uptown->reserved_deal_id && Schema::hasColumn('deals', 'inventory_unit_id')) {
                DB::table('deals')->where('id', $uptown->reserved_deal_id)->update(['inventory_unit_id' => $inventoryId]);
            }

            DB::table('deals')->where('uptown_id', $uptown->id)->whereNull('inventory_unit_id')->update(['inventory_unit_id' => $inventoryId]);
        }
    }

    private function seedPermissions(): void
    {
        if (! Schema::hasTable('permissions')) {
            return;
        }

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [];
        foreach (['view-inventory', 'view-collections'] as $name) {
            $permissions[] = Permission::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        Role::where('name', 'super-admin')->where('guard_name', 'web')->first()
            ?->givePermissionTo($permissions);
    }
};
