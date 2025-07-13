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
        Schema::table('channel_ad_applications', function (Blueprint $table) {
            // Add marketplace-specific fields
            $table->foreignId('advertiser_id')->nullable()->after('channel_ad_id')->constrained('users')->onDelete('cascade');
            $table->enum('booking_status', ['pending', 'confirmed', 'canceled', 'completed'])->default('pending')->after('status');
            $table->date('start_date')->nullable()->after('booking_status');
            $table->date('end_date')->nullable()->after('start_date');
            $table->enum('payment_status', ['pending', 'held', 'released', 'refunded'])->default('pending')->after('escrow_status');
            $table->decimal('amount', 10, 2)->nullable()->after('payment_status');
            
            // Add indexes for better performance
            $table->index('advertiser_id');
            $table->index('booking_status');
            $table->index('payment_status');
            $table->index(['start_date', 'end_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('channel_ad_applications', function (Blueprint $table) {
            $table->dropForeign(['advertiser_id']);
            $table->dropIndex(['advertiser_id']);
            $table->dropIndex(['booking_status']);
            $table->dropIndex(['payment_status']);
            $table->dropIndex(['start_date', 'end_date']);
            
            $table->dropColumn([
                'advertiser_id',
                'booking_status',
                'start_date',
                'end_date',
                'payment_status',
                'amount'
            ]);
        });
    }
};