<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', fn (Blueprint $table) => $table->index('updated_at'));
        Schema::table('documents', fn (Blueprint $table) => $table->index('updated_at'));
        Schema::table('lecturers', fn (Blueprint $table) => $table->index('updated_at'));
        Schema::table('alumni', fn (Blueprint $table) => $table->index('updated_at'));
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table): void {
            $table->dropIndex(['updated_at']);
        });
        Schema::table('documents', function (Blueprint $table): void {
            $table->dropIndex(['updated_at']);
        });
        Schema::table('lecturers', function (Blueprint $table): void {
            $table->dropIndex(['updated_at']);
        });
        Schema::table('alumni', function (Blueprint $table): void {
            $table->dropIndex(['updated_at']);
        });
    }
};
