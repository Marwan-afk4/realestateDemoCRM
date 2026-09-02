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
        Schema::table('transaction_deals', function (Blueprint $table) {
            $table->foreignId('compound_id')->nullable()->after('uptown_id')->constrained('compounds')->onDelete('cascade');    
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_deals', function (Blueprint $table) {
            //
        });
    }
};
