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
        Schema::create('lead_subscritions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name')->NotNullable();
            $table->string('email')->NotNullable()->unique();
            $table->string('phone')->NotNullable()->unique();
            $table->integer('age')->Notnulable();
            $table->string('governate')->Notnulable();
            $table->integer('experience_year')->Notnulable();
            $table->string('interst_areas')->Notnulable();
            $table->string('projects')->Notnulable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_subscritions');
    }
};
