<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('doctor_external_reports', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->foreignId('doctor_diagnostic_order_id')->constrained()->restrictOnDelete();
            $table->string('title', 180);
            $table->string('source_name', 180);
            $table->string('source_reference', 120)->nullable();
            $table->date('report_date');
            $table->string('file_path');
            $table->string('file_mime', 50);
            $table->unsignedInteger('file_size');
            $table->char('file_sha256', 64);
            $table->uuid('request_key');
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_note')->nullable();
            $table->timestamps();
            $table->unique(['doctor_id', 'request_key']);
            $table->index(['doctor_id', 'reviewed_at', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('doctor_external_reports')->exists()) {
            throw new LogicException('لا يمكن حذف تقارير طبية محفوظة.');
        }
        Schema::dropIfExists('doctor_external_reports');
    }
};
