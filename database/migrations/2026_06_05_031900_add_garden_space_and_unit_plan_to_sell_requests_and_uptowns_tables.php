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
            $table->decimal('garden_space', 8, 2)->nullable()->after('garden_area');
            $table->string('unit_plan')->nullable()->after('detailed_pdf');
        });

        Schema::table('uptowns', function (Blueprint $table) {
            $table->decimal('garden_space', 8, 2)->nullable()->after('space');
            $table->string('unit_plan')->nullable()->after('floor_plan_image');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sell_requests', function (Blueprint $table) {
            $table->dropColumn(['garden_space', 'unit_plan']);
        });

        Schema::table('uptowns', function (Blueprint $table) {
            $table->dropColumn(['garden_space', 'unit_plan']);
        });
    }
};
