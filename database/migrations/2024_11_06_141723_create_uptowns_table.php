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
        Schema::create('uptowns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('name')->Notnulable();
            $table->string('apparment')->Notnulable();
            $table->string('strat_price')->Notnulable();
            $table->date('delivery_date')->Notnulable();
            $table->string('sale_type')->Notnulable();
            $table->longText('image')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uptowns');
    }
};
