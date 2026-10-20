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
        Schema::create('doctor_credentials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->string('title');
            $table->string('issuer');
            $table->string('country');
            $table->date('issued_on');
            $table->date('expires_on')->nullable();
            $table->string('document_path');
            $table->string('document_original_name');
            $table->string('verification_status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'verification_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_credentials');
    }
};
