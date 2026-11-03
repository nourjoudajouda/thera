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
        Schema::create('patient_notifications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('event_key', 150);
            $table->string('kind', 40);
            $table->string('target_type', 30);
            $table->unsignedBigInteger('target_id');
            $table->timestamp('read_at')->nullable();
            $table->string('push_state', 20)->default('pending');
            $table->timestamps();
            $table->unique(['user_id', 'event_key']);
        });
        Schema::create('patient_devices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('personal_access_token_id')->constrained('personal_access_tokens')->cascadeOnDelete();
            $table->text('token');
            $table->char('token_hash', 64)->unique();
            $table->string('platform', 10);
            $table->string('locale', 2)->default('ar');
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::create('patient_reminders', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('prescription_id')->nullable()->constrained('doctor_prescriptions')->restrictOnDelete();
            $table->string('title', 150);
            $table->char('time', 5);
            $table->string('timezone', 64);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
        Schema::create('patient_favorites', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'doctor_id']);
        });
        Schema::create('patient_reviews', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('patient_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_appointment_id')->unique()->constrained()->restrictOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->string('comment', 1000)->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('reviewed_by')->nullable()->constrained('admins')->restrictOnDelete();
            $table->string('review_reason', 500)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Patient notifications, reminders and review history must be preserved.');
    }
};
