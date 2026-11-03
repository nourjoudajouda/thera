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
        Schema::create('doctor_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_visit_id')->constrained()->restrictOnDelete();
            $table->string('kind', 20);
            $table->string('status', 20)->default('draft');
            $table->string('priority', 20)->default('normal');
            $table->foreignId('target_doctor_id')->nullable()->constrained('doctors')->restrictOnDelete();
            $table->foreignId('target_facility_id')->nullable()->constrained('facilities')->restrictOnDelete();
            $table->json('content');
            $table->json('issued_snapshot')->nullable();
            $table->dateTime('approved_at')->nullable();
            $table->unsignedInteger('revision')->default(1);
            $table->uuid('request_key');
            $table->timestamps();
            $table->unique(['doctor_id', 'request_key']);
            $table->index(['doctor_id', 'kind', 'status', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_referrals');
    }
};
