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
        Schema::table('program_profiles', function (Blueprint $table) {
            $table->string('description_image')->nullable()->after('description');
            $table->string('history_image')->nullable()->after('history');
            $table->string('goals_image')->nullable()->after('goals');
            $table->string('advantages_image')->nullable()->after('advantages');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('program_profiles', function (Blueprint $table) {
            $table->dropColumn(['description_image', 'history_image', 'goals_image', 'advantages_image']);
        });
    }
};
