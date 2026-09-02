<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->json('pages')->nullable()->after('title');
        });

        // Migrate existing data from 'body' to 'pages' array
        DB::table('contracts')->get()->each(function ($contract) {
            DB::table('contracts')
                ->where('id', $contract->id)
                ->update([
                    'pages' => json_encode([$contract->body ?? ''])
                ]);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('body');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->text('body')->nullable()->after('title');
        });

        // Migrate data back from 'pages' array to 'body' text
        DB::table('contracts')->get()->each(function ($contract) {
            $pages = json_decode($contract->pages ?? '[]', true);
            $body = is_array($pages) ? implode("\n\n", $pages) : ($contract->pages ?? '');
            DB::table('contracts')
                ->where('id', $contract->id)
                ->update([
                    'body' => $body
                ]);
        });

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('pages');
        });
    }
};
