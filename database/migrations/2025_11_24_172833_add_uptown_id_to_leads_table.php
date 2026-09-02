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
        Schema::table('leads', function (Blueprint $table) {
            if (!Schema::hasColumn('leads', 'uptown_id')) {
                $table->foreignId('uptown_id')->nullable()->constrained('uptowns')->onDelete('cascade');
            } else {
                $table->unsignedBigInteger('uptown_id')->nullable()->change();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            if (Schema::hasColumn('leads', 'uptown_id')) {
                // We do not drop the column to avoid data loss in case it existed before.
                // If this migration added it, ideally we would drop it, but we can't distinguish here easily.
            }
        });
    }
};
