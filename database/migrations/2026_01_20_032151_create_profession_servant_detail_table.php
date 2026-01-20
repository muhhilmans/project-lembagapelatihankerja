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
        Schema::create('profession_servant_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('servant_detail_id')
                ->constrained('servant_details')
                ->onDelete('cascade');
            $table->foreignUuid('profession_id') 
                ->constrained('professions')
                ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profession_servant_detail');
    }
};
