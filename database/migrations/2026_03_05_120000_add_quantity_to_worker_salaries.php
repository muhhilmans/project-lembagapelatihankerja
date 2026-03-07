<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_salaries', function (Blueprint $table) {
            $table->integer('quantity')->nullable()->after('presence');
        });
    }

    public function down(): void
    {
        Schema::table('worker_salaries', function (Blueprint $table) {
            $table->dropColumn('quantity');
        });
    }
};
