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
        Schema::table('master_bpjs', function (Blueprint $table) {
            $table->decimal('employer_fee_pct', 5, 2)->default(0)->after('description');
            $table->decimal('worker_fee_pct', 5, 2)->default(0)->after('employer_fee_pct');
            $table->integer('bpjs_nominal')->default(0)->after('worker_fee_pct');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('master_bpjs', function (Blueprint $table) {
            $table->dropColumn(['employer_fee_pct', 'worker_fee_pct', 'bpjs_nominal']);
        });
    }
};
