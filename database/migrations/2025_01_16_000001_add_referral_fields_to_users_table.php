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
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code', 8)->unique()->nullable()->after('last_login_at');
            $table->unsignedBigInteger('referred_by')->nullable()->after('referral_code');
            $table->timestamp('referred_at')->nullable()->after('referred_by');
            
            $table->foreign('referred_by')->references('id')->on('users')->onDelete('set null');
            $table->index(['referral_code']);
            $table->index(['referred_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['referred_by']);
            $table->dropIndex(['referral_code']);
            $table->dropIndex(['referred_by']);
            $table->dropColumn(['referral_code', 'referred_by', 'referred_at']);
        });
    }
};