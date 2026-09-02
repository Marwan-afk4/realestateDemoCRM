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
        Schema::create('sell_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('age');
            $table->string('identity_front_image');
            $table->string('identity_back_image');
            $table->string('country');
            $table->string('city');
            $table->string('area');
            $table->foreignId('developer_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('compound_id')->nullable()->constrained()->onDelete('set null');
            $table->string('detailed_pdf');
            $table->decimal('price', 15, 2);
            $table->boolean('installments')->default(false);
            $table->integer('installments_years')->nullable();
            $table->integer('installments_years_left')->nullable();
            $table->decimal('installments_total_price', 15, 2)->nullable();
            $table->decimal('installments_price_per_year', 15, 2)->nullable();
            $table->enum('type', ['residential', 'commercial', 'administrative']);
            
            // Common property details (as requested in the first message)
            $table->integer('rooms_no')->nullable();
            $table->integer('bathrooms_no')->nullable();
            $table->decimal('space', 10, 2)->nullable();
            $table->integer('floor_no')->nullable();
            $table->boolean('garden_area')->default(false);
            
            $table->text('notes')->nullable();
            $table->string('sell_time')->nullable(); // Selection from 6 months to 3 years
            
            // For any dynamic data the admin might add via the config tables
            $table->json('extra_data')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sell_requests');
    }
};
