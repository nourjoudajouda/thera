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
        Schema::create('doctor_verification_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('admins')->nullOnDelete();
            $table->string('reference')->nullable()->unique();
            $table->string('status')->default('draft');
            $table->json('documents')->nullable();
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('decided_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'status']);
            $table->index(['status', 'submitted_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_verification_requests');
    }
};
