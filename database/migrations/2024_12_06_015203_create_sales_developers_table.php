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
        Schema::create('sales_developers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('sale_name')->Notnulable();
            $table->string('sale_phone')->Notnulable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales_developers');
    }
};
