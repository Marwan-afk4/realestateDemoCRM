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
        Schema::create('buy_appartment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('apartment_id')->nullable()->constrained('uptowns')->onDelete('set null');
            $table->integer('age');
            $table->string('identity_front_image');
            $table->string('identity_back_image');
            $table->string('city');
            $table->string('area');
            $table->string('job_title');
            $table->decimal('monthly_income', 15, 2)->nullable();
            $table->enum('years_of_installment', [3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20]);
            $table->decimal('deposit_percetage', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('buy_appartment_installments');
    }
};
