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
            $table->longText('master_plan_image')->nullable()->after('latitude');
            $table->longText('floor_plan_image')->nullable()->after('master_plan_image');
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
