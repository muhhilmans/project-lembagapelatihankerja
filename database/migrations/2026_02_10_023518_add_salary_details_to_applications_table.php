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
        Schema::table('applications', function (Blueprint $table) {
            $table->enum('salary_type', ['contract', 'fee'])->nullable();
            $table->double('admin_fee')->nullable();
            $table->string('warranty_duration')->nullable();
            $table->boolean('is_infal')->default(false);
            $table->enum('infal_frequency', ['hourly', 'daily', 'weekly'])->nullable();
            $table->double('deduction_amount')->nullable();
            $table->boolean('include_bpjs')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn([
                'salary_type',
                'admin_fee',
                'warranty_duration',
                'is_infal',
                'infal_frequency',
                'deduction_amount',
                'include_bpjs'
            ]);
        });
    }
};
