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
        Schema::table('buy_appartment_installments', function (Blueprint $table) {
            $table->unsignedInteger('monthly_installment')->nullable()->after('monthly_income');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('buy_appartment_installments', function (Blueprint $table) {
            $table->dropColumn('monthly_installment');
        });
    }
};
