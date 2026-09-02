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
        Schema::table('uptowns', function (Blueprint $table) {
            if (Schema::hasColumn('uptowns', 'sale_type')) {
                $table->dropColumn('sale_type');
            }
            if (!Schema::hasColumn('uptowns', 'installment_plan')) {
                $table->enum('installment_plan', ['monthly', 'yearly'])->nullable()->after('installment_years');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uptowns', function (Blueprint $table) {
            $table->string('sale_type')->nullable();
            $table->dropColumn('installment_plan');
        });
    }
};
