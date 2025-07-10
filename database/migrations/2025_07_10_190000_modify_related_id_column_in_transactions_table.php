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
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the existing related_id column
            $table->dropColumn('related_id');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            // Add the new related_id column as string to support UUIDs
            $table->string('related_id')->nullable()->after('completed_at');
            $table->index(['related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the string related_id column
            $table->dropIndex(['related_id']);
            $table->dropColumn('related_id');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            // Restore the original unsignedBigInteger related_id column
            $table->unsignedBigInteger('related_id')->nullable()->after('completed_at');
        });
    }
};