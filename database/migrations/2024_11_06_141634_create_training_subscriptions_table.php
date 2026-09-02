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
        Schema::create('training_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('full_name')->NotNullable();
            $table->string('email')->NotNullable()->unique();
            $table->string('phone')->NotNullable()->unique();
            $table->integer('age')->Notnulable();
            $table->string('qualification')->Notnulable();
            $table->string('governate')->Notnulable();
            $table->integer('experience_year')->Notnulable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('training_subscriptions');
    }
};
