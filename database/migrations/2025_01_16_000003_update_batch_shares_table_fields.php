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
        Schema::table('batch_shares', function (Blueprint $table) {
            // Rename columns to match the model expectations
            $table->renameColumn('new_members_count', 'share_count');
            $table->renameColumn('reward_claimed', 'rewarded');
            $table->renameColumn('reward_claimed_at', 'rewarded_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batch_shares', function (Blueprint $table) {
            // Revert column names
            $table->renameColumn('share_count', 'new_members_count');
            $table->renameColumn('rewarded', 'reward_claimed');
            $table->renameColumn('rewarded_at', 'reward_claimed_at');
        });
    }
};