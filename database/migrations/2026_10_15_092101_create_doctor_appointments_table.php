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
        Schema::create('doctor_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_facility_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_service_id')->constrained()->restrictOnDelete();
            $table->string('patient_name', 150);
            $table->string('patient_phone', 25);
            $table->string('service_name', 150);
            $table->unsignedBigInteger('price_minor');
            $table->char('currency', 3);
            $table->unsignedSmallInteger('duration_minutes');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->string('status', 25)->default('confirmed');
            $table->string('source', 25)->default('doctor');
            $table->dateTime('arrived_at')->nullable();
            $table->string('notes', 1000)->nullable();
            $table->unsignedInteger('revision')->default(1);
            $table->uuid('request_key');
            $table->char('request_fingerprint', 64);
            $table->timestamps();
            $table->unique(['doctor_id', 'request_key']);
            $table->index(['doctor_id', 'starts_at', 'ends_at']);
            $table->index(['doctor_facility_id', 'starts_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_appointments');
    }
};
