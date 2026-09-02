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
        // First, drop the existing transaction_deals table
        Schema::dropIfExists('transaction_deals');

        // Create the new deals table with the required structure
        Schema::create('deals', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('nationality_id')->nullable();
            $table->string('phone');
            $table->string('email');
            $table->foreignId('developer_id')->constrained('developers')->onDelete('cascade');
            $table->foreignId('compound_id')->constrained('compounds')->onDelete('cascade');
            $table->integer('number_of_units');
            $table->enum('status', ['pending', 'approved', 'rejected', 'semidone'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');

        // Recreate the original transaction_deals table structure
        Schema::create('transaction_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brocker_id')->constrained()->onDelete('cascade');
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_person_id')->constrained()->onDelete('cascade');
            $table->foreignId('uptown_id')->constrained()->onDelete('cascade');
            $table->string('fullname');
            $table->string('phone');
            $table->float('deal_value');
            $table->longText('image')->nullable();
            $table->string('profit')->nullable();
            $table->timestamps();
        });
    }
};
