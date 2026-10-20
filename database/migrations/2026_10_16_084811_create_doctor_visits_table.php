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
        Schema::create('doctor_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_appointment_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_facility_id')->constrained()->restrictOnDelete();
            $table->string('status', 25)->default('draft');
            $table->json('content');
            $table->unsignedInteger('revision')->default(1);
            $table->dateTime('started_at');
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
            $table->index(['doctor_id', 'started_at']);
        });
        Schema::create('doctor_visit_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_visit_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('action', 25);
            $table->unsignedInteger('revision');
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_visit_revisions');
        Schema::dropIfExists('doctor_visits');
    }
};
