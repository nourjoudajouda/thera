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
        foreach (['doctor_diagnostic_orders', 'doctor_referrals'] as $name) {
            Schema::table($name, function (Blueprint $table) use ($name): void {
                $table->unsignedInteger('document_version')->default(1);
                $table->foreignId('corrects_document_id')->nullable()->unique()->constrained($name)->restrictOnDelete();
                $table->text('correction_reason')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (['doctor_diagnostic_orders', 'doctor_referrals'] as $name) {
            if (DB::table($name)->whereNotNull('corrects_document_id')->exists()) {
                throw new LogicException('Cannot remove document versioning while clinical correction records exist.');
            }
        }
        foreach (['doctor_diagnostic_orders', 'doctor_referrals'] as $name) {
            Schema::table($name, function (Blueprint $table): void {
                $table->dropForeign(['corrects_document_id']);
                $table->dropUnique(['corrects_document_id']);
                $table->dropColumn(['corrects_document_id', 'correction_reason', 'document_version']);
            });
        }
    }
};
