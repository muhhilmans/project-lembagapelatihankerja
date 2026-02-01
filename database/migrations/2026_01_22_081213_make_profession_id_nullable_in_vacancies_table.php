<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PENTING: Matikan pengecekan FK sementara agar database nurut
        Schema::disableForeignKeyConstraints();

        // 1. UBAH TABEL VACANCIES
        Schema::table('vacancies', function (Blueprint $table) {
            // A. Coba Hapus Foreign Key Lama (Gunakan array agar Laravel menebak namanya)
            try {
                $table->dropForeign(['profession_id']);
            } catch (\Exception $e) {
                // Jika gagal (misal nama beda), kita coba nama default Laravel
                try {
                    $table->dropForeign('vacancies_profession_id_foreign');
                } catch (\Exception $x) {}
            }

            // B. Ubah Kolom Jadi Nullable
            $table->uuid('profession_id')->nullable()->change();

            // C. Pasang Lagi Foreign Key-nya (Tapi sekarang kolomnya sudah nullable)
            $table->foreign('profession_id')
                  ->references('id')
                  ->on('professions')
                  ->onDelete('cascade');
        });

        // 2. UBAH TABEL SERVANT_DETAILS
        Schema::table('servant_details', function (Blueprint $table) {
            // A. Hapus FK
            try {
                $table->dropForeign(['profession_id']);
            } catch (\Exception $e) {
                try {
                    $table->dropForeign('servant_details_profession_id_foreign');
                } catch (\Exception $x) {}
            }

            // B. Ubah Nullable
            $table->uuid('profession_id')->nullable()->change();

            // C. Pasang FK Lagi
            $table->foreign('profession_id')
                  ->references('id')
                  ->on('professions')
                  ->onDelete('cascade');
        });

        // Nyalakan lagi keamanannya
        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        // Kembalikan ke TIDAK BOLEH NULL (Hati-hati jika data sudah ada yang null)
        Schema::table('vacancies', function (Blueprint $table) {
             // Drop FK dulu sebelum ubah kolom
             $table->dropForeign(['profession_id']);
             $table->uuid('profession_id')->nullable(false)->change();
             $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
        });

        Schema::table('servant_details', function (Blueprint $table) {
             $table->dropForeign(['profession_id']);
             $table->uuid('profession_id')->nullable(false)->change();
             $table->foreign('profession_id')->references('id')->on('professions')->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }
};
