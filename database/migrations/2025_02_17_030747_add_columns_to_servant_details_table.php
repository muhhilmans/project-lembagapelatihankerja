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
        Schema::table('servant_details', function (Blueprint $table) {
            $table->boolean('is_inval')->default(false);
            $table->boolean('is_stay')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('servant_details', function (Blueprint $table) {
            $table->dropColumn(['is_inval', 'is_stay']);
        });
    }
};
