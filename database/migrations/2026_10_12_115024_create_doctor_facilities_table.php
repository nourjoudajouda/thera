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
        Schema::create('doctor_facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->foreignId('facility_id')->constrained()->cascadeOnDelete();
            $table->string('role');
            $table->string('membership_status')->default('active');
            $table->boolean('is_public')->default(true);
            $table->timestamps();

            $table->unique(['doctor_id', 'facility_id']);
            $table->index(['doctor_id', 'membership_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_facilities');
    }
};
