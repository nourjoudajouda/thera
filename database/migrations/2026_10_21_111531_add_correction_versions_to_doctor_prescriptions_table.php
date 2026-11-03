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
        Schema::table('doctor_prescriptions', function (Blueprint $table): void {
            $table->unsignedInteger('document_version')->default(1);
            $table->foreignId('corrects_prescription_id')->nullable()->unique()->constrained('doctor_prescriptions')->restrictOnDelete();
            $table->text('correction_reason')->nullable();
            $table->unique(['doctor_visit_id', 'document_version'], 'doctor_prescription_visit_version_unique');
        });
        Schema::table('doctor_prescriptions', function (Blueprint $table): void {
            $table->dropUnique(['doctor_visit_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('doctor_prescriptions')->whereNotNull('corrects_prescription_id')->exists()) {
            throw new LogicException('Cannot roll back prescription versioning while correction records exist. Preserve the clinical history.');
        }
        Schema::table('doctor_prescriptions', function (Blueprint $table): void {
            $table->unique('doctor_visit_id');
            $table->dropForeign(['corrects_prescription_id']);
            $table->dropUnique(['corrects_prescription_id']);
            $table->dropUnique('doctor_prescription_visit_version_unique');
            $table->dropColumn(['document_version', 'corrects_prescription_id', 'correction_reason']);
        });
    }
};
