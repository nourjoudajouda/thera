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
        Schema::create('doctor_schedule_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_schedule_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->string('type', 20);
            $table->json('periods')->nullable();
            $table->string('note', 500)->nullable();
            $table->unique(['doctor_schedule_id', 'date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedule_exceptions');
    }
};
