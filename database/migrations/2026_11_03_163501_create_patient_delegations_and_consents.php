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
        Schema::create('patient_delegations', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('kind', 20);
            $table->string('relationship', 100);
            $table->string('status', 20)->default('pending');
            $table->string('proof_path')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->restrictOnDelete();
            $table->string('review_reason', 500)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->unique(['patient_id', 'user_id']);
        });
        Schema::create('patient_doctor_consents', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('granted_by')->constrained('users')->restrictOnDelete();
            $table->timestamp('expires_at');
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();
            $table->unique(['patient_id', 'doctor_id']);
        });
        Schema::create('patient_access_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained()->restrictOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('admins')->restrictOnDelete();
            $table->string('action', 50);
            $table->json('detail')->nullable();
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Delegations, consents and access events must be preserved.');
    }
};
