<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sell_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sell_requests', 'uptown_type_id')) {
                $table->foreignId('uptown_type_id')->nullable()->after('compound_id')->constrained('uptown_types')->onDelete('set null');
            }
            if (!Schema::hasColumn('sell_requests', 'unit_sub_type_id')) {
                $table->foreignId('unit_sub_type_id')->nullable()->after('uptown_type_id')->constrained('unit_sub_types')->onDelete('set null');
            }
            if (Schema::hasColumn('sell_requests', 'type')) {
                $table->dropColumn('type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sell_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('sell_requests', 'type')) {
                $table->string('type')->nullable()->after('installments_price_per_year');
            }
            $table->dropConstrainedForeignId('unit_sub_type_id');
            $table->dropConstrainedForeignId('uptown_type_id');
        });
    }
};
