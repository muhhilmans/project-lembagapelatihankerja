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
        Schema::create('warranty_payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('application_id')->nullable();
            $table->integer('month_number')->nullable();
            $table->date('month_date')->nullable();
            $table->bigInteger('amount')->default(0);
            $table->string('payment_image')->nullable();
            $table->string('status')->default('pending'); // pending, waiting, paid
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();

            $table->foreign('application_id')->references('id')->on('applications')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('warranty_payments');
    }
};
