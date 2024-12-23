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
        Schema::create('apply_jobs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('servant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignUuid('vacancy_id')->references('id')->on('vacancies')->onDelete('cascade');
            $table->enum('status', ['pending', 'interview', 'accepted', 'rejected'])->default('pending');
            $table->text('notes')->nullable();
            $table->date('interview_date')->nullable();
            $table->string('file_contract')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apply_jobs');
    }
};
