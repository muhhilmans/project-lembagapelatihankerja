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
        Schema::create('profession_vacancy', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('vacancy_id')->constrained('vacancies')->cascadeOnDelete();
            $table->foreignUuid('profession_id')->constrained('professions')->cascadeOnDelete();

            $table->unique(['vacancy_id', 'profession_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_vacancy');
    }
};
