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
        Schema::table('broker_leads', function (Blueprint $table) {
            if (!Schema::hasColumn('broker_leads', 'brocker_id')) {
                $table->foreignId('brocker_id')->after('lead_id')->constrained('brockers')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('broker_leads', function (Blueprint $table) {
            if (Schema::hasColumn('broker_leads', 'brocker_id')) {
                $table->dropForeign(['brocker_id']);
                $table->dropColumn('brocker_id');
            }
        });
    }
};
