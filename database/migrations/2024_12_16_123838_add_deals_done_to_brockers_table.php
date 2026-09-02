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
        Schema::table('brockers', callback: function (Blueprint $table) {
            $table->integer('deals_done')->nullable()->after('number_of_deals');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('brockers', function (Blueprint $table) {
            //
        });
    }
};
