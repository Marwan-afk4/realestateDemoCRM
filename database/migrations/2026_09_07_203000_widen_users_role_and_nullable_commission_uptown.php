<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE users MODIFY role VARCHAR(32) NOT NULL DEFAULT 'user'");

        $this->dropForeignKey('commissions', 'uptown_id');
        DB::statement('ALTER TABLE commissions MODIFY uptown_id BIGINT UNSIGNED NULL');
        Schema::table('commissions', function ($table) {
            $table->foreign('uptown_id')->references('id')->on('uptowns')->nullOnDelete();
        });
    }

    public function down(): void
    {
        $this->dropForeignKey('commissions', 'uptown_id');
        DB::statement('ALTER TABLE commissions MODIFY uptown_id BIGINT UNSIGNED NOT NULL');
        Schema::table('commissions', function ($table) {
            $table->foreign('uptown_id')->references('id')->on('uptowns')->cascadeOnDelete();
        });

        DB::statement("ALTER TABLE users MODIFY role ENUM('user','brocker','admin','trainer','SuperAdmin') NOT NULL DEFAULT 'user'");
    }

    private function dropForeignKey(string $table, string $column): void
    {
        $keys = DB::select(
            'SELECT CONSTRAINT_NAME as name
             FROM information_schema.KEY_COLUMN_USAGE
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = ?
               AND COLUMN_NAME = ?
               AND REFERENCED_TABLE_NAME IS NOT NULL',
            [$table, $column]
        );

        foreach ($keys as $key) {
            DB::statement("ALTER TABLE `{$table}` DROP FOREIGN KEY `{$key->name}`");
        }
    }
};
