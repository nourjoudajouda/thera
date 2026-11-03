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
        Schema::table('doctor_patients', function (Blueprint $table): void {
            $table->foreignId('patient_id')->nullable()->constrained()->restrictOnDelete();
            $table->unique(['doctor_id', 'patient_id']);
        });
        Schema::create('patient_directory_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_appointment_id')->nullable()->constrained()->restrictOnDelete();
            $table->string('action', 30);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('doctor_patients')->whereNotNull('patient_id')->exists() || DB::table('patient_directory_events')->exists()) {
            throw new LogicException('لا يمكن إسقاط روابط مرضى ثيرا أو سجل استخدامها.');
        }
        Schema::dropIfExists('patient_directory_events');
        Schema::table('doctor_patients', function (Blueprint $table): void {
            $table->dropUnique(['doctor_id', 'patient_id']);
            $table->dropConstrainedForeignId('patient_id');
        });
    }
};
