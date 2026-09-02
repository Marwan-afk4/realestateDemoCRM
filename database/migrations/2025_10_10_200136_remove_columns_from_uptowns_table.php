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
            $table->dropColumn('image');
            $table->dropColumn('favourite');
            $table->dropColumn('apparment');
            $table->foreignId('uptown_type_id')->after('id')->nullable()->constrained('uptown_types')->SetNullOnDelete();
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
