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
        Schema::create('marketing_devs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->string('name')->Notnulable();
            $table->string('phone')->unique()->Notnulable();
            $table->string('place')->Notnulable();
            $table->string('start_date')->Notnulable();
            $table->string('end_date')->Notnulable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketing_devs');
    }
};
