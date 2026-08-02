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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content')->nullable();
            $table->timestamps();
        });

        DB::table('pages')->insert([
            ['title' => 'Kebijakan Privasi', 'slug' => 'kebijakan-privasi', 'content' => null, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Aksesibilitas', 'slug' => 'aksesibilitas', 'content' => null, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
