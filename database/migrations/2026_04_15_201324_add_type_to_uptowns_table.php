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
            if (!Schema::hasColumn('uptowns', 'type')) {
                $table->enum('type', ['rent', 'buy'])->default('buy')->after('sale_type');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('uptowns', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
