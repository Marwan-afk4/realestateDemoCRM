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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_agency_id')->constrained('marketing_agencies')->onDelete('cascade');
            $table->foreignId('uptown_id')->constrained()->onDelete('cascade');
            $table->string('lead_name')->Notnulable();
            $table->string('lead_phone')->Notnulable();
            $table->date('brocker_start_date')->nullable();
            $table->string('sales_man_name')->nullable();
            $table->string('sales_man_phone')->nullable();
            $table->enum('status',['lost','done','pending','empty'])->default('empty');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
