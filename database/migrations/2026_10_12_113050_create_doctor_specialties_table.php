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
        Schema::create('doctor_specialties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('specialty_id')->constrained()->restrictOnDelete();
            $table->string('type');
            $table->string('verification_status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->unique(['doctor_id', 'specialty_id']);
            $table->index(['doctor_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_specialties');
    }
};
