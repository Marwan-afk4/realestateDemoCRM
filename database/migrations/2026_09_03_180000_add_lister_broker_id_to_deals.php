<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (! Schema::hasColumn('deals', 'lister_broker_id')) {
                $table->foreignId('lister_broker_id')
                    ->nullable()
                    ->after('brocker_id')
                    ->constrained('brockers')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('deals', function (Blueprint $table) {
            if (Schema::hasColumn('deals', 'lister_broker_id')) {
                $table->dropConstrainedForeignId('lister_broker_id');
            }
        });
    }
};
