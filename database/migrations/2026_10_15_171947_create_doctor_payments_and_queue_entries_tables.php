<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('doctor_payments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_appointment_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('kind', 20);
            $table->bigInteger('amount_minor');
            $table->char('currency', 3);
            $table->string('method', 20);
            $table->string('reference', 100)->nullable();
            $table->string('reason', 500)->nullable();
            $table->foreignId('reversal_of_id')->nullable()->unique()->constrained('doctor_payments')->restrictOnDelete();
            $table->uuid('request_key');
            $table->char('request_fingerprint', 64);
            $table->json('receipt_snapshot');
            $table->timestamps();
            $table->unique(['doctor_id', 'request_key']);
            $table->index(['doctor_appointment_id', 'created_at']);
        });
        Schema::create('doctor_queue_entries', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_appointment_id')->unique()->constrained()->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->dateTime('called_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('doctor_queue_entries');
        Schema::dropIfExists('doctor_payments');
    }
};
