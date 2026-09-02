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
            $table->enum('cash', [1,0])->after('status');
            $table->enum('installment', [1,0])->after('cash');
            $table->integer('installment_years')->after('installment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uptowns', function (Blueprint $table) {
            //
        });
    }
};
