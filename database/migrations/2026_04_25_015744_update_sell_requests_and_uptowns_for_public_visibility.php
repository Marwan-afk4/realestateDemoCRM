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
            $table->enum('visibility', ['public', 'private'])->default('private')->after('status');
        });

        Schema::table('uptowns', function (Blueprint $table) {
            $table->string('code')->unique()->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sell_requests', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });

        Schema::table('uptowns', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
