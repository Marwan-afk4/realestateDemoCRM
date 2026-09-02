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
        Schema::create('unit_sub_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('uptown_type_id')->constrained('uptown_types')->onDelete('cascade');
            $table->string('name_en');
            $table->string('name_ar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_sub_types');
    }
};
