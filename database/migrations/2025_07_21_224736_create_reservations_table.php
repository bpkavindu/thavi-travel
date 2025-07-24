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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('traveller_id');
            $table->foreign('traveller_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('guide_id');
            $table->foreign('guide_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignId('tour_plan_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->unsignedInteger('guest_count');
            $table->string('status')->default(1)->comment('1-pending, 2-confirmed, 0-cancelled');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
