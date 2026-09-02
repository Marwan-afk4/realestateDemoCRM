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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->Notnulable();
            $table->string('last_name')->Notnulable();
            $table->string('email')->unique()->Notnullable();
            $table->string('phone')->Notnulable()->unique();
            $table->string('password')->Notnulable();
            $table->string('provider')->Notnulable();
            $table->string('provider_id')->Notnulable();
            $table->string('role')->Notnulable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
