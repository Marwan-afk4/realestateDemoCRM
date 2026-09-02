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
        Schema::create('administrative_data', function (Blueprint $table) {
            $table->id();
            $table->string('field_name');
            $table->string('label_en');
            $table->string('label_ar')->nullable();
            $table->string('type')->default('text');
            $table->boolean('is_required')->default(true);
            $table->json('options')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('administrative_data');
    }
};
