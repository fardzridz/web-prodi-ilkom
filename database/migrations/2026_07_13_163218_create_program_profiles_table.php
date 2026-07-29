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
        Schema::create('program_profiles', function (Blueprint $table) {
            $table->id();
            $table->longText('history')->nullable();
            $table->longText('description')->nullable();
            $table->text('vision')->nullable();
            $table->longText('mission')->nullable();
            $table->longText('goals')->nullable();
            $table->string('accreditation')->nullable();
            $table->longText('advantages')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('program_profiles');
    }
};
