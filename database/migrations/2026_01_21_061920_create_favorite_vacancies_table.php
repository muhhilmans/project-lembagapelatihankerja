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
        Schema::create('favorite_vacancies', function (Blueprint $table) {
        $table->id();
        $table->foreignUuid('user_id')->constrained('users')->cascadeOnDelete();
        $table->foreignUuid('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
        $table->timestamps();

        $table->unique(['user_id', 'vacancy_id']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorite_vacancies');
    }
};
