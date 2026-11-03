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
        Schema::create('doctor_diagnostic_order_revisions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_diagnostic_order_id');
            $table->foreign('doctor_diagnostic_order_id', 'diagnostic_order_revision_order_fk')->references('id')->on('doctor_diagnostic_orders')->restrictOnDelete();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->string('action', 20);
            $table->unsignedInteger('revision');
            $table->json('snapshot')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_diagnostic_order_revisions');
    }
};
