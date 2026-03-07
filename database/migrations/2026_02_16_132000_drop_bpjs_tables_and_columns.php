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
            // Drop foreign key first
            $table->dropForeign(['bpjs_id']);
            $table->dropColumn('bpjs_id');
        });

        Schema::dropIfExists('master_bpjs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('master_bpjs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('employer_fee_pct', 5, 2)->default(0);
            $table->decimal('worker_fee_pct', 5, 2)->default(0);
            $table->integer('bpjs_nominal')->default(0);
            $table->timestamps();
        });

        Schema::table('applications', function (Blueprint $table) {
            $table->foreignUuid('bpjs_id')->nullable()->constrained('master_bpjs')->onDelete('set null');
        });
    }
};
