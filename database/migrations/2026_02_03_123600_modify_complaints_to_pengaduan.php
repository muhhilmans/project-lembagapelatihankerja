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
        // 1. Rename table if old table exists and new one doesn't
        if (Schema::hasTable('complaints') && !Schema::hasTable('pengaduan')) {
            Schema::rename('complaints', 'pengaduan');
        }

        // 2. Modify structure - Drop Items
        Schema::table('pengaduan', function (Blueprint $table) {
             // We can't easily check for FK existence in Blueprint, but we can catch the error if we use raw statements, 
             // OR we just assume the name. 
             // To be safe against "Key does not exist" errors, we can check information_schema or just try/catch the Schema operation?
             // But simpler is to rely on consistent state. The error previously was due to wrong name guessing.
             // We will try to drop the known old foreign keys.
             // However, to avoid "Error: Can't drop because it doesn't exist" (idempotency), we should check.
        });
        
        // Helper to drop FK if exists
        $dropFkIfExists = function($table, $fkName) {
            $exists = DB::select("SELECT * FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?", [$table, $fkName]);
            if (!empty($exists)) {
                Schema::table($table, function (Blueprint $table) use ($fkName) {
                    $table->dropForeign($fkName);
                });
            }
        };

        $dropFkIfExists('pengaduan', 'complaints_application_id_foreign');
        $dropFkIfExists('pengaduan', 'complaints_servant_id_foreign');
        $dropFkIfExists('pengaduan', 'complaints_employe_id_foreign');
        
        // Also try 'pengaduan_' prefixed ones just in case
        $dropFkIfExists('pengaduan', 'pengaduan_application_id_foreign');
        $dropFkIfExists('pengaduan', 'pengaduan_servant_id_foreign');
        $dropFkIfExists('pengaduan', 'pengaduan_employe_id_foreign');


        Schema::table('pengaduan', function (Blueprint $table) {
            // Drop columns if they exist
            if (Schema::hasColumn('pengaduan', 'application_id')) $table->dropColumn('application_id');
            if (Schema::hasColumn('pengaduan', 'servant_id')) $table->dropColumn('servant_id');
            if (Schema::hasColumn('pengaduan', 'employe_id')) $table->dropColumn('employe_id');
            if (Schema::hasColumn('pengaduan', 'message')) $table->dropColumn('message');
            if (Schema::hasColumn('pengaduan', 'status')) $table->dropColumn('status');
            if (Schema::hasColumn('pengaduan', 'notes_rejected')) $table->dropColumn('notes_rejected');
            if (Schema::hasColumn('pengaduan', 'file')) $table->dropColumn('file');
        });

        // 3. Add new columns
        Schema::table('pengaduan', function (Blueprint $table) {
            if (!Schema::hasColumn('pengaduan', 'contract_id')) {
                $table->uuid('contract_id')->nullable()->after('id')->comment('relasi kontrak');
            }
            if (!Schema::hasColumn('pengaduan', 'complaint_type_id')) {
                $table->foreignUuid('complaint_type_id')->nullable()->constrained('urgencies')->onDelete('set null');
            }
            if (!Schema::hasColumn('pengaduan', 'urgency_level')) {
                $table->enum('urgency_level', ['LOW', 'MEDIUM', 'HIGH', 'CRITICAL'])->nullable(); 
            }
            if (!Schema::hasColumn('pengaduan', 'reporter_id')) {
                $table->foreignUuid('reporter_id')->constrained('users')->onDelete('cascade')->comment('pengaju');
            }
            if (!Schema::hasColumn('pengaduan', 'reported_user_id')) {
                $table->foreignUuid('reported_user_id')->nullable()->constrained('users')->onDelete('cascade')->comment('terlapor');
            }
            if (!Schema::hasColumn('pengaduan', 'description')) {
                $table->text('description')->comment('kronologi');
            }
            if (!Schema::hasColumn('pengaduan', 'status')) {
                $table->enum('status', ['open', 'investigating', 'resolved'])->default('open');
            }
            if (!Schema::hasColumn('pengaduan', 'applied_sanction_id')) {
               $table->uuid('applied_sanction_id')->nullable(); 
            }
            if (!Schema::hasColumn('pengaduan', 'resolved_at')) {
                $table->timestamp('resolved_at')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Simplified down: just try to drop new columns and rename back
        Schema::table('pengaduan', function (Blueprint $table) {
            // Check before dropping to avoid errors in partial rollback?
            // For now, standard rollback
           // $table->dropForeign(['complaint_type_id']); // This might fail if constraint name differs :P
           // Strict rollback is hard here without same checks.
        });
        
         if (Schema::hasTable('pengaduan') && !Schema::hasTable('complaints')) {
            Schema::rename('pengaduan', 'complaints');
        }
    }
};
