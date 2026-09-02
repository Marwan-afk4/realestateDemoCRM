<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (! Schema::hasColumn('sell_requests', 'delivery_date')) {
            Schema::table('sell_requests', function (Blueprint $table) {
                $table->string('delivery_date', 7)->nullable()->after('execution_date');
            });

            return;
        }

        // Normalize existing full dates to YYYY-MM, then change column type
        $rows = DB::table('sell_requests')->whereNotNull('delivery_date')->get(['id', 'delivery_date']);
        foreach ($rows as $row) {
            $value = (string) $row->delivery_date;
            $normalized = strlen($value) >= 7 ? substr($value, 0, 7) : null;
            DB::table('sell_requests')->where('id', $row->id)->update([
                'delivery_date' => $normalized,
            ]);
        }

        DB::statement('ALTER TABLE sell_requests MODIFY delivery_date VARCHAR(7) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasColumn('sell_requests', 'delivery_date')) {
            return;
        }

        $rows = DB::table('sell_requests')->whereNotNull('delivery_date')->get(['id', 'delivery_date']);
        foreach ($rows as $row) {
            $value = (string) $row->delivery_date;
            if (preg_match('/^\d{4}-\d{2}$/', $value)) {
                DB::table('sell_requests')->where('id', $row->id)->update([
                    'delivery_date' => $value.'-01',
                ]);
            }
        }

        DB::statement('ALTER TABLE sell_requests MODIFY delivery_date DATE NULL');
    }
};
