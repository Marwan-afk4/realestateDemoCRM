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
        if (Schema::hasColumn('marketing_agencies', 'total_deals') && ! Schema::hasColumn('marketing_agencies', 'total_leads')) {
            Schema::table('marketing_agencies', function (Blueprint $table) {
                $table->renameColumn('total_deals', 'total_leads');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('marketing_agencies', 'total_leads') && ! Schema::hasColumn('marketing_agencies', 'total_deals')) {
            Schema::table('marketing_agencies', function (Blueprint $table) {
                $table->renameColumn('total_leads', 'total_deals');
            });
        }
    }
};
