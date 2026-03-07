<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_salaries', function (Blueprint $table) {
            $table->string('payment_majikan_status')->nullable()->after('payment_majikan_image');
            $table->timestamp('payment_majikan_verified_at')->nullable()->after('payment_majikan_status');
        });
    }

    public function down(): void
    {
        Schema::table('worker_salaries', function (Blueprint $table) {
            $table->dropColumn(['payment_majikan_status', 'payment_majikan_verified_at']);
        });
    }
};
