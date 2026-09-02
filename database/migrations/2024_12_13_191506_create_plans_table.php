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
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->notnull();
            $table->integer('count_of_leads')->notnull();
            $table->integer('period_in_days')->notnull();
            $table->double('price')->notnull();
            $table->enum('discount_type', ['percentage', 'fixed'])->nullable();
            $table->double('discount_value')->nullable();
            $table->double('price_after_discount')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
