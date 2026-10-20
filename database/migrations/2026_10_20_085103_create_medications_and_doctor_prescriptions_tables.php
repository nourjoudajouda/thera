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
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->string('active_ingredient', 150);
            $table->string('strength', 100);
            $table->string('dosage_form', 100);
            $table->char('fingerprint', 64)->unique();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('decided_by')->nullable()->constrained('admins')->restrictOnDelete();
            $table->dateTime('decided_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
        Schema::create('doctor_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_visit_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('status', 20)->default('draft');
            $table->json('content');
            $table->json('issued_snapshot')->nullable();
            $table->unsignedInteger('revision')->default(1);
            $table->dateTime('approved_at')->nullable();
            $table->timestamps();
            $table->index(['doctor_id', 'created_at']);
        });
        Schema::create('doctor_prescription_revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_prescription_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('action', 20);
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
        Schema::dropIfExists('doctor_prescription_revisions');
        Schema::dropIfExists('doctor_prescriptions');
        Schema::dropIfExists('medications');
    }
};
