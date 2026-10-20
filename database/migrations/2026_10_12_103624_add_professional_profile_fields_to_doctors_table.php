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
        Schema::table('doctors', function (Blueprint $table) {
            $table->json('languages')->nullable()->after('phone');
            $table->text('bio')->nullable()->after('languages');
            $table->string('professional_title')->nullable()->after('bio');
            $table->unsignedTinyInteger('years_experience')->nullable()->after('professional_title');
            $table->text('professional_bio')->nullable()->after('years_experience');
            $table->timestamp('profile_completed_at')->nullable()->after('professional_bio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('doctors', function (Blueprint $table) {
            $table->dropColumn([
                'languages',
                'bio',
                'professional_title',
                'years_experience',
                'professional_bio',
                'profile_completed_at',
            ]);
        });
    }
};
