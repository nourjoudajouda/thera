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
        Schema::create('doctor_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_facility_id')->constrained()->restrictOnDelete();
            $table->string('name', 150);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('price_minor');
            $table->char('currency', 3);
            $table->unsignedSmallInteger('duration_minutes');
            $table->text('instructions')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
            $table->unique(['doctor_facility_id', 'name']);
            $table->index(['doctor_facility_id', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_services');
    }
};
