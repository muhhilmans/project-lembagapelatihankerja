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
            if (Schema::hasColumn('applications', 'include_bpjs')) {
                $table->dropColumn('include_bpjs');
            }
            if (Schema::hasColumn('applications', 'bpjs_id')) {
                $table->dropColumn('bpjs_id');
            }
        });

        Schema::table('applications', function (Blueprint $table) {
             $table->foreignUuid('bpjs_id')->nullable()->constrained('master_bpjs')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropForeign(['bpjs_id']);
            $table->dropColumn('bpjs_id');
            $table->boolean('include_bpjs')->default(false);
        });
    }
};
