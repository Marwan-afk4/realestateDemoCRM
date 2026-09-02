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
            $table->foreignId('sales_developer_id')->nullable()->after('developer_id')->constrained('sales_developers')->onDelete('cascade');
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
