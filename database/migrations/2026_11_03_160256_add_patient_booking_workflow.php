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
        if (! Schema::hasColumn('doctor_appointments', 'is_test')) {
            Schema::table('doctor_appointments', function (Blueprint $table): void {
                $table->boolean('is_test')->default(false);
                $table->json('payment_policy_snapshot')->nullable();
                $table->unsignedBigInteger('test_paid_minor')->default(0);
                $table->timestamp('hold_expires_at')->nullable()->index();
            });
        }
        Schema::create('patient_payment_attempts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_appointment_id')->constrained()->restrictOnDelete();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->uuid('request_key');
            $table->unsignedBigInteger('amount_minor');
            $table->string('status', 20);
            $table->string('outcome', 20);
            $table->timestamps();
            $table->unique(['doctor_appointment_id', 'request_key'], 'patient_attempt_request_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Booking and payment history must be preserved. Use a forward migration.');
    }
};
