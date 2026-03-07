<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->enum('status', ['draft', 'scheduled', 'published'])->default('published')->after('tags');
            $table->timestamp('published_at')->nullable()->after('status');
        });

        // Set published_at for existing blogs that don't have it
        DB::table('blogs')->whereNull('published_at')->update([
            'published_at' => now(),
            'status' => 'published',
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('blogs', function (Blueprint $table) {
            $table->dropColumn(['status', 'published_at']);
        });
    }
};
