<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $executionDateValues = [
        'immediately',
        '6_months',
        '1_year',
        '2_years',
        '3_years',
        '4_years_or_more',
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1) Add delivery_date for admin to set on accept
        Schema::table('sell_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('sell_requests', 'delivery_date')) {
                $table->date('delivery_date')->nullable()->after('execution_date');
            }
        });

        // 2) Convert execution_date from date -> enum (via string), mapping old sell_time when possible
        if (Schema::hasColumn('sell_requests', 'execution_date')) {
            DB::statement('ALTER TABLE sell_requests MODIFY execution_date VARCHAR(50) NULL');
            DB::table('sell_requests')->update(['execution_date' => null]);
        } else {
            Schema::table('sell_requests', function (Blueprint $table) {
                $table->string('execution_date')->nullable()->after('status');
            });
        }

        if (Schema::hasColumn('sell_requests', 'sell_time')) {
            $rows = DB::table('sell_requests')->whereNotNull('sell_time')->get(['id', 'sell_time']);
            foreach ($rows as $row) {
                $mapped = $this->mapSellTimeToExecutionDate($row->sell_time);
                if ($mapped) {
                    DB::table('sell_requests')->where('id', $row->id)->update([
                        'execution_date' => $mapped,
                    ]);
                }
            }

            Schema::table('sell_requests', function (Blueprint $table) {
                $table->dropColumn('sell_time');
            });
        }

        $enumList = "'" . implode("','", $this->executionDateValues) . "'";
        DB::statement("ALTER TABLE sell_requests MODIFY execution_date ENUM({$enumList}) NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sell_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('sell_requests', 'sell_time')) {
                $table->string('sell_time')->nullable()->after('notes');
            }
            if (Schema::hasColumn('sell_requests', 'delivery_date')) {
                $table->dropColumn('delivery_date');
            }
        });

        DB::statement('ALTER TABLE sell_requests MODIFY execution_date DATE NULL');
    }

    private function mapSellTimeToExecutionDate(?string $sellTime): ?string
    {
        if ($sellTime === null || $sellTime === '') {
            return null;
        }

        $normalized = strtolower(trim(str_replace(['-', ' '], ['_', '_'], $sellTime)));
        $normalized = preg_replace('/_+/', '_', $normalized);

        $map = [
            'immediate' => 'immediately',
            'immediately' => 'immediately',
            'now' => 'immediately',
            'ready' => 'immediately',
            '6_months' => '6_months',
            '6_month' => '6_months',
            '1_year' => '1_year',
            '1_years' => '1_year',
            '2_years' => '2_years',
            '2_year' => '2_years',
            '3_years' => '3_years',
            '3_year' => '3_years',
            '4_years' => '4_years_or_more',
            '4_year' => '4_years_or_more',
            '4_years_or_more' => '4_years_or_more',
        ];

        return $map[$normalized] ?? null;
    }
};
