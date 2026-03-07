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
        Schema::table('pengaduan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduan', 'resolution_notes')) {
                $table->text('resolution_notes')->nullable()->after('status')->comment('Catatan penyelesaian dari admin');
            }
            if (!Schema::hasColumn('pengaduan', 'resolved_by')) {
                $table->foreignUuid('resolved_by')->nullable()->after('resolution_notes')->constrained('users')->onDelete('set null')->comment('Admin yang menyelesaikan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pengaduan', function (Blueprint $table) {
            if (Schema::hasColumn('pengaduan', 'resolved_by')) {
                $table->dropForeign(['resolved_by']);
                $table->dropColumn('resolved_by');
            }
            if (Schema::hasColumn('pengaduan', 'resolution_notes')) {
                $table->dropColumn('resolution_notes');
            }
        });
    }
};
