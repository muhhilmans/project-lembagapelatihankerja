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
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('servant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignUuid('vacancy_id')->nullable()->references('id')->on('vacancies')->onDelete('cascade');
            $table->foreignUuid('employe_id')->nullable()->references('id')->on('users')->onDelete('cascade');
            $table->enum('status', ['pending', 'interview', 'passed', 'choose', 'verify', 'accepted', 'rejected'])->default('pending');
            $table->text('notes_interview')->nullable();
            $table->text('notes_verify')->nullable();
            $table->text('notes_accepted')->nullable();
            $table->text('notes_rejected')->nullable();
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
        Schema::dropIfExists('applications');
    }
};
