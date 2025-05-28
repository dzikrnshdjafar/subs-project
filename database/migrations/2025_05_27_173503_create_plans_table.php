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
            $table->string('name')->unique(); // 'gold', 'platinum', 'diamond'
            $table->string('slug')->unique(); // slug untuk URL atau identifikasi
            $table->text('description')->nullable();
            $table->integer('rank')->default(0);
            $table->decimal('price', 8, 2)->default(0); // Harga bisa 0 untuk free
            $table->integer('duration_days')->nullable(); // null untuk free/lifetime, atau jumlah hari
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
