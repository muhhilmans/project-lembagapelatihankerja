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
        Schema::table('complaints', function (Blueprint $table) {
            // Drop foreign keys sebelum mengubah kolom
            $table->dropForeign(['servant_id']);
            $table->dropForeign(['employe_id']);

            // Ubah kolom menjadi nullable
            $table->uuid('servant_id')->nullable()->change();
            $table->uuid('employe_id')->nullable()->change();

            // Tambahkan kembali foreign keys
            $table->foreign('servant_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['servant_id']);
            $table->dropForeign(['employe_id']);

            // Ubah kembali kolom menjadi non-nullable
            $table->uuid('servant_id')->nullable(false)->change();
            $table->uuid('employe_id')->nullable(false)->change();

            // Tambahkan kembali foreign keys
            $table->foreign('servant_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('employe_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};
