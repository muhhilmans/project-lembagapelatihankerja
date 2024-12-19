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
        Schema::create('servant_details', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->enum('gender', ['male', 'female', 'not_filled'])->default('not_filled');
            $table->string('place_of_birth')->default('-');
            $table->date('date_of_birth')->nullable();
            $table->string('religion')->default('-');
            $table->enum('marital_status', ['married', 'single', 'divorced', 'not_filled'])->default('not_filled');
            $table->boolean('children')->default(false);
            $table->foreignUuid('profession_id')->references('id')->on('professions')->onDelete('cascade');
            $table->enum('last_education', ['SD', 'SMP', 'SMA', 'D1', 'D2', 'D3', 'S1', 'S2', 'S3', 'not_filled'])->default('not_filled');
            $table->string('phone')->default('-');
            $table->string('emergency_number')->default('-');
            $table->string('address')->default('-');
            $table->string('rt')->nullable();
            $table->string('rw')->nullable();
            $table->string('village')->nullable();
            $table->string('district')->nullable();
            $table->string('regency')->nullable();
            $table->string('province')->nullable();
            $table->string('photo')->nullable();
            $table->string('identity_card')->nullable();
            $table->string('family_card')->nullable();
            $table->boolean('working_status')->default(false);
            $table->string('experience')->default('-');
            $table->string('description')->default('-');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servant_details');
    }
};
