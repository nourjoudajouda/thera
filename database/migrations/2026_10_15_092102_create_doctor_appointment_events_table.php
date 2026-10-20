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
        Schema::create('doctor_appointment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_appointment_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('action', 25);
            $table->string('reason', 500)->nullable();
            $table->json('before')->nullable();
            $table->json('after');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_appointment_events');
    }
};
