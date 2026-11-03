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
        Schema::table('patients', function (Blueprint $table): void {
            $table->date('birth_date')->nullable();
            $table->string('gender', 20)->nullable();
            $table->string('address', 500)->nullable();
            $table->json('health_information')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new LogicException('Patient profile data must be preserved; use a forward migration.');
    }
};
