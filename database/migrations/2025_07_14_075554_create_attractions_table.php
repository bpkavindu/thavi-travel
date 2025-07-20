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
        Schema::create('attractions', function (Blueprint $table) {
          $table->id();
            $table->string('name', 255);
            $table->decimal('rating', 2, 1); // e.g., 4.8
            $table->string('price', 3);      // $, $$, $$$
            $table->string('category', 50);
            $table->decimal('distance', 6, 2); // e.g., 12.50 km
            $table->string('country', 100);
            $table->string('city', 100);
            $table->longText('image')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attractions');
    }
};
