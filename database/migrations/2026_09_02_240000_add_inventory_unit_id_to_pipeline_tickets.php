<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pipeline_tickets', function (Blueprint $table) {
            if (! Schema::hasColumn('pipeline_tickets', 'inventory_unit_id')) {
                $table->foreignId('inventory_unit_id')->nullable()->after('brocker_id')->constrained('inventory_units')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('pipeline_tickets', function (Blueprint $table) {
            if (Schema::hasColumn('pipeline_tickets', 'inventory_unit_id')) {
                $table->dropConstrainedForeignId('inventory_unit_id');
            }
        });
    }
};
