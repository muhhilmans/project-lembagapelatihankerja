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
        Schema::create('complaints', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->nullable()->references('id')->on('applications')->onDelete('cascade');
            $table->foreignUuid('servant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreignUuid('employe_id')->references('id')->on('users')->onDelete('cascade');
            $table->text('message');
            $table->enum('status', ['pending', 'process', 'accepted', 'rejected'])->default('pending');
            $table->text('notes_rejected')->nullable();
            $table->string('file')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('complaints');
    }
};
