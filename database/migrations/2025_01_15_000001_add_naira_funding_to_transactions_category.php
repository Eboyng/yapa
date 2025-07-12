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
            // Drop the existing enum column and recreate with new values
            $table->dropColumn('category');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            // Add the category column back with all required values including naira_funding
            $table->enum('category', [
                'credit_purchase',
                'naira_funding',
                'whatsapp_message',
                'sms_message',
                'refund',
                'earnings',
                'withdrawal',
                'bonus',
                'penalty',
                'whatsapp_change_fee',
                'batch_join',
                'ad_earning',
                'manual_adjustment',
                'channel_ad_escrow',
                'channel_ad_payment',
                'channel_sale_escrow',
                'channel_sale_payment',
                'referral_reward',
                'batch_share_reward'
            ])->after('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop the updated enum column
            $table->dropColumn('category');
        });
        
        Schema::table('transactions', function (Blueprint $table) {
            // Restore the previous enum column without naira_funding
            $table->enum('category', [
                'credit_purchase',
                'whatsapp_message',
                'sms_message',
                'refund',
                'earnings',
                'withdrawal',
                'bonus',
                'penalty',
                'whatsapp_change_fee',
                'batch_join',
                'ad_earning',
                'manual_adjustment',
                'channel_ad_escrow',
                'channel_ad_payment',
                'channel_sale_escrow',
                'channel_sale_payment',
                'referral_reward',
                'batch_share_reward'
            ])->after('type');
        });
    }
};