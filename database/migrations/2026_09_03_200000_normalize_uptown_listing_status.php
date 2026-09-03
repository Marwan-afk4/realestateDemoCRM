<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! $this->hasUptownsTable()) {
            return;
        }

        DB::table('uptowns')
            ->where('status', 'unsold')
            ->update(['status' => 'available']);
    }

    public function down(): void
    {
        // Legacy status — no rollback.
    }

    private function hasUptownsTable(): bool
    {
        return DB::getSchemaBuilder()->hasTable('uptowns');
    }
};
