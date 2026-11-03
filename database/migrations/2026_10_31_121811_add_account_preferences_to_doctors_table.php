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
        Schema::table('doctors', function (Blueprint $table) {
            $table->json('account_preferences')->nullable();
            $table->unsignedInteger('preferences_revision')->default(0);
        });
        Schema::create('doctor_setting_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->restrictOnDelete();
            $table->unsignedInteger('revision');
            $table->json('before');
            $table->json('after');
            $table->timestamp('created_at');
            $table->unique(['doctor_id', 'revision']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::table('doctor_setting_events')->exists()) {
            throw new RuntimeException('لا تُحذف إعدادات محفوظة أو سجل تدقيقها بالرجوع عن الترحيل؛ استخدم ترحيلًا أماميًا.');
        }
        Schema::dropIfExists('doctor_setting_events');
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn(['account_preferences', 'preferences_revision']);
        });
    }
};
