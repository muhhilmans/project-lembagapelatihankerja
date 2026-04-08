<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('worker_salaries', function (Blueprint $table) {
            $table->double('payment_majikan_amount')->nullable()->after('payment_majikan_image');
            $table->string('payment_majikan_method')->nullable()->after('payment_majikan_amount');
            $table->string('payment_majikan_ref_number')->nullable()->after('payment_majikan_method');

            $table->double('payment_pembantu_amount')->nullable()->after('payment_pembantu_image');
            $table->string('payment_pembantu_ref_number')->nullable()->after('payment_pembantu_amount');
            $table->enum('payment_pembantu_status', ['belum', 'sudah'])->default('belum')->after('payment_pembantu_ref_number');
            $table->timestamp('payment_pembantu_transfer_at')->nullable()->after('payment_pembantu_status');
        });
    }

    public function down(): void
    {
        Schema::table('worker_salaries', function (Blueprint $table) {
            $table->dropColumn([
                'payment_majikan_amount',
                'payment_majikan_method',
                'payment_majikan_ref_number',
                'payment_pembantu_amount',
                'payment_pembantu_ref_number',
                'payment_pembantu_status',
                'payment_pembantu_transfer_at',
            ]);
        });
    }
};
