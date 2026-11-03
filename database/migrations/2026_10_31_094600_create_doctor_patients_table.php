<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_patients', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('phone', 16);
            $table->timestamps();
            $table->index(['doctor_id', 'id']);
        });
        Schema::table('doctor_appointments', function (Blueprint $table): void {
            $table->foreignId('doctor_patient_id')->nullable()->constrained()->restrictOnDelete();
        });
        Schema::create('doctor_patient_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_appointment_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('action', 30);
            $table->uuid('request_key')->nullable();
            $table->json('snapshot')->nullable();
            $table->timestamps();
            $table->unique(['doctor_id', 'request_key']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('doctor_patients')->exists()) {
            throw new LogicException('لا يمكن حذف هوية ملفات مرضى محفوظة.');
        }
        Schema::dropIfExists('doctor_patient_events');
        Schema::table('doctor_appointments', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('doctor_patient_id');
        });
        Schema::dropIfExists('doctor_patients');
    }
};
