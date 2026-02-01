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
        Schema::create('worker_salaries', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('application_id')->references('id')->on('applications')->onDelete('cascade');
            $table->date('month');
            $table->integer('presence');
            $table->double('total_salary')->unsigned();
            $table->double('total_salary_majikan')->unsigned();
            $table->double('total_salary_pembantu')->unsigned();
            $table->string('status')->nullable();
            $table->string('payment_majikan_image')->nullable();
            $table->string('payment_pembantu_image')->nullable();
            $table->foreignUuid('voucher_id')->nullable()->references('id')->on('vouchers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('worker_salaries');
    }
};
