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
        Schema::create('developer_salesmen', function (Blueprint $table) {
            $table->id();
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_people_id')->constrained('sale_people')->onDelete('cascade');
            $table->foreignId('uptown_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('developer_salesmen');
    }
};
