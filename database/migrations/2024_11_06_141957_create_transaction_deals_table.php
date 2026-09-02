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
        Schema::create('transaction_deals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brocker_id')->constrained()->onDelete('cascade');
            $table->foreignId('developer_id')->constrained()->onDelete('cascade');
            $table->foreignId('sale_person_id')->constrained()->onDelete('cascade');
            $table->foreignId('uptown_id')->constrained()->onDelete('cascade');
            $table->string('fullname')->Notnulable();
            $table->string('phone')->Notnulable();
            $table->float('deal_value')->Notnulable();
            $table->longText('image')->nullable();
            $table->string('profit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_deals');
    }
};
