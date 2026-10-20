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
        Schema::create('facility_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type');
            $table->string('city');
            $table->string('role', 100);
            $table->string('status')->default('pending');
            $table->text('rejection_reason')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'status']);
            $table->index(['name', 'city']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facility_proposals');
    }
};
