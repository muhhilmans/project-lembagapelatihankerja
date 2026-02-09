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
        Schema::create('urgencies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->comment('MISCONDUCT, ABSENT, FRAUD, HARASSMENT');
            $table->string('name')->comment('Nama jenis komplain');
            $table->text('description')->nullable()->comment('Penjelasan');
            $table->enum('default_urgency', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL']);
            $table->enum('target_role', ['klien', 'mitra', 'admin']);
            $table->string('sla_time')->comment('3x24 / 2 x 24 / 1x24/ 24jam / 12jam / 6jam');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('urgencies');
    }
};
